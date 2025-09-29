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
        Schema::create('rsp', function (Blueprint $table) {
               $table->id();
            $table->foreignId('pasien_id')->constrained('pasien')->onDelete('cascade');
            $table->string('dskp_rsp');
            $table->string('jmlh_rsp');
            $table->string('bya_rsp')->default('0 RP');
            $table->string('disc_rsp')->default('0%');
            $table->string('st_rsp')->default('0 RP');
            $table->date('tanggal');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rsp');
    }
};
