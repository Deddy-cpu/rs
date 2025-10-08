<?php

namespace App\Http\Controllers;

use App\Models\TindakanTarif;
use App\Models\TindakanQ;
use App\Models\GrpEselon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TindakanTarifController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $order = $request->input('order', 'asc');

        $tindakanTarifs = TindakanTarif::with(['tindakanQ', 'grpEselon'])
            ->when($search, function ($query, $search) {
                $query->whereHas('tindakanQ', function ($q) use ($search) {
                    $q->where('tindakan_q_desc', 'like', "%{$search}%");
                })
                ->orWhereHas('grpEselon', function ($q) use ($search) {
                    $q->where('eselon_desc', 'like', "%{$search}%");
                })
                ->orWhere('tarif', 'like', "%{$search}%")
                ->orWhere('update_by', 'like', "%{$search}%");
            })
            ->orderBy('tarif', $order)
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('dokter/mastering/tindakan_tarif/index', [
            'tindakanTarifs' => $tindakanTarifs,
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
        $tindakanQs = TindakanQ::where('aktif', 'Y')->get();
        $grpEselons = GrpEselon::all();

        return Inertia::render('dokter/mastering/tindakan_tarif/create', [
            'tindakanQs' => $tindakanQs,
            'grpEselons' => $grpEselons,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tindakan_q_id' => 'required|exists:tindakan_q,id',
            'eselon_grp_id' => 'required|exists:grp_eselon,id',
            'tarif' => 'required|numeric|min:0',
            'aktif' => 'required|string|in:Y,N',
            'update_date' => 'required|date',
            'update_by' => 'nullable|string|max:255',
        ]);

        TindakanTarif::create($validated);

        return redirect()->route('tindakan-tarif.index')
            ->with('success', 'Data Tindakan Tarif berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(TindakanTarif $tindakanTarif)
    {
        $tindakanTarif->load(['tindakanQ', 'grpEselon']);
        
        return Inertia::render('dokter/mastering/tindakan_tarif/show', [
            'tindakanTarif' => $tindakanTarif
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TindakanTarif $tindakanTarif)
    {
        $tindakanQs = TindakanQ::where('aktif', 'Y')->get();
        $grpEselons = GrpEselon::all();

        return Inertia::render('dokter/mastering/tindakan_tarif/edit', [
            'tindakanTarif' => $tindakanTarif,
            'tindakanQs' => $tindakanQs,
            'grpEselons' => $grpEselons,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TindakanTarif $tindakanTarif)
    {
        $validated = $request->validate([
            'tindakan_q_id' => 'required|exists:tindakan_q,id',
            'eselon_grp_id' => 'required|exists:grp_eselon,id',
            'tarif' => 'required|numeric|min:0',
            'aktif' => 'required|string|in:Y,N',
            'update_date' => 'required|date',
            'update_by' => 'nullable|string|max:255',
        ]);

        $tindakanTarif->update($validated);

        return redirect()->route('tindakan-tarif.index')
            ->with('success', 'Data Tindakan Tarif berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TindakanTarif $tindakanTarif)
    {
        $tindakanTarif->delete();

        return redirect()->route('tindakan-tarif.index')
            ->with('success', 'Data Tindakan Tarif berhasil dihapus');
    }
}
