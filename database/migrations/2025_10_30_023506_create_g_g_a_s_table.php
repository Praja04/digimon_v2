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
        Schema::create('ggas', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();
            $table->foreignId('production_batch_id')->constrained('production_batches')->onDelete('cascade');
            $table->string('batch_number', 50);
            $table->string('dissolver_number', 50);
            $table->decimal('brix', 5, 2)->nullable();
            $table->decimal('nacl', 5, 2)->nullable();
            $table->string('organo')->nullable();
            $table->enum('disposition', ['Release', 'Release Bersyarat', 'Resampling', 'Reject', 'Repro'])->nullable();
            $table->string('revisi', 50)->nullable();
            $table->tinyInteger('not_standard')->default(0);
            $table->enum('status', ['OK', 'NOT OK'])->nullable();
            $table->text('disposition_remark')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ggas');
    }
};
