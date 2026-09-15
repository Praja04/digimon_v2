<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pelarutan_2_drafts', function (Blueprint $table) {
            $table->string('brix')->nullable()->change();
            $table->string('nacl')->nullable()->change();
            $table->string('visco')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('pelarutan_2_drafts', function (Blueprint $table) {
            $table->decimal('brix', 10, 4)->nullable()->change();
            $table->decimal('nacl', 10, 4)->nullable()->change();
            $table->decimal('visco', 10, 4)->nullable()->change();
        });
    }
};