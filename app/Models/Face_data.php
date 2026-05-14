<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Face_data extends Model
{
      protected $table = 'face_data';
    protected $primaryKey = 'id_face';
    public $timestamps = false;

    protected $fillable = [
        'id_user',
        'face_embedding'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}
