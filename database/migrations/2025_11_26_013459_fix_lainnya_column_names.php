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
        Schema::table('lainnyas', function (Blueprint $table) {
            // Fix typo: rename jmlh_lainnaya to jmlh_lainnya
            if (Schema::hasColumn('lainnyas', 'jmlh_lainnaya')) {
                $table->renameColumn('jmlh_lainnaya', 'jmlh_lainnya');
            }
            
            // Add dktr_lainnya column if not exists
            if (!Schema::hasColumn('lainnyas', 'dktr_lainnya')) {
                $table->string('dktr_lainnya')->nullable()->after('psn_id');
            }
            
            // Add detail_transaksi_id column if not exists
            if (!Schema::hasColumn('lainnyas', 'detail_transaksi_id')) {
                $table->foreignId('detail_transaksi_id')->nullable()->after('psn_id')->constrained('detail_transaksis')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lainnyas', function (Blueprint $table) {
            // Revert: rename jmlh_lainnya back to jmlh_lainnaya
            if (Schema::hasColumn('lainnyas', 'jmlh_lainnya')) {
                $table->renameColumn('jmlh_lainnya', 'jmlh_lainnaya');
            }
            
            // Drop dktr_lainnya column if exists
            if (Schema::hasColumn('lainnyas', 'dktr_lainnya')) {
                $table->dropColumn('dktr_lainnya');
            }
            
            // Drop detail_transaksi_id column if exists
            if (Schema::hasColumn('lainnyas', 'detail_transaksi_id')) {
                $table->dropForeign(['detail_transaksi_id']);
                $table->dropColumn('detail_transaksi_id');
            }
        });
    }
};
