<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rsp extends Model
{
    protected $table = 'rsp';
    protected $fillable = [
        'detail_transaksi_id', 
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

    public function detailTransaksi()
    {
        return $this->belongsTo(DetailTransaksi::class);
    }
}
