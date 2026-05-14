<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Evaluasi extends Model
{
    use HasFactory;

    protected $table = 'evaluasi';

    protected $fillable = [
        'jurnal_id',
        'kepsek_id',
        'nilai',
        'catatan',
    ];

    protected $casts = [
        'nilai' => 'integer',
    ];

    /* =========================
       RELASI
    ========================= */

    public function jurnal()
    {
        return $this->belongsTo(Jurnal::class, 'jurnal_id');
    }

    public function kepsek()
    {
        return $this->belongsTo(User::class, 'kepsek_id');
    }

    // Shortcut langsung ke guru lewat jurnal
    public function guru()
    {
        return $this->hasOneThrough(
            Guru::class,
            Jurnal::class,
            'id',       // jurnal.id
            'id',       // guru.id
            'jurnal_id', // evaluasi.jurnal_id
            'guru_id'   // jurnal.guru_id
        );
    }
}