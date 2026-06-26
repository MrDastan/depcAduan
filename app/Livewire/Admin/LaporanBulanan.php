<?php

namespace App\Livewire\Admin;

use App\Models\Aduan;
use Illuminate\Support\Carbon;
use Livewire\Component;

class LaporanBulanan extends Component
{
    public int $bulan;
    public int $tahun;

    public function mount(): void
    {
        $this->bulan = now()->month;
        $this->tahun = now()->year;
    }

    public function render()
    {
        $b = $this->bulan;
        $t = $this->tahun;

        $aduan   = Aduan::whereYear('created_at', $t)->whereMonth('created_at', $b)->get();
        $selesai = $aduan->where('status', 'Selesai');

        $jumlah    = $aduan->count();
        $jmlSelesai = $selesai->count();
        $kadar     = $jumlah > 0 ? round($jmlSelesai / $jumlah * 100) : 0;
        $purataDays = $selesai->filter(fn ($a) => $a->tarikh_siap && $a->tarikh_rosak)
            ->avg(fn ($a) => $a->tarikh_rosak->diffInDays($a->tarikh_siap));

        $lepas = Carbon::create($t, $b)->subMonth();
        $jumlahLepas = Aduan::whereYear('created_at', $lepas->year)->whereMonth('created_at', $lepas->month)->count();

        $kategori = ['Elektrikal', 'Mekanikal', 'Paip', 'Penyejukan', 'Struktur', 'Lain-lain'];
        $byKategori = collect($kategori)->map(fn ($k) => [
            'nama'    => $k,
            'jumlah'  => $aduan->where('kategori', $k)->count(),
            'selesai' => $aduan->where('kategori', $k)->where('status', 'Selesai')->count(),
            'kadar'   => $aduan->where('kategori', $k)->count() > 0
                ? round($aduan->where('kategori', $k)->where('status', 'Selesai')->count() / $aduan->where('kategori', $k)->count() * 100) : 0,
        ])->filter(fn ($k) => $k['jumlah'] > 0)->values();

        $byKeutamaan = collect(['Kritikal', 'Tinggi', 'Sederhana', 'Rendah'])->map(fn ($p) => [
            'nama'    => $p,
            'jumlah'  => $aduan->where('keutamaan', $p)->count(),
            'selesai' => $aduan->where('keutamaan', $p)->where('status', 'Selesai')->count(),
        ]);

        $byBahagian = collect(['FP', 'CP', 'D/S', 'Blast', 'HQ'])
            ->map(fn ($b) => ['nama' => $b, 'jumlah' => $aduan->where('bahagian_pelapor', $b)->count()])
            ->filter(fn ($b) => $b['jumlah'] > 0)->values();

        $trend = [];
        for ($i = 5; $i >= 0; $i--) {
            $tgt = Carbon::create($t, $b)->subMonths($i);
            $trend[] = [
                'label'   => $tgt->translatedFormat('M Y'),
                'jumlah'  => Aduan::whereYear('created_at', $tgt->year)->whereMonth('created_at', $tgt->month)->count(),
                'selesai' => Aduan::whereYear('tarikh_siap', $tgt->year)->whereMonth('tarikh_siap', $tgt->month)->count(),
            ];
        }

        return view('livewire.admin.laporan-bulanan', compact(
            'jumlah', 'jmlSelesai', 'kadar', 'purataDays', 'jumlahLepas',
            'byKategori', 'byKeutamaan', 'byBahagian', 'trend'
        ));
    }
}
