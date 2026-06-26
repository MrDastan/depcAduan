<?php

namespace App\Filament\Widgets;

use App\Models\Aduan;
use Filament\Widgets\ChartWidget;

class AduanChart extends ChartWidget
{
    protected static ?string $heading = 'Aduan 6 Bulan Terakhir';

    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $labels = [];
        $diterima = [];
        $selesai = [];

        for ($i = 5; $i >= 0; $i--) {
            $bulan = now()->subMonths($i);
            $labels[] = $bulan->translatedFormat('M Y');

            $diterima[] = Aduan::whereYear('created_at', $bulan->year)
                ->whereMonth('created_at', $bulan->month)
                ->count();

            $selesai[] = Aduan::whereYear('tarikh_siap', $bulan->year)
                ->whereMonth('tarikh_siap', $bulan->month)
                ->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Diterima',
                    'data' => $diterima,
                    'backgroundColor' => 'rgba(13, 148, 136, 0.5)',
                    'borderColor' => 'rgb(13, 148, 136)',
                ],
                [
                    'label' => 'Selesai',
                    'data' => $selesai,
                    'backgroundColor' => 'rgba(34, 197, 94, 0.5)',
                    'borderColor' => 'rgb(34, 197, 94)',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
