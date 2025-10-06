<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    protected $table = 'transaksi';
    protected $fillable = [
        'pasien_id', 
        'total_biaya', 
        'tanggal', 
        'status'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'total_biaya' => 'decimal:2',
    ];

    public function pasien()
    {
        return $this->belongsTo(Pasien::class);
    }

    public function detailTransaksi()
    {
        return $this->hasMany(DetailTransaksi::class, 'transaksi_id', 'id');
    }
}

