<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Users;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AbsensiController extends Controller
{
    // Dashboard Absensi
    public function index()
    {
        $userId = auth()->user()->id_user;
        $today = date('Y-m-d');
        
        $absensiHariIni = Absensi::where('id_user', $userId)
                                  ->where('tanggal', $today)
                                  ->first();
        
        $bulanIni = date('Y-m');
        $stats = [
            'hadir' => Absensi::where('id_user', $userId)
                              ->where('tanggal', 'LIKE', "$bulanIni%")
                              ->where('status', 'hadir')
                              ->count(),
            'izin' => Absensi::where('id_user', $userId)
                             ->where('tanggal', 'LIKE', "$bulanIni%")
                             ->where('status', 'izin')
                             ->count(),
            'sakit' => Absensi::where('id_user', $userId)
                              ->where('tanggal', 'LIKE', "$bulanIni%")
                              ->where('status', 'sakit')
                              ->count(),
        ];
        
        return view('absensi.index', compact('absensiHariIni', 'stats'));
    }

    // Absen Masuk (Basic)
    public function absenMasuk(Request $request)
    {
        $userId = auth()->user()->id_user;
        $today = date('Y-m-d');
        $now = date('H:i:s');
        
        $cek = Absensi::where('id_user', $userId)
                      ->where('tanggal', $today)
                      ->first();
        
        if ($cek) {
            return back()->with('error', 'Anda sudah absen masuk hari ini pada ' . $cek->jam_masuk);
        }

        $status = ($now > '08:00:00') ? 'terlambat' : 'hadir';

        Absensi::create([
            'id_user' => $userId,
            'tanggal' => $today,
            'jam_masuk' => $now,
            'status' => $status
        ]);

        $pesan = ($status == 'terlambat') 
            ? 'Absen masuk berhasil, namun Anda terlambat.' 
            : 'Absen masuk berhasil! Selamat bekerja 😊';

        return back()->with('success', $pesan);
    }

    // Absen Masuk Hybrid (GPS + Face Recognition)
    public function absenMasukHybrid(Request $request)
    {
        $userId = auth()->user()->id_user;
        $today = date('Y-m-d');
        $now = date('H:i:s');

        $cek = Absensi::where('id_user', $userId)
                      ->where('tanggal', $today)
                      ->first();

        if ($cek) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah absen masuk hari ini pada ' . $cek->jam_masuk
            ]);
        }

        $status = ($now > '08:00:00') ? 'terlambat' : 'hadir';

        Absensi::create([
            'id_user'        => $userId,
            'tanggal'        => $today,
            'jam_masuk'      => $now,
            'status'         => $status,
            'latitude'       => $request->latitude,
            'longitude'      => $request->longitude,
            'face_confidence'=> $request->face_confidence,
        ]);

        $pesan = ($status == 'terlambat')
            ? 'Absen masuk berhasil, namun Anda terlambat.'
            : 'Absen masuk berhasil! Selamat bekerja 😊';

        return response()->json([
            'success' => true,
            'message' => $pesan
        ]);
    }

    // Absen Keluar Hybrid (GPS + Face Recognition)
    public function absenKeluarHybrid(Request $request)
    {
        $userId = auth()->user()->id_user;
        $today = date('Y-m-d');
        $now = date('H:i:s');

        $absensi = Absensi::where('id_user', $userId)
                          ->where('tanggal', $today)
                          ->first();

        if (!$absensi) {
            return response()->json([
                'success' => false,
                'message' => 'Anda belum absen masuk hari ini.'
            ]);
        }

        if ($absensi->jam_keluar) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah absen keluar pada ' . $absensi->jam_keluar
            ]);
        }

        $absensi->update([
            'jam_keluar' => $now,
            'latitude'   => $request->latitude,
            'longitude'  => $request->longitude,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Absen keluar berhasil! Hati-hati di jalan 😊'
        ]);
    }

    // Absen Keluar (Basic)
    public function absenKeluar(Request $request)
    {
        $userId = auth()->user()->id_user;
        $today = date('Y-m-d');
        $now = date('H:i:s');
        
        $absensi = Absensi::where('id_user', $userId)
                          ->where('tanggal', $today)
                          ->first();
        
        if (!$absensi) {
            return back()->with('error', 'Anda belum absen masuk hari ini.');
        }

        if ($absensi->jam_keluar) {
            return back()->with('error', 'Anda sudah absen keluar pada ' . $absensi->jam_keluar);
        }

        $absensi->update(['jam_keluar' => $now]);

        return back()->with('success', 'Absen keluar berhasil! Hati-hati di jalan 😊');
    }

    // Absen Izin
    public function izin(Request $request)
    {
        $request->validate(['keterangan' => 'required|string|max:255']);

        $userId = auth()->user()->id_user;
        $today = date('Y-m-d');
        
        $cek = Absensi::where('id_user', $userId)->where('tanggal', $today)->first();
        
        if ($cek) {
            return back()->with('error', 'Anda sudah melakukan absensi hari ini.');
        }

        Absensi::create([
            'id_user'    => $userId,
            'tanggal'    => $today,
            'status'     => 'izin',
            'keterangan' => $request->keterangan
        ]);

        return back()->with('success', 'Izin berhasil dicatat.');
    }

    // Absen Sakit
    public function sakit(Request $request)
    {
        $request->validate(['keterangan' => 'required|string|max:255']);

        $userId = auth()->user()->id_user;
        $today = date('Y-m-d');
        
        $cek = Absensi::where('id_user', $userId)->where('tanggal', $today)->first();
        
        if ($cek) {
            return back()->with('error', 'Anda sudah melakukan absensi hari ini.');
        }

        Absensi::create([
            'id_user'    => $userId,
            'tanggal'    => $today,
            'status'     => 'sakit',
            'keterangan' => $request->keterangan
        ]);

        return back()->with('success', 'Status sakit berhasil dicatat. Semoga cepat sembuh 🙏');
    }

    // Halaman Rekap
    public function rekap(Request $request)
    {
        $userId = auth()->user()->id_user;
        $bulan = $request->get('bulan', date('Y-m'));
        
        $absensi = Absensi::where('id_user', $userId)
                          ->where('tanggal', 'LIKE', "$bulan%")
                          ->orderBy('tanggal', 'desc')
                          ->get();
        
        $total = [
            'hadir'     => $absensi->where('status', 'hadir')->count(),
            'terlambat' => $absensi->where('status', 'terlambat')->count(),
            'izin'      => $absensi->where('status', 'izin')->count(),
            'sakit'     => $absensi->where('status', 'sakit')->count(),
        ];
        
        return view('absensi.rekap', compact('absensi', 'total', 'bulan'));
    }

public function exportPdf(Request $request)
{
    $bulan = $request->bulan ?? date('m');
    $tahun = $request->tahun ?? date('Y');

    $absensi = Absensi::with('user')
        ->whereMonth('tanggal', $bulan)
        ->whereYear('tanggal', $tahun)
        ->orderBy('tanggal')
        ->get();

    $pdf = Pdf::loadView('pdf.rekap-pdf', [
        'absensi'        => $absensi,
        'judul'          => 'Guru & Pegawai',
        'bulanLabel'     => Carbon::create($tahun, $bulan)->translatedFormat('F Y'),
        'tahunPelajaran' => '2025/2026',
        'semester'       => 'Genap',
    ])->setPaper('a4', 'landscape');

    return $pdf->download('rekap-absensi-' . $bulan . '-' . $tahun . '.pdf');
}

    // Halaman Kalender
public function kalender(Request $request)
{
    $bulan = $request->bulan ?? date('Y-m');
    $tahun = substr($bulan, 0, 4);
    $bulanInt = (int) substr($bulan, 5, 2);

    // Ambil data absensi
    $dataAbsensi = Absensi::where('id_user', auth()->user()->id_user)
        ->where('tanggal', 'LIKE', "$bulan%")
        ->get()
        ->keyBy('tanggal');

    // Ambil hari libur dari API
    $hariBesar = [];
    try {
        $response = \Illuminate\Support\Facades\Http::timeout(5)
            ->get("https://api-harilibur.vercel.app/api?month=$bulanInt&year=$tahun");

        if ($response->successful()) {
            foreach ($response->json() as $item) {
                if ($item['is_national_holiday']) {
                    $tgl = date('d', strtotime($item['holiday_date']));
                    $hariBesar[$tgl] = $item['holiday_name'];
                }
            }
        }
    } catch (\Exception $e) {
        // Kalau API gagal, lanjut tanpa hari besar
    }

    return view('absensi.kalender', [
        'bulan'    => $bulan,
        'absensi'  => $dataAbsensi,
        'hariBesar'=> $hariBesar,
    ]);
    }
}