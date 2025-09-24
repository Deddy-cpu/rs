<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pasien;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\DetailTransaksi; 
use Illuminate\Support\Facades\DB;


class KasirController extends Controller
{
    // Ambil semua pasien + transaksi + detail
public function index()
{
    $pasien = Pasien::with('transaksi.detail')->get();

    return Inertia::render('kasir/index', [
        'pasien' => $pasien
    ]);
}




public function create()
{
    return Inertia::render('kasir/create');
}   

    // Tambah pasien + transaksi baru
   public function store(Request $request)
{
    $validated = $request->validate([
        // pasien
        'nama_pasien'   => 'required|string|max:255',
       'alamat'       => 'required|string|max:255',
       'perawatan'    => 'required|string|max:255',
       'Penjamin'     => 'required|string|max:255',
        'tanggal'      => 'required|date',


        // transaksi
        'dokter'     => 'required|string|max:255',
        'tindakan'   => 'required|string|max:255',
        'jmlh'       => 'required|integer',
        'dskrps'     => 'nullable|string|max:255',
        
        

        // detail transaksi
       'resep'      => 'nullable|string|max:255',
       'jumlah'     => 'required|integer',
       'deskripsi'  => 'nullable|string|max:255',

    ]);

    DB::transaction(function () use ($validated) {
        $pasien = Pasien::create([
            'nama_pasien'   => $validated['nama_pasien'],
            'alamat'       => $validated['alamat'],
            'perawatan'    => $validated['perawatan'],
            'Penjamin'     => $validated['Penjamin'],
            'tanggal'      => $validated['tanggal'],
        ]); 

        $transaksi = Transaksi::create([
            'pasien_id'     => $pasien->id,
            'dokter'        => $validated['dokter'],
            'tindakan'      => $validated['tindakan'],
            'jmlh'          => $validated['jmlh'],
            'dskrps'        => $validated['dskrps'] ?? null,
        ]);

        DetailTransaksi::create([
            'transaksi_id' => $transaksi->id,
            'resep'        => $validated['resep'] ?? null,
            'jumlah'       => $validated['jumlah'],
            'deskripsi'    => $validated['deskripsi'] ?? null,
        ]);
    });

    return redirect()->route('kasir.index')->with('success', 'Data berhasil disimpan!');
}


    // Ambil detail transaksi berdasarkan pasien
    public function show($id)
    {
        $pasien = Pasien::with(['transaksi.detail'])->findOrFail($id);
        return response()->json($pasien);
    }
}
