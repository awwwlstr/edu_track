<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Guru;
use App\Models\User;

class GuruProfileController extends Controller
{

    /* =========================================
       TAMPILKAN PROFIL
    ========================================= */

    public function index()
    {
        /** @var User $user */
        $user = Auth::user();

        $guru = Guru::where('user_id', $user->id)->first();

        return view('guru.profil', compact('user', 'guru'));
    }


    /* =========================================
       UPDATE PROFIL
    ========================================= */

    public function updateProfil(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $request->validate([
            'nama_lengkap'  => 'required|string|max:255',
            'nip'           => 'nullable|string|max:50',
            'tempat_lahir'  => 'nullable|string|max:100',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'nullable|in:Laki-laki,Perempuan',
            'jabatan'       => 'nullable|string|max:100',
            'alamat'        => 'nullable|string',
            'foto'          => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);


        /* ================= UPDATE USER ================= */

        $user->update([
            'nama_lengkap' => $request->nama_lengkap,
        ]);


        /* ================= AMBIL / BUAT DATA GURU ================= */

        /** @var \App\Models\Guru $guru */
        $guru = Guru::firstOrCreate(['user_id' => $user->id]);

        $data = [
            'nip'           => $request->nip,
            'tempat_lahir'  => $request->tempat_lahir,
            'tanggal_lahir' => $request->tanggal_lahir,
            'jenis_kelamin' => $request->jenis_kelamin,
            'jabatan'       => $request->jabatan,
            'alamat'        => $request->alamat,
        ];


        /* ================= UPLOAD FOTO ================= */
        // FIX: upload dulu, baru hapus foto lama setelah upload berhasil
        // Sehingga jika upload gagal, foto lama tidak ikut terhapus

        if ($request->hasFile('foto')) {

            $newPath = $request->file('foto')->store('foto_guru', 'public');

            // Hapus foto lama SETELAH upload baru berhasil
            if ($newPath && !empty($guru->foto) && Storage::disk('public')->exists($guru->foto)) {
                Storage::disk('public')->delete($guru->foto);
            }

            $data['foto'] = $newPath;
        }


        /* ================= UPDATE DATA GURU ================= */

        $guru->update($data);

        return back()->with('success', 'Profil berhasil diperbarui');
    }
}