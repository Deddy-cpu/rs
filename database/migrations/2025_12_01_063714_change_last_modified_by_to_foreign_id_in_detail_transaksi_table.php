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
        Schema::table('detail_transaksi', function (Blueprint $table) {
            // Drop the old string column if exists
            if (Schema::hasColumn('detail_transaksi', 'last_modified_by')) {
                // First, drop the index if it exists
                try {
                    $table->dropIndex(['version', 'last_modified_at']);
                } catch (\Exception $e) {
                    // Index might not exist, continue
                }
                
                // Drop the old string column
                $table->dropColumn('last_modified_by');
            }
            
            // Add new foreignId column
            $table->foreignId('last_modified_by')
                ->nullable()
                ->after('last_modified_at')
                ->constrained('users')
                ->onDelete('set null');
            
            // Re-add the index
            $table->index(['version', 'last_modified_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detail_transaksi', function (Blueprint $table) {
            // Drop the foreign key constraint
            $table->dropForeign(['last_modified_by']);
            
            // Drop the index
            try {
                $table->dropIndex(['version', 'last_modified_at']);
            } catch (\Exception $e) {
                // Index might not exist, continue
            }
            
            // Drop the foreignId column
            $table->dropColumn('last_modified_by');
            
            // Re-add the old string column
            $table->string('last_modified_by')->nullable()->after('last_modified_at');
            
            // Re-add the index
            $table->index(['version', 'last_modified_at']);
        });
    }
};
