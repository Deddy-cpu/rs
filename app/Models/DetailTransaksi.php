<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\OptimisticLockingException;

class DetailTransaksi extends Model
{
    protected $table = 'detail_transaksi';
    protected $fillable = [
        'transaksi_id', 
        'resep', 
        'jumlah', 
        'deskripsi', 
        'biaya',
        'icd',
        'version',
        'last_modified_at',
        'last_modified_by'
    ];

    protected $casts = [
        'version' => 'integer',
        'last_modified_at' => 'datetime',
    ];

    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class);
    }

    public function konsuls()
    {
        return $this->hasMany(Konsul::class, 'detail_transaksi_id', 'id');
    }

    public function tindaks()
    {
        return $this->hasMany(Tindak::class, 'detail_transaksi_id', 'id');
    }

    public function alkes()
    {
        return $this->hasMany(Alkes::class, 'detail_transaksi_id', 'id');
    }

    public function rsp()
    {
        return $this->hasMany(Rsp::class, 'detail_transaksi_id', 'id');
    }

    public function lainnyas()
    {
        return $this->hasMany(Lainnya::class, 'detail_transaksi_id', 'id');
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
        $this->last_modified_by = $userId ?? Auth::user()?->name ?? 'System';
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

