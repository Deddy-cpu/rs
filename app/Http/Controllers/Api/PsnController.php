<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Psn;
use App\Models\Pasien;
use Illuminate\Support\Facades\DB;
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
            'transaksi.detailTransaksi.konsuls',
            'transaksi.detailTransaksi.tindaks',
            'transaksi.detailTransaksi.alkes',
            'transaksi.detailTransaksi.rsp',
            'transaksi.detailTransaksi.lainnyas'
        ])->where('psn_id', $id)->get();

        // Transform the data to be compatible with frontend expectations
        $transformedPasienData = $pasienData->map(function ($pasien) {
            // Flatten the nested structure for frontend compatibility
            $pasien->konsuls = collect();
            $pasien->tindaks = collect();
            $pasien->alkes = collect();
            $pasien->rsp = collect();
            $pasien->lainnyas = collect();

            foreach ($pasien->transaksi as $transaksi) {
                foreach ($transaksi->detailTransaksi as $detailTransaksi) {
                    $pasien->konsuls = $pasien->konsuls->merge($detailTransaksi->konsuls);
                    $pasien->tindaks = $pasien->tindaks->merge($detailTransaksi->tindaks);
                    $pasien->alkes = $pasien->alkes->merge($detailTransaksi->alkes);
                    $pasien->rsp = $pasien->rsp->merge($detailTransaksi->rsp);
                    $pasien->lainnyas = $pasien->lainnyas->merge($detailTransaksi->lainnyas);
                }
            }

            return $pasien;
        });
        
        // Check if this is an API request
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'psn' => $psn,
                'pasienData' => $transformedPasienData
            ]);
        }
        
        // Render Inertia view for web routes
        return Inertia::render('pasien/show', [
            'psn' => $psn,
            'pasienData' => $transformedPasienData,
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

    // Menampilkan form edit kunjungan
    public function editKunjungan($psnId, $kunjunganId)
    {
        $psn = Psn::findOrFail($psnId);
        $kunjungan = Pasien::findOrFail($kunjunganId);
        
        return Inertia::render('pasien/kunjungan/edit', [
            'psn' => $psn,
            'kunjungan' => $kunjungan
        ]);
    }

    // Mengupdate data kunjungan
    public function updateKunjungan(Request $request, $psnId, $kunjunganId)
    {
        $validated = $request->validate([
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
            $kunjungan = Pasien::findOrFail($kunjunganId);
            $kunjungan->update($validated);
            
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'Kunjungan berhasil diupdate',
                    'data' => $kunjungan
                ], 200);
            }
            
            return redirect()->route('pasien.show', $psnId)
                ->with('success', 'Kunjungan berhasil diupdate');
                
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'Gagal mengupdate kunjungan',
                    'error' => $e->getMessage()
                ], 500);
            }
            
            return back()->withErrors(['error' => 'Gagal mengupdate kunjungan: ' . $e->getMessage()]);
        }
    }

    // Menampilkan form tambah kunjungan dengan transaksi
    public function createKunjunganWithTransaction($psnId = null)
    {
        $psn = null;
        $existingPasien = null;
        
        if ($psnId) {
            $psn = Psn::findOrFail($psnId);
            
            // Check if there's already a visit for this patient
            $existingPasien = Pasien::where('psn_id', $psnId)->first();
        }
        
        return Inertia::render('dokter/pasien_kunjungan/detail_transaksi', [
            'psn' => $psn,
            'existingPasien' => $existingPasien
        ]);
    }

    // Menyimpan kunjungan dengan transaksi lengkap
    public function storeKunjunganWithTransaction(Request $request)
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
            
            // Transaction data
            'konsul' => 'nullable|array',
            'konsul.*.dokter' => 'nullable|string|max:255',
            'konsul.*.dskp_kons' => 'nullable|string|max:255',
            'konsul.*.jmlh_kons' => 'nullable|numeric',
            'konsul.*.bya_kons' => 'nullable|numeric',
            'konsul.*.disc_kons' => 'nullable|string|max:255',
            'konsul.*.st_kons' => 'nullable|numeric',
            'konsul.*.tanggal' => 'nullable|date',
            
            'tindak' => 'nullable|array',
            'tindak.*.dktr_tindak' => 'nullable|string|max:255',
            'tindak.*.dskp_tindak' => 'nullable|string|max:255',
            'tindak.*.jmlh_tindak' => 'nullable|numeric',
            'tindak.*.bya_tindak' => 'nullable|numeric',
            'tindak.*.disc_tindak' => 'nullable|string|max:255',
            'tindak.*.st_tindak' => 'nullable|numeric',
            'tindak.*.tanggal' => 'nullable|date',
            
            'alkes' => 'nullable|array',
            'alkes.*.poli' => 'nullable|string|max:255',
            'alkes.*.dskp_alkes' => 'nullable|string|max:255',
            'alkes.*.jmlh_alkes' => 'nullable|numeric',
            'alkes.*.bya_alkes' => 'nullable|numeric',
            'alkes.*.disc_alkes' => 'nullable|string|max:255',
            'alkes.*.st_alkes' => 'nullable|numeric',
            'alkes.*.tanggal' => 'nullable|date',
            
            'rsp' => 'nullable|array',
            'rsp.*.dskp_rsp' => 'nullable|string|max:255',
            'rsp.*.jmlh_rsp' => 'nullable|numeric',
            'rsp.*.bya_rsp' => 'nullable|numeric',
            'rsp.*.disc_rsp' => 'nullable|string|max:255',
            'rsp.*.st_rsp' => 'nullable|numeric',
            'rsp.*.tanggal' => 'nullable|date',
            
            'lainnya' => 'nullable|array',
            'lainnya.*.dskp_lainnya' => 'nullable|string|max:255',
            'lainnya.*.jmlh_lainnaya' => 'nullable|numeric',
            'lainnya.*.bya_lainnya' => 'nullable|numeric',
            'lainnya.*.disc_lainnya' => 'nullable|string|max:255',
            'lainnya.*.st_lainnya' => 'nullable|numeric',
            'lainnya.*.tanggal' => 'nullable|date',
        ]);

        try {
            DB::beginTransaction();

            // Find existing patient visit or create new one
            $pasien = Pasien::where('psn_id', $validated['psn_id'])->first();
            
            if (!$pasien) {
                // Create new patient visit if doesn't exist
                $pasien = Pasien::create([
                    'psn_id' => $validated['psn_id'],
                    'no_reg' => $validated['no_reg'],
                    'tgl_reg' => $validated['tgl_reg'],
                    'nm_p' => $validated['nm_p'],
                    'mrn' => $validated['mrn'],
                    'almt_B' => $validated['almt_B'],
                    'no_inv' => !empty($validated['no_inv']) ? $validated['no_inv'] : null,
                    'tgl_inv' => !empty($validated['tgl_inv']) ? $validated['tgl_inv'] : null,
                    'perawatan' => $validated['perawatan'],
                    'penjamin' => $validated['penjamin'],
                    'no_sjp' => !empty($validated['no_sjp']) ? $validated['no_sjp'] : null,
                    'icd' => !empty($validated['icd']) ? $validated['icd'] : null,
                    'kunjungan' => $validated['kunjungan'],
                ]);
            } else {
                // Update existing patient visit with new data if provided
                $pasien->update([
                    'no_reg' => $validated['no_reg'],
                    'tgl_reg' => $validated['tgl_reg'],
                    'nm_p' => $validated['nm_p'],
                    'mrn' => $validated['mrn'],
                    'almt_B' => $validated['almt_B'],
                    'no_inv' => !empty($validated['no_inv']) ? $validated['no_inv'] : $pasien->no_inv,
                    'tgl_inv' => !empty($validated['tgl_inv']) ? $validated['tgl_inv'] : $pasien->tgl_inv,
                    'perawatan' => $validated['perawatan'],
                    'penjamin' => $validated['penjamin'],
                    'no_sjp' => !empty($validated['no_sjp']) ? $validated['no_sjp'] : $pasien->no_sjp,
                    'icd' => !empty($validated['icd']) ? $validated['icd'] : $pasien->icd,
                    'kunjungan' => $validated['kunjungan'],
                ]);
            }

            // Create transaction record first
            $transaksi = $pasien->transaksi()->create([
                'pasien_id' => $pasien->id,
                'total_biaya' => 0, // Will be calculated later
                'tanggal' => now(),
                'status' => 'pending',
            ]);

            $totalBiaya = 0;

            // Create DetailTransaksi and related medical services
            if (!empty($validated['konsul'])) {
                foreach ($validated['konsul'] as $konsulData) {
                    $detailTransaksi = $transaksi->detailTransaksi()->create([
                        'transaksi_id' => $transaksi->id,
                        'resep' => 'Konsultasi',
                        'jumlah' => $konsulData['jmlh_kons'] ?? 1,
                        'deskripsi' => $konsulData['dskp_kons'] ?? '',
                        'biaya' => $konsulData['bya_kons'] ?? 0,
                    ]);

                    $detailTransaksi->konsuls()->create([
                        'detail_transaksi_id' => $detailTransaksi->id,
                        'dokter' => $konsulData['dokter'] ?? '',
                        'dskp_kons' => $konsulData['dskp_kons'] ?? '',
                        'jmlh_kons' => $konsulData['jmlh_kons'] ?? 0,
                        'bya_kons' => $konsulData['bya_kons'] ?? 0,
                        'disc_kons' => $konsulData['disc_kons'] ?? '0%',
                        'st_kons' => $konsulData['st_kons'] ?? 0,
                        'tanggal' => $konsulData['tanggal'] ?? now(),
                    ]);

                    $totalBiaya += ($konsulData['jmlh_kons'] ?? 0) * ($konsulData['bya_kons'] ?? 0);
                }
            }

            if (!empty($validated['tindak'])) {
                foreach ($validated['tindak'] as $tindakData) {
                    $detailTransaksi = $transaksi->detailTransaksi()->create([
                        'transaksi_id' => $transaksi->id,
                        'resep' => 'Tindakan',
                        'jumlah' => $tindakData['jmlh_tindak'] ?? 1,
                        'deskripsi' => $tindakData['dskp_tindak'] ?? '',
                        'biaya' => $tindakData['bya_tindak'] ?? 0,
                    ]);

                    $detailTransaksi->tindaks()->create([
                        'detail_transaksi_id' => $detailTransaksi->id,
                        'dktr_tindak' => $tindakData['dktr_tindak'] ?? '',
                        'dskp_tindak' => $tindakData['dskp_tindak'] ?? '',
                        'jmlh_tindak' => $tindakData['jmlh_tindak'] ?? 0,
                        'bya_tindak' => $tindakData['bya_tindak'] ?? 0,
                        'disc_tindak' => $tindakData['disc_tindak'] ?? '0%',
                        'st_tindak' => $tindakData['st_tindak'] ?? 0,
                        'tanggal' => $tindakData['tanggal'] ?? now(),
                    ]);

                    $totalBiaya += ($tindakData['jmlh_tindak'] ?? 0) * ($tindakData['bya_tindak'] ?? 0);
                }
            }

            if (!empty($validated['alkes'])) {
                foreach ($validated['alkes'] as $alkesData) {
                    $detailTransaksi = $transaksi->detailTransaksi()->create([
                        'transaksi_id' => $transaksi->id,
                        'resep' => 'Alkes',
                        'jumlah' => $alkesData['jmlh_alkes'] ?? 1,
                        'deskripsi' => $alkesData['dskp_alkes'] ?? '',
                        'biaya' => $alkesData['bya_alkes'] ?? 0,
                    ]);

                    $detailTransaksi->alkes()->create([
                        'detail_transaksi_id' => $detailTransaksi->id,
                        'poli' => $alkesData['poli'] ?? '',
                        'dskp_alkes' => $alkesData['dskp_alkes'] ?? '',
                        'jmlh_alkes' => $alkesData['jmlh_alkes'] ?? 0,
                        'bya_alkes' => $alkesData['bya_alkes'] ?? 0,
                        'disc_alkes' => $alkesData['disc_alkes'] ?? '0%',
                        'st_alkes' => $alkesData['st_alkes'] ?? 0,
                        'tanggal' => $alkesData['tanggal'] ?? now(),
                    ]);

                    $totalBiaya += ($alkesData['jmlh_alkes'] ?? 0) * ($alkesData['bya_alkes'] ?? 0);
                }
            }

            if (!empty($validated['rsp'])) {
                foreach ($validated['rsp'] as $rspData) {
                    $detailTransaksi = $transaksi->detailTransaksi()->create([
                        'transaksi_id' => $transaksi->id,
                        'resep' => 'Resep',
                        'jumlah' => $rspData['jmlh_rsp'] ?? 1,
                        'deskripsi' => $rspData['dskp_rsp'] ?? '',
                        'biaya' => $rspData['bya_rsp'] ?? 0,
                    ]);

                    $detailTransaksi->rsp()->create([
                        'detail_transaksi_id' => $detailTransaksi->id,
                        'dskp_rsp' => $rspData['dskp_rsp'] ?? '',
                        'jmlh_rsp' => $rspData['jmlh_rsp'] ?? 0,
                        'bya_rsp' => $rspData['bya_rsp'] ?? 0,
                        'disc_rsp' => $rspData['disc_rsp'] ?? '0%',
                        'st_rsp' => $rspData['st_rsp'] ?? 0,
                        'tanggal' => $rspData['tanggal'] ?? now(),
                    ]);

                    $totalBiaya += ($rspData['jmlh_rsp'] ?? 0) * ($rspData['bya_rsp'] ?? 0);
                }
            }

            if (!empty($validated['lainnya'])) {
                foreach ($validated['lainnya'] as $lainnyaData) {
                    $detailTransaksi = $transaksi->detailTransaksi()->create([
                        'transaksi_id' => $transaksi->id,
                        'resep' => 'Lainnya',
                        'jumlah' => $lainnyaData['jmlh_lainnaya'] ?? 1,
                        'deskripsi' => $lainnyaData['dskp_lainnya'] ?? '',
                        'biaya' => $lainnyaData['bya_lainnya'] ?? 0,
                    ]);

                    $detailTransaksi->lainnyas()->create([
                        'detail_transaksi_id' => $detailTransaksi->id,
                        'dskp_lainnya' => $lainnyaData['dskp_lainnya'] ?? '',
                        'jmlh_lainnaya' => $lainnyaData['jmlh_lainnaya'] ?? 0,
                        'bya_lainnya' => $lainnyaData['bya_lainnya'] ?? 0,
                        'disc_lainnya' => $lainnyaData['disc_lainnya'] ?? '0%',
                        'st_lainnya' => $lainnyaData['st_lainnya'] ?? 0,
                        'tanggal' => $lainnyaData['tanggal'] ?? now(),
                    ]);

                    $totalBiaya += ($lainnyaData['jmlh_lainnaya'] ?? 0) * ($lainnyaData['bya_lainnya'] ?? 0);
                }
            }

            // Update transaction with calculated total
            $transaksi->update(['total_biaya' => $totalBiaya]);

            DB::commit();

            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'Kunjungan dengan transaksi berhasil ditambahkan',
                    'data' => $pasien->load(['transaksi.detailTransaksi.konsuls', 'transaksi.detailTransaksi.tindaks', 'transaksi.detailTransaksi.alkes', 'transaksi.detailTransaksi.rsp', 'transaksi.detailTransaksi.lainnyas'])
                ], 201);
            }
            
            return redirect()->route('pasien.show', $validated['psn_id'])
                ->with('success', 'Kunjungan dengan transaksi berhasil ditambahkan');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'Gagal menambahkan kunjungan dengan transaksi',
                    'error' => $e->getMessage()
                ], 500);
            }
            
            return back()->withErrors(['error' => 'Gagal menambahkan kunjungan dengan transaksi: ' . $e->getMessage()]);
        }
    }
}
