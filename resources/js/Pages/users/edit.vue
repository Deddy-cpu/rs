<script setup lang="ts">
import { ref } from 'vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import PasswordInput from '@/Components/PasswordInput.vue'
import { Head, router, usePage } from '@inertiajs/vue3'

const page = usePage()
const user: any = page.props.user

// Form data
const form = ref({
  name: user.name || '',
  role: user.role || '',
  email: user.email || '',
  password: '', // kosong, diisi kalau mau update password
  password_confirmation: ''
})
const errors = ref<any>({})
const showPopup = ref(false)
const popupMessage = ref("")
const popupType = ref("success") // success | error
// State modal konfirmasi
const showConfirm = ref(false)


function openConfirmModal() {
  showConfirm.value = true
}

function closeConfirmModal() {
  showConfirm.value = false
}

function confirmUpdate() {
  router.put(`/users/${user.id}`, form.value, {
    onSuccess: () => {
      popupMessage.value = "User berhasil diperbarui!"
      popupType.value = "success"
      showPopup.value = true
      setTimeout(() => {
        showPopup.value = false
        router.visit('/users') // redirect setelah popup hilang
      }, 2000)
    },
    onError: (errors) => {
      popupMessage.value = "Gagal memperbarui user!"
      popupType.value = "error"
      showPopup.value = true
      console.error(errors)
      setTimeout(() => {
        showPopup.value = false
      }, 3000)
    }
  })
  closeConfirmModal()
}
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Edit User" />

    <div
      class="min-h-screen bg-cover bg-center flex items-center justify-center p-6"
      style="background-image: url('/images/bg-login.png')"
    >
      <div class="max-w-lg w-full bg-white/70 backdrop-blur-md shadow-lg rounded-lg p-6">
        <h1 class="text-2xl font-bold mb-4 text-blue-700">Edit User</h1>

        <form @submit.prevent="openConfirmModal" class="space-y-4">
          <!-- Name -->
          <div>
            <label class="block text-sm font-medium text-gray-700">Name</label>
            <input v-model="form.name" type="text"
                   class="mt-1 block w-full border rounded-lg px-3 py-2 shadow-sm focus:ring-blue-500 focus:border-blue-500"/>
          </div>

          <!-- Role -->
          <div>
            <label class="block text-sm font-medium text-gray-700">Role</label>
            <select v-model="form.role"
                    class="mt-1 block w-full border rounded-lg px-3 py-2 shadow-sm focus:ring-blue-500 focus:border-blue-500">
              <option disabled value="">Pilih Role</option>
              <option value="admin">Admin</option>
              <option value="kasir">Kasir</option>
              <option value="dokter">Dokter</option>
              <option value="perawat">Perawat</option>
              <option value="manajemen">Manajemen</option>
            </select>
          </div>

          <!-- Email -->
          <div>
            <label class="block text-sm font-medium text-gray-700">Email</label>
            <input v-model="form.email" type="email"
                   class="mt-1 block w-full border rounded-lg px-3 py-2 shadow-sm focus:ring-blue-500 focus:border-blue-500"/>
          </div>

          <!-- Password -->
          <div>
            <label class="block text-sm font-medium text-gray-700">Password (Kosongkan jika tidak ingin ubah)</label>
            <PasswordInput
              v-model="form.password"
              class="mt-1 w-full border rounded-lg px-3 py-2"
              autocomplete="new-password"
            />
          </div>

          <!-- Confirm Password -->
          <div>
            <label class="block text-sm font-medium text-gray-700">Confirm Password</label>
            <PasswordInput
              v-model="form.password_confirmation"
              class="mt-1 w-full border rounded-lg px-3 py-2"
              autocomplete="new-password"
            />
          </div>

          <div class="flex justify-end">
            <button type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
              Update
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal Konfirmasi -->
    <div v-if="showConfirm" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
      <div class="bg-white/90 backdrop-blur-md rounded-lg shadow-lg p-6 w-full max-w-md">
        <h2 class="text-lg font-semibold mb-4 text-gray-800">Konfirmasi</h2>
        <p class="text-gray-600 mb-6">Apakah Anda yakin ingin mengedit user ini?</p>
        <div class="flex justify-end space-x-3">
          <button @click="closeConfirmModal"
                  class="px-4 py-2 rounded bg-gray-300 text-gray-800 hover:bg-gray-400 transition">
            Batal
          </button>
          <button @click="confirmUpdate"
                  class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700 transition">
            Ya, Update
          </button>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
