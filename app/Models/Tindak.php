<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tindak extends Model
{
    protected $table = 'tindaks';
    protected $fillable = [
        'psn_id',
        'detail_transaksi_id', 
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

    public function detailTransaksi()
    {
        return $this->belongsTo(DetailTransaksi::class);
    }
}
