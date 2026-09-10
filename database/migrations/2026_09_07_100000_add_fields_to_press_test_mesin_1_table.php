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
        Schema::table('press_test_mesin_1', function (Blueprint $table) {
            if (!Schema::hasColumn('press_test_mesin_1', 'date')) {
                $table->string('date')->nullable()->after('id');
            }
            if (!Schema::hasColumn('press_test_mesin_1', 'time')) {
                $table->string('time')->nullable()->after('date');
            }
            if (!Schema::hasColumn('press_test_mesin_1', 'grup')) {
                $table->string('grup')->nullable()->after('time');
            }
            if (!Schema::hasColumn('press_test_mesin_1', 'shift')) {
                $table->string('shift')->nullable()->after('grup');
            }
            if (!Schema::hasColumn('press_test_mesin_1', 'mesin')) {
                $table->string('mesin')->nullable()->after('shift');
            }
            if (!Schema::hasColumn('press_test_mesin_1', 'press')) {
                $table->string('press')->nullable()->after('mesin');
            }
            if (!Schema::hasColumn('press_test_mesin_1', 'varian')) {
                $table->string('varian')->nullable()->after('press');
            }
            if (!Schema::hasColumn('press_test_mesin_1', 'sample')) {
                $table->string('sample')->nullable()->after('variant');
            }
            if (!Schema::hasColumn('press_test_mesin_1', 'status_sensor')) {
                $table->string('status_sensor')->nullable()->after('status');
            }
            if (!Schema::hasColumn('press_test_mesin_1', 'verif_manual')) {
                $table->string('verif_manual')->nullable()->after('status_sensor');
            }
            if (!Schema::hasColumn('press_test_mesin_1', 'jenis_bocor')) {
                $table->string('jenis_bocor')->nullable()->after('verif_manual');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('press_test_mesin_1', function (Blueprint $table) {
            $table->dropColumn([
                'date',
                'time',
                'grup',
                'shift',
                'mesin',
                'press',
                'varian',
                'sample',
                'status_sensor',
                'verif_manual',
                'jenis_bocor',
            ]);
        });
    }
};
