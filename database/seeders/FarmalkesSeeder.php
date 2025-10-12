<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Farmalkes;

class FarmalkesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sampleData = [
            [
                'kode' => 'FAR001',
                'nama_item' => 'Paracetamol 500mg',
                'deskripsi' => 'Obat penurun demam dan pereda nyeri',
                'jenis' => 'farmasi',
                'kategori' => 'Analgesik',
                'satuan' => 'Tablet',
                'harga' => 2500.00,
                'stok' => 100,
                'aktif' => 'Y',
                'update_by' => 'System',
            ],
            [
                'kode' => 'FAR002',
                'nama_item' => 'Amoxicillin 500mg',
                'deskripsi' => 'Antibiotik untuk infeksi bakteri',
                'jenis' => 'farmasi',
                'kategori' => 'Antibiotik',
                'satuan' => 'Kapsul',
                'harga' => 15000.00,
                'stok' => 50,
                'aktif' => 'Y',
                'update_by' => 'System',
            ],
            [
                'kode' => 'ALK001',
                'nama_item' => 'Tensimeter Digital',
                'deskripsi' => 'Alat pengukur tekanan darah digital',
                'jenis' => 'alkes',
                'kategori' => 'Diagnostik',
                'satuan' => 'Unit',
                'harga' => 250000.00,
                'stok' => 5,
                'aktif' => 'Y',
                'update_by' => 'System',
            ],
            [
                'kode' => 'ALK002',
                'nama_item' => 'Stetoskop',
                'deskripsi' => 'Alat untuk mendengarkan suara jantung dan paru',
                'jenis' => 'alkes',
                'kategori' => 'Diagnostik',
                'satuan' => 'Unit',
                'harga' => 150000.00,
                'stok' => 8,
                'aktif' => 'Y',
                'update_by' => 'System',
            ],
            [
                'kode' => 'FAR003',
                'nama_item' => 'Vitamin C 1000mg',
                'deskripsi' => 'Suplemen vitamin C untuk daya tahan tubuh',
                'jenis' => 'farmasi',
                'kategori' => 'Vitamin',
                'satuan' => 'Tablet',
                'harga' => 8000.00,
                'stok' => 3,
                'aktif' => 'Y',
                'update_by' => 'System',
            ],
        ];

        foreach ($sampleData as $data) {
            Farmalkes::create($data);
        }
    }
}
