<?php

namespace App\Http\Controllers;

use App\Models\Kontak;
use Illuminate\Http\Request;

class KontakController extends Controller
{
    public function index()
    {
        $kontaks = Kontak::with('percakapanAktif')
            ->latest()
            ->paginate(20);

        return view('kontak.index', compact('kontaks'));
    }

    public function create()
    {
        return view('kontak.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nomor_whatsapp' => 'required|string|unique:kontaks,nomor_whatsapp'
        ]);

        Kontak::create($validated);

        return redirect()->route('kontak.index')
            ->with('success', 'Kontak berhasil ditambahkan');
    }

    public function show(Kontak $kontak)
    {
        $kontak->load(['percakapans.pesans' => function($query) {
            $query->latest();
        }]);

        return view('kontak.show', compact('kontak'));
    }

    public function edit(Kontak $kontak)
    {
        return view('kontak.edit', compact('kontak'));
    }

    public function update(Request $request, Kontak $kontak)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nomor_whatsapp' => 'required|string|unique:kontaks,nomor_whatsapp,' . $kontak->id
        ]);

        $kontak->update($validated);

        return redirect()->route('kontak.index')
            ->with('success', 'Kontak berhasil diupdate');
    }

    public function destroy(Kontak $kontak)
    {
        $kontak->delete();

        return redirect()->route('kontak.index')
            ->with('success', 'Kontak berhasil dihapus');
    }
}
