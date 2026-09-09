<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;

class ResetPasswordController extends Controller
{
    /**
     * Menampilkan form reset password
     */
    public function showResetForm(
        Request $request,
        string $token
    ) {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    /**
     * Proses reset password
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],

            'password' => [
                'required',
                'confirmed',
                PasswordRule::min(8),
            ],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',

            'password.required' =>
                'Password baru wajib diisi.',

            'password.confirmed' =>
                'Konfirmasi password tidak cocok.',

            'password.min' =>
                'Password minimal 8 karakter.',
        ]);

        $status = Password::reset(
            $request->only(
                'email',
                'password',
                'password_confirmation',
                'token'
            ),

            function ($user, $password) {

                $user->forceFill([
                    'password' => Hash::make($password),

                    // Update waktu password diubah
                    'password_changed_at' => now(),

                    // Generate remember token baru
                    'remember_token' => Str::random(60),
                ])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {

            return redirect()
                ->route('login')
                ->with(
                    'status',
                    'Password berhasil diubah. Silakan login kembali.'
                );
        }

        return back()->withErrors([
            'email' => __($status),
        ]);
    }
}