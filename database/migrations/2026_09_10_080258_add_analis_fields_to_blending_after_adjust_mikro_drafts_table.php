<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('blending_after_adjust_mikro_drafts', function (Blueprint $table) {
            $table->unsignedTinyInteger('shift')->nullable()->after('blending_after_adjust_mikro_id');
            $table->string('nama_analis')->nullable()->after('shift');
        });
    }

    public function down(): void
    {
        Schema::table('blending_after_adjust_mikro_drafts', function (Blueprint $table) {
            $table->dropColumn([
                'shift',
                'nama_analis',
            ]);
        });
    }
};