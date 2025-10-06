<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lainnya extends Model
{
    protected $table = 'lainnyas';
    protected $fillable = [
        'detail_transaksi_id', 
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

    public function detailTransaksi()
    {
        return $this->belongsTo(DetailTransaksi::class);
    }
}
