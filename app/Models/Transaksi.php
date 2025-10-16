<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Exceptions\OptimisticLockingException;

class Transaksi extends Model
{
    protected $table = 'transaksi';
    protected $fillable = [
        'kunjungan_id', 
        'total_biaya', 
        'tanggal', 
        'status',
        'version',
        'last_modified_at',
        'last_modified_by'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'total_biaya' => 'decimal:2',
        'version' => 'integer',
        'last_modified_at' => 'datetime',
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

    /**
     * Check if the current version matches the expected version
     */
    public function isVersionValid($expectedVersion): bool
    {
        return $this->version === $expectedVersion;
    }

    /**
     * Increment version and update last modified info
     */
    public function incrementVersion($userId = null): void
    {
        $this->version++;
        $this->last_modified_at = now();
        $this->last_modified_by = $userId ?? auth()->user()?->name ?? 'System';
    }

    /**
     * Update with optimistic locking
     */
    public function updateWithOptimisticLock(array $data, $expectedVersion, $userId = null): bool
    {
        if (!$this->isVersionValid($expectedVersion)) {
            throw new OptimisticLockingException();
        }

        $this->incrementVersion($userId);
        return $this->update($data);
    }
}

