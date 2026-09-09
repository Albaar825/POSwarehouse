<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function showChangePasswordForm()
    {
        /** @var User $user */
        $user = Auth::user();

        return view('profile.change-password', [
            'canChange' => $user->canChangePassword(),
            'daysLeft' => $user->daysUntilCanChangePassword(),
        ]);
    }

    public function updatePassword(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        if (! $user->canChangePassword()) {
            return back()->withErrors([
                'current_password' => 'Kamu baru bisa ganti password lagi dalam '.$user->daysUntilCanChangePassword().' hari.',
            ]);
        }

        $data = $request->validate([
            'current_password' => ['required'],
            'password' => ['required', 'min:6', 'confirmed'],
        ]);

        if (! Hash::check($data['current_password'], $user->password)) {
            return back()->withErrors([
                'current_password' => 'Password lama yang kamu masukkan salah.',
            ]);
        }

        $user->forceFill([
            'password' => Hash::make($data['password']),
            'password_changed_at' => now(),
        ])->save();

        return back()->with('success', 'Password berhasil diubah. Kamu bisa ganti password lagi setelah 7 hari.');
    }
}
