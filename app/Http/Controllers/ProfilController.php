<?php

namespace App\Http\Controllers;

use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfilController extends Controller
{
    // Tampilkan Profil
    public function index()
    {
        $user = auth()->user();
        return view('profil.index', compact('user'));
    }

    // Update Data Profil
    public function update(Request $request)
    {
        $request->validate([
            'nama'  => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . auth()->user()->id_user . ',id_user',
            'nip'   => 'nullable|unique:users,nip,' . auth()->user()->id_user . ',id_user',
        ]);

        $user = Users::find(auth()->user()->id_user);
        $user->update([
            'nama'  => $request->nama,
            'email' => $request->email,
            'nip'   => $request->nip,
        ]);

        return back()->with('success', 'Profil berhasil diperbarui!');
    }

    // Update Password
    public function updatePassword(Request $request)
    {
        $request->validate([
            'password_lama' => 'required',
            'password_baru' => 'required|min:6|confirmed',
        ]);

        $user = auth()->user();

        if (!Hash::check($request->password_lama, $user->password)) {
            return back()->with('error', 'Password lama tidak sesuai!');
        }

        Users::find($user->id_user)->update([
            'password' => Hash::make($request->password_baru)
        ]);

        return back()->with('success', 'Password berhasil diubah!');
    }

    // Update Foto Profil
    public function updateFoto(Request $request)
    {
        $request->validate([
            'foto' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $user = Users::find(auth()->user()->id_user);

        // Hapus foto lama
        if ($user->foto && file_exists(public_path('fotoprofil/' . $user->foto))) {
            unlink(public_path('fotoprofil/' . $user->foto));
        }

        $file     = $request->file('foto');
        $filename = time() . '_' . $user->id_user . '.' . $file->getClientOriginalExtension();

        $file->move(public_path('fotoprofil'), $filename);

        $user->update(['foto' => $filename]);

        return back()->with('success', 'Foto profil berhasil diperbarui!');
    }

} // ✅ tutup class