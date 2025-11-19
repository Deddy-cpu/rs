<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;
use App\Models\Dokter;
use App\Models\Polis;

class UsersController extends Controller
{
    // Index dengan search & pagination
    public function index(Request $request)
    {
        $search = $request->input('search');

        $users = User::query()
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('role', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
            })
            ->paginate(5) // tampilkan 5 user per halaman
            ->withQueryString();

        return Inertia::render('users/index', [
            'users'   => $users,
            'filters' => $request->only('search'),
        ]);
    }

    // Show a single user
    public function show($id)
    {
        $user = User::findOrFail($id);
        return response()->json($user);
    }

    // API endpoint to get all users for modal selection
    public function apiUsers()
    {
        $users = User::select('id', 'name', 'email', 'role')
            ->where('role', '!=', 'admin') // Exclude admin users
            ->orderBy('name')
            ->get();
            
        return response()->json([
            'users' => $users
        ]);
    }

    // Show the form for creating a new user
    public function create()
    {
        $polis = Polis::where('aktif', 'Y')
            ->orderBy('poli_desc', 'asc')
            ->get(['id', 'poli_desc']);
        
        return Inertia::render('users/create', [
            'polis' => $polis
        ]);
    }

    // Store a new user
    public function store(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'role'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
            'ruangan'  => 'nullable|string|max:255',
            'ruangan_id' => 'nullable|string|max:255',
        ]);

        // Simpan user baru
        $user = User::create([
            'name'     => $validated['name'],
            'role'     => $validated['role'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Jika role adalah dokter atau perawat, buat record di tabel dokter
        if ($validated['role'] === 'dokter' || $validated['role'] === 'perawat') {
            // Jika ruangan_id ada, ambil nama poli dari table polis
            $ruanganValue = null;
            if (!empty($validated['ruangan_id'])) {
                $poli = Polis::find($validated['ruangan_id']);
                $ruanganValue = $poli ? $poli->poli_desc : $validated['ruangan'];
            } else {
                $ruanganValue = $validated['ruangan'] ?? null;
            }
            
            Dokter::create([
                'user_id' => $user->id,
                'nama_dokter' => $validated['name'],
                'aktif' => 'Ya',
                'role' => $validated['role'],
                'ruangan' => $ruanganValue,
            ]);
        }

        return redirect()->route('users.index')->with('success', 'User created successfully!');
    }

    // Edit user
    public function edit($id)
    {
        $user = User::with('dokter')->findOrFail($id);
        $polis = Polis::where('aktif', 'Y')
            ->orderBy('poli_desc', 'asc')
            ->get(['id', 'poli_desc']);
        
        return Inertia::render('users/edit', [
            'user' => $user,
            'polis' => $polis
        ]);
    }

    // Update user
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'role'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'string', 'min:6'],
            'ruangan'  => ['nullable', 'string', 'max:255'],
            'ruangan_id' => ['nullable', 'string', 'max:255'],
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $oldRole = $user->role;
        $user->update($validated);

        // Handle role changes and ruangan update
        $existingDokter = Dokter::where('user_id', $user->id)->first();
        
        if ($validated['role'] === 'dokter' || $validated['role'] === 'perawat') {
            // Jika ruangan_id ada, ambil nama poli dari table polis
            $ruanganValue = null;
            if (!empty($validated['ruangan_id'])) {
                $poli = Polis::find($validated['ruangan_id']);
                $ruanganValue = $poli ? $poli->poli_desc : $validated['ruangan'];
            } else {
                $ruanganValue = $validated['ruangan'] ?? null;
            }
            
            // Jika role adalah dokter atau perawat, update atau create record dokter
            if ($existingDokter) {
                $existingDokter->update([
                    'nama_dokter' => $validated['name'],
                    'role' => $validated['role'],
                    'ruangan' => $ruanganValue,
                ]);
            } else {
                Dokter::create([
                    'user_id' => $user->id,
                    'nama_dokter' => $validated['name'],
                    'aktif' => 'Ya',
                    'role' => $validated['role'],
                    'ruangan' => $ruanganValue,
                ]);
            }
        } elseif ($oldRole === 'dokter' || $oldRole === 'perawat') {
            // Jika role berubah dari dokter/perawat ke role lain, hapus record dokter
            if ($existingDokter) {
                $existingDokter->delete();
            }
        }

        return redirect()->route('users.index')->with('success', 'User updated successfully!');
    }

    // Delete user
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        // Hapus record dokter jika ada
        if ($user->role === 'dokter') {
            Dokter::where('user_id', $user->id)->delete();
        }
        
        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully!');
    }
}
