<div wire:poll.5s="checkBaru">

  @if($adaBaru)
  <div style="background:#E1F5EE;border:1px solid #1D9E75;border-radius:10px;padding:10px 16px;margin-bottom:16px;display:flex;align-items:center;gap:10px;animation:pulse 1s ease 3"
       x-data x-init="setTimeout(()=>$el.style.display='none',6000)">
    <i class="ti ti-bell-ringing" style="color:#1D9E75;font-size:20px"></i>
    <span style="font-size:13px;font-weight:600;color:#085041">Aduan baru masuk!</span>
  </div>
  @endif

  {{-- Stats bar --}}
  <div class="stat-grid" style="margin-bottom:20px">
    <div class="sc red">
      <div class="n">{{ $stats['baru'] }}</div>
      <div class="l">Belum ditindak</div>
    </div>
    <div class="sc amber">
      <div class="n">{{ $stats['dalam_proses'] }}</div>
      <div class="l">Dalam proses</div>
    </div>
    <div class="sc green">
      <div class="n">{{ $stats['selesai'] }}</div>
      <div class="l">Selesai hari ini</div>
    </div>
    <div class="sc" style="border-color:{{ $stats['kritikal'] > 0 ? '#E24B4A' : '#E2E5EA' }}">
      <div class="n" style="color:{{ $stats['kritikal'] > 0 ? '#E24B4A' : '#1A1D23' }}">{{ $stats['kritikal'] }}</div>
      <div class="l">Kritikal / Tinggi belum ditindak</div>
    </div>
  </div>

  {{-- Label kemaskini --}}
  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px">
    <p style="font-size:13px;font-weight:600">Aduan Terkini</p>
    <span style="font-size:11px;color:#9CA3AF;display:flex;align-items:center;gap:5px">
      <span style="width:7px;height:7px;border-radius:50%;background:#1D9E75;display:inline-block;animation:blink 1.5s ease infinite"></span>
      Kemaskini setiap 5 saat
    </span>
  </div>

  {{-- Aduan cards --}}
  <div class="aduan-list">
    @forelse ($terkini as $a)
      @php
        $stripCls = match($a->keutamaan) { 'Kritikal'=>'strip-k','Tinggi'=>'strip-t','Sederhana'=>'strip-s',default=>'strip-r' };
        $priEmoji = match($a->keutamaan) { 'Kritikal'=>'🚨','Tinggi'=>'🔴','Sederhana'=>'🟡',default=>'🟢' };
        $isNew = $a->created_at->gt(now()->subMinutes(5));
      @endphp
      <div class="acard{{ $isNew ? ' flash' : '' }}">
        <div class="acard-strip {{ $stripCls }}"></div>
        @if($isNew)
        <div style="position:absolute;top:8px;right:8px;background:#E24B4A;color:#fff;font-size:9px;font-weight:700;padding:2px 7px;border-radius:99px;letter-spacing:.5px">BARU</div>
        @endif
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
          <span class="tag"><i class="ti ti-clock" style="font-size:12px"></i>{{ $a->created_at->diffForHumans() }}</span>
          @if($a->juruteknik)<span class="tag"><i class="ti ti-user" style="font-size:12px"></i>{{ $a->juruteknik->name }}</span>@endif
        </div>
        <div class="acard-footer">
          <div class="acard-footer-info">
            <i class="ti ti-user" style="font-size:12px"></i> {{ $a->nama_pelapor }}{{ $a->no_telefon_pelapor ? ' · '.$a->no_telefon_pelapor : '' }}
            &nbsp;·&nbsp;<span style="font-family:monospace">{{ $a->no_tiket }}</span>
          </div>
          @if($a->status === 'Baru' && auth()->user()->can('aduan.assign'))
          <div class="acard-actions">
            <a href="{{ route('admin.aliran') }}" class="btn pri"><i class="ti ti-user-check"></i> Tugaskan</a>
          </div>
          @endif
        </div>
      </div>
    @empty
      <div class="empty"><i class="ti ti-mood-happy"></i><p>Tiada aduan buat masa ini.</p></div>
    @endforelse
  </div>

  <style>
    @keyframes blink { 0%,100%{opacity:1} 50%{opacity:.2} }
    .acard.flash { animation: flashIn 2s ease; }
    @keyframes flashIn { 0%{background:#E1F5EE} 100%{background:#fff} }
    @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:.6} }
  </style>
</div>
