<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\Kunjungan;
use App\Models\DetailTransaksi;
use App\Models\TindakanTarif;
use App\Models\Farmalkes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class TransaksiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $transaksis = Transaksi::with(['kunjungan.psn', 'detailTransaksi'])
            ->orderBy('tanggal', 'desc')
            ->paginate(15);

        return Inertia::render('transaksi/index', [
            'transaksis' => $transaksis
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Kunjungan $kunjungan)
    {
        $kunjungan->load('psn');
        
        // Get tindakan tarifs for the modal
        $tindakanTarifs = TindakanTarif::with(['tindakanQ', 'grpEselon'])
            ->where('aktif', 'Y')
            ->orderBy('tarif', 'asc')
            ->get();
        
        $farmalkes = Farmalkes::where('aktif', 'Y')
            ->orderBy('nama_item', 'asc')
            ->get();
        
        return Inertia::render('dokter/pasien_kunjungan/detail_transaksi', [
            'kunjungan' => $kunjungan,
            'kunjunganId' => $kunjungan->id,
            'tindakanTarifs' => $tindakanTarifs,
            'farmalkes' => $farmalkes
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kunjungan_id' => 'required|exists:kunjungans,id',
            'tanggal' => 'required|date',
            'status' => 'required|in:pending,completed,cancelled',
            
            // Ignore kunjungan fields for create mode (they're not needed)
            'psn_id' => 'nullable',
            'no_reg' => 'nullable',
            'tgl_reg' => 'nullable',
            'nm_p' => 'nullable',
            'mrn' => 'nullable',
            'almt_B' => 'nullable',
            'no_inv' => 'nullable',
            'tgl_inv' => 'nullable',
            'perawatan' => 'nullable',
            'penjamin' => 'nullable',
            'no_sjp' => 'nullable',
            'icd' => 'nullable',
            'kunjungan' => 'nullable',
            
            // Konsultasi
            'konsul' => 'nullable|array',
            'konsul.*.dokter' => 'nullable|string|max:255',
            'konsul.*.dskp_kons' => 'nullable|string|max:255',
            'konsul.*.jmlh_kons' => 'nullable|numeric|min:0',
            'konsul.*.bya_kons' => 'nullable|numeric|min:0',
            'konsul.*.disc_kons' => 'nullable|string|max:255',
            'konsul.*.st_kons' => 'nullable|numeric',
            'konsul.*.tanggal' => 'nullable|date',
            
            // Tindakan
            'tindak' => 'nullable|array',
            'tindak.*.dktr_tindak' => 'nullable|string|max:255',
            'tindak.*.dskp_tindak' => 'nullable|string|max:255',
            'tindak.*.jmlh_tindak' => 'nullable|numeric|min:0',
            'tindak.*.bya_tindak' => 'nullable|numeric|min:0',
            'tindak.*.disc_tindak' => 'nullable|string|max:255',
            'tindak.*.st_tindak' => 'nullable|numeric',
            'tindak.*.tanggal' => 'nullable|date',
            
            // Alkes
            'alkes' => 'nullable|array',
            'alkes.*.poli' => 'nullable|string|max:255',
            'alkes.*.dskp_alkes' => 'nullable|string|max:255',
            'alkes.*.jmlh_alkes' => 'nullable|numeric|min:0',
            'alkes.*.bya_alkes' => 'nullable|numeric|min:0',
            'alkes.*.disc_alkes' => 'nullable|string|max:255',
            'alkes.*.st_alkes' => 'nullable|numeric',
            'alkes.*.tanggal' => 'nullable|date',
            
            // Resep
            'rsp' => 'nullable|array',
            'rsp.*.dktr_rsp' => 'nullable|string|max:255',
            'rsp.*.dskp_rsp' => 'nullable|string|max:255',
            'rsp.*.jmlh_rsp' => 'nullable|numeric|min:0',
            'rsp.*.bya_rsp' => 'nullable|numeric|min:0',
            'rsp.*.disc_rsp' => 'nullable|string|max:255',
            'rsp.*.st_rsp' => 'nullable|numeric',
            'rsp.*.tanggal' => 'nullable|date',
            
            // Lainnya
            'lainnya' => 'nullable|array',
            'lainnya.*.dktr_lainnya' => 'nullable|string|max:255',
            'lainnya.*.dskp_lainnya' => 'nullable|string|max:255',
            'lainnya.*.jmlh_lainnya' => 'nullable|numeric|min:0',
            'lainnya.*.bya_lainnya' => 'nullable|numeric|min:0',
            'lainnya.*.disc_lainnya' => 'nullable|string|max:255',
            'lainnya.*.st_lainnya' => 'nullable|numeric',
            'lainnya.*.tanggal' => 'nullable|date',
        ]);

        try {
            DB::beginTransaction();

            // Get the kunjungan
            $kunjungan = Kunjungan::findOrFail($validated['kunjungan_id']);

            // Create transaction record
            $transaksi = Transaksi::create([
                'kunjungan_id' => $validated['kunjungan_id'],
                'total_biaya' => 0, // Will be calculated
                'tanggal' => $validated['tanggal'],
                'status' => $validated['status'],
            ]);

            $totalBiaya = 0;

            // Process Konsultasi
            if (!empty($validated['konsul'])) {
                foreach ($validated['konsul'] as $konsulData) {
                    if (!empty($konsulData['dskp_kons']) && $konsulData['bya_kons'] > 0) {
                        $detailTransaksi = DetailTransaksi::create([
                            'transaksi_id' => $transaksi->id,
                            'resep' => 'Konsultasi',
                            'jumlah' => $konsulData['jmlh_kons'] ?? 1,
                            'deskripsi' => $konsulData['dskp_kons'],
                            'biaya' => $konsulData['bya_kons'],
                        ]);

                        $detailTransaksi->konsuls()->create([
                            'detail_transaksi_id' => $detailTransaksi->id,
                            'dokter' => $konsulData['dokter'] ?? '',
                            'dskp_kons' => $konsulData['dskp_kons'],
                            'jmlh_kons' => $konsulData['jmlh_kons'] ?? 1,
                            'bya_kons' => $konsulData['bya_kons'],
                            'disc_kons' => $konsulData['disc_kons'] ?? '0%',
                            'st_kons' => $konsulData['st_kons'] ?? 0,
                            'tanggal' => $konsulData['tanggal'] ?? $validated['tanggal'],
                        ]);

                        $totalBiaya += ($konsulData['jmlh_kons'] ?? 1) * $konsulData['bya_kons'];
                    }
                }
            }

            // Process Tindakan
            if (!empty($validated['tindak'])) {
                foreach ($validated['tindak'] as $tindakData) {
                    if (!empty($tindakData['dskp_tindak']) && $tindakData['bya_tindak'] > 0) {
                        $detailTransaksi = DetailTransaksi::create([
                            'transaksi_id' => $transaksi->id,
                            'resep' => 'Tindakan',
                            'jumlah' => $tindakData['jmlh_tindak'] ?? 1,
                            'deskripsi' => $tindakData['dskp_tindak'],
                            'biaya' => $tindakData['bya_tindak'],
                        ]);

                        $detailTransaksi->tindaks()->create([
                            'detail_transaksi_id' => $detailTransaksi->id,
                            'dktr_tindak' => $tindakData['dktr_tindak'] ?? '',
                            'dskp_tindak' => $tindakData['dskp_tindak'],
                            'jmlh_tindak' => $tindakData['jmlh_tindak'] ?? 1,
                            'bya_tindak' => $tindakData['bya_tindak'],
                            'disc_tindak' => $tindakData['disc_tindak'] ?? '0%',
                            'st_tindak' => $tindakData['st_tindak'] ?? 0,
                            'tanggal' => $tindakData['tanggal'] ?? $validated['tanggal'],
                        ]);

                        $totalBiaya += ($tindakData['jmlh_tindak'] ?? 1) * $tindakData['bya_tindak'];
                    }
                }
            }

            // Process Alkes
            if (!empty($validated['alkes'])) {
                foreach ($validated['alkes'] as $alkesData) {
                    if (!empty($alkesData['dskp_alkes']) && $alkesData['bya_alkes'] > 0) {
                        $detailTransaksi = DetailTransaksi::create([
                            'transaksi_id' => $transaksi->id,
                            'resep' => 'Alkes',
                            'jumlah' => $alkesData['jmlh_alkes'] ?? 1,
                            'deskripsi' => $alkesData['dskp_alkes'],
                            'biaya' => $alkesData['bya_alkes'],
                        ]);

                        $detailTransaksi->alkes()->create([
                            'detail_transaksi_id' => $detailTransaksi->id,
                            'poli' => $alkesData['poli'] ?? '',
                            'dskp_alkes' => $alkesData['dskp_alkes'],
                            'jmlh_alkes' => $alkesData['jmlh_alkes'] ?? 1,
                            'bya_alkes' => $alkesData['bya_alkes'],
                            'disc_alkes' => $alkesData['disc_alkes'] ?? '0%',
                            'st_alkes' => $alkesData['st_alkes'] ?? 0,
                            'tanggal' => $alkesData['tanggal'] ?? $validated['tanggal'],
                        ]);

                        $totalBiaya += ($alkesData['jmlh_alkes'] ?? 1) * $alkesData['bya_alkes'];
                    }
                }
            }

            // Process Resep
            if (!empty($validated['rsp'])) {
                foreach ($validated['rsp'] as $rspData) {
                    if (!empty($rspData['dskp_rsp']) && $rspData['bya_rsp'] > 0) {
                        $detailTransaksi = DetailTransaksi::create([
                            'transaksi_id' => $transaksi->id,
                            'resep' => 'Resep',
                            'jumlah' => $rspData['jmlh_rsp'] ?? 1,
                            'deskripsi' => $rspData['dskp_rsp'],
                            'biaya' => $rspData['bya_rsp'],
                        ]);

                        $detailTransaksi->rsps()->create([
                            'detail_transaksi_id' => $detailTransaksi->id,
                            'dktr_rsp' => $rspData['dktr_rsp'] ?? '',
                            'dskp_rsp' => $rspData['dskp_rsp'],
                            'jmlh_rsp' => $rspData['jmlh_rsp'] ?? 1,
                            'bya_rsp' => $rspData['bya_rsp'],
                            'disc_rsp' => $rspData['disc_rsp'] ?? '0%',
                            'st_rsp' => $rspData['st_rsp'] ?? 0,
                            'tanggal' => $rspData['tanggal'] ?? $validated['tanggal'],
                        ]);

                        $totalBiaya += ($rspData['jmlh_rsp'] ?? 1) * $rspData['bya_rsp'];
                    }
                }
            }

            // Process Lainnya
            if (!empty($validated['lainnya'])) {
                foreach ($validated['lainnya'] as $lainnyaData) {
                    if (!empty($lainnyaData['dskp_lainnya']) && $lainnyaData['bya_lainnya'] > 0) {
                        $detailTransaksi = DetailTransaksi::create([
                            'transaksi_id' => $transaksi->id,
                            'resep' => 'Lainnya',
                            'jumlah' => $lainnyaData['jmlh_lainnya'] ?? 1,
                            'deskripsi' => $lainnyaData['dskp_lainnya'],
                            'biaya' => $lainnyaData['bya_lainnya'],
                        ]);

                        $detailTransaksi->lainnyas()->create([
                            'detail_transaksi_id' => $detailTransaksi->id,
                            'dktr_lainnya' => $lainnyaData['dktr_lainnya'] ?? '',
                            'dskp_lainnya' => $lainnyaData['dskp_lainnya'],
                            'jmlh_lainnya' => $lainnyaData['jmlh_lainnya'] ?? 1,
                            'bya_lainnya' => $lainnyaData['bya_lainnya'],
                            'disc_lainnya' => $lainnyaData['disc_lainnya'] ?? '0%',
                            'st_lainnya' => $lainnyaData['st_lainnya'] ?? 0,
                            'tanggal' => $lainnyaData['tanggal'] ?? $validated['tanggal'],
                        ]);

                        $totalBiaya += ($lainnyaData['jmlh_lainnya'] ?? 1) * $lainnyaData['bya_lainnya'];
                    }
                }
            }

            // Update total biaya
            $transaksi->update(['total_biaya' => $totalBiaya]);

            DB::commit();

            return redirect()->route('kunjungan.show', ['kunjungan' => $kunjungan->id])
                ->with('success', 'Transaksi berhasil dibuat dengan total biaya Rp ' . number_format($totalBiaya, 0, ',', '.'));

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->withErrors(['error' => 'Gagal membuat transaksi: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Transaksi $transaksi)
    {
        $transaksi->load(['kunjungan.psn', 'detailTransaksi.konsuls', 'detailTransaksi.tindaks', 'detailTransaksi.alkes', 'detailTransaksi.rsps', 'detailTransaksi.lainnyas']);

        return Inertia::render('transaksi/show', [
            'transaksi' => $transaksi
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Transaksi $transaksi)
    {
        $transaksi->load(['kunjungan.psn', 'detailTransaksi.konsuls', 'detailTransaksi.tindaks', 'detailTransaksi.alkes', 'detailTransaksi.rsps', 'detailTransaksi.lainnyas']);

        return Inertia::render('transaksi/edit', [
            'transaksi' => $transaksi
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Transaksi $transaksi)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'status' => 'required|in:pending,completed,cancelled',
        ]);

        try {
            DB::beginTransaction();

            $transaksi->update($validated);

            DB::commit();

            return redirect()->route('transaksi.show', $transaksi->id)
                ->with('success', 'Transaksi berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->withErrors(['error' => 'Gagal memperbarui transaksi: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transaksi $transaksi)
    {
        try {
            DB::beginTransaction();

            // Delete related detail transactions and medical services
            foreach ($transaksi->detailTransaksi as $detail) {
                $detail->konsuls()->delete();
                $detail->tindaks()->delete();
                $detail->alkes()->delete();
                $detail->rsps()->delete();
                $detail->lainnyas()->delete();
                $detail->delete();
            }
            
            // Delete the transaction
            $transaksi->delete();

            DB::commit();

            return redirect()->route('transaksi.index')
                ->with('success', 'Transaksi berhasil dihapus.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->withErrors(['error' => 'Gagal menghapus transaksi: ' . $e->getMessage()]);
        }
    }
}
