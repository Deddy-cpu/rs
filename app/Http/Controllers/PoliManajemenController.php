<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kunjungan;
use App\Models\Polis;
use Inertia\Inertia;

class PoliManajemenController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $selectedPoli = $request->input('poli');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        // Get all kunjungan data with relationships
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

        // Filter by selected poli (kunjungan type)
        if ($selectedPoli && $selectedPoli !== 'all') {
            $query->where('kunjungan', $selectedPoli);
        }

        // Filter by date range
        if ($dateFrom) {
            $query->whereDate('tgl_reg', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('tgl_reg', '<=', $dateTo);
        }

        $kunjunganData = $query->orderBy('tgl_reg', 'desc')->paginate(15)->withQueryString();

        // Transform the data for frontend compatibility
        $transformedKunjungan = $kunjunganData->getCollection()->map(function ($kunjungan) {
            // Flatten the nested structure
            $kunjungan->konsuls = collect();
            $kunjungan->tindaks = collect();
            $kunjungan->alkes = collect();
            $kunjungan->rsp = collect();
            $kunjungan->lainnyas = collect();

            // Keep transaksi data for frontend
            $transaksiData = collect();
            
            foreach ($kunjungan->transaksi as $transaksi) {
                // Add transaksi summary
                $transaksiData->push([
                    'id' => $transaksi->id,
                    'total_biaya' => $transaksi->total_biaya,
                    'tanggal' => $transaksi->tanggal,
                    'status' => $transaksi->status
                ]);
                
                foreach ($transaksi->detailTransaksi as $detailTransaksi) {
                    $kunjungan->konsuls = $kunjungan->konsuls->merge($detailTransaksi->konsuls);
                    $kunjungan->tindaks = $kunjungan->tindaks->merge($detailTransaksi->tindaks);
                    $kunjungan->alkes = $kunjungan->alkes->merge($detailTransaksi->alkes);
                    $kunjungan->rsp = $kunjungan->rsp->merge($detailTransaksi->rsp);
                    $kunjungan->lainnyas = $kunjungan->lainnyas->merge($detailTransaksi->lainnyas);
                }
            }
            
            // Add transaksi data to kunjungan
            $kunjungan->transaksi = $transaksiData;

            return $kunjungan;
        });

        // Replace the collection in the paginated result
        $kunjunganData->setCollection($transformedKunjungan);

        // Get unique kunjungan types for filter buttons
        $availablePolis = Kunjungan::distinct()
            ->whereNotNull('kunjungan')
            ->where('kunjungan', '!=', '')
            ->pluck('kunjungan')
            ->filter()
            ->sort()
            ->values();

        // Get statistics
        $totalKunjungan = Kunjungan::count();
        $totalByPoli = Kunjungan::selectRaw('kunjungan, COUNT(*) as count')
            ->whereNotNull('kunjungan')
            ->where('kunjungan', '!=', '')
            ->groupBy('kunjungan')
            ->orderBy('count', 'desc')
            ->get()
            ->pluck('count', 'kunjungan');

        return Inertia::render('dokter/poli_manajemen/index', [
            'kunjungan' => $kunjunganData,
            'availablePolis' => $availablePolis,
            'totalKunjungan' => $totalKunjungan,
            'totalByPoli' => $totalByPoli,
            'selectedPoli' => $selectedPoli,
            'filters' => $request->only(['search', 'poli', 'date_from', 'date_to']),
            'flash' => [
                'success' => session('success'),
                'error' => session('error')
            ]
        ]);
    }

    public function show($poli)
    {
        // Get data for specific poli
        $query = Kunjungan::with([
            'psn',
            'transaksi.detailTransaksi.konsuls',
            'transaksi.detailTransaksi.tindaks',
            'transaksi.detailTransaksi.alkes',
            'transaksi.detailTransaksi.rsp',
            'transaksi.detailTransaksi.lainnyas'
        ])->where('kunjungan', $poli);

        $kunjunganData = $query->orderBy('tgl_reg', 'desc')->paginate(15)->withQueryString();

        // Transform the data
        $transformedKunjungan = $kunjunganData->getCollection()->map(function ($kunjungan) {
            $kunjungan->konsuls = collect();
            $kunjungan->tindaks = collect();
            $kunjungan->alkes = collect();
            $kunjungan->rsp = collect();
            $kunjungan->lainnyas = collect();

            $transaksiData = collect();
            
            foreach ($kunjungan->transaksi as $transaksi) {
                $transaksiData->push([
                    'id' => $transaksi->id,
                    'total_biaya' => $transaksi->total_biaya,
                    'tanggal' => $transaksi->tanggal,
                    'status' => $transaksi->status
                ]);
                
                foreach ($transaksi->detailTransaksi as $detailTransaksi) {
                    $kunjungan->konsuls = $kunjungan->konsuls->merge($detailTransaksi->konsuls);
                    $kunjungan->tindaks = $kunjungan->tindaks->merge($detailTransaksi->tindaks);
                    $kunjungan->alkes = $kunjungan->alkes->merge($detailTransaksi->alkes);
                    $kunjungan->rsp = $kunjungan->rsp->merge($detailTransaksi->rsp);
                    $kunjungan->lainnyas = $kunjungan->lainnyas->merge($detailTransaksi->lainnyas);
                }
            }
            
            $kunjungan->transaksi = $transaksiData;
            return $kunjungan;
        });

        $kunjunganData->setCollection($transformedKunjungan);

        // Get available polis for navigation
        $availablePolis = Kunjungan::distinct()
            ->whereNotNull('kunjungan')
            ->where('kunjungan', '!=', '')
            ->pluck('kunjungan')
            ->filter()
            ->sort()
            ->values();

        // Get statistics for this poli
        $totalByPoli = Kunjungan::selectRaw('kunjungan, COUNT(*) as count')
            ->whereNotNull('kunjungan')
            ->where('kunjungan', '!=', '')
            ->groupBy('kunjungan')
            ->orderBy('count', 'desc')
            ->get()
            ->pluck('count', 'kunjungan');

        return Inertia::render('dokter/poli_manajemen/index', [
            'kunjungan' => $kunjunganData,
            'availablePolis' => $availablePolis,
            'totalByPoli' => $totalByPoli,
            'selectedPoli' => $poli,
            'filters' => ['poli' => $poli],
            'flash' => [
                'success' => session('success'),
                'error' => session('error')
            ]
        ]);
    }
}

