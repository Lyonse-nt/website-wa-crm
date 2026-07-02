<?php

namespace App\Http\Controllers;

use App\Models\Kontak;
use App\Models\Percakapan;
use App\Models\Pesan;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;

class WhatsAppController extends Controller
{
    protected $whatsappService;

    public function __construct(WhatsAppService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

    public function index()
    {
        $kontaks = Kontak::all();
        return view('pesan.index', compact('kontaks'));
    }

    public function send(Request $request)
    {
        $validated = $request->validate([
            'nomor_whatsapp' => 'required|string',
            'pesan' => 'required|string'
        ]);

        $kontak = Kontak::firstOrCreate(
            ['nomor_whatsapp' => $validated['nomor_whatsapp']],
            ['nama' => $validated['nomor_whatsapp']]
        );

        $percakapan = Percakapan::firstOrCreate(
            ['kontak_id' => $kontak->id],
            [
                'pesan_terakhir' => $validated['pesan'],
                'waktu_pesan_terakhir' => now()
            ]
        );

        $result = $this->whatsappService->sendText(
            $validated['nomor_whatsapp'],
            $validated['pesan']
        );

        $pesan = Pesan::create([
            'percakapan_id' => $percakapan->id,
            'arah_pesan' => 'keluar',
            'jenis_pesan' => 'text',
            'isi_pesan' => $validated['pesan'],
            'whatsapp_message_id' => $result['data']['messages'][0]['id'] ?? null,
            'status' => $result['success'] ? 'sent' : 'failed',
            'raw_response' => $result['data']
        ]);

        $percakapan->update([
            'pesan_terakhir' => $validated['pesan'],
            'waktu_pesan_terakhir' => now()
        ]);

        if ($result['success']) {
            return redirect()->back()->with('success', 'Pesan berhasil dikirim');
        }

        return redirect()->back()->with('error', 'Pesan gagal dikirim');
    }
}
