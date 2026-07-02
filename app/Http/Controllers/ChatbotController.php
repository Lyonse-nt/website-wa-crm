<?php

namespace App\Http\Controllers;

use App\Models\ChatbotSetting;
use App\Models\ChatbotMenu;
use App\Models\ChatbotKeyword;
use Illuminate\Http\Request;

class ChatbotController extends Controller
{
    public function index()
    {
        $welcomeMessage = ChatbotSetting::getSetting('welcome_message', '');
        $defaultReply = ChatbotSetting::getSetting('default_reply', '');
        $chatbotEnabled = ChatbotSetting::getSetting('chatbot_enabled', 'true') === 'true';
        $replyAllMessages = ChatbotSetting::getSetting('reply_all_messages', 'true') === 'true';
        
        $menus = ChatbotMenu::orderBy('order')->orderBy('menu_number')->get();
        $keywords = ChatbotKeyword::orderByRaw("CASE priority WHEN 'high' THEN 1 WHEN 'medium' THEN 2 WHEN 'low' THEN 3 END")->get();

        return view('chatbot.index', compact(
            'welcomeMessage',
            'defaultReply',
            'chatbotEnabled',
            'replyAllMessages',
            'menus',
            'keywords'
        ));
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'welcome_message' => 'nullable|string',
            'default_reply' => 'nullable|string',
            'chatbot_enabled' => 'nullable|boolean',
            'reply_all_messages' => 'nullable|boolean',
        ]);

        ChatbotSetting::setSetting('welcome_message', $request->welcome_message ?? '');
        ChatbotSetting::setSetting('default_reply', $request->default_reply ?? '');
        ChatbotSetting::setSetting('chatbot_enabled', $request->chatbot_enabled ? 'true' : 'false');
        ChatbotSetting::setSetting('reply_all_messages', $request->reply_all_messages ? 'true' : 'false');

        return redirect()->route('chatbot.index')->with('success', 'Pengaturan berhasil disimpan');
    }

    public function storeMenu(Request $request)
    {
        $request->validate([
            'menu_number' => 'required|integer',
            'menu_label' => 'required|string|max:255',
            'reply_message' => 'required|string',
        ]);

        ChatbotMenu::create($request->all());

        return redirect()->route('chatbot.index')->with('success', 'Menu berhasil ditambahkan');
    }

    public function updateMenu(Request $request, ChatbotMenu $menu)
    {
        $request->validate([
            'menu_number' => 'required|integer',
            'menu_label' => 'required|string|max:255',
            'reply_message' => 'required|string',
            'is_active' => 'nullable|boolean',
        ]);

        $menu->update($request->all());

        return redirect()->route('chatbot.index')->with('success', 'Menu berhasil diupdate');
    }

    public function deleteMenu(ChatbotMenu $menu)
    {
        $menu->delete();
        return redirect()->route('chatbot.index')->with('success', 'Menu berhasil dihapus');
    }

    public function storeKeyword(Request $request)
    {
        $request->validate([
            'keywords' => 'required|string',
            'reply_message' => 'required|string',
            'priority' => 'required|in:high,medium,low',
        ]);

        ChatbotKeyword::create($request->all());

        return redirect()->route('chatbot.index')->with('success', 'Keyword berhasil ditambahkan');
    }

    public function updateKeyword(Request $request, ChatbotKeyword $keyword)
    {
        $request->validate([
            'keywords' => 'required|string',
            'reply_message' => 'required|string',
            'priority' => 'required|in:high,medium,low',
            'is_active' => 'nullable|boolean',
        ]);

        $keyword->update($request->all());

        return redirect()->route('chatbot.index')->with('success', 'Keyword berhasil diupdate');
    }

    public function deleteKeyword(ChatbotKeyword $keyword)
    {
        $keyword->delete();
        return redirect()->route('chatbot.index')->with('success', 'Keyword berhasil dihapus');
    }
}
