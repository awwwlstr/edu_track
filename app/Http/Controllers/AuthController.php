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
            'nama'     => 'required',
            'nip'      => 'required|unique:users',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:6',
            'foto'     => 'required|image|mimes:jpg,jpeg,png'
            // role dihapus dari validasi
        ]);

        // buat nama file
        $namaFile = $request->nip . '.jpg';

        // simpan ke folder dataset
        $path = public_path('dataset');

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        $request->file('foto')->move($path, $namaFile);

        Users::create([
            'nama'     => $request->nama,
            'nip'      => $request->nip,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'guru', // ← dipatenkan selalu guru
            'foto'     => $namaFile
        ]);

        return redirect('/login')->with('success', 'Registrasi berhasil');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}