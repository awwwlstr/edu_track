<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Users;
use App\Models\Absensi;
use App\Models\PengajuanIzin;
use App\Models\Jurnal;
use App\Models\Evaluasi;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $today   = date('Y-m-d');
        $bulanIni = date('Y-m');

        $totalGuru       = Users::where('role', 'guru')->count();
        $hadirHariIni    = Absensi::whereDate('tanggal', $today)->whereIn('status', ['hadir', 'terlambat'])->count();
        $izinHariIni     = Absensi::whereDate('tanggal', $today)->where('status', 'izin')->count();
        $sakitHariIni    = Absensi::whereDate('tanggal', $today)->where('status', 'sakit')->count();
        $alphaHariIni    = $totalGuru - ($hadirHariIni + $izinHariIni + $sakitHariIni);
        $pengajuanMenunggu = PengajuanIzin::where('status', 'menunggu')->count();
        $absensiTerbaru  = Absensi::with('user')->whereDate('tanggal', $today)->orderBy('jam_masuk', 'desc')->limit(10)->get();

        // Statistik jurnal
        $totalJurnal    = Jurnal::count();
        $jurnalPending  = Jurnal::where('status', 'pending')->count();
        $jurnalDinilai  = Jurnal::where('status', 'dinilai')->count();
        $jurnalRevisi   = Jurnal::where('status', 'revisi')->count();

        return view('admin.dashboard', compact(
            'totalGuru',
            'hadirHariIni',
            'izinHariIni',
            'sakitHariIni',
            'alphaHariIni',
            'pengajuanMenunggu',
            'absensiTerbaru',
            'totalJurnal',
            'jurnalPending',
            'jurnalDinilai',
            'jurnalRevisi'
        ));
    }
}