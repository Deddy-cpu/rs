<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Psn;
use App\Models\Pasien;
use Inertia\Inertia;
class PsnController extends Controller
{
    // Menampilkan semua data psn
    public function index(Request $request)
    {
        $psns = Psn::all();
        
        // Check if this is an API request
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json($psns);
        }
        
        // Otherwise render Inertia view for web routes
        return Inertia::render('pasien/index', [
            'psns' => $psns,
        ]);
    }


    // Menampilkan form create pasien
    public function create()
    {
        return Inertia::render('pasien/create');
    }

    // Menyimpan data psn baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nm_p'      => 'required|string',
            'nik'       => 'required|integer',
            'no_bpjs'   => 'required|integer',
            'agm'       => 'required|string',
            'tgl_lahir' => 'required|string',
            'kelamin'   => 'required|in:L,P,kosong',
            'almt_L'    => 'required|string',
            'almt_B'    => 'required|string',
        ]);

        $psn = Psn::create($validated);

        return response()->json($psn, 201);
    }

    // Menampilkan detail psn berdasarkan id
    public function show(Request $request, $id)
    {
        $psn = Psn::find($id);
        if (!$psn) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['message' => 'Data tidak ditemukan'], 404);
            }
            return redirect()->route('pasien.index')->with('error', 'Data tidak ditemukan');
        }
        
        // Load related data for comprehensive patient view
        $pasienData = Pasien::with([
            'konsuls',
            'tindaks', 
            'alkes',
            'rsp',
            'lainnyas',
            'transaksi.detail'
        ])->where('psn_id', $id)->get();
        
        // Check if this is an API request
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'psn' => $psn,
                'pasienData' => $pasienData
            ]);
        }
        
        // Render Inertia view for web routes
        return Inertia::render('pasien/show', [
            'psn' => $psn,
            'pasienData' => $pasienData,
            'flash' => [
                'success' => session('success'),
                'error' => session('error')
            ]
        ]);
    }

    // Menampilkan form edit pasien
    public function edit($id)
    {
        $psn = Psn::findOrFail($id);
        return Inertia::render('pasien/edit', [
            'psn' => $psn
        ]);
    }

    // Mengupdate data psn
    public function update(Request $request, $id)
    {
        $psn = Psn::find($id);
        if (!$psn) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        $validated = $request->validate([
            'nm_p'      => 'sometimes|required|string',
            'nik'       => 'sometimes|required|integer',
            'no_bpjs'   => 'sometimes|required|integer',
            'agm'       => 'sometimes|required|string',
            'tgl_lahir' => 'sometimes|required|string',
            'kelamin'   => 'sometimes|required|in:L,P,kosong',
            'almt_L'    => 'sometimes|required|string',
            'almt_B'    => 'sometimes|required|string',
        ]);

        $psn->update($validated);

        return response()->json($psn);
    }

    // Menghapus data psn
    public function destroy($id)
    {
        $psn = Psn::find($id);
        if (!$psn) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }
        $psn->delete();
        return response()->json(['message' => 'Data berhasil dihapus']);
    }

    // Menampilkan form create kunjungan
    public function createKunjungan($id)
    {
        $psn = Psn::findOrFail($id);
        return Inertia::render('pasien/kunjungan/create', [
            'psn' => $psn
        ]);
    }

    // Menyimpan data kunjungan baru
    public function storeKunjungan(Request $request)
    {
        $validated = $request->validate([
            'psn_id' => 'required|exists:psns,id',
            'no_reg' => 'required|string|max:255',
            'tgl_reg' => 'required|date',
            'nm_p' => 'required|string|max:255',
            'mrn' => 'required|string|max:255',
            'almt_B' => 'required|string|max:255',
            'no_inv' => 'nullable|string|max:255',
            'tgl_inv' => 'nullable|date',
            'perawatan' => 'required|string|max:255',
            'penjamin' => 'required|string|max:255',
            'no_sjp' => 'nullable|string|max:255',
            'icd' => 'nullable|string|max:255',
            'kunjungan' => 'required|in:umum,gigi,kia,laboratorium,apotek',
        ]);

        try {
            $pasien = Pasien::create($validated);
            
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'Kunjungan berhasil ditambahkan',
                    'data' => $pasien
                ], 201);
            }
            
            return redirect()->route('pasien.show', $validated['psn_id'])
                ->with('success', 'Kunjungan berhasil ditambahkan');
                
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'Gagal menambahkan kunjungan',
                    'error' => $e->getMessage()
                ], 500);
            }
            
            return back()->withErrors(['error' => 'Gagal menambahkan kunjungan: ' . $e->getMessage()]);
        }
    }
}
