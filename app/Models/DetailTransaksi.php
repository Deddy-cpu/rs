<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailTransaksi extends Model
{
    protected $table = 'detail_transaksi';
    protected $fillable = ['transaksi_id', 'tindakan', 'jumlah', 'deskripsi', 'resep'];

    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class);
    }

    
}

