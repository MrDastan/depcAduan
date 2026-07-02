<?php

namespace App\Filament\Widgets;

use App\Models\Aduan;
use App\Services\AnomaliPenyelenggaraanService;
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
        $anomali = app(AnomaliPenyelenggaraanService::class)->aduanAnomali();
        $sebabById = $anomali->keyBy(fn (array $row) => $row['aduan']->id)
            ->map(fn (array $row) => implode(' | ', $row['sebab']));

        return $table
            ->query(Aduan::query()->whereIn('id', $anomali->pluck('aduan.id'))->latest())
            ->columns([
                Tables\Columns\TextColumn::make('no_tiket')->label('No. Tiket')->searchable(),
                Tables\Columns\TextColumn::make('nama_peralatan')->label('Peralatan')->limit(25),
                Tables\Columns\TextColumn::make('kategori')->label('Kategori')->placeholder('—'),
                Tables\Columns\TextColumn::make('juruteknik.name')->label('Juruteknik')->placeholder('—'),
                Tables\Columns\TextColumn::make('sebab_anomali')
                    ->label('Sebab Anomali')
                    ->state(fn (Aduan $record) => $sebabById[$record->id] ?? '-')
                    ->wrap()
                    ->color('danger'),
            ])
            ->paginated([10, 25])
            ->emptyStateHeading('Tiada anomali masa atau kos dikesan')
            ->emptyStateIcon('heroicon-o-check-circle');
    }
}
