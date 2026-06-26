<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotaAduan extends Model
{
    protected $table = 'nota_aduan';

    protected $fillable = ['aduan_id', 'user_id', 'jenis', 'kandungan'];

    public function aduan()
    {
        return $this->belongsTo(Aduan::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
