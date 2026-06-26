<?php

namespace App\Http\Controllers\superadmin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Setting;
use App\Models\CoinPackage;
use App\Models\TopupTransaction;
use App\Models\Room;
use RealRashid\SweetAlert\Facades\Alert;

class DashboardSuperAdminController extends Controller
{
    public function index()
    {
        // Dynamic dashboard stats
        $totalUsers = User::where('role', 'user')->count();
        $blockedUsers = User::where('role', 'user')->where('is_blocked', true)->count();
        $totalRevenue = TopupTransaction::where('status', 'SUCCESS')->sum('amount');
        $totalCoinsSold = TopupTransaction::where('status', 'SUCCESS')->sum('coin_amount');

        // Recent 8 transactions
        $recentTransactions = TopupTransaction::with('user')
            ->orderBy('created_at', 'desc')
            ->take(8)
            ->get();

        // Top 8 coin holders
        $topPlayers = User::where('role', 'user')
            ->orderBy('kuansing_poin', 'desc')
            ->take(8)
            ->get();

        return view('pagesuperadmin.dashboard.index', compact(
            'totalUsers',
            'blockedUsers',
            'totalRevenue',
            'totalCoinsSold',
            'recentTransactions',
            'topPlayers'
        ));
    }

    /**
     * Manage players.
     */
    public function users()
    {
        $users = User::where('role', 'user')
            ->withCount(['wins', 'losses'])
            ->get();

        return view('pagesuperadmin.users.index', compact('users'));
    }

    /**
     * Toggle block/unblock status.
     */
    public function toggleBlock($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->user()->id) {
            Alert::error('Aksi Gagal', 'Anda tidak dapat memblokir akun Anda sendiri.');
            return redirect()->back();
        }

        $user->is_blocked = !$user->is_blocked;
        $user->save();

        $statusText = $user->is_blocked ? 'diblokir' : 'diaktifkan kembali';
        Alert::success('Berhasil', "Akun player {$user->nama_jalur} ({$user->email}) berhasil {$statusText}.");

        return redirect()->back();
    }

    /**
     * KlikQRIS API Settings.
     */
    public function settings()
    {
        $apiKey = Setting::get('klikqris_api_key', '');
        $merchantId = Setting::get('klikqris_merchant_id', '');

        return view('pagesuperadmin.settings.index', compact('apiKey', 'merchantId'));
    }

    /**
     * Save settings.
     */
    public function saveSettings(Request $request)
    {
        $request->validate([
            'klikqris_api_key' => 'required|string',
            'klikqris_merchant_id' => 'required|string',
        ]);

        Setting::set('klikqris_api_key', $request->klikqris_api_key);
        Setting::set('klikqris_merchant_id', $request->klikqris_merchant_id);

        Alert::success('Berhasil', 'Kredensial API KlikQRIS berhasil diperbarui.');

        return redirect()->back();
    }

    /**
     * Manage coin packages.
     */
    public function packages()
    {
        $packages = CoinPackage::orderBy('coin_amount', 'asc')->get();

        return view('pagesuperadmin.packages.index', compact('packages'));
    }

    /**
     * Store new coin package.
     */
    public function storePackage(Request $request)
    {
        $request->validate([
            'coin_amount' => 'required|integer|min:1|unique:coin_packages,coin_amount',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:255',
        ]);

        CoinPackage::create([
            'coin_amount' => $request->coin_amount,
            'price' => $request->price,
            'description' => $request->description,
        ]);

        Alert::success('Berhasil', 'Paket koin baru berhasil ditambahkan.');

        return redirect()->back();
    }

    /**
     * Update package.
     */
    public function updatePackage(Request $request, $id)
    {
        $package = CoinPackage::findOrFail($id);

        $request->validate([
            'coin_amount' => 'required|integer|min:1|unique:coin_packages,coin_amount,' . $id,
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:255',
        ]);

        $package->update([
            'coin_amount' => $request->coin_amount,
            'price' => $request->price,
            'description' => $request->description,
        ]);

        Alert::success('Berhasil', 'Paket koin berhasil diperbarui.');

        return redirect()->back();
    }

    /**
     * Delete package.
     */
    public function deletePackage(Request $request, $id)
    {
        $package = CoinPackage::findOrFail($id);
        $package->delete();

        Alert::success('Berhasil', 'Paket koin berhasil dihapus.');

        return redirect()->back();
    }

    /**
     * List all transactions.
     */
    public function transactions()
    {
        $transactions = TopupTransaction::with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pagesuperadmin.transactions.index', compact('transactions'));
    }
}
