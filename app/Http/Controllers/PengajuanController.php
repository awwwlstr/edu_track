<?php

namespace App\Http\Controllers;


use App\Models\PengajuanIzin;
use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PengajuanController extends Controller
{
    // Halaman Pengajuan
    public function index()
    {
        $pengajuan = PengajuanIzin::where('id_user', auth()->user()->id_user)
                                   ->orderBy('created_at', 'desc')
                                   ->get();
        
        return view('pengajuan.index', compact('pengajuan'));
    }

    // Submit Pengajuan
    public function store(Request $request)
    {
        $request->validate([
            'jenis' => 'required|in:izin,sakit',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'alasan' => 'required|string',
            'surat_keterangan' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $data = [
            'id_user' => auth()->user()->id_user,
            'jenis' => $request->jenis,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'alasan' => $request->alasan,
            'status' => 'menunggu',
        ];

        // Upload surat keterangan jika ada
        if ($request->hasFile('surat_keterangan')) {
            $file = $request->file('surat_keterangan');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('surat', $filename, 'public');
            $data['surat_keterangan'] = $filename;
        }

        PengajuanIzin::create($data);

        return back()->with('success', 'Pengajuan berhasil dikirim! Menunggu persetujuan admin.');
    }

    // Detail Pengajuan
    public function show($id)
    {
        $pengajuan = PengajuanIzin::where('id_pengajuan', $id)
                                   ->where('id_user', auth()->user()->id_user)
                                   ->firstOrFail();
        
        return view('pengajuan.show', compact('pengajuan'));
    }
}
