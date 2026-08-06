<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(
            'packaging_inner_outer_samplings',
            function (Blueprint $table): void {
                $table->json('jenis_ketidaksesuaian_json')->nullable();
                $table->json('foto_pengecekan_json')->nullable();
                $table->json('foto_ketidaksesuaian_json')->nullable();
            }
        );

        DB::table('packaging_inner_outer_samplings')
            ->orderBy('id')
            ->chunkById(100, function ($rows): void {
                foreach ($rows as $row) {
                    DB::table('packaging_inner_outer_samplings')
                        ->where('id', $row->id)
                        ->update([
                            'jenis_ketidaksesuaian_json' =>
                                $this->toJsonArray($row->jenis_ketidaksesuaian),
                            'foto_pengecekan_json' =>
                                $this->toJsonArray($row->foto_pengecekan),
                            'foto_ketidaksesuaian_json' =>
                                $this->toJsonArray($row->foto_ketidaksesuaian),
                        ]);
                }
            });

        Schema::table(
            'packaging_inner_outer_samplings',
            function (Blueprint $table): void {
                $table->dropColumn([
                    'jenis_ketidaksesuaian',
                    'foto_pengecekan',
                    'foto_ketidaksesuaian',
                ]);
            }
        );

        Schema::table(
            'packaging_inner_outer_samplings',
            function (Blueprint $table): void {
                $table->renameColumn(
                    'jenis_ketidaksesuaian_json',
                    'jenis_ketidaksesuaian'
                );
                $table->renameColumn(
                    'foto_pengecekan_json',
                    'foto_pengecekan'
                );
                $table->renameColumn(
                    'foto_ketidaksesuaian_json',
                    'foto_ketidaksesuaian'
                );
            }
        );
    }

    public function down(): void
    {
        Schema::table(
            'packaging_inner_outer_samplings',
            function (Blueprint $table): void {
                $table->text('jenis_ketidaksesuaian_text')->nullable();
                $table->text('foto_pengecekan_text')->nullable();
                $table->text('foto_ketidaksesuaian_text')->nullable();
            }
        );

        DB::table('packaging_inner_outer_samplings')
            ->orderBy('id')
            ->chunkById(100, function ($rows): void {
                foreach ($rows as $row) {
                    DB::table('packaging_inner_outer_samplings')
                        ->where('id', $row->id)
                        ->update([
                            'jenis_ketidaksesuaian_text' =>
                                $this->firstValue($row->jenis_ketidaksesuaian),
                            'foto_pengecekan_text' =>
                                $this->firstValue($row->foto_pengecekan),
                            'foto_ketidaksesuaian_text' =>
                                $this->firstValue($row->foto_ketidaksesuaian),
                        ]);
                }
            });

        Schema::table(
            'packaging_inner_outer_samplings',
            function (Blueprint $table): void {
                $table->dropColumn([
                    'jenis_ketidaksesuaian',
                    'foto_pengecekan',
                    'foto_ketidaksesuaian',
                ]);
            }
        );

        Schema::table(
            'packaging_inner_outer_samplings',
            function (Blueprint $table): void {
                $table->renameColumn(
                    'jenis_ketidaksesuaian_text',
                    'jenis_ketidaksesuaian'
                );
                $table->renameColumn(
                    'foto_pengecekan_text',
                    'foto_pengecekan'
                );
                $table->renameColumn(
                    'foto_ketidaksesuaian_text',
                    'foto_ketidaksesuaian'
                );
            }
        );
    }

    private function toJsonArray(mixed $value): ?string
    {
        if ($value === null || trim((string) $value) === '') {
            return null;
        }

        $decoded = json_decode((string) $value, true);

        $items = is_array($decoded)
            ? $decoded
            : [$value];

        $items = array_values(array_filter(
            $items,
            fn ($item) => $item !== null && trim((string) $item) !== ''
        ));

        return $items === []
            ? null
            : json_encode(
                $items,
                JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
            );
    }

    private function firstValue(mixed $value): ?string
    {
        $decoded = is_string($value)
            ? json_decode($value, true)
            : $value;

        if (is_array($decoded)) {
            return isset($decoded[0])
                ? (string) $decoded[0]
                : null;
        }

        return $decoded !== null
            ? (string) $decoded
            : null;
    }
};