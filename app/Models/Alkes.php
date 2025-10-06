<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alkes extends Model
{
    protected $table = 'alkes';
    protected $fillable = [
        'detail_transaksi_id', 
        'poli', 
        'dskp_alkes', 
        'jmlh_alkes', 
        'bya_alkes', 
        'disc_alkes', 
        'st_alkes', 
        'tanggal'
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function detailTransaksi()
    {
        return $this->belongsTo(DetailTransaksi::class);
    }
}
