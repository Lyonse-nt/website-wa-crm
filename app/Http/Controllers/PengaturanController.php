<?php

namespace App\Http\Controllers;

class PengaturanController extends Controller
{
    public function index()
    {
        $settings = [
            'phone_number_id' => config('services.whatsapp.phone_number_id'),
            'webhook_url' => url('/webhook'),
            'verify_token' => config('services.whatsapp.webhook_verify_token'),
            'api_version' => config('services.whatsapp.version'),
        ];

        return view('pengaturan.index', compact('settings'));
    }
}
