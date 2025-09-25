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
        'pasien' => $pasien ,

        


    ]);
}




 public function create(Pasien $pasien , Transaksi $transaksi)
    {
        return Inertia::render('kasir/create', [
            'pasien' => $pasien
            , 'transaksi' => $transaksi 
        ]);
    }
    // Tambah pasien + transaksi baru
 public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            // ========== 1. Pasien ==========
            if ($request->filled('pasien_id')) {
                $pasien = Pasien::findOrFail($request->pasien_id);
            } else {
                // buat pasien baru
                $validatedPasien = $request->validate([
                    'nama_pasien' => 'required|string|max:255',
                    'alamat'      => 'nullable|string',
                    'perawatan'   => 'nullable|string',
                    'Penjamin'    => 'nullable|string',
                    'tanggal'     => 'nullable|date',
                ]);
                $pasien = Pasien::create($validatedPasien);
            }

            // ========== 2. Transaksi ==========
            $transaksiInput = $request->input('transaksi', []);

            // kalau user cuma kirim flat (dokter, tindakan, dst) → bungkus ke array
            if (empty($transaksiInput) && $request->filled('dokter')) {
                $transaksiInput = [[
                    'dokter'   => $request->dokter,
                    'tindakan' => $request->tindakan,
                    'jmlh'     => $request->jmlh,
                    'dskrps'   => $request->dskrps,
                    'bya'      => $request->bya,
                    'detail'   => $request->input('detail', []),
                ]];
            }

            foreach ($transaksiInput as $trxData) {
                // validasi tiap transaksi
                $validatedTrx = validator($trxData, [
                    'dokter'   => 'required|string|max:255',
                    'tindakan' => 'required|string|max:255',
                    'jmlh'     => 'required|numeric|min:1',
                    'dskrps'   => 'nullable|string',
                    'bya'      => 'required|numeric|min:0',
                ])->validate();

                $transaksi = Transaksi::create([
                    'pasien_id' => $pasien->id,
                    'dokter'    => $validatedTrx['dokter'],
                    'tindakan'  => $validatedTrx['tindakan'],
                    'jmlh'      => $validatedTrx['jmlh'],
                    'dskrps'    => $validatedTrx['dskrps'] ?? null,
                    'bya'       => $validatedTrx['bya'],
                ]);

                // ========== 3. Detail Transaksi ==========
                if (!empty($trxData['detail'])) {
                    foreach ($trxData['detail'] as $detail) {
                        $validatedDetail = validator($detail, [
                            'resep'     => 'nullable|string',
                            'jumlah'    => 'nullable|numeric|min:1',
                            'deskripsi' => 'nullable|string',
                            'biaya'     => 'nullable|numeric|min:0',
                        ])->validate();

                        DetailTransaksi::create([
                            'transaksi_id' => $transaksi->id,
                            'resep'        => $validatedDetail['resep'] ?? null,
                            'jumlah'       => $validatedDetail['jumlah'] ?? 0,
                            'deskripsi'    => $validatedDetail['deskripsi'] ?? null,
                            'biaya'        => $validatedDetail['biaya'] ?? 0,
                        ]);
                    }
                }
            }

            DB::commit();
            return redirect()->route('kasir.index')->with('success', 'Data berhasil disimpan.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    
}

    // Ambil detail transaksi berdasarkan pasien
    public function show($id)
    {
        $pasien = Pasien::with(['transaksi.detail'])->findOrFail($id);
        return response()->json($pasien);
    }



  public function destroy($id)
{
    $transaksi = \App\Models\Transaksi::findOrFail($id);
    $transaksi->delete();

    return redirect()->route('kasir.index')->with('success', 'Transaksi berhasil dihapus.');
}
    public function edit($id)
    {
        $transaksi = Transaksi::with('detail')->findOrFail($id);
        return Inertia::render('kasir/edit', [
            'transaksi' => $transaksi
        ]);     
}
}
