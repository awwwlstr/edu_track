<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Jurnal extends Model
{
    use HasFactory;

    protected $table = 'jurnal';

    protected $fillable = [
        'guru_id',
        'tanggal',
        'jam_pelajaran',
        'mata_pelajaran',
        'kelas',
        'materi',
        'kendala',
        'status',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    /* =========================
       RELASI
    ========================= */

    // GANTI: dari Guru::class ke Users::class
    public function user()
    {
        return $this->belongsTo(Users::class, 'guru_id', 'id_user');
    }

    // Hapus relasi guru() karena tidak ada model Guru
    // public function guru() { ... }

    public function evaluasi()
    {
        return $this->hasOne(Evaluasi::class, 'jurnal_id');
    }

    /* =========================
       LOCAL SCOPES
    ========================= */

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeDinilai($query)
    {
        return $query->where('status', 'dinilai');
    }

    public function scopeRevisi($query)
    {
        return $query->where('status', 'revisi');
    }
}