<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rsp extends Model
{
    protected $table = 'rsp';
    protected $fillable = [
        'pasien_id', 
        'dskp_rsp', 
        'jmlh_rsp', 
        'bya_rsp', 
        'disc_rsp', 
        'st_rsp', 
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
