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
        Schema::table('doc_pasteurisasi_storages', function (Blueprint $table) {
            $table->string('jenis_produk')->nullable()->after('production_batch_id');
            $table->date('tanggal_produksi')->nullable()->after('jenis_produk');
            $table->string('jam_produksi_start')->nullable()->after('tanggal_produksi');
            $table->string('jam_produksi_end')->nullable()->after('jam_produksi_start');
            $table->string('batch')->nullable()->after('kode_shift_grup');
            $table->text('catatan')->nullable()->after('pic_checker');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('doc_pasteurisasi_storages', function (Blueprint $table) {
            $table->dropColumn([
                'jenis_produk',
                'tanggal_produksi',
                'jam_produksi_start',
                'jam_produksi_end',
                'batch',
                'catatan'
            ]);
        });
    }
};
