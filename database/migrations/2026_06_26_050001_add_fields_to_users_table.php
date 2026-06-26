<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('bahagian')->nullable()->after('email');       // FP, CP, D/S, HQ dll
            $table->string('no_telefon')->nullable()->after('bahagian');
            $table->string('jawatan')->nullable()->after('no_telefon');   // Juruteknik, Penyelia dll
            $table->boolean('is_active')->default(true)->after('jawatan');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['bahagian', 'no_telefon', 'jawatan', 'is_active']);
        });
    }
};
