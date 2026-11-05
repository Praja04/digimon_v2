<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Mengimplementasikan skema relasional dinamis sesuai spesifikasi Excel:
     * - Table 1 (Master): identitas_rm / master_jenis_bahans
     * - Table 2 (Transaksi): transaksi_analisa_rm (Sesi pengujian per kategori)
     * - Table 3 (Master Bahan Baku): master_standar_rms (Definisi parameter per bahan)
     * - Table 4 (Sample): analisa_rm_sample_details (Nilai hasil ukur dinamis per parameter)
     */
    public function up(): void
    {
        // 1. Table Transaksi (Table 2 di Excel)
        if (!Schema::hasTable('analisa_rm_transaksi')) {
            Schema::create('analisa_rm_transaksi', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('id_identitas'); // FK ke identitas_rm (Table 1)
                $table->string('kategori', 50)->default('incoming'); // incoming, sta, monitoring, dll
                $table->string('disposisi', 50)->nullable(); // Release, Reject
                $table->string('status', 20)->default('final'); // draft, final
                $table->text('keterangan')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();

                $table->foreign('id_identitas')
                    ->references('id')
                    ->on('identitas_rm')
                    ->onDelete('cascade');

                $table->foreign('created_by')
                    ->references('id')
                    ->on('users')
                    ->onDelete('set null');

                $table->unique(['id_identitas', 'kategori'], 'unique_identitas_kategori');
                $table->index(['id_identitas', 'kategori']);
                $table->index('status');
            });
        }

        // 2. Table Sample / Hasil Pengujian Dinamis (Table Sample di Excel)
        if (!Schema::hasTable('analisa_rm_sample_details')) {
            Schema::create('analisa_rm_sample_details', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('transaksi_id'); // FK ke analisa_rm_transaksi (Table 2)
                $table->unsignedInteger('sampel_ke')->default(1);
                $table->unsignedBigInteger('master_standar_id')->nullable(); // FK ke master_standar_rms (Table 3)
                $table->string('parameter', 100); // Nama parameter (Brix, NaCl, Organo, dll)
                $table->string('nilai', 255)->nullable(); // Qty / Nilai hasil ukur
                $table->boolean('is_sesuai')->nullable(); // Apakah sesuai standar master
                $table->timestamps();

                $table->foreign('transaksi_id')
                    ->references('id')
                    ->on('analisa_rm_transaksi')
                    ->onDelete('cascade');

                $table->foreign('master_standar_id')
                    ->references('id')
                    ->on('master_standar_rms')
                    ->onDelete('set null');

                $table->index(['transaksi_id', 'sampel_ke'], 'idx_transaksi_sampel');
                $table->index(['transaksi_id', 'parameter'], 'idx_transaksi_param');
            });
        }

        // 3. Table Pendukung Glassware Tare Lab (Khusus penimbangan cawan/beaker untuk Gula Kristal)
        if (!Schema::hasTable('analisa_rm_sample_glasswares')) {
            Schema::create('analisa_rm_sample_glasswares', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('transaksi_id');
                $table->unsignedInteger('sampel_ke')->default(1);

                // Beaker
                $table->string('no_beaker', 100)->nullable();
                $table->decimal('berat_beaker_500', 10, 4)->nullable();
                $table->decimal('berat_beaker_250', 10, 4)->nullable();
                $table->decimal('timbang_a', 10, 4)->nullable();
                $table->decimal('timbang_b', 10, 4)->nullable();

                // Cawan
                $table->string('no_cawan', 100)->nullable();
                $table->decimal('berat_cawan', 10, 4)->nullable();
                $table->decimal('timbang_aa', 10, 4)->nullable();

                $table->timestamps();

                $table->foreign('transaksi_id')
                    ->references('id')
                    ->on('analisa_rm_transaksi')
                    ->onDelete('cascade');

                $table->unique(['transaksi_id', 'sampel_ke'], 'unique_transaksi_sampel_gw');
            });
        }

        // 4. Backward Compatibility VIEW: transaksi_analisa_rm
        \Illuminate\Support\Facades\DB::statement('CREATE OR REPLACE VIEW transaksi_analisa_rm AS SELECT * FROM analisa_rm_transaksi');

        // 5. AUTO-MIGRASI DATA LAMA DI SERVER DEPLOY (Zero Data Loss)
        if (\Illuminate\Support\Facades\Schema::hasTable('analisa_rm_sampels')) {
            $legacyRows = \Illuminate\Support\Facades\DB::table('analisa_rm_sampels')->get();
            foreach ($legacyRows as $row) {
                // Cek apakah transaksi sudah ada
                $existingTx = \Illuminate\Support\Facades\DB::table('analisa_rm_transaksi')
                    ->where('id_identitas', $row->id_identitas)
                    ->where('kategori', $row->kategori ?? 'incoming')
                    ->first();

                if (!$existingTx) {
                    $transaksiId = \Illuminate\Support\Facades\DB::table('analisa_rm_transaksi')->insertGetId([
                        'id_identitas' => $row->id_identitas,
                        'kategori'     => $row->kategori ?? 'incoming',
                        'disposisi'    => $row->disposisi,
                        'status'       => $row->status ?? 'final',
                        'keterangan'   => $row->keterangan,
                        'created_by'   => $row->created_by,
                        'created_at'   => $row->created_at ?? now(),
                        'updated_at'   => $row->updated_at ?? now(),
                    ]);
                } else {
                    $transaksiId = $existingTx->id;
                }

                // Map parameter lama ke tabel detail dinamis
                $paramMap = [
                    'Brix'         => $row->brix ?? null,
                    'pH'           => $row->ph ?? null,
                    '%Kadar Air'   => $row->ka ?? null,
                    '%NaCl'        => $row->nacl ?? null,
                    'Berat Jenis'  => $row->berat_jenis ?? null,
                    'Suhu'         => $row->suhu ?? null,
                    'Aw'           => $row->aw ?? null,
                    'Viskositas'   => $row->viskositas ?? null,
                    '%TN'          => $row->tn ?? null,
                    'Gross Weight' => $row->gross_weight ?? null,
                    '%Kotoran'     => $row->kotoran ?? null,
                    'Organo'       => $row->organo ?? null,
                    'Warna'        => $row->warna ?? null,
                    'Aroma'        => $row->aroma ?? null,
                    'Fisik'        => $row->fisik ?? null,
                    'Rasa'         => $row->rasa ?? null,
                ];

                foreach ($paramMap as $paramName => $val) {
                    if ($val !== null && $val !== '') {
                        \Illuminate\Support\Facades\DB::table('analisa_rm_sample_details')->insert([
                            'transaksi_id'      => $transaksiId,
                            'sampel_ke'         => $row->sampel_ke ?? 1,
                            'master_standar_id' => null,
                            'parameter'         => $paramName,
                            'nilai'             => (string)$val,
                            'is_sesuai'         => 1,
                            'created_at'        => $row->created_at ?? now(),
                            'updated_at'        => $row->updated_at ?? now(),
                        ]);
                    }
                }

                // Pindahkan glassware jika ada
                if (!empty($row->no_beaker) || !empty($row->no_cawan)) {
                    \Illuminate\Support\Facades\DB::table('analisa_rm_sample_glasswares')->insert([
                        'transaksi_id'     => $transaksiId,
                        'sampel_ke'        => $row->sampel_ke ?? 1,
                        'no_beaker'        => $row->no_beaker,
                        'berat_beaker_500' => $row->berat_beaker_500,
                        'berat_beaker_250' => $row->berat_beaker_250,
                        'timbang_a'        => $row->timbang_a,
                        'timbang_b'        => $row->timbang_b,
                        'no_cawan'         => $row->no_cawan,
                        'berat_cawan'      => $row->berat_cawan,
                        'timbang_aa'       => $row->timbang_aa,
                        'created_at'       => $row->created_at ?? now(),
                        'updated_at'       => $row->updated_at ?? now(),
                    ]);
                }
            }

            // Backup tabel lama agar data fisik aslinya tetap aman
            if (!\Illuminate\Support\Facades\Schema::hasTable('legacy_analisa_rm_sampels_backup')) {
                \Illuminate\Support\Facades\Schema::rename('analisa_rm_sampels', 'legacy_analisa_rm_sampels_backup');
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analisa_rm_sample_glasswares');
        Schema::dropIfExists('analisa_rm_sample_details');
        Schema::dropIfExists('analisa_rm_transaksi');
    }
};

