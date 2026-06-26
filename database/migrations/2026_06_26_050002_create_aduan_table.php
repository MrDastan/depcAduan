<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aduan', function (Blueprint $table) {
            $table->id();
            $table->string('no_tiket')->unique();          // ADU-2026-0001
            $table->string('nama_pelapor');
            $table->string('bahagian_pelapor');
            $table->string('no_telefon_pelapor')->nullable();
            $table->string('nama_peralatan');
            $table->string('lokasi');
            $table->text('perihal_kerosakan');
            $table->date('tarikh_rosak');
            $table->enum('keutamaan', ['Rendah', 'Sederhana', 'Tinggi', 'Kritikal'])->default('Sederhana');
            $table->enum('status', ['Baru', 'Dalam Proses', 'Selesai', 'Ditutup'])->default('Baru');
            $table->enum('kategori', ['Elektrikal', 'Mekanikal', 'Paip', 'Penyejukan', 'Struktur', 'Lain-lain'])->nullable();
            $table->foreignId('juruteknik_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('disahkan_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('diluluskan_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->date('tarikh_sasaran_siap')->nullable();
            $table->date('tarikh_siap')->nullable();
            $table->text('catatan_penyelia')->nullable();
            $table->text('tindakan_juruteknik')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aduan');
    }
};
