<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue"
import { Head, Link } from "@inertiajs/vue3"

const props = defineProps({
  pasien: Object,
})

// Helpers to sum numeric values from string fields like "50000 RP"
const toNumber = (val) => {
  if (!val) return 0
  const s = String(val).replace(/[^0-9]/g, '')
  return Number(s || 0)
}

const sumBy = (arr = [], field) => (arr || []).reduce((sum, r) => sum + toNumber(r?.[field]), 0)

const totalSemuaSubtotal = () => {
  const p = props.pasien || {}
  const totalKonsul = sumBy(p.konsuls, 'st_kons')
  const totalTindak = sumBy(p.tindaks, 'st_tindak')
  const totalAlkes = sumBy(p.alkes, 'st_alkes')
  const totalRsp = sumBy(p.rsp, 'st_rsp')
  const totalLainnya = sumBy(p.lainnyas, 'st_lainnya')
  return totalKonsul + totalTindak + totalAlkes + totalRsp + totalLainnya
} 





const printPdf = () => {
  window.open(route('kasir.pdf', props.pasien.id), '_blank')
}
</script>

<template>
  <AuthenticatedLayout>
    <Head :title="`Detail Pasien - ${pasien?.nama_pasien || ''}`" />

    <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold">Detail Pasien</h2>
        <Link :href="route('kasir.index')" class="px-4 py-2 border rounded">Kembali</Link>
      </div>

      <!-- Info Pasien -->
      <div class="bg-white shadow rounded-lg p-6 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <p class="text-sm text-gray-500">Nama Pasien</p>
            <p class="font-semibold">{{ pasien?.nama_pasien }}</p>
          </div>
          <div>
            <p class="text-sm text-gray-500">Alamat</p>
            <p class="font-semibold">{{ pasien?.alamat }}</p>
          </div>
          <div>
            <p class="text-sm text-gray-500">Penjamin</p>
            <p class="font-semibold">{{ pasien.Penjamin }}</p>
          </div>

             <div>
            <p class="text-sm text-gray-500">Perawatan</p>
            <p class="font-semibold">{{ pasien.perawatan }}</p>
          </div>
          <div>
            <p class="text-sm text-gray-500">Tanggal</p>
            <p class="font-semibold">{{ pasien?.tanggal }}</p>
          </div>
        </div>
      </div>

      <!-- Konsul -->
      <div v-if="pasien?.konsuls?.length" class="bg-white shadow rounded-lg p-6 mb-8">
        <h3 class="text-lg font-semibold mb-3">🩺 Konsultasi</h3>
        <div class="overflow-x-auto">
          <table class="w-full text-sm border border-gray-200">
            <thead class="bg-gray-100">
              <tr>
                <th class="p-2 border">Dokter</th>
                <th class="p-2 border">Deskripsi</th>
                <th class="p-2 border">Jumlah</th>
                <th class="p-2 border">Biaya</th>
                <th class="p-2 border">Diskon</th>
                <th class="p-2 border">Subtotal</th>
                <th class="p-2 border">Tanggal</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(r, i) in pasien.konsuls" :key="i" class="hover:bg-gray-50">
                <td class="p-2 border">{{ r.dokter }}</td>
                <td class="p-2 border">{{ r.dskp_kons }}</td>
                <td class="p-2 border">{{ r.jmlh_kons }}</td>
                <td class="p-2 border">{{ r.bya_kons }}</td>
                <td class="p-2 border">{{ r.disc_kons }}</td>
                <td class="p-2 border">{{ r.st_kons }}</td>
                <td class="p-2 border">{{ r.tanggal }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Tindaks -->
      <div v-if="pasien?.tindaks?.length" class="bg-white shadow rounded-lg p-6 mb-8">
        <h3 class="text-lg font-semibold mb-3">⚕️ Tindakan</h3>
        <div class="overflow-x-auto">
          <table class="w-full text-sm border border-gray-200">
            <thead class="bg-gray-100">
              <tr>
                <th class="p-2 border">Dokter</th>
                <th class="p-2 border">Deskripsi</th>
                <th class="p-2 border">Jumlah</th>
                <th class="p-2 border">Biaya</th>
                <th class="p-2 border">Diskon</th>
                <th class="p-2 border">Subtotal</th>
                <th class="p-2 border">Tanggal</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(r, i) in pasien.tindaks" :key="i" class="hover:bg-gray-50">
                <td class="p-2 border">{{ r.dktr_tindak }}</td>
                <td class="p-2 border">{{ r.dskp_tindak }}</td>
                <td class="p-2 border">{{ r.jmlh_tindak }}</td>
                <td class="p-2 border">{{ r.bya_tindak }}</td>
                <td class="p-2 border">{{ r.disc_tindak }}</td>
                <td class="p-2 border">{{ r.st_tindak }}</td>
                <td class="p-2 border">{{ r.tanggal }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Alkes -->
      <div v-if="pasien?.alkes?.length" class="bg-white shadow rounded-lg p-6 mb-8">
        <h3 class="text-lg font-semibold mb-3">🔬 Alkes</h3>
        <div class="overflow-x-auto">
          <table class="w-full text-sm border border-gray-200">
            <thead class="bg-gray-100">
              <tr>
                <th class="p-2 border">Poli</th>
                <th class="p-2 border">Deskripsi</th>
                <th class="p-2 border">Jumlah</th>
                <th class="p-2 border">Biaya</th>
                <th class="p-2 border">Diskon</th>
                <th class="p-2 border">Subtotal</th>
                <th class="p-2 border">Tanggal</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(r, i) in pasien.alkes" :key="i" class="hover:bg-gray-50">
                <td class="p-2 border">{{ r.poli }}</td>
                <td class="p-2 border">{{ r.dskp_alkes }}</td>
                <td class="p-2 border">{{ r.jmlh_alkes }}</td>
                <td class="p-2 border">{{ r.bya_alkes }}</td>
                <td class="p-2 border">{{ r.disc_alkes }}</td>
                <td class="p-2 border">{{ r.st_alkes }}</td>
                <td class="p-2 border">{{ r.tanggal }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- RSP -->
      <div v-if="pasien?.rsp?.length" class="bg-white shadow rounded-lg p-6 mb-8">
        <h3 class="text-lg font-semibold mb-3">💊 Resep</h3>
        <div class="overflow-x-auto">
          <table class="w-full text-sm border border-gray-200">
            <thead class="bg-gray-100">
              <tr>
                <th class="p-2 border">Deskripsi</th>
                <th class="p-2 border">Jumlah</th>
                <th class="p-2 border">Biaya</th>
                <th class="p-2 border">Diskon</th>
                <th class="p-2 border">Subtotal</th>
                <th class="p-2 border">Tanggal</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(r, i) in pasien.rsp" :key="i" class="hover:bg-gray-50">
                <td class="p-2 border">{{ r.dskp_rsp }}</td>
                <td class="p-2 border">{{ r.jmlh_rsp }}</td>
                <td class="p-2 border">{{ r.bya_rsp }}</td>
                <td class="p-2 border">{{ r.disc_rsp }}</td>
                <td class="p-2 border">{{ r.st_rsp }}</td>
                <td class="p-2 border">{{ r.tanggal }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Lainnya -->
      <div v-if="pasien?.lainnyas?.length" class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-semibold mb-3">📋 Lainnya</h3>
        <div class="overflow-x-auto">
          <table class="w-full text-sm border border-gray-200">
            <thead class="bg-gray-100">
              <tr>
                <th class="p-2 border">Deskripsi</th>
                <th class="p-2 border">Jumlah</th>
                <th class="p-2 border">Biaya</th>
                <th class="p-2 border">Diskon</th>
                <th class="p-2 border">Subtotal</th>
                <th class="p-2 border">Tanggal</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(r, i) in pasien.lainnyas" :key="i" class="hover:bg-gray-50">
                <td class="p-2 border">{{ r.dskp_lainnya }}</td>
                <td class="p-2 border">{{ r.jmlh_lainnaya }}</td>
                <td class="p-2 border">{{ r.bya_lainnya }}</td>
                <td class="p-2 border">{{ r.disc_lainnya }}</td>
                <td class="p-2 border">{{ r.st_lainnya }}</td>
                <td class="p-2 border">{{ r.tanggal }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Grand Total dari semua subtotal -->
      <div class="bg-white shadow rounded-lg p-6 mt-8">
        <div class="flex items-center justify-between">
          <h3 class="text-lg font-semibold">Grand Total</h3>
          <p class="text-xl font-bold">{{ totalSemuaSubtotal() }} RP</p>
        </div>
      </div>

      <button
        @click="printPdf"
        class="mt-6 flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-700 text-white font-semibold rounded-lg shadow-md hover:from-blue-600 hover:to-blue-800 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 16v-8m0 0l-4 4m4-4l4 4M6 20h12a2 2 0 002-2v-7a2 2 0 00-2-2h-1V7a2 2 0 00-2-2H9a2 2 0 00-2 2v2H6a2 2 0 00-2 2v7a2 2 0 002 2z" />
        </svg>
        DOWNLOAD OR PRINT
      </button>



    </div>
  </AuthenticatedLayout>
</template>
