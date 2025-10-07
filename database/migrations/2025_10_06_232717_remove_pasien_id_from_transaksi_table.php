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
        Schema::table('transaksi', function (Blueprint $table) {
            // Drop foreign key constraint first
            $table->dropForeign(['pasien_id']);
            // Drop the pasien_id column
            $table->dropColumn('pasien_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaksi', function (Blueprint $table) {
            // Restore the pasien_id column
            $table->foreignId('pasien_id')->constrained('pasien')->onDelete('cascade');
        });
    }
};
