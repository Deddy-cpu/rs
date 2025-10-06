<script setup lang="ts">
import { ref } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const form = ref({
  nama_pasien: '',
  alamat: '',
  perawatan: '',
  Penjamin: '',
  tanggal: '',
})

function submitPasien() {
  router.post('/pasien', form.value, {
    onSuccess: () => {
      alert('Pasien berhasil ditambahkan!')
      router.visit('/kasir')
    },
    onError: (errors) => {
      console.error(errors)
      alert('Gagal menambahkan pasien!')
    }
  })
}
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Tambah Pasien" />

    <div
      class="min-h-screen bg-cover bg-center flex items-center justify-center p-6"
      style="background-image: url('/images/bg-login.png')"
    >
      <!-- CARD FORM -->
      <div class="w-full max-w-lg mx-auto bg-white/40 backdrop-blur-md shadow-xl rounded-2xl p-8">
        <!-- Header -->
        <h1 class="text-2xl font-bold text-center text-gray-800 mb-6">Tambah Pasien</h1>

        <!-- FORM -->
        <form @submit.prevent="submitPasien" class="space-y-5">
          <!-- Nama Pasien -->
          <div>
            <label class="block text-sm font-medium text-gray-600 mb-1">Nama Pasien</label>
            <input v-model="form.nama_pasien" type="text" placeholder="Masukkan nama pasien" required
                   class="w-full border border-gray-300 rounded-lg px-4 py-3 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"/>
          </div>

          <!-- Alamat -->
          <div>
            <label class="block text-sm font-medium text-gray-600 mb-1">Alamat</label>
            <input v-model="form.alamat" type="text" placeholder="Masukkan alamat" required
                   class="w-full border border-gray-300 rounded-lg px-4 py-3 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"/>
          </div>

          <!-- Perawatan -->
          <div>
            <label class="block text-sm font-medium text-gray-600 mb-1">Perawatan</label>
            <input v-model="form.perawatan" type="text" placeholder="Jenis perawatan" required
                   class="w-full border border-gray-300 rounded-lg px-4 py-3 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"/>
          </div>

          <!-- Penjamin -->
          <div>
            <label class="block text-sm font-medium text-gray-600 mb-1">Penjamin</label>
            <input v-model="form.Penjamin" type="text" placeholder="Nama penjamin" required
                   class="w-full border border-gray-300 rounded-lg px-4 py-3 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"/>
          </div>

          <!-- Tanggal -->
          <div>
            <label class="block text-sm font-medium text-gray-600 mb-1">Tanggal</label>
            <input v-model="form.tanggal" type="date" required
                   class="w-full border border-gray-300 rounded-lg px-4 py-3 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"/>
          </div>

          <!-- Tombol -->
          <div class="flex justify-between gap-3 pt-2">
            <button type="button"
                    @click="router.visit('/kasir')"
                    class="flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg bg-gray-200 text-gray-700 font-medium hover:bg-gray-300 transition">
              ⬅️ Batal
            </button>
            <button type="submit"
                    class="px-6 py-2.5 rounded-lg bg-green-600 text-white font-semibold hover:bg-green-700 shadow transition">
              Simpan
            </button>
          </div>
        </form>
      </div>
    </div>
  </AuthenticatedLayout>
</template>