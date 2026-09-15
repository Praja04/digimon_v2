<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pelarutan_1_drafts', function (Blueprint $table) {
            $table->string('disposition')
                ->nullable()
                ->after('status_disposition');
        });
    }

    public function down(): void
    {
        Schema::table('pelarutan_1_drafts', function (Blueprint $table) {
            $table->dropColumn('disposition');
        });
    }
};