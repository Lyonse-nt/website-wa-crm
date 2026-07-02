<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogApi extends Model
{
    protected $fillable = [
        'endpoint',
        'request',
        'response',
        'status_http'
    ];

    protected $casts = [
        'request' => 'array',
        'response' => 'array'
    ];
}
