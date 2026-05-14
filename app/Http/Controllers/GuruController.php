<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Jurnal;

class GuruController extends Controller
{

    public function dashboard()
    {

        $guru = Auth::user()->guru;

        if (!$guru) {
            return redirect()->route('login')
                ->with('error', 'Data guru tidak ditemukan');
        }


        /* ================= BASE QUERY ================= */
        // FIX: gunakan satu base query + clone agar tidak query berulang

        $base = Jurnal::where('guru_id', $guru->id);


        /* ================= DATA JURNAL ================= */

        $jurnal = (clone $base)->latest()->paginate(10);


        /* ================= STATISTIK ================= */

        $totalJurnal = (clone $base)->count();

        $totalJurnalBulan = (clone $base)
            ->whereBetween('tanggal', [
                now()->startOfMonth(),
                now()->endOfMonth()
            ])
            ->count();

        $totalDinilai = (clone $base)->where('status', 'dinilai')->count();

        $totalRevisi  = (clone $base)->where('status', 'revisi')->count();

        $totalPending = (clone $base)->where('status', 'pending')->count();


        /* ================= RATA-RATA ================= */
        // FIX: nilai diambil dari relasi evaluasi, bukan kolom jurnal langsung
        // Pastikan model Jurnal punya relasi: public function evaluasi() { return $this->hasOne(Evaluasi::class); }

        $rataRata = (clone $base)
            ->join('evaluasi', 'jurnal.id', '=', 'evaluasi.jurnal_id')
            ->avg('evaluasi.nilai');


        /* ================= STATUS TERAKHIR ================= */

        $statusTerakhir = (clone $base)->latest()->value('status');


        /* ================= JURNAL TERBARU ================= */

        $jurnalTerbaru = (clone $base)->latest()->take(5)->get();


        /* ================= NOTIFIKASI ================= */

        $notifikasi = (clone $base)
            ->where('status', 'dinilai')
            ->latest()
            ->take(5)
            ->get();


        return view('guru.dashboard', compact(
            'guru',
            'jurnal',
            'totalJurnal',
            'totalJurnalBulan',
            'totalDinilai',
            'totalRevisi',
            'totalPending',
            'rataRata',
            'statusTerakhir',
            'jurnalTerbaru',
            'notifikasi'
        ));
    }
}