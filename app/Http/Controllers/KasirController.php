<?php

namespace App\Http\Controllers;

use App\Models\Kunjungan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class KasirController extends Controller
{
    /**
     * Display a listing of kunjungan data for kasir
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $filterPenjamin = $request->input('penjamin');
        $filterPerawatan = $request->input('perawatan');
        $filterKunjungan = $request->input('kunjungan');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $dayFilter = $request->input('day_filter');

        $query = Kunjungan::with([
            'psn',
            'transaksi.detailTransaksi.konsuls',
            'transaksi.detailTransaksi.tindaks',
            'transaksi.detailTransaksi.alkes',
            'transaksi.detailTransaksi.rsp',
            'transaksi.detailTransaksi.lainnyas'
        ]);

        // Filter by search query
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nm_p', 'like', "%{$search}%")
                  ->orWhere('no_reg', 'like', "%{$search}%")
                  ->orWhere('mrn', 'like', "%{$search}%")
                  ->orWhereHas('psn', function($psnQuery) use ($search) {
                      $psnQuery->where('nm_p', 'like', "%{$search}%")
                               ->orWhere('nik', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by penjamin
        if ($filterPenjamin) {
            $query->where('penjamin', $filterPenjamin);
        }

        // Filter by perawatan
        if ($filterPerawatan) {
            $query->where('perawatan', $filterPerawatan);
        }

        // Filter by kunjungan
        if ($filterKunjungan) {
            $query->where('kunjungan', $filterKunjungan);
        }

        // Filter by date range
        if ($dateFrom) {
            $query->whereDate('tgl_reg', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('tgl_reg', '<=', $dateTo);
        }

        // Filter by day (quick filter)
        if ($dayFilter && !$dateFrom && !$dateTo) {
            $today = now()->startOfDay();
            
            switch ($dayFilter) {
                case 'today':
                    $query->whereDate('tgl_reg', $today->toDateString());
                    break;
                case 'yesterday':
                    $yesterday = $today->copy()->subDay();
                    $query->whereDate('tgl_reg', $yesterday->toDateString());
                    break;
                case 'this_week':
                    $startOfWeek = $today->copy()->startOfWeek();
                    $query->whereBetween('tgl_reg', [$startOfWeek, $today]);
                    break;
                case 'last_week':
                    $startOfLastWeek = $today->copy()->subWeek()->startOfWeek();
                    $endOfLastWeek = $today->copy()->subWeek()->endOfWeek();
                    $query->whereBetween('tgl_reg', [$startOfLastWeek, $endOfLastWeek]);
                    break;
                case 'this_month':
                    $startOfMonth = $today->copy()->startOfMonth();
                    $query->whereBetween('tgl_reg', [$startOfMonth, $today]);
                    break;
                case 'last_month':
                    $startOfLastMonth = $today->copy()->subMonth()->startOfMonth();
                    $endOfLastMonth = $today->copy()->subMonth()->endOfMonth();
                    $query->whereBetween('tgl_reg', [$startOfLastMonth, $endOfLastMonth]);
                    break;
                case 'this_year':
                    $startOfYear = $today->copy()->startOfYear();
                    $query->whereBetween('tgl_reg', [$startOfYear, $today]);
                    break;
            }
        }

        $kunjungan = $query->orderBy('tgl_reg', 'desc')->paginate(10)->withQueryString();

        // Transform the data to be compatible with frontend expectations
        $transformedKunjungan = $kunjungan->getCollection()->map(function ($kunjungan) {
            // Flatten the nested structure for frontend compatibility
            $kunjungan->konsuls = collect();
            $kunjungan->tindaks = collect();
            $kunjungan->alkes = collect();
            $kunjungan->rsp = collect();
            $kunjungan->lainnyas = collect();

            foreach ($kunjungan->transaksi as $transaksi) {
                foreach ($transaksi->detailTransaksi as $detailTransaksi) {
                    $kunjungan->konsuls = $kunjungan->konsuls->merge($detailTransaksi->konsuls);
                    $kunjungan->tindaks = $kunjungan->tindaks->merge($detailTransaksi->tindaks);
                    $kunjungan->alkes = $kunjungan->alkes->merge($detailTransaksi->alkes);
                    $kunjungan->rsp = $kunjungan->rsp->merge($detailTransaksi->rsp);
                    $kunjungan->lainnyas = $kunjungan->lainnyas->merge($detailTransaksi->lainnyas);
                }
            }

            return $kunjungan;
        });

        // Get unique values for filters
        $uniquePenjamin = Kunjungan::distinct()->pluck('penjamin')->filter()->values();
        $uniquePerawatan = Kunjungan::distinct()->pluck('perawatan')->filter()->values();
        $uniqueKunjungan = Kunjungan::distinct()->pluck('kunjungan')->filter()->values();

        // Update the pagination collection with transformed data
        $kunjungan->setCollection($transformedKunjungan);

        return Inertia::render('kasir/index', [
            'kunjungan' => $kunjungan,
            'filters' => $request->only(['search', 'penjamin', 'perawatan', 'kunjungan', 'date_from', 'date_to', 'day_filter']),
            'uniquePenjamin' => $uniquePenjamin,
            'uniquePerawatan' => $uniquePerawatan,
            'uniqueKunjungan' => $uniqueKunjungan,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('kasir/create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Implementation for storing kasir data
        // This can be implemented based on your specific requirements
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $kunjungan = Kunjungan::with([
            'psn',
            'transaksi.detailTransaksi.konsuls',
            'transaksi.detailTransaksi.tindaks',
            'transaksi.detailTransaksi.alkes',
            'transaksi.detailTransaksi.rsp',
            'transaksi.detailTransaksi.lainnyas'
        ])->findOrFail($id);

        // Transform the data to be compatible with frontend expectations
        $kunjungan->konsuls = collect();
        $kunjungan->tindaks = collect();
        $kunjungan->alkes = collect();
        $kunjungan->rsp = collect();
        $kunjungan->lainnyas = collect();

        foreach ($kunjungan->transaksi as $transaksi) {
            foreach ($transaksi->detailTransaksi as $detailTransaksi) {
                $kunjungan->konsuls = $kunjungan->konsuls->merge($detailTransaksi->konsuls);
                $kunjungan->tindaks = $kunjungan->tindaks->merge($detailTransaksi->tindaks);
                $kunjungan->alkes = $kunjungan->alkes->merge($detailTransaksi->alkes);
                $kunjungan->rsp = $kunjungan->rsp->merge($detailTransaksi->rsp);
                $kunjungan->lainnyas = $kunjungan->lainnyas->merge($detailTransaksi->lainnyas);
            }
        }

        return Inertia::render('kasir/show', [
            'kunjungan' => $kunjungan,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        // Implementation for editing kasir data
        // This can be implemented based on your specific requirements
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Implementation for updating kasir data
        // This can be implemented based on your specific requirements
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Implementation for deleting kasir data
        // This can be implemented based on your specific requirements
    }

    /**
     * Show bayar page for kunjungan
     */
    public function bayar($id)
    {
        $kunjungan = Kunjungan::with([
            'psn',
            'transaksi.detailTransaksi.konsuls',
            'transaksi.detailTransaksi.tindaks',
            'transaksi.detailTransaksi.alkes',
            'transaksi.detailTransaksi.rsp',
            'transaksi.detailTransaksi.lainnyas'
        ])->findOrFail($id);

        // Transform the data
        $kunjungan->konsuls = collect();
        $kunjungan->tindaks = collect();
        $kunjungan->alkes = collect();
        $kunjungan->rsp = collect();
        $kunjungan->lainnyas = collect();

        foreach ($kunjungan->transaksi as $transaksi) {
            foreach ($transaksi->detailTransaksi as $detailTransaksi) {
                $kunjungan->konsuls = $kunjungan->konsuls->merge($detailTransaksi->konsuls);
                $kunjungan->tindaks = $kunjungan->tindaks->merge($detailTransaksi->tindaks);
                $kunjungan->alkes = $kunjungan->alkes->merge($detailTransaksi->alkes);
                $kunjungan->rsp = $kunjungan->rsp->merge($detailTransaksi->rsp);
                $kunjungan->lainnyas = $kunjungan->lainnyas->merge($detailTransaksi->lainnyas);
            }
        }

        return Inertia::render('kasir/bayar', [
            'kunjungan' => $kunjungan,
        ]);
    }

    /**
     * Update invoice data for kunjungan
     */
    public function updateBayar(Request $request, $id)
    {
        $validated = $request->validate([
            'no_inv' => 'required|string|max:255',
            'tgl_inv' => 'required|date',
        ]);

        $kunjungan = Kunjungan::findOrFail($id);
        $kunjungan->update($validated);

        return redirect()->route('kasir.kunjungan.print', $id)
            ->with('success', 'Invoice berhasil diupdate');
    }

    /**
     * Show print page for kunjungan
     */
    public function print($id)
    {
        $kunjungan = Kunjungan::with([
            'psn',
            'transaksi.detailTransaksi.konsuls',
            'transaksi.detailTransaksi.tindaks',
            'transaksi.detailTransaksi.alkes',
            'transaksi.detailTransaksi.rsp',
            'transaksi.detailTransaksi.lainnyas'
        ])->findOrFail($id);

        // Transform the data to be compatible with frontend expectations
        $kunjungan->konsuls = collect();
        $kunjungan->tindaks = collect();
        $kunjungan->alkes = collect();
        $kunjungan->rsp = collect();
        $kunjungan->lainnyas = collect();

        foreach ($kunjungan->transaksi as $transaksi) {
            foreach ($transaksi->detailTransaksi as $detailTransaksi) {
                $kunjungan->konsuls = $kunjungan->konsuls->merge($detailTransaksi->konsuls);
                $kunjungan->tindaks = $kunjungan->tindaks->merge($detailTransaksi->tindaks);
                $kunjungan->alkes = $kunjungan->alkes->merge($detailTransaksi->alkes);
                $kunjungan->rsp = $kunjungan->rsp->merge($detailTransaksi->rsp);
                $kunjungan->lainnyas = $kunjungan->lainnyas->merge($detailTransaksi->lainnyas);
            }
        }

        // Calculate totals
        $totals = [
            'konsul' => $this->calculateKonsulTotal($kunjungan),
            'tindak' => $this->calculateTindakTotal($kunjungan),
            'alkes' => $this->calculateAlkesTotal($kunjungan),
            'rsp' => $this->calculateRspTotal($kunjungan),
            'lainnya' => $this->calculateLainnyaTotal($kunjungan),
        ];
        
        $grandTotal = array_sum($totals);

        return Inertia::render('kasir/print', [
            'kunjungan' => $kunjungan,
            'totals' => $totals,
            'grandTotal' => $grandTotal,
        ]);
    }

    /**
     * Generate PDF for kunjungan
     */
    public function pdf($id)
    {
        $kunjungan = Kunjungan::with([
            'psn',
            'transaksi.detailTransaksi.konsuls',
            'transaksi.detailTransaksi.tindaks',
            'transaksi.detailTransaksi.alkes',
            'transaksi.detailTransaksi.rsp',
            'transaksi.detailTransaksi.lainnyas'
        ])->findOrFail($id);

        // Transform the data to be compatible with frontend expectations
        $kunjungan->konsuls = collect();
        $kunjungan->tindaks = collect();
        $kunjungan->alkes = collect();
        $kunjungan->rsp = collect();
        $kunjungan->lainnyas = collect();

        foreach ($kunjungan->transaksi as $transaksi) {
            foreach ($transaksi->detailTransaksi as $detailTransaksi) {
                $kunjungan->konsuls = $kunjungan->konsuls->merge($detailTransaksi->konsuls);
                $kunjungan->tindaks = $kunjungan->tindaks->merge($detailTransaksi->tindaks);
                $kunjungan->alkes = $kunjungan->alkes->merge($detailTransaksi->alkes);
                $kunjungan->rsp = $kunjungan->rsp->merge($detailTransaksi->rsp);
                $kunjungan->lainnyas = $kunjungan->lainnyas->merge($detailTransaksi->lainnyas);
            }
        }

        // Calculate totals
        $totals = [
            'konsul' => $this->calculateKonsulTotal($kunjungan),
            'tindak' => $this->calculateTindakTotal($kunjungan),
            'alkes' => $this->calculateAlkesTotal($kunjungan),
            'rsp' => $this->calculateRspTotal($kunjungan),
            'lainnya' => $this->calculateLainnyaTotal($kunjungan),
        ];
        
        $grandTotal = array_sum($totals);

        try {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('print.kunjungan', compact('kunjungan', 'totals', 'grandTotal'))
                      ->setPaper('A4', 'portrait')
                      ->setOptions([
                          'isHtml5ParserEnabled' => true,
                          'isRemoteEnabled' => true,
                          'defaultFont' => 'DejaVu Sans',
                          'isPhpEnabled' => true,
                          'isJavascriptEnabled' => false,
                          'isFontSubsettingEnabled' => true
                      ]);
        
            $filename = 'kunjungan-'.$kunjungan->no_reg.'.pdf';
            
            return $pdf->download($filename);
        } catch (\Exception $e) {
            \Log::error('PDF Generation Error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to generate PDF: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Calculate konsul total
     */
    private function calculateKonsulTotal($kunjungan)
    {
        if (!$kunjungan->konsuls || $kunjungan->konsuls->isEmpty()) return 0;
        
        return $kunjungan->konsuls->sum(function($konsul) {
            $jumlah = floatval($konsul->jmlh_kons) ?: 0;
            $biaya = floatval($konsul->bya_kons) ?: 0;
            $subtotal = $jumlah * $biaya;
            
            $diskon = 0;
            if ($konsul->disc_kons && $konsul->disc_kons !== '0%') {
                $discPercent = floatval(preg_replace('/[^\d]/', '', $konsul->disc_kons)) ?: 0;
                $diskon = ($subtotal * $discPercent) / 100;
            }
            
            return max($subtotal - $diskon, 0);
        });
    }

    /**
     * Calculate tindak total
     */
    private function calculateTindakTotal($kunjungan)
    {
        if (!$kunjungan->tindaks || $kunjungan->tindaks->isEmpty()) return 0;
        
        return $kunjungan->tindaks->sum(function($tindak) {
            $jumlah = floatval($tindak->jmlh_tindak) ?: 0;
            $biaya = floatval($tindak->bya_tindak) ?: 0;
            $subtotal = $jumlah * $biaya;
            
            $diskon = 0;
            if ($tindak->disc_tindak && $tindak->disc_tindak !== '0%') {
                $discPercent = floatval(preg_replace('/[^\d]/', '', $tindak->disc_tindak)) ?: 0;
                $diskon = ($subtotal * $discPercent) / 100;
            }
            
            return max($subtotal - $diskon, 0);
        });
    }

    /**
     * Calculate alkes total
     */
    private function calculateAlkesTotal($kunjungan)
    {
        if (!$kunjungan->alkes || $kunjungan->alkes->isEmpty()) return 0;
        
        return $kunjungan->alkes->sum(function($alkes) {
            $jumlah = floatval($alkes->jmlh_alkes) ?: 0;
            $biaya = floatval($alkes->bya_alkes) ?: 0;
            $subtotal = $jumlah * $biaya;
            
            $diskon = 0;
            if ($alkes->disc_alkes && $alkes->disc_alkes !== '0%') {
                $discPercent = floatval(preg_replace('/[^\d]/', '', $alkes->disc_alkes)) ?: 0;
                $diskon = ($subtotal * $discPercent) / 100;
            }
            
            return max($subtotal - $diskon, 0);
        });
    }

    /**
     * Calculate rsp total
     */
    private function calculateRspTotal($kunjungan)
    {
        if (!$kunjungan->rsp || $kunjungan->rsp->isEmpty()) return 0;
        
        return $kunjungan->rsp->sum(function($rsp) {
            $jumlah = floatval($rsp->jmlh_rsp) ?: 0;
            $biaya = floatval($rsp->bya_rsp) ?: 0;
            $subtotal = $jumlah * $biaya;
            
            $diskon = 0;
            if ($rsp->disc_rsp && $rsp->disc_rsp !== '0%') {
                $discPercent = floatval(preg_replace('/[^\d]/', '', $rsp->disc_rsp)) ?: 0;
                $diskon = ($subtotal * $discPercent) / 100;
            }
            
            return max($subtotal - $diskon, 0);
        });
    }

    /**
     * Calculate lainnya total
     */
    private function calculateLainnyaTotal($kunjungan)
    {
        if (!$kunjungan->lainnyas || $kunjungan->lainnyas->isEmpty()) return 0;
        
        return $kunjungan->lainnyas->sum(function($lainnya) {
            $jumlah = floatval($lainnya->jmlh_lainnaya) ?: 0;
            $biaya = floatval($lainnya->bya_lainnya) ?: 0;
            $subtotal = $jumlah * $biaya;
            
            $diskon = 0;
            if ($lainnya->disc_lainnya && $lainnya->disc_lainnya !== '0%') {
                $discPercent = floatval(preg_replace('/[^\d]/', '', $lainnya->disc_lainnya)) ?: 0;
                $diskon = ($subtotal * $discPercent) / 100;
            }
            
            return max($subtotal - $diskon, 0);
        });
    }
}
