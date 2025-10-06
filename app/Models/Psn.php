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
    ];

    // Relasi ke tabel Pasien
    public function pasiens()
    {
        return $this->hasMany(Pasien::class, 'psn_id');
    }
}
