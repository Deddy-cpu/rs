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
        Schema::table('alkes', function (Blueprint $table) {
            // Add dokter column after psn_id if not exists
            if (!Schema::hasColumn('alkes', 'dokter')) {
                $table->string('dokter')->nullable()->after('psn_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('alkes', function (Blueprint $table) {
            // Drop dokter column if exists
            if (Schema::hasColumn('alkes', 'dokter')) {
                $table->dropColumn('dokter');
            }
        });
    }
};
