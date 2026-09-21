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
        Schema::table('analisa_short_term', function (Blueprint $table) {
            if (!Schema::hasColumn('analisa_short_term', 'kategori')) {
                $table->string('kategori')->default('incoming')->after('id_identitas');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('analisa_short_term', function (Blueprint $table) {
            if (Schema::hasColumn('analisa_short_term', 'kategori')) {
                $table->dropColumn('kategori');
            }
        });
    }
};
