<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    protected $table = 'transaksi';
    protected $fillable = ['pasien_id', 'tanggal', 'jns_perawatan', 'dokter'];

    public function pasien()
    {
        return $this->belongsTo(Pasien::class);
    }

    public function detail()
    {
        return $this->hasMany(DetailTransaksi::class);
    }
}

