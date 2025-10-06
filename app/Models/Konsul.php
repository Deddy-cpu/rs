<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Konsul extends Model
{
    protected $table = 'konsuls';
    protected $fillable = [
        'detail_transaksi_id', 
        'dokter', 
        'dskp_kons', 
        'jmlh_kons', 
        'bya_kons', 
        'disc_kons', 
        'st_kons', 
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
