<?php

namespace App\Livewire\Admin;

use App\Models\Aduan;
use App\Models\NotaAduan;
use App\Models\User;
use Livewire\Component;

class AliranKerja extends Component
{
    public ?int $assignId      = null;
    public int $assignTeknik   = 0;
    public string $assignTarikh  = '';
    public string $assignCatatan = '';

    public function openAssign(int $id): void
    {
        $this->assignId      = $id;
        $this->assignTeknik  = 0;
        $this->assignTarikh  = now()->addDays(3)->format('Y-m-d');
        $this->assignCatatan = '';
        $this->dispatch('open-modal', 'assign-aliran');
    }

    public function confirmAssign(): void
    {
        $this->validate(['assignTeknik' => 'required|exists:users,id'], ['assignTeknik.required' => 'Sila pilih juruteknik.']);

        $aduan = Aduan::findOrFail($this->assignId);
        $aduan->update([
            'juruteknik_id'      => $this->assignTeknik,
            'status'             => 'Dalam Proses',
            'tarikh_sasaran_siap' => $this->assignTarikh ?: null,
            'catatan_penyelia'   => $this->assignCatatan ?: null,
        ]);
        NotaAduan::create([
            'aduan_id'  => $aduan->id,
            'user_id'   => auth()->id(),
            'jenis'     => 'tugasan',
            'kandungan' => 'Ditugaskan kepada ' . User::find($this->assignTeknik)->name,
        ]);

        $this->dispatch('close-modal', 'assign-aliran');
        $this->dispatch('toast', '✅ ' . User::find($this->assignTeknik)->name . ' ditugaskan');
        $this->assignId = null;
    }

    public function tandaSelesai(int $id): void
    {
        $aduan = Aduan::findOrFail($id);
        $aduan->update(['status' => 'Selesai', 'tarikh_siap' => now(), 'disahkan_oleh' => auth()->id()]);
        NotaAduan::create([
            'aduan_id'  => $id,
            'user_id'   => auth()->id(),
            'jenis'     => 'selesai',
            'kandungan' => 'Aduan ditanda selesai oleh ' . auth()->user()->name,
        ]);
        $this->dispatch('toast', '✅ Aduan ditanda selesai');
    }

    public function render()
    {
        return view('livewire.admin.aliran-kerja', [
            'menunggu'     => Aduan::where('status', 'Baru')->latest()->get(),
            'dalam_proses' => Aduan::where('status', 'Dalam Proses')->latest()->get(),
            'selesai'      => Aduan::where('status', 'Selesai')->latest()->take(10)->get(),
            'juruteknik'   => User::role('Juruteknik')->pluck('name', 'id'),
        ]);
    }
}
