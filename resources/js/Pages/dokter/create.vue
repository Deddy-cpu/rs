<script setup lang="ts">
import { ref } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { useAuth } from '@/composables/useAuth'

const { isAdmin } = useAuth()

const form = ref({
  name: '',
  email: '',
  password: '',
  nama_dokter: '',
  aktif: 'Ya',
})

const loading = ref(false)
const error = ref('')
const success = ref(false)

function submitCreate() {
  loading.value = true
  error.value = ''
  
  router.post('/dokter', form.value, {
    onSuccess: () => {
      success.value = true
      setTimeout(() => {
        router.visit('/dokter')
      }, 1500)
    },
    onError: (errors) => {
      error.value = 'Gagal menambahkan dokter.'
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
    <Head title="Tambah Dokter" />

    <div class="min-h-screen bg-cover bg-center flex items-center justify-center" style="background-image: url('/images/bg-login.png')">
      <!-- Card Form Semi-transparan dengan Blur -->
      <div class="bg-white/60 backdrop-blur-md rounded-xl shadow-lg max-w-xl w-full p-6">
        
        <!-- Header -->
        <div class="mb-4 text-center">
          <h1 class="text-2xl font-bold text-blue-700 flex items-center justify-center gap-2 mb-1">
            👨‍⚕️ Tambah Dokter
          </h1>
          <p class="text-gray-600 text-sm">Tambahkan dokter baru ke sistem</p>
        </div>

        <!-- Form -->
        <form @submit.prevent="submitCreate" class="space-y-4 text-sm">
          <!-- Nama User -->
          <div>
            <label class="block font-medium text-gray-700 mb-1">Nama User</label>
            <input
              v-model="form.name"
              type="text"
              required
              placeholder="Masukkan nama user"
              class="w-full px-3 py-2 rounded-lg border border-gray-300 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-700 placeholder-gray-400 transition"
            />
          </div>

          <!-- Email -->
          <div>
            <label class="block font-medium text-gray-700 mb-1">Email</label>
            <input
              v-model="form.email"
              type="email"
              required
              placeholder="Masukkan email"
              class="w-full px-3 py-2 rounded-lg border border-gray-300 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-700 placeholder-gray-400 transition"
            />
          </div>

          <!-- Password -->
          <div>
            <label class="block font-medium text-gray-700 mb-1">Password</label>
            <input
              v-model="form.password"
              type="password"
              required
              placeholder="Masukkan password"
              class="w-full px-3 py-2 rounded-lg border border-gray-300 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-700 placeholder-gray-400 transition"
            />
          </div>

          <!-- Nama Dokter -->
          <div>
            <label class="block font-medium text-gray-700 mb-1">Nama Dokter</label>
            <input
              v-model="form.nama_dokter"
              type="text"
              required
              placeholder="Masukkan nama dokter"
              class="w-full px-3 py-2 rounded-lg border border-gray-300 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-700 placeholder-gray-400 transition"
            />
          </div>

          <!-- Status Aktif -->
          <div>
            <label class="block font-medium text-gray-700 mb-1">Status</label>
            <select
              v-model="form.aktif"
              required
              class="w-full px-3 py-2 rounded-lg border border-gray-300 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-700 transition"
            >
              <option value="Ya">Aktif</option>
              <option value="Tidak">Nonaktif</option>
            </select>
          </div>

          <!-- Tombol -->
          <div class="flex justify-between gap-2 pt-3">
            <button
              type="button"
              @click="router.visit('/dokter')"
              class="flex items-center justify-center gap-1 px-4 py-2 rounded-lg bg-gray-200 text-gray-700 font-medium hover:bg-gray-300 transition text-sm"
              :disabled="loading"
            >
              ⬅️ Batal
            </button>
            <button
              type="submit"
              class="px-4 py-2 rounded-lg bg-green-600 text-white font-semibold hover:bg-green-700 shadow transition text-sm flex items-center justify-center gap-1"
              :disabled="loading"
            >
              {{ loading ? 'Menyimpan...' : 'Simpan Dokter' }}
              <i v-if="loading" class="fas fa-spinner fa-spin text-xs"></i>
            </button>
          </div>
        </form>

        <!-- Error Message -->
        <div v-if="error" class="mt-3 p-2 rounded-lg bg-red-100 text-red-700 text-xs text-center">
          {{ error }}
        </div>
        
        <!-- Success Message -->
        <div v-if="success" class="mt-3 p-2 rounded-lg bg-green-100 text-green-700 text-xs text-center">
          ✅ Dokter berhasil ditambahkan!
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

