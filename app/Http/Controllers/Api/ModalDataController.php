<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TindakanTarif;
use App\Models\Farmalkes;
use Illuminate\Http\Request;

class ModalDataController extends Controller
{
    /**
     * Get tindakan tarifs for modal with pagination and server-side filtering
     * OPTIMASI: Server-side filtering, pagination, limit query
     */
    public function getTindakanTarifs(Request $request)
    {
        $search = trim($request->input('search', ''));
        $grpEselonId = $request->input('grp_eselon_id');
        $page = max(1, (int) $request->input('page', 1));
        $perPage = min(50, max(10, (int) $request->input('per_page', 20))); // Limit 10-50 per page

        $query = TindakanTarif::with(['tindakanQ', 'grpEselon'])
            ->where('aktif', 'Y');

        // Filter by grp_eselon_id if provided (prioritas utama)
        if ($grpEselonId) {
            $query->whereHas('grpEselon', function ($q) use ($grpEselonId) {
                $q->where('id', $grpEselonId);
            });
        }

        // Server-side search filter (lebih efisien dari client-side)
        if ($search && strlen($search) >= 2) { // Minimum 2 karakter untuk search
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

        // Optimasi: Limit query dengan pagination
        $tindakanTarifs = $query->orderBy('tarif', 'asc')
            ->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'data' => $tindakanTarifs->items(),
            'pagination' => [
                'current_page' => $tindakanTarifs->currentPage(),
                'last_page' => $tindakanTarifs->lastPage(),
                'per_page' => $tindakanTarifs->perPage(),
                'total' => $tindakanTarifs->total(),
                'has_more' => $tindakanTarifs->hasMorePages(),
                'from' => $tindakanTarifs->firstItem(),
                'to' => $tindakanTarifs->lastItem()
            ]
        ]);
    }

    /**
     * Get farmalkes for modal with pagination and server-side filtering
     * OPTIMASI: Server-side filtering, pagination, limit query
     * PERBAIKAN: Menghapus filter jenis dan kategori (kolom sudah dihapus dari tabel)
     */
    public function getFarmalkes(Request $request)
    {
        $search = trim($request->input('search', ''));
        $page = max(1, (int) $request->input('page', 1));
        $perPage = min(50, max(10, (int) $request->input('per_page', 20))); // Limit 10-50 per page

        $query = Farmalkes::where('aktif', 'Y');

        // Server-side search filter (lebih efisien dari client-side)
        // PERBAIKAN: Hanya search di kolom yang masih ada (kode, nama_item, deskripsi, satuan, harga)
        if ($search && strlen($search) >= 2) { // Minimum 2 karakter untuk search
            $query->where(function ($q) use ($search) {
                $q->where('kode', 'like', "%{$search}%")
                  ->orWhere('nama_item', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%")
                  ->orWhere('satuan', 'like', "%{$search}%")
                  ->orWhere('harga', 'like', "%{$search}%");
            });
        }

        // Optimasi: Limit query dengan pagination
        $farmalkes = $query->orderBy('nama_item', 'asc')
            ->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'data' => $farmalkes->items(),
            'pagination' => [
                'current_page' => $farmalkes->currentPage(),
                'last_page' => $farmalkes->lastPage(),
                'per_page' => $farmalkes->perPage(),
                'total' => $farmalkes->total(),
                'has_more' => $farmalkes->hasMorePages(),
                'from' => $farmalkes->firstItem(),
                'to' => $farmalkes->lastItem()
            ]
        ]);
    }
}
