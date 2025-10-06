<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue"
import { Head, Link } from "@inertiajs/vue3"

const props = defineProps({
  pasien: Object,
})

// Helper to convert ISO date string (e.g. "2025-09-30T00:00:00.000000Z") to "yyyy-MM-dd"
function toDateInputValue(dateStr) {
  if (!dateStr) return ''
  if (/^\d{4}-\d{2}-\d{2}$/.test(dateStr)) return dateStr
  const d = new Date(dateStr)
  if (isNaN(d.getTime())) return ''
  const pad = n => n.toString().padStart(2, '0')
  return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`
}

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

     <div
      class="min-h-screen bg-cover bg-center flex items-center justify-center p-6"
      style="background-image: url('/images/bg-login.png')"
    >

    <div class="bg-white/70 backdrop-blur-sm rounded-xl p-6 shadow-lg w-full">
    <div class="flex justify-center items-center mb-8">
          <h2 class="text-3xl font-extrabold text-blue-700 tracking-tight">
    Edit Data Pasien
  </h2>
      </div>

      <!-- Info Pasien -->
      <div class="bg-gradient-to-r from-blue-100 to-blue-50 shadow-lg rounded-xl p-8 mb-10">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <p class="text-xs text-black-600">Nama Pasien</p>
            <p class="font-bold text-black-600 text-lg">{{ pasien?.nama_pasien }}</p>
          </div>
          <div>
            <p class="text-xs text-black-600">Alamat</p>
            <p class="font-bold text-black-600 text-lg">{{ pasien?.alamat }}</p>
          </div>
          <div>
            <p class="text-xs text-black-600">Penjamin</p>
            <p class="font-bold text-black-600 text-lg">{{ pasien.Penjamin }}</p>
          </div>
          <div>
            <p class="text-xs text-black-600">Perawatan</p>
            <p class="font-bold text-black-600 text-lg">{{ pasien.perawatan }}</p>
          </div>
          <div>
            <p class="text-xs text-black-600">Tanggal</p>
            <p class="font-bold text-black-600 text-lg">{{ toDateInputValue(pasien?.tanggal) }}</p>
          </div>
        </div>
      </div>

      <!-- Konsul -->
      <div v-if="pasien?.konsuls?.length" class="bg-white shadow rounded-xl p-6 mb-8">
        <h3 class="text-base font-bold text-blue-700 mb-3">Konsultasi</h3>
        <div class="overflow-x-auto">
          <table class="w-full text-sm border border-blue-100 bg-white rounded-xl">
            <thead class="bg-blue-50">
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
              <tr v-for="(r, i) in pasien.konsuls" :key="i" class="hover:bg-blue-50">
                <td class="p-2 border">{{ r.dokter }}</td>
                <td class="p-2 border">{{ r.dskp_kons }}</td>
                <td class="p-2 border text-center">{{ r.jmlh_kons }}</td>
                <td class="p-2 border text-right">{{ r.bya_kons }}</td>
                <td class="p-2 border text-right">{{ r.disc_kons }}</td>
                <td class="p-2 border text-right">{{ r.st_kons }}</td>
                <td class="p-2 border text-center">{{ toDateInputValue(r.tanggal) }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Tindaks -->
      <div v-if="pasien?.tindaks?.length" class="bg-white shadow rounded-xl p-6 mb-8">
        <h3 class="text-base font-bold text-blue-700 mb-3">Tindakan</h3>
        <div class="overflow-x-auto">
          <table class="w-full text-sm border border-blue-100 bg-white rounded-xl">
            <thead class="bg-blue-50">
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
              <tr v-for="(r, i) in pasien.tindaks" :key="i" class="hover:bg-blue-50">
                <td class="p-2 border">{{ r.dktr_tindak }}</td>
                <td class="p-2 border">{{ r.dskp_tindak }}</td>
                <td class="p-2 border text-center">{{ r.jmlh_tindak }}</td>
                <td class="p-2 border text-right">{{ r.bya_tindak }}</td>
                <td class="p-2 border text-right">{{ r.disc_tindak }}</td>
                <td class="p-2 border text-right">{{ r.st_tindak }}</td>
                <td class="p-2 border text-center">{{ toDateInputValue(r.tanggal) }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Alkes -->
      <div v-if="pasien?.alkes?.length" class="bg-white shadow rounded-xl p-6 mb-8">
        <h3 class="text-base font-bold text-blue-700 mb-3">Alkes</h3>
        <div class="overflow-x-auto">
          <table class="w-full text-sm border border-blue-100 bg-white rounded-xl">
            <thead class="bg-blue-50">
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
              <tr v-for="(r, i) in pasien.alkes" :key="i" class="hover:bg-blue-50">
                <td class="p-2 border">{{ r.poli }}</td>
                <td class="p-2 border">{{ r.dskp_alkes }}</td>
                <td class="p-2 border text-center">{{ r.jmlh_alkes }}</td>
                <td class="p-2 border text-right">{{ r.bya_alkes }}</td>
                <td class="p-2 border text-right">{{ r.disc_alkes }}</td>
                <td class="p-2 border text-right">{{ r.st_alkes }}</td>
                <td class="p-2 border text-center">{{ toDateInputValue(r.tanggal) }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- RSP -->
      <div v-if="pasien?.rsp?.length" class="bg-white shadow rounded-xl p-6 mb-8">
        <h3 class="text-base font-bold text-blue-700 mb-3">Resep</h3>
        <div class="overflow-x-auto">
          <table class="w-full text-sm border border-blue-100 bg-white rounded-xl">
            <thead class="bg-blue-50">
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
              <tr v-for="(r, i) in pasien.rsp" :key="i" class="hover:bg-blue-50">
                <td class="p-2 border">{{ r.dskp_rsp }}</td>
                <td class="p-2 border text-center">{{ r.jmlh_rsp }}</td>
                <td class="p-2 border text-right">{{ r.bya_rsp }}</td>
                <td class="p-2 border text-right">{{ r.disc_rsp }}</td>
                <td class="p-2 border text-right">{{ r.st_rsp }}</td>
                <td class="p-2 border text-center">{{ toDateInputValue(r.tanggal) }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Lainnya -->
      <div v-if="pasien?.lainnyas?.length" class="bg-white shadow rounded-xl p-6">
        <h3 class="text-base font-bold text-blue-700 mb-3">Lainnya</h3>
        <div class="overflow-x-auto">
          <table class="w-full text-sm border border-blue-100 bg-white rounded-xl">
            <thead class="bg-blue-50">
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
              <tr v-for="(r, i) in pasien.lainnyas" :key="i" class="hover:bg-blue-50">
                <td class="p-2 border">{{ r.dskp_lainnya }}</td>
                <td class="p-2 border text-center">{{ r.jmlh_lainnaya }}</td>
                <td class="p-2 border text-right">{{ r.bya_lainnya }}</td>
                <td class="p-2 border text-right">{{ r.disc_lainnya }}</td>
                <td class="p-2 border text-right">{{ r.st_lainnya }}</td>
                <td class="p-2 border text-center">{{ toDateInputValue(r.tanggal) }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Grand Total dari semua subtotal -->
      <div class="bg-gradient-to-r from-blue-100 to-blue-50 shadow-lg rounded-xl p-8 mt-10">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
          <h3 class="text-lg font-bold text-blue-700">Grand Total</h3>
          <p class="text-2xl font-extrabold text-blue-900 tracking-wider">Rp. {{ totalSemuaSubtotal().toLocaleString() }} <span class="text-base font-semibold"></span></p>
        </div>
      </div>

      <div class="mt-10 flex justify-between items-center gap-4">
        <Link :href="route('kasir.index')" class="px-5 py-3 border border-blue-600 text-blue-600 rounded-lg font-semibold text-lg hover:bg-blue-50 transition">
          Kembali
        </Link>
        <button @click="printPdf" class="px-8 py-3 bg-blue-600 text-white rounded-lg font-semibold text-lg shadow hover:bg-blue-700 transition flex items-center gap-2">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 16v-8m0 0l-4 4m4-4l4 4M6 20h12a2 2 0 002-2v-7a2 2 0 00-2-2h-1V7a2 2 0 00-2-2H9a2 2 0 00-2 2v2H6a2 2 0 00-2 2v7a2 2 0 002 2z" />
          </svg>
          Download / Print
        </button>
      </div>
    </div>
     </div>
  </AuthenticatedLayout>
</template>
