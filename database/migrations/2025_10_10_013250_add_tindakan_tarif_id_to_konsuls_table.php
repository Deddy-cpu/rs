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
            $table->foreignId('tindakan_tarif_id')->nullable()->constrained('tindakan_tarif')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('konsuls', function (Blueprint $table) {
            $table->dropForeign(['tindakan_tarif_id']);
            $table->dropColumn('tindakan_tarif_id');
        });
    }
};
