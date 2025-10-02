<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue"
import { Head, Link, useForm } from "@inertiajs/vue3"
import { ref, computed } from "vue"

// Helper to convert ISO date string (e.g. "2025-09-30T00:00:00.000000Z") to "yyyy-MM-dd"
function toDateInputValue(dateStr) {
  if (!dateStr) return ''
  // If already in yyyy-MM-dd, return as is
  if (/^\d{4}-\d{2}-\d{2}$/.test(dateStr)) return dateStr
  // Try to parse ISO string
  const d = new Date(dateStr)
  if (isNaN(d.getTime())) return ''
  // Pad month and day
  const pad = n => n.toString().padStart(2, '0')
  return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`
}

const props = defineProps({
  pasien: Object,
})

// Clone pasien data for editing, and normalize all date fields to "yyyy-MM-dd"
function normalizeDates(obj) {
  if (!obj || typeof obj !== 'object') return obj
  // For arrays, normalize each item
  if (Array.isArray(obj)) {
    return obj.map(normalizeDates)
  }
  // For objects, check for "tanggal" field and normalize
  const newObj = { ...obj }
  for (const key in newObj) {
    if (key === 'tanggal' && newObj[key]) {
      newObj[key] = toDateInputValue(newObj[key])
    } else if (typeof newObj[key] === 'object') {
      newObj[key] = normalizeDates(newObj[key])
    }
  }
  return newObj
}

const form = useForm({
  ...normalizeDates(props.pasien),
  // Defensive: ensure arrays exist for editing and normalize their date fields
  konsuls: props.pasien?.konsuls ? normalizeDates([...props.pasien.konsuls]) : [],
  tindaks: props.pasien?.tindaks ? normalizeDates([...props.pasien.tindaks]) : [],
  alkes: props.pasien?.alkes ? normalizeDates([...props.pasien.alkes]) : [],
  rsp: props.pasien?.rsp ? normalizeDates([...props.pasien.rsp]) : [],
  lainnyas: props.pasien?.lainnyas ? normalizeDates([...props.pasien.lainnyas]) : [],
})

// Helpers to sum numeric values from string fields like "50000 RP"
const toNumber = (val) => {
  if (!val) return 0
  const s = String(val).replace(/[^0-9]/g, '')
  return Number(s || 0)
}

const sumBy = (arr = [], field) => (arr || []).reduce((sum, r) => sum + toNumber(r?.[field]), 0)

const totalSemuaSubtotal = computed(() => {
  const p = form
  const totalKonsul = sumBy(p.konsuls, 'st_kons')
  const totalTindak = sumBy(p.tindaks, 'st_tindak')
  const totalAlkes = sumBy(p.alkes, 'st_alkes')
  const totalRsp = sumBy(p.rsp, 'st_rsp')
  const totalLainnya = sumBy(p.lainnyas, 'st_lainnya')
  return totalKonsul + totalTindak + totalAlkes + totalRsp + totalLainnya
})

const update = () => {
  // Before submit, ensure all date fields are in yyyy-MM-dd format
  const normalizeAllDates = (arr) => (arr || []).map(row => ({
    ...row,
    tanggal: toDateInputValue(row.tanggal)
  }))
  form.tanggal = toDateInputValue(form.tanggal)
  form.konsuls = normalizeAllDates(form.konsuls)
  form.tindaks = normalizeAllDates(form.tindaks)
  form.alkes = normalizeAllDates(form.alkes)
  form.rsp = normalizeAllDates(form.rsp)
  form.lainnyas = normalizeAllDates(form.lainnyas)

  form.put(route('kasir.update', props.pasien.id), {
    preserveScroll: true,
  })
}
</script>

<template>
  <AuthenticatedLayout>
    <Head :title="`Edit Pasien - ${form.nama_pasien || ''}`" />

    <form @submit.prevent="update" class="max-w-5xl mx-auto py-8 px-4 sm:px-8 lg:px-12">
      <div class="flex flex-col sm:flex-row justify-between items-center mb-8 gap-4">
        <h2 class="text-3xl font-extrabold text-blue-700 tracking-tight">Edit Data Pasien</h2>
        <!-- Kembali button moved below, next to Simpan Perubahan -->
      </div>

      <!-- Info Pasien -->
      <div class="bg-gradient-to-br from-blue-50 to-white shadow-lg rounded-xl p-8 mb-10">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Nama Pasien</label>
            <input v-model="form.nama_pasien" class="border border-blue-200 rounded-lg px-3 py-2 w-full focus:ring-2 focus:ring-blue-300 outline-none" />
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Alamat</label>
            <input v-model="form.alamat" class="border border-blue-200 rounded-lg px-3 py-2 w-full focus:ring-2 focus:ring-blue-300 outline-none" />
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Penjamin</label>
            <input v-model="form.Penjamin" class="border border-blue-200 rounded-lg px-3 py-2 w-full focus:ring-2 focus:ring-blue-300 outline-none" />
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Perawatan</label>
            <input v-model="form.perawatan" class="border border-blue-200 rounded-lg px-3 py-2 w-full focus:ring-2 focus:ring-blue-300 outline-none" />
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Tanggal</label>
            <input v-model="form.tanggal" type="date" class="border border-blue-200 rounded-lg px-3 py-2 w-full focus:ring-2 focus:ring-blue-300 outline-none" />
          </div>
        </div>
      </div>

      <!-- Konsul -->
      <div v-if="form.konsuls?.length" class="bg-white shadow-lg rounded-xl p-6 mb-10">
        <h3 class="text-lg font-bold mb-4 text-blue-700 flex items-center gap-2"><span class="text-2xl">🩺</span> Konsultasi</h3>
        <div class="overflow-x-auto">
          <table class="w-full text-sm border border-gray-200 rounded-lg overflow-hidden">
            <thead class="bg-blue-100">
              <tr>
                <th class="p-2 border text-left">Dokter</th>
                <th class="p-2 border text-left">Deskripsi</th>
                <th class="p-2 border text-center">Jumlah</th>
                <th class="p-2 border text-right">Biaya</th>
                <th class="p-2 border text-right">Diskon</th>
                <th class="p-2 border text-right">Subtotal</th>
                <th class="p-2 border text-center">Tanggal</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(r, i) in form.konsuls" :key="i" class="hover:bg-blue-50 transition">
                <td class="p-2 border">
                  <input v-model="form.konsuls[i].dokter" class="border rounded px-2 py-1 w-full" />
                </td>
                <td class="p-2 border">
                  <input v-model="form.konsuls[i].dskp_kons" class="border rounded px-2 py-1 w-full" />
                </td>
                <td class="p-2 border text-center">
                  <input v-model="form.konsuls[i].jmlh_kons" class="border rounded px-2 py-1 w-full text-center" />
                </td>
                <td class="p-2 border text-right">
                  <input v-model="form.konsuls[i].bya_kons" class="border rounded px-2 py-1 w-full text-right" />
                </td>
                <td class="p-2 border text-right">
                  <input v-model="form.konsuls[i].disc_kons" class="border rounded px-2 py-1 w-full text-right" />
                </td>
                <td class="p-2 border text-right">
                  <input v-model="form.konsuls[i].st_kons" class="border rounded px-2 py-1 w-full text-right" />
                </td>
                <td class="p-2 border text-center">
                  <input
                    v-model="form.konsuls[i].tanggal"
                    type="date"
                    class="border rounded px-2 py-1 w-full text-center"
                  />
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Tindaks -->
      <div v-if="form.tindaks?.length" class="bg-white shadow-lg rounded-xl p-6 mb-10">
        <h3 class="text-lg font-bold mb-4 text-blue-700 flex items-center gap-2"><span class="text-2xl">⚕️</span> Tindakan</h3>
        <div class="overflow-x-auto">
          <table class="w-full text-sm border border-gray-200 rounded-lg overflow-hidden">
            <thead class="bg-blue-100">
              <tr>
                <th class="p-2 border text-left">Dokter</th>
                <th class="p-2 border text-left">Deskripsi</th>
                <th class="p-2 border text-center">Jumlah</th>
                <th class="p-2 border text-right">Biaya</th>
                <th class="p-2 border text-right">Diskon</th>
                <th class="p-2 border text-right">Subtotal</th>
                <th class="p-2 border text-center">Tanggal</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(r, i) in form.tindaks" :key="i" class="hover:bg-blue-50 transition">
                <td class="p-2 border">
                  <input v-model="form.tindaks[i].dktr_tindak" class="border rounded px-2 py-1 w-full" />
                </td>
                <td class="p-2 border">
                  <input v-model="form.tindaks[i].dskp_tindak" class="border rounded px-2 py-1 w-full" />
                </td>
                <td class="p-2 border text-center">
                  <input v-model="form.tindaks[i].jmlh_tindak" class="border rounded px-2 py-1 w-full text-center" />
                </td>
                <td class="p-2 border text-right">
                  <input v-model="form.tindaks[i].bya_tindak" class="border rounded px-2 py-1 w-full text-right" />
                </td>
                <td class="p-2 border text-right">
                  <input v-model="form.tindaks[i].disc_tindak" class="border rounded px-2 py-1 w-full text-right" />
                </td>
                <td class="p-2 border text-right">
                  <input v-model="form.tindaks[i].st_tindak" class="border rounded px-2 py-1 w-full text-right" />
                </td>
                <td class="p-2 border text-center">
                  <input
                    v-model="form.tindaks[i].tanggal"
                    type="date"
                    class="border rounded px-2 py-1 w-full text-center"
                  />
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Alkes -->
      <div v-if="form.alkes?.length" class="bg-white shadow-lg rounded-xl p-6 mb-10">
        <h3 class="text-lg font-bold mb-4 text-blue-700 flex items-center gap-2"><span class="text-2xl">🔬</span> Alkes</h3>
        <div class="overflow-x-auto">
          <table class="w-full text-sm border border-gray-200 rounded-lg overflow-hidden">
            <thead class="bg-blue-100">
              <tr>
                <th class="p-2 border text-left">Poli</th>
                <th class="p-2 border text-left">Deskripsi</th>
                <th class="p-2 border text-center">Jumlah</th>
                <th class="p-2 border text-right">Biaya</th>
                <th class="p-2 border text-right">Diskon</th>
                <th class="p-2 border text-right">Subtotal</th>
                <th class="p-2 border text-center">Tanggal</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(r, i) in form.alkes" :key="i" class="hover:bg-blue-50 transition">
                <td class="p-2 border">
                  <input v-model="form.alkes[i].poli" class="border rounded px-2 py-1 w-full" />
                </td>
                <td class="p-2 border">
                  <input v-model="form.alkes[i].dskp_alkes" class="border rounded px-2 py-1 w-full" />
                </td>
                <td class="p-2 border text-center">
                  <input v-model="form.alkes[i].jmlh_alkes" class="border rounded px-2 py-1 w-full text-center" />
                </td>
                <td class="p-2 border text-right">
                  <input v-model="form.alkes[i].bya_alkes" class="border rounded px-2 py-1 w-full text-right" />
                </td>
                <td class="p-2 border text-right">
                  <input v-model="form.alkes[i].disc_alkes" class="border rounded px-2 py-1 w-full text-right" />
                </td>
                <td class="p-2 border text-right">
                  <input v-model="form.alkes[i].st_alkes" class="border rounded px-2 py-1 w-full text-right" />
                </td>
                <td class="p-2 border text-center">
                  <input
                    v-model="form.alkes[i].tanggal"
                    type="date"
                    class="border rounded px-2 py-1 w-full text-center"
                  />
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- RSP -->
      <div v-if="form.rsp?.length" class="bg-white shadow-lg rounded-xl p-6 mb-10">
        <h3 class="text-lg font-bold mb-4 text-blue-700 flex items-center gap-2"><span class="text-2xl">💊</span> Resep</h3>
        <div class="overflow-x-auto">
          <table class="w-full text-sm border border-gray-200 rounded-lg overflow-hidden">
            <thead class="bg-blue-100">
              <tr>
                <th class="p-2 border text-left">Deskripsi</th>
                <th class="p-2 border text-center">Jumlah</th>
                <th class="p-2 border text-right">Biaya</th>
                <th class="p-2 border text-right">Diskon</th>
                <th class="p-2 border text-right">Subtotal</th>
                <th class="p-2 border text-center">Tanggal</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(r, i) in form.rsp" :key="i" class="hover:bg-blue-50 transition">
                <td class="p-2 border">
                  <input v-model="form.rsp[i].dskp_rsp" class="border rounded px-2 py-1 w-full" />
                </td>
                <td class="p-2 border text-center">
                  <input v-model="form.rsp[i].jmlh_rsp" class="border rounded px-2 py-1 w-full text-center" />
                </td>
                <td class="p-2 border text-right">
                  <input v-model="form.rsp[i].bya_rsp" class="border rounded px-2 py-1 w-full text-right" />
                </td>
                <td class="p-2 border text-right">
                  <input v-model="form.rsp[i].disc_rsp" class="border rounded px-2 py-1 w-full text-right" />
                </td>
                <td class="p-2 border text-right">
                  <input v-model="form.rsp[i].st_rsp" class="border rounded px-2 py-1 w-full text-right" />
                </td>
                <td class="p-2 border text-center">
                  <input
                    v-model="form.rsp[i].tanggal"
                    type="date"
                    class="border rounded px-2 py-1 w-full text-center"
                  />
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Lainnya -->
      <div v-if="form.lainnyas?.length" class="bg-white shadow-lg rounded-xl p-6 mb-10">
        <h3 class="text-lg font-bold mb-4 text-blue-700 flex items-center gap-2"><span class="text-2xl">📋</span> Lainnya</h3>
        <div class="overflow-x-auto">
          <table class="w-full text-sm border border-gray-200 rounded-lg overflow-hidden">
            <thead class="bg-blue-100">
              <tr>
                <th class="p-2 border text-left">Deskripsi</th>
                <th class="p-2 border text-center">Jumlah</th>
                <th class="p-2 border text-right">Biaya</th>
                <th class="p-2 border text-right">Diskon</th>
                <th class="p-2 border text-right">Subtotal</th>
                <th class="p-2 border text-center">Tanggal</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(r, i) in form.lainnyas" :key="i" class="hover:bg-blue-50 transition">
                <td class="p-2 border">
                  <input v-model="form.lainnyas[i].dskp_lainnya" class="border rounded px-2 py-1 w-full" />
                </td>
                <td class="p-2 border text-center">
                  <input v-model="form.lainnyas[i].jmlh_lainnaya" class="border rounded px-2 py-1 w-full text-center" />
                </td>
                <td class="p-2 border text-right">
                  <input v-model="form.lainnyas[i].bya_lainnya" class="border rounded px-2 py-1 w-full text-right" />
                </td>
                <td class="p-2 border text-right">
                  <input v-model="form.lainnyas[i].disc_lainnya" class="border rounded px-2 py-1 w-full text-right" />
                </td>
                <td class="p-2 border text-right">
                  <input v-model="form.lainnyas[i].st_lainnya" class="border rounded px-2 py-1 w-full text-right" />
                </td>
                <td class="p-2 border text-center">
                  <input
                    v-model="form.lainnyas[i].tanggal"
                    type="date"
                    class="border rounded px-2 py-1 w-full text-center"
                  />
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Grand Total dari semua subtotal -->
      <div class="bg-gradient-to-r from-blue-100 to-blue-50 shadow-lg rounded-xl p-8 mt-10">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
          <h3 class="text-lg font-bold text-blue-700">Grand Total</h3>
          <p class="text-2xl font-extrabold text-blue-900 tracking-wider">{{ totalSemuaSubtotal.toLocaleString() }} <span class="text-base font-semibold">RP</span></p>
        </div>
      </div>

      <div class="mt-10 flex justify-between items-center gap-4">
        <Link :href="route('kasir.index')" class="px-5 py-3 border border-blue-600 text-blue-600 rounded-lg font-semibold text-lg hover:bg-blue-50 transition">
          Kembali
        </Link>
        <button type="submit" class="px-8 py-3 bg-blue-600 text-white rounded-lg font-semibold text-lg shadow hover:bg-blue-700 transition">
          Simpan Perubahan
        </button>
      </div>
    </form>
  </AuthenticatedLayout>
</template>
