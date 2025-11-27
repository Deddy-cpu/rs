<?php

namespace App\Http\Controllers;

use App\Models\GrpEselon;
use App\Models\Eselon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
                $query->where('grp_eselon_desc', 'like', "%{$search}%")
                      ->orWhere('update_by', 'like', "%{$search}%");
            })
            ->orderBy('grp_eselon_desc', $order)
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
            'grp_eselon_desc' => 'required|string|max:255',
            'aktif' => 'required|string|in:Y,N',
            'update_by' => 'nullable|string|max:255',
        ]);

        // Automatically set update_by to authenticated user's name
        $validated['update_by'] = auth()->user()->name ?? 'System';
        $validated['update_date'] = now();

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
            'grp_eselon_desc' => 'required|string|max:255',
            'aktif' => 'required|string|in:Y,N',
            'update_by' => 'nullable|string|max:255',
        ]);

        // Automatically set update_by to authenticated user's name
        $validated['update_by'] = auth()->user()->name ?? 'System';
        $validated['update_date'] = now();

        // Check status changes
        $wasActive = $grpEselon->aktif === 'Y';
        $wasInactive = $grpEselon->aktif === 'N';
        $willBeActive = $validated['aktif'] === 'Y';
        $willBeInactive = $validated['aktif'] === 'N';

        DB::beginTransaction();
        try {
            $grpEselon->update($validated);

            $message = 'Data GRP Eselon berhasil diperbarui';
            $affectedCount = 0;

            // If grup eselon is being deactivated, deactivate all related eselon
            if ($wasActive && $willBeInactive) {
                $affectedCount = Eselon::where('grp_eselon_id', $grpEselon->id)
                    ->where('aktif', 'Y')
                    ->update([
                        'aktif' => 'N',
                        'update_date' => now(),
                        'update_by' => $validated['update_by'] ?? auth()->user()->name ?? 'System'
                    ]);

                if ($affectedCount > 0) {
                    $message .= " dan {$affectedCount} eselon terkait telah dinonaktifkan secara otomatis.";
                }
            }
            // If grup eselon is being activated (from inactive to active), activate all related eselon
            elseif ($wasInactive && $willBeActive) {
                $affectedCount = Eselon::where('grp_eselon_id', $grpEselon->id)
                    ->where('aktif', 'N')
                    ->update([
                        'aktif' => 'Y',
                        'update_date' => now(),
                        'update_by' => $validated['update_by'] ?? auth()->user()->name ?? 'System'
                    ]);

                if ($affectedCount > 0) {
                    $message .= " dan {$affectedCount} eselon terkait telah diaktifkan secara otomatis.";
                }
            }

            DB::commit();

            return redirect()->route('grp-eselon.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['error' => 'Gagal memperbarui data: ' . $e->getMessage()]);
        }
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
