<?php

namespace App\Http\Controllers;

use App\Models\Kunjungan;
use App\Models\Psn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class KunjunganController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $kunjungans = Kunjungan::with(['psn', 'transaksi'])
            ->orderBy('tgl_reg', 'desc')
            ->paginate(15);

        return Inertia::render('dokter/pasien_kunjungan/index', [
            'pasien' => $kunjungans
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        
        $kunjungans = Kunjungan::select('id', 'no_reg', 'tgl_reg', 'nm_p', 'mrn', 'almt_B', 'no_inv', 'tgl_inv', 'perawatan', 'penjamin', 'no_sjp', 'icd', 'kunjungan')->get();
        return Inertia::render('kunjungan/create', [
                'kunjungans' => $kunjungans

        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
           
            'no_reg' => 'required|string|max:255|unique:kunjungans,no_reg',
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
            'kunjungan' => 'required|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            $kunjungan = Kunjungan::create($validated);

            DB::commit();

            // Broadcast WebSocket update
            \App\Helpers\WebSocketBroadcast::kunjunganCreated($kunjungan);

            return redirect()->route('kunjungan.show', $kunjungan->id)
                ->with('success', 'Kunjungan berhasil dibuat.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->withErrors(['error' => 'Gagal membuat kunjungan: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Kunjungan $kunjungan)
    {
        $kunjungan->load(['psn', 'transaksi.detailTransaksi']);

        return Inertia::render('kunjungan/show', [
            'kunjungan' => $kunjungan
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Kunjungan $kunjungan)
    {
        $psns = Psn::select('id', 'nm_p', 'nik', 'no_bpjs')->get();
        
        return Inertia::render('kunjungan/edit', [
            'kunjungan' => $kunjungan,
            'psns' => $psns
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Kunjungan $kunjungan)
    {
        $validated = $request->validate([
            'psn_id' => 'required|exists:psns,id',
            'no_reg' => 'required|string|max:255|unique:kunjungans,no_reg,' . $kunjungan->id,
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
            'kunjungan' => 'required|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            $kunjungan->update($validated);

            DB::commit();

            // Broadcast WebSocket update
            \App\Helpers\WebSocketBroadcast::kunjunganUpdated($kunjungan);

            return redirect()->route('kunjungan.show', $kunjungan->id)
                ->with('success', 'Kunjungan berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->withErrors(['error' => 'Gagal memperbarui kunjungan: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Kunjungan $kunjungan)
    {
        try {
            DB::beginTransaction();

            // Delete related transactions first
            $kunjungan->transaksi()->delete();
            
            // Store ID before deletion for broadcast
            $kunjunganId = $kunjungan->id;
            
            // Delete the kunjungan
            $kunjungan->delete();

            DB::commit();

            // Broadcast WebSocket update
            \App\Helpers\WebSocketBroadcast::kunjunganDeleted($kunjunganId);

            return redirect()->route('dokter.pasien-kunjungan')
                ->with('success', 'Kunjungan berhasil dihapus.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->withErrors(['error' => 'Gagal menghapus kunjungan: ' . $e->getMessage()]);
        }
    }

    /**
     * Get kunjungans by patient ID
     */
    public function getByPatient($psnId)
    {
        $kunjungans = Kunjungan::where('psn_id', $psnId)
            ->with(['transaksi'])
            ->orderBy('tgl_reg', 'desc')
            ->get();

        return response()->json($kunjungans);
    }

    /**
     * Get kunjungan statistics
     */
    public function statistics()
    {
        $stats = [
            'total_kunjungan' => Kunjungan::count(),
            'kunjungan_hari_ini' => Kunjungan::whereDate('tgl_reg', today())->count(),
            'kunjungan_bulan_ini' => Kunjungan::whereMonth('tgl_reg', now()->month)
                ->whereYear('tgl_reg', now()->year)
                ->count(),
            'perawatan_stats' => Kunjungan::select('perawatan', DB::raw('count(*) as total'))
                ->groupBy('perawatan')
                ->get(),
            'penjamin_stats' => Kunjungan::select('penjamin', DB::raw('count(*) as total'))
                ->groupBy('penjamin')
                ->get(),
            'kunjungan_stats' => Kunjungan::select('kunjungan', DB::raw('count(*) as total'))
                ->groupBy('kunjungan')
                ->get(),
        ];

        return response()->json($stats);
    }

    /**
     * Search kunjungans
     */
    public function search(Request $request)
    {
        $query = Kunjungan::with(['psn']);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_reg', 'like', "%{$search}%")
                  ->orWhere('nm_p', 'like', "%{$search}%")
                  ->orWhere('mrn', 'like', "%{$search}%")
                  ->orWhere('no_inv', 'like', "%{$search}%")
                  ->orWhereHas('psn', function ($psnQuery) use ($search) {
                      $psnQuery->where('nik', 'like', "%{$search}%")
                               ->orWhere('no_bpjs', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->has('perawatan')) {
            $query->where('perawatan', $request->perawatan);
        }

        if ($request->has('penjamin')) {
            $query->where('penjamin', $request->penjamin);
        }

        if ($request->has('kunjungan')) {
            $query->where('kunjungan', $request->kunjungan);
        }

        if ($request->has('date_from')) {
            $query->whereDate('tgl_reg', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('tgl_reg', '<=', $request->date_to);
        }

        $kunjungans = $query->orderBy('tgl_reg', 'desc')->paginate(15);

        return Inertia::render('dokter/pasien_kunjungan/index', [
            'pasien' => $kunjungans,
            'filters' => $request->only(['search', 'perawatan', 'penjamin', 'kunjungan', 'date_from', 'date_to'])
        ]);
    }

    /**
     * Export kunjungans to CSV
     */
    public function export(Request $request)
    {
        $query = Kunjungan::with(['psn']);

        // Apply same filters as search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_reg', 'like', "%{$search}%")
                  ->orWhere('nm_p', 'like', "%{$search}%")
                  ->orWhere('mrn', 'like', "%{$search}%")
                  ->orWhere('no_inv', 'like', "%{$search}%")
                  ->orWhereHas('psn', function ($psnQuery) use ($search) {
                      $psnQuery->where('nik', 'like', "%{$search}%")
                               ->orWhere('no_bpjs', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->has('perawatan')) {
            $query->where('perawatan', $request->perawatan);
        }

        if ($request->has('penjamin')) {
            $query->where('penjamin', $request->penjamin);
        }

        if ($request->has('kunjungan')) {
            $query->where('kunjungan', $request->kunjungan);
        }

        if ($request->has('date_from')) {
            $query->whereDate('tgl_reg', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('tgl_reg', '<=', $request->date_to);
        }

        $kunjungans = $query->orderBy('tgl_reg', 'desc')->get();

        $filename = 'kunjungans_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($kunjungans) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'No Registrasi',
                'Tanggal Registrasi',
                'Nama Pasien',
                'NIK',
                'No BPJS',
                'MRN',
                'Alamat',
                'No Invoice',
                'Tanggal Invoice',
                'Jenis Perawatan',
                'Penjamin',
                'No SJP',
                'ICD Code',
                'Jenis Kunjungan',
                'Tanggal Dibuat',
                'Tanggal Diperbarui'
            ]);

            // CSV data
            foreach ($kunjungans as $kunjungan) {
                fputcsv($file, [
                    $kunjungan->no_reg,
                    $kunjungan->tgl_reg ? \Carbon\Carbon::parse($kunjungan->tgl_reg)->format('Y-m-d') : '',
                    $kunjungan->nm_p,
                    $kunjungan->psn->nik ?? '',
                    $kunjungan->psn->no_bpjs ?? '',
                    $kunjungan->mrn,
                    $kunjungan->almt_B,
                    $kunjungan->no_inv ?? '',
                    $kunjungan->tgl_inv ? \Carbon\Carbon::parse($kunjungan->tgl_inv)->format('Y-m-d') : '',
                    $kunjungan->perawatan,
                    $kunjungan->penjamin,
                    $kunjungan->no_sjp ?? '',
                    $kunjungan->icd ?? '',
                    $kunjungan->kunjungan,
                    $kunjungan->created_at->format('Y-m-d H:i:s'),
                    $kunjungan->updated_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
