<?php

namespace App\Http\Controllers;

use App\Models\TindakanQ;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TindakanQController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $order = $request->input('order', 'asc');

        $tindakanQs = TindakanQ::query()
            ->when($search, function ($query, $search) {
                $query->where('tindakan_q_desc', 'like', "%{$search}%")
                      ->orWhere('update_by', 'like', "%{$search}%");
            })
            ->orderBy('tindakan_q_desc', $order)
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('dokter/mastering/tindakanq/index', [
            'tindakanQs' => $tindakanQs,
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
        return Inertia::render('dokter/mastering/tindakanq/create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tindakan_q_desc' => 'required|string|max:255',
            'aktif' => 'required|string|in:Y,N',
            'update_date' => 'required|date',
            'update_by' => 'nullable|string|max:255',
        ]);

        TindakanQ::create($validated);

        return redirect()->route('tindakanq.index')
            ->with('success', 'Data Tindakan Q berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(TindakanQ $tindakanq)
    {
        return Inertia::render('dokter/mastering/tindakanq/show', [
            'tindakanQ' => $tindakanq
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TindakanQ $tindakanq)
    {
        return Inertia::render('dokter/mastering/tindakanq/edit', [
            'tindakanQ' => $tindakanq
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TindakanQ $tindakanq)
    {
        $validated = $request->validate([
            'tindakan_q_desc' => 'required|string|max:255',
            'aktif' => 'required|string|in:Y,N',
            'update_date' => 'required|date',
            'update_by' => 'nullable|string|max:255',
        ]);

        $tindakanq->update($validated);

        return redirect()->route('tindakanq.index')
            ->with('success', 'Data Tindakan Q berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TindakanQ $tindakanq)
    {
        $tindakanq->delete();

        return redirect()->route('tindakanq.index')
            ->with('success', 'Data Tindakan Q berhasil dihapus');
    }
}
