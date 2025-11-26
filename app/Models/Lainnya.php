<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lainnya extends Model
{
    protected $table = 'lainnyas';
    protected $fillable = [
        'psn_id',
        'detail_transaksi_id', 
        'dktr_lainnya',
        'dskp_lainnya', 
        'jmlh_lainnya', 
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
