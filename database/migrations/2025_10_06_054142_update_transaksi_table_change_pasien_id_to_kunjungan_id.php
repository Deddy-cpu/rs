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
            // Check if kunjungan_id column exists, if not add it
            if (!Schema::hasColumn('transaksi', 'kunjungan_id')) {
                $table->foreignId('kunjungan_id')->nullable()->constrained('kunjungans')->onDelete('cascade');
            }
            
            // Add index for better performance if it doesn't exist
            $table->index('kunjungan_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaksi', function (Blueprint $table) {
            // Drop the new foreign key constraint and column
            $table->dropForeign(['kunjungan_id']);
            $table->dropIndex(['kunjungan_id']);
            $table->dropColumn('kunjungan_id');
            
            // Restore the original pasien_id column
            $table->foreignId('pasien_id')->constrained('pasien')->onDelete('cascade');
        });
    }
};