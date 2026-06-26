<div>
  {{-- Stats --}}
  <div class="stat-grid" style="grid-template-columns:repeat(3,1fr)">
    <div class="sc red"><div class="n">{{ $menunggu->count() }}</div><div class="l">Menunggu tugasan</div></div>
    <div class="sc amber"><div class="n">{{ $dalam_proses->count() }}</div><div class="l">Sedang dibaiki</div></div>
    <div class="sc green"><div class="n">{{ $selesai->count() }}</div><div class="l">Selesai</div></div>
  </div>

  {{-- Menunggu Tugasan --}}
  <p style="font-size:13px;font-weight:600;margin-bottom:10px">Perlu tugaskan juruteknik</p>
  <div class="aduan-list" style="margin-bottom:24px">
    @forelse ($menunggu as $a)
      @php $stripCls = match($a->keutamaan){'Kritikal'=>'strip-k','Tinggi'=>'strip-t','Sederhana'=>'strip-s',default=>'strip-r'}; @endphp
      <div class="acard">
        <div class="acard-strip {{ $stripCls }}"></div>
        <div class="acard-top">
          <h4>{{ $a->nama_peralatan }}</h4>
          <span class="badge baru">⚡ Baru</span>
        </div>
        <div class="meta">
          <span class="tag"><i class="ti ti-map-pin" style="font-size:12px"></i>{{ $a->lokasi }}</span>
          <span class="tag"><i class="ti ti-building" style="font-size:12px"></i>{{ $a->bahagian_pelapor }}</span>
          <span class="tag">{{ match($a->keutamaan){'Kritikal'=>'🚨','Tinggi'=>'🔴','Sederhana'=>'🟡',default=>'🟢'} }} {{ $a->keutamaan }}</span>
          <span class="tag"><i class="ti ti-calendar" style="font-size:12px"></i>{{ $a->tarikh_rosak?->format('d/m/Y') }}</span>
        </div>
        <p class="acard-desc">{{ $a->perihal_kerosakan }}</p>
        <div class="acard-footer">
          <div class="acard-footer-info"><i class="ti ti-user" style="font-size:12px"></i> {{ $a->nama_pelapor }} &nbsp;·&nbsp; <span style="font-family:monospace">{{ $a->no_tiket }}</span></div>
          <div class="acard-actions">
            @can('aduan.assign')
            <button class="btn pri" wire:click="openAssign({{ $a->id }})"><i class="ti ti-user-check"></i> Tugaskan</button>
            @endcan
            <button class="btn" wire:click="$dispatch('toast','📲 Makluman dihantar')"><i class="ti ti-send"></i> Maklum</button>
          </div>
        </div>
      </div>
    @empty
      <div class="empty"><i class="ti ti-circle-check"></i><p>Tiada aduan menunggu tugasan.</p></div>
    @endforelse
  </div>

  {{-- Dalam Proses --}}
  <p style="font-size:13px;font-weight:600;margin-bottom:10px">Dalam proses — menunggu pengesahan</p>
  <div class="aduan-list" style="margin-bottom:24px">
    @forelse ($dalam_proses as $a)
      @php $stripCls = match($a->keutamaan){'Kritikal'=>'strip-k','Tinggi'=>'strip-t','Sederhana'=>'strip-s',default=>'strip-r'}; @endphp
      <div class="acard">
        <div class="acard-strip {{ $stripCls }}"></div>
        <div class="acard-top">
          <h4>{{ $a->nama_peralatan }}</h4>
          <span class="badge prog"><i class="ti ti-loader"></i> Dalam Proses</span>
        </div>
        <div class="meta">
          <span class="tag"><i class="ti ti-map-pin" style="font-size:12px"></i>{{ $a->lokasi }}</span>
          <span class="tag"><i class="ti ti-building" style="font-size:12px"></i>{{ $a->bahagian_pelapor }}</span>
          @if($a->juruteknik) <span class="tag"><i class="ti ti-user" style="font-size:12px"></i>{{ $a->juruteknik->name }}</span> @endif
          @if($a->tarikh_sasaran_siap) <span class="tag">🎯 Sasaran: {{ $a->tarikh_sasaran_siap->format('d/m/Y') }}</span> @endif
        </div>
        <p class="acard-desc">{{ $a->perihal_kerosakan }}</p>
        <div class="acard-footer">
          <div class="acard-footer-info"><i class="ti ti-user" style="font-size:12px"></i> {{ $a->nama_pelapor }} &nbsp;·&nbsp; <span style="font-family:monospace">{{ $a->no_tiket }}</span></div>
          <div class="acard-actions">
            @can('aduan.verify')
            <button class="btn warn" wire:click="tandaSelesai({{ $a->id }})" wire:confirm="Tandakan aduan {{ $a->no_tiket }} sebagai selesai?"><i class="ti ti-check"></i> Selesai</button>
            @endcan
          </div>
        </div>
      </div>
    @empty
      <div class="empty"><i class="ti ti-mood-happy"></i><p>Tiada aduan dalam proses.</p></div>
    @endforelse
  </div>

  {{-- Modal Tugaskan --}}
  <div class="modal-bg" id="assign-aliran">
    <div class="modal">
      <h3><i class="ti ti-user-check" style="color:#1D9E75"></i> Tugaskan Juruteknik</h3>
      @if($assignId)
        @php $sel = \App\Models\Aduan::find($assignId); @endphp
        <div class="fg"><label>Tiket</label><input readonly value="{{ $sel?->no_tiket }} — {{ $sel?->nama_peralatan }}" style="background:#F5F6F8"></div>
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
      <div class="fg"><label>Sasaran siap</label><input type="date" wire:model="assignTarikh"></div>
      <div class="fg"><label>Catatan (pilihan)</label><input type="text" wire:model="assignCatatan" placeholder="Arahan khas..."></div>
      <div class="modal-actions">
        <button class="btn" onclick="document.getElementById('assign-aliran').classList.remove('show')">Batal</button>
        <button class="btn pri" wire:click="confirmAssign"><i class="ti ti-check"></i> Tugaskan</button>
      </div>
    </div>
  </div>
</div>
