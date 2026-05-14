<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    use HasFactory;
    
    protected $table = 'absensi';
    protected $primaryKey = 'id_absensi';
    public $timestamps = false;

    protected $fillable = [
        'id_user',
        'tanggal',
        'jam_masuk',
        'jam_keluar',
        'status',
        'keterangan',
        'latitude',
        'longitude',
        'foto_masuk',
        'foto_keluar'
    ];

    public function user()
    {
        return $this->belongsTo(Users::class, 'id_user', 'id_user');
    }
}
