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
            $table->foreignId('farmalkes_id')->nullable()->constrained('farmalkes')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('alkes', function (Blueprint $table) {
            $table->dropForeign(['farmalkes_id']);
            $table->dropColumn('farmalkes_id');
        });
    }
};
