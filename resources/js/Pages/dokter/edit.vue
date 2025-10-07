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

    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-50 p-4 md:p-6">
      <div class="max-w-2xl mx-auto">
        
        <!-- Header Section -->
        <div class="mb-8">
          <div class="text-center mb-6">
            <div class="w-20 h-20 bg-gradient-to-r from-yellow-500 to-orange-500 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
              <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
              </svg>
            </div>
            <h1 class="text-4xl md:text-5xl font-bold bg-gradient-to-r from-yellow-600 to-orange-600 bg-clip-text text-transparent mb-2">
              Edit Dokter
            </h1>
            <p class="text-gray-600 text-lg">Perbarui informasi dokter klinik</p>
          </div>
        </div>

        <!-- Form Container -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
          
          <!-- Form Header -->
          <div class="bg-gradient-to-r from-yellow-500 to-orange-500 px-6 py-4">
            <div class="flex items-center gap-3">
              <div class="p-2 bg-white/20 rounded-lg">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
              </div>
              <div>
                <h2 class="text-xl font-bold text-white">Informasi Dokter</h2>
                <p class="text-yellow-100 text-sm">Lengkapi form di bawah untuk memperbarui data</p>
              </div>
            </div>
          </div>

          <!-- Form Content -->
          <form @submit.prevent="submitEdit" class="p-6 space-y-6">
            
            <!-- Personal Information Section -->
            <div class="space-y-4">
              <div class="flex items-center gap-3 mb-4">
                <div class="p-2 bg-blue-100 rounded-lg">
                  <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                  </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900">Informasi Personal</h3>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Nama User -->
                <div class="space-y-2">
                  <label class="block text-sm font-medium text-gray-700">Nama User</label>
                  <div class="relative">
                    <input
                      v-model="form.name"
                      type="text"
                      required
                      class="w-full pl-4 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 bg-gray-50 focus:bg-white"
                      placeholder="Masukkan nama user"
                    />
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                      <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                      </svg>
                    </div>
                  </div>
                </div>

                <!-- Nama Dokter -->
                <div class="space-y-2">
                  <label class="block text-sm font-medium text-gray-700">Nama Dokter</label>
                  <div class="relative">
                    <input
                      v-model="form.nama_dokter"
                      type="text"
                      required
                      class="w-full pl-4 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 bg-gray-50 focus:bg-white"
                      placeholder="Masukkan nama dokter"
                    />
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                      <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                      </svg>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Contact Information Section -->
            <div class="space-y-4">
              <div class="flex items-center gap-3 mb-4">
                <div class="p-2 bg-green-100 rounded-lg">
                  <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                  </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900">Informasi Kontak</h3>
              </div>

              <!-- Email -->
              <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700">Email</label>
                <div class="relative">
                  <input
                    v-model="form.email"
                    type="email"
                    required
                    class="w-full pl-4 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 bg-gray-50 focus:bg-white"
                    placeholder="Masukkan email"
                  />
                  <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                  </div>
                </div>
              </div>

              <!-- Password (Optional) -->
              <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700">Password Baru (Opsional)</label>
                <div class="relative">
                  <input
                    v-model="form.password"
                    type="password"
                    class="w-full pl-4 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 bg-gray-50 focus:bg-white"
                    placeholder="Kosongkan jika tidak ingin mengubah password"
                  />
                  <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                  </div>
                </div>
                <p class="text-xs text-gray-500 flex items-center gap-1">
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                  Kosongkan jika tidak ingin mengubah password
                </p>
              </div>
            </div>

            <!-- System Information Section -->
            <div class="space-y-4">
              <div class="flex items-center gap-3 mb-4">
                <div class="p-2 bg-purple-100 rounded-lg">
                  <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                  </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900">Status Sistem</h3>
              </div>

              <!-- Status Aktif -->
              <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700">Status</label>
                <div class="relative">
                  <select
                    v-model="form.aktif"
                    class="w-full pl-4 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 bg-gray-50 focus:bg-white appearance-none"
                    required
                  >
                    <option value="Ya">Aktif</option>
                    <option value="Tidak">Nonaktif</option>
                  </select>
                  <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                  </div>
                </div>
              </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row justify-between gap-4 pt-6 border-t border-gray-200">
              <button
                type="button"
                @click="router.visit('/dokter')"
                class="flex items-center justify-center gap-2 px-6 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-all duration-200 font-semibold"
                :disabled="loading"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Batal
              </button>
              <button
                type="submit"
                class="flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl hover:from-blue-700 hover:to-blue-800 transition-all duration-200 font-semibold shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed"
                :disabled="loading"
              >
                <svg v-if="loading" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                {{ loading ? 'Menyimpan...' : 'Simpan Perubahan' }}
              </button>
            </div>
          </form>

          <!-- Messages -->
          <div v-if="error || success" class="px-6 pb-6">
            <!-- Error Message -->
            <div v-if="error" class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm">
              <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                {{ error }}
              </div>
            </div>
            
            <!-- Success Message -->
            <div v-if="success" class="p-4 rounded-xl bg-green-50 border border-green-200 text-green-700 text-sm">
              <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Data dokter berhasil diperbarui!
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
