<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Aduan extends Model
{
    use SoftDeletes;

    protected $table = 'aduan';

    protected $fillable = [
        'no_tiket', 'nama_pelapor', 'bahagian_pelapor', 'no_telefon_pelapor',
        'nama_peralatan', 'lokasi', 'perihal_kerosakan', 'tarikh_rosak',
        'keutamaan', 'status', 'kategori', 'juruteknik_id',
        'disahkan_oleh', 'diluluskan_oleh', 'tarikh_sasaran_siap',
        'tarikh_siap', 'catatan_penyelia', 'tindakan_juruteknik',
    ];

    protected $casts = [
        'tarikh_rosak' => 'date',
        'tarikh_sasaran_siap' => 'date',
        'tarikh_siap' => 'date',
    ];

    public function juruteknik()
    {
        return $this->belongsTo(User::class, 'juruteknik_id');
    }

    public function pengesah()
    {
        return $this->belongsTo(User::class, 'disahkan_oleh');
    }

    public function pelulus()
    {
        return $this->belongsTo(User::class, 'diluluskan_oleh');
    }

    public function gambar()
    {
        return $this->hasMany(GambarAduan::class);
    }

    public function nota()
    {
        return $this->hasMany(NotaAduan::class)->latest();
    }

    // Auto-jana no tiket — ADU-2026-0001
    protected static function booted(): void
    {
        static::creating(function (Aduan $aduan) {
            if (! empty($aduan->no_tiket)) {
                return;
            }

            $tahun = now()->year;
            $bil = static::withTrashed()->whereYear('created_at', $tahun)->count() + 1;
            $aduan->no_tiket = 'ADU-' . $tahun . '-' . str_pad($bil, 4, '0', STR_PAD_LEFT);
        });
    }
}
