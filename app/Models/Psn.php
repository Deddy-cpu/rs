<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Psn extends Model
{
    use HasFactory;

    protected $table = 'psns';

    protected $fillable = [
        'nm_p',
        'nik',
        'no_bpjs',
        'agm',
        'tgl_lahir',
        'kelamin',
        'almt_L',
        'almt_B',
        'no_telp',
        'nama_ayah',
        'nama_ibu',
    ];

    // Relasi ke tabel Kunjungan
    public function kunjungans()
    {
        return $this->hasMany(Kunjungan::class, 'psn_id');
    }

    // Relasi ke tabel Transaksi melalui Kunjungan
    public function transaksis()
    {
        return $this->hasManyThrough(Transaksi::class, Kunjungan::class, 'psn_id', 'kunjungan_id');
    }

}
