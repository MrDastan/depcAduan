<?php

namespace App\Filament\Widgets;

use App\Models\Aduan;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class AnomaliPenyelenggaraanWidget extends BaseWidget
{
    protected static ?string $heading = 'Anomali Penyelenggaraan (Masa & Kos)';

    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->check() && auth()->user()->can('laporan.view');
    }

    public function table(Table $table): Table
    {
        $purataTempoh = $this->purataTempohSiapMengikutKategori();
        $purataKos = $this->purataKosMengikutKategori();
        $flaggedIds = $this->kenalPastiAduanAnomali($purataTempoh, $purataKos);

        return $table
            ->query(Aduan::query()->whereIn('id', $flaggedIds)->latest())
            ->columns([
                Tables\Columns\TextColumn::make('no_tiket')->label('No. Tiket')->searchable(),
                Tables\Columns\TextColumn::make('nama_peralatan')->label('Peralatan')->limit(25),
                Tables\Columns\TextColumn::make('kategori')->label('Kategori')->placeholder('—'),
                Tables\Columns\TextColumn::make('juruteknik.name')->label('Juruteknik')->placeholder('—'),
                Tables\Columns\TextColumn::make('sebab_anomali')
                    ->label('Sebab Anomali')
                    ->state(fn (Aduan $record) => implode(' | ', $this->sebabAnomali($record, $purataTempoh, $purataKos)))
                    ->wrap()
                    ->color('danger'),
            ])
            ->paginated([10, 25])
            ->emptyStateHeading('Tiada anomali masa atau kos dikesan')
            ->emptyStateIcon('heroicon-o-check-circle');
    }

    protected function purataTempohSiapMengikutKategori(): array
    {
        return Aduan::query()
            ->whereNotNull('tarikh_siap')
            ->get(['kategori', 'tarikh_rosak', 'tarikh_siap'])
            ->groupBy(fn (Aduan $a) => $a->kategori ?? '-')
            ->map(fn ($grp) => $grp->avg(fn (Aduan $a) => $a->tempohSiapHari()))
            ->toArray();
    }

    protected function purataKosMengikutKategori(): array
    {
        return Aduan::query()
            ->whereNotNull('kos_sebenar')
            ->get(['kategori', 'kos_sebenar'])
            ->groupBy(fn (Aduan $a) => $a->kategori ?? '-')
            ->map(fn ($grp) => $grp->avg(fn (Aduan $a) => (float) $a->kos_sebenar))
            ->toArray();
    }

    protected function kenalPastiAduanAnomali(array $purataTempoh, array $purataKos): array
    {
        return Aduan::query()
            ->where(function ($q) {
                $q->lewat()->orWhereNotNull('kos_sebenar');
            })
            ->get()
            ->filter(fn (Aduan $a) => count($this->sebabAnomali($a, $purataTempoh, $purataKos)) > 0)
            ->pluck('id')
            ->all();
    }

    protected function sebabAnomali(Aduan $aduan, array $purataTempoh, array $purataKos): array
    {
        $sebab = [];

        if ($aduan->isLewat()) {
            $sebab[] = $aduan->tarikh_siap
                ? 'Siap lewat drpd tarikh sasaran'
                : 'Belum selesai, sudah melepasi tarikh sasaran';
        }

        $tempoh = $aduan->tempohSiapHari();
        $purataT = $purataTempoh[$aduan->kategori ?? '-'] ?? null;
        if ($tempoh !== null && $purataT && $tempoh > $purataT * 2) {
            $sebab[] = "Tempoh siap ({$tempoh} hari) jauh melebihi purata kategori (" . round($purataT) . ' hari)';
        }

        if ($aduan->kos_sebenar && $aduan->anggaran_kos && (float) $aduan->kos_sebenar > (float) $aduan->anggaran_kos * 1.2) {
            $lebih = round(((float) $aduan->kos_sebenar / (float) $aduan->anggaran_kos - 1) * 100);
            $sebab[] = "Kos sebenar melebihi anggaran ({$lebih}%)";
        }

        $purataK = $purataKos[$aduan->kategori ?? '-'] ?? null;
        if ($aduan->kos_sebenar && $purataK && (float) $aduan->kos_sebenar > $purataK * 2) {
            $sebab[] = 'Kos jauh melebihi purata kategori (RM ' . number_format($purataK, 2) . ')';
        }

        return $sebab;
    }
}
