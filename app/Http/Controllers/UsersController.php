<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;
use App\Models\Dokter;

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

    // Show the form for creating a new user
    public function create()
    {
        return Inertia::render('users/create');
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
        ]);

        // Simpan user baru
        $user = User::create([
            'name'     => $validated['name'],
            'role'     => $validated['role'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Jika role adalah dokter, buat record di tabel dokter
        if ($validated['role'] === 'dokter') {
            Dokter::create([
                'user_id' => $user->id,
                'nama_dokter' => $validated['name'],
                'aktif' => 'Ya',
                'role_id' => null, // Karena kita tidak menggunakan roles table untuk dokter
            ]);
        }

        return redirect()->route('users.index')->with('success', 'User created successfully!');
    }

    // Edit user
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return Inertia::render('users/edit', [
            'user' => $user
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
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $oldRole = $user->role;
        $user->update($validated);

        // Handle role changes
        if ($oldRole !== $validated['role']) {
            // Jika role berubah menjadi dokter, buat record dokter
            if ($validated['role'] === 'dokter') {
                // Cek apakah sudah ada record dokter
                $existingDokter = Dokter::where('user_id', $user->id)->first();
                if (!$existingDokter) {
                    Dokter::create([
                        'user_id' => $user->id,
                        'nama_dokter' => $validated['name'],
                        'aktif' => 'Ya',
                        'role_id' => null,
                    ]);
                }
            }
            // Jika role berubah dari dokter ke role lain, hapus record dokter
            elseif ($oldRole === 'dokter') {
                Dokter::where('user_id', $user->id)->delete();
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
