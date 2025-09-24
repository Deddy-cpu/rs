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
  bya: '',

  // detail transaksi
  resep: '',
  jumlah: '',
  deskripsi: '',
  biaya: ''


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
        <div>
          <label class="block">Biaya</label>
          <input v-model="form.bya" type="number" class="border p-2 w-full rounded" />
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
        <div>
          <label class="block">Biaya</label>
          <input v-model="form.biaya" type="number" class="border p-2 w-full rounded" />
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
