<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Dokter;
use App\Models\Kunjungan;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class DokterController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $dokters = Dokter::query()
            ->with('user')
            ->when($search, function ($query, $search) {
                $query->where('nama_dokter', 'like', "%{$search}%")
                      ->orWhereHas('user', function ($userQuery) use ($search) {
                          $userQuery->where('name', 'like', "%{$search}%")
                                   ->orWhere('email', 'like', "%{$search}%");
                      });
            })
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('dokter/index', [
            'dokters' => $dokters,
            'filters' => $request->only('search'),
        ]);
    }

    public function show($id)
    {
        $dokter = Dokter::with('user')->findOrFail($id);
        return Inertia::render('dokter/show', [
            'dokter' => $dokter
        ]);
    }

    public function create()
    {
        return Inertia::render('dokter/create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
            'nama_dokter' => 'required|string|max:255',
            'aktif' => 'required|string|in:Ya,Tidak',
        ]);

        // Create user with dokter role
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'dokter',
        ]);

        // Create dokter record
        Dokter::create([
            'user_id' => $user->id,
            'nama_dokter' => $validated['nama_dokter'],
            'aktif' => $validated['aktif'],
            'role' => 'dokter',
        ]);

        return redirect()->route('dokter.index')->with('success', 'Dokter berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $dokter = Dokter::with('user')->findOrFail($id);
        return Inertia::render('dokter/edit', [
            'dokter' => $dokter
        ]);
    }

    public function update(Request $request, $id)
    {
        $dokter = Dokter::with('user')->findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $dokter->user_id,
            'password' => 'nullable|string|min:6',
            'nama_dokter' => 'required|string|max:255',
            'aktif' => 'required|string|in:Ya,Tidak',
        ]);

        // Update user
        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        if (!empty($validated['password'])) {
            $userData['password'] = Hash::make($validated['password']);
        }

        $dokter->user->update($userData);

        // Update dokter
        $dokter->update([
            'nama_dokter' => $validated['nama_dokter'],
            'aktif' => $validated['aktif'],
        ]);

        return redirect()->route('dokter.index')->with('success', 'Dokter berhasil diperbarui!');
    }

    public function pasienKunjungan(Request $request)
    {
        $search = $request->input('search');
        $filterPenjamin = $request->input('penjamin');
        $filterPerawatan = $request->input('perawatan');
        $filterKunjungan = $request->input('kunjungan');
        $filterPoli = $request->input('poli');
<<<<<<< HEAD
        $filterDate = $request->input('date'); // Optional date filter
=======
        $showRiwayat = $request->input('riwayat', false); // Default false = hanya kunjungan aktif
>>>>>>> be1d14e9aa3d61495cd14a9a6dde029795e626e6

        $query = Kunjungan::with([
            'psn',
            'transaksi.detailTransaksi.konsuls',
            'transaksi.detailTransaksi.tindaks',
            'transaksi.detailTransaksi.alkes',
            'transaksi.detailTransaksi.rsp',
            'transaksi.detailTransaksi.lainnyas'
        ]);

<<<<<<< HEAD
        // Default filter: Only show today's data if no date filter is provided
        // If user provides a date filter, use it; otherwise default to today
        if ($filterDate) {
            // User provided a specific date
            $query->whereDate('tgl_reg', $filterDate);
        } else {
            // Default: Only show today's data
            $query->whereDate('tgl_reg', now()->toDateString());
=======
        // Filter berdasarkan status: aktif (belum selesai) atau riwayat (sudah selesai)
        // Kunjungan selesai = sudah ada transaksi
        if ($showRiwayat) {
            // Riwayat: hanya kunjungan yang sudah ada transaksi
            $query->whereHas('transaksi');
        } else {
            // Aktif: hanya kunjungan yang belum ada transaksi
            // Setelah kunjungan selesai (ada transaksi), pasien akan secara otomatis
            // dipindahkan ke riwayat dan tidak muncul di daftar aktif
            $query->whereDoesntHave('transaksi');
>>>>>>> be1d14e9aa3d61495cd14a9a6dde029795e626e6
        }

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

        // Filter by poli (match poli_desc with kunjungan column)
        if ($filterPoli) {
            $query->where('kunjungan', $filterPoli);
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
        $kunjungan->setCollection($transformedKunjungan);

        // Get unique values for filters
        $uniquePenjamin = Kunjungan::distinct()->pluck('penjamin')->filter();
        $uniquePerawatan = Kunjungan::distinct()->pluck('perawatan')->filter();
        $uniqueKunjungan = Kunjungan::distinct()->pluck('kunjungan')->filter();
        $uniquePoli = \App\Models\Polis::where('aktif', 'Y')->pluck('poli_desc')->filter();

        return Inertia::render('dokter/pasien_kunjungan/index', [
            'pasien' => $kunjungan, // Keep 'pasien' key for frontend compatibility
<<<<<<< HEAD
            'filters' => $request->only(['search', 'penjamin', 'perawatan', 'kunjungan', 'poli', 'date']),
=======
            'filters' => $request->only(['search', 'penjamin', 'perawatan', 'kunjungan', 'poli', 'riwayat']),
            'showRiwayat' => $showRiwayat,
>>>>>>> be1d14e9aa3d61495cd14a9a6dde029795e626e6
            'uniquePenjamin' => $uniquePenjamin,
            'uniquePerawatan' => $uniquePerawatan,
            'uniqueKunjungan' => $uniqueKunjungan,
            'uniquePoli' => $uniquePoli,
            'flash' => [
                'success' => session('success'),
                'error' => session('error')
            ]
        ]);
    }

    public function destroy($id)
    {
        $dokter = Dokter::findOrFail($id);
        
        // Delete user (this will also delete dokter due to cascade)
        $dokter->user->delete();

        return redirect()->route('dokter.index')->with('success', 'Dokter berhasil dihapus!');
    }

    public function poliUmum(Request $request)
    {
        return $this->getPoliPasien($request, 'umum', 'dokter/poli_layanan/poli_layanan');
    }

    public function poliGigi(Request $request)
    {
        return $this->getPoliPasien($request, 'gigi', 'dokter/poli_layanan/poli_layanan');
    }

    public function kia(Request $request)
    {
        return $this->getPoliPasien($request, 'kia', 'dokter/poli_layanan/poli_layanan');
    }

    public function laboratorium(Request $request)
    {
        return $this->getPoliPasien($request, 'laboratorium', 'dokter/poli_layanan/poli_layanan');
    }

    public function apotek(Request $request)
    {
        return $this->getPoliPasien($request, 'apotek', 'dokter/poli_layanan/poli_layanan');
    }

    public function poliLayanan(Request $request)
    {
        try {
            $search = $request->input('search');
            $date = $request->input('date');
            $polis = $request->input('polis');
            $status = $request->input('status');
            
            // Polis-specific filters
            $polisSearch = $request->input('polis_search');
            $polisStatus = $request->input('polis_status');
            $polisSort = $request->input('polis_sort', 'poli_desc');

        // Get kunjungan data with all relationships
        $query = Kunjungan::with([
            'psn',
            'eselon',
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

        // Filter by date
        if ($date) {
            $query->whereDate('tgl_reg', $date);
        }

        // Filter by polis (kunjungan field matches poli_desc from polis table)
        if ($polis) {
            $query->where(function($q) use ($polis) {
                $q->where('kunjungan', $polis)
                  ->orWhere('penjamin', $polis);
            });
        }

        $kunjunganData = $query->orderBy('tgl_reg', 'desc')->paginate(10)->withQueryString();

        // Transform the data to be compatible with frontend expectations
        $transformedKunjungan = $kunjunganData->getCollection()->map(function ($kunjungan) {
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

            // Map penjamin to polis_id for frontend compatibility
            $kunjungan->polis_id = $kunjungan->penjamin;
            
            return $kunjungan;
        });

        // Replace the collection in the paginated result
        $kunjunganData->setCollection($transformedKunjungan);

        // Get polis data with filters
        $polisQuery = \App\Models\Polis::query();
        
        // Apply polis search filter
        if ($polisSearch) {
            $polisQuery->where(function($q) use ($polisSearch) {
                $q->where('poli_desc', 'like', "%{$polisSearch}%")
                  ->orWhere('update_by', 'like', "%{$polisSearch}%");
            });
        }
        
        // Apply polis status filter
        if ($polisStatus) {
            if ($polisStatus === 'active') {
                $polisQuery->where('aktif', 'Y');
            } elseif ($polisStatus === 'inactive') {
                $polisQuery->where('aktif', 'N');
            }
        } else {
            // Default to active polis only if no status filter
            $polisQuery->where('aktif', 'Y');
        }
        
        // Apply sorting
        switch ($polisSort) {
            case 'update_date':
                $polisQuery->orderBy('update_date', 'desc');
                break;
            case 'update_by':
                $polisQuery->orderBy('update_by', 'asc');
                break;
            case 'poli_desc':
            default:
                $polisQuery->orderBy('poli_desc', 'asc');
                break;
        }
        
        $polisData = $polisQuery->get();

            // Add debug information
            $debugInfo = [
                'kunjungan_count' => $kunjunganData->total(),
                'polis_count' => $polisData->count(),
                'filters_applied' => [
                    'search' => $search,
                    'date' => $date,
                    'polis' => $polis,
                    'status' => $status,
                    'polis_search' => $polisSearch,
                    'polis_status' => $polisStatus,
                    'polis_sort' => $polisSort
                ]
            ];

            return Inertia::render('dokter/poli_layanan/poli_layanan', [
                'kunjungan' => $kunjunganData,
                'polis' => $polisData,
                'filters' => $request->only(['search', 'date', 'polis', 'status', 'polis_search', 'polis_status', 'polis_sort']),
                'debug' => $debugInfo,
                'flash' => [
                    'success' => session('success'),
                    'error' => session('error')
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error in poliLayanan: ' . $e->getMessage());
            
            return Inertia::render('dokter/poli_layanan/poli_layanan', [
                'kunjungan' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10),
                'polis' => collect(),
                'filters' => $request->only(['search', 'date', 'polis', 'status', 'polis_search', 'polis_status', 'polis_sort']),
                'debug' => [
                    'error' => $e->getMessage(),
                    'kunjungan_count' => 0,
                    'polis_count' => 0
                ],
                'flash' => [
                    'error' => 'Terjadi kesalahan saat memuat data: ' . $e->getMessage()
                ]
            ]);
        }
    }

    private function getPoliPasien(Request $request, $serviceType, $viewPath)
    {
        $search = $request->input('search');
        $kunjungan = $request->input('kunjungan', $serviceType);
        $date = $request->input('date');

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

        // Filter by service type (kunjungan)
        if ($kunjungan && $kunjungan !== 'all') {
            $query->where('kunjungan', $kunjungan);
        }

        // Filter by date
        if ($date) {
            $query->whereDate('tgl_reg', $date);
        }

        $kunjunganData = $query->orderBy('tgl_reg', 'desc')->paginate(10)->withQueryString();

        // Transform the data to be compatible with frontend expectations
        $transformedKunjungan = $kunjunganData->getCollection()->map(function ($kunjungan) {
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

        // Replace the collection in the paginated result
        $kunjunganData->setCollection($transformedKunjungan);

        return Inertia::render($viewPath, [
            'pasien' => $kunjunganData, // Changed from 'kunjungan' to 'pasien' for Vue component compatibility
            'filters' => $request->only(['search', 'kunjungan', 'date']),
            'serviceType' => $serviceType,
            'flash' => [
                'success' => session('success'),
                'error' => session('error')
            ]
        ]);
    }
}
