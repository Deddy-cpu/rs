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
<<<<<<< HEAD
    public function edit($id)
    {
        try {
            // Find polis by ID
            $polis = Polis::findOrFail($id);
            
            // Verify polis exists and has ID
            if (!$polis || !$polis->id) {
                return redirect()->route('polis.index')
                    ->with('error', 'Polis tidak ditemukan.');
            }

            return Inertia::render('polis/edit', [
                'polis' => [
                    'id' => $polis->id,
                    'poli_desc' => $polis->poli_desc,
                    'aktif' => $polis->aktif,
                    'update_date' => $polis->update_date,
                    'update_by' => $polis->update_by,
                    'created_at' => $polis->created_at,
                    'updated_at' => $polis->updated_at,
                ],
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('polis.index')
                ->with('error', 'Polis tidak ditemukan.');
        } catch (\Exception $e) {
            return redirect()->route('polis.index')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
=======
    public function edit(Polis $poli)
    {
        return Inertia::render('polis/edit', [
            'polis' => $poli,
        ]);
>>>>>>> be1d14e9aa3d61495cd14a9a6dde029795e626e6
    }

    /**
     * Update the specified resource in storage.
     */
<<<<<<< HEAD
    public function update(Request $request, $id)
=======
    public function update(Request $request, Polis $poli)
>>>>>>> be1d14e9aa3d61495cd14a9a6dde029795e626e6
    {
        try {
            // Find polis by ID
            $polis = Polis::findOrFail($id);
            
            // Verify polis exists
            if (!$polis || !$polis->id) {
                return redirect()->route('polis.index')
                    ->with('error', 'Polis tidak ditemukan.');
            }

<<<<<<< HEAD
            $request->validate([
                'poli_desc' => 'required|string|max:255',
                'aktif' => 'required|string|in:Y,N',
                'update_by' => 'nullable|string|max:255',
            ]);
=======
        $poli->update([
            'poli_desc' => $request->poli_desc,
            'aktif' => $request->aktif,
            'update_date' => now(),
            'update_by' => $request->update_by ?? auth()->user()->name ?? 'System',
        ]);
>>>>>>> be1d14e9aa3d61495cd14a9a6dde029795e626e6

            $polis->update([
                'poli_desc' => $request->poli_desc,
                'aktif' => $request->aktif,
                'update_date' => now(),
                'update_by' => $request->update_by ?? auth()->user()->name ?? 'System',
            ]);

            // Broadcast WebSocket update
            \App\Helpers\WebSocketBroadcast::poliUpdated($polis);

            return redirect()->route('polis.index')->with('success', 'Polis berhasil diperbarui.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('polis.index')
                ->with('error', 'Polis tidak ditemukan.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Gagal memperbarui polis: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Polis $poli)
    {
<<<<<<< HEAD
        $poliId = $polis->id;
        $polis->delete();
=======
        $poli->delete();
>>>>>>> be1d14e9aa3d61495cd14a9a6dde029795e626e6

        // Broadcast WebSocket update
        \App\Helpers\WebSocketBroadcast::poliDeleted($poliId);

        return redirect()->route('polis.index')->with('success', 'Polis berhasil dihapus.');
    }
}
