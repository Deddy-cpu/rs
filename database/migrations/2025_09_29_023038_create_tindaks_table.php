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
        Schema::create('tindaks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('psn_id')->constrained('psns')->onDelete('cascade');
            $table->string('dktr_tindak');
            $table->string('dskp_tindak');
            $table->string('jmlh_tindak');
            $table->string('bya_tindak')->default('0 RP');
            $table->string('disc_tindak')->default('0%');
            $table->string('st_tindak')->default('0 RP');
            $table->date('tanggal');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tindaks');
    }
};
