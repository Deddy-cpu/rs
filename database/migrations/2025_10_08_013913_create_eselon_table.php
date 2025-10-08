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
        Schema::create('eselon', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grp_eselon_id')->constrained('grp_eselon')->onDelete('cascade');
            $table->string('eselon_desc');
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
        Schema::dropIfExists('eselon');
    }
};
