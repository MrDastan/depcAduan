<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class GambarAduan extends Model
{
    protected $table = 'gambar_aduan';

    protected $fillable = ['aduan_id', 'path', 'nama_asal'];

    public function aduan()
    {
        return $this->belongsTo(Aduan::class);
    }

    public function getUrlAttribute(): string
    {
        return Storage::url($this->path);
    }
}
