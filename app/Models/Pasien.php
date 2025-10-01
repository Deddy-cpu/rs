<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pasien extends Model
{
    protected $table = 'pasien';
    protected $fillable = ['nama_pasien', 'alamat', 'perawatan', 'Penjamin', 'tanggal'];

    public function transaksi()
    {
        return $this->hasMany(Transaksi::class);
    }

    public function konsuls()
    {
        return $this->hasMany(Konsul::class);
    }

    public function tindaks()
    {
        return $this->hasMany(Tindak::class);
    }

    public function alkes()
    {
        return $this->hasMany(Alkes::class);
    }

    public function rsp()
    {
        return $this->hasMany(Rsp::class);
    }

    public function lainnyas()
    {
        return $this->hasMany(Lainnya::class);
    }
}

