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
        Schema::create('transaksi', function (Blueprint $table) {
    $table->id();
    $table->foreignId('pasien_id')->constrained('pasien')->onDelete('cascade');
    $table->string('dokter');
    $table->string('tindakan');
    $table->string('jmlh')->default('kosong');
    $table->string('dskrps')->nullable();
    $table->string('bya')->default('0 RP');
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};
