<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatbotSetting extends Model
{
    protected $fillable = [
        'setting_key',
        'setting_value',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public static function getSetting(string $key, $default = null)
    {
        $setting = self::where('setting_key', $key)
            ->where('is_active', true)
            ->first();
        
        return $setting ? $setting->setting_value : $default;
    }

    public static function setSetting(string $key, $value, bool $isActive = true)
    {
        return self::updateOrCreate(
            ['setting_key' => $key],
            [
                'setting_value' => $value,
                'is_active' => $isActive
            ]
        );
    }
}
