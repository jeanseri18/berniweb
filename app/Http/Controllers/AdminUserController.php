<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function show($id)
    {
        $user = User::with(['wallet', 'sentParcels', 'deliveredParcels', 'kycSubmissions'])->findOrFail($id);
        return view('admin.users.show', compact('user'));
    }

    public function verifyPhone($id)
    {
        $user = User::findOrFail($id);
        $user->forceFill([
            'is_verified' => true,
            'otp_code' => null,
            'otp_expires_at' => null,
        ])->save();

        return redirect()
            ->route('admin.users.show', $user->id)
            ->with('success', 'Numéro vérifié manuellement.');
    }
}
