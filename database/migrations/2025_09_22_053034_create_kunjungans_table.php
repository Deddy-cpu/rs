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
        Schema::create('kunjungans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('psn_id'); // Reference to psns table
            $table->string('no_reg')->unique(); // Nomor registrasi kunjungan
            $table->date('tgl_reg'); // Tanggal registrasi
            $table->string('nm_p'); // Nama pasien
            $table->string('mrn'); // Medical Record Number
            $table->text('almt_B'); // Alamat pasien
            $table->string('no_inv')->nullable(); // Nomor invoice
            $table->date('tgl_inv')->nullable(); // Tanggal invoice
            $table->string('perawatan'); // Jenis perawatan
            $table->foreignId('eselon_id')->constrained('eselon');
            $table->string('no_sjp')->nullable(); // Nomor SJP
            $table->string('icd')->nullable(); // Kode ICD
            $table->string('kunjungan'); // Jenis kunjungan (umum, gigi, dll)
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('psn_id')->references('id')->on('psns')->onDelete('cascade');
            
            // Indexes
            $table->index('psn_id');
            $table->index('no_reg');
            $table->index('tgl_reg');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kunjungans');
    }
};
