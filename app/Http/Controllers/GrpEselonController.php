<?php

namespace App\Http\Controllers;

use App\Models\GrpEselon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class GrpEselonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $order = $request->input('order', 'asc');

        $grpEselons = GrpEselon::query()
            ->when($search, function ($query, $search) {
                $query->where('eselon_desc', 'like', "%{$search}%")
                      ->orWhere('updated_by', 'like', "%{$search}%");
            })
            ->orderBy('eselon_desc', $order)
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('dokter/mastering/grp_eselon/index', [
            'grpEselons' => $grpEselons,
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
        return Inertia::render('dokter/mastering/grp_eselon/create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'eselon_desc' => 'required|string|max:255',
            'update_date' => 'required|date',
            'updated_by' => 'nullable|string|max:255',
        ]);

        GrpEselon::create($validated);

        return redirect()->route('grp-eselon.index')
            ->with('success', 'Data GRP Eselon berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(GrpEselon $grpEselon)
    {
        return Inertia::render('dokter/mastering/grp_eselon/show', [
            'grpEselon' => $grpEselon
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(GrpEselon $grpEselon)
    {
        return Inertia::render('dokter/mastering/grp_eselon/edit', [
            'grpEselon' => $grpEselon
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, GrpEselon $grpEselon)
    {
        $validated = $request->validate([
            'eselon_desc' => 'required|string|max:255',
            'update_date' => 'required|date',
            'updated_by' => 'nullable|string|max:255',
        ]);

        $grpEselon->update($validated);

        return redirect()->route('grp-eselon.index')
            ->with('success', 'Data GRP Eselon berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GrpEselon $grpEselon)
    {
        $grpEselon->delete();

        return redirect()->route('grp-eselon.index')
            ->with('success', 'Data GRP Eselon berhasil dihapus');
    }
}
