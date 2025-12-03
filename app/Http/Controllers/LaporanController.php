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

        $query = Transaksi::with([
            'kunjungan.psn', 
            'detailTransaksi.konsuls.tindakanTarif.tindakanQ',
            'detailTransaksi.konsuls.eselon.grpEselon',
            'detailTransaksi.tindaks',
            'detailTransaksi.alkes',
            'detailTransaksi.rsp',
            'detailTransaksi.lainnyas'
        ])
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

        // Get detailed data for detail_transaksi table
        $dataDetailTransaksi = [];
        foreach ($transaksis as $transaksi) {
            foreach ($transaksi->detailTransaksi as $detail) {
                // Konsuls
                foreach ($detail->konsuls as $konsul) {
                    $dataDetailTransaksi[] = [
                        'transaksi_id' => $transaksi->id,
                        'tanggal' => $transaksi->tanggal->format('d/m/Y'),
                        'no_reg' => $transaksi->kunjungan->no_reg ?? '-',
                        'nama_pasien' => $transaksi->kunjungan->psn->nm_p ?? '-',
                        'jenis' => 'Konsul',
                        'item' => $konsul->tindakanTarif->tindakanQ->tindakan_q_desc ?? $konsul->dskp_kons ?? '-',
                        'dokter' => $konsul->dokter ?? '-',
                        'jumlah' => $konsul->jmlh_kons ?? 0,
                        'biaya' => $konsul->bya_kons ?? 0,
                        'disc' => $konsul->disc_kons ?? '0%',
                        'subtotal' => ($konsul->jmlh_kons ?? 0) * ($konsul->bya_kons ?? 0),
                    ];
                }
                
                // Tindaks
                foreach ($detail->tindaks as $tindak) {
                    $dataDetailTransaksi[] = [
                        'transaksi_id' => $transaksi->id,
                        'tanggal' => $transaksi->tanggal->format('d/m/Y'),
                        'no_reg' => $transaksi->kunjungan->no_reg ?? '-',
                        'nama_pasien' => $transaksi->kunjungan->psn->nm_p ?? '-',
                        'jenis' => 'Tindakan',
                        'item' => $tindak->dskp_tindak ?? '-',
                        'dokter' => '-',
                        'jumlah' => $tindak->jmlh_tindak ?? 0,
                        'biaya' => $tindak->bya_tindak ?? 0,
                        'disc' => $tindak->disc_tindak ?? '0%',
                        'subtotal' => ($tindak->jmlh_tindak ?? 0) * ($tindak->bya_tindak ?? 0),
                    ];
                }
                
                // Alkes
                foreach ($detail->alkes as $alkes) {
                    $dataDetailTransaksi[] = [
                        'transaksi_id' => $transaksi->id,
                        'tanggal' => $transaksi->tanggal->format('d/m/Y'),
                        'no_reg' => $transaksi->kunjungan->no_reg ?? '-',
                        'nama_pasien' => $transaksi->kunjungan->psn->nm_p ?? '-',
                        'jenis' => 'Alat Kesehatan',
                        'item' => $alkes->dskp_alkes ?? '-',
                        'dokter' => '-',
                        'jumlah' => $alkes->jmlh_alkes ?? 0,
                        'biaya' => $alkes->bya_alkes ?? 0,
                        'disc' => $alkes->disc_alkes ?? '0%',
                        'subtotal' => ($alkes->jmlh_alkes ?? 0) * ($alkes->bya_alkes ?? 0),
                    ];
                }
                
                // Rsp (Resep)
                foreach ($detail->rsp as $rsp) {
                    $dataDetailTransaksi[] = [
                        'transaksi_id' => $transaksi->id,
                        'tanggal' => $transaksi->tanggal->format('d/m/Y'),
                        'no_reg' => $transaksi->kunjungan->no_reg ?? '-',
                        'nama_pasien' => $transaksi->kunjungan->psn->nm_p ?? '-',
                        'jenis' => 'Resep',
                        'item' => $rsp->dskp_rsp ?? '-',
                        'dokter' => $rsp->dokter ?? '-',
                        'jumlah' => $rsp->jmlh_rsp ?? 0,
                        'biaya' => $rsp->bya_rsp ?? 0,
                        'disc' => $rsp->disc_rsp ?? '0%',
                        'subtotal' => ($rsp->jmlh_rsp ?? 0) * ($rsp->bya_rsp ?? 0),
                    ];
                }
                
                // Lainnya
                foreach ($detail->lainnyas as $lainnya) {
                    $dataDetailTransaksi[] = [
                        'transaksi_id' => $transaksi->id,
                        'tanggal' => $transaksi->tanggal->format('d/m/Y'),
                        'no_reg' => $transaksi->kunjungan->no_reg ?? '-',
                        'nama_pasien' => $transaksi->kunjungan->psn->nm_p ?? '-',
                        'jenis' => 'Lainnya',
                        'item' => $lainnya->dskp_lainnya ?? '-',
                        'dokter' => $lainnya->dktr_lainnya ?? '-',
                        'jumlah' => $lainnya->jmlh_lainnya ?? 0,
                        'biaya' => $lainnya->bya_lainnya ?? 0,
                        'disc' => $lainnya->disc_lainnya ?? '0%',
                        'subtotal' => ($lainnya->jmlh_lainnya ?? 0) * ($lainnya->bya_lainnya ?? 0),
                    ];
                }
            }
        }

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
            'dataDetailTransaksi' => $dataDetailTransaksi,
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
        $type = $request->input('type', 'summary'); // 'summary' or 'detail'

        $query = Transaksi::with([
            'kunjungan.psn', 
            'detailTransaksi.konsuls.tindakanTarif.tindakanQ',
            'detailTransaksi.konsuls.eselon.grpEselon',
            'detailTransaksi.tindaks',
            'detailTransaksi.alkes',
            'detailTransaksi.rsp',
            'detailTransaksi.lainnyas'
        ])
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

        if ($type === 'detail') {
            // Export detail perincian
            $dataDetailTransaksi = [];
            foreach ($transaksis as $transaksi) {
                foreach ($transaksi->detailTransaksi as $detail) {
                    // Konsuls
                    foreach ($detail->konsuls as $konsul) {
                        $dataDetailTransaksi[] = [
                            'tanggal' => $transaksi->tanggal->format('d/m/Y'),
                            'no_reg' => $transaksi->kunjungan->no_reg ?? '-',
                            'nama_pasien' => $transaksi->kunjungan->psn->nm_p ?? '-',
                            'jenis' => 'Konsul',
                            'item' => $konsul->tindakanTarif->tindakanQ->tindakan_q_desc ?? $konsul->dskp_kons ?? '-',
                            'dokter' => $konsul->dokter ?? '-',
                            'jumlah' => $konsul->jmlh_kons ?? 0,
                            'biaya' => (float) ($konsul->bya_kons ?? 0),
                            'disc' => $konsul->disc_kons ?? '0%',
                            'subtotal' => (float) (($konsul->jmlh_kons ?? 0) * ($konsul->bya_kons ?? 0)),
                        ];
                    }
                    
                    // Tindaks
                    foreach ($detail->tindaks as $tindak) {
                        $dataDetailTransaksi[] = [
                            'tanggal' => $transaksi->tanggal->format('d/m/Y'),
                            'no_reg' => $transaksi->kunjungan->no_reg ?? '-',
                            'nama_pasien' => $transaksi->kunjungan->psn->nm_p ?? '-',
                            'jenis' => 'Tindakan',
                            'item' => $tindak->dskp_tindak ?? '-',
                            'dokter' => $tindak->dktr_tindak ?? '-',
                            'jumlah' => $tindak->jmlh_tindak ?? 0,
                            'biaya' => (float) ($tindak->bya_tindak ?? 0),
                            'disc' => $tindak->disc_tindak ?? '0%',
                            'subtotal' => (float) (($tindak->jmlh_tindak ?? 0) * ($tindak->bya_tindak ?? 0)),
                        ];
                    }
                    
                    // Alkes
                    foreach ($detail->alkes as $alkes) {
                        $dataDetailTransaksi[] = [
                            'tanggal' => $transaksi->tanggal->format('d/m/Y'),
                            'no_reg' => $transaksi->kunjungan->no_reg ?? '-',
                            'nama_pasien' => $transaksi->kunjungan->psn->nm_p ?? '-',
                            'jenis' => 'Alat Kesehatan',
                            'item' => $alkes->dskp_alkes ?? '-',
                            'dokter' => $alkes->dokter ?? '-',
                            'jumlah' => $alkes->jmlh_alkes ?? 0,
                            'biaya' => (float) ($alkes->bya_alkes ?? 0),
                            'disc' => $alkes->disc_alkes ?? '0%',
                            'subtotal' => (float) (($alkes->jmlh_alkes ?? 0) * ($alkes->bya_alkes ?? 0)),
                        ];
                    }
                    
                    // Rsp
                    foreach ($detail->rsp as $rsp) {
                        $dataDetailTransaksi[] = [
                            'tanggal' => $transaksi->tanggal->format('d/m/Y'),
                            'no_reg' => $transaksi->kunjungan->no_reg ?? '-',
                            'nama_pasien' => $transaksi->kunjungan->psn->nm_p ?? '-',
                            'jenis' => 'Resep',
                            'item' => $rsp->dskp_rsp ?? '-',
                            'dokter' => $rsp->dokter ?? '-',
                            'jumlah' => $rsp->jmlh_rsp ?? 0,
                            'biaya' => (float) ($rsp->bya_rsp ?? 0),
                            'disc' => $rsp->disc_rsp ?? '0%',
                            'subtotal' => (float) (($rsp->jmlh_rsp ?? 0) * ($rsp->bya_rsp ?? 0)),
                        ];
                    }
                    
                    // Lainnya
                    foreach ($detail->lainnyas as $lainnya) {
                        $dataDetailTransaksi[] = [
                            'tanggal' => $transaksi->tanggal->format('d/m/Y'),
                            'no_reg' => $transaksi->kunjungan->no_reg ?? '-',
                            'nama_pasien' => $transaksi->kunjungan->psn->nm_p ?? '-',
                            'jenis' => 'Lainnya',
                            'item' => $lainnya->dskp_lainnya ?? '-',
                            'dokter' => $lainnya->dktr_lainnya ?? '-',
                            'jumlah' => $lainnya->jmlh_lainnya ?? 0,
                            'biaya' => (float) ($lainnya->bya_lainnya ?? 0),
                            'disc' => $lainnya->disc_lainnya ?? '0%',
                            'subtotal' => (float) (($lainnya->jmlh_lainnya ?? 0) * ($lainnya->bya_lainnya ?? 0)),
                        ];
                    }
                }
            }

            $pdf = Pdf::loadView('print.laporan-detail', [
                'filterLabel' => $filterLabel,
                'filterType' => $filterType,
                'totalTransaksi' => $totalTransaksi,
                'totalPemasukan' => $totalPemasukan,
                'totalItemTerjual' => $totalItemTerjual,
                'dataDetailTransaksi' => $dataDetailTransaksi,
                'tanggalCetak' => Carbon::now()->format('d F Y H:i:s'),
            ])
            ->setPaper('A4', 'landscape')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'DejaVu Sans',
            ]);

            $filename = 'laporan-perincian-' . strtolower(str_replace(' ', '-', $filterLabel)) . '.pdf';
            return $pdf->download($filename);
        }

        // Export summary (default)
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
        $type = $request->input('type', 'summary'); // 'summary' or 'detail'

        $query = Transaksi::with([
            'kunjungan.psn', 
            'detailTransaksi.konsuls.tindakanTarif.tindakanQ',
            'detailTransaksi.konsuls.eselon.grpEselon',
            'detailTransaksi.tindaks',
            'detailTransaksi.alkes',
            'detailTransaksi.rsp',
            'detailTransaksi.lainnyas'
        ])
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

        if ($type === 'detail') {
            // Export detail perincian
            $data[] = ['LAPORAN PERINCIAN TRANSAKSI'];
            $data[] = ['RS LNG BADAK'];
            $data[] = ['Kel. Satimpo, Kec. Bontang Selatan, Bontang - 75324, Kalimantan Timur'];
            $data[] = [];
            $data[] = ['Periode', $filterLabel];
            $data[] = ['Tanggal Cetak', Carbon::now()->format('d F Y H:i:s')];
            $data[] = [];
            $data[] = ['No', 'Tanggal', 'No. Registrasi', 'Nama Pasien', 'Jenis', 'Item', 'Dokter', 'Jumlah', 'Biaya (Rp)', 'Diskon', 'Subtotal (Rp)'];
            
            $no = 1;
            $totalSubtotal = 0;
            foreach ($transaksis as $transaksi) {
                foreach ($transaksi->detailTransaksi as $detail) {
                    // Konsuls
                    foreach ($detail->konsuls as $konsul) {
                        $subtotal = ($konsul->jmlh_kons ?? 0) * ($konsul->bya_kons ?? 0);
                        $totalSubtotal += $subtotal;
                        $data[] = [
                            $no++,
                            $transaksi->tanggal->format('d/m/Y'),
                            $transaksi->kunjungan->no_reg ?? '-',
                            $transaksi->kunjungan->psn->nm_p ?? '-',
                            'Konsul',
                            $konsul->tindakanTarif->tindakanQ->tindakan_q_desc ?? $konsul->dskp_kons ?? '-',
                            $konsul->dokter ?? '-',
                            number_format($konsul->jmlh_kons ?? 0, 0, ',', '.'),
                            number_format($konsul->bya_kons ?? 0, 0, ',', '.'),
                            $konsul->disc_kons ?? '0%',
                            number_format($subtotal, 0, ',', '.'),
                        ];
                    }
                    
                    // Tindaks
                    foreach ($detail->tindaks as $tindak) {
                        $subtotal = ($tindak->jmlh_tindak ?? 0) * ($tindak->bya_tindak ?? 0);
                        $totalSubtotal += $subtotal;
                        $data[] = [
                            $no++,
                            $transaksi->tanggal->format('d/m/Y'),
                            $transaksi->kunjungan->no_reg ?? '-',
                            $transaksi->kunjungan->psn->nm_p ?? '-',
                            'Tindakan',
                            $tindak->dskp_tindak ?? '-',
                            $tindak->dktr_tindak ?? '-',
                            number_format($tindak->jmlh_tindak ?? 0, 0, ',', '.'),
                            number_format($tindak->bya_tindak ?? 0, 0, ',', '.'),
                            $tindak->disc_tindak ?? '0%',
                            number_format($subtotal, 0, ',', '.'),
                        ];
                    }
                    
                    // Alkes
                    foreach ($detail->alkes as $alkes) {
                        $subtotal = ($alkes->jmlh_alkes ?? 0) * ($alkes->bya_alkes ?? 0);
                        $totalSubtotal += $subtotal;
                        $data[] = [
                            $no++,
                            $transaksi->tanggal->format('d/m/Y'),
                            $transaksi->kunjungan->no_reg ?? '-',
                            $transaksi->kunjungan->psn->nm_p ?? '-',
                            'Alat Kesehatan',
                            $alkes->dskp_alkes ?? '-',
                            $alkes->dokter ?? '-',
                            number_format($alkes->jmlh_alkes ?? 0, 0, ',', '.'),
                            number_format($alkes->bya_alkes ?? 0, 0, ',', '.'),
                            $alkes->disc_alkes ?? '0%',
                            number_format($subtotal, 0, ',', '.'),
                        ];
                    }
                    
                    // Rsp
                    foreach ($detail->rsp as $rsp) {
                        $subtotal = ($rsp->jmlh_rsp ?? 0) * ($rsp->bya_rsp ?? 0);
                        $totalSubtotal += $subtotal;
                        $data[] = [
                            $no++,
                            $transaksi->tanggal->format('d/m/Y'),
                            $transaksi->kunjungan->no_reg ?? '-',
                            $transaksi->kunjungan->psn->nm_p ?? '-',
                            'Resep',
                            $rsp->dskp_rsp ?? '-',
                            $rsp->dokter ?? '-',
                            number_format($rsp->jmlh_rsp ?? 0, 0, ',', '.'),
                            number_format($rsp->bya_rsp ?? 0, 0, ',', '.'),
                            $rsp->disc_rsp ?? '0%',
                            number_format($subtotal, 0, ',', '.'),
                        ];
                    }
                    
                    // Lainnya
                    foreach ($detail->lainnyas as $lainnya) {
                        $subtotal = ($lainnya->jmlh_lainnya ?? 0) * ($lainnya->bya_lainnya ?? 0);
                        $totalSubtotal += $subtotal;
                        $data[] = [
                            $no++,
                            $transaksi->tanggal->format('d/m/Y'),
                            $transaksi->kunjungan->no_reg ?? '-',
                            $transaksi->kunjungan->psn->nm_p ?? '-',
                            'Lainnya',
                            $lainnya->dskp_lainnya ?? '-',
                            $lainnya->dktr_lainnya ?? '-',
                            number_format($lainnya->jmlh_lainnya ?? 0, 0, ',', '.'),
                            number_format($lainnya->bya_lainnya ?? 0, 0, ',', '.'),
                            $lainnya->disc_lainnya ?? '0%',
                            number_format($subtotal, 0, ',', '.'),
                        ];
                    }
                }
            }
            
            // Summary section
            $data[] = [];
            $data[] = ['RINGKASAN'];
            $data[] = ['Total Transaksi', number_format($totalTransaksi, 0, ',', '.') . ' transaksi'];
            $data[] = ['Total Pemasukan', 'Rp ' . number_format($totalPemasukan, 0, ',', '.')];
            $data[] = ['Total Item Terjual', number_format($totalItemTerjual, 0, ',', '.') . ' item'];
            $data[] = ['Total Item Detail', number_format($no - 1, 0, ',', '.') . ' item'];
            $data[] = ['Total Subtotal', 'Rp ' . number_format($totalSubtotal, 0, ',', '.')];
            
            $filename = 'laporan-perincian-' . strtolower(str_replace(' ', '-', $filterLabel)) . '-' . Carbon::now()->format('Y-m-d') . '.csv';
        } else {
            // Export summary (default)
            $data[] = ['LAPORAN TRANSAKSI'];
            $data[] = ['RS LNG BADAK'];
            $data[] = ['Kel. Satimpo, Kec. Bontang Selatan, Bontang - 75324, Kalimantan Timur'];
            $data[] = [];
            $data[] = ['Periode', $filterLabel];
            $data[] = ['Tanggal Cetak', Carbon::now()->format('d F Y H:i:s')];
            $data[] = [];
            $data[] = ['No', 'Tanggal', 'No. Registrasi', 'Nama Pasien', 'Total Biaya (Rp)', 'Jumlah Item', 'Status'];

            $no = 1;
            foreach ($transaksis as $transaksi) {
                $itemCount = $transaksi->detailTransaksi->sum('jumlah');
                $data[] = [
                    $no++,
                    $transaksi->tanggal->format('d/m/Y'),
                    $transaksi->kunjungan->no_reg ?? '-',
                    $transaksi->kunjungan->psn->nm_p ?? '-',
                    number_format($transaksi->total_biaya, 0, ',', '.'),
                    number_format($itemCount, 0, ',', '.'),
                    $transaksi->status ?? '-',
                ];
            }

            // Summary section
            $data[] = [];
            $data[] = ['RINGKASAN'];
            $data[] = ['Total Transaksi', number_format($totalTransaksi, 0, ',', '.') . ' transaksi'];
            $data[] = ['Total Pemasukan', 'Rp ' . number_format($totalPemasukan, 0, ',', '.')];
            $data[] = ['Total Item Terjual', number_format($totalItemTerjual, 0, ',', '.') . ' item'];

            $filename = 'laporan-' . strtolower(str_replace(' ', '-', $filterLabel)) . '-' . Carbon::now()->format('Y-m-d') . '.csv';
        }

        // Change extension to .xls for Excel compatibility
        $filename = str_replace('.csv', '.xls', $filename);
        
        $headers = [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function() use ($data, $type) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Write Excel XML header
            fwrite($file, '<?xml version="1.0" encoding="UTF-8"?>' . "\n");
            fwrite($file, '<?mso-application progid="Excel.Sheet"?>' . "\n");
            fwrite($file, '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"' . "\n");
            fwrite($file, ' xmlns:o="urn:schemas-microsoft-com:office:office"' . "\n");
            fwrite($file, ' xmlns:x="urn:schemas-microsoft-com:office:excel"' . "\n");
            fwrite($file, ' xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"' . "\n");
            fwrite($file, ' xmlns:html="http://www.w3.org/TR/REC-html40">' . "\n");
            fwrite($file, '<Worksheet ss:Name="Laporan">' . "\n");
            fwrite($file, '<Table>' . "\n");
            
            $headerRowIndex = 7; // Header row index after info section
            $summaryStartIndex = null;
            
            // Find summary start index
            foreach ($data as $idx => $row) {
                if (isset($row[0]) && strpos($row[0], 'RINGKASAN') !== false) {
                    $summaryStartIndex = $idx;
                    break;
                }
            }
            
            foreach ($data as $rowIndex => $row) {
                fwrite($file, '<Row>' . "\n");
                
                // Determine row type
                $isTitleRow = $rowIndex < 3; // First 3 rows are title
                $isInfoRow = $rowIndex >= 3 && $rowIndex < $headerRowIndex && !empty($row[0]); // Info rows
                $isHeaderRow = $rowIndex === $headerRowIndex; // Column headers
                $isSummaryRow = $summaryStartIndex !== null && $rowIndex >= $summaryStartIndex; // Summary rows
                $isEmptyRow = empty(array_filter($row)); // Empty row
                
                foreach ($row as $cellIndex => $cell) {
                    if ($isEmptyRow) {
                        // Skip empty cells
                        continue;
                    }
                    
                    $cellValue = htmlspecialchars($cell ?? '', ENT_XML1, 'UTF-8');
                    
                    // Determine cell type - keep as String for formatted numbers
                    $cellType = 'String';
                    
                    // Check if cell should be right-aligned (for numeric columns)
                    $shouldRightAlign = false;
                    if (!$isHeaderRow && !$isTitleRow && !$isInfoRow && !$isSummaryRow) {
                        // Right align numbers in biaya, subtotal, and total biaya columns
                        if ($type === 'detail' && ($cellIndex === 7 || $cellIndex === 8 || $cellIndex === 10)) {
                            $shouldRightAlign = true;
                        } elseif ($type === 'summary' && $cellIndex === 4) {
                            $shouldRightAlign = true;
                        }
                    }
                    
                    // Style for different row types
                    $styleId = '';
                    if ($isTitleRow) {
                        $styleId = 'ss:StyleID="HeaderTitle"';
                    } elseif ($isHeaderRow) {
                        $styleId = 'ss:StyleID="Header"';
                    } elseif ($isSummaryRow) {
                        $styleId = 'ss:StyleID="Summary"';
                    } elseif ($shouldRightAlign) {
                        // Right align numbers
                        $styleId = 'ss:StyleID="Number"';
                    }
                    
                    fwrite($file, '<Cell ' . $styleId . '><Data ss:Type="' . $cellType . '">' . $cellValue . '</Data></Cell>' . "\n");
                }
                
                fwrite($file, '</Row>' . "\n");
            }
            
            fwrite($file, '</Table>' . "\n");
            
            // Add styles
            fwrite($file, '<Styles>' . "\n");
            // Header Title Style
            fwrite($file, '<Style ss:ID="HeaderTitle">' . "\n");
            fwrite($file, '<Font ss:Bold="1" ss:Size="14"/>' . "\n");
            fwrite($file, '<Alignment ss:Horizontal="Center"/>' . "\n");
            fwrite($file, '</Style>' . "\n");
            // Header Style
            fwrite($file, '<Style ss:ID="Header">' . "\n");
            fwrite($file, '<Font ss:Bold="1"/>' . "\n");
            fwrite($file, '<Interior ss:Color="#D3D3D3" ss:Pattern="Solid"/>' . "\n");
            fwrite($file, '<Borders>' . "\n");
            fwrite($file, '<Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>' . "\n");
            fwrite($file, '</Borders>' . "\n");
            fwrite($file, '</Style>' . "\n");
            // Summary Style
            fwrite($file, '<Style ss:ID="Summary">' . "\n");
            fwrite($file, '<Font ss:Bold="1"/>' . "\n");
            fwrite($file, '<Interior ss:Color="#E6E6FA" ss:Pattern="Solid"/>' . "\n");
            fwrite($file, '</Style>' . "\n");
            // Number Style
            fwrite($file, '<Style ss:ID="Number">' . "\n");
            fwrite($file, '<Alignment ss:Horizontal="Right"/>' . "\n");
            fwrite($file, '</Style>' . "\n");
            fwrite($file, '</Styles>' . "\n");
            
            fwrite($file, '</Worksheet>' . "\n");
            fwrite($file, '</Workbook>' . "\n");
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

