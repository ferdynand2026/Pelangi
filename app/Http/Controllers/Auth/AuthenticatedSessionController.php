<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $user = Auth::user();

        // Cek status akun
        if ($user->status == 0) {
            Auth::logout();
            throw ValidationException::withMessages([
                'status' => ['Akun Anda tidak aktif, silahkan hubungi admin.'],
            ]);
        }

        $request->session()->regenerate();

        // Simpan session_id baru ke DB
        // Dipakai oleh FingerprintController untuk menghapus session lama
        // saat ada login dari device lain (action=keep)
        $user->update([
            'session_id' => $request->session()->getId(),
        ]);

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Bersihkan session_id di DB saat user logout manual
        if (Auth::check()) {
            Auth::user()->update([
                'session_id' => null,
            ]);
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}