<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('analisa_rm_sampels')) {
            Schema::create('analisa_rm_sampels', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('id_identitas');
                $table->string('kategori', 50)->default('incoming'); // incoming, sta, monitoring
                $table->unsignedInteger('sampel_ke')->default(1);

                // Parameter Pengujian Fisika & Kimia
                $table->double('brix')->nullable();
                $table->double('ph')->nullable();
                $table->double('ka')->nullable(); // % Kadar Air
                $table->double('nacl')->nullable(); // % NaCl
                $table->double('berat_jenis')->nullable();
                $table->double('suhu')->nullable();
                $table->double('aw')->nullable();
                $table->double('viskositas')->nullable();
                $table->double('tn')->nullable(); // % Total Nitrogen
                $table->double('gross_weight')->nullable();

                // Alat & Penimbangan Laboratorium (Gula Kelapa / Gula Tebu / dll)
                $table->string('no_beaker', 100)->nullable();
                $table->decimal('berat_beaker_500', 10, 4)->nullable();
                $table->decimal('berat_beaker_250', 10, 4)->nullable();
                $table->decimal('timbang_a', 10, 4)->nullable();
                $table->decimal('timbang_b', 10, 4)->nullable();
                $table->string('no_cawan', 100)->nullable();
                $table->decimal('berat_cawan', 10, 4)->nullable();
                $table->decimal('timbang_aa', 10, 4)->nullable();

                // Parameter Kualitatif & Sensori
                $table->string('kotoran', 255)->nullable();
                $table->string('organo', 255)->nullable();
                $table->string('warna', 255)->nullable();
                $table->string('aroma', 255)->nullable();
                $table->string('fisik', 255)->nullable();
                $table->string('rasa', 255)->nullable();
                $table->string('aroma_pengotor', 255)->nullable();

                // Status & Disposisi
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

                $table->index(['id_identitas', 'kategori']);
                $table->index('status');
            });
        }

        // Salin data lama ke analisa_rm_sampels agar tidak ada data hilang
        if (Schema::hasTable('analisa_short_term')) {
            $shortTerms = DB::table('analisa_short_term')->get();
            $sampleCounter = [];
            foreach ($shortTerms as $st) {
                $key = $st->id_identitas . '_' . ($st->kategori ?? 'incoming');
                $sampleCounter[$key] = ($sampleCounter[$key] ?? 0) + 1;

                DB::table('analisa_rm_sampels')->insert([
                    'id_identitas'    => $st->id_identitas,
                    'kategori'        => $st->kategori ?? 'incoming',
                    'sampel_ke'       => $sampleCounter[$key],
                    'brix'            => $st->brix,
                    'ph'              => $st->ph,
                    'no_beaker'       => $st->no_beaker,
                    'berat_beaker_500'=> $st->berat_beaker_500,
                    'berat_beaker_250'=> $st->berat_beaker_250,
                    'timbang_a'       => $st->timbang_a,
                    'timbang_b'       => $st->timbang_b,
                    'kotoran'         => $st->kotoran,
                    'rasa'            => $st->rasa,
                    'aroma_pengotor'  => $st->aroma_pengotor,
                    'no_cawan'        => $st->no_cawan,
                    'berat_cawan'     => $st->berat_cawan,
                    'timbang_aa'      => $st->timbang_aa,
                    'ka'              => $st->ka,
                    'organo'          => $st->organo,
                    'warna'           => $st->warna,
                    'aroma'           => $st->aroma,
                    'disposisi'       => $st->disposisi,
                    'status'          => $st->status ?? 'final',
                    'keterangan'      => $st->keterangan,
                    'created_by'      => $st->created_by,
                    'created_at'      => $st->created_at,
                    'updated_at'      => $st->updated_at,
                ]);
            }
        }

        if (Schema::hasTable('analisa_garam_gula')) {
            $garamGulas = DB::table('analisa_garam_gula')->get();
            foreach ($garamGulas as $gg) {
                DB::table('analisa_rm_sampels')->insert([
                    'id_identitas'    => $gg->id_identitas,
                    'kategori'        => 'incoming',
                    'sampel_ke'       => 1,
                    'ka'              => is_numeric($gg->{'%ka'} ?? null) ? (float)$gg->{'%ka'} : null,
                    'nacl'            => is_numeric($gg->{'%nacl'} ?? null) ? (float)$gg->{'%nacl'} : null,
                    'gross_weight'    => is_numeric($gg->gross_weight ?? null) ? (float)$gg->gross_weight : null,
                    'kotoran'         => $gg->kotoran,
                    'fisik'           => $gg->fisik,
                    'organo'          => $gg->organo,
                    'warna'           => $gg->warna,
                    'aroma'           => $gg->aroma,
                    'disposisi'       => $gg->disposisi,
                    'status'          => $gg->status ?? 'final',
                    'keterangan'      => $gg->keterangan,
                    'created_by'      => $gg->created_by,
                    'created_at'      => $gg->created_at,
                    'updated_at'      => $gg->updated_at,
                ]);
            }
        }

        if (Schema::hasTable('analisa_bahan_cair')) {
            $bahanCairs = DB::table('analisa_bahan_cair')->get();
            foreach ($bahanCairs as $bc) {
                DB::table('analisa_rm_sampels')->insert([
                    'id_identitas'    => $bc->id_identitas,
                    'kategori'        => 'incoming',
                    'sampel_ke'       => $bc->sampel_ke ?? 1,
                    'brix'            => $bc->brix,
                    'ph'              => $bc->ph,
                    'berat_jenis'     => $bc->berat_jenis,
                    'suhu'            => $bc->suhu,
                    'aw'              => $bc->aw,
                    'viskositas'      => $bc->viskositas,
                    'nacl'            => $bc->nacl,
                    'tn'              => $bc->tn,
                    'gross_weight'    => $bc->gross_weight,
                    'warna'           => $bc->warna,
                    'aroma'           => $bc->aroma,
                    'organo'          => $bc->organo,
                    'disposisi'       => $bc->disposisi,
                    'status'          => $bc->status ?? 'final',
                    'keterangan'      => $bc->keterangan,
                    'created_by'      => $bc->created_by,
                    'created_at'      => $bc->created_at,
                    'updated_at'      => $bc->updated_at,
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('analisa_rm_sampels');
    }
};

