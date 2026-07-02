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
        'anggaran_kos', 'kos_sebenar',
    ];

    protected $casts = [
        'tarikh_rosak' => 'date',
        'tarikh_sasaran_siap' => 'date',
        'tarikh_siap' => 'date',
        'anggaran_kos' => 'decimal:2',
        'kos_sebenar' => 'decimal:2',
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

    /** Aduan lewat: siap selepas tarikh sasaran, atau masih terbuka melepasi tarikh sasaran. */
    public function isLewat(): bool
    {
        if (! $this->tarikh_sasaran_siap) {
            return false;
        }

        if ($this->tarikh_siap) {
            return $this->tarikh_siap->gt($this->tarikh_sasaran_siap);
        }

        return ! in_array($this->status, ['Selesai', 'Ditutup'])
            && $this->tarikh_sasaran_siap->lt(now()->startOfDay());
    }

    public function scopeLewat($query)
    {
        return $query->where(function ($q) {
            $q->whereNotNull('tarikh_sasaran_siap')->where(function ($q2) {
                $q2->where(function ($q3) {
                    $q3->whereNotNull('tarikh_siap')
                        ->whereColumn('tarikh_siap', '>', 'tarikh_sasaran_siap');
                })->orWhere(function ($q3) {
                    $q3->whereNull('tarikh_siap')
                        ->whereNotIn('status', ['Selesai', 'Ditutup'])
                        ->where('tarikh_sasaran_siap', '<', now()->startOfDay());
                });
            });
        });
    }

    /** Tempoh siap dalam hari (tarikh_rosak -> tarikh_siap), null jika belum siap. */
    public function tempohSiapHari(): ?int
    {
        return $this->tarikh_siap ? $this->tarikh_rosak->diffInDays($this->tarikh_siap) : null;
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
