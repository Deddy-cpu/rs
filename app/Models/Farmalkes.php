<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Farmalkes extends Model
{
    protected $table = 'farmalkes';
    
    protected $fillable = [
        'kode',
        'nama_item',
        'deskripsi',
        'jenis',
        'kategori',
        'satuan',
        'harga',
        'stok',
        'aktif',
        'update_by'
    ];

    protected $casts = [
        'harga' => 'decimal:2',
        'stok' => 'integer',
    ];

    // Scope for active items
    public function scopeActive($query)
    {
        return $query->where('aktif', 'Y');
    }

    // Scope for farmasi items
    public function scopeFarmasi($query)
    {
        return $query->where('jenis', 'farmasi');
    }

    // Scope for alkes items
    public function scopeAlkes($query)
    {
        return $query->where('jenis', 'alkes');
    }

    // Scope for low stock items
    public function scopeLowStock($query, $threshold = 10)
    {
        return $query->where('stok', '<=', $threshold);
    }
}
