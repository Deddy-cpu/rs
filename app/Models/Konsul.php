<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Konsul extends Model
{
    protected $table = 'konsuls';
    protected $fillable = [
        'psn_id',
        'eselon_id',
        'detail_transaksi_id',
        'tindakan_tarif_id',
        'dokter', 
        'dskp_kons', 
        'jmlh_kons', 
        'bya_kons', 
        'disc_kons', 
        'st_kons', 
        'tanggal'
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function detailTransaksi()
    {
        return $this->belongsTo(DetailTransaksi::class);
    }

    public function eselon()
    {
        return $this->belongsTo(Eselon::class);
    }

    public function tindakanTarif()
    {
        return $this->belongsTo(TindakanTarif::class);
    }
}
