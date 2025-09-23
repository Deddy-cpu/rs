<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pasien;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Inertia\Inertia;

class KasirController extends Controller
{
    // Ambil semua pasien + transaksi + detail
public function index()
{
    $pasien = Pasien::with(['transaksi.detail'])->get();

    return Inertia::render('kasir/index', [
        'pasien' => $pasien
        ->map(function ($p) {
            return $p->transaksi->map(function ($t) use ($p) {
                return $t->detail->map(function ($d) use ($p, $t) {
                    return [
                        'id'            => $d->id,
                        'nama_pasien'  => $p->nama_pasien,
                        'tanggal'      => $t->tanggal,
                        'jns_perawatan'=> $t->jns_perawatan,
                        'dokter'       => $t->dokter,
                        'tindakan'     => $d->tindakan,
                        'jumlah'       => $d->jumlah,
                        'deskripsi1'   => $d->deskripsi,
                        'resep'        => $d->resep,
                        'deskripsi2'   => $d->deskripsi,
                    ];
                });
            })->flatten(1);
        })->flatten(1)
        

      
    ]);
}




    // Tambah pasien + transaksi baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_pasien'   => 'required|string|max:255',
            'tanggal'       => 'required|date',
            'jns_perawatan' => 'required|string|max:255',
            'dokter'        => 'required|string|max:255',
            'detail'        => 'required|array', // array detail transaksi
        ]);

        // Simpan pasien
        $pasien = Pasien::firstOrCreate([
            'nama_pasien' => $validated['nama_pasien']
        ]);

        // Simpan transaksi
        $transaksi = $pasien->transaksi()->create([
            'tanggal'       => $validated['tanggal'],
            'jns_perawatan' => $validated['jns_perawatan'],
            'dokter'        => $validated['dokter'],
        ]);

        // Simpan detail transaksi
        foreach ($validated['detail'] as $item) {
            $transaksi->detail()->create([
                'tindakan'  => $item['tindakan'] ?? null,
                'jumlah'    => $item['jumlah'] ?? 0,
                'deskripsi' => $item['deskripsi'] ?? null,
                'resep'     => $item['resep'] ?? null,
            ]);
        }

        return response()->json([
            'message' => 'Transaksi berhasil dibuat',
            'data'    => $transaksi->load('detail')
        ]);
    }

    // Ambil detail transaksi berdasarkan pasien
    public function show($id)
    {
        $pasien = Pasien::with(['transaksi.detail'])->findOrFail($id);
        return response()->json($pasien);
    }
}
