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
        Schema::table('farmalkes', function (Blueprint $table) {
            $table->dropColumn(['jenis', 'kategori', 'stok']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('farmalkes', function (Blueprint $table) {
            $table->enum('jenis', ['farmasi', 'alkes'])->after('deskripsi');
            $table->string('kategori', 100)->after('jenis');
            $table->integer('stok')->default(0)->after('harga');
        });
    }
};
