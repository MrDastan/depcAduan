<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('aduan', function (Blueprint $table) {
            $table->decimal('anggaran_kos', 10, 2)->nullable()->after('tarikh_siap');
            $table->decimal('kos_sebenar', 10, 2)->nullable()->after('anggaran_kos');
        });
    }

    public function down(): void
    {
        Schema::table('aduan', function (Blueprint $table) {
            $table->dropColumn(['anggaran_kos', 'kos_sebenar']);
        });
    }
};
