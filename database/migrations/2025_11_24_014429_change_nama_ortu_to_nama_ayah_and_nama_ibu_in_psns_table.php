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
        Schema::table('psns', function (Blueprint $table) {
            // Drop nama_ortu column if exists
            if (Schema::hasColumn('psns', 'nama_ortu')) {
                $table->dropColumn('nama_ortu');
            }
            // Add nama_ayah and nama_ibu columns
            $table->string('nama_ayah')->nullable()->after('no_telp');
            $table->string('nama_ibu')->nullable()->after('nama_ayah');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('psns', function (Blueprint $table) {
            // Drop nama_ayah and nama_ibu
            $table->dropColumn(['nama_ayah', 'nama_ibu']);
            // Re-add nama_ortu
            $table->string('nama_ortu')->nullable()->after('no_telp');
        });
    }
};
