<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('packaging_karton_bcts')) {
            return;
        }

        Schema::create('packaging_karton_bcts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('packaging_incoming_id')
                ->unique()
                ->constrained('packaging_incomings')
                ->cascadeOnDelete();

            $table->decimal('bct4gt1', 8, 2)->nullable();
            $table->decimal('bct4gt2', 8, 2)->nullable();
            $table->unsignedTinyInteger('resampling_count')->default(0);
            $table->json('values')->nullable();
            $table->decimal('average', 8, 2)->default(0);
            $table->string('status', 50)->default('Tidak Terisi');

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('packaging_karton_bcts');
    }
};

