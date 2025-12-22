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
        Schema::table('blending_awal', function (Blueprint $table) {
            $table->string('nomor_blending', 50)->nullable()->change();
        });

        Schema::table('blending_after_adjust_mikro', function (Blueprint $table) {
            $table->string('nomor_blending', 50)->nullable()->change();
        });

        Schema::table('monitoring_turun_blending', function (Blueprint $table) {
            $table->string('nomor_blending', 50)->nullable()->change();
        });

        Schema::table('monitoring_pasteurisasi', function (Blueprint $table) {
            $table->string('nomor_blending', 50)->nullable()->change();
        });

        Schema::table('monitoring_storage_kimia', function (Blueprint $table) {
            $table->string('nomor_blending', 50)->nullable()->change();
        });

        Schema::table('monitoring_storage_mikro', function (Blueprint $table) {
            $table->string('nomor_blending', 50)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blending_awal', function (Blueprint $table) {
            $table->integer('nomor_blending')->nullable()->change();
        });

        Schema::table('blending_after_adjust_mikro', function (Blueprint $table) {
            $table->integer('nomor_blending')->nullable()->change();
        });

        Schema::table('monitoring_turun_blending', function (Blueprint $table) {
            $table->integer('nomor_blending')->nullable()->change();
        });

        Schema::table('monitoring_pasteurisasi', function (Blueprint $table) {
            $table->integer('nomor_blending')->nullable()->change();
        });

        Schema::table('monitoring_storage_kimia', function (Blueprint $table) {
            $table->integer('nomor_blending')->nullable()->change();
        });

        Schema::table('monitoring_storage_mikro', function (Blueprint $table) {
            $table->integer('nomor_blending')->nullable()->change();
        });
    }
};
