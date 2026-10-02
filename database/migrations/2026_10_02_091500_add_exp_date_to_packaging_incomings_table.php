<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('packaging_incomings')) {
            Schema::table('packaging_incomings', function (Blueprint $table) {
                if (! Schema::hasColumn('packaging_incomings', 'exp_date')) {
                    $table->date('exp_date')->nullable()->after('tanggal_kedatangan');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('packaging_incomings')) {
            Schema::table('packaging_incomings', function (Blueprint $table) {
                if (Schema::hasColumn('packaging_incomings', 'exp_date')) {
                    $table->dropColumn('exp_date');
                }
            });
        }
    }
};
