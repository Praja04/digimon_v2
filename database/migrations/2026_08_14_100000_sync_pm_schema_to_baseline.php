<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->syncJenisIncomings();
        $this->syncSuppliers();
        $this->syncPackagingIncomings();
        $this->syncInnerOuterSamplings();
        $this->syncKartonSamplings();
        $this->syncPouchSamplings();
    }

    public function down(): void
    {
        /*
         * Sengaja no-op.
         * Migration ini adalah migration rekonsiliasi untuk menyamakan
         * database existing dengan baseline PM yang sudah dipakai aplikasi.
         * Rollback tidak boleh menghapus data/kolom yang mungkin sudah ada
         * sebelum migration ini dibuat.
         */
    }

    private function syncJenisIncomings(): void
    {
        if (! Schema::hasTable('jenis_incomings')) {
            return;
        }

        if (! Schema::hasColumn('jenis_incomings', 'status')) {
            Schema::table('jenis_incomings', function (Blueprint $table): void {
                $table->boolean('status')->default(true);
            });
        }

        DB::statement(
            'ALTER TABLE `jenis_incomings` '
            . 'MODIFY `kategori` VARCHAR(50) NOT NULL, '
            . 'MODIFY `nama` VARCHAR(255) NOT NULL, '
            . 'MODIFY `status` TINYINT(1) NOT NULL DEFAULT 1'
        );
    }

    private function syncSuppliers(): void
    {
        if (! Schema::hasTable('suppliers')) {
            return;
        }

        DB::statement(
            'ALTER TABLE `suppliers` '
            . 'MODIFY `jenis_incoming_id` BIGINT UNSIGNED NULL, '
            . 'MODIFY `kode` VARCHAR(50) NULL, '
            . 'MODIFY `nama` VARCHAR(150) NULL, '
            . 'MODIFY `status` TINYINT(1) NOT NULL DEFAULT 1'
        );
    }

    private function syncPackagingIncomings(): void
    {
        if (! Schema::hasTable('packaging_incomings')) {
            return;
        }

        if (! Schema::hasColumn('packaging_incomings', 'jumlah')) {
            Schema::table('packaging_incomings', function (Blueprint $table): void {
                $table->decimal('jumlah', 15, 2)->nullable();
            });
        }

        if (Schema::hasColumn('packaging_incomings', 'quantity_incoming')) {
            DB::statement(
                'UPDATE `packaging_incomings` '
                . 'SET `jumlah` = COALESCE(`jumlah`, `quantity_incoming`) '
                . 'WHERE `quantity_incoming` IS NOT NULL'
            );

            Schema::table('packaging_incomings', function (Blueprint $table): void {
                $table->dropColumn('quantity_incoming');
            });
        }

        if (! Schema::hasColumn('packaging_incomings', 'jumlah_sampel')) {
            Schema::table('packaging_incomings', function (Blueprint $table): void {
                $table->unsignedInteger('jumlah_sampel')->nullable();
            });
        }
    }

    private function syncInnerOuterSamplings(): void
    {
        $tableName = 'packaging_inner_outer_samplings';

        if (! Schema::hasTable($tableName)) {
            return;
        }

        if (! Schema::hasColumn($tableName, 'status_proses')) {
            Schema::table($tableName, function (Blueprint $table): void {
                $table->string('status_proses', 20)->default('draft');
            });
        }

        foreach ([
            'jenis_ketidaksesuaian',
            'foto_pengecekan',
            'foto_ketidaksesuaian',
        ] as $column) {
            $this->ensureJsonArrayColumn($tableName, $column);
        }
    }

    private function syncKartonSamplings(): void
    {
        $tableName = 'packaging_karton_samplings';

        if (! Schema::hasTable($tableName)) {
            return;
        }

        foreach ([
            'jenis_ketidaksesuaian',
            'foto',
            'foto_ketidaksesuaian',
        ] as $column) {
            $this->ensureJsonArrayColumn($tableName, $column);
        }
    }

    private function syncPouchSamplings(): void
    {
        $tableName = 'packaging_pouch_samplings';

        if (! Schema::hasTable($tableName)) {
            return;
        }

        if (! Schema::hasColumn($tableName, 'status_proses')) {
            Schema::table($tableName, function (Blueprint $table): void {
                $table->string('status_proses', 20)->default('draft');
            });
        }

        if (! Schema::hasColumn($tableName, 'hasil_thickness')) {
            Schema::table($tableName, function (Blueprint $table): void {
                $table->json('hasil_thickness')->nullable();
            });
        }

        $this->ensureJsonArrayColumn(
            $tableName,
            'jenis_ketidaksesuaian'
        );

        $this->ensureJsonArrayColumn(
            $tableName,
            'foto_ketidaksesuaian'
        );
    }

    private function ensureJsonArrayColumn(
        string $tableName,
        string $columnName
    ): void {
        if (! Schema::hasColumn($tableName, $columnName)) {
            Schema::table(
                $tableName,
                function (Blueprint $table) use ($columnName): void {
                    $table->json($columnName)->nullable();
                }
            );

            return;
        }

        if ($this->columnDataType($tableName, $columnName) === 'json') {
            return;
        }

        $temporaryColumn = $columnName . '_json_sync';

        if (! Schema::hasColumn($tableName, $temporaryColumn)) {
            Schema::table(
                $tableName,
                function (Blueprint $table) use ($temporaryColumn): void {
                    $table->json($temporaryColumn)->nullable();
                }
            );
        }

        DB::table($tableName)
            ->select(['id', $columnName])
            ->orderBy('id')
            ->chunkById(
                100,
                function ($rows) use (
                    $tableName,
                    $columnName,
                    $temporaryColumn
                ): void {
                    foreach ($rows as $row) {
                        DB::table($tableName)
                            ->where('id', $row->id)
                            ->update([
                                $temporaryColumn =>
                                    $this->normalizeToJsonArray(
                                        $row->{$columnName}
                                    ),
                            ]);
                    }
                }
            );

        Schema::table(
            $tableName,
            function (Blueprint $table) use ($columnName): void {
                $table->dropColumn($columnName);
            }
        );

        Schema::table(
            $tableName,
            function (Blueprint $table) use (
                $columnName,
                $temporaryColumn
            ): void {
                $table->renameColumn(
                    $temporaryColumn,
                    $columnName
                );
            }
        );
    }

    private function columnDataType(
        string $tableName,
        string $columnName
    ): ?string {
        return DB::table('information_schema.COLUMNS')
            ->where(
                'TABLE_SCHEMA',
                DB::getDatabaseName()
            )
            ->where('TABLE_NAME', $tableName)
            ->where('COLUMN_NAME', $columnName)
            ->value('DATA_TYPE');
    }

    private function normalizeToJsonArray(
        mixed $value
    ): ?string {
        if (
            $value === null
            || trim((string) $value) === ''
        ) {
            return null;
        }

        if (is_string($value)) {
            $decoded = json_decode($value, true);

            if (
                json_last_error() === JSON_ERROR_NONE
                && is_array($decoded)
            ) {
                $items = $decoded;
            } else {
                $items = [$value];
            }
        } elseif (is_array($value)) {
            $items = $value;
        } else {
            $items = [(string) $value];
        }

        $items = array_values(
            array_filter(
                $items,
                static fn ($item): bool =>
                    $item !== null
                    && trim((string) $item) !== ''
            )
        );

        return $items === []
            ? null
            : json_encode(
                $items,
                JSON_UNESCAPED_UNICODE
                | JSON_UNESCAPED_SLASHES
            );
    }
};