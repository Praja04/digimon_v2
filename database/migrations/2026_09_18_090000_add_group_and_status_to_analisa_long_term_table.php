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
        Schema::table('analisa_long_term', function (Blueprint $table) {
            if (!Schema::hasColumn('analisa_long_term', 'group')) {
                $table->string('group')->nullable()->after('disposisi');
            }
            if (!Schema::hasColumn('analisa_long_term', 'status')) {
                $table->string('status')->default('final')->after('group');
            }
            if (Schema::hasColumn('analisa_long_term', 'attachment')) {
                $table->text('attachment')->nullable()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('analisa_long_term', function (Blueprint $table) {
            if (Schema::hasColumn('analisa_long_term', 'group')) {
                $table->dropColumn('group');
            }
            if (Schema::hasColumn('analisa_long_term', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
