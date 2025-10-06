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



}

