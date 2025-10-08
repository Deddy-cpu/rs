<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TindakanQ extends Model
{
    use HasFactory;

    protected $table = 'tindakan_q';

    protected $fillable = [
        'tindakan_q_desc',
        'aktif',
        'update_date',
        'update_by',
    ];

    protected $casts = [
        'update_date' => 'date',
    ];
}
