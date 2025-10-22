<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\OptimisticLockingException;

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
        'eselon_id',
        'no_sjp',
        'icd',
        'kunjungan',
        'version',
        'last_modified_at',
        'last_modified_by'
    ];

    protected $casts = [
        'tgl_reg' => 'date',
        'tgl_inv' => 'date',
        'version' => 'integer',
        'last_modified_at' => 'datetime',
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
     * Get the eselon that owns the visit
     */
    public function eselon(): BelongsTo
    {
        return $this->belongsTo(Eselon::class, 'eselon_id', 'id');
    }

    /**
     * Get the transactions for the visit
     */
    public function transaksi(): HasMany
    {
        return $this->hasMany(Transaksi::class, 'kunjungan_id', 'id');
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
        $this->last_modified_by = $userId ?? (Auth::check() ? Auth::user()->name : 'System');
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
