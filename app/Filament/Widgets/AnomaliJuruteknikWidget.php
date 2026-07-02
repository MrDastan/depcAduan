<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Services\AnomaliPenyelenggaraanService;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class AnomaliJuruteknikWidget extends BaseWidget
{
    protected static ?string $heading = 'Anomali Prestasi Juruteknik';

    protected static ?int $sort = 5;

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->check() && auth()->user()->can('laporan.view');
    }

    public function table(Table $table): Table
    {
        $anomali = app(AnomaliPenyelenggaraanService::class)->juruteknikAnomali();
        $statById = $anomali->keyBy(fn (array $row) => $row['juruteknik']->id);

        return $table
            ->query(User::query()->whereIn('id', $anomali->pluck('juruteknik.id')))
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Juruteknik'),
                Tables\Columns\TextColumn::make('jawatan')->label('Jawatan')->placeholder('—'),
                Tables\Columns\TextColumn::make('jumlah_ditugaskan')
                    ->label('Jumlah Ditugaskan')
                    ->state(fn (User $record) => $statById[$record->id]['jumlah']),
                Tables\Columns\TextColumn::make('jumlah_lewat')
                    ->label('Jumlah Lewat')
                    ->state(fn (User $record) => $statById[$record->id]['lewat']),
                Tables\Columns\TextColumn::make('kadar_lewat')
                    ->label('Kadar Lewat')
                    ->state(fn (User $record) => $statById[$record->id]['kadar'] . '%')
                    ->badge()
                    ->color('danger'),
            ])
            ->paginated(false)
            ->emptyStateHeading('Tiada anomali prestasi juruteknik dikesan')
            ->emptyStateIcon('heroicon-o-check-circle');
    }
}
