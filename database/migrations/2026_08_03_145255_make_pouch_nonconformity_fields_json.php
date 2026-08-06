<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement(
            'ALTER TABLE packaging_pouch_samplings '
            . 'ADD COLUMN jenis_ketidaksesuaian_json JSON NULL '
            . 'AFTER konfirmasi_ketidaksesuaian'
        );

        DB::statement(
            "UPDATE packaging_pouch_samplings "
            . "SET jenis_ketidaksesuaian_json = CASE "
            . "WHEN jenis_ketidaksesuaian IS NULL OR TRIM(jenis_ketidaksesuaian) = '' THEN JSON_ARRAY() "
            . "ELSE JSON_ARRAY(jenis_ketidaksesuaian) END"
        );

        DB::statement(
            'ALTER TABLE packaging_pouch_samplings '
            . 'DROP COLUMN jenis_ketidaksesuaian'
        );

        DB::statement(
            'ALTER TABLE packaging_pouch_samplings '
            . 'RENAME COLUMN jenis_ketidaksesuaian_json TO jenis_ketidaksesuaian'
        );

        DB::statement(
            'ALTER TABLE packaging_pouch_samplings '
            . 'ADD COLUMN foto_ketidaksesuaian_json JSON NULL '
            . 'AFTER foto_pengecekan'
        );

        DB::statement(
            "UPDATE packaging_pouch_samplings "
            . "SET foto_ketidaksesuaian_json = CASE "
            . "WHEN foto_ketidaksesuaian IS NULL OR TRIM(foto_ketidaksesuaian) = '' THEN JSON_ARRAY() "
            . "ELSE JSON_ARRAY(foto_ketidaksesuaian) END"
        );

        DB::statement(
            'ALTER TABLE packaging_pouch_samplings '
            . 'DROP COLUMN foto_ketidaksesuaian'
        );

        DB::statement(
            'ALTER TABLE packaging_pouch_samplings '
            . 'RENAME COLUMN foto_ketidaksesuaian_json TO foto_ketidaksesuaian'
        );
    }

    public function down(): void
    {
        DB::statement(
            'ALTER TABLE packaging_pouch_samplings '
            . 'ADD COLUMN jenis_ketidaksesuaian_string VARCHAR(255) NULL '
            . 'AFTER konfirmasi_ketidaksesuaian'
        );

        DB::statement(
            "UPDATE packaging_pouch_samplings "
            . "SET jenis_ketidaksesuaian_string = JSON_UNQUOTE(JSON_EXTRACT(jenis_ketidaksesuaian, '$[0]'))"
        );

        DB::statement(
            'ALTER TABLE packaging_pouch_samplings '
            . 'DROP COLUMN jenis_ketidaksesuaian'
        );

        DB::statement(
            'ALTER TABLE packaging_pouch_samplings '
            . 'RENAME COLUMN jenis_ketidaksesuaian_string TO jenis_ketidaksesuaian'
        );

        DB::statement(
            'ALTER TABLE packaging_pouch_samplings '
            . 'ADD COLUMN foto_ketidaksesuaian_string VARCHAR(255) NULL '
            . 'AFTER foto_pengecekan'
        );

        DB::statement(
            "UPDATE packaging_pouch_samplings "
            . "SET foto_ketidaksesuaian_string = JSON_UNQUOTE(JSON_EXTRACT(foto_ketidaksesuaian, '$[0]'))"
        );

        DB::statement(
            'ALTER TABLE packaging_pouch_samplings '
            . 'DROP COLUMN foto_ketidaksesuaian'
        );

        DB::statement(
            'ALTER TABLE packaging_pouch_samplings '
            . 'RENAME COLUMN foto_ketidaksesuaian_string TO foto_ketidaksesuaian'
        );
    }
};