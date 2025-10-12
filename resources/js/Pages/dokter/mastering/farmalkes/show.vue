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
    
    <div class="min-h-screen bg-gray-50 py-6">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
          <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
              <div class="flex items-center space-x-3">
                <Link
                  :href="route('farmalkes.index')"
                  class="p-2 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors"
                >
                  <i class="fas fa-arrow-left text-gray-600"></i>
                </Link>
                <div class="p-2 bg-indigo-100 rounded-lg">
                  <i class="fas fa-eye text-indigo-600 text-xl"></i>
                </div>
                <div>
                  <h1 class="text-2xl font-bold text-gray-900">Detail Farmalkes</h1>
                  <p class="text-sm text-gray-600">{{ farmalkes.nama_item }}</p>
                </div>
              </div>
              <div class="flex items-center space-x-2">
                <Link
                  :href="route('farmalkes.edit', farmalkes.id)"
                  class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                  <i class="fas fa-edit mr-2"></i>
                  Edit
                </Link>
              </div>
            </div>
          </div>

          <!-- Content -->
          <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Basic Information -->
              <div class="space-y-4">
                <h3 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-2">
                  Informasi Dasar
                </h3>
                
                <div>
                  <label class="block text-sm font-medium text-gray-500">Kode Item</label>
                  <p class="mt-1 text-sm text-gray-900 font-mono">{{ farmalkes.kode }}</p>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-500">Nama Item</label>
                  <p class="mt-1 text-sm text-gray-900 font-medium">{{ farmalkes.nama_item }}</p>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-500">Jenis</label>
                  <div class="mt-1">
                    <span 
                      class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                      :class="getTypeBadge(farmalkes.jenis)"
                    >
                      {{ getTypeText(farmalkes.jenis) }}
                    </span>
                  </div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-500">Kategori</label>
                  <p class="mt-1 text-sm text-gray-900">{{ farmalkes.kategori }}</p>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-500">Satuan</label>
                  <p class="mt-1 text-sm text-gray-900">{{ farmalkes.satuan }}</p>
                </div>
              </div>

              <!-- Financial & Status Information -->
              <div class="space-y-4">
                <h3 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-2">
                  Informasi Keuangan & Status
                </h3>

                <div>
                  <label class="block text-sm font-medium text-gray-500">Harga</label>
                  <p class="mt-1 text-sm text-gray-900 font-medium">{{ formatCurrency(farmalkes.harga) }}</p>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-500">Stok</label>
                  <p class="mt-1 text-sm text-gray-900">
                    <span :class="farmalkes.stok <= 10 ? 'text-red-600 font-semibold' : ''">
                      {{ farmalkes.stok }} {{ farmalkes.satuan }}
                    </span>
                    <span v-if="farmalkes.stok <= 10" class="ml-2 text-xs text-red-500">
                      (Stok Rendah!)
                    </span>
                  </p>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-500">Status</label>
                  <div class="mt-1">
                    <span 
                      class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                      :class="getStatusBadge(farmalkes.aktif)"
                    >
                      {{ getStatusText(farmalkes.aktif) }}
                    </span>
                  </div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-500">Terakhir Diupdate</label>
                  <p class="mt-1 text-sm text-gray-900">{{ formatDate(farmalkes.updated_at) }}</p>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-500">Diupdate Oleh</label>
                  <p class="mt-1 text-sm text-gray-900">{{ farmalkes.update_by || '-' }}</p>
                </div>
              </div>
            </div>

            <!-- Description -->
            <div v-if="farmalkes.deskripsi" class="mt-6 pt-6 border-t border-gray-200">
              <h3 class="text-lg font-medium text-gray-900 mb-3">Deskripsi</h3>
              <p class="text-sm text-gray-700 leading-relaxed">{{ farmalkes.deskripsi }}</p>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end space-x-3 mt-8 pt-6 border-t border-gray-200">
              <Link
                :href="route('farmalkes.index')"
                class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
              >
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali ke Daftar
              </Link>
              <Link
                :href="route('farmalkes.edit', farmalkes.id)"
                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
              >
                <i class="fas fa-edit mr-2"></i>
                Edit Data
              </Link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
