<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Guru extends Model
{
    use HasFactory;

    protected $table = 'guru';

    protected $fillable = [
        'user_id',
        'nip',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'jabatan',
        'alamat',
        'foto',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    /* =========================
       RELASI
    ========================= */

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function jurnal()
    {
        return $this->hasMany(Jurnal::class, 'guru_id');
    }

    /* =========================
       ACCESSOR
    ========================= */

    // Hitung rata-rata nilai semua jurnal guru ini via evaluasi
    public function getRataRataNilaiAttribute(): ?float
    {
        return $this->jurnal()
            ->join('evaluasi', 'jurnal.id', '=', 'evaluasi.jurnal_id')
            ->avg('evaluasi.nilai');
    }
}