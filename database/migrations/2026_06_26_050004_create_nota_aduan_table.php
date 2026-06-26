<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nota_aduan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aduan_id')->constrained('aduan')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('jenis');   // 'tugasan', 'kemaskini', 'selesai', 'nota'
            $table->text('kandungan');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nota_aduan');
    }
};
