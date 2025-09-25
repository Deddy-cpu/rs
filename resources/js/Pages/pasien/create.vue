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
      alert('pasien berhasil ditambahkan!')
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

    <div class="max-w-2xl mx-auto mt-12 bg-white rounded-2xl shadow-xl p-8 border border-gray-200">
      <!-- Header tanpa tombol kembali -->
      <h1 class="text-3xl font-extrabold text-blue-700 mb-6">Tambah pasien</h1>

      <!-- FORM -->
      <form @submit.prevent="submitPasien" class="space-y-6">
        <!-- Obat Resep -->
        <div>
          <label class="block text-sm font-medium text-gray-600 mb-1">Nama pasien</label>
          <input v-model="form.nama_pasien" type="text" required
                 class="w-full border border-gray-300 rounded-lg px-4 py-3 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"/>
        </div>

        <!-- Alamat -->
        <div>
          <label class="block text-sm font-medium text-gray-600 mb-1">alamat</label>
          <input v-model="form.alamat" type="text" required
                 class="w-full border border-gray-300 rounded-lg px-4 py-3 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"/>
        </div>

        <!-- Perawatan -->
        <div>
          <label class="block text-sm font-medium text-gray-600 mb-1">Perawatan</label>
          <input v-model="form.perawatan" type="text" required
                 
                 class="w-full border border-gray-300 rounded-lg px-4 py-3 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"/>
        </div>
         
        <!-- Penjamin -->
        <div>
          <label class="block text-sm font-medium text-gray-600 mb-1">Penjamin</label>
          <input v-model="form.Penjamin" type="text" required
                 
                 class="w-full border border-gray-300 rounded-lg px-4 py-3 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"/>
        </div>

        <!-- Tanggal -->
        <div>
          <label class="block text-sm font-medium text-gray-600 mb-1">tanggal</label>
          <input v-model="form.tanggal" type="date" required
                
                 class="w-full border border-gray-300 rounded-lg px-4 py-3 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"/>
        </div>

        <!-- Tombol -->
        <div class="flex justify-end gap-3">
          <button type="button"
                  @click="router.visit('/kasir')"
                  class="px-5 py-2.5 rounded-lg bg-gray-400 text-white hover:bg-gray-500 transition">
            Batal
          </button>
          <button type="submit"
                  class="px-5 py-2.5 rounded-lg bg-green-600 text-white font-semibold hover:bg-green-700 shadow transition">
            Simpan
          </button>
        </div>
      </form>
    </div>
  </AuthenticatedLayout>
</template>
