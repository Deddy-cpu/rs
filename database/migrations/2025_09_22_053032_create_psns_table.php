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
        Schema::create('psns', function (Blueprint $table) {
            $table->id();
            $table->string('nm_p');
            $table->integer('nik');
            $table->integer('no_bpjs');
            $table->string('agm');
            $table->string('tgl_lahir');
            $table->enum('kelamin', ['L', 'P','kosong'])->default('kosong');
            $table->string('almt_L');
            $table->string('almt_B');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('psns');
    }
};
