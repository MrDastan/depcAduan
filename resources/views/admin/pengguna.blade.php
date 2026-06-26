@extends('admin.layout')
@section('title', 'Pengurusan Pengguna')
@section('page-title', 'Pengurusan Pengguna')

@section('content')
<p style="font-size:13px;font-weight:600;margin-bottom:12px">Peringkat akses mengikut jawatan</p>
<div style="display:flex;flex-direction:column;gap:10px;margin-bottom:28px">
  @foreach ($roles as $r)
  <div style="background:#fff;border:1px solid #E2E5EA;border-left:4px solid {{ $r['warna'] }};border-radius:12px;padding:16px">
    <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;flex-wrap:wrap">
      <div class="avatar" style="background:{{ $r['warna'] }}22;color:{{ $r['warna'] }}">{{ $r['init'] }}</div>
      <strong style="font-size:14px">{{ $r['title'] }}</strong>
      <span style="font-size:11px;padding:2px 10px;border-radius:99px;background:{{ $r['warna'] }}22;color:{{ $r['warna'] }};font-weight:600">{{ $r['level'] }}</span>
    </div>
    <p style="font-size:12px;color:#6B7280;margin-bottom:10px">{{ $r['desc'] }}</p>
    <ul style="list-style:none;display:flex;flex-direction:column;gap:4px">
      @foreach ($r['ya'] as $x)
      <li style="font-size:12px;display:flex;align-items:center;gap:6px"><i class="ti ti-check" style="color:#1D9E75;font-size:14px"></i>{{ $x }}</li>
      @endforeach
      @foreach ($r['tidak'] as $x)
      <li style="font-size:12px;display:flex;align-items:center;gap:6px;text-decoration:line-through;color:#9CA3AF"><i class="ti ti-x" style="color:#E24B4A;font-size:14px"></i>{{ $x }}</li>
      @endforeach
    </ul>
  </div>
  @endforeach
</div>

<p style="font-size:13px;font-weight:600;margin-bottom:12px">Senarai Pengguna Aktif</p>
<div style="background:#fff;border:1px solid #E2E5EA;border-radius:12px;overflow:hidden">
  <table class="tbl">
    <thead>
      <tr>
        <th>Nama</th>
        <th>E-mel</th>
        <th>Bahagian</th>
        <th>Peranan</th>
      </tr>
    </thead>
    <tbody>
      @forelse ($pengguna as $u)
      <tr>
        <td style="font-weight:500">{{ $u->name }}</td>
        <td style="color:#6B7280">{{ $u->email }}</td>
        <td>{{ $u->bahagian ?? '—' }}</td>
        <td>
          @foreach ($u->roles as $role)
          <span style="font-size:11px;padding:2px 8px;border-radius:99px;background:#E1F5EE;color:#085041;font-weight:600">{{ $role->name }}</span>
          @endforeach
        </td>
      </tr>
      @empty
      <tr><td colspan="4" style="text-align:center;color:#9CA3AF;padding:24px">Tiada pengguna.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>
@endsection
