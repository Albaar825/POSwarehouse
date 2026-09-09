<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect('/dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (
            Auth::attempt(
                $credentials,
                $request->boolean('remember')
            )
        ) {

            $request->session()->regenerate();

            $user = Auth::user();

            if (! $user->is_active) {

                Auth::logout();

                return back()->withErrors([
                    'email' =>
                        'Akun kamu nonaktif, hubungi admin.'
                ]);
            }

            return redirect()->intended('/dashboard');
        }

        return back()
            ->withErrors([
                'email' =>
                    'Email atau password salah.',
            ])
            ->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }
}