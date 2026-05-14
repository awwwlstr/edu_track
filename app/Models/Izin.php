<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Izin extends Model
{
     protected $table = 'izin';
    protected $primaryKey = 'id_izin';
    public $timestamps = false;

    protected $fillable = [
        'id_user',
        'jenis',
        'keterangan',
        'tanggal_mulai',
        'tanggal_selesai',
        'status_verifikasi'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}
