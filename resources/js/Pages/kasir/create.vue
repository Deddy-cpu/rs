<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Kasir - Input Data</h1>
        <p class="mt-2 text-gray-600">Alur 4 halaman: Tindakan, Resep, Lainnya, Total</p>
      </div>

      <!-- Stepper -->
      <div class="bg-white shadow rounded-lg p-4 mb-6">
        <div class="flex items-center justify-between">
          <div class="flex-1 flex items-center gap-2">
            <div :class="['px-3 py-1 rounded-full text-sm font-semibold', currentStep === 1 ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700']">1. Tindakan</div>
            <div class="w-8 h-px bg-gray-300"></div>
            <div :class="['px-3 py-1 rounded-full text-sm font-semibold', currentStep === 2 ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700']">2. Resep</div>
            <div class="w-8 h-px bg-gray-300"></div>
            <div :class="['px-3 py-1 rounded-full text-sm font-semibold', currentStep === 3 ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700']">3. Lainnya</div>
            <div class="w-8 h-px bg-gray-300"></div>
            <div :class="['px-3 py-1 rounded-full text-sm font-semibold', currentStep === 4 ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700']">4. Total</div>
          </div>
          <div class="flex items-center gap-2">
            <button @click="prevStep" :disabled="currentStep === 1" class="px-3 py-2 text-sm border rounded disabled:opacity-50">Sebelumnya</button>
            <button v-if="currentStep < 4" @click="nextStep" class="px-3 py-2 text-sm bg-blue-600 text-white rounded">Berikutnya</button>
          </div>
        </div>
      </div>

      <form @submit.prevent="submitForm" class="space-y-8">
        <!-- Informasi Pasien (selalu tampil di atas) -->
        <div class="bg-white shadow rounded-lg p-6">
          <h2 class="text-xl font-semibold text-gray-900 mb-4">Informasi Pasien - (contoh input data pasien)</h2>
          <input v-if="form.pasien_id" type="hidden" v-model="form.pasien_id" />
          <div v-if="form.pasien_id" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <p class="text-sm text-gray-500">Nama Pasien</p>
              <p class="font-semibold">{{ form.nama_pasien }}</p>
            </div>
            <div>
              <p class="text-sm text-gray-500">Alamat</p>
              <p class="font-semibold">{{ form.alamat }}</p>
            </div>
            <div>
              <p class="text-sm text-gray-500">Perawatan</p>
              <p class="font-semibold">{{ form.perawatan }}</p>
            </div>
            <div>
              <p class="text-sm text-gray-500">Penjamin</p>
              <p class="font-semibold">{{ form.Penjamin }}</p>
            </div>
            <div>
              <p class="text-sm text-gray-500">Tanggal</p>
              <p class="font-semibold">{{ form.tanggal }}</p>
            </div>
            <div class="md:col-span-2">
              <a :href="route('pasien.index')" class="inline-block mt-2 px-3 py-2 text-sm border rounded">Ganti Pasien</a>
            </div>
          </div>
          <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Nama Pasien</label>
              <input v-model="form.nama_pasien" type="text" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Masukkan nama pasien" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Alamat</label>
              <input v-model="form.alamat" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Masukkan alamat" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Perawatan</label>
              <input v-model="form.perawatan" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Jenis perawatan" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Penjamin</label>
              <input v-model="form.Penjamin" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Asuransi/Penjamin" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal</label>
              <input v-model="form.tanggal" type="date" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" />
            </div>
          </div>
        </div>

        <!-- STEP 1: Tindakan (Konsul + Tindakan) -->
        <div v-show="currentStep === 1" class="space-y-6">
          <!-- Konsul Table -->
          <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center justify-between mb-4">
              <h2 class="text-xl font-semibold text-gray-900">Tabel Konsultasi</h2>
              <button type="button" @click="addKonsul()" class="px-3 py-2 text-sm bg-blue-600 text-white rounded">+ Tambah Baris</button>
            </div>
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
                    <th class="p-2 border">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(row, i) in konsuls" :key="i" class="hover:bg-gray-50">
                    <td class="p-2 border"><input v-model="row.dokter" class="w-full border rounded px-2 py-1" /></td>
                    <td class="p-2 border"><input v-model="row.dskp_kons" class="w-full border rounded px-2 py-1" /></td>
                    <td class="p-2 border"><input v-model="row.jmlh_kons" class="w-full border rounded px-2 py-1" /></td>
                    <td class="p-2 border"><input v-model="row.bya_kons" @input="updateSubtotal(row, 'bya_kons','disc_kons','st_kons','jmlh_kons')" class="w-full border rounded px-2 py-1" /></td>
                    <td class="p-2 border"><input v-model="row.disc_kons" @input="updateSubtotal(row, 'bya_kons','disc_kons','st_kons','jmlh_kons')" class="w-full border rounded px-2 py-1" /></td>
                    <td class="p-2 border"><input v-model="row.st_kons" class="w-full border rounded px-2 py-1" /></td>
                    <td class="p-2 border"><input v-model="row.tanggal" type="date" class="w-full border rounded px-2 py-1" /></td>
                    <td class="p-2 border text-center"><button type="button" @click="removeKonsul(i)" class="px-2 py-1 text-xs bg-red-600 text-white rounded">Hapus</button></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Tindakan Table -->
          <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center justify-between mb-4">
              <h2 class="text-xl font-semibold text-gray-900">Tabel Tindakan</h2>
              <button type="button" @click="addTindak()" class="px-3 py-2 text-sm bg-green-600 text-white rounded">+ Tambah Baris</button>
            </div>
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
                    <th class="p-2 border">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(row, i) in tindaks" :key="i" class="hover:bg-gray-50">
                    <td class="p-2 border"><input v-model="row.dktr_tindak" class="w-full border rounded px-2 py-1" /></td>
                    <td class="p-2 border"><input v-model="row.dskp_tindak" class="w-full border rounded px-2 py-1" /></td>
                    <td class="p-2 border"><input v-model="row.jmlh_tindak" class="w-full border rounded px-2 py-1" /></td>
                    <td class="p-2 border"><input v-model="row.bya_tindak" @input="updateSubtotal(row, 'bya_tindak','disc_tindak','st_tindak','jmlh_tindak')" class="w-full border rounded px-2 py-1" /></td>
                    <td class="p-2 border"><input v-model="row.disc_tindak" @input="updateSubtotal(row, 'bya_tindak','disc_tindak','st_tindak','jmlh_tindak')" class="w-full border rounded px-2 py-1" /></td>
                    <td class="p-2 border"><input v-model="row.st_tindak" class="w-full border rounded px-2 py-1" /></td>
                    <td class="p-2 border"><input v-model="row.tanggal" type="date" class="w-full border rounded px-2 py-1" /></td>
                    <td class="p-2 border text-center"><button type="button" @click="removeTindak(i)" class="px-2 py-1 text-xs bg-red-600 text-white rounded">Hapus</button></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- STEP 2: Resep (Alkes + Resep[RSP]) -->
        <div v-show="currentStep === 2" class="space-y-6">
          <!-- Alkes Table -->
          <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center justify-between mb-4">
              <h2 class="text-xl font-semibold text-gray-900">Tabel Alkes</h2>
              <button type="button" @click="addAlkes()" class="px-3 py-2 text-sm bg-purple-600 text-white rounded">+ Tambah Baris</button>
            </div>
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
                    <th class="p-2 border">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(row, i) in alkesList" :key="i" class="hover:bg-gray-50">
                    <td class="p-2 border"><input v-model="row.poli" class="w-full border rounded px-2 py-1" /></td>
                    <td class="p-2 border"><input v-model="row.dskp_alkes" class="w-full border rounded px-2 py-1" /></td>
                    <td class="p-2 border"><input v-model="row.jmlh_alkes" class="w-full border rounded px-2 py-1" /></td>
                    <td class="p-2 border"><input v-model="row.bya_alkes" @input="updateSubtotal(row, 'bya_alkes','disc_alkes','st_alkes','jmlh_alkes')" class="w-full border rounded px-2 py-1" /></td>
                    <td class="p-2 border"><input v-model="row.disc_alkes" @input="updateSubtotal(row, 'bya_alkes','disc_alkes','st_alkes','jmlh_alkes')" class="w-full border rounded px-2 py-1" /></td>
                    <td class="p-2 border"><input v-model="row.st_alkes" class="w-full border rounded px-2 py-1" /></td>
                    <td class="p-2 border"><input v-model="row.tanggal" type="date" class="w-full border rounded px-2 py-1" /></td>
                    <td class="p-2 border text-center"><button type="button" @click="removeAlkes(i)" class="px-2 py-1 text-xs bg-red-600 text-white rounded">Hapus</button></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Resep (RSP) Table -->
          <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center justify-between mb-4">
              <h2 class="text-xl font-semibold text-gray-900">Tabel Resep</h2>
              <button type="button" @click="addRsp()" class="px-3 py-2 text-sm bg-yellow-600 text-white rounded">+ Tambah Baris</button>
            </div>
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
                    <th class="p-2 border">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(row, i) in rspList" :key="i" class="hover:bg-gray-50">
                    <td class="p-2 border"><input v-model="row.dskp_rsp" class="w-full border rounded px-2 py-1" /></td>
                    <td class="p-2 border"><input v-model="row.jmlh_rsp" class="w-full border rounded px-2 py-1" /></td>
                    <td class="p-2 border"><input v-model="row.bya_rsp" @input="updateSubtotal(row, 'bya_rsp','disc_rsp','st_rsp','jmlh_rsp')" class="w-full border rounded px-2 py-1" /></td>
                    <td class="p-2 border"><input v-model="row.disc_rsp" @input="updateSubtotal(row, 'bya_rsp','disc_rsp','st_rsp','jmlh_rsp')" class="w-full border rounded px-2 py-1" /></td>
                    <td class="p-2 border"><input v-model="row.st_rsp" class="w-full border rounded px-2 py-1" /></td>
                    <td class="p-2 border"><input v-model="row.tanggal" type="date" class="w-full border rounded px-2 py-1" /></td>
                    <td class="p-2 border text-center"><button type="button" @click="removeRsp(i)" class="px-2 py-1 text-xs bg-red-600 text-white rounded">Hapus</button></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- STEP 3: Lainnya -->
        <div v-show="currentStep === 3" class="space-y-6">
          <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center justify-between mb-4">
              <h2 class="text-xl font-semibold text-gray-900">Tabel Lainnya</h2>
              <button type="button" @click="addLainnya()" class="px-3 py-2 text-sm bg-red-600 text-white rounded">+ Tambah Baris</button>
            </div>
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
                    <th class="p-2 border">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(row, i) in lainnyas" :key="i" class="hover:bg-gray-50">
                    <td class="p-2 border"><input v-model="row.dskp_lainnya" class="w-full border rounded px-2 py-1" /></td>
                    <td class="p-2 border"><input v-model="row.jmlh_lainnaya" class="w-full border rounded px-2 py-1" /></td>
                    <td class="p-2 border"><input v-model="row.bya_lainnya" @input="updateSubtotal(row, 'bya_lainnya','disc_lainnya','st_lainnya','jmlh_lainnaya')" class="w-full border rounded px-2 py-1" /></td>
                    <td class="p-2 border"><input v-model="row.disc_lainnya" @input="updateSubtotal(row, 'bya_lainnya','disc_lainnya','st_lainnya','jmlh_lainnaya')" class="w-full border rounded px-2 py-1" /></td>
                    <td class="p-2 border"><input v-model="row.st_lainnya" class="w-full border rounded px-2 py-1" /></td>
                    <td class="p-2 border"><input v-model="row.tanggal" type="date" class="w-full border rounded px-2 py-1" /></td>
                    <td class="p-2 border text-center"><button type="button" @click="removeLainnya(i)" class="px-2 py-1 text-xs bg-red-600 text-white rounded">Hapus</button></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- STEP 4: Total -->
        <div v-show="currentStep === 4" class="space-y-6">
          <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Ringkasan Total Biaya</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div class="p-4 border rounded">
                <p class="font-semibold mb-2">Konsultasi</p>
                <p>Total item: {{ konsuls.length }}</p>
                <p>Total biaya: {{ sumCurrency(konsuls.map(r => toNumber(r.jmlh_kons) * toNumber(r.bya_kons))) }} RP</p>
              </div>
              <div class="p-4 border rounded">
                <p class="font-semibold mb-2">Tindakan</p>
                <p>Total item: {{ tindaks.length }}</p>
                <p>Total biaya: {{ sumCurrency(tindaks.map(r => toNumber(r.jmlh_tindak) * toNumber(r.bya_tindak))) }} RP</p>
              </div>
              <div class="p-4 border rounded">
                <p class="font-semibold mb-2">Alkes</p>
                <p>Total item: {{ alkesList.length }}</p>
                <p>Total biaya: {{ sumCurrency(alkesList.map(r => toNumber(r.jmlh_alkes) * toNumber(r.bya_alkes))) }} RP</p>
              </div>
              <div class="p-4 border rounded">
                <p class="font-semibold mb-2">Resep</p>
                <p>Total item: {{ rspList.length }}</p>
                <p>Total biaya: {{ sumCurrency(rspList.map(r => toNumber(r.jmlh_rsp) * toNumber(r.bya_rsp))) }} RP</p>
              </div>
              <div class="p-4 border rounded">
                <p class="font-semibold mb-2">Lainnya</p>
                <p>Total item: {{ lainnyas.length }}</p>
                <p>Total biaya: {{ sumCurrency(lainnyas.map(r => toNumber(r.jmlh_lainnaya) * toNumber(r.bya_lainnya))) }} RP</p>
              </div>
              <div class="p-4 border rounded md:col-span-2 bg-gray-50">
                <p class="text-lg font-bold">Grand Total: {{ grandTotal }} RP</p>
              </div>
            </div>
          </div>

          <!-- Actions -->
          <div class="flex justify-end space-x-4">
            <Link :href="route('kasir.index')" class="px-6 py-3 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500">Batal</Link>
            <button type="submit" :disabled="isSubmitting" class="px-6 py-3 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50">
              {{ isSubmitting ? 'Menyimpan...' : 'Simpan Data' }}
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'

const props = defineProps({
    pasien: Object
})

const isSubmitting = ref(false)
const currentStep = ref(1)

// Informasi Pasien: sinkronisasi props.pasien ke form jika berubah
const form = useForm({
    pasien_id: props.pasien?.id || null,
    nama_pasien: props.pasien?.nama_pasien || '',
    alamat: props.pasien?.alamat || '',
    perawatan: props.pasien?.perawatan || '',
    Penjamin: props.pasien?.Penjamin || '',
    tanggal: props.pasien?.tanggal || new Date().toISOString().split('T')[0],

    konsul: [],
    tindak: [],
    alkes: [],
    rsp: [],
    lainnya: []
})

// Sinkronisasi data pasien jika props.pasien berubah (misal setelah "Ganti Pasien")
watch(
  () => props.pasien,
  (newPasien) => {
    if (newPasien) {
      form.pasien_id = newPasien.id || null
      form.nama_pasien = newPasien.nama_pasien || ''
      form.alamat = newPasien.alamat || ''
      form.perawatan = newPasien.perawatan || ''
      form.Penjamin = newPasien.Penjamin || ''
      form.tanggal = newPasien.tanggal || new Date().toISOString().split('T')[0]
    } else {
      form.pasien_id = null
      form.nama_pasien = ''
      form.alamat = ''
      form.perawatan = ''
      form.Penjamin = ''
      form.tanggal = new Date().toISOString().split('T')[0]
    }
  },
  { immediate: true }
)

const nextStep = () => { if (currentStep.value < 4) currentStep.value += 1 }
const prevStep = () => { if (currentStep.value > 1) currentStep.value -= 1 }

// Helpers
const toNumber = (val) => {
    if (!val) return 0
    const m = String(val).replace(/[^0-9,.]/g, '')
    return Number(m || 0)
}
const sumCurrency = (arr) => arr.reduce((s, v) => s + toNumber(v), 0)

/**
 * Update subtotal: (biaya * jumlah) - diskon
 * - Ambil jumlah (jumlah_*)
 * - Ambil biaya (bya_*)
 * - Hitung total = biaya * jumlah
 * - Hitung diskon (persen dari total)
 * - Subtotal = total - diskon
 */
function updateSubtotal(row, biayaKey, discKey, subtotalKey, jumlahKey) {
    const biaya = toNumber(row[biayaKey])
    const jumlah = toNumber(row[jumlahKey])
    const total = biaya * (jumlah || 1)
    const discValue = row[discKey]
    // Jika discount tidak ada atau null, subtotal = jumlah * biaya
    if (discValue === undefined || discValue === null || discValue === '' || discValue === '0%' || discValue === 0) {
        row[subtotalKey] = `${total} RP`
    } else {
        const discPercent = Number(String(discValue).replace(/[^0-9]/g, ''))
        const potongan = Math.floor((total * (discPercent || 0)) / 100)
        const subtotal = Math.max(total - potongan, 0)
        row[subtotalKey] = `${subtotal} RP`
    }
}

// Default row generator
const getDefaultRow = (keys) => {
    const defaultData = {}
    keys.forEach(key => {
        if (key.includes('disc')) {
            defaultData[key] = '0%'
        } else if (key.includes('st_')) {
            defaultData[key] = '0 RP'
        } else if (key.includes('jmlh_')) {
            defaultData[key] = '1'
        } else {
            defaultData[key] = ''
        }
    })
    defaultData.tanggal = new Date().toISOString().split('T')[0]
    return defaultData
}

const konsulKeys = ['dokter', 'dskp_kons', 'jmlh_kons', 'bya_kons', 'disc_kons', 'st_kons', 'tanggal']
const tindakKeys = ['dktr_tindak', 'dskp_tindak', 'jmlh_tindak', 'bya_tindak', 'disc_tindak', 'st_tindak', 'tanggal']
const alkesKeys = ['poli', 'dskp_alkes', 'jmlh_alkes', 'bya_alkes', 'disc_alkes', 'st_alkes', 'tanggal']
const rspKeys = ['dskp_rsp', 'jmlh_rsp', 'bya_rsp', 'disc_rsp', 'st_rsp', 'tanggal']
const lainnyaKeys = ['dskp_lainnya', 'jmlh_lainnaya', 'bya_lainnya', 'disc_lainnya', 'st_lainnya', 'tanggal']

const konsuls = ref([getDefaultRow(konsulKeys)])
const tindaks = ref([getDefaultRow(tindakKeys)])
const alkesList = ref([getDefaultRow(alkesKeys)])
const rspList = ref([getDefaultRow(rspKeys)])
const lainnyas = ref([getDefaultRow(lainnyaKeys)])

function addKonsul() { konsuls.value.push(getDefaultRow(konsulKeys)) }
function removeKonsul(i) { if (konsuls.value.length > 1) { konsuls.value.splice(i, 1) } }
function addTindak() { tindaks.value.push(getDefaultRow(tindakKeys)) }
function removeTindak(i) { if (tindaks.value.length > 1) { tindaks.value.splice(i, 1) } }
function addAlkes() { alkesList.value.push(getDefaultRow(alkesKeys)) }
function removeAlkes(i) { if (alkesList.value.length > 1) { alkesList.value.splice(i, 1) } }
function addRsp() { rspList.value.push(getDefaultRow(rspKeys)) }
function removeRsp(i) { if (rspList.value.length > 1) { rspList.value.splice(i, 1) } }
function addLainnya() { lainnyas.value.push(getDefaultRow(lainnyaKeys)) }
function removeLainnya(i) { if (lainnyas.value.length > 1) { lainnyas.value.splice(i, 1) } }

// Grand total: sum semua subtotal (sudah dikali jumlah dan diskon)
const grandTotal = computed(() => {
    // Helper: parse number safely
    const safeNumber = (val) => {
        if (typeof val === 'number') return val
        if (!val) return 0
        // Remove non-numeric except dot and minus
        return Number(String(val).replace(/[^0-9.-]/g, '')) || 0
    }

    // Only sum rows with valid biaya and jumlah
    const sumGroup = (rows, jumlahKey, biayaKey, discKey) => {
        return (rows.value || []).reduce((total, row) => {
            const jumlah = safeNumber(row[jumlahKey])
            const biaya = safeNumber(row[biayaKey])
            const discRaw = row[discKey] || '0'
            // Accept both "10%" and "10" as 10 percent
            const discPercent = safeNumber(discRaw)
            if (!biaya || !jumlah) return total // skip empty rows
            const subtotal = biaya * jumlah
            const potongan = Math.floor((subtotal * discPercent) / 100)
            return total + Math.max(subtotal - potongan, 0)
        }, 0)
    }

    let total = 0
    total += sumGroup(konsuls, 'jmlh_kons', 'bya_kons', 'disc_kons')
    total += sumGroup(tindaks, 'jmlh_tindak', 'bya_tindak', 'disc_tindak')
    total += sumGroup(alkesList, 'jmlh_alkes', 'bya_alkes', 'disc_alkes')
    total += sumGroup(rspList, 'jmlh_rsp', 'bya_rsp', 'disc_rsp')
    total += sumGroup(lainnyas, 'jmlh_lainnaya', 'bya_lainnya', 'disc_lainnya')
    return total
})

const submitForm = () => {
    isSubmitting.value = true

    // Only send plain JS objects, not refs, to Inertia
    const filterAndClean = (list, key) => {
        // Defensive: if list is a ref, get .value
        const arr = Array.isArray(list) ? list : (list?.value || [])
        // Fix: filter out rows where ALL fields except 'tanggal' are empty or default
        return arr.filter(row => {
            // Exclude 'tanggal' from check
            const keysToCheck = Object.keys(row).filter(k => k !== 'tanggal')
            // If at least one field (other than tanggal) is not empty string, not '0', not '0%', not '0 RP', not null/undefined
            return keysToCheck.some(k => {
                const v = (row[k] ?? '').toString().trim()
                return v !== '' && v !== '0' && v !== '0%' && v !== '0 RP'
            })
        }).map(row => {
            const cleanedRow = { ...row }
            const biayaKey = Object.keys(row).find(k => k.startsWith('bya_'))
            const subtotalKey = Object.keys(row).find(k => k.startsWith('st_'))
            const jumlahKey = Object.keys(row).find(k => k.startsWith('jmlh_'))

            // Pastikan biaya, jumlah, subtotal dikonversi ke number
            if (biayaKey) cleanedRow[biayaKey] = toNumber(row[biayaKey])
            if (jumlahKey) cleanedRow[jumlahKey] = toNumber(row[jumlahKey])
            if (subtotalKey) cleanedRow[subtotalKey] = toNumber(row[subtotalKey])

            Object.keys(cleanedRow).forEach(k => {
                if (typeof cleanedRow[k] === 'string') {
                     cleanedRow[k] = cleanedRow[k].replace(/ RP| %/g, '').trim()
                }
            })

            return cleanedRow
        })
    }

    // Always send plain arrays, not refs, to Inertia
    form.konsul = filterAndClean(konsuls, 'dskp_kons')
    form.tindak = filterAndClean(tindaks, 'dskp_tindak')
    form.alkes = filterAndClean(alkesList, 'dskp_alkes')
    form.rsp = filterAndClean(rspList, 'dskp_rsp')
    form.lainnya = filterAndClean(lainnyas, 'dskp_lainnya')

    if (form.konsul.length === 0) delete form.konsul
    if (form.tindak.length === 0) delete form.tindak
    if (form.alkes.length === 0) delete form.alkes
    if (form.rsp.length === 0) delete form.rsp
    if (form.lainnya.length === 0) delete form.lainnya

    // Remove any Vue refs from form before sending
    const plainForm = {}
    Object.keys(form).forEach(key => {
      if (typeof form[key]?.value !== 'undefined') {
        plainForm[key] = form[key].value
      } else {
        plainForm[key] = form[key]
      }
    })

    // Use form.post, but pass plainForm as data
    form.post(route('kasir.store'), {
        data: plainForm,
        onFinish: () => {
            isSubmitting.value = false
        },
        onError: (errors) => {
             console.error("Validation Errors:", errors);
             if (errors.konsul || errors.tindak) currentStep.value = 1;
             else if (errors.alkes || errors.rsp) currentStep.value = 2;
             else if (errors.lainnya) currentStep.value = 3;
        }
    })
}

defineExpose({ sumCurrency, grandTotal, updateSubtotal, toNumber })
</script>