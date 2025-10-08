<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TindakanTarif extends Model
{
    use HasFactory;

    protected $table = 'tindakan_tarif';

    protected $fillable = [
        'tindakan_q_id',
        'eselon_grp_id',
        'tarif',
        'aktif',
        'update_date',
        'update_by',
    ];

    protected $casts = [
        'update_date' => 'date',
        'tarif' => 'decimal:2',
    ];

    // Relationships
    public function tindakanQ()
    {
        return $this->belongsTo(TindakanQ::class, 'tindakan_q_id');
    }

    public function grpEselon()
    {
        return $this->belongsTo(GrpEselon::class, 'eselon_grp_id');
    }
}
