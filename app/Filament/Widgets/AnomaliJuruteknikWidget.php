<?php

namespace App\Filament\Widgets;

use App\Models\Aduan;
use App\Models\User;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Collection;

class AnomaliJuruteknikWidget extends BaseWidget
{
    protected static ?string $heading = 'Anomali Prestasi Juruteknik';

    protected static ?int $sort = 5;

    protected int|string|array $columnSpan = 'full';

    /** Bilangan minimum aduan ditugaskan sebelum juruteknik disertakan dalam perbandingan. */
    protected const MIN_SAMPEL = 3;

    /** Berapa mata peratusan kadar lewat melebihi purata sebelum dianggap anomali. */
    protected const AMBANG_LEBIHAN = 20;

    public static function canView(): bool
    {
        return auth()->check() && auth()->user()->can('laporan.view');
    }

    public function table(Table $table): Table
    {
        $statistik = $this->statistikJuruteknik();
        $flaggedIds = $this->kenalPastiJuruteknikAnomali($statistik);

        return $table
            ->query(User::query()->whereIn('id', $flaggedIds))
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Juruteknik'),
                Tables\Columns\TextColumn::make('jawatan')->label('Jawatan')->placeholder('—'),
                Tables\Columns\TextColumn::make('jumlah_ditugaskan')
                    ->label('Jumlah Ditugaskan')
                    ->state(fn (User $record) => $statistik[$record->id]['jumlah']),
                Tables\Columns\TextColumn::make('jumlah_lewat')
                    ->label('Jumlah Lewat')
                    ->state(fn (User $record) => $statistik[$record->id]['lewat']),
                Tables\Columns\TextColumn::make('kadar_lewat')
                    ->label('Kadar Lewat')
                    ->state(fn (User $record) => $statistik[$record->id]['kadar'] . '%')
                    ->badge()
                    ->color('danger'),
            ])
            ->paginated(false)
            ->emptyStateHeading('Tiada anomali prestasi juruteknik dikesan')
            ->emptyStateIcon('heroicon-o-check-circle');
    }

    protected function statistikJuruteknik(): Collection
    {
        return User::role('Juruteknik')->get()->mapWithKeys(function (User $juruteknik) {
            $aduan = $juruteknik->aduanDitugaskan()->withTrashed()->get();
            $jumlah = $aduan->count();
            $lewat = $aduan->filter(fn (Aduan $a) => $a->isLewat())->count();

            return [$juruteknik->id => [
                'jumlah' => $jumlah,
                'lewat' => $lewat,
                'kadar' => $jumlah > 0 ? round($lewat / $jumlah * 100, 1) : 0.0,
            ]];
        });
    }

    protected function kenalPastiJuruteknikAnomali(Collection $statistik): array
    {
        $layakDibanding = $statistik->filter(fn ($s) => $s['jumlah'] >= self::MIN_SAMPEL);

        if ($layakDibanding->isEmpty()) {
            return [];
        }

        $purataKadar = $layakDibanding->avg('kadar');
        $ambang = $purataKadar + self::AMBANG_LEBIHAN;

        return $layakDibanding
            ->filter(fn ($s) => $s['kadar'] > $ambang)
            ->keys()
            ->all();
    }
}
