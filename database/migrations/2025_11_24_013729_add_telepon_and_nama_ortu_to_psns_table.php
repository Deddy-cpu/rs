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
            $table->string('no_telp')->nullable()->after('almt_B');
            $table->string('nama_ortu')->nullable()->after('no_telp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('psns', function (Blueprint $table) {
            $table->dropColumn(['no_telp', 'nama_ortu']);
        });
    }
};
