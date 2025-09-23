<script setup lang="ts">
import { ref } from 'vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, router, usePage } from '@inertiajs/vue3'

const page = usePage()
const user: any = page.props.user

// Form data
const form = ref({
  name: user.name || '',
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

  // show/hide password
  const showPassword = ref(false)
  function togglePassword() {
    showPassword.value = !showPassword.value
  }

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
      }, 2000) // popup hilang otomatis setelah 2 detik
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

    <div class="max-w-lg mx-auto mt-10 bg-white shadow rounded-lg p-6">
      <h1 class="text-2xl font-bold mb-4 text-blue-700">Edit User</h1>

      <form @submit.prevent="openConfirmModal" class="space-y-4">
        <!-- Name -->
        <div>
          <label class="block text-sm font-medium text-gray-700">Name</label>
          <input v-model="form.name" type="text"
                 class="mt-1 block w-full border rounded-lg px-3 py-2 shadow-sm focus:ring-blue-500 focus:border-blue-500"/>
        </div>

        <!-- Email -->
        <div>
          <label class="block text-sm font-medium text-gray-700">Email</label>
          <input v-model="form.email" type="email"
                 class="mt-1 block w-full border rounded-lg px-3 py-2 shadow-sm focus:ring-blue-500 focus:border-blue-500"/>
        </div>

        <!-- Password with visibility toggle -->
        <div>
          <label class="block text-sm font-medium text-gray-700">Password (Kosongkan jika tidak ingin ubah)</label>
          <div class="relative mt-1">
            <input
              :type="showPassword ? 'text' : 'password'"
              v-model="form.password"
              class="w-full border rounded-lg px-3 py-2 pr-10"
              autocomplete="new-password"
            />
          </div>
        </div>

        <!-- Confirm Password -->
        <div>
          <label class="block text-sm font-medium text-gray-700">Confirm Password</label>
          <div class="relative mt-1">
            <input
              :type="showPassword ? 'text' : 'password'"
              v-model="form.password_confirmation"
              class="w-full border rounded-lg px-3 py-2 pr-10"
              autocomplete="new-password"
            />
            <!-- Reuse same toggle button position visually: invisible placeholder to keep layout aligned -->
            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none" aria-hidden="true">
              <!-- empty placeholder to align fields -->
            </div>
          </div>
        </div>

        <div class="flex items-center justify-between">
          <div class="text-sm text-gray-500">
            <label class="inline-flex items-center cursor-pointer">
              <input type="checkbox" class="mr-2" :checked="showPassword" @change="togglePassword" />
              <span>Lihat password</span>
            </label>
          </div>

          <div class="flex justify-end">
            <button type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
              Update
            </button>
          </div>
        </div>
      </form>
    </div>

    <!-- Modal Konfirmasi -->
    <div v-if="showConfirm" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
      <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
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