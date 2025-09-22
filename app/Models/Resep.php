<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resep extends Model
{
    use HasFactory;

    protected $table = 'reseps'; // nama tabel di database

    // field yang boleh diisi (mass assignment)
    protected $fillable = [
        'obat_resep',
        'jumlah',
        'pemakaian_perhari',
    ];
}
