<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Polis extends Model
{
    use HasFactory;

    protected $table = 'polis';

    protected $fillable = [
        'poli_desc',
        'aktif',
        'update_date',
        'update_by',
    ];

    protected $casts = [
        'update_date' => 'date',
    ];
}
