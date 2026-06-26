<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;
use RealRashid\SweetAlert\Facades\Alert;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        try {
            return Socialite::driver('google')->redirect();
        } catch (\Exception $e) {
            Alert::error('Terjadi kesalahan saat redirect ke Google. Silakan coba lagi.');
            return redirect('/login');
        }
    }

    public function handleGoogleCallback()
    {
        try {
            // Socialite sudah dikonfigurasi dengan Guzzle verify => false di AppServiceProvider
            $googleUser = Socialite::driver('google')->user();

            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                if ($user->is_blocked) {
                    Alert::error('Login Gagal', 'Akun Anda telah dinonaktifkan (blocked) oleh admin.');
                    return $this->respondWithPopupScript('error', '/login', 'Akun Anda telah dinonaktifkan (blocked) oleh admin.');
                }

                // Update google_id jika belum ada
                if (!$user->google_id) {
                    $user->update(['google_id' => $googleUser->getId()]);
                }

                // Kalau user lama belum punya nama_jalur atau foto_profile, wajib lengkapi dulu
                if (!$user->nama_jalur || !$user->foto_profile) {
                    // Jangan login dulu, arahkan ke halaman lengkapi data
                    return $this->respondWithPopupScript('success', route('google.complete', ['user_id' => $user->id]));
                }

                Auth::login($user);

                // Redirect langsung untuk user yang sudah terdaftar dan profilnya lengkap
                $redirectUrl = ($user->role == 'admin' || $user->role == 'superadmin')
                    ? route('dashboard-superadmin')
                    : '/main-menu';
                return $this->respondWithPopupScript('success', $redirectUrl);
            }

            // Jika user belum terdaftar, buat user baru dengan data minimal
            // JANGAN Auth::login dulu — user harus isi profil dahulu
            $user = User::create([
                'email' => $googleUser->getEmail(),
                'password' => Hash::make(uniqid()),
                'role' => 'user',
                'google_id' => $googleUser->getId(),
            ]);

            // Redirect ke halaman lengkapi data, belum login
            return $this->respondWithPopupScript('success', route('google.complete', ['user_id' => $user->id]));

        } catch (\Laravel\Socialite\Two\InvalidStateException $e) {
            // Handle invalid state exception dengan redirect dan alert
            Alert::error('Sesi OAuth tidak valid', 'Silakan coba login lagi.');
            return $this->respondWithPopupScript('error', '/login', 'Sesi OAuth tidak valid. Silakan coba login lagi.');

        } catch (\Exception $e) {
            // Handle SSL certificate errors dan error lainnya dengan redirect dan alert
            $errorMessage = $e->getMessage();
            $customMessage = 'Terjadi kesalahan saat login dengan Google.';

            if (
                strpos($errorMessage, 'SSL certificate problem') !== false ||
                strpos($errorMessage, 'cURL error 60') !== false
            ) {
                $customMessage = 'Masalah SSL Certificate. Terjadi masalah dengan sertifikat SSL. Silakan coba lagi atau hubungi administrator.';
            } elseif (strpos($errorMessage, 'Client error') !== false) {
                $customMessage = 'Error OAuth. Terjadi kesalahan pada OAuth. Pastikan konfigurasi Google OAuth sudah benar.';
            } else {
                $customMessage = 'Terjadi kesalahan saat login dengan Google: ' . $errorMessage;
            }

            Alert::error('Error Google OAuth', $customMessage);
            return $this->respondWithPopupScript('error', '/login', $customMessage);
        }
    }

    public function showCompleteForm(Request $request)
    {
        $user_id = $request->query('user_id');
        $user = User::where('id', $user_id)->first();

        if (!$user) {
            Alert::error('Data Google tidak ditemukan.', 'Silakan login dengan Google terlebih dahulu.');
            return redirect('/login');
        }

        // Kalau user sudah punya nama_jalur & foto_profile, profil sudah lengkap
        // Login dan langsung redirect ke main-menu
        if ($user->nama_jalur && $user->foto_profile) {
            Auth::login($user);
            return redirect('/main-menu');
        }

        return view('auth.complete-google-register', ['user' => $user]);
    }

    public function completeRegister(Request $request)
    {
        try {
            $data = $request->validate([
                'user_id' => 'required|exists:users,id',
                'nama_jalur' => 'required|string|max:50|unique:users,nama_jalur',
                'foto_profile' => 'required|string',
                'agree-terms' => 'required',
            ], [
                'user_id.required' => 'ID User tidak ditemukan.',
                'user_id.exists' => 'User tidak ditemukan.',
                'nama_jalur.required' => 'Nama Jalur wajib diisi.',
                'nama_jalur.unique' => 'Nama Jalur sudah digunakan, coba nama lain.',
                'nama_jalur.max' => 'Nama Jalur maksimal 50 karakter.',
                'foto_profile.required' => 'Foto Profile wajib diisi.',
                'agree-terms.required' => 'Anda harus menyetujui syarat dan ketentuan.',
            ]);
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }

        $user = User::find($data['user_id']);

        if (!$user) {
            Alert::error('User tidak ditemukan.', 'Silakan login dengan Google terlebih dahulu.');
            return redirect('/login');
        }

        // Kalau user sudah pernah complete register sebelumnya, langsung login saja
        if ($user->nama_jalur && $user->foto_profile) {
            if ($user->is_blocked) {
                Alert::error('Gagal', 'Akun Anda telah dinonaktifkan.');
                return redirect('/login');
            }
            Auth::login($user);
            return redirect('/main-menu');
        }

        try {
            $avatarKey = $data['foto_profile'];
            $finalPath = 'profiles/default.gif';
            $sourceFile = public_path("game_pacu/assets/image/ui/{$avatarKey}.gif");

            if (file_exists($sourceFile)) {
                $destFileName = time() . '_' . $avatarKey . '.gif';
                $destPath = public_path("profiles/{$destFileName}");

                if (!file_exists(public_path('profiles'))) {
                    mkdir(public_path('profiles'), 0755, true);
                }

                copy($sourceFile, $destPath);
                $finalPath = 'profiles/' . $destFileName;
            } else {
                if (strpos($avatarKey, 'profiles/') !== false) {
                    $finalPath = $avatarKey;
                }
            }

            $user->update([
                'nama_jalur' => $data['nama_jalur'],
                'foto_profile' => $finalPath,
            ]);

            // Login user SETELAH data profil lengkap tersimpan
            Auth::login($user);

            Alert::success('Akun berhasil dibuat lewat Google!', 'Selamat datang di Linkskuy!');
            if ($user->role == 'admin' || $user->role == 'superadmin') {
                return redirect()->route('dashboard-superadmin');
            } else {
                return redirect('/main-menu');
            }

        } catch (\Exception $e) {
            Alert::error('Gagal menyelesaikan pendaftaran', 'Terjadi kesalahan. Silakan coba lagi.');
            return back()->withInput();
        }
    }

    private function respondWithPopupScript($status, $redirectUrl = null, $errorMessage = null)
    {
        $redirectUrlJson = json_encode($redirectUrl);
        $errorMessageJson = json_encode($errorMessage);
        $statusJson = json_encode($status);
        return response()->make("
            <!DOCTYPE html>
            <html>
            <head>
                <title>Authenticating...</title>
            </head>
            <body>
                <script>
                    const status = {$statusJson};
                    const redirectUrl = {$redirectUrlJson};
                    const errorMessage = {$errorMessageJson};

                    if (window.opener) {
                        window.opener.postMessage({
                            type: 'google-login-response',
                            status: status,
                            redirect: redirectUrl,
                            message: errorMessage
                        }, window.location.origin);
                        window.close();
                    } else {
                        if (status === 'success') {
                            window.location.href = redirectUrl;
                        } else {
                            window.location.href = '/login';
                        }
                    }
                </script>
            </body>
            </html>
        ");
    }
}
