<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailTransaksi extends Model
{
    protected $table = 'detail_transaksi';
    protected $fillable = ['transaksi_id', 'resep', 'jumlah', 'deskripsi', 'biaya'];

    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class);
    }

    public function konsuls()
    {
        return $this->hasMany(Konsul::class, 'detail_transaksi_id', 'id');
    }

    public function tindaks()
    {
        return $this->hasMany(Tindak::class, 'detail_transaksi_id', 'id');
    }

    public function alkes()
    {
        return $this->hasMany(Alkes::class, 'detail_transaksi_id', 'id');
    }

    public function rsp()
    {
        return $this->hasMany(Rsp::class, 'detail_transaksi_id', 'id');
    }

    public function lainnyas()
    {
        return $this->hasMany(Lainnya::class, 'detail_transaksi_id', 'id');
    }
}

