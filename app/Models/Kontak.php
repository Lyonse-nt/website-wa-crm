<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kontak extends Model
{
    protected $fillable = [
        'nama',
        'nomor_whatsapp'
    ];

    public function percakapans()
    {
        return $this->hasMany(Percakapan::class);
    }

    public function percakapanAktif()
    {
        return $this->hasOne(Percakapan::class)->latest('waktu_pesan_terakhir');
    }
}
