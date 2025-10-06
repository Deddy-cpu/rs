<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Tindakan;
use Illuminate\Http\Request;

class TindakanController extends Controller
{
    // Menampilkan semua data
    public function index(Request $request)
    {
        $search = $request->input('search');
        $order  = $request->input('order', 'asc'); // default asc (1,2,3,4...)

        $tindakan = Tindakan::query()
            ->when($search, function ($query, $search) {
                $query->where('id', 'like', "%{$search}%")
                    ->orWhere('id_pasien', 'like', "%{$search}%")
                    ->orWhere('dokter', 'like', "%{$search}%")
                    ->orWhere('tindakan', 'like', "%{$search}%")
                    ->orWhere('jumlah', 'like', "%{$search}%");
            })
            ->orderBy('id', $order) // bisa asc atau desc
            ->paginate(5)
            ->withQueryString();

        return Inertia::render('tindakan/index', [
            'tindakan' => $tindakan,
            'filters'  => [
                'search' => $search,
                'order'  => $order,
            ]
        ]);
    }

    public function create()
    {
        return Inertia::render('tindakan/create');
    }

    // Menyimpan data baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_pasien' => 'required|string|max:255',
            'dokter'    => 'required|string|max:255',
            'tindakan'  => 'required|string|max:255',
            'jumlah'    => 'required|integer',
        ]);

        Tindakan::create($validated);

        return redirect()->route('tindakan.index')
            ->with('success', 'Data tindakan berhasil ditambahkan');
    }

    // Menampilkan data by ID
    public function show($id)
    {
        $tindakan = Tindakan::findOrFail($id);
        return response()->json($tindakan);
    }

    public function edit($id)
    {
        $tindakan = Tindakan::findOrFail($id);
        return Inertia::render('tindakan/edit', [
            'tindakan' => $tindakan
        ]);
    }

    // Update data
    public function update(Request $request, $id)
    {
        $tindakan = Tindakan::findOrFail($id);

        $validated = $request->validate([
            'id_pasien' => 'required|string|max:255',
            'dokter'    => 'required|string|max:255',
            'tindakan'  => 'required|string|max:255',
            'jumlah'    => 'required|integer',
        ]);

        $tindakan->update($validated);

        return redirect()->route('tindakan.index')
            ->with('success', 'Data tindakan berhasil diperbarui!');
    }

    // Hapus data
    public function destroy($id)
    {
        $tindakan = Tindakan::findOrFail($id);
        $tindakan->delete();

        return redirect()->route('tindakan.index')
            ->with('success', 'Data tindakan berhasil dihapus!');
    }
}
