<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    /**
     * Form lupa password
     */
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Kirim email reset password
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => [
                'required',
                'email',
                'exists:users,email',
            ],
        ], [
            'email.required' =>
                'Email wajib diisi.',

            'email.email' =>
                'Format email tidak valid.',

            'email.exists' =>
                'Email tidak ditemukan.',
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {

            return back()->with(
                'status',
                'Link reset password telah dikirim ke email.'
            );
        }

        return back()->withErrors([
            'email' => __($status),
        ]);
    }
}