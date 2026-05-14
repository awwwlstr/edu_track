<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class GuruController extends Controller
{
    public function index()
    {
        $guru = Users::where('role', 'guru')->orderBy('nama')->get();
        return view('admin.guru.index', compact('guru'));
    }

    public function create()
    {
        return view('admin.guru.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'nip' => 'required|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);

        Users::create([
            'nama' => $request->nama,
            'nip' => $request->nip,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'guru'
        ]);

        return redirect('/admin/guru')->with('success', 'Guru berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $guru = Users::findOrFail($id);
        return view('admin.guru.edit', compact('guru'));
    }

    public function update(Request $request, $id)
    {
        $guru = Users::findOrFail($id);

        $request->validate([
            'nama' => 'required|string|max:100',
            'nip' => 'required|unique:users,nip,' . $id . ',id_user',
            'email' => 'required|email|unique:users,email,' . $id . ',id_user',
        ]);

        $data = [
            'nama' => $request->nama,
            'nip' => $request->nip,
            'email' => $request->email,
        ];

        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        }

        $guru->update($data);

        return redirect('/admin/guru')->with('success', 'Data guru berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $guru = Users::findOrFail($id);
        $guru->delete();

        return redirect('/admin/guru')->with('success', 'Guru berhasil dihapus!');
    }
}
