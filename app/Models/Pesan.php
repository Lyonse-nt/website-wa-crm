<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pesan extends Model
{
    protected $fillable = [
        'percakapan_id',
        'arah_pesan',
        'jenis_pesan',
        'isi_pesan',
        'whatsapp_message_id',
        'status',
        'raw_response'
    ];

    protected $casts = [
        'raw_response' => 'array'
    ];

    public function percakapan()
    {
        return $this->belongsTo(Percakapan::class);
    }
}
