<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use App\Models\User;
use App\Models\Role;

class AuthenticatedSessionController extends Controller
{
    /**
     * Menampilkan halaman login
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Memproses request login
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        try {
            // Otentikasi pengguna
            $request->authenticate();
            $request->session()->regenerate();

            /** @var User $user */
            $user = Auth::user();
            
            // Log informasi user
            Log::info('User berhasil login', [
                'user_id' => $user->id,
                'email' => $user->email
            ]);

            // Verifikasi role user
            if (!$user->role) {
                Log::error('Role tidak ditemukan untuk user', ['user_id' => $user->id]);
                Auth::logout();
                return redirect()->route('login')
                    ->withErrors(['email' => 'Akun Anda belum dikonfigurasi dengan benar. Silakan hubungi administrator.']);
            }

            // Redirect berdasarkan role
            return match ($user->role->name) {
                'admin' => redirect()->intended(route('admin.dashboard')),
                'teknisi' => redirect()->intended(route('teknisi.dashboard')),
                'kurir' => redirect()->intended(route('kurir.dashboard')),
                'refil' => redirect()->intended(route('refil.dashboard')),
                default => $this->handleRoleTidakDikenal($user),
            };

        } catch (\Exception $e) {
            Log::error('Gagal login: ' . $e->getMessage(), [
                'exception' => $e,
                'email' => $request->email
            ]);
            
            return back()->withErrors([
                'email' => 'Login gagal. Silakan coba lagi atau hubungi support.'
            ]);
        }
    }

    /**
     * Menangani role yang tidak dikenali
     */
    protected function handleRoleTidakDikenal(User $user): RedirectResponse
    {
        Log::warning('Role tidak dikenali', [
            'user_id' => $user->id,
            'role_id' => $user->role_id,
            'role_name' => $user->role->name ?? 'null'
        ]);
        
        Auth::logout();
        return redirect()->route('login')
            ->withErrors(['email' => 'Konfigurasi role akun Anda tidak valid.']);
    }

    /**
     * Memproses logout
     */
    public function destroy(Request $request): RedirectResponse
    {
        try {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect('/');
            
        } catch (\Exception $e) {
            Log::error('Gagal logout: ' . $e->getMessage());
            return redirect('/')->withErrors(['message' => 'Logout gagal. Silakan coba lagi.']);
        }
    }
}