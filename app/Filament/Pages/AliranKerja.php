<?php

namespace App\Filament\Pages;

use App\Models\Aduan;
use App\Models\NotaAduan;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class AliranKerja extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-arrows-right-left';

    protected static ?string $navigationLabel = 'Aliran Kerja';

    protected static ?string $title = 'Aliran Kerja';

    protected static ?string $navigationGroup = 'Penyelenggaraan';

    protected static ?int $navigationSort = 2;

    protected static string $view = 'filament.pages.aliran-kerja';

    public function getViewData(): array
    {
        return [
            'menunggu' => Aduan::where('status', 'Baru')->latest()->get(),
            'dalam_proses' => Aduan::where('status', 'Dalam Proses')->latest()->get(),
            'selesai' => Aduan::where('status', 'Selesai')->latest()->take(10)->get(),
            'juruteknik_list' => User::role('Juruteknik')->pluck('name', 'id'),
        ];
    }

    public function tugaskan(int $aduanId, int $juruteknikId, ?string $catatan = null, ?string $sasaran = null): void
    {
        $aduan = Aduan::findOrFail($aduanId);
        $aduan->update([
            'juruteknik_id' => $juruteknikId,
            'status' => 'Dalam Proses',
            'catatan_penyelia' => $catatan,
            'tarikh_sasaran_siap' => $sasaran,
        ]);
        NotaAduan::create([
            'aduan_id' => $aduanId,
            'user_id' => auth()->id(),
            'jenis' => 'tugasan',
            'kandungan' => 'Ditugaskan kepada ' . User::find($juruteknikId)->name,
        ]);
        Notification::make()->title('Juruteknik berjaya ditugaskan')->success()->send();
    }

    public function tandaSelesai(int $aduanId): void
    {
        $aduan = Aduan::findOrFail($aduanId);
        $aduan->update(['status' => 'Selesai', 'tarikh_siap' => now(), 'disahkan_oleh' => auth()->id()]);
        NotaAduan::create([
            'aduan_id' => $aduanId,
            'user_id' => auth()->id(),
            'jenis' => 'selesai',
            'kandungan' => 'Aduan ditanda selesai oleh ' . auth()->user()->name,
        ]);
        Notification::make()->title('Aduan ditanda selesai')->success()->send();
    }
}
