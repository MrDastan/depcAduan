<?php

namespace App\Http\Controllers;

use App\Models\Aduan;
use App\Models\GambarAduan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AduanStaffController extends Controller
{
    /** Paparkan borang aduan staff. */
    public function borang(): View
    {
        return view('staff.aduan');
    }

    /** Terima & simpan aduan baru (dipanggil melalui fetch). */
    public function hantar(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nama_pelapor' => 'required|string|max:100',
            'bahagian_pelapor' => 'required|string|max:100',
            'no_telefon_pelapor' => 'nullable|string|max:20',
            'nama_peralatan' => 'required|string|max:200',
            'lokasi' => 'required|string|max:200',
            'perihal_kerosakan' => 'required|string',
            'tarikh_rosak' => 'required|date',
            'keutamaan' => 'required|in:Rendah,Sederhana,Tinggi,Kritikal',
            'gambar' => 'nullable|array|max:5',
            'gambar.*' => 'nullable|image|max:5120',
        ]);

        $aduan = Aduan::create($validated);

        if ($request->hasFile('gambar')) {
            foreach ($request->file('gambar') as $file) {
                $path = $file->store('aduan/' . $aduan->id, 'public');
                GambarAduan::create([
                    'aduan_id' => $aduan->id,
                    'path' => $path,
                    'nama_asal' => $file->getClientOriginalName(),
                ]);
            }
        }

        // TODO: Hantar notifikasi kepada penyelia (email / Telegram)

        return response()->json([
            'success' => true,
            'no_tiket' => $aduan->no_tiket,
        ]);
    }

    /** Semak status aduan melalui no tiket (dipanggil melalui fetch). */
    public function semak(string $tiket): JsonResponse
    {
        $aduan = Aduan::where('no_tiket', $tiket)->first();

        if (! $aduan) {
            return response()->json([
                'success' => false,
                'message' => 'Nombor tiket tidak dijumpai. Sila semak semula.',
            ], 404);
        }

        $urutan = ['Baru' => 0, 'Dalam Proses' => 1, 'Selesai' => 2, 'Ditutup' => 3];
        $kini = $urutan[$aduan->status] ?? 0;

        $langkah = [
            [
                'label' => 'Aduan diterima',
                'sub' => $aduan->created_at->translatedFormat('d M Y, g:i a'),
                'state' => 'done',
            ],
            [
                'label' => 'Disahkan & ditugaskan penyelia',
                'sub' => $aduan->juruteknik
                    ? 'Juruteknik: ' . $aduan->juruteknik->name
                    : 'Menunggu penyelia',
                'state' => $kini >= 1 ? 'done' : ($kini === 0 ? 'active' : ''),
            ],
            [
                'label' => 'Juruteknik sedang bekerja',
                'sub' => $aduan->tarikh_sasaran_siap
                    ? 'Dijangka siap: ' . $aduan->tarikh_sasaran_siap->translatedFormat('d M Y')
                    : 'Menunggu tugasan',
                'state' => $kini >= 2 ? 'done' : ($kini === 1 ? 'active' : ''),
            ],
            [
                'label' => 'Selesai',
                'sub' => $aduan->tarikh_siap
                    ? 'Siap: ' . $aduan->tarikh_siap->translatedFormat('d M Y')
                    : '—',
                'state' => $kini >= 2 ? 'done' : '',
            ],
        ];

        $badge = match ($aduan->status) {
            'Baru' => 'baru',
            'Dalam Proses' => 'prog',
            'Selesai', 'Ditutup' => 'selesai',
            default => 'prog',
        };

        return response()->json([
            'success' => true,
            'no_tiket' => $aduan->no_tiket,
            'status' => $aduan->status,
            'badge' => $badge,
            'info' => $aduan->nama_peralatan . ' · ' . $aduan->lokasi,
            'steps' => $langkah,
        ]);
    }
}
