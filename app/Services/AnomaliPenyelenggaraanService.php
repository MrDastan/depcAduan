<?php

namespace App\Services;

use App\Models\Aduan;
use App\Models\User;
use Illuminate\Support\Collection;

class AnomaliPenyelenggaraanService
{
    protected const MIN_SAMPEL_JURUTEKNIK = 3;

    protected const AMBANG_LEBIHAN_KADAR = 20;

    /** Senarai aduan dengan anomali masa/kos: [['aduan' => Aduan, 'sebab' => string[]]]. */
    public function aduanAnomali(): Collection
    {
        $purataTempoh = $this->purataTempohSiapMengikutKategori();
        $purataKos = $this->purataKosMengikutKategori();

        return Aduan::query()
            ->where(function ($q) {
                $q->lewat()->orWhereNotNull('kos_sebenar');
            })
            ->latest()
            ->get()
            ->map(fn (Aduan $a) => [
                'aduan' => $a,
                'sebab' => $this->sebabAnomaliAduan($a, $purataTempoh, $purataKos),
            ])
            ->filter(fn (array $row) => count($row['sebab']) > 0)
            ->values();
    }

    /** Senarai juruteknik dengan kadar lewat jauh melebihi purata rakan sekerja. */
    public function juruteknikAnomali(): Collection
    {
        $statistik = $this->statistikJuruteknik();
        $layakDibanding = $statistik->filter(fn (array $s) => $s['jumlah'] >= self::MIN_SAMPEL_JURUTEKNIK);

        if ($layakDibanding->isEmpty()) {
            return collect();
        }

        $ambang = $layakDibanding->avg('kadar') + self::AMBANG_LEBIHAN_KADAR;

        return $layakDibanding->filter(fn (array $s) => $s['kadar'] > $ambang)->values();
    }

    public function purataTempohSiapMengikutKategori(): array
    {
        return Aduan::query()
            ->whereNotNull('tarikh_siap')
            ->get(['kategori', 'tarikh_rosak', 'tarikh_siap'])
            ->groupBy(fn (Aduan $a) => $a->kategori ?? '-')
            ->map(fn (Collection $grp) => $grp->avg(fn (Aduan $a) => $a->tempohSiapHari()))
            ->toArray();
    }

    public function purataKosMengikutKategori(): array
    {
        return Aduan::query()
            ->whereNotNull('kos_sebenar')
            ->get(['kategori', 'kos_sebenar'])
            ->groupBy(fn (Aduan $a) => $a->kategori ?? '-')
            ->map(fn (Collection $grp) => $grp->avg(fn (Aduan $a) => (float) $a->kos_sebenar))
            ->toArray();
    }

    public function sebabAnomaliAduan(Aduan $aduan, array $purataTempoh, array $purataKos): array
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

    protected function statistikJuruteknik(): Collection
    {
        return User::role('Juruteknik')->get()->map(function (User $juruteknik) {
            $aduan = $juruteknik->aduanDitugaskan()->withTrashed()->get();
            $jumlah = $aduan->count();
            $lewat = $aduan->filter(fn (Aduan $a) => $a->isLewat())->count();

            return [
                'juruteknik' => $juruteknik,
                'jumlah' => $jumlah,
                'lewat' => $lewat,
                'kadar' => $jumlah > 0 ? round($lewat / $jumlah * 100, 1) : 0.0,
            ];
        });
    }
}
