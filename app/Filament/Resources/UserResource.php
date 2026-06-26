<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Pengguna';

    protected static ?string $modelLabel = 'Pengguna';

    protected static ?string $pluralModelLabel = 'Pengguna';

    protected static ?string $navigationGroup = 'Pentadbiran';

    public static function canAccess(): bool
    {
        return auth()->user()?->can('user.manage') ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Maklumat Pengguna')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama')->required()->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->label('E-mel')->email()->required()->unique(ignoreRecord: true)->maxLength(255),
                        Forms\Components\Select::make('bahagian')
                            ->label('Bahagian')
                            ->options([
                                'FP' => 'Further Processing (FP)',
                                'CP' => 'Chilling Plant (CP)',
                                'D/S' => 'Dirty Side (D/S)',
                                'Blast' => 'Blast Freezer',
                                'HQ' => 'HQ / Pejabat',
                            ]),
                        Forms\Components\TextInput::make('jawatan')
                            ->label('Jawatan')->maxLength(255),
                        Forms\Components\TextInput::make('no_telefon')
                            ->label('No. Telefon')->tel()->maxLength(255),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktif')->default(true),
                    ]),

                Forms\Components\Section::make('Peranan & Kata Laluan')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('roles')
                            ->label('Peranan')
                            ->relationship('roles', 'name')
                            ->multiple()->preload(),
                        Forms\Components\TextInput::make('password')
                            ->label('Kata Laluan')
                            ->password()
                            ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                            ->dehydrated(fn (?string $state): bool => filled($state))
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->helperText('Biar kosong jika tidak mahu menukar kata laluan.')
                            ->maxLength(255),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('E-mel')->searchable(),
                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Peranan')->badge(),
                Tables\Columns\TextColumn::make('bahagian')
                    ->label('Bahagian'),
                Tables\Columns\TextColumn::make('jawatan')
                    ->label('Jawatan'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('roles')
                    ->label('Peranan')
                    ->relationship('roles', 'name'),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status Aktif'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
