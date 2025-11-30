<?php

namespace App\Http\Controllers;

use App\Models\Kunjungan;
use App\Models\Transaksi;
use App\Models\Psn;
use App\Models\User;
use App\Models\Eselon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Get dashboard data for general users (no specific role)
     * Shows visit history for TV display
     */
    public function index()
    {
        // Daftar poli
        $poliList = [
            'Semua Poli',
            'Apotek',
            'KIA (Kesehatan Ibu dan Anak)',
            'Laboratorium',
            'Poli Bedah',
            'Poli Gigi',
            'Poli Jantung',
            'Poli Kulit dan Kelamin',
            'Poli Mata',
            'Poli THT',
            'Poli Umum',
        ];

        // Kunjungan hari ini
        $kunjunganHariIni = Kunjungan::whereDate('tgl_reg', today())
            ->with('psn')
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($kunjungan, $index) {
                // Determine status based on status_kunjungan field
                $statusRaw = $kunjungan->status_kunjungan ?? 'pending';
                $statusLabel = match($statusRaw) {
                    'completed' => 'Selesai',
                    'in_progress' => 'Sedang Dilayani',
                    default => 'Menunggu'
                };
                
                return [
                    'id' => $kunjungan->id,
                    'no_antrian' => $index + 1,
                    'nama' => $kunjungan->nm_p,
                    'poli' => $kunjungan->kunjungan,
                    'status' => $statusLabel,
                    'status_raw' => $statusRaw,
                    'waktu' => $kunjungan->created_at->format('H:i'),
                ];
            });

        // Riwayat kunjungan (hari-hari sebelumnya, dikelompokkan per hari)
        $riwayatKunjungan = Kunjungan::whereDate('tgl_reg', '<', today())
            ->with('psn')
            ->orderBy('tgl_reg', 'desc')
            ->orderBy('created_at', 'asc')
            ->get()
            ->groupBy(function ($kunjungan) {
                return $kunjungan->tgl_reg->format('Y-m-d');
            })
            ->map(function ($kunjunganHari, $tanggal) {
                return [
                    'tanggal' => $tanggal,
                    'tanggalFormatted' => Carbon::parse($tanggal)->locale('id')->isoFormat('dddd, D MMMM YYYY'),
                    'total' => $kunjunganHari->count(),
                    'kunjungan' => $kunjunganHari->map(function ($kunjungan, $index) {
                        $statusRaw = $kunjungan->status_kunjungan ?? 'pending';
                        $statusLabel = match($statusRaw) {
                            'completed' => 'Selesai',
                            'in_progress' => 'Sedang Dilayani',
                            default => 'Menunggu'
                        };
                        
                        return [
                            'id' => $kunjungan->id,
                            'no_antrian' => $index + 1,
                            'nama' => $kunjungan->nm_p,
                            'poli' => $kunjungan->kunjungan,
                            'status' => $statusLabel,
                            'status_raw' => $statusRaw,
                            'waktu' => $kunjungan->created_at->format('H:i'),
                        ];
                    })->values(),
                ];
            })
            ->values()
            ->take(14); // Ambil 14 hari terakhir

        return Inertia::render('Dashboard', [
            'kunjunganHariIni' => $kunjunganHariIni,
            'riwayatKunjungan' => $riwayatKunjungan,
            'totalHariIni' => $kunjunganHariIni->count(),
            'poliList' => $poliList,
        ]);
    }

    /**
     * Get admin dashboard data (monitoring only - read-only)
     */
    public function admin()
    {
        $stats = [
            'totalUsers' => User::count(),
            'activeUsers' => User::whereNotNull('email_verified_at')->count(),
            'doctors' => User::where('role', 'dokter')->count(),
            'cashiers' => User::where('role', 'kasir')->count(),
            'pendaftaran' => User::where('role', 'pendaftaran')->count(),
        ];

        // Total pasien
        $totalPasien = Psn::count();
        $pasienBulanIni = Psn::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // Total kunjungan
        $totalKunjungan = Kunjungan::count();
        $kunjunganHariIni = Kunjungan::whereDate('tgl_reg', today())->count();
        $kunjunganBulanIni = Kunjungan::whereMonth('tgl_reg', now()->month)
            ->whereYear('tgl_reg', now()->year)
            ->count();

        // Total transaksi
        $totalTransaksi = Transaksi::count();
        $transaksiHariIni = Transaksi::whereDate('tanggal', today())->count();
        $pendapatanHariIni = (float) Transaksi::whereDate('tanggal', today())
            ->sum('total_biaya');
        $pendapatanBulanIni = (float) Transaksi::whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->sum('total_biaya');

        // Distribusi pasien per poli (hari ini dengan perbandingan kemarin)
        $distribusiPoli = Kunjungan::whereDate('tgl_reg', today())
            ->select('kunjungan', DB::raw('count(*) as total'))
            ->groupBy('kunjungan')
            ->orderBy('total', 'desc')
            ->get()
            ->map(function ($item) {
                // Hitung perbandingan dengan hari sebelumnya
                $kemarin = Kunjungan::whereDate('tgl_reg', today()->subDay())
                    ->where('kunjungan', $item->kunjungan)
                    ->count();
                
                $selisih = $item->total - $kemarin;
                $persentase = $kemarin > 0 ? round(($selisih / $kemarin) * 100, 1) : ($item->total > 0 ? 100 : 0);
                
                return [
                    'poli' => $item->kunjungan,
                    'total' => $item->total,
                    'kemarin' => $kemarin,
                    'selisih' => $selisih,
                    'persentase' => $persentase,
                    'naik' => $selisih > 0,
                    'turun' => $selisih < 0
                ];
            });

        // Konsultasi per jam (hari ini dengan perbandingan kemarin)
        $konsultasiPerJam = [];
        for ($hour = 8; $hour <= 16; $hour++) {
            $countHariIni = Kunjungan::whereDate('tgl_reg', today())
                ->whereRaw('HOUR(created_at) = ?', [$hour])
                ->count();
            
            $countKemarin = Kunjungan::whereDate('tgl_reg', today()->subDay())
                ->whereRaw('HOUR(created_at) = ?', [$hour])
                ->count();
            
            $selisih = $countHariIni - $countKemarin;
            $persentase = $countKemarin > 0 ? round(($selisih / $countKemarin) * 100, 1) : ($countHariIni > 0 ? 100 : 0);
            
            $konsultasiPerJam[] = [
                'jam' => str_pad($hour, 2, '0', STR_PAD_LEFT) . ':00',
                'total' => $countHariIni,
                'kemarin' => $countKemarin,
                'selisih' => $selisih,
                'persentase' => $persentase,
                'naik' => $selisih > 0,
                'turun' => $selisih < 0
            ];
        }

        // Transaksi per jam hari ini dengan perbandingan kemarin
        $transaksiPerJam = [];
        for ($hour = 8; $hour <= 16; $hour++) {
            $countHariIni = Transaksi::whereDate('tanggal', today())
                ->whereRaw('HOUR(created_at) = ?', [$hour])
                ->count();
            
            $countKemarin = Transaksi::whereDate('tanggal', today()->subDay())
                ->whereRaw('HOUR(created_at) = ?', [$hour])
                ->count();
            
            $selisih = $countHariIni - $countKemarin;
            $persentase = $countKemarin > 0 ? round(($selisih / $countKemarin) * 100, 1) : ($countHariIni > 0 ? 100 : 0);
            
            $transaksiPerJam[] = [
                'jam' => str_pad($hour, 2, '0', STR_PAD_LEFT) . ':00',
                'total' => $countHariIni,
                'kemarin' => $countKemarin,
                'selisih' => $selisih,
                'persentase' => $persentase,
                'naik' => $selisih > 0,
                'turun' => $selisih < 0
            ];
        }

        // Pendaftaran per hari dalam seminggu (dengan perbandingan minggu lalu)
        $pendaftaranPerHari = [];
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
        $startOfWeek = now()->startOfWeek();
        $startOfLastWeek = now()->subWeek()->startOfWeek();
        for ($i = 0; $i < 7; $i++) {
            $date = $startOfWeek->copy()->addDays($i);
            $dateLastWeek = $startOfLastWeek->copy()->addDays($i);
            
            $count = Kunjungan::whereDate('tgl_reg', $date)->count();
            $countLastWeek = Kunjungan::whereDate('tgl_reg', $dateLastWeek)->count();
            
            $selisih = $count - $countLastWeek;
            $persentase = $countLastWeek > 0 ? round(($selisih / $countLastWeek) * 100, 1) : ($count > 0 ? 100 : 0);
            
            $pendaftaranPerHari[] = [
                'hari' => $days[$i],
                'total' => $count,
                'mingguLalu' => $countLastWeek,
                'selisih' => $selisih,
                'persentase' => $persentase,
                'naik' => $selisih > 0,
                'turun' => $selisih < 0
            ];
        }

        // Recent activities
        $recentKunjungan = Kunjungan::with('psn')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($kunjungan) {
                return [
                    'id' => $kunjungan->id,
                    'pasien' => $kunjungan->nm_p,
                    'poli' => $kunjungan->kunjungan,
                    'waktu' => $kunjungan->created_at->format('H:i'),
                    'tanggal' => $kunjungan->created_at->format('d M Y'),
                ];
            });

        return Inertia::render('AdminDashboard', [
            'stats' => $stats,
            'totalPasien' => $totalPasien,
            'pasienBulanIni' => $pasienBulanIni,
            'totalKunjungan' => $totalKunjungan,
            'kunjunganHariIni' => $kunjunganHariIni,
            'kunjunganBulanIni' => $kunjunganBulanIni,
            'totalTransaksi' => $totalTransaksi,
            'transaksiHariIni' => $transaksiHariIni,
            'pendapatanHariIni' => $pendapatanHariIni,
            'pendapatanBulanIni' => $pendapatanBulanIni,
            'distribusiPoli' => $distribusiPoli,
            'konsultasiPerJam' => $konsultasiPerJam,
            'transaksiPerJam' => $transaksiPerJam,
            'pendaftaranPerHari' => $pendaftaranPerHari,
            'kunjunganKemarin' => Kunjungan::whereDate('tgl_reg', today()->subDay())->count(),
            'transaksiKemarin' => Transaksi::whereDate('tanggal', today()->subDay())->count(),
            'recentKunjungan' => $recentKunjungan,
        ]);
    }

    /**
     * Get dokter dashboard data
     * Data akan otomatis update ketika:
     * - Ada kunjungan baru dibuat → Total Pasien & Pasien Hari Ini bertambah
     * - Ada transaksi dibuat (konsultasi selesai) → Konsultasi Selesai bertambah, Antrian Menunggu berkurang
     */
    public function dokter()
    {
        // For now, show all kunjungan data since we don't have clear dokter-eselon relationship
        // In production, you would filter by dokter's eselon_id
        
        // Total pasien (unique patients yang pernah ada kunjungannya - bulan ini)
        // Akan bertambah ketika ada kunjungan baru dengan pasien yang belum pernah ada di bulan ini
        $totalPasien = Kunjungan::whereMonth('tgl_reg', now()->month)
            ->whereYear('tgl_reg', now()->year)
            ->distinct()
            ->count('psn_id');
        
        // Pasien hari ini (unique patients yang datang hari ini)
        // Akan bertambah ketika ada kunjungan baru hari ini dengan pasien yang belum datang hari ini
        $pasienHariIni = Kunjungan::whereDate('tgl_reg', today())
            ->distinct()
            ->count('psn_id');

        // Konsultasi selesai hari ini (kunjungan hari ini yang sudah ada transaksi)
        // Akan bertambah ketika ada transaksi dibuat untuk kunjungan hari ini
        $konsultasiSelesai = Kunjungan::whereDate('tgl_reg', today())
            ->whereHas('transaksi')
            ->count();

        // Antrian menunggu (kunjungan hari ini yang belum ada transaksi)
        // Akan berkurang ketika ada transaksi dibuat (konsultasi selesai)
        $antrianMenunggu = Kunjungan::whereDate('tgl_reg', today())
            ->whereDoesntHave('transaksi')
            ->count();

        // Distribusi pasien per poli (hari ini)
        $poliData = Kunjungan::whereDate('tgl_reg', today())
            ->select('kunjungan', DB::raw('count(*) as total'))
            ->groupBy('kunjungan')
            ->orderBy('total', 'desc')
            ->get();

        $poliLabels = $poliData->pluck('kunjungan')->toArray();
        $poliCounts = $poliData->pluck('total')->toArray();

        // Konsultasi per jam hari ini dengan perbandingan kemarin
        $konsultasiPerJam = [];
        $konsultasiLabels = [];
        for ($hour = 8; $hour <= 16; $hour++) {
            $countHariIni = Kunjungan::whereDate('tgl_reg', today())
                ->whereRaw('HOUR(created_at) = ?', [$hour])
                ->count();
            
            $countKemarin = Kunjungan::whereDate('tgl_reg', today()->subDay())
                ->whereRaw('HOUR(created_at) = ?', [$hour])
                ->count();
            
            $konsultasiPerJam[] = [
                'jam' => str_pad($hour, 2, '0', STR_PAD_LEFT) . ':00',
                'total' => $countHariIni,
                'kemarin' => $countKemarin,
                'selisih' => $countHariIni - $countKemarin,
                'persentase' => $countKemarin > 0 ? round((($countHariIni - $countKemarin) / $countKemarin) * 100, 1) : ($countHariIni > 0 ? 100 : 0),
                'naik' => $countHariIni > $countKemarin,
                'turun' => $countHariIni < $countKemarin
            ];
            $konsultasiLabels[] = str_pad($hour, 2, '0', STR_PAD_LEFT) . ':00';
        }

        return Inertia::render('DokterDashboard', [
            'stats' => [
                'totalPasien' => $totalPasien,
                'pasienHariIni' => $pasienHariIni,
                'konsultasiSelesai' => $konsultasiSelesai,
                'antrianMenunggu' => $antrianMenunggu,
            ],
            'poliChartData' => [
                'labels' => $poliLabels ?: ['Poli Umum', 'Poli Gigi', 'KIA', 'Laboratorium', 'Apotek'],
                'data' => $poliCounts ?: [0, 0, 0, 0, 0],
            ],
            'konsultasiChartData' => [
                'labels' => $konsultasiLabels,
                'data' => array_column($konsultasiPerJam, 'total'),
                'detail' => $konsultasiPerJam, // Data lengkap untuk indikator
            ],
        ]);
    }

    /**
     * Get pendaftaran dashboard data
     */
    public function pendaftaran()
    {
        // Pendaftaran hari ini
        $pendaftaranHariIni = Kunjungan::whereDate('tgl_reg', today())->count();
        
        // Pendaftaran bulan ini
        $pendaftaranBulanIni = Kunjungan::whereMonth('tgl_reg', now()->month)
            ->whereYear('tgl_reg', now()->year)
            ->count();

        // Pasien baru vs lama (asumsi pasien baru adalah yang baru dibuat bulan ini)
        $pasienBaru = Psn::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        
        $pasienLama = Kunjungan::whereDate('tgl_reg', today())
            ->whereHas('psn', function($query) {
                $query->whereMonth('created_at', '<', now()->month)
                    ->orWhereYear('created_at', '<', now()->year);
            })
            ->count();

        // Pendaftaran per hari dalam seminggu
        $pendaftaranPerHari = [];
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
        $startOfWeek = now()->startOfWeek();
        for ($i = 0; $i < 7; $i++) {
            $date = $startOfWeek->copy()->addDays($i);
            $count = Kunjungan::whereDate('tgl_reg', $date)->count();
            $pendaftaranPerHari[] = [
                'hari' => $days[$i],
                'total' => $count
            ];
        }

        // Jenis pasien (berdasarkan penjamin)
        $jenisPasienData = Kunjungan::select('penjamin', DB::raw('count(*) as total'))
            ->whereNotNull('penjamin')
            ->groupBy('penjamin')
            ->get();

        $jenisPasienLabels = $jenisPasienData->pluck('penjamin')->toArray();
        $jenisPasienCounts = $jenisPasienData->pluck('total')->toArray();

        // Antrian pasien hari ini (hanya hari ini)
        $antrianPasien = Kunjungan::whereDate('tgl_reg', today())
            ->with('psn')
            ->orderBy('created_at', 'asc')
            ->limit(10)
            ->get()
            ->map(function ($kunjungan, $index) {
                return [
                    'no' => 'A' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                    'nama' => $kunjungan->nm_p,
                    'poli' => $kunjungan->kunjungan,
                    'status' => $kunjungan->transaksi()->exists() ? 'Selesai' : 'Menunggu',
                    'waktu' => $kunjungan->created_at->format('H:i'),
                    'tanggal' => $kunjungan->tgl_reg->format('Y-m-d'),
                ];
            });

        // Riwayat kunjungan (hari-hari sebelumnya, dikelompokkan per hari)
        $riwayatKunjungan = Kunjungan::whereDate('tgl_reg', '<', today())
            ->with('psn')
            ->orderBy('tgl_reg', 'desc')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function ($kunjungan) {
                return $kunjungan->tgl_reg->format('Y-m-d');
            })
            ->map(function ($kunjunganHari, $tanggal) {
                return [
                    'tanggal' => $tanggal,
                    'tanggalFormatted' => \Carbon\Carbon::parse($tanggal)->locale('id')->isoFormat('dddd, D MMMM YYYY'),
                    'total' => $kunjunganHari->count(),
                    'kunjungan' => $kunjunganHari->map(function ($kunjungan, $index) {
                        return [
                            'id' => $kunjungan->id,
                            'no' => 'A' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                            'nama' => $kunjungan->nm_p,
                            'poli' => $kunjungan->kunjungan,
                            'status' => $kunjungan->transaksi()->exists() ? 'Selesai' : 'Menunggu',
                            'waktu' => $kunjungan->created_at->format('H:i'),
                            'no_reg' => $kunjungan->no_reg,
                        ];
                    })->values(),
                ];
            })
            ->values()
            ->take(7); // Ambil 7 hari terakhir

        return Inertia::render('PendaftaranDashboard', [
            'stats' => [
                'pendaftaranHariIni' => $pendaftaranHariIni,
                'pendaftaranBulanIni' => $pendaftaranBulanIni,
                'pasienBaru' => $pasienBaru,
                'pasienLama' => $pasienLama,
            ],
            'pendaftaranChartData' => [
                'labels' => array_column($pendaftaranPerHari, 'hari'),
                'data' => array_column($pendaftaranPerHari, 'total'),
                'detail' => $pendaftaranPerHari, // Data lengkap untuk indikator
            ],
            'jenisPasienChartData' => [
                'labels' => $jenisPasienLabels ?: ['Pasien Baru', 'Pasien Lama', 'Pasien BPJS', 'Pasien Umum'],
                'data' => $jenisPasienCounts ?: [0, 0, 0, 0],
            ],
            'antrianPasien' => $antrianPasien,
            'riwayatKunjungan' => $riwayatKunjungan,
        ]);
    }

    /**
     * Get kasir dashboard data
     */
    public function kasir()
    {
        // Transaksi hari ini
        $transaksiHariIni = Transaksi::whereDate('tanggal', today())->count();
        
        // Pendapatan hari ini
        $pendapatanHariIni = (float) Transaksi::whereDate('tanggal', today())
            ->sum('total_biaya');

        // Transaksi bulan ini
        $transaksiBulanIni = Transaksi::whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->count();

        // Pendapatan bulan ini
        $pendapatanBulanIni = (float) Transaksi::whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->sum('total_biaya');

        // Transaksi per jam hari ini dengan perbandingan kemarin
        $transaksiPerJam = [];
        $transaksiLabels = [];
        for ($hour = 8; $hour <= 16; $hour++) {
            $countHariIni = Transaksi::whereDate('tanggal', today())
                ->whereRaw('HOUR(created_at) = ?', [$hour])
                ->count();
            
            $countKemarin = Transaksi::whereDate('tanggal', today()->subDay())
                ->whereRaw('HOUR(created_at) = ?', [$hour])
                ->count();
            
            $selisih = $countHariIni - $countKemarin;
            $persentase = $countKemarin > 0 ? round(($selisih / $countKemarin) * 100, 1) : ($countHariIni > 0 ? 100 : 0);
            
            $transaksiPerJam[] = [
                'jam' => str_pad($hour, 2, '0', STR_PAD_LEFT) . ':00',
                'total' => $countHariIni,
                'kemarin' => $countKemarin,
                'selisih' => $selisih,
                'persentase' => $persentase,
                'naik' => $selisih > 0,
                'turun' => $selisih < 0
            ];
            $transaksiLabels[] = str_pad($hour, 2, '0', STR_PAD_LEFT) . ':00';
        }

        // Metode pembayaran (dari detail transaksi jika ada, atau default)
        // Asumsi: kita akan menggunakan status atau field lain jika tersedia
        $metodePembayaran = [
            'Tunai' => Transaksi::whereDate('tanggal', today())->count() * 0.4, // estimasi
            'Debit' => Transaksi::whereDate('tanggal', today())->count() * 0.3,
            'Kredit' => Transaksi::whereDate('tanggal', today())->count() * 0.2,
            'BPJS' => Transaksi::whereDate('tanggal', today())->count() * 0.05,
            'QRIS' => Transaksi::whereDate('tanggal', today())->count() * 0.05,
        ];

        // Recent transactions
        $recentTransactions = Transaksi::with(['kunjungan.psn'])
            ->whereDate('tanggal', today())
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($transaksi) {
                return [
                    'id' => 'TRX' . str_pad($transaksi->id, 3, '0', STR_PAD_LEFT),
                    'pasien' => $transaksi->kunjungan->nm_p ?? 'N/A',
                    'amount' => $transaksi->total_biaya,
                    'method' => 'Tunai', // default, bisa diganti jika ada field
                    'time' => $transaksi->created_at->format('H:i'),
                ];
            });

        return Inertia::render('KasirDashboard', [
            'stats' => [
                'transaksiHariIni' => $transaksiHariIni,
                'pendapatanHariIni' => $pendapatanHariIni,
                'transaksiBulanIni' => $transaksiBulanIni,
                'pendapatanBulanIni' => $pendapatanBulanIni,
            ],
            'transaksiChartData' => [
                'labels' => $transaksiLabels,
                'data' => array_column($transaksiPerJam, 'total'),
                'detail' => $transaksiPerJam, // Data lengkap untuk indikator
            ],
            'pembayaranChartData' => [
                'labels' => array_keys($metodePembayaran),
                'data' => array_values($metodePembayaran),
            ],
            'recentTransactions' => $recentTransactions,
        ]);
    }
}

