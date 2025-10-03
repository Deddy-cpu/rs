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

    <div class="min-h-screen bg-cover bg-center p-6" style="background-image: url('/images/bg-login.png')">
      <div class="bg-white/70 backdrop-blur-sm rounded-xl p-6 shadow-lg max-w-2xl mx-auto">
        
        <!-- Header -->
        <div class="mb-6 text-center">
          <h1 class="text-3xl font-extrabold text-blue-700 tracking-wide flex items-center gap-2 justify-center">
            👨‍⚕️ Detail Dokter
          </h1>
          <p class="text-gray-600 mt-2">Informasi lengkap dokter</p>
        </div>

        <!-- Dokter Information -->
        <div class="space-y-4">
          <!-- Nama Dokter -->
          <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-200">
            <label class="block text-sm font-medium text-gray-600 mb-1">Nama Dokter</label>
            <p class="text-lg font-semibold text-gray-800">{{ props.dokter.nama_dokter }}</p>
          </div>

          <!-- Nama User -->
          <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-200">
            <label class="block text-sm font-medium text-gray-600 mb-1">Nama User</label>
            <p class="text-lg font-semibold text-gray-800">{{ props.dokter.user.name }}</p>
          </div>

          <!-- Email -->
          <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-200">
            <label class="block text-sm font-medium text-gray-600 mb-1">Email</label>
            <p class="text-lg font-semibold text-gray-800">{{ props.dokter.user.email }}</p>
          </div>

          <!-- Role -->
          <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-200">
            <label class="block text-sm font-medium text-gray-600 mb-1">Role</label>
            <span class="inline-block px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-semibold">
              {{ props.dokter.user.role }}
            </span>
          </div>

          <!-- Status -->
          <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-200">
            <label class="block text-sm font-medium text-gray-600 mb-1">Status</label>
            <span 
              :class="props.dokter.aktif === 'Ya' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
              class="inline-block px-3 py-1 rounded-full text-sm font-semibold"
            >
              {{ props.dokter.aktif === 'Ya' ? 'Aktif' : 'Nonaktif' }}
            </span>
          </div>

          <!-- Created At -->
          <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-200">
            <label class="block text-sm font-medium text-gray-600 mb-1">Tanggal Dibuat</label>
            <p class="text-sm text-gray-600">{{ new Date(props.dokter.created_at).toLocaleDateString('id-ID') }}</p>
          </div>

          <!-- Updated At -->
          <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-200">
            <label class="block text-sm font-medium text-gray-600 mb-1">Terakhir Diperbarui</label>
            <p class="text-sm text-gray-600">{{ new Date(props.dokter.updated_at).toLocaleDateString('id-ID') }}</p>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-between gap-3 pt-6">
          <button
            @click="router.visit('/dokter')"
            class="flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg bg-gray-200 text-gray-700 font-medium hover:bg-gray-300 transition"
          >
            ⬅️ Kembali
          </button>
          
          <div class="flex gap-2" v-if="isAdmin">
            <button
              @click="router.visit(`/dokter/${props.dokter.id}/edit`)"
              class="px-4 py-2.5 rounded-lg bg-yellow-500 text-white font-medium hover:bg-yellow-600 transition"
            >
              ✏️ Edit
            </button>
            <button
              @click="deleteDokter"
              class="px-4 py-2.5 rounded-lg bg-red-600 text-white font-medium hover:bg-red-700 transition"
            >
              🗑 Hapus
            </button>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
