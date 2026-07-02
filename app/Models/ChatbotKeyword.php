<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatbotKeyword extends Model
{
    protected $fillable = [
        'keywords',
        'reply_message',
        'priority',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function getKeywordsArray()
    {
        return array_map('trim', explode(',', strtolower($this->keywords)));
    }

    public static function findByMessage(string $message)
    {
        $message = strtolower(trim($message));
        
        // Order by priority: high -> medium -> low
        $keywords = self::where('is_active', true)
            ->orderByRaw("CASE priority WHEN 'high' THEN 1 WHEN 'medium' THEN 2 WHEN 'low' THEN 3 END")
            ->get();

        foreach ($keywords as $keyword) {
            $keywordArray = $keyword->getKeywordsArray();
            
            foreach ($keywordArray as $key) {
                if (str_contains($message, $key)) {
                    return $keyword;
                }
            }
        }

        return null;
    }
}
