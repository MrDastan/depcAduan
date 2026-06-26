<?php

namespace App\Filament\Pages;

use App\Models\Aduan;
use App\Models\NotaAduan;
use Filament\Pages\Page;
use Illuminate\Support\Collection;

class NotifikasiAduan extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-bell';

    protected static ?string $navigationLabel = 'Notifikasi';

    protected static ?string $title = 'Notifikasi';

    protected static ?string $navigationGroup = 'Penyelenggaraan';

    protected static ?int $navigationSort = 3;

    protected static string $view = 'filament.pages.notifikasi-aduan';

    public static function getNavigationBadge(): ?string
    {
        $count = static::getNotifCount();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    private static function getNotifCount(): int
    {
        return Aduan::whereIn('keutamaan', ['Kritikal', 'Tinggi'])
            ->where('status', 'Baru')
            ->count();
    }

    public function getViewData(): array
    {
        $notifs = collect();

        // Aduan kritikal tanpa juruteknik
        Aduan::where('keutamaan', 'Kritikal')->where('status', 'Baru')->latest()->each(function ($a) use (&$notifs) {
            $notifs->push([
                'warna' => 'red',
                'teks' => 'Kritikal: ' . $a->nama_peralatan . ' (' . $a->bahagian_pelapor . ') — tiada juruteknik ditugaskan',
                'sub' => $a->no_tiket . ' · ' . $a->created_at->format('d M Y'),
                'tiket' => $a->no_tiket,
            ]);
        });

        // Aduan Tinggi tanpa juruteknik lebih 1 hari
        Aduan::where('keutamaan', 'Tinggi')->where('status', 'Baru')
            ->where('created_at', '<', now()->subDay())->latest()->each(function ($a) use (&$notifs) {
            $notifs->push([
                'warna' => 'orange',
                'teks' => 'Tinggi: ' . $a->nama_peralatan . ' — belum ada tindakan (' . $a->created_at->diffForHumans() . ')',
                'sub' => $a->no_tiket . ' · ' . $a->bahagian_pelapor,
                'tiket' => $a->no_tiket,
            ]);
        });

        // Aduan melebihi sasaran siap
        Aduan::where('status', 'Dalam Proses')
            ->whereNotNull('tarikh_sasaran_siap')
            ->where('tarikh_sasaran_siap', '<', now()->toDateString())
            ->latest()->each(function ($a) use (&$notifs) {
            $notifs->push([
                'warna' => 'amber',
                'teks' => 'Melebihi sasaran: ' . $a->nama_peralatan . ' — sasaran ' . $a->tarikh_sasaran_siap->format('d/m/Y'),
                'sub' => $a->no_tiket . ' · Juruteknik: ' . ($a->juruteknik?->name ?? '—'),
                'tiket' => $a->no_tiket,
            ]);
        });

        // Aduan selesai minggu ini
        Aduan::where('status', 'Selesai')
            ->where('tarikh_siap', '>=', now()->startOfWeek())
            ->latest()->take(5)->each(function ($a) use (&$notifs) {
            $notifs->push([
                'warna' => 'green',
                'teks' => $a->no_tiket . ' ' . $a->nama_peralatan . ' — disahkan selesai',
                'sub' => $a->bahagian_pelapor . ' · ' . $a->tarikh_siap->format('d M Y'),
                'tiket' => $a->no_tiket,
            ]);
        });

        // Log aktiviti terkini
        $log = NotaAduan::with(['aduan', 'user'])
            ->latest()
            ->take(10)
            ->get();

        return [
            'notifs' => $notifs,
            'log' => $log,
        ];
    }
}
