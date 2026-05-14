<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Models\PengajuanIzin;
use App\Models\Notifikasi;
use Illuminate\Http\Request;

class PengajuanAdminController extends Controller
{
    public function index()
    {
        $pengajuan = PengajuanIzin::with('user')
                                   ->orderBy('created_at', 'desc')
                                   ->get();
        
        return view('admin.pengajuan.index', compact('pengajuan'));
    }

    public function approve($id)
    {
        $pengajuan = PengajuanIzin::findOrFail($id);
        $pengajuan->update(['status' => 'disetujui']);

        // Buat notifikasi
        Notifikasi::create([
            'id_user' => $pengajuan->id_user,
            'judul' => 'Pengajuan Disetujui',
            'pesan' => 'Pengajuan ' . $pengajuan->jenis . ' Anda telah disetujui oleh admin.',
        ]);

        return redirect('/admin/pengajuan')->with('success', 'Pengajuan berhasil disetujui!');
    }

    public function reject(Request $request, $id)
    {
        $pengajuan = PengajuanIzin::findOrFail($id);
        $pengajuan->update([
            'status' => 'ditolak',
            'catatan_admin' => $request->catatan
        ]);

        // Buat notifikasi
        Notifikasi::create([
            'id_user' => $pengajuan->id_user,
            'judul' => 'Pengajuan Ditolak',
            'pesan' => 'Pengajuan ' . $pengajuan->jenis . ' Anda ditolak. Catatan: ' . ($request->catatan ?? '-'),
        ]);

        return redirect('/admin/pengajuan')->with('success', 'Pengajuan berhasil ditolak!');
    }
}
