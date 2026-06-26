@extends('admin.layout')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="stat-grid">
  <div class="sc red"><div class="n">{{ $stats['baru'] }}</div><div class="l">Belum ditindak</div></div>
  <div class="sc amber"><div class="n">{{ $stats['dalam_proses'] }}</div><div class="l">Dalam proses</div></div>
  <div class="sc green"><div class="n">{{ $stats['selesai'] }}</div><div class="l">Selesai bulan ini</div></div>
  <div class="sc"><div class="n">{{ $stats['jumlah'] }}</div><div class="l">Jumlah bulan ini</div></div>
</div>

<p style="font-size:13px;font-weight:600;margin-bottom:12px">Aduan terbaru</p>
<div class="aduan-list">
  @forelse ($terbaru as $a)
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
          <a href="{{ route('admin.aduan') }}" class="btn"><i class="ti ti-arrow-right"></i> Lihat semua</a>
        </div>
      </div>
    </div>
  @empty
    <div class="empty"><i class="ti ti-mood-happy"></i><p>Tiada aduan buat masa ini.</p></div>
  @endforelse
</div>
@endsection
