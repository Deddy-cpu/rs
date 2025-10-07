<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { useAuth } from '@/composables/useAuth'

interface Dokter {
  id: number
  user_id: number
  nama_dokter: string
  aktif: string
  created_at: string
  updated_at: string
  user: {
    id: number
    name: string
    email: string
    role: string
    created_at: string
  }
}

const props = defineProps<{
  dokter: Dokter
}>()

const { isAdmin } = useAuth()

function deleteDokter() {
  if (confirm('Apakah Anda yakin ingin menghapus dokter ini?')) {
    router.delete(`/dokter/${props.dokter.id}`, {
      onSuccess: () => {
        router.visit('/dokter')
      }
    })
  }
}
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Detail Dokter" />

    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-50 p-4 md:p-6">
      <div class="max-w-4xl mx-auto">
        
        <!-- Header Section -->
        <div class="mb-8">
          <div class="text-center mb-6">
            <div class="w-24 h-24 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
              <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
              </svg>
            </div>
            <h1 class="text-4xl md:text-5xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent mb-2">
              Detail Dokter
            </h1>
            <p class="text-gray-600 text-lg">Informasi lengkap dokter klinik</p>
          </div>
        </div>

        <!-- Main Content -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          
          <!-- Profile Card -->
          <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl p-6 shadow-xl border border-gray-100 sticky top-6">
              <div class="text-center">
                <div class="w-32 h-32 bg-gradient-to-r from-blue-100 to-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4">
                  <svg class="w-16 h-16 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                  </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ props.dokter.nama_dokter }}</h2>
                <p class="text-gray-600 mb-4">{{ props.dokter.user.name }}</p>
                
                <!-- Status Badge -->
                <div class="mb-6">
                  <span 
                    :class="props.dokter.aktif === 'Ya' 
                      ? 'bg-green-100 text-green-800 border-green-200' 
                      : 'bg-red-100 text-red-800 border-red-200'"
                    class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold border"
                  >
                    <div 
                      :class="props.dokter.aktif === 'Ya' ? 'bg-green-400' : 'bg-red-400'"
                      class="w-2 h-2 rounded-full mr-2"
                    ></div>
                    {{ props.dokter.aktif === 'Ya' ? 'Aktif' : 'Nonaktif' }}
                  </span>
                </div>

                <!-- Action Buttons -->
                <div class="space-y-3">
                  <button
                    v-if="isAdmin"
                    @click="router.visit(`/dokter/${props.dokter.id}/edit`)"
                    class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-gradient-to-r from-yellow-500 to-yellow-600 text-white rounded-xl hover:from-yellow-600 hover:to-yellow-700 transition-all duration-200 font-semibold shadow-lg hover:shadow-xl"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit Dokter
                  </button>
                  <button
                    v-if="isAdmin"
                    @click="deleteDokter"
                    class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-xl hover:from-red-600 hover:to-red-700 transition-all duration-200 font-semibold shadow-lg hover:shadow-xl"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    Hapus Dokter
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Information Cards -->
          <div class="lg:col-span-2 space-y-6">
            
            <!-- Personal Information -->
            <div class="bg-white rounded-2xl p-6 shadow-xl border border-gray-100">
              <div class="flex items-center gap-3 mb-6">
                <div class="p-2 bg-blue-100 rounded-lg">
                  <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                  </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900">Informasi Personal</h3>
              </div>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                  <label class="block text-sm font-medium text-gray-600">Nama Dokter</label>
                  <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                    <p class="text-lg font-semibold text-gray-900">{{ props.dokter.nama_dokter }}</p>
                  </div>
                </div>
                
                <div class="space-y-2">
                  <label class="block text-sm font-medium text-gray-600">Nama User</label>
                  <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                    <p class="text-lg font-semibold text-gray-900">{{ props.dokter.user.name }}</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Contact Information -->
            <div class="bg-white rounded-2xl p-6 shadow-xl border border-gray-100">
              <div class="flex items-center gap-3 mb-6">
                <div class="p-2 bg-green-100 rounded-lg">
                  <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                  </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900">Informasi Kontak</h3>
              </div>
              
              <div class="space-y-4">
                <div class="space-y-2">
                  <label class="block text-sm font-medium text-gray-600">Email</label>
                  <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                    <div class="flex items-center gap-3">
                      <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                      </svg>
                      <p class="text-lg font-semibold text-gray-900">{{ props.dokter.user.email }}</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- System Information -->
            <div class="bg-white rounded-2xl p-6 shadow-xl border border-gray-100">
              <div class="flex items-center gap-3 mb-6">
                <div class="p-2 bg-purple-100 rounded-lg">
                  <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                  </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900">Informasi Sistem</h3>
              </div>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                  <label class="block text-sm font-medium text-gray-600">Role</label>
                  <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                    <span class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-semibold">
                      {{ props.dokter.user.role }}
                    </span>
                  </div>
                </div>
                
                <div class="space-y-2">
                  <label class="block text-sm font-medium text-gray-600">Status</label>
                  <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                    <span 
                      :class="props.dokter.aktif === 'Ya' 
                        ? 'bg-green-100 text-green-800 border-green-200' 
                        : 'bg-red-100 text-red-800 border-red-200'"
                      class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold border"
                    >
                      <div 
                        :class="props.dokter.aktif === 'Ya' ? 'bg-green-400' : 'bg-red-400'"
                        class="w-2 h-2 rounded-full mr-2"
                      ></div>
                      {{ props.dokter.aktif === 'Ya' ? 'Aktif' : 'Nonaktif' }}
                    </span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Timestamps -->
            <div class="bg-white rounded-2xl p-6 shadow-xl border border-gray-100">
              <div class="flex items-center gap-3 mb-6">
                <div class="p-2 bg-indigo-100 rounded-lg">
                  <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900">Informasi Waktu</h3>
              </div>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                  <label class="block text-sm font-medium text-gray-600">Tanggal Dibuat</label>
                  <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                    <div class="flex items-center gap-3">
                      <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                      </svg>
                      <p class="text-sm text-gray-600">{{ new Date(props.dokter.created_at).toLocaleDateString('id-ID', { 
                        weekday: 'long', 
                        year: 'numeric', 
                        month: 'long', 
                        day: 'numeric' 
                      }) }}</p>
                    </div>
                  </div>
                </div>
                
                <div class="space-y-2">
                  <label class="block text-sm font-medium text-gray-600">Terakhir Diperbarui</label>
                  <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                    <div class="flex items-center gap-3">
                      <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                      </svg>
                      <p class="text-sm text-gray-600">{{ new Date(props.dokter.updated_at).toLocaleDateString('id-ID', { 
                        weekday: 'long', 
                        year: 'numeric', 
                        month: 'long', 
                        day: 'numeric' 
                      }) }}</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Back Button -->
        <div class="flex justify-center mt-8">
          <button
            @click="router.visit('/dokter')"
            class="flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-gray-500 to-gray-600 text-white rounded-xl hover:from-gray-600 hover:to-gray-700 transition-all duration-200 font-semibold shadow-lg hover:shadow-xl"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali ke Daftar Dokter
          </button>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
