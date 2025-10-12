<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Fix existing tarif data by removing thousand separators
        DB::table('tindakan_tarif')->get()->each(function ($record) {
            $cleanTarif = str_replace('.', '', $record->tarif);
            $cleanTarif = str_replace(',', '.', $cleanTarif);
            
            DB::table('tindakan_tarif')
                ->where('id', $record->id)
                ->update(['tarif' => $cleanTarif]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration is not reversible as we can't determine original formatting
        // If rollback is needed, restore from backup
    }
};