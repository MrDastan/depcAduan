<?php

namespace App\Filament\Widgets;

use App\Models\Aduan;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AduanStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            Stat::make('Belum Ditindak', Aduan::where('status', 'Baru')->count())
                ->description('Aduan baru')
                ->color('danger')
                ->icon('heroicon-o-exclamation-circle'),
            Stat::make('Dalam Proses', Aduan::where('status', 'Dalam Proses')->count())
                ->description('Sedang dibaiki')
                ->color('warning')
                ->icon('heroicon-o-arrow-path'),
            Stat::make('Selesai Bulan Ini', Aduan::where('status', 'Selesai')
                ->whereMonth('tarikh_siap', now()->month)
                ->whereYear('tarikh_siap', now()->year)
                ->count())
                ->description('Siap dibaiki')
                ->color('success')
                ->icon('heroicon-o-check-circle'),
            Stat::make('Jumlah Bulan Ini', Aduan::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count())
                ->description('Aduan diterima')
                ->color('primary')
                ->icon('heroicon-o-clipboard-document-list'),
        ];
    }
}
