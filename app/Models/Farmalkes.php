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
        'satuan',
        'harga',
        'aktif',
        'update_by'
    ];

    protected $casts = [
        'harga' => 'decimal:2',
    ];

    // Scope for active items
    public function scopeActive($query)
    {
        return $query->where('aktif', 'Y');
    }
}
