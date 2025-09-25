<script setup lang="ts">
import { ref } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const form = ref({
  obat_resep: '',
  jumlah: 0,
  pemakaian_perhari: ''
})

function submitResep() {
  router.post('/resep', form.value, {
    onSuccess: () => {
      alert('Resep berhasil ditambahkan!')
      router.visit('/resep')
    },
    onError: (errors) => {
      console.error(errors)
      alert('Gagal menambahkan resep!')
    }
  })
}
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Tambah Resep" />

    <div
      class="min-h-screen bg-cover bg-center flex items-center justify-center p-6"
      style="background-image: url('/images/bg-login.png')"
    >
      <!-- Card transparan -->
      <div class="w-full max-w-lg mx-auto bg-white/40 backdrop-blur-md shadow-xl rounded-2xl p-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Tambah Resep</h2>

        <!-- FORM -->
        <form @submit.prevent="submitResep" class="space-y-4">
          <!-- Obat Resep -->
          <div>
            <label class="block text-sm font-medium text-gray-700">Obat Resep</label>
            <input v-model="form.obat_resep" type="text" required
              class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg 
                     focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"/>
          </div>

          <!-- Jumlah -->
          <div>
            <label class="block text-sm font-medium text-gray-700">Jumlah</label>
            <input v-model="form.jumlah" type="number" min="1" required
              class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg 
                     focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"/>
          </div>

          <!-- Pemakaian -->
          <div>
            <label class="block text-sm font-medium text-gray-700">Pemakaian Perhari</label>
            <input v-model="form.pemakaian_perhari" type="text" required
              placeholder="Contoh: 3x sehari"
              class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg 
                     focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"/>
          </div>

          <!-- Tombol -->
          <div class="flex justify-between gap-3">
            <button type="button"
              @click="router.visit('/resep')"
              class="w-1/2 bg-gray-200 text-gray-700 py-2 px-4 rounded-lg 
                     hover:bg-gray-300 transition font-medium flex items-center justify-center">
              ⬅ Batal
            </button>
            <button type="submit"
              class="w-1/2 bg-green-600 text-white py-2 px-4 rounded-lg 
                     hover:bg-green-700 transition font-semibold flex items-center justify-center">
              Simpan
            </button>
          </div>
        </form>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
