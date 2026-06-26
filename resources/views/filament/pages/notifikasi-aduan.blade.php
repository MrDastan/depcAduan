<x-filament-panels::page>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Panel kiri: Notifikasi sistem --}}
        <div>
            <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-3">Notifikasi Sistem</h3>

            @if ($notifs->isEmpty())
                <div class="text-center py-12 text-gray-400">
                    <x-heroicon-o-bell-slash class="w-10 h-10 mx-auto mb-2 opacity-40" />
                    <p class="text-sm">Tiada notifikasi buat masa ini.</p>
                </div>
            @else
                <div class="flex flex-col gap-3">
                    @foreach ($notifs as $n)
                    <div class="flex gap-3 p-4 bg-white border border-gray-200 rounded-xl">
                        <div class="mt-1 flex-shrink-0">
                            @if ($n['warna'] === 'red')
                                <span class="w-2.5 h-2.5 rounded-full bg-red-500 block"></span>
                            @elseif ($n['warna'] === 'orange')
                                <span class="w-2.5 h-2.5 rounded-full bg-orange-500 block"></span>
                            @elseif ($n['warna'] === 'amber')
                                <span class="w-2.5 h-2.5 rounded-full bg-amber-400 block"></span>
                            @else
                                <span class="w-2.5 h-2.5 rounded-full bg-green-500 block"></span>
                            @endif
                        </div>
                        <div>
                            <p class="text-sm text-gray-800">{{ $n['teks'] }}</p>
                            <span class="text-xs text-gray-400">{{ $n['sub'] }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Panel kanan: Log aktiviti --}}
        <div>
            <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-3">Log Aktiviti Terkini</h3>

            @if ($log->isEmpty())
                <div class="text-center py-12 text-gray-400">
                    <x-heroicon-o-clipboard-document-list class="w-10 h-10 mx-auto mb-2 opacity-40" />
                    <p class="text-sm">Tiada log aktiviti.</p>
                </div>
            @else
                <div class="flex flex-col gap-2">
                    @foreach ($log as $l)
                    <div class="flex gap-3 p-3 bg-white border border-gray-200 rounded-lg">
                        <div class="flex-shrink-0 w-7 h-7 rounded-full bg-teal-100 flex items-center justify-center">
                            @if ($l->jenis === 'tugasan')
                                <x-heroicon-o-user-plus class="w-3.5 h-3.5 text-teal-700" />
                            @elseif ($l->jenis === 'selesai')
                                <x-heroicon-o-check-circle class="w-3.5 h-3.5 text-green-700" />
                            @else
                                <x-heroicon-o-chat-bubble-left class="w-3.5 h-3.5 text-blue-700" />
                            @endif
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-medium text-gray-700 truncate">{{ $l->aduan?->no_tiket ?? '—' }} · {{ $l->kandungan }}</p>
                            <span class="text-xs text-gray-400">{{ $l->user?->name ?? '—' }} · {{ $l->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>

</x-filament-panels::page>
