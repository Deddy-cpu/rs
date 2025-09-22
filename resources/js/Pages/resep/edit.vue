<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

// Props dari controller (resep yang mau diedit)
const props = defineProps({
  resep: {
    type: Object,
    required: true,
  },
});


// Inertia form
const form = useForm({
  obat_resep: props.resep.obat_resep,
  jumlah: props.resep.jumlah,
  pemakaian_perhari: props.resep.pemakaian_perhari,
});

// Submit update
const submit = () => {
  form.put(route('resep.update', props.resep.id));
};
</script>

<template>
     <AuthenticatedLayout>
  <div class="max-w-lg mx-auto bg-white shadow p-6 rounded-lg">
    <h2 class="text-2xl font-bold mb-6">Edit Resep</h2>

    <form @submit.prevent="submit" class="space-y-4">
      <!-- Obat Resep -->
      <div>
        <label class="block text-gray-700">Obat Resep</label>
        <input
          type="text"
          v-model="form.obat_resep"
          class="w-full border rounded px-3 py-2"
        />
      
      </div>

      <!-- Jumlah -->
      <div>
        <label class="block text-gray-700">Jumlah</label>
        <input
          type="number"
          v-model="form.jumlah"
          class="w-full border rounded px-3 py-2"
        />
      
      </div>

      <!-- Pemakaian Perhari -->
      <div>
        <label class="block text-gray-700">Pemakaian Perhari</label>
        <input
          type="text"
          v-model="form.pemakaian_perhari"
          class="w-full border rounded px-3 py-2"
        />
      
      </div>

      <!-- Tombol -->
      <div class="flex justify-end space-x-2">
        <button
          type="submit"
          class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
          :disabled="form.processing"
        >
          Update
        </button>
      </div>
    </form>
  </div>
  </AuthenticatedLayout>
</template>
