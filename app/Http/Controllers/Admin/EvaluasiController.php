<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jurnal;
use App\Models\Evaluasi;
use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EvaluasiController extends Controller
{
    /* ======================================================
       LIST SEMUA JURNAL
    ====================================================== */
    public function index(Request $request)
    {
        $query = Jurnal::with(['user', 'evaluasi']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('mata_pelajaran', 'like', '%' . $request->search . '%')
                  ->orWhere('kelas', 'like', '%' . $request->search . '%');
            });
        }

        $jurnal = $query->latest()->paginate(10);

        return view('admin.evaluasi.index', compact('jurnal'));
    }

    /* ======================================================
       DETAIL JURNAL
    ====================================================== */
    public function show($id)
    {
        $jurnal = Jurnal::with(['user', 'evaluasi'])->findOrFail($id);

        return view('admin.evaluasi.detail', compact('jurnal'));
    }

    /* ======================================================
       SIMPAN EVALUASI
    ====================================================== */
    public function store(Request $request, $id)
    {
        $request->validate([
            'nilai'   => 'required|integer|min:1|max:100',
            'catatan' => 'nullable|string|max:500',
        ]);

        $jurnal = Jurnal::with('evaluasi')->findOrFail($id);

        if ($jurnal->evaluasi) {
            return back()->with('error', 'Jurnal ini sudah dievaluasi.');
        }

        Evaluasi::create([
            'jurnal_id' => $jurnal->id,
            'kepsek_id' => Auth::user()->id_user,
            'nilai'     => $request->nilai,
            'catatan'   => $request->catatan,
        ]);

        $jurnal->update(['status' => 'dinilai']);

        return back()->with('success', 'Evaluasi berhasil disimpan.');
    }
}