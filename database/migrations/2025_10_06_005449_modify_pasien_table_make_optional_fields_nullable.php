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
        Schema::table('pasien', function (Blueprint $table) {
            $table->string('no_inv')->nullable()->change();
            $table->string('tgl_inv')->nullable()->change();
            $table->string('no_sjp')->nullable()->change();
            $table->string('icd')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pasien', function (Blueprint $table) {
            $table->string('no_inv')->nullable(false)->change();
            $table->string('tgl_inv')->nullable(false)->change();
            $table->string('no_sjp')->nullable(false)->change();
            $table->string('icd')->nullable(false)->change();
        });
    }
};
