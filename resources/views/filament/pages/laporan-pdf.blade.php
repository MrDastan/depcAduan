<!DOCTYPE html>
<html lang="ms">
<head>
<meta charset="UTF-8">
<title>Laporan Bulanan {{ $namaBulan }}</title>
<style>
  body { font-family: Arial, sans-serif; font-size: 13px; color: #1a1d23; padding: 30px; }
  h1 { font-size: 18px; margin-bottom: 4px; }
  p.sub { color: #6b7280; font-size: 12px; margin-bottom: 24px; }
  .grid4 { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin-bottom: 24px; }
  .card { border: 1px solid #e2e5ea; border-radius: 8px; padding: 12px 14px; }
  .card .n { font-size: 22px; font-weight: 700; }
  .card .l { font-size: 11px; color: #6b7280; }
  table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
  th { background: #f5f6f8; text-align: left; padding: 8px 12px; font-size: 11px; color: #6b7280; text-transform: uppercase; }
  td { padding: 8px 12px; border-top: 1px solid #e2e5ea; }
  h2 { font-size: 13px; font-weight: 600; margin: 20px 0 8px; }
  .footer { margin-top: 40px; font-size: 11px; color: #9ca3af; border-top: 1px solid #e2e5ea; padding-top: 12px; }
</style>
</head>
<body>
<h1>Laporan Bulanan Penyelenggaraan</h1>
<p class="sub">{{ $namaBulan }} &nbsp;·&nbsp; Jana oleh SysPenyelenggaraan &nbsp;·&nbsp; {{ now()->format('d/m/Y H:i') }}</p>

<div class="grid4">
  <div class="card"><div class="n">{{ $jumlah }}</div><div class="l">Jumlah Aduan</div></div>
  <div class="card"><div class="n">{{ $jmlSelesai }}</div><div class="l">Selesai</div></div>
  <div class="card"><div class="n">{{ $kadar }}%</div><div class="l">Kadar Penyelesaian</div></div>
  <div class="card"><div class="n">{{ $purataDays ? number_format($purataDays, 1) : '—' }}</div><div class="l">Purata Hari Selesai</div></div>
</div>

<h2>Mengikut Kategori</h2>
<table>
  <thead><tr><th>Kategori</th><th>Jumlah</th><th>Selesai</th><th>Kadar</th></tr></thead>
  <tbody>
    @foreach ($byKategori as $k)
    @if ($k['jumlah'] > 0)
    <tr><td>{{ $k['nama'] }}</td><td>{{ $k['jumlah'] }}</td><td>{{ $k['selesai'] }}</td><td>{{ $k['kadar'] }}%</td></tr>
    @endif
    @endforeach
  </tbody>
</table>

<h2>Mengikut Keutamaan</h2>
<table>
  <thead><tr><th>Keutamaan</th><th>Jumlah</th><th>Selesai</th></tr></thead>
  <tbody>
    @foreach ($byKeutamaan as $p)
    <tr><td>{{ $p['nama'] }}</td><td>{{ $p['jumlah'] }}</td><td>{{ $p['selesai'] }}</td></tr>
    @endforeach
  </tbody>
</table>

<h2>Trend 6 Bulan</h2>
<table>
  <thead><tr><th>Bulan</th><th>Diterima</th><th>Selesai</th></tr></thead>
  <tbody>
    @foreach ($trend as $t)
    <tr><td>{{ $t['label'] }}</td><td>{{ $t['jumlah'] }}</td><td>{{ $t['selesai'] }}</td></tr>
    @endforeach
  </tbody>
</table>

<div class="footer">Dokumen ini dijana secara automatik oleh Sistem Pengurusan Penyelenggaraan DEPC/HQ.</div>
</body>
</html>
