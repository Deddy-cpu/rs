<?php

namespace App\Http\Controllers;

use App\Models\Eselon;
use App\Models\GrpEselon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EselonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $order = $request->input('order', 'asc');

        $eselons = Eselon::with('grpEselon')
            ->when($search, function ($query, $search) {
                $query->where('eselon_desc', 'like', "%{$search}%")
                      ->orWhere('update_by', 'like', "%{$search}%")
                      ->orWhereHas('grpEselon', function ($q) use ($search) {
                          $q->where('eselon_desc', 'like', "%{$search}%");
                      });
            })
            ->orderBy('eselon_desc', $order)
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('dokter/mastering/eselon/index', [
            'eselons' => $eselons,
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
        $grpEselons = GrpEselon::orderBy('eselon_desc')->get();
        
        return Inertia::render('dokter/mastering/eselon/create', [
            'grpEselons' => $grpEselons
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'grp_eselon_id' => 'required|exists:grp_eselon,id',
            'eselon_desc' => 'required|string|max:255',
            'aktif' => 'required|string|in:Y,N',
            'update_date' => 'required|date',
            'update_by' => 'nullable|string|max:255',
        ]);

        Eselon::create($validated);

        return redirect()->route('eselon.index')
            ->with('success', 'Data Eselon berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(Eselon $eselon)
    {
        $eselon->load('grpEselon');
        
        return Inertia::render('dokter/mastering/eselon/show', [
            'eselon' => $eselon
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Eselon $eselon)
    {
        $grpEselons = GrpEselon::orderBy('eselon_desc')->get();
        
        return Inertia::render('dokter/mastering/eselon/edit', [
            'eselon' => $eselon,
            'grpEselons' => $grpEselons
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Eselon $eselon)
    {
        $validated = $request->validate([
            'grp_eselon_id' => 'required|exists:grp_eselon,id',
            'eselon_desc' => 'required|string|max:255',
            'aktif' => 'required|string|in:Y,N',
            'update_date' => 'required|date',
            'update_by' => 'nullable|string|max:255',
        ]);

        $eselon->update($validated);

        return redirect()->route('eselon.index')
            ->with('success', 'Data Eselon berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Eselon $eselon)
    {
        $eselon->delete();

        return redirect()->route('eselon.index')
            ->with('success', 'Data Eselon berhasil dihapus');
    }
}
