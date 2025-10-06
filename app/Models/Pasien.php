<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pasien extends Model
{
    protected $table = 'pasien';
  
    protected $fillable = [
        'psn_id',
        'no_reg',
        'tgl_reg',
        'nm_p',
        'mrn',
        'almt_B',
        'no_inv',
        'tgl_inv',
        'perawatan',
        'penjamin',
        'no_sjp',
        'icd',
        'kunjungan',
    ];

    public function psn()
    {
        return $this->belongsTo(Psn::class, 'psn_id');
    }

    public function transaksi()
    {
        return $this->hasMany(Transaksi::class, 'pasien_id', 'id');
    }

    public function konsuls()
    {
        return $this->hasMany(Konsul::class, 'pasien_id', 'id');
    }

    public function tindaks()
    {
        return $this->hasMany(Tindak::class, 'pasien_id', 'id');
    }

    public function alkes()
    {
        return $this->hasMany(Alkes::class, 'pasien_id', 'id');
    }

    public function rsp()
    {
        return $this->hasMany(Rsp::class, 'pasien_id', 'id');
    }

    public function lainnyas()
    {
        return $this->hasMany(Lainnya::class, 'pasien_id', 'id');
    }
}

