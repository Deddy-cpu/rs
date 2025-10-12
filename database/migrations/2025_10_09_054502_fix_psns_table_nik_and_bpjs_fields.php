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
        Schema::table('psns', function (Blueprint $table) {
            // Change nik from integer to string to handle 16-digit NIK numbers
            $table->string('nik')->change();
            
            // Change no_bpjs from integer to string to handle large BPJS numbers
            $table->string('no_bpjs')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('psns', function (Blueprint $table) {
            // Revert back to integer (this might cause data loss for large numbers)
            $table->integer('nik')->change();
            $table->integer('no_bpjs')->change();
        });
    }
};