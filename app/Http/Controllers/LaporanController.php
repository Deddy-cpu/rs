<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;
// use Maatwebsite\Excel\Facades\Excel; // Commented out - using CSV export instead
use Carbon\Carbon;

class LaporanController extends Controller
{
    /**
     * Display the reports page
     */
    public function index(Request $request)
    {
        $filterType = $request->input('filter_type', 'daily'); // daily, monthly, yearly
        $date = $request->input('date', date('Y-m-d'));
        $month = $request->input('month', date('Y-m'));
        $year = $request->input('year', date('Y'));

        $query = Transaksi::with(['kunjungan.psn', 'detailTransaksi'])
            ->where('status', 'lunas'); // Only show paid transactions

        // Apply filters based on filter type
        switch ($filterType) {
            case 'daily':
                $query->whereDate('tanggal', $date);
                $filterLabel = 'Harian - ' . Carbon::parse($date)->format('d F Y');
                break;
            case 'monthly':
                $query->whereYear('tanggal', Carbon::parse($month)->year)
                      ->whereMonth('tanggal', Carbon::parse($month)->month);
                $filterLabel = 'Bulanan - ' . Carbon::parse($month)->format('F Y');
                break;
            case 'yearly':
                $query->whereYear('tanggal', $year);
                $filterLabel = 'Tahunan - ' . $year;
                break;
            default:
                $query->whereDate('tanggal', $date);
                $filterLabel = 'Harian - ' . Carbon::parse($date)->format('d F Y');
        }

        $transaksis = $query->orderBy('tanggal', 'desc')->get();

        // Calculate statistics
        $totalTransaksi = $transaksis->count();
        $totalPemasukan = $transaksis->sum('total_biaya');
        $totalItemTerjual = DetailTransaksi::whereIn('transaksi_id', $transaksis->pluck('id'))
            ->sum('jumlah');

        // Get detailed data for table
        $dataTransaksi = $transaksis->map(function ($transaksi) {
            $itemCount = $transaksi->detailTransaksi->sum('jumlah');
            return [
                'id' => $transaksi->id,
                'tanggal' => $transaksi->tanggal->format('d/m/Y'),
                'no_reg' => $transaksi->kunjungan->no_reg ?? '-',
                'nama_pasien' => $transaksi->kunjungan->psn->nm_p ?? '-',
                'total_biaya' => number_format($transaksi->total_biaya, 0, ',', '.'),
                'total_biaya_raw' => $transaksi->total_biaya,
                'jumlah_item' => $itemCount,
                'status' => $transaksi->status ?? '-',
            ];
        });

        return Inertia::render('laporan/index', [
            'filterType' => $filterType,
            'date' => $date,
            'month' => $month,
            'year' => $year,
            'filterLabel' => $filterLabel,
            'totalTransaksi' => $totalTransaksi,
            'totalPemasukan' => number_format($totalPemasukan, 0, ',', '.'),
            'totalPemasukanRaw' => $totalPemasukan,
            'totalItemTerjual' => $totalItemTerjual,
            'dataTransaksi' => $dataTransaksi,
        ]);
    }

    /**
     * Export report to PDF
     */
    public function exportPdf(Request $request)
    {
        $filterType = $request->input('filter_type', 'daily');
        $date = $request->input('date', date('Y-m-d'));
        $month = $request->input('month', date('Y-m'));
        $year = $request->input('year', date('Y'));

        $query = Transaksi::with(['kunjungan.psn', 'detailTransaksi'])
            ->where('status', 'lunas'); // Only export paid transactions

        switch ($filterType) {
            case 'daily':
                $query->whereDate('tanggal', $date);
                $filterLabel = 'Harian - ' . Carbon::parse($date)->format('d F Y');
                break;
            case 'monthly':
                $query->whereYear('tanggal', Carbon::parse($month)->year)
                      ->whereMonth('tanggal', Carbon::parse($month)->month);
                $filterLabel = 'Bulanan - ' . Carbon::parse($month)->format('F Y');
                break;
            case 'yearly':
                $query->whereYear('tanggal', $year);
                $filterLabel = 'Tahunan - ' . $year;
                break;
        }

        $transaksis = $query->orderBy('tanggal', 'desc')->get();

        $totalTransaksi = $transaksis->count();
        $totalPemasukan = $transaksis->sum('total_biaya');
        $totalItemTerjual = DetailTransaksi::whereIn('transaksi_id', $transaksis->pluck('id'))
            ->sum('jumlah');

        $dataTransaksi = $transaksis->map(function ($transaksi) {
            $itemCount = $transaksi->detailTransaksi->sum('jumlah');
            return [
                'tanggal' => $transaksi->tanggal->format('d/m/Y'),
                'no_reg' => $transaksi->kunjungan->no_reg ?? '-',
                'nama_pasien' => $transaksi->kunjungan->psn->nm_p ?? '-',
                'total_biaya' => (float) $transaksi->total_biaya,
                'jumlah_item' => $itemCount,
                'status' => $transaksi->status ?? '-',
            ];
        });

        $pdf = Pdf::loadView('print.laporan', [
            'filterLabel' => $filterLabel,
            'filterType' => $filterType,
            'totalTransaksi' => $totalTransaksi,
            'totalPemasukan' => $totalPemasukan,
            'totalItemTerjual' => $totalItemTerjual,
            'dataTransaksi' => $dataTransaksi,
            'tanggalCetak' => Carbon::now()->format('d F Y H:i:s'),
        ])
        ->setPaper('A4', 'landscape')
        ->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'DejaVu Sans',
        ]);

        $filename = 'laporan-' . strtolower(str_replace(' ', '-', $filterLabel)) . '.pdf';
        return $pdf->download($filename);
    }

    /**
     * Export report to Excel
     */
    public function exportExcel(Request $request)
    {
        $filterType = $request->input('filter_type', 'daily');
        $date = $request->input('date', date('Y-m-d'));
        $month = $request->input('month', date('Y-m'));
        $year = $request->input('year', date('Y'));

        $query = Transaksi::with(['kunjungan.psn', 'detailTransaksi'])
            ->where('status', 'lunas'); // Only export paid transactions

        switch ($filterType) {
            case 'daily':
                $query->whereDate('tanggal', $date);
                $filterLabel = 'Harian - ' . Carbon::parse($date)->format('d F Y');
                break;
            case 'monthly':
                $query->whereYear('tanggal', Carbon::parse($month)->year)
                      ->whereMonth('tanggal', Carbon::parse($month)->month);
                $filterLabel = 'Bulanan - ' . Carbon::parse($month)->format('F Y');
                break;
            case 'yearly':
                $query->whereYear('tanggal', $year);
                $filterLabel = 'Tahunan - ' . $year;
                break;
        }

        $transaksis = $query->orderBy('tanggal', 'desc')->get();

        $totalTransaksi = $transaksis->count();
        $totalPemasukan = $transaksis->sum('total_biaya');
        $totalItemTerjual = DetailTransaksi::whereIn('transaksi_id', $transaksis->pluck('id'))
            ->sum('jumlah');

        $data = [];
        $data[] = ['LAPORAN TRANSAKSI'];
        $data[] = ['Periode: ' . $filterLabel];
        $data[] = ['Tanggal Cetak: ' . Carbon::now()->format('d F Y H:i:s')];
        $data[] = [];
        $data[] = ['No', 'Tanggal', 'No. Registrasi', 'Nama Pasien', 'Total Biaya', 'Jumlah Item', 'Status'];

        $no = 1;
        foreach ($transaksis as $transaksi) {
            $itemCount = $transaksi->detailTransaksi->sum('jumlah');
            $data[] = [
                $no++,
                $transaksi->tanggal->format('d/m/Y'),
                $transaksi->kunjungan->no_reg ?? '-',
                $transaksi->kunjungan->psn->nm_p ?? '-',
                $transaksi->total_biaya,
                $itemCount,
                $transaksi->status ?? '-',
            ];
        }

        $data[] = [];
        $data[] = ['TOTAL TRANSAKSI', $totalTransaksi];
        $data[] = ['TOTAL PEMASUKAN', $totalPemasukan];
        $data[] = ['TOTAL ITEM TERJUAL', $totalItemTerjual];

        $filename = 'laporan-' . strtolower(str_replace(' ', '-', $filterLabel)) . '-' . Carbon::now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            foreach ($data as $row) {
                fputcsv($file, $row);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

