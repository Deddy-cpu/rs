<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
  grpEselon: {
    type: Object,
    required: true
  }
})

function formatDate(dateString) {
  if (!dateString) return '-'
  const date = new Date(dateString)
  return date.toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const getStatusBadge = (aktif) => {
  if (aktif === 'Y') return 'bg-green-100 text-green-800'
  return 'bg-red-100 text-red-800'
}

const getStatusText = (aktif) => (aktif === 'Y' ? 'Aktif' : 'Tidak Aktif')
</script>

<template>
  <AuthenticatedLayout>
    <Head :title="`Detail ${props.grpEselon.grp_eselon_desc}`" />

    <div class="min-h-screen bg-cover bg-center p-6" style="background-image: url('/images/bg-login.png')">
      <!-- Header -->
      <div class="text-center mb-8">
        <h1 class="text-4xl md:text-5xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent mb-2">
          <span class="inline-block align-middle mr-2">👀</span>
          Detail GRP Eselon
        </h1>
        <p class="text-gray-600 text-lg">Informasi lengkap tentang grup eselon</p>
      </div>

      <!-- Form Container (read-only style) -->
      <div class="max-w-4xl mx-auto">
        <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
          <!-- Header Bar -->
          <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-8 py-6 text-white">
            <div class="flex items-center gap-4">
              <div class="w-12 h-12 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
              </div>
              <div>
                <h2 class="text-2xl font-bold">Detail GRP Eselon</h2>
                <p class="text-blue-100">Informasi detail untuk grup: {{ props.grpEselon.grp_eselon_desc }}</p>
              </div>
            </div>
          </div>

          <!-- Content -->
          <div class="p-8">
            <form class="space-y-8">
              <!-- Deskripsi -->
              <div class="space-y-2">
                <label class="block text-sm font-semibold text-gray-700">Deskripsi Grup Eselon</label>
                <p class="w-full px-4 py-3 border border-gray-300 rounded-xl bg-gray-50 text-lg text-gray-900 font-semibold">{{ props.grpEselon.grp_eselon_desc }}</p>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label class="block text-sm font-semibold text-gray-700">Status</label>
                  <div class="mt-1">
                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full" :class="getStatusBadge(props.grpEselon.aktif)">
                      {{ getStatusText(props.grpEselon.aktif) }}
                    </span>
                  </div>
                </div>

                <div>
                  <label class="block text-sm font-semibold text-gray-700">Tanggal Update</label>
                  <p class="mt-1 text-sm text-gray-900 font-medium">{{ formatDate(props.grpEselon.update_date) }}</p>
                </div>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label class="block text-sm font-semibold text-gray-700">Diupdate Oleh</label>
                  <p class="mt-1 text-sm text-gray-900">{{ props.grpEselon.update_by || '-' }}</p>
                </div>

                <div>
                  <label class="block text-sm font-semibold text-gray-700">Tanggal Dibuat</label>
                  <p class="mt-1 text-sm text-gray-900">{{ formatDate(props.grpEselon.created_at) }}</p>
                </div>
              </div>

              <!-- Footer Buttons -->
              <div class="flex flex-col sm:flex-row gap-4 pt-6 border-t border-gray-200">
                <Link :href="route('grp-eselon.index')" class="flex-1 px-6 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition-all duration-200 font-semibold flex items-center justify-center gap-2">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                  Kembali
                </Link>

                <Link :href="route('grp-eselon.edit', props.grpEselon.id)" class="flex-1 px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 font-semibold flex items-center justify-center gap-2">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                  Edit Data
                </Link>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
