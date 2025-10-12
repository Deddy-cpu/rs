<?php

namespace App\Http\Controllers;

use App\Models\Farmalkes;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class FarmalkesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $order = $request->input('order', 'asc');

        $farmalkes = Farmalkes::when($search, function ($query, $search) {
                $query->where('kode', 'like', "%{$search}%")
                      ->orWhere('nama_item', 'like', "%{$search}%")
                      ->orWhere('deskripsi', 'like', "%{$search}%")
                      ->orWhere('kategori', 'like', "%{$search}%")
                      ->orWhere('jenis', 'like', "%{$search}%");
            })
            ->orderBy('nama_item', $order)
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('dokter/mastering/farmalkes/index', [
            'farmalkes' => $farmalkes,
            'filters' => [
                'search' => $search,
                'order' => $order,
            ]
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('dokter/mastering/farmalkes/create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'kode' => 'required|string|max:50|unique:farmalkes,kode',
            'nama_item' => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:500',
            'jenis' => 'required|in:farmasi,alkes',
            'kategori' => 'required|string|max:100',
            'satuan' => 'required|string|max:50',
            'harga' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'aktif' => 'required|in:Y,N',
        ]);

        Farmalkes::create([
            'kode' => $request->kode,
            'nama_item' => $request->nama_item,
            'deskripsi' => $request->deskripsi,
            'jenis' => $request->jenis,
            'kategori' => $request->kategori,
            'satuan' => $request->satuan,
            'harga' => $request->harga,
            'stok' => $request->stok,
            'aktif' => $request->aktif,
            'update_by' => Auth::user()->name ?? 'System',
        ]);

        return redirect()->route('farmalkes.index')
            ->with('success', 'Data Farmalkes berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Farmalkes $farmalke)
    {
        return Inertia::render('dokter/mastering/farmalkes/show', [
            'farmalkes' => $farmalke
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Farmalkes $farmalke)
    {
        return Inertia::render('dokter/mastering/farmalkes/edit', [
            'farmalkes' => $farmalke
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Farmalkes $farmalke)
    {
        // Debug: Log the incoming request data
        \Log::info('Farmalkes Update Request:', [
            'farmalkes_id' => $farmalke->id,
            'request_data' => $request->all(),
            'user' => Auth::user()->name ?? 'Unknown'
        ]);

        $request->validate([
            'kode' => 'required|string|max:50|unique:farmalkes,kode,' . $farmalke->id,
            'nama_item' => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:500',
            'jenis' => 'required|in:farmasi,alkes',
            'kategori' => 'required|string|max:100',
            'satuan' => 'required|string|max:50',
            'harga' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'aktif' => 'required|in:Y,N',
        ]);

        $farmalke->update([
            'kode' => $request->kode,
            'nama_item' => $request->nama_item,
            'deskripsi' => $request->deskripsi,
            'jenis' => $request->jenis,
            'kategori' => $request->kategori,
            'satuan' => $request->satuan,
            'harga' => $request->harga,
            'stok' => $request->stok,
            'aktif' => $request->aktif,
            'update_by' => Auth::user()->name ?? 'System',
        ]);

        \Log::info('Farmalkes Update Successful:', [
            'farmalkes_id' => $farmalke->id,
            'updated_data' => $farmalke->fresh()->toArray()
        ]);

        return redirect()->route('farmalkes.index')
            ->with('success', 'Data Farmalkes berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Farmalkes $farmalke)
    {
        $farmalke->delete();

        return redirect()->route('farmalkes.index')
            ->with('success', 'Data Farmalkes berhasil dihapus.');
    }
}
