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

    <div class="max-w-2xl mx-auto mt-12 bg-white rounded-2xl shadow-xl p-8 border border-gray-200">
      <!-- Header tanpa tombol kembali -->
      <h1 class="text-3xl font-extrabold text-blue-700 mb-6">Tambah Resep</h1>

      <!-- FORM -->
      <form @submit.prevent="submitResep" class="space-y-6">
        <!-- Obat Resep -->
        <div>
          <label class="block text-sm font-medium text-gray-600 mb-1">Obat Resep</label>
          <input v-model="form.obat_resep" type="text" required
                 class="w-full border border-gray-300 rounded-lg px-4 py-3 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"/>
        </div>

        <!-- Jumlah -->
        <div>
          <label class="block text-sm font-medium text-gray-600 mb-1">Jumlah</label>
          <input v-model="form.jumlah" type="number" min="1" required
                 class="w-full border border-gray-300 rounded-lg px-4 py-3 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"/>
        </div>

        <!-- Pemakaian -->
        <div>
          <label class="block text-sm font-medium text-gray-600 mb-1">Pemakaian Perhari</label>
          <input v-model="form.pemakaian_perhari" type="text" required
                 placeholder="Contoh: 3x sehari"
                 class="w-full border border-gray-300 rounded-lg px-4 py-3 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"/>
        </div>

        <!-- Tombol -->
        <div class="flex justify-end gap-3">
          <button type="button"
                  @click="router.visit('/resep')"
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
