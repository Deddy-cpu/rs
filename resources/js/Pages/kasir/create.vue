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
          <div
            class="flex-1 flex items-center gap-2"
          >
            <div :class="['px-3 py-1 rounded-full text-sm font-semibold', currentStep === 1 ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700']">1. Tindakan</div>
            <div class="w-8 h-px bg-gray-300"></div>
            <div :class="['px-3 py-1 rounded-full text-sm font-semibold', currentStep === 2 ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700']">2. Resep</div>
            <div class="w-8 h-px bg-gray-300"></div>
            <div :class="['px-3 py-1 rounded-full text-sm font-semibold', currentStep === 3 ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700']">3. Lainnya</div>
            <div class="w-8 h-px bg-gray-300"></div>
            <div :class="['px-3 py-1 rounded-full text-sm font-semibold', currentStep === 4 ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700']">4. Total</div>
          </div>
          <div class="flex items-center gap-2">
            <button @click="prevStep" :disabled="currentStep===1" class="px-3 py-2 text-sm border rounded disabled:opacity-50">Sebelumnya</button>
            <button v-if="currentStep<4" @click="nextStep" class="px-3 py-2 text-sm bg-blue-600 text-white rounded">Berikutnya</button>
          </div>
        </div>
      </div>

      <form @submit.prevent="submitForm" class="space-y-8">
        <!-- Informasi Pasien (selalu tampil di atas) -->
        <div class="bg-white shadow rounded-lg p-6">
          <h2 class="text-xl font-semibold text-gray-900 mb-4">Informasi Pasien</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
                    <td class="p-2 border"><input v-model="row.bya_kons" class="w-full border rounded px-2 py-1" /></td>
                    <td class="p-2 border"><input v-model="row.disc_kons" class="w-full border rounded px-2 py-1" /></td>
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
                    <td class="p-2 border"><input v-model="row.bya_tindak" class="w-full border rounded px-2 py-1" /></td>
                    <td class="p-2 border"><input v-model="row.disc_tindak" class="w-full border rounded px-2 py-1" /></td>
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
                    <td class="p-2 border"><input v-model="row.bya_alkes" class="w-full border rounded px-2 py-1" /></td>
                    <td class="p-2 border"><input v-model="row.disc_alkes" class="w-full border rounded px-2 py-1" /></td>
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
                    <td class="p-2 border"><input v-model="row.bya_rsp" class="w-full border rounded px-2 py-1" /></td>
                    <td class="p-2 border"><input v-model="row.disc_rsp" class="w-full border rounded px-2 py-1" /></td>
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
                    <td class="p-2 border"><input v-model="row.bya_lainnya" class="w-full border rounded px-2 py-1" /></td>
                    <td class="p-2 border"><input v-model="row.disc_lainnya" class="w-full border rounded px-2 py-1" /></td>
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
                <p>Total biaya: {{ sumCurrency(konsuls.map(r => r.bya_kons)) }} RP</p>
              </div>
              <div class="p-4 border rounded">
                <p class="font-semibold mb-2">Tindakan</p>
                <p>Total item: {{ tindaks.length }}</p>
                <p>Total biaya: {{ sumCurrency(tindaks.map(r => r.bya_tindak)) }} RP</p>
              </div>
              <div class="p-4 border rounded">
                <p class="font-semibold mb-2">Alkes</p>
                <p>Total item: {{ alkesList.length }}</p>
                <p>Total biaya: {{ sumCurrency(alkesList.map(r => r.bya_alkes)) }} RP</p>
              </div>
              <div class="p-4 border rounded">
                <p class="font-semibold mb-2">Resep</p>
                <p>Total item: {{ rspList.length }}</p>
                <p>Total biaya: {{ sumCurrency(rspList.map(r => r.bya_rsp)) }} RP</p>
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
import { ref, computed } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'

const props = defineProps({
  pasien: Object
})

const isSubmitting = ref(false)
const currentStep = ref(1)

const nextStep = () => { if (currentStep.value < 4) currentStep.value += 1 }
const prevStep = () => { if (currentStep.value > 1) currentStep.value -= 1 }

// Helpers
const toNumber = (val) => {
  if (!val) return 0
  const m = String(val).replace(/[^0-9]/g, '')
  return Number(m || 0)
}
const sumCurrency = (arr) => arr.reduce((s, v) => s + toNumber(v), 0)

// Table arrays (UI only). Backend expects single object each; we'll map first row.
const konsuls = ref([{ dokter: '', dskp_kons: '', jmlh_kons: '', bya_kons: '', disc_kons: '0%', st_kons: '0 RP', tanggal: new Date().toISOString().split('T')[0] }])
const tindaks = ref([{ dktr_tindak: '', dskp_tindak: '', jmlh_tindak: '', bya_tindak: '', disc_tindak: '0%', st_tindak: '0 RP', tanggal: new Date().toISOString().split('T')[0] }])
const alkesList = ref([{ poli: '', dskp_alkes: '', jmlh_alkes: '', bya_alkes: '', disc_alkes: '0%', st_alkes: '0 RP', tanggal: new Date().toISOString().split('T')[0] }])
const rspList = ref([{ dskp_rsp: '', jmlh_rsp: '', bya_rsp: '', disc_rsp: '0%', st_rsp: '0 RP', tanggal: new Date().toISOString().split('T')[0] }])
const lainnyas = ref([{ dskp_lainnya: '', jmlh_lainnaya: '', bya_lainnya: '', disc_lainnya: '0%', st_lainnya: '0 RP', tanggal: new Date().toISOString().split('T')[0] }])

function addKonsul(){ konsuls.value.push({ dokter: '', dskp_kons: '', jmlh_kons: '', bya_kons: '', disc_kons: '0%', st_kons: '0 RP', tanggal: new Date().toISOString().split('T')[0] }) }
function removeKonsul(i){ konsuls.value.splice(i,1) }
function addTindak(){ tindaks.value.push({ dktr_tindak: '', dskp_tindak: '', jmlh_tindak: '', bya_tindak: '', disc_tindak: '0%', st_tindak: '0 RP', tanggal: new Date().toISOString().split('T')[0] }) }
function removeTindak(i){ tindaks.value.splice(i,1) }
function addAlkes(){ alkesList.value.push({ poli: '', dskp_alkes: '', jmlh_alkes: '', bya_alkes: '', disc_alkes: '0%', st_alkes: '0 RP', tanggal: new Date().toISOString().split('T')[0] }) }
function removeAlkes(i){ alkesList.value.splice(i,1) }
function addRsp(){ rspList.value.push({ dskp_rsp: '', jmlh_rsp: '', bya_rsp: '', disc_rsp: '0%', st_rsp: '0 RP', tanggal: new Date().toISOString().split('T')[0] }) }
function removeRsp(i){ rspList.value.splice(i,1) }
function addLainnya(){ lainnyas.value.push({ dskp_lainnya: '', jmlh_lainnaya: '', bya_lainnya: '', disc_lainnya: '0%', st_lainnya: '0 RP', tanggal: new Date().toISOString().split('T')[0] }) }
function removeLainnya(i){ lainnyas.value.splice(i,1) }

const grandTotal = computed(() => {
  return sumCurrency(konsuls.value.map(r=>r.bya_kons))
    + sumCurrency(tindaks.value.map(r=>r.bya_tindak))
    + sumCurrency(alkesList.value.map(r=>r.bya_alkes))
    + sumCurrency(rspList.value.map(r=>r.bya_rsp))
    + sumCurrency(lainnyas.value.map(r=>r.bya_lainnya))
})

// Form for backend
const form = useForm({
  nama_pasien: props.pasien?.nama_pasien || '',
  alamat: props.pasien?.alamat || '',
  perawatan: props.pasien?.perawatan || '',
  Penjamin: props.pasien?.Penjamin || '',
  tanggal: props.pasien?.tanggal || new Date().toISOString().split('T')[0],
  konsul: { dokter: '', dskp_kons: '', jmlh_kons: '', bya_kons: '', disc_kons: '0%', st_kons: '0 RP', tanggal: new Date().toISOString().split('T')[0] },
  tindak: { dktr_tindak: '', dskp_tindak: '', jmlh_tindak: '', bya_tindak: '', disc_tindak: '0%', st_tindak: '0 RP', tanggal: new Date().toISOString().split('T')[0] },
  alkes: { poli: '', dskp_alkes: '', jmlh_alkes: '', bya_alkes: '', disc_alkes: '0%', st_alkes: '0 RP', tanggal: new Date().toISOString().split('T')[0] },
  rsp: { dskp_rsp: '', jmlh_rsp: '', bya_rsp: '', disc_rsp: '0%', st_rsp: '0 RP', tanggal: new Date().toISOString().split('T')[0] },
  lainnya: { dskp_lainnya: '', jmlh_lainnaya: '', bya_lainnya: '', disc_lainnya: '0%', st_lainnya: '0 RP', tanggal: new Date().toISOString().split('T')[0] }
})

const submitForm = () => {
  isSubmitting.value = true

  // Map FIRST ROW ONLY to backend (backend expects single object per kategori)
  if (konsuls.value.length) form.konsul = { ...konsuls.value[0] }
  if (tindaks.value.length) form.tindak = { ...tindaks.value[0] }
  if (alkesList.value.length) form.alkes = { ...alkesList.value[0] }
  if (rspList.value.length) form.rsp = { ...rspList.value[0] }
  if (lainnyas.value.length) form.lainnya = { ...lainnyas.value[0] }

  form.post(route('kasir.store'), {
    onFinish: () => {
      isSubmitting.value = false
    }
  })
}

// expose helpers used in template
defineExpose({ sumCurrency, grandTotal })
</script>
