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

    <div class="max-w-lg mx-auto mt-10 bg-white shadow rounded-lg p-6">
      <h1 class="text-2xl font-bold mb-4 text-blue-700">Tambah Resep</h1>

      <form @submit.prevent="submitResep" class="space-y-4">

        <div>
          <label class="block text-sm font-medium text-gray-700">Obat Resep</label>
          <input v-model="form.obat_resep" type="text"
                 required
                 class="mt-1 block w-full border rounded-lg px-3 py-2 shadow-sm focus:ring-blue-500 focus:border-blue-500"/>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">Jumlah</label>
          <input v-model="form.jumlah" type="number" min="1"
                 required
                 class="mt-1 block w-full border rounded-lg px-3 py-2 shadow-sm focus:ring-blue-500 focus:border-blue-500"/>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">Pemakaian Perhari</label>
          <input v-model="form.pemakaian_perhari" type="text"
                 required
                 placeholder="Contoh: 3x sehari"
                 class="mt-1 block w-full border rounded-lg px-3 py-2 shadow-sm focus:ring-blue-500 focus:border-blue-500"/>
        </div>

        <div class="flex justify-end">
          <button type="submit"
                  class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition">
            Simpan
          </button>
        </div>
      </form>
    </div>
  </AuthenticatedLayout>
</template>
