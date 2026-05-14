<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Users;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\User;

class AbsenAdminController extends Controller
{
    public function index(Request $request)
    {
        $bulan = $request->get('bulan', date('Y-m'));
        $guruId = $request->get('guru_id');

        $query = Absensi::with('user')
                        ->where('tanggal', 'LIKE', "$bulan%")
                        ->orderBy('tanggal', 'desc');

        if ($guruId) {
            $query->where('id_user', $guruId);
        }

        $absensi = $query->get();
        $guru = Users::where('role', 'guru')->orderBy('nama')->get();

        return view('admin.absensi.index', compact('absensi', 'guru', 'bulan', 'guruId'));
    }

    public function exportPdf(Request $request)
{
    $bulan = $request->bulan ?? date('Y-m');
    $guruId = $request->guru_id;

    $query = Absensi::with('user')
        ->whereYear('tanggal', substr($bulan, 0, 4))
        ->whereMonth('tanggal', substr($bulan, 5, 2));

    // Jika dipilih guru tertentu
    if ($guruId) {
        $query->where('id_user', $guruId);
        $judulGuru = User::find($guruId)->nama ?? 'Guru';
        $judul = "Laporan Absensi - $judulGuru";
    } else {
        $judul = "Laporan Absensi Semua Guru";
    }

    $absensi = $query->orderBy('tanggal')->get();
    $bulanLabel = \Carbon\Carbon::parse($bulan)->translatedFormat('F Y');

    $pdf = Pdf::loadView('admin.absensi.export-pdf', compact('absensi', 'judul', 'bulanLabel'))
              ->setPaper('a4', 'landscape');

    return $pdf->download("absensi-$bulan.pdf");
    }

    public function destroy($id)
    {
        $absensi = Absensi::findOrFail($id);
        $absensi->delete();

        return back()->with('success', 'Data absensi berhasil dihapus!');
    }
}
