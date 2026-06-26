<?php

namespace App\Filament\Pages;

use App\Models\Aduan;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;

class LaporanBulanan extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationLabel = 'Laporan Bulanan';

    protected static ?string $title = 'Laporan Bulanan';

    protected static ?string $navigationGroup = 'Laporan';

    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.pages.laporan-bulanan';

    public int $bulan;
    public int $tahun;

    public function mount(): void
    {
        $this->bulan = now()->month;
        $this->tahun = now()->year;
    }

    public function getViewData(): array
    {
        $bulan = $this->bulan;
        $tahun = $this->tahun;

        $aduan = Aduan::whereYear('created_at', $tahun)->whereMonth('created_at', $bulan)->get();
        $selesai = $aduan->where('status', 'Selesai');

        // Stats utama
        $jumlah = $aduan->count();
        $jmlSelesai = $selesai->count();
        $kadar = $jumlah > 0 ? round($jmlSelesai / $jumlah * 100) : 0;
        $purataDays = $selesai->filter(fn ($a) => $a->tarikh_siap && $a->tarikh_rosak)
            ->avg(fn ($a) => $a->tarikh_rosak->diffInDays($a->tarikh_siap));

        // Bulan lepas untuk perbandingan
        $lepas = Carbon::create($tahun, $bulan)->subMonth();
        $jumlahLepas = Aduan::whereYear('created_at', $lepas->year)->whereMonth('created_at', $lepas->month)->count();

        // Breakdown kategori
        $kategori = ['Elektrikal', 'Mekanikal', 'Paip', 'Penyejukan', 'Struktur', 'Lain-lain'];
        $byKategori = [];
        foreach ($kategori as $k) {
            $list = $aduan->where('kategori', $k);
            $byKategori[] = [
                'nama' => $k,
                'jumlah' => $list->count(),
                'selesai' => $list->where('status', 'Selesai')->count(),
                'kadar' => $list->count() > 0 ? round($list->where('status', 'Selesai')->count() / $list->count() * 100) : 0,
            ];
        }

        // Breakdown keutamaan
        $byKeutamaan = [];
        foreach (['Kritikal', 'Tinggi', 'Sederhana', 'Rendah'] as $p) {
            $list = $aduan->where('keutamaan', $p);
            $byKeutamaan[] = [
                'nama' => $p,
                'jumlah' => $list->count(),
                'selesai' => $list->where('status', 'Selesai')->count(),
            ];
        }

        // Breakdown bahagian
        $byBahagian = [];
        foreach (['FP', 'CP', 'D/S', 'Blast', 'HQ'] as $b) {
            $list = $aduan->where('bahagian_pelapor', $b);
            if ($list->count() > 0) {
                $byBahagian[] = ['nama' => $b, 'jumlah' => $list->count()];
            }
        }

        // 6 bulan trend
        $trend = [];
        for ($i = 5; $i >= 0; $i--) {
            $tgt = Carbon::create($tahun, $bulan)->subMonths($i);
            $trend[] = [
                'label' => $tgt->translatedFormat('M Y'),
                'jumlah' => Aduan::whereYear('created_at', $tgt->year)->whereMonth('created_at', $tgt->month)->count(),
                'selesai' => Aduan::whereYear('tarikh_siap', $tgt->year)->whereMonth('tarikh_siap', $tgt->month)->count(),
            ];
        }

        return compact('jumlah', 'jmlSelesai', 'kadar', 'purataDays', 'jumlahLepas', 'byKategori', 'byKeutamaan', 'byBahagian', 'trend', 'bulan', 'tahun');
    }

    public function updatedBulan(): void {}
    public function updatedTahun(): void {}

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export')
                ->label('Eksport PDF')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->action(fn () => $this->exportPdf()),
        ];
    }

    public function exportPdf(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $data = $this->getViewData();
        $namaBulan = Carbon::create($this->tahun, $this->bulan)->translatedFormat('F Y');

        $html = view('filament.pages.laporan-pdf', array_merge($data, ['namaBulan' => $namaBulan]))->render();

        return response()->streamDownload(function () use ($html) {
            echo $html;
        }, 'Laporan-' . $this->tahun . '-' . str_pad($this->bulan, 2, '0', STR_PAD_LEFT) . '.html');
    }
}
