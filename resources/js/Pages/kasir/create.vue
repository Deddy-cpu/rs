<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const step = ref(1) // halaman aktif

// form gabungan pasien + transaksi + detail
const form = useForm({
  // pasien
  nama_pasien: '',
  alamat: '',
  perawatan: '',
  Penjamin: '',
  tanggal : '',

  // transaksi
  dokter: '',
  tindakan: '',
  jmlh: '',
  dskrps: '',

  // detail transaksi
  resep: '',
  jumlah: '',
  deskripsi: ''


})

const nextStep = () => {
  if (step.value < 3) step.value++
}

const prevStep = () => {
  if (step.value > 1) step.value--
}

const submit = () => {
  form.post(route('kasir.store'), {
    onSuccess: () => {
      alert('Data berhasil disimpan!')
    },
    onError: (errors) => {
      console.error(errors)
      alert('Gagal menyimpan data!')
    }
  })
}
</script>

<template>
  <AuthenticatedLayout>
    <div class="p-6">
      <h2 class="text-xl font-bold mb-4">Tambah Data Pasien & Transaksi</h2>

      <!-- Step 1: Pasien -->
      <div v-if="step === 1" class="space-y-4">
        <h3 class="font-semibold">👤 Data Pasien</h3>
        <div>
          <label class="block">Nama Pasien</label>
          <input v-model="form.nama_pasien" type="text" class="border p-2 w-full rounded" />
        </div>
        <div>
          <label class="block">alamat</label>
          <input v-model="form.alamat" type="text" class="border p-2 w-full rounded" />
        </div>
        <div>
          <label class="block">perawatan</label>
          <input v-model="form.perawatan" type="text" class="border p-2 w-full rounded" />  
        </div>
        <div>
          <label class="block">penjamin</label>
          <input v-model="form.Penjamin" type="text" class="border p-2 w-full rounded" />
        </div>
       
      

        <div>
          <label class="block">tanggal</label>
          <input v-model="form.tanggal" type="date" class="border p-2 w-full rounded" />  
        </div>
          
      </div>

      <!-- Step 2: Transaksi -->
      <div v-if="step === 2" class="space-y-4">
        <h3 class="font-semibold">Tindakan</h3>
        <div>
          <label class="block">Dokter</label>
          <input v-model="form.dokter" type="text" class="border p-2 w-full rounded" />
        </div>
        <div>
          <label class="block">Tindakan</label>
          <input v-model="form.tindakan" type="text" class="border p-2 w-full rounded" />
        </div>
        <div>
          <label class="block">Jumlah</label>
          <input v-model="form.jmlh" type="number" class="border p-2 w-full rounded" />
        </div>
        <div>
          <label class="block">Deskripsi</label>
          <input v-model="form.dskrps" type="text" class="border p-2 w-full rounded" />
        </div>
      </div>
     

      <!-- Step 3: Detail Transaksi -->
      <div v-if="step === 3" class="space-y-4">
        <h3 class="font-semibold">Resep</h3>
        <div>
          <label class="block">Resep</label>
          <input v-model="form.resep" type="text" class="border p-2 w-full rounded" />
          </div>
        <div>
          <label class="block">Jumlah</label> 
          <input v-model="form.jumlah" type="number" class="border p-2 w-full rounded" />
          </div>
        <div>
          <label class="block">Deskripsi</label>
          <input v-model="form.deskripsi" type="text" class="border p-2 w-full rounded" />
          </div>
      </div>

      <!-- Navigation Buttons -->
      <div class="flex justify-between mt-6">
        <button
          v-if="step > 1"
          @click="prevStep"
          class="px-4 py-2 bg-gray-400 text-white rounded hover:bg-gray-500"
        >
          ← Kembali
        </button>

        <button
          v-if="step < 3"
          @click="nextStep"
          class="ml-auto px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
        >
          Selanjutnya →
        </button>

        <button
          v-if="step === 3"
          @click="submit"
          class="ml-auto px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700"
        >
          Simpan Data
        </button>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Head, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

// props: optionally pass pasien (object) when creating transaksi untuk pasien tertentu
// optionally pass pasienList (array) when you want a select list
const props = defineProps({
  pasien: Object,      // optional single pasien (page used under /pasien/{id}/transaksi/create)
  pasienList: { type: Array, default: () => [] } // optional list for select
})
const propsPasien = props.pasien ?? null

// useForm dari Inertia: akan otomatis handle errors -> form.errors
const form = useForm({
  // if page has an existing pasien, fill pasien_id
  pasien_id: propsPasien?.id ?? '',
  // patient create fields (only if user wants to create new patient inline)
  nama_pasien: '',
  alamat: '',
  perawatan: '',
  Penjamin: '',
  tanggal: '',

  // transactions array (support multiple)
  transaksi: [
    {
      dokter: '',    // default value (not null)
      tindakan: '',
      jmlh: 1,
      dskrps: '',
      bya: 0,
      detail: [
        { resep: '', jumlah: 1, deskripsi: '', biaya: 0 }
      ]
    }
  ]
})

// helper: akses errors (form.errors)
const errors = computed(() => form.errors)

// CRUD helpers
function addTransaksi() {
  form.transaksi.push({
    dokter: '-',
    tindakan: '-',
    jmlh: 1,
    dskrps: '',
    bya: 0,
    detail: [{ resep: '', jumlah: 1, deskripsi: '', biaya: 0 }]
  })
}
function removeTransaksi(i) {
  form.transaksi.splice(i, 1)
}
function addDetail(tIndex) {
  form.transaksi[tIndex].detail.push({ resep: '', jumlah: 1, deskripsi: '', biaya: 0 })
}
function removeDetail(tIndex, dIndex) {
  form.transaksi[tIndex].detail.splice(dIndex, 1)
}

// submit: send payload. For backward compatibility we also send dokter/tindakan/jmlh/bya from first transaction at top-level.
function submit() {
  // quick client-side check for pasien_id or nama_pasien
  if (!form.pasien_id && !form.nama_pasien) {
    alert('Harap pilih pasien atau isi data pasien baru.')
    return
  }

  // ensure transaksi[0] has required fields (front-end guard)
  const t0 = form.transaksi[0]
  if (!t0?.dokter || !t0?.tindakan) {
    alert('Isi minimal dokter dan tindakan pada transaksi pertama.')
    return
  }

  // prepare payload: use form directly, but also add top-level keys for compatibility
  const payload = {
    ...form,
    dokter: t0.dokter,
    tindakan: t0.tindakan,
    jmlh: t0.jmlh,
    bya: t0.bya
  }

  // post (adjust url if your route is different)
  form.post('/kasir', {
    data: payload,
    onError: (errs) => {
      // Inertia will populate form.errors automatically; optional console log
      console.log('Validation errors:', errs)
    },
    onSuccess: () => {
      alert('Sukses: data tersimpan.')
      // reset form or redirect as you need
    }
  })
}
</script>

<style scoped>
/* kecilkan style supaya rapi */
</style>
