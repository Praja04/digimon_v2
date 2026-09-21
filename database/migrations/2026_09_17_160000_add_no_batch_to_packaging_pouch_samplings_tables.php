<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('packaging_pouch_samplings')) {
            Schema::table('packaging_pouch_samplings', function (Blueprint $table) {
                if (! Schema::hasColumn('packaging_pouch_samplings', 'no_batch')) {
                    $table->string('no_batch')->nullable()->after('jumlah_sampel');
                }
            });
        }

        if (Schema::hasTable('packaging_pouch_sampling_drafts')) {
            Schema::table('packaging_pouch_sampling_drafts', function (Blueprint $table) {
                if (! Schema::hasColumn('packaging_pouch_sampling_drafts', 'no_batch')) {
                    $table->string('no_batch')->nullable()->after('jumlah_sampel');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('packaging_pouch_samplings')) {
            Schema::table('packaging_pouch_samplings', function (Blueprint $table) {
                if (Schema::hasColumn('packaging_pouch_samplings', 'no_batch')) {
                    $table->dropColumn('no_batch');
                }
            });
        }

        if (Schema::hasTable('packaging_pouch_sampling_drafts')) {
            Schema::table('packaging_pouch_sampling_drafts', function (Blueprint $table) {
                if (Schema::hasColumn('packaging_pouch_sampling_drafts', 'no_batch')) {
                    $table->dropColumn('no_batch');
                }
            });
        }
    }
};
