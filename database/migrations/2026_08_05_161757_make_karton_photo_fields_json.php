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
            'packaging_karton_samplings',
            function (Blueprint $table): void {
                $table->json('jenis_ketidaksesuaian_baru')->nullable();
                $table->json('foto_baru')->nullable();
                $table->json('foto_ketidaksesuaian_baru')->nullable();
            }
        );

        DB::table('packaging_karton_samplings')
            ->select([
                'id',
                'jenis_ketidaksesuaian',
                'foto',
                'foto_ketidaksesuaian',
            ])
            ->orderBy('id')
            ->chunkById(
                100,
                function ($rows): void {
                    foreach ($rows as $row) {
                        DB::table('packaging_karton_samplings')
                            ->where('id', $row->id)
                            ->update([
                                'jenis_ketidaksesuaian_baru' =>
                                    $this->normalizeToJsonArray(
                                        $row->jenis_ketidaksesuaian
                                    ),

                                'foto_baru' =>
                                    $this->normalizeToJsonArray(
                                        $row->foto
                                    ),

                                'foto_ketidaksesuaian_baru' =>
                                    $this->normalizeToJsonArray(
                                        $row->foto_ketidaksesuaian
                                    ),
                            ]);
                    }
                }
            );

        Schema::table(
            'packaging_karton_samplings',
            function (Blueprint $table): void {
                $table->dropColumn([
                    'jenis_ketidaksesuaian',
                    'foto',
                    'foto_ketidaksesuaian',
                ]);
            }
        );

        Schema::table(
            'packaging_karton_samplings',
            function (Blueprint $table): void {
                $table->renameColumn(
                    'jenis_ketidaksesuaian_baru',
                    'jenis_ketidaksesuaian'
                );

                $table->renameColumn(
                    'foto_baru',
                    'foto'
                );

                $table->renameColumn(
                    'foto_ketidaksesuaian_baru',
                    'foto_ketidaksesuaian'
                );
            }
        );
    }

    public function down(): void
    {
        Schema::table(
            'packaging_karton_samplings',
            function (Blueprint $table): void {
                $table->text('jenis_ketidaksesuaian_lama')->nullable();
                $table->text('foto_lama')->nullable();
                $table->text('foto_ketidaksesuaian_lama')->nullable();
            }
        );

        DB::table('packaging_karton_samplings')
            ->select([
                'id',
                'jenis_ketidaksesuaian',
                'foto',
                'foto_ketidaksesuaian',
            ])
            ->orderBy('id')
            ->chunkById(
                100,
                function ($rows): void {
                    foreach ($rows as $row) {
                        DB::table('packaging_karton_samplings')
                            ->where('id', $row->id)
                            ->update([
                                'jenis_ketidaksesuaian_lama' =>
                                    $this->firstArrayValue(
                                        $row->jenis_ketidaksesuaian
                                    ),

                                'foto_lama' =>
                                    $this->firstArrayValue(
                                        $row->foto
                                    ),

                                'foto_ketidaksesuaian_lama' =>
                                    $this->firstArrayValue(
                                        $row->foto_ketidaksesuaian
                                    ),
                            ]);
                    }
                }
            );

        Schema::table(
            'packaging_karton_samplings',
            function (Blueprint $table): void {
                $table->dropColumn([
                    'jenis_ketidaksesuaian',
                    'foto',
                    'foto_ketidaksesuaian',
                ]);
            }
        );

        Schema::table(
            'packaging_karton_samplings',
            function (Blueprint $table): void {
                $table->renameColumn(
                    'jenis_ketidaksesuaian_lama',
                    'jenis_ketidaksesuaian'
                );

                $table->renameColumn(
                    'foto_lama',
                    'foto'
                );

                $table->renameColumn(
                    'foto_ketidaksesuaian_lama',
                    'foto_ketidaksesuaian'
                );
            }
        );
    }

    private function normalizeToJsonArray(
        mixed $value
    ): ?string {
        if ($value === null || trim((string) $value) === '') {
            return null;
        }

        $decoded = json_decode((string) $value, true);

        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            $normalized = array_values(
                array_filter(
                    $decoded,
                    static fn ($item): bool =>
                        $item !== null
                        && trim((string) $item) !== ''
                )
            );
        } else {
            $normalized = [(string) $value];
        }

        return empty($normalized)
            ? null
            : json_encode(
                $normalized,
                JSON_UNESCAPED_UNICODE
                | JSON_UNESCAPED_SLASHES
            );
    }

    private function firstArrayValue(
        mixed $value
    ): ?string {
        if ($value === null || trim((string) $value) === '') {
            return null;
        }

        $decoded = json_decode((string) $value, true);

        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            $first = collect($decoded)
                ->first(
                    static fn ($item): bool =>
                        $item !== null
                        && trim((string) $item) !== ''
                );

            return $first === null
                ? null
                : (string) $first;
        }

        return (string) $value;
    }
};