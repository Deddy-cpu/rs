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
        Schema::table('konsuls', function (Blueprint $table) {
            // Add eselon_id column
            $table->unsignedBigInteger('eselon_id')->nullable()->after('psn_id');
            
            // Add foreign key constraint
            $table->foreign('eselon_id')->references('id')->on('eselon')->onDelete('set null');
            
            // Add index for better performance
            $table->index('eselon_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('konsuls', function (Blueprint $table) {
            // Drop foreign key constraint
            $table->dropForeign(['eselon_id']);
            
            // Drop index
            $table->dropIndex(['eselon_id']);
            
            // Drop column
            $table->dropColumn('eselon_id');
        });
    }
};