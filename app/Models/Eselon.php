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
        'update_date' => 'datetime',
    ];

    /**
     * Parse the update_date from DD/MM/YYYY format
     */
    public function getUpdateDateAttribute($value)
    {
        if (!$value) return null;
        
        // If it's already a Carbon instance, return it
        if ($value instanceof \Carbon\Carbon) {
            return $value;
        }
        
        // Try to parse DD/MM/YYYY format
        if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})(?:\s+(\d{1,2}):(\d{2})(?::(\d{2}))?)?$/', $value, $matches)) {
            $day = $matches[1];
            $month = $matches[2];
            $year = $matches[3];
            $hour = isset($matches[4]) ? $matches[4] : 0;
            $minute = isset($matches[5]) ? $matches[5] : 0;
            $second = isset($matches[6]) ? $matches[6] : 0;
            
            return \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', 
                sprintf('%04d-%02d-%02d %02d:%02d:%02d', $year, $month, $day, $hour, $minute, $second)
            );
        }
        
        // Fallback to default parsing
        try {
            return \Carbon\Carbon::parse($value);
        } catch (\Exception $e) {
            return null;
        }
    }

    // Relasi ke GrpEselon
    public function grpEselon()
    {
        return $this->belongsTo(GrpEselon::class, 'grp_eselon_id');
    }

    // Relasi ke Konsul
    public function konsuls()
    {
        return $this->hasMany(Konsul::class);
    }
}
