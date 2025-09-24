<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, router } from '@inertiajs/vue3'

// Props dari controller (resep yang mau diedit)
const props = defineProps({
  resep: {
    type: Object,
    required: true,
  },
})

// Inertia form
const form = useForm({
  obat_resep: props.resep.obat_resep,
  jumlah: props.resep.jumlah,
  pemakaian_perhari: props.resep.pemakaian_perhari,
})

// Submit update
const submit = () => {
  form.put(route('resep.update', props.resep.id))
}
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Edit Resep" />

    <div
      class="min-h-screen bg-cover bg-center flex items-center justify-center p-6"
      style="background-image: url('/images/bg-login.png')"
    >
      <!-- Card transparan -->
      <div class="w-full max-w-lg mx-auto bg-white/40 backdrop-blur-md shadow-xl rounded-2xl p-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Edit Resep</h2>

        <form @submit.prevent="submit" class="space-y-4">
          <!-- Obat Resep -->
          <div>
            <label class="block text-sm font-medium text-gray-700">Obat Resep</label>
            <input
              type="text"
              v-model="form.obat_resep"
              class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg 
                     focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              required
            />
          </div>

          <!-- Jumlah -->
          <div>
            <label class="block text-sm font-medium text-gray-700">Jumlah</label>
            <input
              type="number"
              v-model="form.jumlah"
              min="1"
              class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg 
                     focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              required
            />
          </div>

          <!-- Pemakaian Perhari -->
          <div>
            <label class="block text-sm font-medium text-gray-700">Pemakaian Perhari</label>
            <input
              type="text"
              v-model="form.pemakaian_perhari"
              class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg 
                     focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              required
            />
          </div>

          <!-- Tombol -->
          <div class="flex justify-between gap-3">
            <button
              type="button"
              @click="router.visit('/resep')"
              class="w-1/2 bg-gray-200 text-gray-700 py-2 px-4 rounded-lg 
                     hover:bg-gray-300 transition font-medium flex items-center justify-center"
              :disabled="form.processing"
            >
              ⬅ Batal
            </button>

            <button
              type="submit"
              class="w-1/2 bg-blue-600 text-white py-2 px-4 rounded-lg 
                     hover:bg-blue-700 transition font-semibold flex items-center justify-center"
              :disabled="form.processing"
            >
              Update
            </button>
          </div>
        </form>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
