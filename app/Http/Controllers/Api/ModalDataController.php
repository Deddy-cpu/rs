<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TindakanTarif;
use App\Models\Farmalkes;
use Illuminate\Http\Request;

class ModalDataController extends Controller
{
    /**
     * Get tindakan tarifs for modal with pagination and filtering
     */
    public function getTindakanTarifs(Request $request)
    {
        $search = $request->input('search', '');
        $grpEselonId = $request->input('grp_eselon_id');
        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 20);

        $query = TindakanTarif::with(['tindakanQ', 'grpEselon'])
            ->where('aktif', 'Y');

        // Filter by grp_eselon_id if provided
        if ($grpEselonId) {
            $query->whereHas('grpEselon', function ($q) use ($grpEselonId) {
                $q->where('id', $grpEselonId);
            });
        }

        // Apply search filter
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('tindakanQ', function ($subQ) use ($search) {
                    $subQ->where('tindakan_q_desc', 'like', "%{$search}%");
                })
                ->orWhereHas('grpEselon', function ($subQ) use ($search) {
                    $subQ->where('grp_eselon_desc', 'like', "%{$search}%");
                })
                ->orWhere('tarif', 'like', "%{$search}%");
            });
        }

        $tindakanTarifs = $query->orderBy('tarif', 'asc')
            ->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'data' => $tindakanTarifs->items(),
            'pagination' => [
                'current_page' => $tindakanTarifs->currentPage(),
                'last_page' => $tindakanTarifs->lastPage(),
                'per_page' => $tindakanTarifs->perPage(),
                'total' => $tindakanTarifs->total(),
                'has_more' => $tindakanTarifs->hasMorePages()
            ]
        ]);
    }

    /**
     * Get farmalkes for modal with pagination and filtering
     */
    public function getFarmalkes(Request $request)
    {
        $search = $request->input('search', '');
        $jenis = $request->input('jenis'); // 'farmasi' or 'alkes'
        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 20);

        $query = Farmalkes::where('aktif', 'Y');

        // Filter by jenis if provided
        if ($jenis) {
            $query->where('jenis', $jenis);
        }

        // Apply search filter
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('kode', 'like', "%{$search}%")
                  ->orWhere('nama_item', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%")
                  ->orWhere('kategori', 'like', "%{$search}%")
                  ->orWhere('jenis', 'like', "%{$search}%")
                  ->orWhere('satuan', 'like', "%{$search}%")
                  ->orWhere('harga', 'like', "%{$search}%");
            });
        }

        $farmalkes = $query->orderBy('nama_item', 'asc')
            ->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'data' => $farmalkes->items(),
            'pagination' => [
                'current_page' => $farmalkes->currentPage(),
                'last_page' => $farmalkes->lastPage(),
                'per_page' => $farmalkes->perPage(),
                'total' => $farmalkes->total(),
                'has_more' => $farmalkes->hasMorePages()
            ]
        ]);
    }
}
