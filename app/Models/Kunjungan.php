<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kunjungan extends Model
{
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
        'kunjungan'
    ];

    protected $casts = [
        'tgl_reg' => 'date',
        'tgl_inv' => 'date',
    ];

    /**
     * Get the patient that owns the visit
     */

     public function psn(): BelongsTo
     {
        return $this->belongsTo(Psn::class, 'psn_id', 'id');
     }
    public function pasien(): BelongsTo
    {
        return $this->belongsTo(Psn::class, 'psn_id', 'id');
    }

    /**
     * Get the transactions for the visit
     */
    public function transaksi(): HasMany
    {
        return $this->hasMany(Transaksi::class, 'kunjungan_id', 'id');
    }
}
