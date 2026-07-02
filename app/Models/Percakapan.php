<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Percakapan extends Model
{
    protected $fillable = [
        'kontak_id',
        'pesan_terakhir',
        'waktu_pesan_terakhir'
    ];

    protected $casts = [
        'waktu_pesan_terakhir' => 'datetime'
    ];

    public function kontak()
    {
        return $this->belongsTo(Kontak::class);
    }

    public function pesans()
    {
        return $this->hasMany(Pesan::class);
    }
}
