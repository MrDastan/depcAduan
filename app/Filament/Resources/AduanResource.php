<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AduanResource\Pages;
use App\Models\Aduan;
use App\Models\NotaAduan;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AduanResource extends Resource
{
    protected static ?string $model = Aduan::class;

    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';

    protected static ?string $navigationLabel = 'Aduan Kerosakan';

    protected static ?string $modelLabel = 'Aduan';

    protected static ?string $pluralModelLabel = 'Aduan';

    protected static ?string $navigationGroup = 'Penyelenggaraan';

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::where('status', 'Baru')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Maklumat Pelapor')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('nama_pelapor')
                            ->label('Nama Pelapor')->required()->maxLength(100),
                        Forms\Components\Select::make('bahagian_pelapor')
                            ->label('Bahagian Pelapor')
                            ->options(static::bahagianOptions())
                            ->required(),
                        Forms\Components\TextInput::make('no_telefon_pelapor')
                            ->label('No. Telefon')->tel()->maxLength(20),
                    ]),

                Forms\Components\Section::make('Maklumat Kerosakan')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('nama_peralatan')
                            ->label('Nama Peralatan')->required()->maxLength(200),
                        Forms\Components\TextInput::make('lokasi')
                            ->label('Lokasi')->required()->maxLength(200),
                        Forms\Components\DatePicker::make('tarikh_rosak')
                            ->label('Tarikh Rosak')->required()->maxDate(now()),
                        Forms\Components\Select::make('kategori')
                            ->label('Kategori')
                            ->options([
                                'Elektrikal' => 'Elektrikal',
                                'Mekanikal' => 'Mekanikal',
                                'Paip' => 'Paip',
                                'Penyejukan' => 'Penyejukan',
                                'Struktur' => 'Struktur',
                                'Lain-lain' => 'Lain-lain',
                            ]),
                        Forms\Components\Select::make('keutamaan')
                            ->label('Keutamaan')
                            ->options([
                                'Rendah' => 'Rendah',
                                'Sederhana' => 'Sederhana',
                                'Tinggi' => 'Tinggi',
                                'Kritikal' => 'Kritikal',
                            ])
                            ->default('Sederhana')->required(),
                        Forms\Components\Textarea::make('perihal_kerosakan')
                            ->label('Perihal Kerosakan')->required()->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Pengurusan & Status')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'Baru' => 'Baru',
                                'Dalam Proses' => 'Dalam Proses',
                                'Selesai' => 'Selesai',
                                'Ditutup' => 'Ditutup',
                            ])
                            ->default('Baru')->required(),
                        Forms\Components\Select::make('juruteknik_id')
                            ->label('Juruteknik')
                            ->options(fn () => User::role('Juruteknik')->pluck('name', 'id'))
                            ->searchable()->preload(),
                        Forms\Components\DatePicker::make('tarikh_sasaran_siap')
                            ->label('Tarikh Sasaran Siap'),
                        Forms\Components\DatePicker::make('tarikh_siap')
                            ->label('Tarikh Siap'),
                        Forms\Components\Textarea::make('catatan_penyelia')
                            ->label('Catatan Penyelia')->columnSpanFull(),
                        Forms\Components\Textarea::make('tindakan_juruteknik')
                            ->label('Tindakan Juruteknik')->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('no_tiket')
                    ->label('No. Tiket')->searchable()->sortable()->copyable(),
                Tables\Columns\TextColumn::make('nama_peralatan')
                    ->label('Peralatan')->searchable()->limit(30),
                Tables\Columns\TextColumn::make('lokasi')
                    ->label('Lokasi')->searchable(),
                Tables\Columns\TextColumn::make('bahagian_pelapor')
                    ->label('Bahagian'),
                Tables\Columns\TextColumn::make('keutamaan')
                    ->label('Keutamaan')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Rendah' => 'success',
                        'Sederhana' => 'warning',
                        'Tinggi', 'Kritikal' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Baru' => 'danger',
                        'Dalam Proses' => 'warning',
                        'Selesai' => 'success',
                        'Ditutup' => 'gray',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('juruteknik.name')
                    ->label('Juruteknik')->placeholder('—'),
                Tables\Columns\TextColumn::make('tarikh_rosak')
                    ->label('Tarikh Rosak')->date('d/m/Y')->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dilapor')->dateTime('d/m/Y H:i')->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'Baru' => 'Baru',
                        'Dalam Proses' => 'Dalam Proses',
                        'Selesai' => 'Selesai',
                        'Ditutup' => 'Ditutup',
                    ]),
                Tables\Filters\SelectFilter::make('keutamaan')
                    ->label('Keutamaan')
                    ->options([
                        'Rendah' => 'Rendah',
                        'Sederhana' => 'Sederhana',
                        'Tinggi' => 'Tinggi',
                        'Kritikal' => 'Kritikal',
                    ]),
                Tables\Filters\SelectFilter::make('juruteknik_id')
                    ->label('Juruteknik')
                    ->relationship('juruteknik', 'name'),
                Tables\Filters\SelectFilter::make('bahagian_pelapor')
                    ->label('Bahagian')
                    ->options([
                        'FP' => 'Further Processing (FP)',
                        'CP' => 'Chilling Plant (CP)',
                        'D/S' => 'Dirty Side (D/S)',
                        'Blast' => 'Blast Freezer',
                        'HQ' => 'HQ / Pejabat',
                    ]),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\Action::make('tugaskan')
                    ->label('Tugaskan')
                    ->icon('heroicon-o-user-plus')
                    ->color('primary')
                    ->visible(fn (Aduan $record) => $record->status === 'Baru' && auth()->user()->can('aduan.assign'))
                    ->form([
                        Forms\Components\Select::make('juruteknik_id')
                            ->label('Juruteknik')
                            ->options(User::role('Juruteknik')->pluck('name', 'id'))
                            ->searchable()->required(),
                        Forms\Components\DatePicker::make('tarikh_sasaran_siap')
                            ->label('Sasaran Siap'),
                        Forms\Components\Textarea::make('catatan_penyelia')
                            ->label('Catatan'),
                    ])
                    ->action(function (Aduan $record, array $data) {
                        $record->update([
                            ...$data,
                            'status' => 'Dalam Proses',
                        ]);
                        NotaAduan::create([
                            'aduan_id' => $record->id,
                            'user_id' => auth()->id(),
                            'jenis' => 'tugasan',
                            'kandungan' => 'Ditugaskan kepada ' . User::find($data['juruteknik_id'])->name,
                        ]);
                        Notification::make()->title('Juruteknik berjaya ditugaskan')->success()->send();
                    }),

                Tables\Actions\Action::make('selesai')
                    ->label('Tandakan Selesai')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Aduan $record) => $record->status === 'Dalam Proses' && auth()->user()->can('aduan.verify'))
                    ->requiresConfirmation()
                    ->action(function (Aduan $record) {
                        $record->update(['status' => 'Selesai', 'tarikh_siap' => now(), 'disahkan_oleh' => auth()->id()]);
                        NotaAduan::create([
                            'aduan_id' => $record->id,
                            'user_id' => auth()->id(),
                            'jenis' => 'selesai',
                            'kandungan' => 'Aduan ditanda selesai oleh ' . auth()->user()->name,
                        ]);
                        Notification::make()->title('Aduan ditanda selesai')->success()->send();
                    }),

                Tables\Actions\Action::make('maklum')
                    ->label('Maklum')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('gray')
                    ->requiresConfirmation()
                    ->modalHeading('Hantar Makluman')
                    ->modalDescription(fn (Aduan $record) => 'Hantar makluman kepada ' . $record->nama_pelapor . ' (' . $record->no_telefon_pelapor . ') mengenai status aduan ' . $record->no_tiket . '?')
                    ->action(function (Aduan $record) {
                        NotaAduan::create([
                            'aduan_id' => $record->id,
                            'user_id' => auth()->id(),
                            'jenis' => 'nota',
                            'kandungan' => 'Makluman dihantar kepada pelapor (' . $record->nama_pelapor . ')',
                        ]);
                        Notification::make()->title('Makluman dihantar kepada ' . $record->nama_pelapor)->success()->send();
                    }),

                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAduans::route('/'),
            'create' => Pages\CreateAduan::route('/create'),
            'edit' => Pages\EditAduan::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);

        // Juruteknik hanya nampak aduan yang ditugaskan kepada mereka.
        if (auth()->check() && auth()->user()->hasRole('Juruteknik')) {
            $query->where('juruteknik_id', auth()->id());
        }

        return $query;
    }

    protected static function bahagianOptions(): array
    {
        return [
            'FP' => 'Further Processing (FP)',
            'CP' => 'Chilling Plant (CP)',
            'D/S' => 'Dirty Side (D/S)',
            'Blast' => 'Blast Freezer',
            'HQ' => 'HQ / Pejabat',
        ];
    }
}
