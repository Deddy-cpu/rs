<script setup lang="ts">
import { ref } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { useAuth } from '@/composables/useAuth'

interface Dokter {
  id: number
  user_id: number
  nama_dokter: string
  aktif: string
  user: {
    id: number
    name: string
    email: string
    role: string
  }
}

const props = defineProps<{
  dokter: Dokter
}>()

const { isAdmin } = useAuth()

const form = ref({
  name: props.dokter.user.name,
  email: props.dokter.user.email,
  password: '',
  nama_dokter: props.dokter.nama_dokter,
  aktif: props.dokter.aktif,
})

const loading = ref(false)
const error = ref('')
const success = ref(false)

function submitEdit() {
  loading.value = true
  error.value = ''
  
  // Remove empty password from form data
  const formData = { ...form.value }
  if (!formData.password) {
    delete formData.password
  }
  
  router.put(`/dokter/${props.dokter.id}`, formData, {
    onSuccess: () => {
      success.value = true
      setTimeout(() => {
        router.visit('/dokter')
      }, 1500)
    },
    onError: (errors) => {
      error.value = 'Gagal mengupdate data dokter.'
      console.error('Validation errors:', errors)
    },
    onFinish: () => {
      loading.value = false
    }
  })
}
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Edit Dokter" />

    <div class="min-h-screen bg-cover bg-center p-6" style="background-image: url('/images/bg-login.png')">
      <div class="bg-white/70 backdrop-blur-sm rounded-xl p-6 shadow-lg max-w-2xl mx-auto">
        
        <!-- Header -->
        <div class="mb-6 text-center">
          <h1 class="text-3xl font-extrabold text-blue-700 tracking-wide flex items-center gap-2 justify-center">
            ✏️ Edit Dokter
          </h1>
          <p class="text-gray-600 mt-2">Perbarui informasi dokter</p>
        </div>

        <!-- Form -->
        <form @submit.prevent="submitEdit" class="space-y-5">
          <!-- Nama User -->
          <div>
            <label class="block text-sm font-medium text-gray-600 mb-1">Nama User</label>
            <input
              v-model="form.name"
              type="text"
              required
              class="w-full border border-gray-300 rounded-lg px-4 py-3 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              placeholder="Masukkan nama user"
            />
          </div>

          <!-- Email -->
          <div>
            <label class="block text-sm font-medium text-gray-600 mb-1">Email</label>
            <input
              v-model="form.email"
              type="email"
              required
              class="w-full border border-gray-300 rounded-lg px-4 py-3 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              placeholder="Masukkan email"
            />
          </div>

          <!-- Password (Optional) -->
          <div>
            <label class="block text-sm font-medium text-gray-600 mb-1">Password Baru (Opsional)</label>
            <input
              v-model="form.password"
              type="password"
              class="w-full border border-gray-300 rounded-lg px-4 py-3 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              placeholder="Kosongkan jika tidak ingin mengubah password"
            />
            <p class="text-xs text-gray-500 mt-1">Kosongkan jika tidak ingin mengubah password</p>
          </div>

          <!-- Nama Dokter -->
          <div>
            <label class="block text-sm font-medium text-gray-600 mb-1">Nama Dokter</label>
            <input
              v-model="form.nama_dokter"
              type="text"
              required
              class="w-full border border-gray-300 rounded-lg px-4 py-3 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              placeholder="Masukkan nama dokter"
            />
          </div>

          <!-- Status Aktif -->
          <div>
            <label class="block text-sm font-medium text-gray-600 mb-1">Status</label>
            <select
              v-model="form.aktif"
              class="w-full border border-gray-300 rounded-lg px-4 py-3 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              required
            >
              <option value="Ya">Aktif</option>
              <option value="Tidak">Nonaktif</option>
            </select>
          </div>

          <!-- Tombol -->
          <div class="flex justify-between gap-3 pt-4">
            <button
              type="button"
              @click="router.visit('/dokter')"
              class="flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg bg-gray-200 text-gray-700 font-medium hover:bg-gray-300 transition"
              :disabled="loading"
            >
              ⬅️ Batal
            </button>
            <button
              type="submit"
              class="px-6 py-2.5 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700 shadow transition disabled:opacity-50"
              :disabled="loading"
            >
              {{ loading ? 'Menyimpan...' : 'Simpan Perubahan' }}
            </button>
          </div>
        </form>

        <!-- Error Message -->
        <div v-if="error" class="mt-4 p-3 rounded-lg bg-red-100 text-red-700 text-sm text-center">
          {{ error }}
        </div>
        
        <!-- Success Message -->
        <div v-if="success" class="mt-4 p-3 rounded-lg bg-green-100 text-green-700 text-sm text-center">
          ✅ Data dokter berhasil diperbarui!
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
