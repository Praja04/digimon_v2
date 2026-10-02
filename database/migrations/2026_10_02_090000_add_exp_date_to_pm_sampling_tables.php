<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = [
            'packaging_inner_outer_samplings',
            'packaging_inner_outer_sampling_drafts',
            'packaging_pouch_samplings',
            'packaging_pouch_sampling_drafts',
            'packaging_karton_samplings',
            'packaging_karton_sampling_drafts',
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    if (! Schema::hasColumn($tableName, 'exp_date')) {
                        $table->date('exp_date')->nullable()->after('no_batch');
                    }
                });
            }
        }
    }

    public function down(): void
    {
        $tables = [
            'packaging_inner_outer_samplings',
            'packaging_inner_outer_sampling_drafts',
            'packaging_pouch_samplings',
            'packaging_pouch_sampling_drafts',
            'packaging_karton_samplings',
            'packaging_karton_sampling_drafts',
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    if (Schema::hasColumn($tableName, 'exp_date')) {
                        $table->dropColumn('exp_date');
                    }
                });
            }
        }
    }
};
