<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pasien;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\DetailTransaksi; 
use Illuminate\Support\Facades\DB;




class PasienController extends Controller
{
    // Ambil semua pasien + transaksi + detail

    public function create() 
    {
        return Inertia::render('pasien/create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            // pasien
            'nama_pasien'   => 'required|string|max:255',
           'alamat'       => 'required|string|max:255',
           'perawatan'    => 'required|string|max:255',
           'Penjamin'     => 'required|string|max:255',
            'tanggal'      => 'required|date',
        ]);

        DB::transaction(function () use ($validated) {
            $pasien = Pasien::create([
                'nama_pasien' => $validated['nama_pasien'],
                'alamat'      => $validated['alamat'],
                'perawatan'   => $validated['perawatan'],
                'Penjamin'    => $validated['Penjamin'],
                'tanggal'     => $validated['tanggal'],
            ]);     
        });
        return redirect()->route('kasir.index')->with('success', 'Data pasien dan transaksi berhasil disimpan.');


}




}
