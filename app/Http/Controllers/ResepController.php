<?php

namespace App\Http\Controllers;

use App\Models\Resep;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ResepController extends Controller
{
    /**
     * Menampilkan semua resep
     */
    public function index()
    {
        $reseps = Resep::all();
        return Inertia::render('resep/index', [
            'reseps' => $reseps
        ]);
    }

    /**
     * Form tambah resep
     */
    public function create()
    {
        return Inertia::render('resep/create');
    }

    /**
     * Simpan resep baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'obat_resep' => 'required|string|max:255',
            'jumlah' => 'required|integer|min:1',
            'pemakaian_perhari' => 'required|string|max:255',
        ]);

        Resep::create($validated);

        return redirect()->route('resep.index')->with('success', 'Resep berhasil ditambahkan!');
    }

    /**
     * Form edit resep
     */
    public function edit($id)
    {
        $resep = Resep::findOrFail($id);
        return Inertia::render('resep/edit', [
            'resep' => $resep
        ]);
    }

    /**
     * Update resep
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'obat_resep' => 'required|string|max:255',
            'jumlah' => 'required|integer|min:1',
            'pemakaian_perhari' => 'required|string|max:255',
        ]);

        $resep = Resep::findOrFail($id);
        $resep->update($validated);

        return redirect()->route('resep.index')->with('success', 'Resep berhasil diperbarui!');
    }

    /**
     * Hapus resep
     */
    public function destroy($id)
    {
        $resep = Resep::findOrFail($id);
        $resep->delete();

        return redirect()->route('resep.index')->with('success', 'Resep berhasil dihapus!');
    }
}
