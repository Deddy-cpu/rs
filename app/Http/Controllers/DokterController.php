<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Dokter;
use App\Models\Pasien;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;

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
            'role_id' => null,
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

        $query = Pasien::with([
            'psn',
            'konsuls',
            'tindaks', 
            'alkes',
            'rsp',
            'lainnyas'
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

        $pasien = $query->orderBy('tgl_reg', 'desc')->paginate(10)->withQueryString();

        // Get unique values for filters
        $uniquePenjamin = Pasien::distinct()->pluck('penjamin')->filter();
        $uniquePerawatan = Pasien::distinct()->pluck('perawatan')->filter();

        return Inertia::render('dokter/pasien_kunjungan/index', [
            'pasien' => $pasien,
            'filters' => $request->only(['search', 'penjamin', 'perawatan']),
            'uniquePenjamin' => $uniquePenjamin,
            'uniquePerawatan' => $uniquePerawatan,
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
        return $this->getPoliPasien($request, 'umum', 'dokter/poli_layanan/poli_umum');
    }

    public function poliGigi(Request $request)
    {
        return $this->getPoliPasien($request, 'gigi', 'dokter/poli_layanan/poli_gigi');
    }

    public function kia(Request $request)
    {
        return $this->getPoliPasien($request, 'kia', 'dokter/poli_layanan/kia');
    }

    public function laboratorium(Request $request)
    {
        return $this->getPoliPasien($request, 'laboratorium', 'dokter/poli_layanan/lab');
    }

    public function apotek(Request $request)
    {
        return $this->getPoliPasien($request, 'apotek', 'dokter/poli_layanan/apotek');
    }

    private function getPoliPasien(Request $request, $serviceType, $viewPath)
    {
        $search = $request->input('search');
        $kunjungan = $request->input('kunjungan', $serviceType);
        $date = $request->input('date');

        $query = Pasien::with([
            'psn',
            'konsuls',
            'tindaks', 
            'alkes',
            'rsp',
            'lainnyas'
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

        $pasien = $query->orderBy('tgl_reg', 'desc')->paginate(10)->withQueryString();

        return Inertia::render($viewPath, [
            'pasien' => $pasien,
            'filters' => $request->only(['search', 'kunjungan', 'date']),
            'serviceType' => $serviceType,
            'flash' => [
                'success' => session('success'),
                'error' => session('error')
            ]
        ]);
    }
}
