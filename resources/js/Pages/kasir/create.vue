<script setup lang="ts">
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

/**
 * Jika proyekmu menggunakan Ziggy/route() helper, route(...) akan dipakai.
 * Jika tidak, fallback akan mengirim ke '/kasir' (ubah kalau endpoint lain).
 */
declare const route: any // biar TypeScript tidak protes kalau Ziggy ada

// step yang aktif (1..3)
const step = ref<number>(1)

// definisi tipe form (sama seperti fields yang kamu pakai)
type KasirForm = {
  nama_pasien: string
  alamat: string
  perawatan: string
  Penjamin: string
  tanggal: string

  dokter: string
  tindakan: string
  jmlh: number
  dskrps: string

  resep: string
  jumlah: number
  deskripsi: string
}

// gunakan useForm dari Inertia (reactive + handling errors/processing)
const form = useForm<KasirForm>({
  nama_pasien: '',
  alamat: '',
  perawatan: '',
  Penjamin: '',
  tanggal: '',

  dokter: '',
  tindakan: '',
  jmlh: 0,
  dskrps: '',

  resep: '',
  jumlah: 0,
  deskripsi: ''
})

// navigasi step
const nextStep = () => {
  if (step.value < 3) step.value++
}
const prevStep = () => {
  if (step.value > 1) step.value--
}

// submit ke server (pakai Ziggy jika ada)
const submit = () => {
  const target = (typeof route === 'function' && route('kasir.store')) ? route('kasir.store') : '/kasir'
  form.post(target, {
    onSuccess: () => {
      // beri umpan balik, bisa diganti dengan Inertia visit jika ingin redirect
      alert('Data berhasil disimpan!')
      // contoh redirect ke halaman list kasir (ubah sesuai route)
      if (typeof route === 'function') {
        // jika Ziggy tersedia, gunakan route('kasir.index')
        try { window.location.href = route('kasir.index') } catch { /* ignore */ }
      } else {
        window.location.href = '/kasir'
      }
    },
    onError: (errors) => {
      console.error('Validation errors:', errors)
      // errors sudah tersedia di form.errors (ditampilkan di bawah input)
      // jangan perlu alert lagi — tapi tetap bisa jika mau:
      // alert('Gagal menyimpan data! Periksa input.')
    }
  })
}
</script>

<template>
  <AuthenticatedLayout>
    <div class="min-h-screen flex items-center justify-center bg-gray-50 p-6">
      <div class="w-full max-w-3xl bg-white rounded-2xl shadow-lg p-8">
        <h2 class="text-2xl font-bold text-center text-blue-700 mb-6">
          Tambah Data Pasien & Transaksi
        </h2>

     <!-- Stepper -->
<div class="mb-6">
  <div class="flex items-center w-full">
    <!-- Step 1 -->
    <div class="flex items-center flex-1">
      <div
        :class="[
          'w-9 h-9 rounded-full flex items-center justify-center font-semibold z-10',
          step === 1 ? 'bg-blue-600 text-white' : step > 1 ? 'bg-green-500 text-white' : 'border border-gray-300 text-gray-600'
        ]"
      >
        1
      </div>
      <div class="flex-1 h-1 -ml-1" :class="step > 1 ? 'bg-green-500' : 'bg-gray-200'"></div>
    </div>

    <!-- Step 2 -->
    <div class="flex items-center flex-1">
      <div
        :class="[
          'w-9 h-9 rounded-full flex items-center justify-center font-semibold z-10',
          step === 2 ? 'bg-blue-600 text-white' : step > 2 ? 'bg-green-500 text-white' : 'border border-gray-300 text-gray-600'
        ]"
      >
        2
      </div>
      <div class="flex-1 h-1 -ml-1" :class="step > 2 ? 'bg-green-500' : 'bg-gray-200'"></div>
    </div>

    <!-- Step 3 -->
    <div class="flex items-center">
      <div
        :class="[
          'w-9 h-9 rounded-full flex items-center justify-center font-semibold z-10',
          step === 3 ? 'bg-blue-600 text-white' : 'border border-gray-300 text-gray-600'
        ]"
      >
        3
      </div>
    </div>
  </div>

  <!-- Label bawah -->
  <div class="flex justify-between text-xs text-gray-500 mt-2">
    <div class="w-1/3 text-left">Data Pasien</div>
    <div class="w-1/3 text-center">Transaksi</div>
    <div class="w-1/3 text-right">Detail Resep</div>
  </div>
</div>

        <!-- FORM BODY -->
        <form @submit.prevent="submit" class="space-y-6">
          <!-- Step 1 -->
          <div v-if="step === 1" class="space-y-4">
            <h3 class="text-lg font-medium text-gray-700">👤 Data Pasien</h3>

            <div>
              <label class="block text-sm text-gray-600 mb-1">Nama Pasien</label>
              <input v-model="form.nama_pasien" type="text" class="w-full rounded border px-3 py-2 focus:ring-2 focus:ring-blue-300" />
              <p v-if="form.errors && form.errors.nama_pasien" class="text-sm text-red-600 mt-1">{{ form.errors.nama_pasien }}</p>
            </div>

            <div>
              <label class="block text-sm text-gray-600 mb-1">Alamat</label>
              <input v-model="form.alamat" type="text" class="w-full rounded border px-3 py-2 focus:ring-2 focus:ring-blue-300" />
              <p v-if="form.errors && form.errors.alamat" class="text-sm text-red-600 mt-1">{{ form.errors.alamat }}</p>
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm text-gray-600 mb-1">Perawatan</label>
                <input v-model="form.perawatan" type="text" class="w-full rounded border px-3 py-2 focus:ring-2 focus:ring-blue-300" />
                <p v-if="form.errors && form.errors.perawatan" class="text-sm text-red-600 mt-1">{{ form.errors.perawatan }}</p>
              </div>

              <div>
                <label class="block text-sm text-gray-600 mb-1">Penjamin</label>
                <input v-model="form.Penjamin" type="text" class="w-full rounded border px-3 py-2 focus:ring-2 focus:ring-blue-300" />
                <p v-if="form.errors && form.errors.Penjamin" class="text-sm text-red-600 mt-1">{{ form.errors.Penjamin }}</p>
              </div>
            </div>

            <div>
              <label class="block text-sm text-gray-600 mb-1">Tanggal</label>
              <input v-model="form.tanggal" type="date" class="w-full rounded border px-3 py-2 focus:ring-2 focus:ring-blue-300" />
              <p v-if="form.errors && form.errors.tanggal" class="text-sm text-red-600 mt-1">{{ form.errors.tanggal }}</p>
            </div>
          </div>

          <!-- Step 2 -->
          <div v-if="step === 2" class="space-y-4">
            <h3 class="text-lg font-medium text-gray-700">💳 Data Transaksi</h3>

            <div>
              <label class="block text-sm text-gray-600 mb-1">Dokter</label>
              <input v-model="form.dokter" type="text" class="w-full rounded border px-3 py-2 focus:ring-2 focus:ring-blue-300" />
              <p v-if="form.errors && form.errors.dokter" class="text-sm text-red-600 mt-1">{{ form.errors.dokter }}</p>
            </div>

            <div>
              <label class="block text-sm text-gray-600 mb-1">Tindakan</label>
              <input v-model="form.tindakan" type="text" class="w-full rounded border px-3 py-2 focus:ring-2 focus:ring-blue-300" />
              <p v-if="form.errors && form.errors.tindakan" class="text-sm text-red-600 mt-1">{{ form.errors.tindakan }}</p>
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm text-gray-600 mb-1">Jumlah</label>
                <input v-model.number="form.jmlh" type="number" min="0" class="w-full rounded border px-3 py-2 focus:ring-2 focus:ring-blue-300" />
                <p v-if="form.errors && form.errors.jmlh" class="text-sm text-red-600 mt-1">{{ form.errors.jmlh }}</p>
              </div>

              <div>
                <label class="block text-sm text-gray-600 mb-1">Deskripsi</label>
                <input v-model="form.dskrps" type="text" class="w-full rounded border px-3 py-2 focus:ring-2 focus:ring-blue-300" />
                <p v-if="form.errors && form.errors.dskrps" class="text-sm text-red-600 mt-1">{{ form.errors.dskrps }}</p>
              </div>
            </div>
          </div>

          <!-- Step 3 -->
          <div v-if="step === 3" class="space-y-4">
            <h3 class="text-lg font-medium text-gray-700">💊 Detail Resep</h3>

            <div>
              <label class="block text-sm text-gray-600 mb-1">Resep</label>
              <input v-model="form.resep" type="text" class="w-full rounded border px-3 py-2 focus:ring-2 focus:ring-blue-300" />
              <p v-if="form.errors && form.errors.resep" class="text-sm text-red-600 mt-1">{{ form.errors.resep }}</p>
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm text-gray-600 mb-1">Jumlah</label>
                <input v-model.number="form.jumlah" type="number" min="0" class="w-full rounded border px-3 py-2 focus:ring-2 focus:ring-blue-300" />
                <p v-if="form.errors && form.errors.jumlah" class="text-sm text-red-600 mt-1">{{ form.errors.jumlah }}</p>
              </div>

              <div>
                <label class="block text-sm text-gray-600 mb-1">Deskripsi</label>
                <input v-model="form.deskripsi" type="text" class="w-full rounded border px-3 py-2 focus:ring-2 focus:ring-blue-300" />
                <p v-if="form.errors && form.errors.deskripsi" class="text-sm text-red-600 mt-1">{{ form.errors.deskripsi }}</p>
              </div>
            </div>
          </div>

          <!-- Navigation -->
          <div class="flex items-center justify-between mt-6">
            <button
              v-if="step > 1"
              type="button"
              @click="prevStep"
              class="px-4 py-2 bg-gray-400 text-white rounded-lg hover:bg-gray-500 transition"
            >
              ← Kembali
            </button>

            <div class="ml-auto flex gap-3">
              <button
                v-if="step < 3"
                type="button"
                @click="nextStep"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition"
              >
                Selanjutnya →
              </button>

              <button
                v-if="step === 3"
                type="submit"
                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition"
              >
                ✅ Simpan Data
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
