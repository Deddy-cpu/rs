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
        Schema::create('tindakan_tarif', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tindakan_q_id')->constrained('tindakan_q')->onDelete('cascade');
            $table->foreignId('eselon_grp_id')->constrained('grp_eselon')->onDelete('cascade');
            $table->string('tarif');
            $table->string('aktif');
            $table->string('update_date');
            $table->string('update_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tindakan_tarif');
    }
};
