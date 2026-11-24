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
        Schema::table('sampling_kondisi_mobil', function (Blueprint $table) {
            $table->string('keterangan_bersih')->nullable()->after('bersih');
            $table->string('keterangan_kering')->nullable()->after('kering');
            $table->string('keterangan_benda_asing')->nullable()->after('benda_asing');
            $table->string('keterangan_cacat')->nullable()->after('cacat');
            $table->string('keterangan_segel')->nullable()->after('segel');
            $table->string('keterangan_berbau')->nullable()->after('berbau');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sampling_kondisi_mobil', function (Blueprint $table) {
            $table->dropColumn([
                'keterangan_bersih',
                'keterangan_kering',
                'keterangan_benda_asing',
                'keterangan_cacat',
                'keterangan_segel',
                'keterangan_berbau',
            ]);
        });
    }
};
