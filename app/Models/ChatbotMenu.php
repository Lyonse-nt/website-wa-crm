<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatbotMenu extends Model
{
    protected $fillable = [
        'menu_number',
        'menu_label',
        'reply_message',
        'is_active',
        'order'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'menu_number' => 'integer',
        'order' => 'integer'
    ];

    public static function getActiveMenus()
    {
        return self::where('is_active', true)
            ->orderBy('order')
            ->orderBy('menu_number')
            ->get();
    }

    public static function findByNumber(int $number)
    {
        return self::where('menu_number', $number)
            ->where('is_active', true)
            ->first();
    }
}
