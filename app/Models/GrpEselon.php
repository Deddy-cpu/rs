<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GrpEselon extends Model
{
    use HasFactory;

    protected $table = 'grp_eselon';

    protected $fillable = [
        'eselon_desc',
        'update_date',
        'updated_by',
    ];

    protected $casts = [
        'update_date' => 'date',
    ];
}
