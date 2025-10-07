<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaksi extends Model
{
    protected $table = 'transaksi';
    protected $fillable = [
        'kunjungan_id', 
        'total_biaya', 
        'tanggal', 
        'status'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'total_biaya' => 'decimal:2',
    ];

    /**
     * Get the visit that owns the transaction
     */
    public function kunjungan(): BelongsTo
    {
        return $this->belongsTo(Kunjungan::class, 'kunjungan_id', 'id');
    }

    /**
     * Get the patient through the visit
     */
    public function psn()
    {
        return $this->kunjungan->psn;
    }

    /**
     * Get the detail transactions for the transaction
     */
    public function detailTransaksi(): HasMany
    {
        return $this->hasMany(DetailTransaksi::class, 'transaksi_id', 'id');
    }
}

