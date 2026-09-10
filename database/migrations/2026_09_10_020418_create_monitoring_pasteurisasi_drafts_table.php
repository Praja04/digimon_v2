<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('monitoring_pasteurisasi_drafts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('monitoring_pasteurisasi_id')
                ->unique()
                ->constrained('monitoring_pasteurisasi')
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Data Analisa Draft
            |--------------------------------------------------------------------------
            | Nullable karena draft boleh belum lengkap.
            */
            $table->string('brix')->nullable();
            $table->string('visco')->nullable();
            $table->string('nacl')->nullable();
            $table->string('bj')->nullable();
            $table->string('ph')->nullable();
            $table->string('aw')->nullable();

            $table->text('organo')->nullable();
            $table->string('buih')->nullable();
            $table->text('aroma')->nullable();
            $table->string('endapan')->nullable();

            $table->string('status_disposition')->nullable();
            $table->string('disposition')->nullable();
            $table->text('disposition_remark')->nullable();

            $table->string('adjustment_qty_air')->nullable();
            $table->string('adjustment_qty_gula')->nullable();
            $table->string('adjustment_qty_garam')->nullable();

            /*
            |--------------------------------------------------------------------------
            | User pembuat draft
            |--------------------------------------------------------------------------
            */
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monitoring_pasteurisasi_drafts');
    }
};