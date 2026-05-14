<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use Illuminate\Http\Request;

class NotifikasiController extends Controller
{
    // Halaman Notifikasi
    public function index()
    {
        $notifikasi = Notifikasi::where('id_user', auth()->user()->id_user)
                                 ->orderBy('created_at', 'desc')
                                 ->get();
        
        return view('notifikasi.index', compact('notifikasi'));
    }

    // Tandai Sudah Dibaca
    public function markAsRead($id)
    {
        $notif = Notifikasi::where('id_notifikasi', $id)
                           ->where('id_user', auth()->user()->id_user)
                           ->firstOrFail();
        
        $notif->update(['is_read' => 1]);

        return back()->with('success', 'Notifikasi ditandai sudah dibaca.');
    }
}
