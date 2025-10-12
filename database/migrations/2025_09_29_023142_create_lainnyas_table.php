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
        Schema::create('lainnyas', function (Blueprint $table) {
             $table->id();
            $table->foreignId('psn_id')->constrained('psns')->onDelete('cascade');
            
            $table->string('dskp_lainnya');
            $table->string('jmlh_lainnaya');
            $table->string('bya_lainnya')->default('0 RP');
            $table->string('disc_lainnya')->default('0%');
            $table->string('st_lainnya')->default('0 RP');
            $table->date('tanggal');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lainnyas');
    }
};
