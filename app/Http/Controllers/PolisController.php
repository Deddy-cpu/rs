<?php

namespace App\Http\Controllers;

use App\Models\Polis;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PolisController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Polis::query();

        // Search functionality
        if ($request->has('search') && $request->search) {
            $query->where('poli_desc', 'like', '%' . $request->search . '%');
        }

        // Filter by status
        if ($request->has('aktif') && $request->aktif) {
            $query->where('aktif', $request->aktif);
        }

        $polis = $query->orderBy('created_at', 'desc')->paginate(10);

        return Inertia::render('polis/index', [
            'polis' => $polis,
            'filters' => $request->only(['search', 'aktif']),
            'flash' => [
                'success' => session('success'),
                'error' => session('error'),
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('polis/create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'poli_desc' => 'required|string|max:255',
            'aktif' => 'required|string|in:Y,N',
            'update_by' => 'nullable|string|max:255',
        ]);

        $polis = Polis::create([
            'poli_desc' => $request->poli_desc,
            'aktif' => $request->aktif,
            'update_date' => now(),
            'update_by' => $request->update_by ?? auth()->user()->name ?? 'System',
        ]);

        // Broadcast WebSocket update
        \App\Helpers\WebSocketBroadcast::poliCreated($polis);

        return redirect()->route('polis.index')->with('success', 'Polis berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Polis $poli)
    {
        return Inertia::render('polis/show', [
            'polis' => $poli,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Polis $poli)
    {
        return Inertia::render('polis/edit', [
            'polis' => $poli,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Polis $poli)
    {
        $request->validate([
            'poli_desc' => 'required|string|max:255',
            'aktif' => 'required|string|in:Y,N',
            'update_by' => 'nullable|string|max:255',
        ]);

        $poli->update([
            'poli_desc' => $request->poli_desc,
            'aktif' => $request->aktif,
            'update_date' => now(),
            'update_by' => $request->update_by ?? auth()->user()->name ?? 'System',
        ]);

        // Broadcast WebSocket update
        \App\Helpers\WebSocketBroadcast::poliUpdated($poli);

        return redirect()->route('polis.index')->with('success', 'Polis berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Polis $poli)
    {
        $poliId = $poli->id;
        $poli->delete();

        // Broadcast WebSocket update
        \App\Helpers\WebSocketBroadcast::poliDeleted($poliId);

        return redirect()->route('polis.index')->with('success', 'Polis berhasil dihapus.');
    }
}
