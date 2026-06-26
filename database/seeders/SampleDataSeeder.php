<?php

namespace Database\Seeders;

use App\Models\Aduan;
use App\Models\NotaAduan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SampleDataSeeder extends Seeder
{
    public function run(): void
    {
        // ── Pentadbir (akaun utama) ──────────────────────────────
        $admin = User::updateOrCreate(
            ['email' => 'admin@depc.test'],
            [
                'name' => 'Pentadbir Sistem',
                'password' => Hash::make('password'),
                'bahagian' => 'HQ',
                'jawatan' => 'Pentadbir',
                'is_active' => true,
            ]
        );
        $admin->syncRoles('Pentadbir');

        // ── Pengurus Operasi ─────────────────────────────────────
        $pengurus = User::updateOrCreate(
            ['email' => 'pengurus@depc.test'],
            [
                'name' => 'Pengurus Operasi',
                'password' => Hash::make('password'),
                'bahagian' => 'HQ',
                'jawatan' => 'Pengurus Operasi',
                'is_active' => true,
            ]
        );
        $pengurus->syncRoles('Pengurus Operasi');

        // ── Penyelia ─────────────────────────────────────────────
        $penyelia = User::updateOrCreate(
            ['email' => 'penyelia@depc.test'],
            [
                'name' => 'Penyelia Penyelenggaraan',
                'password' => Hash::make('password'),
                'bahagian' => 'FP',
                'jawatan' => 'Penyelia',
                'is_active' => true,
            ]
        );
        $penyelia->syncRoles('Penyelia Penyelenggaraan');

        // ── Juruteknik ───────────────────────────────────────────
        $juru1 = User::updateOrCreate(
            ['email' => 'juruteknik1@depc.test'],
            [
                'name' => 'Ahmad Juruteknik',
                'password' => Hash::make('password'),
                'bahagian' => 'FP',
                'jawatan' => 'Juruteknik',
                'is_active' => true,
            ]
        );
        $juru1->syncRoles('Juruteknik');

        $juru2 = User::updateOrCreate(
            ['email' => 'juruteknik2@depc.test'],
            [
                'name' => 'Siti Juruteknik',
                'password' => Hash::make('password'),
                'bahagian' => 'CP',
                'jawatan' => 'Juruteknik',
                'is_active' => true,
            ]
        );
        $juru2->syncRoles('Juruteknik');

        // ── Aduan sampel ─────────────────────────────────────────
        $aduan1 = Aduan::create([
            'nama_pelapor' => 'Rosli bin Kassim',
            'bahagian_pelapor' => 'FP',
            'no_telefon_pelapor' => '0123456789',
            'nama_peralatan' => 'Mesin Penyejuk Beku No. 3',
            'lokasi' => 'Bilik Penyejukan FP',
            'perihal_kerosakan' => 'Suhu tidak turun ke paras yang ditetapkan, berbunyi kuat.',
            'tarikh_rosak' => now()->subDays(2)->toDateString(),
            'keutamaan' => 'Tinggi',
            'kategori' => 'Penyejukan',
        ]);

        $aduan2 = Aduan::create([
            'nama_pelapor' => 'Nurul Ain',
            'bahagian_pelapor' => 'CP',
            'no_telefon_pelapor' => '0198765432',
            'nama_peralatan' => 'Lampu Dewan Pembungkusan',
            'lokasi' => 'Dewan Pembungkusan CP',
            'perihal_kerosakan' => 'Lampu kelip-kelip dan sebahagian tidak menyala.',
            'tarikh_rosak' => now()->subDay()->toDateString(),
            'keutamaan' => 'Sederhana',
            'kategori' => 'Elektrikal',
        ]);

        // Aduan dalam proses (sudah ditugaskan)
        $aduan3 = Aduan::create([
            'nama_pelapor' => 'Kamal Hassan',
            'bahagian_pelapor' => 'D/S',
            'nama_peralatan' => 'Paip Air Utama',
            'lokasi' => 'Kawasan Cucian D/S',
            'perihal_kerosakan' => 'Paip bocor menyebabkan lantai sentiasa basah.',
            'tarikh_rosak' => now()->subDays(3)->toDateString(),
            'keutamaan' => 'Kritikal',
            'kategori' => 'Paip',
            'status' => 'Dalam Proses',
            'juruteknik_id' => $juru1->id,
            'tarikh_sasaran_siap' => now()->addDay()->toDateString(),
            'catatan_penyelia' => 'Sila utamakan, risiko keselamatan.',
        ]);

        NotaAduan::create([
            'aduan_id' => $aduan3->id,
            'user_id' => $penyelia->id,
            'jenis' => 'tugasan',
            'kandungan' => 'Ditugaskan kepada ' . $juru1->name,
        ]);
    }
}
