<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengajuanIzin extends Model
{
     use HasFactory;
    
    protected $table = 'pengajuan_izin';
    protected $primaryKey = 'id_pengajuan';
    public $timestamps = false;

    protected $fillable = [
        'id_user',
        'jenis',
        'tanggal_mulai',
        'tanggal_selesai',
        'alasan',
        'surat_keterangan',
        'status',
        'catatan_admin'
    ];

    public function user()
    {
        return $this->belongsTo(Users::class, 'id_user', 'id_user');
    }
}
