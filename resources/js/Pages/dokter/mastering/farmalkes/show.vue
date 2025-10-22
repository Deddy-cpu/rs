<script setup>
import { computed } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

// Props
const props = defineProps({
  farmalkes: {
    type: Object,
    required: true
  }
})

// Format currency
const formatCurrency = (amount) => {
  if (amount === null || amount === undefined || amount === '') return '-'
  
  const floatAmount = parseFloat(amount)
  if (isNaN(floatAmount)) return '-'
  
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
    maximumFractionDigits: 2
  }).format(floatAmount)
}

// Format date
const formatDate = (dateString) => {
  if (!dateString) return '-'
  const date = new Date(dateString)
  return date.toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

// Get status badge class
const getStatusBadge = (aktif) => {
  if (aktif === 'Y') {
    return 'bg-green-100 text-green-800'
  }
  return 'bg-red-100 text-red-800'
}

const getStatusText = (aktif) => {
  return aktif === 'Y' ? 'Aktif' : 'Tidak Aktif'
}

// Get type badge class
const getTypeBadge = (type) => {
  if (type === 'farmasi') {
    return 'bg-blue-100 text-blue-800'
  } else if (type === 'alkes') {
    return 'bg-purple-100 text-purple-800'
  }
  return 'bg-gray-100 text-gray-800'
}

const getTypeText = (type) => {
  if (type === 'farmasi') {
    return 'Farmasi'
  } else if (type === 'alkes') {
    return 'Alat Kesehatan'
  }
  return 'Lainnya'
}
</script>

<template>
  <AuthenticatedLayout>
    <Head :title="`Detail ${farmalkes.nama_item}`" />

    <div class="min-h-screen bg-cover bg-center p-6" style="background-image: url('/images/bg-login.png')">
      <!-- Header -->
      <div class="text-center mb-8">
        <h1 class="text-4xl md:text-5xl font-bold bg-gradient-to-r from-red-600 to-rose-600 bg-clip-text text-transparent mb-2">
          <span class="inline-block align-middle mr-2">👀</span>
          Detail Farmalkes
        </h1>
        <p class="text-gray-600 text-lg">Informasi lengkap tentang item farmalkes</p>
      </div>

      <!-- Form Container (read-only style) -->
      <div class="max-w-4xl mx-auto">
        <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
          <!-- Header Bar -->
          <div class="bg-gradient-to-r from-red-600 to-rose-600 px-8 py-6 text-white">
            <div class="flex items-center gap-4">
              <div class="w-12 h-12 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
              </div>
              <div>
                <h2 class="text-2xl font-bold">Detail Farmalkes</h2>
                <p class="text-red-100">Informasi detail untuk item: {{ farmalkes.nama_item }}</p>
              </div>
            </div>
          </div>

          <!-- Content -->
          <div class="p-8">
            <form class="space-y-8">
              <!-- Kode -->
              <div class="space-y-2">
                <label class="block text-sm font-semibold text-gray-700">Kode Item</label>
                <p class="w-full px-4 py-3 border border-gray-300 rounded-xl bg-gray-50 text-lg text-gray-900 font-mono">{{ farmalkes.kode }}</p>
              </div>

              <!-- Nama -->
              <div class="space-y-2">
                <label class="block text-sm font-semibold text-gray-700">Nama Item</label>
                <p class="w-full px-4 py-3 border border-gray-300 rounded-xl bg-gray-50 text-lg text-gray-900">{{ farmalkes.nama_item }}</p>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Left -->
                <div class="space-y-4">
                  <div>
                    <label class="block text-sm font-semibold text-gray-700">Jenis</label>
                    <div class="mt-1">
                      <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full" :class="getTypeBadge(farmalkes.jenis)">
                        {{ getTypeText(farmalkes.jenis) }}
                      </span>
                    </div>
                  </div>

                  <div>
                    <label class="block text-sm font-semibold text-gray-700">Kategori</label>
                    <p class="mt-1 text-sm text-gray-900">{{ farmalkes.kategori }}</p>
                  </div>

                  <div>
                    <label class="block text-sm font-semibold text-gray-700">Satuan</label>
                    <p class="mt-1 text-sm text-gray-900">{{ farmalkes.satuan }}</p>
                  </div>
                </div>

                <!-- Right -->
                <div class="space-y-4">
                  <div>
                    <label class="block text-sm font-semibold text-gray-700">Harga</label>
                    <p class="mt-1 text-sm text-gray-900 font-medium">{{ formatCurrency(farmalkes.harga) }}</p>
                  </div>

                  <div>
                    <label class="block text-sm font-semibold text-gray-700">Stok</label>
                    <p class="mt-1 text-sm text-gray-900"><span :class="farmalkes.stok <= 10 ? 'text-red-600 font-semibold' : ''">{{ farmalkes.stok }} {{ farmalkes.satuan }}</span></p>
                  </div>

                  <div>
                    <label class="block text-sm font-semibold text-gray-700">Status</label>
                    <div class="mt-1">
                      <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full" :class="getStatusBadge(farmalkes.aktif)">
                        {{ getStatusText(farmalkes.aktif) }}
                      </span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Deskripsi -->
              <div>
                <label class="block text-sm font-semibold text-gray-700">Deskripsi</label>
                <div class="mt-1 p-4 border border-gray-300 rounded-xl bg-gray-50 text-sm text-gray-700" v-if="farmalkes.deskripsi">
                  {{ farmalkes.deskripsi }}
                </div>
                <p v-else class="mt-1 text-sm text-gray-500">-</p>
              </div>

              <!-- Footer Buttons -->
              <div class="flex flex-col sm:flex-row gap-4 pt-6 border-t border-gray-200">
                <Link :href="route('farmalkes.index')" class="flex-1 px-6 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition-all duration-200 font-semibold flex items-center justify-center gap-2">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                  Kembali
                </Link>

                <Link :href="route('farmalkes.edit', farmalkes.id)" class="flex-1 px-6 py-3 bg-gradient-to-r from-red-600 to-rose-600 text-white rounded-xl hover:from-red-700 hover:to-rose-700 transition-all duration-200 font-semibold flex items-center justify-center gap-2">
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
