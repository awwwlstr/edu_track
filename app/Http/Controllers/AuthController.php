<?php

namespace App\Http\Controllers;

use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login()
    {
        return view('auth.login');
    }

    public function loginProses(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            if (auth()->user()->role === 'admin') {
                return redirect('/admin')->with('success', 'Selamat datang, Admin!');
            }

            return redirect('/absensi')->with('success', 'Selamat datang!');
        }

        return back()->with('error', 'Email atau password salah');
    }

    public function register()
    {
        return view('auth.register');
    }

    public function registerProses(Request $request)
    {
        $request->validate([
            'nama'        => 'required',
            'nip'         => 'required|unique:users',
            'email'       => 'required|email|unique:users',
            'password'    => 'required|min:6',
            'foto'        => 'nullable|image|mimes:jpg,jpeg,png',
            'foto_base64' => 'required',
        ]);

        // =========================
        // FOTO WAJAH (DATASET)
        // =========================
        $pathDataset = public_path('dataset');

        if (!file_exists($pathDataset)) {
            mkdir($pathDataset, 0755, true);
        }

        $namaFileWajah = $request->nip . '.jpg';

        if ($request->filled('foto_base64')) {
            $base64 = explode(',', $request->foto_base64);
            $image  = base64_decode(end($base64));

            file_put_contents(
                $pathDataset . '/' . $namaFileWajah,
                $image
            );
        }

        // =========================
        // FOTO PROFIL
        // =========================
        $namaFileProfil = null;

        if ($request->hasFile('foto') && $request->file('foto')->isValid()) {
            $file = $request->file('foto');

            $namaFileProfil = time() . '_' . $request->nip . '.' . $file->getClientOriginalExtension();

            $tersimpan = $file->move(public_path('fotoprofil'), $namaFileProfil); // ✅ fix: $ bukan $$

            if (!$tersimpan) {
                return back()->with('error', 'Gagal menyimpan foto profil');
            }
        }

        // =========================
        // USER CREATE
        // =========================
        Users::create([
            'nama'     => $request->nama,
            'nip'      => $request->nip,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'guru',
            'foto'     => $namaFileProfil,
        ]);

        return redirect('/login')->with('success', 'Registrasi berhasil');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'Berhasil logout');
    }
}