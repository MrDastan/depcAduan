<x-filament-panels::page>

    {{-- Pemilih bulan & tahun --}}
    <div class="flex gap-3 mb-6 flex-wrap items-end">
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Bulan</label>
            <select wire:model.live="bulan" class="text-sm border border-gray-300 rounded-lg px-3 py-2">
                @foreach (range(1,12) as $m)
                <option value="{{ $m }}">{{ \Carbon\Carbon::create(null, $m)->translatedFormat('F') }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Tahun</label>
            <select wire:model.live="tahun" class="text-sm border border-gray-300 rounded-lg px-3 py-2">
                @foreach (range(now()->year, now()->year - 3, -1) as $y)
                <option value="{{ $y }}">{{ $y }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Stats utama --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white border border-gray-200 rounded-xl p-4">
            <div class="text-2xl font-bold text-gray-800">{{ $jumlah }}</div>
            <div class="text-xs text-gray-500 mt-1">Jumlah Aduan Bulan Ini</div>
            @if ($jumlahLepas > 0)
            <div class="text-xs mt-2 {{ $jumlah > $jumlahLepas ? 'text-red-500' : 'text-green-500' }}">
                {{ $jumlah > $jumlahLepas ? '▲' : '▼' }} {{ abs($jumlah - $jumlahLepas) }} vs bulan lepas
            </div>
            @endif
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-4">
            <div class="text-2xl font-bold text-green-600">{{ $jmlSelesai }}</div>
            <div class="text-xs text-gray-500 mt-1">Selesai Dibaiki</div>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-4">
            <div class="text-2xl font-bold {{ $kadar >= 70 ? 'text-green-600' : ($kadar >= 40 ? 'text-amber-600' : 'text-red-600') }}">{{ $kadar }}%</div>
            <div class="text-xs text-gray-500 mt-1">Kadar Penyelesaian</div>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-4">
            <div class="text-2xl font-bold text-blue-600">{{ $purataDays ? number_format($purataDays, 1) : '—' }}</div>
            <div class="text-xs text-gray-500 mt-1">Purata Hari Selesai</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

        {{-- Mengikut Kategori --}}
        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-100 font-semibold text-sm">Mengikut Kategori</div>
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="text-left px-4 py-2 text-xs text-gray-500 uppercase">Kategori</th>
                        <th class="text-center px-3 py-2 text-xs text-gray-500 uppercase">Jumlah</th>
                        <th class="text-center px-3 py-2 text-xs text-gray-500 uppercase">Selesai</th>
                        <th class="text-center px-3 py-2 text-xs text-gray-500 uppercase">Kadar</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($byKategori as $k)
                    @if ($k['jumlah'] > 0)
                    <tr class="border-t border-gray-100">
                        <td class="px-4 py-2.5">{{ $k['nama'] }}</td>
                        <td class="px-3 py-2.5 text-center font-semibold">{{ $k['jumlah'] }}</td>
                        <td class="px-3 py-2.5 text-center">{{ $k['selesai'] }}</td>
                        <td class="px-3 py-2.5 text-center">
                            <span class="text-xs px-2 py-0.5 rounded-full font-medium
                                {{ $k['kadar'] >= 70 ? 'bg-green-100 text-green-700' :
                                   ($k['kadar'] >= 40 ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700') }}">
                                {{ $k['kadar'] }}%
                            </span>
                        </td>
                    </tr>
                    @endif
                    @endforeach
                    @if (collect($byKategori)->sum('jumlah') === 0)
                    <tr><td colspan="4" class="px-4 py-6 text-center text-gray-400 text-xs">Tiada data.</td></tr>
                    @endif
                </tbody>
            </table>
        </div>

        {{-- Mengikut Keutamaan & Bahagian --}}
        <div class="flex flex-col gap-4">
            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-100 font-semibold text-sm">Mengikut Keutamaan</div>
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="text-left px-4 py-2 text-xs text-gray-500 uppercase">Keutamaan</th>
                            <th class="text-center px-3 py-2 text-xs text-gray-500 uppercase">Jumlah</th>
                            <th class="text-center px-3 py-2 text-xs text-gray-500 uppercase">Selesai</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($byKeutamaan as $p)
                        <tr class="border-t border-gray-100">
                            <td class="px-4 py-2">
                                <span class="text-xs px-2 py-0.5 rounded-full font-medium
                                    {{ $p['nama'] === 'Kritikal' ? 'bg-red-100 text-red-700' :
                                       ($p['nama'] === 'Tinggi' ? 'bg-orange-100 text-orange-700' :
                                       ($p['nama'] === 'Sederhana' ? 'bg-amber-100 text-amber-700' : 'bg-green-100 text-green-700')) }}">
                                    {{ $p['nama'] }}
                                </span>
                            </td>
                            <td class="px-3 py-2 text-center font-semibold">{{ $p['jumlah'] }}</td>
                            <td class="px-3 py-2 text-center">{{ $p['selesai'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-100 font-semibold text-sm">Mengikut Bahagian</div>
                @if (count($byBahagian) > 0)
                <div class="p-4 flex flex-col gap-2">
                    @foreach ($byBahagian as $b)
                    <div class="flex items-center gap-3">
                        <span class="text-xs font-medium w-12 text-gray-600">{{ $b['nama'] }}</span>
                        <div class="flex-1 bg-gray-100 rounded-full h-2">
                            <div class="bg-teal-500 h-2 rounded-full" style="width: {{ $jumlah > 0 ? round($b['jumlah'] / $jumlah * 100) : 0 }}%"></div>
                        </div>
                        <span class="text-xs text-gray-500 w-8 text-right">{{ $b['jumlah'] }}</span>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-xs text-gray-400 text-center py-4">Tiada data.</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Trend 6 bulan --}}
    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100 font-semibold text-sm">Trend 6 Bulan Terakhir</div>
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50">
                    <th class="text-left px-4 py-2 text-xs text-gray-500 uppercase">Bulan</th>
                    <th class="text-center px-3 py-2 text-xs text-gray-500 uppercase">Diterima</th>
                    <th class="text-center px-3 py-2 text-xs text-gray-500 uppercase">Selesai</th>
                    <th class="px-4 py-2 text-xs text-gray-500 uppercase">Graf</th>
                </tr>
            </thead>
            <tbody>
                @php $maxVal = collect($trend)->max('jumlah') ?: 1; @endphp
                @foreach ($trend as $t)
                <tr class="border-t border-gray-100">
                    <td class="px-4 py-2.5 font-medium">{{ $t['label'] }}</td>
                    <td class="px-3 py-2.5 text-center font-semibold text-teal-700">{{ $t['jumlah'] }}</td>
                    <td class="px-3 py-2.5 text-center text-green-700">{{ $t['selesai'] }}</td>
                    <td class="px-4 py-2.5">
                        <div class="flex gap-1 items-center">
                            <div class="bg-teal-400 h-2 rounded" style="width: {{ round($t['jumlah'] / $maxVal * 100) }}px; max-width:120px; min-width:2px"></div>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</x-filament-panels::page>
