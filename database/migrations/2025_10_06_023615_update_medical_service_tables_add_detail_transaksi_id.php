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
        // Update konsuls table
        Schema::table('konsuls', function (Blueprint $table) {
         
            $table->unsignedBigInteger('detail_transaksi_id')->nullable();
            $table->foreign('detail_transaksi_id')->references('id')->on('detail_transaksi')->onDelete('cascade');
        });

        // Update tindaks table
        Schema::table('tindaks', function (Blueprint $table) {
           
            $table->unsignedBigInteger('detail_transaksi_id')->nullable();
            $table->foreign('detail_transaksi_id')->references('id')->on('detail_transaksi')->onDelete('cascade');
        });

        // Update alkes table
        Schema::table('alkes', function (Blueprint $table) {
         
            $table->unsignedBigInteger('detail_transaksi_id')->nullable();
            $table->foreign('detail_transaksi_id')->references('id')->on('detail_transaksi')->onDelete('cascade');
        });

        // Update rsp table
        Schema::table('rsp', function (Blueprint $table) {
           
            $table->unsignedBigInteger('detail_transaksi_id')->nullable();
            $table->foreign('detail_transaksi_id')->references('id')->on('detail_transaksi')->onDelete('cascade');
        });

        // Update lainnyas table
        Schema::table('lainnyas', function (Blueprint $table) {
            
            $table->unsignedBigInteger('detail_transaksi_id')->nullable();
            $table->foreign('detail_transaksi_id')->references('id')->on('detail_transaksi')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse konsuls table
        Schema::table('konsuls', function (Blueprint $table) {
            $table->dropForeign(['detail_transaksi_id']);
            $table->dropColumn('detail_transaksi_id');
           
        });

        // Reverse tindaks table
        Schema::table('tindaks', function (Blueprint $table) {
            $table->dropForeign(['detail_transaksi_id']);
            $table->dropColumn('detail_transaksi_id');
           
        });

        // Reverse alkes table
        Schema::table('alkes', function (Blueprint $table) {
            $table->dropForeign(['detail_transaksi_id']);
            $table->dropColumn('detail_transaksi_id');
           
        });

        // Reverse rsp table
        Schema::table('rsp', function (Blueprint $table) {
            $table->dropForeign(['detail_transaksi_id']);
            $table->dropColumn('detail_transaksi_id');
           
        });

        // Reverse lainnyas table
        Schema::table('lainnyas', function (Blueprint $table) {
            $table->dropForeign(['detail_transaksi_id']);
            $table->dropColumn('detail_transaksi_id');
           
        });
    }
};
