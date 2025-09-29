<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lainnya extends Model
{
    protected $table = 'lainnyas';
    protected $fillable = [
        'pasien_id', 
        'dskp_lainnya', 
        'jmlh_lainnaya', 
        'bya_lainnya', 
        'disc_lainnya', 
        'st_lainnya', 
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
