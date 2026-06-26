<?php

namespace App\Livewire\Admin;

use App\Models\Aduan;
use App\Models\NotaAduan;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class SenaraAduan extends Component
{
    use WithPagination;

    public string $filStatus   = '';
    public string $filKeutamaan = '';
    public string $filBahagian  = '';

    public ?int $assignId    = null;
    public int $assignTeknik = 0;
    public string $assignTarikh  = '';
    public string $assignCatatan = '';

    protected $queryString = ['filStatus', 'filKeutamaan', 'filBahagian'];

    public function updatingFilStatus()    { $this->resetPage(); }
    public function updatingFilKeutamaan() { $this->resetPage(); }
    public function updatingFilBahagian()  { $this->resetPage(); }

    public function openAssign(int $id): void
    {
        $this->assignId     = $id;
        $this->assignTeknik = 0;
        $this->assignTarikh = now()->addDays(3)->format('Y-m-d');
        $this->assignCatatan = '';
        $this->dispatch('open-modal', 'assign');
    }

    public function confirmAssign(): void
    {
        $this->validate([
            'assignTeknik' => 'required|exists:users,id',
            'assignTarikh' => 'nullable|date',
        ], ['assignTeknik.required' => 'Sila pilih juruteknik.']);

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

        $this->dispatch('close-modal', 'assign');
        $this->dispatch('toast', '✅ ' . User::find($this->assignTeknik)->name . ' ditugaskan untuk ' . $aduan->nama_peralatan);
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
        $this->dispatch('toast', '✅ Aduan ' . $aduan->no_tiket . ' ditanda selesai');
    }

    public function maklum(int $id): void
    {
        $aduan = Aduan::findOrFail($id);
        NotaAduan::create([
            'aduan_id'  => $id,
            'user_id'   => auth()->id(),
            'jenis'     => 'nota',
            'kandungan' => 'Makluman dihantar kepada pelapor (' . $aduan->nama_pelapor . ')',
        ]);
        $this->dispatch('toast', '📲 Makluman dihantar kepada ' . $aduan->nama_pelapor);
    }

    public function render()
    {
        $query = Aduan::with('juruteknik')->latest();

        if (auth()->user()->hasRole('Juruteknik')) {
            $query->where('juruteknik_id', auth()->id());
        }

        if ($this->filStatus)    $query->where('status', $this->filStatus);
        if ($this->filKeutamaan) $query->where('keutamaan', $this->filKeutamaan);
        if ($this->filBahagian)  $query->where('bahagian_pelapor', $this->filBahagian);

        $aduan = $query->paginate(15);
        $juruteknik = User::role('Juruteknik')->pluck('name', 'id');

        return view('livewire.admin.senara-aduan', compact('aduan', 'juruteknik'));
    }
}
