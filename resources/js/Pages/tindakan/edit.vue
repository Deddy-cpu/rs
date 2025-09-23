<script setup lang="ts">
import { ref } from 'vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, router, usePage } from '@inertiajs/vue3'

const page = usePage()
const tindakan: any = page.props.tindakan

// state form
const form = ref({
  dokter: tindakan.dokter || '',
  tindakan: tindakan.tindakan || '',
  jumlah: tindakan.jumlah || 0,
  id_pasien: tindakan.id_pasien || ''
})

// state modal
const showConfirm = ref(false)

function openConfirmModal() {
  showConfirm.value = true
}

function closeConfirmModal() {
  showConfirm.value = false
}

function confirmUpdate() {
  router.put(`/tindakan/${tindakan.id}`, form.value, {
    onSuccess: () => {
      alert("Tindakan berhasil diperbarui!")
      router.visit('/tindakan')
    },
    onError: (errors) => {
      console.error(errors)
      alert("Gagal memperbarui tindakan!")
    }
  })
  closeConfirmModal()
}

// tombol kembali
function goBack() {
  router.visit('/tindakan')
}
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Edit Tindakan" />

    <div class="max-w-lg mx-auto mt-10 bg-white shadow rounded-lg p-6">
      <h1 class="text-2xl font-bold mb-4 text-blue-700">Edit Tindakan</h1>

      <!-- FORM -->
      <form @submit.prevent="openConfirmModal" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700">ID Pasien</label>
          <input v-model="form.id_pasien" type="text"
                 class="mt-1 block w-full border rounded-lg px-3 py-2 shadow-sm focus:ring-blue-500 focus:border-blue-500"/>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">Dokter</label>
          <input v-model="form.dokter" type="text"
                 class="mt-1 block w-full border rounded-lg px-3 py-2 shadow-sm focus:ring-blue-500 focus:border-blue-500"/>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">Tindakan</label>
          <input v-model="form.tindakan" type="text"
                 class="mt-1 block w-full border rounded-lg px-3 py-2 shadow-sm focus:ring-blue-500 focus:border-blue-500"/>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">Jumlah</label>
          <input v-model="form.jumlah" type="number"
                 class="mt-1 block w-full border rounded-lg px-3 py-2 shadow-sm focus:ring-blue-500 focus:border-blue-500"/>
        </div>

        <!-- Tombol Update + Kembali -->
        <div class="flex justify-between">
          <button type="button"
                  @click="goBack"
                  class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 transition">
            Kembali
          </button>
          <button type="submit"
                  class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
            Update
          </button>
        </div>
      </form>
    </div>

    <!-- Modal Konfirmasi -->
    <div v-if="showConfirm" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
      <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
        <h2 class="text-lg font-semibold mb-4 text-gray-800">Konfirmasi</h2>
        <p class="text-gray-600 mb-6">Apakah Anda yakin ingin mengedit data ini?</p>
        <div class="flex justify-end space-x-3">
          <button @click="closeConfirmModal"
                  class="px-4 py-2 rounded bg-gray-300 text-gray-800 hover:bg-gray-400 transition">
            Batal
          </button>
          <button @click="confirmUpdate"
                  class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700 transition">
            Ya, Edit
          </button>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
