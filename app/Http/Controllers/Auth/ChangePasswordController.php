<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ChangePasswordController extends Controller
{
    public function showForm()
    {
        return view('auth.change-password');
    }

    public function update(Request $request)
    {
        $request->validate([
            'password_lama'     => ['required', 'string'],
            'password_baru'     => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'password_baru.confirmed' => 'Konfirmasi password baru tidak cocok.',
            'password_baru.min'       => 'Password baru minimal 6 karakter.',
        ]);

        if (!Hash::check($request->password_lama, auth()->user()->password)) {
            return back()->withErrors(['password_lama' => 'Password lama tidak sesuai.']);
        }

        auth()->user()->update([
            'password' => Hash::make($request->password_baru),
        ]);

        return redirect()->route('dashboard')->with('success', 'Password berhasil diubah.');
    }
}
