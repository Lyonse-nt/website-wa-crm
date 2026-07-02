<?php

namespace App\Http\Controllers;

use App\Models\Percakapan;

class PercakapanController extends Controller
{
    public function index()
    {
        $percakapans = Percakapan::with('kontak')
            ->orderBy('waktu_pesan_terakhir', 'desc')
            ->get();

        $selectedPercakapan = $percakapans->first();
        
        if ($selectedPercakapan) {
            $selectedPercakapan->load(['pesans' => function($query) {
                $query->orderBy('created_at', 'asc');
            }]);
        }

        return view('percakapan.index', compact('percakapans', 'selectedPercakapan'));
    }

    public function show(Percakapan $percakapan)
    {
        $percakapans = Percakapan::with('kontak')
            ->orderBy('waktu_pesan_terakhir', 'desc')
            ->get();

        $percakapan->load(['pesans' => function($query) {
            $query->orderBy('created_at', 'asc');
        }, 'kontak']);

        $selectedPercakapan = $percakapan;

        return view('percakapan.index', compact('percakapans', 'selectedPercakapan'));
    }
}
