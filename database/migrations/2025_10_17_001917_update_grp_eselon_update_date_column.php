<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, add a temporary column
        Schema::table('grp_eselon', function (Blueprint $table) {
            $table->datetime('update_date_new')->nullable();
        });

        // Convert existing data from DD/MM/YYYY format to proper datetime
        $records = \DB::table('grp_eselon')->get();
        foreach ($records as $record) {
            if ($record->update_date) {
                // Parse DD/MM/YYYY format
                if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})(?:\s+(\d{1,2}):(\d{2})(?::(\d{2}))?)?$/', $record->update_date, $matches)) {
                    $day = $matches[1];
                    $month = $matches[2];
                    $year = $matches[3];
                    $hour = isset($matches[4]) ? $matches[4] : 0;
                    $minute = isset($matches[5]) ? $matches[5] : 0;
                    $second = isset($matches[6]) ? $matches[6] : 0;
                    
                    $newDate = sprintf('%04d-%02d-%02d %02d:%02d:%02d', $year, $month, $day, $hour, $minute, $second);
                    
                    \DB::table('grp_eselon')
                        ->where('id', $record->id)
                        ->update(['update_date_new' => $newDate]);
                }
            }
        }

        // Drop the old column and rename the new one
        Schema::table('grp_eselon', function (Blueprint $table) {
            $table->dropColumn('update_date');
        });

        Schema::table('grp_eselon', function (Blueprint $table) {
            $table->renameColumn('update_date_new', 'update_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('grp_eselon', function (Blueprint $table) {
            // Revert back to string
            $table->string('update_date')->change();
        });
    }
};
