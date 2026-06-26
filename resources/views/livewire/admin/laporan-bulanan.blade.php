<div>
  {{-- Pemilih bulan --}}
  <div class="filters" style="margin-bottom:20px">
    <select wire:model.live="bulan">
      @foreach (range(1,12) as $m)
      <option value="{{ $m }}">{{ \Carbon\Carbon::create(null,$m)->translatedFormat('F') }}</option>
      @endforeach
    </select>
    <select wire:model.live="tahun">
      @foreach (range(now()->year, now()->year-3, -1) as $y)
      <option value="{{ $y }}">{{ $y }}</option>
      @endforeach
    </select>
    <button class="btn" wire:click="$refresh" style="margin-left:auto"><i class="ti ti-refresh"></i> Kemaskini</button>
  </div>

  {{-- Stats --}}
  <div class="stat-grid">
    <div class="sc red">
      <div class="n">{{ $jumlah }}</div>
      <div class="l">Jumlah aduan</div>
      @if($jumlahLepas > 0)
      <div style="font-size:10px;margin-top:4px;color:{{ $jumlah > $jumlahLepas ? '#E24B4A' : '#1D9E75' }}">
        {{ $jumlah > $jumlahLepas ? '▲' : '▼' }} {{ abs($jumlah - $jumlahLepas) }} vs bulan lepas
      </div>
      @endif
    </div>
    <div class="sc green"><div class="n">{{ $jmlSelesai }}</div><div class="l">Selesai dibaiki</div></div>
    <div class="sc amber"><div class="n">{{ $kadar }}%</div><div class="l">Kadar penyelesaian</div></div>
    <div class="sc"><div class="n">{{ $purataDays ? number_format($purataDays,1) : '—' }}</div><div class="l">Purata hari selesai</div></div>
  </div>

  {{-- Kategori --}}
  @if($byKategori->count())
  <p style="font-size:13px;font-weight:600;margin-bottom:10px">Mengikut Kategori</p>
  <div style="background:#fff;border:1px solid #E2E5EA;border-radius:12px;overflow:hidden;margin-bottom:20px">
    <table class="tbl">
      <thead><tr><th>Kategori</th><th style="text-align:center">Jumlah</th><th style="text-align:center">Selesai</th><th style="text-align:center">Kadar</th></tr></thead>
      <tbody>
        @foreach ($byKategori as $k)
        <tr>
          <td>{{ $k['nama'] }}</td>
          <td style="text-align:center;font-weight:600">{{ $k['jumlah'] }}</td>
          <td style="text-align:center">{{ $k['selesai'] }}</td>
          <td style="text-align:center">
            <span class="badge {{ $k['kadar'] >= 70 ? 'selesai' : ($k['kadar'] >= 40 ? 'prog' : 'baru') }}">{{ $k['kadar'] }}%</span>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  @endif

  {{-- Keutamaan & Bahagian --}}
  <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px">
    <div>
      <p style="font-size:13px;font-weight:600;margin-bottom:10px">Mengikut Keutamaan</p>
      <div style="background:#fff;border:1px solid #E2E5EA;border-radius:12px;overflow:hidden">
        <table class="tbl">
          <thead><tr><th>Keutamaan</th><th style="text-align:center">Jumlah</th><th style="text-align:center">Selesai</th></tr></thead>
          <tbody>
            @foreach ($byKeutamaan as $p)
            <tr>
              <td>
                <span class="badge {{ match($p['nama']){'Kritikal','Tinggi'=>'baru','Sederhana'=>'prog',default=>'selesai'} }}">{{ $p['nama'] }}</span>
              </td>
              <td style="text-align:center;font-weight:600">{{ $p['jumlah'] }}</td>
              <td style="text-align:center">{{ $p['selesai'] }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
    <div>
      <p style="font-size:13px;font-weight:600;margin-bottom:10px">Mengikut Bahagian</p>
      <div style="background:#fff;border:1px solid #E2E5EA;border-radius:12px;padding:16px">
        @forelse ($byBahagian as $b)
        @php $pct = $jumlah > 0 ? round($b['jumlah']/$jumlah*100) : 0; @endphp
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px">
          <span style="font-size:12px;font-weight:500;width:36px;color:#6B7280">{{ $b['nama'] }}</span>
          <div style="flex:1;background:#F5F6F8;border-radius:99px;height:6px"><div style="background:#1D9E75;height:6px;border-radius:99px;width:{{ $pct }}%"></div></div>
          <span style="font-size:12px;color:#6B7280;width:20px;text-align:right">{{ $b['jumlah'] }}</span>
        </div>
        @empty
        <p style="font-size:13px;color:#9CA3AF;text-align:center;padding:20px 0">Tiada data.</p>
        @endforelse
      </div>
    </div>
  </div>

  {{-- Trend --}}
  <p style="font-size:13px;font-weight:600;margin-bottom:10px">Trend 6 Bulan Terakhir</p>
  <div style="background:#fff;border:1px solid #E2E5EA;border-radius:12px;overflow:hidden;margin-bottom:16px">
    <table class="tbl">
      <thead><tr><th>Bulan</th><th style="text-align:center">Diterima</th><th style="text-align:center">Selesai</th><th>Graf</th></tr></thead>
      <tbody>
        @php $maxVal = collect($trend)->max('jumlah') ?: 1; @endphp
        @foreach ($trend as $t)
        <tr>
          <td style="font-weight:500">{{ $t['label'] }}</td>
          <td style="text-align:center;font-weight:600;color:#1D9E75">{{ $t['jumlah'] }}</td>
          <td style="text-align:center;color:#185FA5">{{ $t['selesai'] }}</td>
          <td><div style="background:#1D9E75;height:6px;border-radius:3px;width:{{ max(2,round($t['jumlah']/$maxVal*120)) }}px"></div></td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <button class="btn full" onclick="window.print()"><i class="ti ti-printer"></i> Cetak / Simpan PDF</button>
</div>
