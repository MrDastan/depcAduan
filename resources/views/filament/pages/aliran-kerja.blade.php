<x-filament-panels::page>
<style>
  .acard { background:#fff; border:1px solid #E2E5EA; border-radius:12px; padding:16px 16px 16px 20px; position:relative; overflow:hidden; transition:box-shadow .2s; margin-bottom:10px; }
  .acard:hover { box-shadow:0 4px 16px rgba(0,0,0,.08); }
  .acard-strip { position:absolute; left:0; top:0; bottom:0; width:4px; border-radius:12px 0 0 12px; }
  .strip-k { background:#E24B4A; }
  .strip-t { background:#EF9F27; }
  .strip-s { background:#639922; }
  .strip-r { background:#185FA5; }
  .strip-b { background:#185FA5; }
  .acard-top { display:flex; align-items:flex-start; justify-content:space-between; gap:8px; margin-bottom:8px; }
  .acard-top h4 { font-size:14px; font-weight:600; flex:1; margin:0; }
  .abadge { display:inline-flex; align-items:center; gap:4px; padding:4px 10px; border-radius:99px; font-size:11px; font-weight:600; white-space:nowrap; }
  .ab-baru   { background:#FAEEDA; color:#633806; }
  .ab-prog   { background:#E6F1FB; color:#0C447C; }
  .ab-selesai{ background:#E1F5EE; color:#085041; }
  .ameta { display:flex; gap:6px; flex-wrap:wrap; margin-bottom:10px; }
  .atag { display:inline-flex; align-items:center; gap:4px; padding:3px 9px; border-radius:99px; font-size:11px; background:#F5F6F8; color:#6B7280; }
  .adesc { font-size:13px; color:#6B7280; margin-bottom:10px; line-height:1.5; }
  .afooter { display:flex; align-items:center; justify-content:space-between; gap:8px; flex-wrap:wrap; }
  .afoot-info { font-size:11px; color:#9CA3AF; }
  .aactions { display:flex; gap:6px; flex-wrap:wrap; }
  .abtn { padding:7px 14px; border-radius:8px; border:1px solid #E2E5EA; background:#fff; font-size:12px; cursor:pointer; color:#1A1D23; display:inline-flex; align-items:center; gap:5px; font-weight:500; transition:all .15s; font-family:inherit; }
  .abtn:hover { background:#F5F6F8; }
  .abtn.pri { background:#1D9E75; border-color:#1D9E75; color:#fff; }
  .abtn.pri:hover { background:#0F6E56; }
  .abtn.warn { background:#FAEEDA; border-color:#EF9F27; color:#633806; }
  .sec-title { font-size:11px; font-weight:600; color:#9CA3AF; text-transform:uppercase; letter-spacing:.5px; margin:0 0 12px; display:flex; align-items:center; gap:8px; }
  .sec-dot { width:8px; height:8px; border-radius:50%; display:inline-block; flex-shrink:0; }
  .sc-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:12px; margin-bottom:24px; }
  .sc { background:#fff; border:1px solid #E2E5EA; border-radius:12px; padding:14px 16px; }
  .sc .sn { font-size:24px; font-weight:600; margin-bottom:2px; }
  .sc .sl { font-size:11px; color:#6B7280; }
  .sc.red .sn  { color:#E24B4A; }
  .sc.amb .sn  { color:#EF9F27; }
  .sc.grn .sn  { color:#1D9E75; }
  .empty-state { text-align:center; padding:48px 20px; color:#6B7280; }
  .empty-state svg { width:40px; height:40px; margin:0 auto 10px; opacity:.35; display:block; }
  .empty-state p { font-size:13px; }
  /* Assign form inline */
  .assign-box { background:#F5F6F8; border:1px solid #E2E5EA; border-radius:10px; padding:14px; margin-top:10px; }
  .fg { display:flex; flex-direction:column; gap:4px; margin-bottom:12px; }
  .fg label { font-size:11px; font-weight:500; color:#6B7280; }
  .fg select, .fg input { border:1px solid #E2E5EA; border-radius:8px; padding:8px 12px; font-size:13px; background:#fff; color:#1A1D23; width:100%; font-family:inherit; }
  .fg select:focus, .fg input:focus { outline:none; border-color:#1D9E75; }
</style>

{{-- Stats --}}
<div class="sc-grid">
  <div class="sc red"><div class="sn">{{ $menunggu->count() }}</div><div class="sl">Menunggu tugasan</div></div>
  <div class="sc amb"><div class="sn">{{ $dalam_proses->count() }}</div><div class="sl">Sedang dibaiki</div></div>
  <div class="sc grn"><div class="sn">{{ $selesai->count() }}</div><div class="sl">Selesai</div></div>
</div>

{{-- Menunggu Tugasan --}}
<p class="sec-title"><span class="sec-dot" style="background:#E24B4A"></span>Perlu tugaskan juruteknik</p>

@forelse ($menunggu as $a)
  @php
    $stripCls = match($a->keutamaan) { 'Kritikal'=>'strip-k','Tinggi'=>'strip-t','Sederhana'=>'strip-s',default=>'strip-r' };
    $priEmoji = match($a->keutamaan) { 'Kritikal'=>'🚨','Tinggi'=>'🔴','Sederhana'=>'🟡',default=>'🟢' };
  @endphp
  <div class="acard" x-data="{ open:false }">
    <div class="acard-strip {{ $stripCls }}"></div>
    <div class="acard-top">
      <h4>{{ $a->nama_peralatan }}</h4>
      <span class="abadge ab-baru">⚡ Baru</span>
    </div>
    <div class="ameta">
      <span class="atag">📍 {{ $a->lokasi }}</span>
      <span class="atag">🏢 {{ $a->bahagian_pelapor }}</span>
      <span class="atag">{{ $priEmoji }} {{ $a->keutamaan }}</span>
      <span class="atag">📅 {{ $a->tarikh_rosak?->format('d/m/Y') }}</span>
    </div>
    <p class="adesc">{{ $a->perihal_kerosakan }}</p>
    <div class="afooter">
      <div class="afoot-info">👤 {{ $a->nama_pelapor }}{{ $a->no_telefon_pelapor ? ' · '.$a->no_telefon_pelapor : '' }} &nbsp;·&nbsp; <span style="font-family:monospace">{{ $a->no_tiket }}</span></div>
      <div class="aactions">
        @can('aduan.assign')
        <button class="abtn pri" @click="open=!open">
          <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg>
          Tugaskan
        </button>
        @endcan
        <button class="abtn" wire:click="maklum({{ $a->id }})">
          <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
          Maklum
        </button>
      </div>
    </div>

    @can('aduan.assign')
    <div x-show="open" x-cloak style="margin-top:12px">
      <div class="assign-box">
        <div class="fg">
          <label>Juruteknik / Kontraktor</label>
          <select x-ref="tek{{ $a->id }}">
            @foreach ($juruteknik_list as $id => $nama)
            <option value="{{ $id }}">{{ $nama }}</option>
            @endforeach
          </select>
        </div>
        <div class="fg">
          <label>Sasaran siap</label>
          <input type="date" x-ref="dat{{ $a->id }}">
        </div>
        <div class="fg">
          <label>Catatan (pilihan)</label>
          <input type="text" x-ref="cat{{ $a->id }}" placeholder="Arahan khas...">
        </div>
        <div style="display:flex;gap:8px;justify-content:flex-end">
          <button class="abtn" @click="open=false">Batal</button>
          <button class="abtn pri"
            @click="$wire.tugaskan({{ $a->id }}, $refs.tek{{ $a->id }}.value, $refs.cat{{ $a->id }}.value, $refs.dat{{ $a->id }}.value); open=false">
            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
            Tugaskan
          </button>
        </div>
      </div>
    </div>
    @endcan
  </div>
@empty
  <div class="empty-state" style="margin-bottom:20px">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    <p>Tiada aduan menunggu tugasan.</p>
  </div>
@endforelse

{{-- Dalam Proses --}}
<p class="sec-title" style="margin-top:28px"><span class="sec-dot" style="background:#EF9F27"></span>Dalam proses — menunggu pengesahan</p>

@forelse ($dalam_proses as $a)
  @php
    $stripCls = match($a->keutamaan) { 'Kritikal'=>'strip-k','Tinggi'=>'strip-t','Sederhana'=>'strip-s',default=>'strip-r' };
    $priEmoji = match($a->keutamaan) { 'Kritikal'=>'🚨','Tinggi'=>'🔴','Sederhana'=>'🟡',default=>'🟢' };
  @endphp
  <div class="acard">
    <div class="acard-strip {{ $stripCls }}"></div>
    <div class="acard-top">
      <h4>{{ $a->nama_peralatan }}</h4>
      <span class="abadge ab-prog">
        <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-3.51"/></svg>
        Dalam Proses
      </span>
    </div>
    <div class="ameta">
      <span class="atag">📍 {{ $a->lokasi }}</span>
      <span class="atag">🏢 {{ $a->bahagian_pelapor }}</span>
      <span class="atag">{{ $priEmoji }} {{ $a->keutamaan }}</span>
      @if($a->juruteknik) <span class="atag">👤 {{ $a->juruteknik->name }}</span> @endif
      @if($a->tarikh_sasaran_siap) <span class="atag">🎯 Sasaran: {{ $a->tarikh_sasaran_siap->format('d/m/Y') }}</span> @endif
    </div>
    <p class="adesc">{{ $a->perihal_kerosakan }}</p>
    <div class="afooter">
      <div class="afoot-info">👤 {{ $a->nama_pelapor }}{{ $a->no_telefon_pelapor ? ' · '.$a->no_telefon_pelapor : '' }} &nbsp;·&nbsp; <span style="font-family:monospace">{{ $a->no_tiket }}</span></div>
      <div class="aactions">
        @can('aduan.verify')
        <button class="abtn warn"
          wire:click="tandaSelesai({{ $a->id }})"
          wire:confirm="Tandakan aduan {{ $a->no_tiket }} sebagai selesai?">
          <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
          Selesai
        </button>
        @endcan
        <button class="abtn" wire:click="maklum({{ $a->id }})">
          <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
          Maklum
        </button>
      </div>
    </div>
  </div>
@empty
  <div class="empty-state">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    <p>Tiada aduan dalam proses.</p>
  </div>
@endforelse

{{-- Selesai --}}
@if($selesai->count())
<p class="sec-title" style="margin-top:28px"><span class="sec-dot" style="background:#1D9E75"></span>Selesai (10 terkini)</p>
@foreach ($selesai as $a)
  <div class="acard" style="opacity:.7">
    <div class="acard-strip strip-s"></div>
    <div class="acard-top">
      <h4>{{ $a->nama_peralatan }}</h4>
      <span class="abadge ab-selesai">
        <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
        Selesai
      </span>
    </div>
    <div class="ameta">
      <span class="atag">📍 {{ $a->lokasi }}</span>
      <span class="atag">🏢 {{ $a->bahagian_pelapor }}</span>
      @if($a->tarikh_siap) <span class="atag">✅ Siap: {{ $a->tarikh_siap->format('d/m/Y') }}</span> @endif
    </div>
    <div class="afoot-info" style="margin-top:4px">👤 {{ $a->nama_pelapor }} &nbsp;·&nbsp; <span style="font-family:monospace">{{ $a->no_tiket }}</span></div>
  </div>
@endforeach
@endif

</x-filament-panels::page>
