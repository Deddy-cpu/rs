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
        Schema::create('farmalkes', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 50)->unique();
            $table->string('nama_item');
            $table->text('deskripsi')->nullable();
            $table->enum('jenis', ['farmasi', 'alkes']);
            $table->string('kategori', 100);
            $table->string('satuan', 50);
            $table->decimal('harga', 15, 2);
            $table->integer('stok')->default(0);
            $table->enum('aktif', ['Y', 'N'])->default('Y');
            $table->string('update_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('farmalkes');
    }
};
