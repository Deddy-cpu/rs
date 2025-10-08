<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pasien;
use App\Models\Psn;
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
use Illuminate\Support\Facades\Validator;

use Barryvdh\DomPDF\Facade\Pdf;


class KasirController extends Controller
{
    // Ambil semua pasien dengan semua relasi
    public function index()
    {
        $pasien = Pasien::with([
            'transaksi.detailTransaksi.konsuls',
            'transaksi.detailTransaksi.tindaks',
            'transaksi.detailTransaksi.alkes',
            'transaksi.detailTransaksi.rsp',
            'transaksi.detailTransaksi.lainnyas'
        ])->paginate(10)
          ->through(function ($pasien) {
              // Normalize attributes for frontend expectations
              $pasien->nama_pasien = $pasien->nm_p ?? '';
              $pasien->alamat = $pasien->almt_B ?? '';
              $pasien->Penjamin = $pasien->penjamin ?? '';
              $pasien->tanggal = $pasien->tgl_reg ?? null;

              // Flatten nested services for quick access if needed
              $pasien->konsuls = collect();
              $pasien->tindaks = collect();
              $pasien->alkes = collect();
              $pasien->rsp = collect();
              $pasien->lainnyas = collect();

              foreach ($pasien->transaksi as $transaksi) {
                  foreach ($transaksi->detailTransaksi as $detailTransaksi) {
                      $pasien->konsuls = $pasien->konsuls->merge($detailTransaksi->konsuls);
                      $pasien->tindaks = $pasien->tindaks->merge($detailTransaksi->tindaks);
                      $pasien->alkes = $pasien->alkes->merge($detailTransaksi->alkes);
                      $pasien->rsp = $pasien->rsp->merge($detailTransaksi->rsp);
                      $pasien->lainnyas = $pasien->lainnyas->merge($detailTransaksi->lainnyas);
                  }
              }

              return $pasien;
          });

        return Inertia::render('kasir/index', [
            'pasien' => $pasien,
        ]);
    }

    public function create(Pasien $pasien  )
    {
        return Inertia::render('kasir/create', [
            'pasien' => $pasien,
            'pasien_id' => $pasien->id,
        ]);
    }

    // Tambah data ke semua 5 tabel
   public function store(Request $request)
{
   DB::beginTransaction();


       // buat/ambil pasien dulu
    // First, create or find a Psn record
    $psn = Psn::create([
        'nm_p' => $request->nama_pasien,
        'nik' => 0,
        'no_bpjs' => 0,
        'agm' => 'Islam',
        'tgl_lahir' => $request->tanggal,
        'kelamin' => 'L',
        'almt_L' => $request->alamat ?? '',
        'almt_B' => $request->alamat ?? '',
    ]);

    $pasien = Pasien::create([
        'psn_id' => $psn->id,
        'nm_p' => $request->nama_pasien,
        'mrn' => 'MRN-' . date('Ymd') . '-' . str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT),
        'almt_B' => $request->alamat,
        'perawatan' => $request->perawatan,
        'penjamin' => $request->Penjamin,
        'tgl_reg' => $request->tanggal,
        'no_reg' => 'REG-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
        'kunjungan' => 'umum',
    ]);
try {
    // ======================
    // KONSUL
    // ======================
    if ($request->filled('konsul')) {
        $konsulData = $request->input('konsul', []);

    $konsulRules = [
    '*.dokter'    => 'required|string|max:255',
    '*.dskp_kons' => 'required|string|max:255',
    '*.jmlh_kons' => 'required|integer',   // ⬅️ tadinya string
    '*.bya_kons'  => 'required|numeric',   // ⬅️ tadinya string
    '*.disc_kons' => 'nullable|string|max:255',
    '*.st_kons'   => 'nullable|numeric',   // ⬅️ tadinya string
    '*.tanggal'   => 'required|date',
];


        $validator = Validator::make($konsulData, $konsulRules);

        if ($validator->fails()) {
            DB::rollBack();
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        foreach ($validator->validated() as $row) {
            Konsul::create([
                'pasien_id'  => $pasien->id,
                'dokter'     => $row['dokter'],
                'dskp_kons'  => $row['dskp_kons'],
                'jmlh_kons'  => $row['jmlh_kons'],
                'bya_kons'   => $row['bya_kons'],
                'disc_kons'  => $row['disc_kons'] ?? '0%',
                'st_kons'    => $row['st_kons'] ?? '0 RP',
                'tanggal'    => $row['tanggal'],
            ]);
        }
    }

    // ======================
    // TINDAK
    // ======================
    if ($request->filled('tindak')) {
        $tindakData = $request->input('tindak', []);

     $tindakRules = [
    '*.dktr_tindak' => 'required|string|max:255',
    '*.dskp_tindak' => 'required|string|max:255',
    '*.jmlh_tindak' => 'required|integer',   // ⬅️ angka
    '*.bya_tindak'  => 'required|numeric',   // ⬅️ angka bisa desimal
    '*.disc_tindak' => 'nullable|string|max:255',
    '*.st_tindak'   => 'nullable|numeric',   // ⬅️ angka
    '*.tanggal'     => 'required|date',
];


        $validator = Validator::make($tindakData, $tindakRules);

        if ($validator->fails()) {
            DB::rollBack();
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        foreach ($validator->validated() as $row) {
            Tindak::create([
                'pasien_id'   => $pasien->id,
                'dktr_tindak' => $row['dktr_tindak'],
                'dskp_tindak' => $row['dskp_tindak'],
                'jmlh_tindak' => $row['jmlh_tindak'],
                'bya_tindak'  => $row['bya_tindak'],
                'disc_tindak' => $row['disc_tindak'] ?? '0%',
                'st_tindak'   => $row['st_tindak'] ?? '0 RP',
                'tanggal'     => $row['tanggal'],
            ]);
        }
    }

    // ======================
    // ALKES
    // ======================
    if ($request->filled('alkes')) {
        $alkesData = $request->input('alkes', []);

      $alkesRules = [
    '*.poli'        => 'required|string|max:255',
    '*.dskp_alkes'  => 'required|string|max:255',   // ⬅️ cocokkan dengan Vue
    '*.jmlh_alkes'  => 'required|integer',          // ⬅️ angka
    '*.bya_alkes'   => 'required|numeric',          // ⬅️ angka desimal boleh
    '*.disc_alkes'  => 'nullable|string|max:255',   // diskon bisa string "10%"
    '*.st_alkes'    => 'nullable|numeric',          // ⬅️ angka
    '*.tanggal'     => 'required|date',
];


        $validator = Validator::make($alkesData, $alkesRules);

        if ($validator->fails()) {
            DB::rollBack();
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        foreach ($validator->validated() as $row) {
         Alkes::create([
    'pasien_id'   => $pasien->id,
    'poli'        => $row['poli'],
    'dskp_alkes'  => $row['dskp_alkes'], // ✅ samakan dengan Vue
    'jmlh_alkes'  => $row['jmlh_alkes'],
    'bya_alkes'   => $row['bya_alkes'],
    'disc_alkes'  => $row['disc_alkes'] ?? '0%',
    'st_alkes'    => $row['st_alkes'] ?? 0,
    'tanggal'     => $row['tanggal'],
]);

        }
    }

    // ======================
    // RSP
    // ======================
    if ($request->filled('rsp')) {
        $rspData = $request->input('rsp', []);

        $rspRules = [
           '*.dskp_rsp'  => 'required|string|max:255', 
            '*.jmlh_rsp' => 'required|integer',
            '*.bya_rsp'  => 'required|numeric',
            '*.disc_rsp' => 'nullable|string|max:255',
            '*.st_rsp'   => 'nullable|numeric',
            '*.tanggal'  => 'required|date',
        ];

        $validator = Validator::make($rspData, $rspRules);

        if ($validator->fails()) {
            DB::rollBack();
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        foreach ($validator->validated() as $row) {
            Rsp::create([
                'pasien_id'  => $pasien->id,
                'dskp_rsp'   => $row['dskp_rsp'],
                'jmlh_rsp'   => $row['jmlh_rsp'],
                'bya_rsp'    => $row['bya_rsp'],
                'disc_rsp'   => $row['disc_rsp'] ?? '0%',
                'st_rsp'     => $row['st_rsp'] ?? '0 RP',
                'tanggal'    => $row['tanggal'],
            ]);
        }
    }

    // ======================
    // LAINNYA
    // ======================
    if ($request->filled('lainnya')) {
        $lainnyaData = $request->input('lainnya', []);

        $lainnyaRules = [
            '*.dskp_lainnya'   => 'nullable|string|max:255',
            '*.jmlh_lainnaya'   => 'nullable|integer',
            '*.bya_lainnya'    => 'nullable|numeric',
            '*.disc_lainnya'   => 'nullable|string|max:255',
            '*.st_lainnya'     => 'nullable|numeric',
            '*.tanggal'     => 'nullable|date',
        ];

        $validator = Validator::make($lainnyaData, $lainnyaRules);

        if ($validator->fails()) {
            DB::rollBack();
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        foreach ($validator->validated() as $row) {
            Lainnya::create([
                'pasien_id'  => $pasien->id,
                'dskp_lainnya'  => $row['dskp_lainnya'],
                'jmlh_lainnaya'  => $row['jmlh_lainnaya'],
                'bya_lainnya'   => $row['bya_lainnya'],
                'disc_lainnya'  => $row['disc_lainnya'] ?? '0%',
                'st_lainnya'    => $row['st_lainnya'] ?? '0 RP',
                'tanggal'    => $row['tanggal'],
            ]);
        }
    }

    DB::commit();

    return response()->json(['success' => true]);

} catch (\Exception $e) {
    DB::rollBack();
    return response()->json(['error' => $e->getMessage()], 500);
}

}


    public function show($id)
    {
        $pasien = Pasien::with([
            'transaksi.detailTransaksi.konsuls',
            'transaksi.detailTransaksi.tindaks',
            'transaksi.detailTransaksi.alkes',
            'transaksi.detailTransaksi.rsp',
            'transaksi.detailTransaksi.lainnyas'
        ])->findOrFail($id);

        // Transform the data to be compatible with frontend expectations
        $pasien->konsuls = collect();
        $pasien->tindaks = collect();
        $pasien->alkes = collect();
        $pasien->rsp = collect();
        $pasien->lainnyas = collect();

        foreach ($pasien->transaksi as $transaksi) {
            foreach ($transaksi->detailTransaksi as $detailTransaksi) {
                $pasien->konsuls = $pasien->konsuls->merge($detailTransaksi->konsuls);
                $pasien->tindaks = $pasien->tindaks->merge($detailTransaksi->tindaks);
                $pasien->alkes = $pasien->alkes->merge($detailTransaksi->alkes);
                $pasien->rsp = $pasien->rsp->merge($detailTransaksi->rsp);
                $pasien->lainnyas = $pasien->lainnyas->merge($detailTransaksi->lainnyas);
            }
        }

        return Inertia::render('kasir/show', [
            'pasien' => $pasien->toArray(),
        ]);
    }

    public function destroy($id)
    {
        $transaksi = Pasien::findOrFail($id);
        $transaksi->delete();
        return redirect()->route('kasir.index')->with('success', 'Transaksi berhasil dihapus.');
    }

    public function edit($id)
    {
        // Find the pasien by id (id is pasien.id)
        $pasien = \App\Models\Pasien::with([
            'transaksi.detailTransaksi.konsuls',
            'transaksi.detailTransaksi.tindaks',
            'transaksi.detailTransaksi.alkes',
            'transaksi.detailTransaksi.rsp',
            'transaksi.detailTransaksi.lainnyas'
        ])->findOrFail($id);

        // Get the first transaction and its detail transactions
        $transaksi = $pasien->transaksi->first();
        
        // Safely get the first related model or null from detail transactions
        $konsul = null;
        $tindak = null;
        $alkes = null;
        $rsp = null;
        $lainnya = null;

        if ($transaksi) {
            $detailTransaksi = $transaksi->detailTransaksi->first();
            if ($detailTransaksi) {
                $konsul = $detailTransaksi->konsuls->first();
                $tindak = $detailTransaksi->tindaks->first();
                $alkes = $detailTransaksi->alkes->first();
                $rsp = $detailTransaksi->rsp->first();
                $lainnya = $detailTransaksi->lainnyas->first();
            }
        }

        // Attach to pasien object for the form
        $pasien->konsul = $konsul;
        $pasien->tindak = $tindak;
        $pasien->alkes = $alkes;
        $pasien->rsp = $rsp;
        $pasien->lainnya = $lainnya;
        $pasien->transaksi_id = $pasien->id;

        return Inertia::render('kasir/edit', [
            'pasien' => $pasien,
            'pasien_id' => $pasien->id,
        ]);
    }

    public function update(Request $request, $id)
    {
        // $id is pasien.id
        $pasien = \App\Models\Pasien::with([
            'transaksi.detailTransaksi.konsuls',
            'transaksi.detailTransaksi.tindaks',
            'transaksi.detailTransaksi.alkes',
            'transaksi.detailTransaksi.rsp',
            'transaksi.detailTransaksi.lainnyas'
        ])->findOrFail($id);

        DB::beginTransaction();
        try {
            // Update pasien main data
            $pasien->update([
                'nama_pasien' => $request->input('nama_pasien'),
                'alamat'      => $request->input('alamat'),
                'perawatan'   => $request->input('perawatan'),
                'Penjamin'    => $request->input('Penjamin'),
                'tanggal'     => $request->input('tanggal'),
            ]);

            // Get or create transaction
            $transaksi = $pasien->transaksi->first();
            if (!$transaksi) {
                $transaksi = $pasien->transaksi()->create([
                    'pasien_id' => $pasien->id,
                    'total_biaya' => 0,
                    'tanggal' => now(),
                    'status' => 'pending',
                ]);
            }

            // Helper to update or create medical service through detail transaction
            $updateOrCreateMedicalService = function ($serviceType, $data) use ($transaksi) {
                if (!is_array($data)) return;
                
                // Find existing detail transaction for this service type
                $detailTransaksi = $transaksi->detailTransaksi()
                    ->where('resep', ucfirst($serviceType))
                    ->first();
                
                if (!$detailTransaksi) {
                    // Create new detail transaction
                    $detailTransaksi = $transaksi->detailTransaksi()->create([
                        'transaksi_id' => $transaksi->id,
                        'resep' => ucfirst($serviceType),
                        'jumlah' => $data['jmlh_' . $serviceType] ?? 1,
                        'deskripsi' => $data['dskp_' . $serviceType] ?? '',
                        'biaya' => $data['bya_' . $serviceType] ?? 0,
                    ]);
                }

                // Update or create the medical service record
                $serviceModel = $detailTransaksi->$serviceType()->first();
                if ($serviceModel) {
                    $serviceModel->update($data);
                } else {
                    $detailTransaksi->$serviceType()->create($data);
                }
            };

            // Update or create konsul
            if ($request->has('konsul')) {
                $updateOrCreateMedicalService('konsuls', $request->input('konsul'));
            }

            // Update or create tindak
            if ($request->has('tindak')) {
                $updateOrCreateMedicalService('tindaks', $request->input('tindak'));
            }

            // Update or create alkes
            if ($request->has('alkes')) {
                $updateOrCreateMedicalService('alkes', $request->input('alkes'));
            }

            // Update or create rsp
            if ($request->has('rsp')) {
                $updateOrCreateMedicalService('rsp', $request->input('rsp'));
            }

            // Update or create lainnya
            if ($request->has('lainnya')) {
                $updateOrCreateMedicalService('lainnyas', $request->input('lainnya'));
            }

            DB::commit();
            return redirect()->route('kasir.index')->with('success', 'Transaksi berhasil diupdate.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function pdf($id)
    {
        $pasien = Pasien::with([
            'transaksi.detailTransaksi.konsuls',
            'transaksi.detailTransaksi.tindaks',
            'transaksi.detailTransaksi.alkes',
            'transaksi.detailTransaksi.rsp',
            'transaksi.detailTransaksi.lainnyas'
        ])->findOrFail($id);
    
        $pdf = Pdf::loadView('print.kasir', compact('pasien'))
                  ->setPaper('A4', 'portrait');
    
        return $pdf->stream('invoice-'.$pasien->id.'.pdf');
    }
    

}