<?php

namespace App\Http\Controllers;

use App\Models\Kontak;
use App\Models\Percakapan;
use App\Models\Pesan;
use App\Models\LogApi;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_kontak' => Kontak::count(),
            'total_percakapan' => Percakapan::count(),
            'pesan_masuk' => Pesan::where('arah_pesan', 'masuk')->count(),
            'pesan_keluar' => Pesan::where('arah_pesan', 'keluar')->count(),
            'total_request_api' => LogApi::count(),
            'pesan_gagal' => Pesan::where('status', 'failed')->count(),
        ];

        $recent_messages = Pesan::with('percakapan.kontak')
            ->latest()
            ->take(5)
            ->get();

        $recent_contacts = Kontak::withCount('percakapans')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact('stats', 'recent_messages', 'recent_contacts'));
    }
}
