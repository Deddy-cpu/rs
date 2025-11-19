<template>
  <AuthenticatedLayout>
    <Head title="Kasir - Daftar Transaksi" />

    <div class="min-h-screen bg-cover bg-center p-6" style="background-image: url('/images/bg-login.png')">
      <div class="max-w-7xl mx-auto">
        <!-- Flash Messages -->
        <div v-if="flash.success" class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
          <div class="flex items-center">
            <i class="fas fa-check-circle mr-2"></i>
            {{ flash.success }}
          </div>
        </div>
        
        <div v-if="flash.error" class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
          <div class="flex items-center">
            <i class="fas fa-exclamation-circle mr-2"></i>
            {{ flash.error }}
          </div>
        </div>

        <!-- Header -->
        <div class="mb-6">
          <div class="text-center mb-4">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">💳 Kasir</h1>
            <p class="text-gray-600">Daftar transaksi yang perlu diproses</p>
          </div>

          <!-- Search -->
          <div class="flex justify-center">
            <div class="relative w-full max-w-md">
              <input
                v-model="searchQuery"
                @keypress.enter="applyFilters"
                type="text"
                placeholder="🔍 Cari pasien, no reg, atau MRN..."
                class="w-full pl-5 pr-14 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition bg-white"
              />
              <button
                @click="applyFilters"
                class="absolute right-3 top-1/2 transform -translate-y-1/2 p-2 text-gray-500 hover:text-gray-700"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
              </button>
            </div>
          </div>
        </div>

        <!-- Daftar Transaksi -->
        <div v-if="filteredKunjungan.length > 0" class="space-y-4">
          <div
            v-for="k in filteredKunjungan"
            :key="k.id"
            class="bg-white rounded-lg shadow-md border border-gray-200 p-6 hover:shadow-lg transition"
          >
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
              <!-- Info Pasien -->
              <div class="flex-1">
                <h3 class="text-xl font-bold text-gray-800 mb-2">{{ k.nm_p }}</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-sm text-gray-600">
                  <div>
                    <span class="font-medium">No Reg:</span>
                    <span class="ml-1">{{ k.no_reg }}</span>
                  </div>
                  <div>
                    <span class="font-medium">MRN:</span>
                    <span class="ml-1">{{ k.mrn }}</span>
                  </div>
                  <div>
                    <span class="font-medium">Tanggal:</span>
                    <span class="ml-1">{{ formatDate(k.tgl_reg) }}</span>
                  </div>
                  <div>
                    <span class="font-medium">Total:</span>
                    <span class="ml-1 font-bold text-green-600">{{ formatCurrency(calculateTotalBiaya(k)) }}</span>
                  </div>
                </div>
              </div>

              <!-- Tombol Bayar -->
              <div class="flex gap-3">
                <button
                  @click="router.visit(route('kasir.bayar', k.id))"
                  class="px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-lg hover:from-green-700 hover:to-emerald-700 transition font-semibold shadow-md hover:shadow-lg"
                >
                  💳 Bayar
                </button>
              </div>
            </div>
          </div>

          <!-- Pagination -->
          <div v-if="kunjungan?.links" class="flex justify-center mt-6">
            <div class="flex space-x-2">
              <template v-for="link in kunjungan.links" :key="link.label">
                <button
                  v-if="link.url"
                  @click="router.visit(link.url, { preserveState: true })"
                  v-html="link.label"
                  class="px-4 py-2 rounded-lg text-sm transition"
                  :class="link.active 
                    ? 'bg-blue-600 text-white shadow-md' 
                    : 'bg-white border text-gray-700 hover:bg-gray-100'"
                />
                <span
                  v-else
                  v-html="link.label"
                  class="px-4 py-2 text-gray-400 text-sm"
                />
              </template>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="text-center py-16 bg-white rounded-lg shadow-md">
          <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
          </div>
          <h3 class="text-xl font-bold text-gray-700 mb-2">Tidak ada transaksi</h3>
          <p class="text-gray-500">Belum ada transaksi yang perlu diproses</p>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
  kunjungan: Object,
  filters: {
    type: Object,
    default: () => ({})
  },
  flash: {
    type: Object,
    default: () => ({})
  }
})

const searchQuery = ref(props.filters.search || '')

const filteredKunjungan = computed(() => {
  return props.kunjungan?.data || []
})

function calculateTotalBiaya(kunjungan) {
  if (kunjungan.transaksi && kunjungan.transaksi.length > 0) {
    return kunjungan.transaksi.reduce((total, transaksi) => {
      return total + (parseFloat(transaksi.total_biaya) || 0)
    }, 0)
  }
  
  let total = 0
  if (kunjungan.konsuls) {
    kunjungan.konsuls.forEach(konsul => {
      total += parseFloat(String(konsul.st_kons).replace(/[^\d]/g, '')) || 0
    })
  }
  if (kunjungan.tindaks) {
    kunjungan.tindaks.forEach(tindak => {
      total += parseFloat(String(tindak.st_tindak).replace(/[^\d]/g, '')) || 0
    })
  }
  if (kunjungan.alkes) {
    kunjungan.alkes.forEach(alkes => {
      total += parseFloat(String(alkes.st_alkes).replace(/[^\d]/g, '')) || 0
    })
  }
  if (kunjungan.rsp) {
    kunjungan.rsp.forEach(rsp => {
      total += parseFloat(String(rsp.st_rsp).replace(/[^\d]/g, '')) || 0
    })
  }
  if (kunjungan.lainnyas) {
    kunjungan.lainnyas.forEach(lainnya => {
      total += parseFloat(String(lainnya.st_lainnya).replace(/[^\d]/g, '')) || 0
    })
  }
  
  return total
}

function formatCurrency(amount) {
  if (!amount) return 'Rp 0'
  const numericAmount = parseFloat(String(amount).replace(/[^\d]/g, '')) || 0
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0
  }).format(numericAmount)
}

function formatDate(dateString) {
  if (!dateString) return '-'
  const date = new Date(dateString)
  return date.toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

function applyFilters() {
  const params = {}
  if (searchQuery.value) params.search = searchQuery.value
  
  router.get(route('kasir.index'), params, {
    preserveState: true,
    replace: true
  })
}

// Auto-search with debounce
let searchTimeout = null
watch(searchQuery, () => {
  if (searchTimeout) {
    clearTimeout(searchTimeout)
  }
  searchTimeout = setTimeout(() => {
    applyFilters()
  }, 500)
})
</script>
