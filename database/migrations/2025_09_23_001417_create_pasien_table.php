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
        Schema::create('pasien', function (Blueprint $table) {
    $table->id();
    $table->foreignId('psn_id')->constrained('psns')->onDelete('cascade');
    $table->string('no_reg');
    $table->date('tgl_reg');
    $table->string('nm_p');
    $table->string('mrn');
    $table->string('almt_B');
    $table->string('no_inv');
    $table->string('tgl_inv');
    $table->string('perawatan');
    $table->string('penjamin');
    $table->string('no_sjp');
    $table->string('icd');
    $table->enum('kunjungan', ['umum', 'gigi', 'kia', 'laboratorium', 'apotek']);
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pasien');
    }
};
