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

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'dokter',
        ]);

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

        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        if (!empty($validated['password'])) {
            $userData['password'] = Hash::make($validated['password']);
        }

        $dokter->user->update($userData);

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
        $filterDate = $request->input('date');
        $showRiwayat = filter_var($request->input('riwayat', false), FILTER_VALIDATE_BOOLEAN);

      $query = Kunjungan::with([
            'psn',
            'transaksi.detailTransaksi.konsuls',
            'transaksi.detailTransaksi.tindaks',
            'transaksi.detailTransaksi.alkes',
            'transaksi.detailTransaksi.rsp',
            'transaksi.detailTransaksi.lainnyas'
        ]);

        // Always filter on tanggal registrasi, default to today if no filter
        if ($filterDate) {
            $query->whereDate('tgl_reg', $filterDate);
        } else {
            $query->whereDate('tgl_reg', now()->toDateString());
        }

        if ($showRiwayat) {
            $query->whereHas('transaksi');
        } else {
            $query->whereDoesntHave('transaksi');
        }

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

        if ($filterPenjamin) {
            $query->where('penjamin', $filterPenjamin);
        }

        if ($filterPerawatan) {
            $query->where('perawatan', $filterPerawatan);
        }

        if ($filterKunjungan) {
            $query->where('kunjungan', $filterKunjungan);
        }

        if ($filterPoli) {
            $query->where('kunjungan', $filterPoli);
        }

        $kunjungan = $query->orderBy('tgl_reg', 'desc')->paginate(10)->withQueryString();

        $transformedKunjungan = $kunjungan->getCollection()->map(function ($kunjungan) {
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

        $kunjungan->setCollection($transformedKunjungan);

        $uniquePenjamin = Kunjungan::distinct()->pluck('penjamin')->filter();
        $uniquePerawatan = Kunjungan::distinct()->pluck('perawatan')->filter();
        $uniqueKunjungan = Kunjungan::distinct()->pluck('kunjungan')->filter();
        $uniquePoli = \App\Models\Polis::where('aktif', 'Y')->pluck('poli_desc')->filter();

        return Inertia::render('dokter/pasien_kunjungan/index', [
            'pasien' => $kunjungan,
            'filters' => $request->only(['search', 'penjamin', 'perawatan', 'kunjungan', 'poli', 'date', 'riwayat']),
            'showRiwayat' => $showRiwayat,
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

        // Delete user (causes cascade dokter deletion)
        if ($dokter->user) {
            $dokter->user->delete();
        }

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

            $polisSearch = $request->input('polis_search');
            $polisStatus = $request->input('polis_status');
            $polisSort = $request->input('polis_sort', 'poli_desc');

            // Default to today's date if no date is provided
            if (!$date) {
                $date = now()->format('Y-m-d');
            }

            $query = Kunjungan::with([
                'psn',
                'eselon',
                'transaksi.detailTransaksi.konsuls',
                'transaksi.detailTransaksi.tindaks',
                'transaksi.detailTransaksi.alkes',
                'transaksi.detailTransaksi.rsp',
                'transaksi.detailTransaksi.lainnyas'
            ]);

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

            // Always filter by date (default to today)
            $query->whereDate('tgl_reg', $date);

            if ($polis) {
                $query->where(function($q) use ($polis) {
                    $q->where('kunjungan', $polis)
                      ->orWhere('penjamin', $polis);
                });
            }

            $kunjunganData = $query->orderBy('tgl_reg', 'desc')->paginate(10)->withQueryString();

            $transformedKunjungan = $kunjunganData->getCollection()->map(function ($kunjungan) {
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

                $kunjungan->polis_id = $kunjungan->penjamin;

                return $kunjungan;
            });

            $kunjunganData->setCollection($transformedKunjungan);

            $polisQuery = \App\Models\Polis::query();

            if ($polisSearch) {
                $polisQuery->where(function($q) use ($polisSearch) {
                    $q->where('poli_desc', 'like', "%{$polisSearch}%")
                      ->orWhere('update_by', 'like', "%{$polisSearch}%");
                });
            }

            if ($polisStatus) {
                if ($polisStatus === 'active') {
                    $polisQuery->where('aktif', 'Y');
                } elseif ($polisStatus === 'inactive') {
                    $polisQuery->where('aktif', 'N');
                }
            } else {
                $polisQuery->where('aktif', 'Y');
            }

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
                'filters' => array_merge($request->only(['search', 'date', 'polis', 'status', 'polis_search', 'polis_status', 'polis_sort']), ['date' => $date]),
                'debug' => $debugInfo,
                'flash' => [
                    'success' => session('success'),
                    'error' => session('error')
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error in poliLayanan: ' . $e->getMessage());

            // Default to today's date if no date is provided (error case)
            $defaultDate = $request->input('date') ?: now()->format('Y-m-d');
            
            return Inertia::render('dokter/poli_layanan/poli_layanan', [
                'kunjungan' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10),
                'polis' => collect(),
                'filters' => array_merge($request->only(['search', 'date', 'polis', 'status', 'polis_search', 'polis_status', 'polis_sort']), ['date' => $defaultDate]),
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

        if ($kunjungan && $kunjungan !== 'all') {
            $query->where('kunjungan', $kunjungan);
        }

        if ($date) {
            $query->whereDate('tgl_reg', $date);
        }

        $kunjunganData = $query->orderBy('tgl_reg', 'desc')->paginate(10)->withQueryString();

        $transformedKunjungan = $kunjunganData->getCollection()->map(function ($kunjungan) {
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

        $kunjunganData->setCollection($transformedKunjungan);

        return Inertia::render($viewPath, [
            'pasien' => $kunjunganData,
            'filters' => $request->only(['search', 'kunjungan', 'date']),
            'serviceType' => $serviceType,
            'flash' => [
                'success' => session('success'),
                'error' => session('error')
            ]
        ]);
    }
}
