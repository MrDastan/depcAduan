<?php

namespace App\Livewire\Admin;

use App\Models\Aduan;
use Livewire\Component;

class LiveMonitor extends Component
{
    public int $lastCount = 0;
    public bool $adaBaru  = false;

    public function mount(): void
    {
        $this->lastCount = Aduan::where('status', 'Baru')->count();
    }

    public function checkBaru(): void
    {
        $now = Aduan::where('status', 'Baru')->count();
        $this->adaBaru  = $now > $this->lastCount;
        $this->lastCount = $now;
    }

    public function render()
    {
        $stats = [
            'baru'        => Aduan::where('status', 'Baru')->count(),
            'dalam_proses' => Aduan::where('status', 'Dalam Proses')->count(),
            'selesai'     => Aduan::where('status', 'Selesai')->whereDate('tarikh_siap', today())->count(),
            'kritikal'    => Aduan::whereIn('keutamaan', ['Kritikal', 'Tinggi'])->where('status', 'Baru')->count(),
        ];

        $terkini = Aduan::with('juruteknik')->latest()->take(8)->get();

        return view('livewire.admin.live-monitor', compact('stats', 'terkini'));
    }
}
