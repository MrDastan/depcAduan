<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Aduan;
use App\Models\NotaAduan;
use App\Models\User;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'baru'        => Aduan::where('status', 'Baru')->count(),
            'dalam_proses' => Aduan::where('status', 'Dalam Proses')->count(),
            'selesai'     => Aduan::where('status', 'Selesai')->whereMonth('tarikh_siap', now()->month)->count(),
            'jumlah'      => Aduan::whereMonth('created_at', now()->month)->count(),
        ];

        $terbaru = Aduan::with('juruteknik')->latest()->take(4)->get();

        return view('admin.dashboard', compact('stats', 'terbaru'));
    }

    public function notifikasi()
    {
        $notifs = collect();

        Aduan::where('keutamaan', 'Kritikal')->where('status', 'Baru')->latest()->each(function ($a) use (&$notifs) {
            $notifs->push(['warna' => 'red', 'teks' => 'Kritikal: ' . $a->nama_peralatan . ' (' . $a->bahagian_pelapor . ') — tiada juruteknik ditugaskan', 'sub' => $a->no_tiket . ' · ' . $a->created_at->format('d M Y')]);
        });

        Aduan::where('keutamaan', 'Tinggi')->where('status', 'Baru')->where('created_at', '<', now()->subDay())->latest()->each(function ($a) use (&$notifs) {
            $notifs->push(['warna' => 'orange', 'teks' => 'Tinggi: ' . $a->nama_peralatan . ' — belum ada tindakan (' . $a->created_at->diffForHumans() . ')', 'sub' => $a->no_tiket . ' · ' . $a->bahagian_pelapor]);
        });

        Aduan::where('status', 'Dalam Proses')->whereNotNull('tarikh_sasaran_siap')->where('tarikh_sasaran_siap', '<', now()->toDateString())->latest()->each(function ($a) use (&$notifs) {
            $notifs->push(['warna' => 'amber', 'teks' => 'Melebihi sasaran: ' . $a->nama_peralatan . ' — sasaran ' . $a->tarikh_sasaran_siap->format('d/m/Y'), 'sub' => $a->no_tiket . ' · ' . ($a->juruteknik?->name ?? '—')]);
        });

        Aduan::where('status', 'Selesai')->where('tarikh_siap', '>=', now()->startOfWeek())->latest()->take(5)->each(function ($a) use (&$notifs) {
            $notifs->push(['warna' => 'green', 'teks' => $a->no_tiket . ' ' . $a->nama_peralatan . ' — disahkan selesai', 'sub' => $a->bahagian_pelapor . ' · ' . $a->tarikh_siap->format('d M Y')]);
        });

        $laporan_notif = ['warna' => 'blue', 'teks' => 'Laporan bulanan ' . now()->translatedFormat('F Y') . ' perlu diserahkan sebelum ' . now()->endOfMonth()->format('d M Y'), 'sub' => 'Peringatan sistem'];
        $notifs->push($laporan_notif);

        $log = NotaAduan::with(['aduan', 'user'])->latest()->take(10)->get();

        return view('admin.notifikasi', compact('notifs', 'log'));
    }

    public function pengguna()
    {
        $roles = [
            ['init' => 'AD', 'cls' => 'av-ad', 'title' => 'Pentadbir Sistem',           'level' => 'Level 4', 'warna' => '#3C3489',
             'desc' => 'Akses penuh — urus semua pengguna, tetapan, laporan.',
             'ya'   => ['Urus pengguna & akses', 'Eksport semua laporan', 'Tetapan sistem', 'Lulus semua peringkat'], 'tidak' => []],
            ['init' => 'MG', 'cls' => 'av-mg', 'title' => 'Pengurus Operasi',            'level' => 'Level 3', 'warna' => '#EF9F27',
             'desc' => 'Lihat semua aduan, lulus pembelian, kelulusan akhir.',
             'ya'   => ['Lihat semua aduan & laporan', 'Lulus permintaan pembelian', 'Kelulusan akhir aduan'], 'tidak' => ['Tambah / ubah rekod aduan']],
            ['init' => 'SV', 'cls' => 'av-sv', 'title' => 'Penyelia Penyelenggaraan',   'level' => 'Level 2', 'warna' => '#1D9E75',
             'desc' => 'Urus aduan harian, tugaskan juruteknik, sahkan selesai.',
             'ya'   => ['Tambah & edit aduan', 'Tugaskan juruteknik', 'Sahkan selesai (peringkat 1)'], 'tidak' => ['Lulus pembelian / kelulusan akhir']],
            ['init' => 'JT', 'cls' => 'av-tc', 'title' => 'Juruteknik',                  'level' => 'Level 1', 'warna' => '#185FA5',
             'desc' => 'Lihat tugasan sendiri, kemaskini status, lampir gambar.',
             'ya'   => ['Lihat tugasan diberikan', 'Kemaskini status & tindakan', 'Lampirkan gambar / dokumen'], 'tidak' => ['Lihat aduan pengguna lain']],
        ];

        $pengguna = User::with('roles')->where('is_active', true)->get();

        return view('admin.pengguna', compact('roles', 'pengguna'));
    }
}
