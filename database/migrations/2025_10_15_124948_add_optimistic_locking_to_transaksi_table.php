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
            $table->unsignedBigInteger('version')->default(1)->after('status');
            $table->timestamp('last_modified_at')->useCurrent()->useCurrentOnUpdate()->after('version');
            $table->string('last_modified_by')->nullable()->after('last_modified_at');
            
            // Add index for better performance
            $table->index(['version', 'last_modified_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaksi', function (Blueprint $table) {
            $table->dropIndex(['version', 'last_modified_at']);
            $table->dropColumn(['version', 'last_modified_at', 'last_modified_by']);
        });
    }
};
