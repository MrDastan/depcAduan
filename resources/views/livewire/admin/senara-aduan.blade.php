<div>
  {{-- Filters --}}
  <div class="filters">
    <select wire:model.live="filStatus">
      <option value="">Semua status</option>
      <option>Baru</option>
      <option>Dalam Proses</option>
      <option>Selesai</option>
      <option>Ditutup</option>
    </select>
    <select wire:model.live="filKeutamaan">
      <option value="">Semua keutamaan</option>
      <option>Kritikal</option>
      <option>Tinggi</option>
      <option>Sederhana</option>
      <option>Rendah</option>
    </select>
    <select wire:model.live="filBahagian">
      <option value="">Semua bahagian</option>
      <option>FP</option>
      <option>CP</option>
      <option>D/S</option>
      <option>Blast</option>
      <option>HQ</option>
    </select>
  </div>

  {{-- Aduan List --}}
  <div class="aduan-list">
    @forelse ($aduan as $a)
      @php
        $stripCls = match($a->keutamaan) { 'Kritikal'=>'strip-k','Tinggi'=>'strip-t','Sederhana'=>'strip-s',default=>'strip-r' };
        $priEmoji = match($a->keutamaan) { 'Kritikal'=>'🚨','Tinggi'=>'🔴','Sederhana'=>'🟡',default=>'🟢' };
      @endphp
      <div class="acard">
        <div class="acard-strip {{ $stripCls }}"></div>
        <div class="acard-top">
          <h4>{{ $a->nama_peralatan }}</h4>
          @if($a->status === 'Selesai')
            <span class="badge selesai"><i class="ti ti-circle-check"></i> Selesai</span>
          @elseif($a->status === 'Dalam Proses')
            <span class="badge prog"><i class="ti ti-loader"></i> Dalam Proses</span>
          @else
            <span class="badge baru">⚡ Baru</span>
          @endif
        </div>
        <div class="meta">
          <span class="tag"><i class="ti ti-map-pin" style="font-size:12px"></i>{{ $a->lokasi }}</span>
          <span class="tag"><i class="ti ti-building" style="font-size:12px"></i>{{ $a->bahagian_pelapor }}</span>
          <span class="tag">{{ $priEmoji }} {{ $a->keutamaan }}</span>
          <span class="tag"><i class="ti ti-clock" style="font-size:12px"></i>{{ $a->created_at->format('d/m/Y H:i') }}</span>
          @if($a->juruteknik) <span class="tag"><i class="ti ti-user" style="font-size:12px"></i>{{ $a->juruteknik->name }}</span> @endif
        </div>
        <p class="acard-desc">{{ $a->perihal_kerosakan }}</p>
        <div class="acard-footer">
          <div class="acard-footer-info">
            <i class="ti ti-user" style="font-size:12px"></i> {{ $a->nama_pelapor }}{{ $a->no_telefon_pelapor ? ' · '.$a->no_telefon_pelapor : '' }}
            &nbsp;·&nbsp;<span style="font-family:monospace">{{ $a->no_tiket }}</span>
          </div>
          <div class="acard-actions">
            @if($a->status === 'Baru' && auth()->user()->can('aduan.assign'))
              <button class="btn pri" wire:click="openAssign({{ $a->id }})"><i class="ti ti-user-check"></i> Tugaskan</button>
            @endif
            @if($a->status === 'Dalam Proses' && auth()->user()->can('aduan.verify'))
              <button class="btn warn" wire:click="tandaSelesai({{ $a->id }})" wire:confirm="Tandakan aduan {{ $a->no_tiket }} sebagai selesai?"><i class="ti ti-check"></i> Selesai</button>
            @endif
            <button class="btn" wire:click="maklum({{ $a->id }})"><i class="ti ti-send"></i> Maklum</button>
          </div>
        </div>
      </div>
    @empty
      <div class="empty"><i class="ti ti-mood-happy"></i><p>Tiada aduan mengikut tapisan ini.</p></div>
    @endforelse
  </div>

  <div style="margin-top:16px">{{ $aduan->links() }}</div>

  {{-- Modal Tugaskan --}}
  <div class="modal-bg" id="assign">
    <div class="modal" onclick.stop>
      <h3><i class="ti ti-user-check" style="color:#1D9E75"></i> Tugaskan Juruteknik</h3>
      @if($assignId)
        @php $sel = \App\Models\Aduan::find($assignId); @endphp
        <div class="fg">
          <label>Tiket</label>
          <input readonly value="{{ $sel?->no_tiket }} — {{ $sel?->nama_peralatan }}" style="background:#F5F6F8">
        </div>
      @endif
      <div class="fg">
        <label>Juruteknik / Kontraktor</label>
        <select wire:model="assignTeknik">
          <option value="0">— Pilih juruteknik —</option>
          @foreach ($juruteknik as $id => $nama)
          <option value="{{ $id }}">{{ $nama }}</option>
          @endforeach
        </select>
        @error('assignTeknik') <span style="color:#E24B4A;font-size:11px">{{ $message }}</span> @enderror
      </div>
      <div class="fg">
        <label>Sasaran siap</label>
        <input type="date" wire:model="assignTarikh">
      </div>
      <div class="fg">
        <label>Catatan (pilihan)</label>
        <input type="text" wire:model="assignCatatan" placeholder="Arahan khas...">
      </div>
      <div class="modal-actions">
        <button class="btn" onclick="document.getElementById('assign').classList.remove('show')">Batal</button>
        <button class="btn pri" wire:click="confirmAssign"><i class="ti ti-check"></i> Tugaskan</button>
      </div>
    </div>
  </div>
</div>
