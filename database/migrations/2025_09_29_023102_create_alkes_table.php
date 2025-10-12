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
        Schema::create('alkes', function (Blueprint $table) {
                $table->id();
            $table->foreignId('psn_id')->constrained('psns')->onDelete('cascade');
            $table->string('poli');
            $table->string('dskp_alkes');
            $table->string('jmlh_alkes');
            $table->string('bya_alkes')->default('0 RP');
            $table->string('disc_alkes')->default('0%');
            $table->string('st_alkes')->default('0 RP');
            $table->date('tanggal');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alkes');
    }
};
