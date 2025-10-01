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

    <form @submit.prevent="update" class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold">Edit Pasien</h2>
        <Link :href="route('kasir.index')" class="px-4 py-2 border rounded">Kembali</Link>
      </div>

      <!-- Info Pasien -->
      <div class="bg-white shadow rounded-lg p-6 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <p class="text-sm text-gray-500">Nama Pasien</p>
            <input v-model="form.nama_pasien" class="font-semibold border rounded px-2 py-1 w-full" />
          </div>
          <div>
            <p class="text-sm text-gray-500">Alamat</p>
            <input v-model="form.alamat" class="font-semibold border rounded px-2 py-1 w-full" />
          </div>
          <div>
            <p class="text-sm text-gray-500">Penjamin</p>
            <input v-model="form.Penjamin" class="font-semibold border rounded px-2 py-1 w-full" />
          </div>
          <div>
            <p class="text-sm text-gray-500">Perawatan</p>
            <input v-model="form.perawatan" class="font-semibold border rounded px-2 py-1 w-full" />
          </div>
          <div>
            <p class="text-sm text-gray-500">Tanggal</p>
            <input v-model="form.tanggal" type="date" class="font-semibold border rounded px-2 py-1 w-full" />
          </div>
        </div>
      </div>

      <!-- Konsul -->
      <div v-if="form.konsuls?.length" class="bg-white shadow rounded-lg p-6 mb-8">
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
              <tr v-for="(r, i) in form.konsuls" :key="i" class="hover:bg-gray-50">
                <td class="p-2 border">
                  <input v-model="form.konsuls[i].dokter" class="border rounded px-1 w-full" />
                </td>
                <td class="p-2 border">
                  <input v-model="form.konsuls[i].dskp_kons" class="border rounded px-1 w-full" />
                </td>
                <td class="p-2 border">
                  <input v-model="form.konsuls[i].jmlh_kons" class="border rounded px-1 w-full" />
                </td>
                <td class="p-2 border">
                  <input v-model="form.konsuls[i].bya_kons" class="border rounded px-1 w-full" />
                </td>
                <td class="p-2 border">
                  <input v-model="form.konsuls[i].disc_kons" class="border rounded px-1 w-full" />
                </td>
                <td class="p-2 border">
                  <input v-model="form.konsuls[i].st_kons" class="border rounded px-1 w-full" />
                </td>
                <td class="p-2 border">
                  <input
                    v-model="form.konsuls[i].tanggal"
                    type="date"
                    class="border rounded px-1 w-full"
                  />
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Tindaks -->
      <div v-if="form.tindaks?.length" class="bg-white shadow rounded-lg p-6 mb-8">
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
              <tr v-for="(r, i) in form.tindaks" :key="i" class="hover:bg-gray-50">
                <td class="p-2 border">
                  <input v-model="form.tindaks[i].dktr_tindak" class="border rounded px-1 w-full" />
                </td>
                <td class="p-2 border">
                  <input v-model="form.tindaks[i].dskp_tindak" class="border rounded px-1 w-full" />
                </td>
                <td class="p-2 border">
                  <input v-model="form.tindaks[i].jmlh_tindak" class="border rounded px-1 w-full" />
                </td>
                <td class="p-2 border">
                  <input v-model="form.tindaks[i].bya_tindak" class="border rounded px-1 w-full" />
                </td>
                <td class="p-2 border">
                  <input v-model="form.tindaks[i].disc_tindak" class="border rounded px-1 w-full" />
                </td>
                <td class="p-2 border">
                  <input v-model="form.tindaks[i].st_tindak" class="border rounded px-1 w-full" />
                </td>
                <td class="p-2 border">
                  <input
                    v-model="form.tindaks[i].tanggal"
                    type="date"
                    class="border rounded px-1 w-full"
                  />
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Alkes -->
      <div v-if="form.alkes?.length" class="bg-white shadow rounded-lg p-6 mb-8">
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
              <tr v-for="(r, i) in form.alkes" :key="i" class="hover:bg-gray-50">
                <td class="p-2 border">
                  <input v-model="form.alkes[i].poli" class="border rounded px-1 w-full" />
                </td>
                <td class="p-2 border">
                  <input v-model="form.alkes[i].dskp_alkes" class="border rounded px-1 w-full" />
                </td>
                <td class="p-2 border">
                  <input v-model="form.alkes[i].jmlh_alkes" class="border rounded px-1 w-full" />
                </td>
                <td class="p-2 border">
                  <input v-model="form.alkes[i].bya_alkes" class="border rounded px-1 w-full" />
                </td>
                <td class="p-2 border">
                  <input v-model="form.alkes[i].disc_alkes" class="border rounded px-1 w-full" />
                </td>
                <td class="p-2 border">
                  <input v-model="form.alkes[i].st_alkes" class="border rounded px-1 w-full" />
                </td>
                <td class="p-2 border">
                  <input
                    v-model="form.alkes[i].tanggal"
                    type="date"
                    class="border rounded px-1 w-full"
                  />
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- RSP -->
      <div v-if="form.rsp?.length" class="bg-white shadow rounded-lg p-6 mb-8">
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
              <tr v-for="(r, i) in form.rsp" :key="i" class="hover:bg-gray-50">
                <td class="p-2 border">
                  <input v-model="form.rsp[i].dskp_rsp" class="border rounded px-1 w-full" />
                </td>
                <td class="p-2 border">
                  <input v-model="form.rsp[i].jmlh_rsp" class="border rounded px-1 w-full" />
                </td>
                <td class="p-2 border">
                  <input v-model="form.rsp[i].bya_rsp" class="border rounded px-1 w-full" />
                </td>
                <td class="p-2 border">
                  <input v-model="form.rsp[i].disc_rsp" class="border rounded px-1 w-full" />
                </td>
                <td class="p-2 border">
                  <input v-model="form.rsp[i].st_rsp" class="border rounded px-1 w-full" />
                </td>
                <td class="p-2 border">
                  <input
                    v-model="form.rsp[i].tanggal"
                    type="date"
                    class="border rounded px-1 w-full"
                  />
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Lainnya -->
      <div v-if="form.lainnyas?.length" class="bg-white shadow rounded-lg p-6">
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
              <tr v-for="(r, i) in form.lainnyas" :key="i" class="hover:bg-gray-50">
                <td class="p-2 border">
                  <input v-model="form.lainnyas[i].dskp_lainnya" class="border rounded px-1 w-full" />
                </td>
                <td class="p-2 border">
                  <input v-model="form.lainnyas[i].jmlh_lainnaya" class="border rounded px-1 w-full" />
                </td>
                <td class="p-2 border">
                  <input v-model="form.lainnyas[i].bya_lainnya" class="border rounded px-1 w-full" />
                </td>
                <td class="p-2 border">
                  <input v-model="form.lainnyas[i].disc_lainnya" class="border rounded px-1 w-full" />
                </td>
                <td class="p-2 border">
                  <input v-model="form.lainnyas[i].st_lainnya" class="border rounded px-1 w-full" />
                </td>
                <td class="p-2 border">
                  <input
                    v-model="form.lainnyas[i].tanggal"
                    type="date"
                    class="border rounded px-1 w-full"
                  />
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Grand Total dari semua subtotal -->
      <div class="bg-white shadow rounded-lg p-6 mt-8">
        <div class="flex items-center justify-between">
          <h3 class="text-lg font-semibold">Grand Total</h3>
          <p class="text-xl font-bold">{{ totalSemuaSubtotal }} RP</p>
        </div>
      </div>

      <div class="mt-8 flex justify-end">
        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
          Simpan Perubahan
        </button>
      </div>
    </form>
  </AuthenticatedLayout>
</template>
