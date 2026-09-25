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
        Schema::table('press_test_data', function (Blueprint $table) {
            $table->string('nama_analis_field')->nullable()->change();
            $table->string('variant')->nullable()->change();
            $table->double('batas')->nullable()->change();

            $table->string('variant_name')->nullable()->after('variant');
            $table->double('ok_min')->nullable()->after('variant_name');
            $table->double('ok_max')->nullable()->after('ok_min');
            $table->double('bocor_min')->nullable()->after('ok_max');
            $table->double('bocor_max')->nullable()->after('bocor_min');
            $table->double('gap')->nullable()->after('bocor_max');
            $table->text('note')->nullable()->after('gap');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('press_test_data', function (Blueprint $table) {
            $table->dropColumn(['variant_name', 'ok_min', 'ok_max', 'bocor_min', 'bocor_max', 'gap', 'note']);
        });
    }
};
