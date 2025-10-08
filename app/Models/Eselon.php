<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Eselon extends Model
{
    use HasFactory;

    protected $table = 'eselon';

    protected $fillable = [
        'grp_eselon_id',
        'eselon_desc',
        'aktif',
        'update_date',
        'update_by',
    ];

    protected $casts = [
        'update_date' => 'date',
    ];

    // Relasi ke GrpEselon
    public function grpEselon()
    {
        return $this->belongsTo(GrpEselon::class, 'grp_eselon_id');
    }
}
