<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tindak extends Model
{
    protected $table = 'tindaks';
    protected $fillable = [
        'pasien_id', 
        'dktr_tindak', 
        'dskp_tindak', 
        'jmlh_tindak', 
        'bya_tindak', 
        'disc_tindak', 
        'st_tindak', 
        'tanggal'
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function pasien()
    {
        return $this->belongsTo(Pasien::class);
    }
}
