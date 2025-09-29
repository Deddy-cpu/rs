<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pasien;
use App\Models\Transaksi;
use App\Models\Konsul;
use App\Models\Tindak;
use App\Models\Alkes;
use App\Models\Rsp;
use App\Models\Lainnya;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\DetailTransaksi; 
use Illuminate\Support\Facades\DB;

class KasirController extends Controller
{
    // Ambil semua pasien dengan semua relasi
    public function index()
    {
        $pasien = Pasien::with([
            'transaksi.detail',
            'konsuls',
            'tindaks', 
            'alkes',
            'rsp',
            'lainnyas'
        ])->get();

        return Inertia::render('kasir/index', [
            'pasien' => $pasien,
        ]);
    }

    public function create(Pasien $pasien = null)
    {
        return Inertia::render('kasir/create', [
            'pasien' => $pasien
        ]);
    }

    // Tambah data ke semua 5 tabel
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            // ========== 1. Pasien ==========
            if ($request->filled('pasien_id')) {
                $pasien = Pasien::findOrFail($request->pasien_id);
            } else {
                $validatedPasien = $request->validate([
                    'nama_pasien' => 'required|string|max:255',
                    'alamat'      => 'nullable|string',
                    'perawatan'   => 'nullable|string',
                    'Penjamin'    => 'nullable|string',
                    'tanggal'     => 'nullable|date',
                ]);
                $pasien = Pasien::create($validatedPasien);
            }

            // ========== 2. Konsul ==========
            if ($request->filled('konsul')) {
                $konsulData = $request->input('konsul');
                $validatedKonsul = validator($konsulData, [
                    'dokter'     => 'required|string|max:255',
                    'dskp_kons'  => 'required|string|max:255',
                    'jmlh_kons'  => 'required|string|max:255',
                    'bya_kons'   => 'required|string|max:255',
                    'disc_kons'  => 'nullable|string|max:255',
                    'st_kons'    => 'nullable|string|max:255',
                    'tanggal'    => 'required|date',
                ])->validate();

                Konsul::create([
                    'pasien_id'  => $pasien->id,
                    'dokter'     => $validatedKonsul['dokter'],
                    'dskp_kons'  => $validatedKonsul['dskp_kons'],
                    'jmlh_kons'  => $validatedKonsul['jmlh_kons'],
                    'bya_kons'   => $validatedKonsul['bya_kons'],
                    'disc_kons'  => $validatedKonsul['disc_kons'] ?? '0%',
                    'st_kons'    => $validatedKonsul['st_kons'] ?? '0 RP',
                    'tanggal'    => $validatedKonsul['tanggal'],
                ]);
            }

            // ========== 3. Tindak ==========
            if ($request->filled('tindak')) {
                $tindakData = $request->input('tindak');
                $validatedTindak = validator($tindakData, [
                    'dktr_tindak'  => 'required|string|max:255',
                    'dskp_tindak'  => 'required|string|max:255',
                    'jmlh_tindak'  => 'required|string|max:255',
                    'bya_tindak'   => 'required|string|max:255',
                    'disc_tindak'  => 'nullable|string|max:255',
                    'st_tindak'    => 'nullable|string|max:255',
                    'tanggal'      => 'required|date',
                ])->validate();

                Tindak::create([
                    'pasien_id'    => $pasien->id,
                    'dktr_tindak'  => $validatedTindak['dktr_tindak'],
                    'dskp_tindak'  => $validatedTindak['dskp_tindak'],
                    'jmlh_tindak'  => $validatedTindak['jmlh_tindak'],
                    'bya_tindak'   => $validatedTindak['bya_tindak'],
                    'disc_tindak'  => $validatedTindak['disc_tindak'] ?? '0%',
                    'st_tindak'    => $validatedTindak['st_tindak'] ?? '0 RP',
                    'tanggal'      => $validatedTindak['tanggal'],
                ]);
            }

            // ========== 4. Alkes ==========
            if ($request->filled('alkes')) {
                $alkesData = $request->input('alkes');
                $validatedAlkes = validator($alkesData, [
                    'poli'         => 'required|string|max:255',
                    'dskp_alkes'   => 'required|string|max:255',
                    'jmlh_alkes'   => 'required|string|max:255',
                    'bya_alkes'    => 'required|string|max:255',
                    'disc_alkes'   => 'nullable|string|max:255',
                    'st_alkes'     => 'nullable|string|max:255',
                    'tanggal'      => 'required|date',
                ])->validate();

                Alkes::create([
                    'pasien_id'   => $pasien->id,
                    'poli'        => $validatedAlkes['poli'],
                    'dskp_alkes'  => $validatedAlkes['dskp_alkes'],
                    'jmlh_alkes'  => $validatedAlkes['jmlh_alkes'],
                    'bya_alkes'   => $validatedAlkes['bya_alkes'],
                    'disc_alkes'  => $validatedAlkes['disc_alkes'] ?? '0%',
                    'st_alkes'    => $validatedAlkes['st_alkes'] ?? '0 RP',
                    'tanggal'     => $validatedAlkes['tanggal'],
                ]);
            }

            // ========== 5. RSP ==========
            if ($request->filled('rsp')) {
                $rspData = $request->input('rsp');
                $validatedRsp = validator($rspData, [
                    'dskp_rsp'   => 'required|string|max:255',
                    'jmlh_rsp'   => 'required|string|max:255',
                    'bya_rsp'    => 'required|string|max:255',
                    'disc_rsp'   => 'nullable|string|max:255',
                    'st_rsp'     => 'nullable|string|max:255',
                    'tanggal'    => 'required|date',
                ])->validate();

                Rsp::create([
                    'pasien_id' => $pasien->id,
                    'dskp_rsp'  => $validatedRsp['dskp_rsp'],
                    'jmlh_rsp'  => $validatedRsp['jmlh_rsp'],
                    'bya_rsp'   => $validatedRsp['bya_rsp'],
                    'disc_rsp'  => $validatedRsp['disc_rsp'] ?? '0%',
                    'st_rsp'    => $validatedRsp['st_rsp'] ?? '0 RP',
                    'tanggal'   => $validatedRsp['tanggal'],
                ]);
            }

            // ========== 6. Lainnya ==========
            if ($request->filled('lainnya')) {
                $lainnyaData = $request->input('lainnya');
                $validatedLainnya = validator($lainnyaData, [
                    'dskp_lainnya'   => 'required|string|max:255',
                    'jmlh_lainnaya'  => 'required|string|max:255',
                    'bya_lainnya'   => 'required|string|max:255',
                    'disc_lainnya'  => 'nullable|string|max:255',
                    'st_lainnya'    => 'nullable|string|max:255',
                    'tanggal'       => 'required|date',
                ])->validate();

                Lainnya::create([
                    'pasien_id'     => $pasien->id,
                    'dskp_lainnya'  => $validatedLainnya['dskp_lainnya'],
                    'jmlh_lainnaya' => $validatedLainnya['jmlh_lainnaya'],
                    'bya_lainnya'   => $validatedLainnya['bya_lainnya'],
                    'disc_lainnya'  => $validatedLainnya['disc_lainnya'] ?? '0%',
                    'st_lainnya'    => $validatedLainnya['st_lainnya'] ?? '0 RP',
                    'tanggal'       => $validatedLainnya['tanggal'],
                ]);
            }

            // ========== 7. Transaksi Lama (untuk kompatibilitas) ==========
            $transaksiInput = $request->input('transaksi', []);
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

    public function show($id)
    {
        $pasien = Pasien::with([
            'transaksi.detail',
            'konsuls',
            'tindaks', 
            'alkes',
            'rsp',
            'lainnyas'
        ])->findOrFail($id);
        return response()->json($pasien);
    }

    public function destroy($id)
    {
        $transaksi = Transaksi::findOrFail($id);
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