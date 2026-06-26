@extends('admin.layout')
@section('title', 'Notifikasi')
@section('page-title', 'Notifikasi')

@section('content')
<p style="font-size:13px;font-weight:600;margin-bottom:12px">Notifikasi terkini</p>
<div style="display:flex;flex-direction:column;gap:8px;margin-bottom:28px">
  @forelse ($notifs as $n)
    @php
      $dotColor = match($n['warna']) { 'red'=>'#E24B4A','orange'=>'#F97316','amber'=>'#EF9F27','green'=>'#1D9E75',default=>'#185FA5' };
    @endphp
    <div style="display:flex;gap:12px;padding:14px;background:#fff;border:1px solid #E2E5EA;border-radius:10px">
      <div style="width:9px;height:9px;border-radius:50%;background:{{ $dotColor }};margin-top:4px;flex-shrink:0"></div>
      <div>
        <p style="font-size:13px">{{ $n['teks'] }}</p>
        <span style="font-size:11px;color:#9CA3AF">{{ $n['sub'] }}</span>
      </div>
    </div>
  @empty
    <div class="empty"><i class="ti ti-bell-off"></i><p>Tiada notifikasi buat masa ini.</p></div>
  @endforelse
</div>

<p style="font-size:13px;font-weight:600;margin-bottom:12px">Log Aktiviti Terkini</p>
<div style="display:flex;flex-direction:column;gap:6px">
  @forelse ($log as $l)
    @php
      $icon = match($l->jenis) { 'tugasan'=>'ti-user-plus','selesai'=>'ti-circle-check',default=>'ti-message' };
    @endphp
    <div style="display:flex;gap:12px;padding:12px 14px;background:#fff;border:1px solid #E2E5EA;border-radius:10px;align-items:flex-start">
      <i class="ti {{ $icon }}" style="font-size:16px;color:#6B7280;margin-top:1px;flex-shrink:0"></i>
      <div>
        <p style="font-size:13px"><span style="font-family:monospace;color:#6B7280">{{ $l->aduan?->no_tiket ?? '—' }}</span> · {{ $l->kandungan }}</p>
        <span style="font-size:11px;color:#9CA3AF">{{ $l->user?->name ?? '—' }} · {{ $l->created_at->diffForHumans() }}</span>
      </div>
    </div>
  @empty
    <div class="empty"><i class="ti ti-clipboard-list"></i><p>Tiada log aktiviti.</p></div>
  @endforelse
</div>
@endsection
