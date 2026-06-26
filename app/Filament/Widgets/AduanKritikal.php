<?php

namespace App\Filament\Widgets;

use App\Models\Aduan;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class AduanKritikal extends BaseWidget
{
    protected static ?string $heading = 'Aduan Keutamaan Tinggi / Kritikal (Belum Selesai)';

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Aduan::query()
                    ->whereIn('keutamaan', ['Tinggi', 'Kritikal'])
                    ->whereNotIn('status', ['Selesai', 'Ditutup'])
                    ->latest()
            )
            ->columns([
                Tables\Columns\TextColumn::make('no_tiket')->label('No. Tiket')->searchable(),
                Tables\Columns\TextColumn::make('nama_peralatan')->label('Peralatan')->limit(30),
                Tables\Columns\TextColumn::make('lokasi')->label('Lokasi'),
                Tables\Columns\TextColumn::make('keutamaan')
                    ->label('Keutamaan')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Tinggi', 'Kritikal' => 'danger',
                        default => 'warning',
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Baru' => 'danger',
                        'Dalam Proses' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('juruteknik.name')->label('Juruteknik')->placeholder('—'),
                Tables\Columns\TextColumn::make('tarikh_rosak')->label('Tarikh Rosak')->date('d/m/Y'),
            ])
            ->paginated([5, 10]);
    }
}
