<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pasien extends Model
{
    protected $table = 'pasien';
    protected $fillable = ['nama_pasien', 'Tempat_Lahir', 'Tanggal_Lahir'];

    public function transaksi()
    {
        return $this->hasMany(Transaksi::class);
    }


}

