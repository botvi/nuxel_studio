<?php

namespace App\Http\Controllers\pagegame;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Setting;
use App\Models\CoinPackage;
use App\Models\TopupTransaction;
use Carbon\Carbon;

class TopupController extends Controller
{
    /**
     * Create QRIS Snap transaction.
     */
    public function createTransaction(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        $packageId = $request->input('package_id');
        $package = CoinPackage::find($packageId);

        if (!$package) {
            return response()->json(['success' => false, 'message' => 'Paket koin tidak ditemukan'], 404);
        }

        $apiKey = Setting::get('klikqris_api_key');
        $merchantId = Setting::get('klikqris_merchant_id');

        if (!$apiKey || !$merchantId) {
            return response()->json([
                'success' => false,
                'message' => 'Metode pembayaran QRIS belum dikonfigurasi oleh admin.'
            ], 422);
        }

        // Generate dynamic unique order ID
        $orderId = 'INV-' . time() . '-' . rand(1000, 9999);

        // Webhook URL endpoint
        $callbackUrl = route('klikqris.webhook');

        // Call KlikQRIS /qris/create API
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'x-api-key' => $apiKey,
                'id_merchant' => $merchantId
            ])->post('https://klikqris.com/api/qris/create', [
                'order_id' => $orderId,
                'id_merchant' => $merchantId,
                'amount' => intval($package->price),
                'keterangan' => "Topup " . $package->coin_amount . " KP - " . ($user->nama_jalur ?? $user->email),
                'callback_url' => $callbackUrl
            ]);

            if ($response->failed()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal terhubung dengan server KlikQRIS: HTTP ' . $response->status()
                ], 500);
            }

            $resData = $response->json();

            if (!isset($resData['status']) || $resData['status'] !== true) {
                return response()->json([
                    'success' => false,
                    'message' => 'KlikQRIS Error: ' . ($resData['message'] ?? 'Gagal membuat tagihan QRIS')
                ], 400);
            }

            $transactionData = $resData['data'];
            $totalAmount = floatval($transactionData['total_amount'] ?? $package->price);
            $signature = $transactionData['signature'] ?? null;
            $qrisUrl = $transactionData['qris_url'] ?? null;

            // Record transaction in DB as PENDING
            TopupTransaction::create([
                'user_id' => $user->id,
                'order_id' => $orderId,
                'amount' => $totalAmount,
                'coin_amount' => $package->coin_amount,
                'status' => 'PENDING',
                'signature' => $signature,
                'qris_url' => $qrisUrl,
            ]);

            return response()->json([
                'success' => true,
                'order_id' => $orderId,
                'signature' => $signature,
                'amount' => $totalAmount,
                'qris_url' => $qrisUrl
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check transaction status dynamically (polling / status check).
     */
    public function checkStatus($order_id)
    {
        $transaction = TopupTransaction::where('order_id', $order_id)->first();

        if (!$transaction) {
            return response()->json(['success' => false, 'message' => 'Transaksi tidak ditemukan'], 404);
        }

        // If already success, return immediately
        if ($transaction->status === 'SUCCESS') {
            $user = User::find($transaction->user_id);
            return response()->json([
                'success' => true,
                'status' => 'SUCCESS',
                'coins' => $user->kuansing_poin
            ]);
        }

        // Fetch API credentials
        $apiKey = Setting::get('klikqris_api_key');
        $merchantId = Setting::get('klikqris_merchant_id');

        if (!$apiKey || !$merchantId) {
            return response()->json([
                'success' => true,
                'status' => $transaction->status // Return current DB status
            ]);
        }

        // Call KlikQRIS /qris/status/{order_id} API to check updated status
        try {
            $response = Http::withHeaders([
                'x-api-key' => $apiKey,
                'id_merchant' => $merchantId
            ])->get("https://klikqris.com/api/qris/status/{$order_id}");

            if ($response->successful()) {
                $resData = $response->json();
                
                if (isset($resData['status']) && $resData['status'] === true && isset($resData['data'])) {
                    $remoteStatus = strtoupper($resData['data']['status'] ?? '');

                    if ($remoteStatus === 'SUCCESS' || $remoteStatus === 'PAID') {
                        // Mark as SUCCESS
                        $transaction->status = 'SUCCESS';
                        $transaction->paid_at = Carbon::now();
                        $transaction->save();

                        // Credit player's coin balance
                        $user = User::find($transaction->user_id);
                        $user->kuansing_poin += $transaction->coin_amount;
                        $user->save();

                        return response()->json([
                            'success' => true,
                            'status' => 'SUCCESS',
                            'coins' => $user->kuansing_poin
                        ]);
                    } elseif ($remoteStatus === 'EXPIRED') {
                        $transaction->status = 'EXPIRED';
                        $transaction->save();

                        return response()->json([
                            'success' => true,
                            'status' => 'EXPIRED'
                        ]);
                    }
                }
            }
        } catch (\Exception $e) {
            // Ignore API status exception and fall back to local database status
        }

        return response()->json([
            'success' => true,
            'status' => $transaction->status
        ]);
    }

    /**
     * Webhook Callback handler.
     */
    public function webhook(Request $request)
    {
        $orderId = $request->input('order_id');
        $incomingStatus = strtoupper($request->input('status') ?? '');
        $signature = $request->input('signature');

        if (!$orderId) {
            return response()->json(['status' => false, 'message' => 'order_id is required'], 400);
        }

        $transaction = TopupTransaction::where('order_id', $orderId)->first();

        if (!$transaction) {
            return response()->json(['status' => false, 'message' => 'Transaction not found'], 404);
        }

        // Validate signature to prevent fake webhooks
        if ($signature !== $transaction->signature) {
            return response()->json(['status' => false, 'message' => 'Invalid signature verification'], 401);
        }

        // Prevent double product dispatching
        if ($transaction->status === 'SUCCESS') {
            return response()->json(['status' => true, 'message' => 'Transaction already processed successfully']);
        }

        if ($incomingStatus === 'PAID' || $incomingStatus === 'SUCCESS') {
            // Update transaction status
            $transaction->status = 'SUCCESS';
            
            $paymentDateStr = $request->input('payment_date');
            $transaction->paid_at = $paymentDateStr ? Carbon::parse($paymentDateStr) : Carbon::now();
            $transaction->save();

            // Credit coin balance to the user
            $user = User::find($transaction->user_id);
            if ($user) {
                $user->kuansing_poin += $transaction->coin_amount;
                $user->save();
            }

            return response()->json(['status' => true, 'message' => 'Webhook processed and coins credited.']);
        } elseif ($incomingStatus === 'EXPIRED') {
            $transaction->status = 'EXPIRED';
            $transaction->save();

            return response()->json(['status' => true, 'message' => 'Transaction marked as expired.']);
        }

        return response()->json(['status' => true, 'message' => 'Status handled: ' . $incomingStatus]);
    }
}
