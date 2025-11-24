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

          <!-- Filters Section -->
          <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <!-- Search -->
            <div class="mb-4">
              <div class="relative w-full max-w-md mx-auto">
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

            <!-- Filter by Day (Quick Filters) -->
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-2">Filter Cepat:</label>
              <div class="flex flex-wrap gap-2 justify-center">
                <button
                  v-for="dayFilter in dayFilters"
                  :key="dayFilter.value"
                  @click="selectDayFilter(dayFilter.value)"
                  :class="[
                    'px-4 py-2 rounded-lg text-sm font-medium transition',
                    selectedDayFilter === dayFilter.value
                      ? 'bg-blue-600 text-white shadow-md'
                      : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                  ]"
                >
                  {{ dayFilter.label }}
                </button>
              </div>
            </div>

            <!-- Date Range Filter -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  <i class="fas fa-calendar-alt mr-1"></i> Dari Tanggal
                </label>
                <input
                  v-model="dateFrom"
                  type="date"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition bg-white"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  <i class="fas fa-calendar-check mr-1"></i> Sampai Tanggal
                </label>
                <input
                  v-model="dateTo"
                  type="date"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition bg-white"
                />
              </div>
              <div class="flex items-end">
                <button
                  @click="applyFilters"
                  class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium"
                >
                  <i class="fas fa-filter mr-2"></i>Terapkan Filter
                </button>
              </div>
            </div>

            <!-- Reset Filter -->
            <div class="mt-4 text-center">
              <button
                @click="resetFilters"
                class="text-sm text-gray-600 hover:text-gray-800 underline"
              >
                <i class="fas fa-redo mr-1"></i>Reset Filter
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
                <div class="flex items-center justify-between mb-2">
                  <h3 class="text-xl font-bold text-gray-800">{{ k.nm_p }}</h3>
                  <!-- Status Badge -->
                  <span 
                    class="px-3 py-1 rounded-full text-xs font-semibold"
                    :class="getPaymentStatus(k) === 'lunas' 
                      ? 'bg-green-100 text-green-800' 
                      : 'bg-yellow-100 text-yellow-800'"
                  >
                    {{ getPaymentStatus(k) === 'lunas' ? '✅ Lunas' : '⏳ Pending' }}
                  </span>
                </div>
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
                  :disabled="getPaymentStatus(k) === 'lunas'"
                  class="px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-lg hover:from-green-700 hover:to-emerald-700 transition font-semibold shadow-md hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  {{ getPaymentStatus(k) === 'lunas' ? '✅ Sudah Lunas' : '💳 Bayar' }}
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
const dateFrom = ref(props.filters.date_from || '')
const dateTo = ref(props.filters.date_to || '')
const selectedDayFilter = ref(props.filters.day_filter || '')

// Day filter options
const dayFilters = [
  { value: '', label: 'Semua' },
  { value: 'today', label: 'Hari Ini' },
  { value: 'yesterday', label: 'Kemarin' },
  { value: 'this_week', label: 'Minggu Ini' },
  { value: 'last_week', label: 'Minggu Lalu' },
  { value: 'this_month', label: 'Bulan Ini' },
  { value: 'last_month', label: 'Bulan Lalu' },
  { value: 'this_year', label: 'Tahun Ini' }
]

const filteredKunjungan = computed(() => {
  return props.kunjungan?.data || []
})

function getPaymentStatus(kunjungan) {
  if (!kunjungan.transaksi || kunjungan.transaksi.length === 0) {
    return 'pending'
  }
  
  // Get status from the first transaction (usually there's only one per kunjungan)
  const firstTransaksi = kunjungan.transaksi[0]
  return firstTransaksi?.status || 'pending'
}

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

function selectDayFilter(value) {
  selectedDayFilter.value = value
  
  // Set date range based on day filter
  const today = new Date()
  today.setHours(0, 0, 0, 0)
  
  if (value === 'today') {
    dateFrom.value = today.toISOString().split('T')[0]
    dateTo.value = today.toISOString().split('T')[0]
  } else if (value === 'yesterday') {
    const yesterday = new Date(today)
    yesterday.setDate(yesterday.getDate() - 1)
    dateFrom.value = yesterday.toISOString().split('T')[0]
    dateTo.value = yesterday.toISOString().split('T')[0]
  } else if (value === 'this_week') {
    const startOfWeek = new Date(today)
    startOfWeek.setDate(today.getDate() - today.getDay())
    dateFrom.value = startOfWeek.toISOString().split('T')[0]
    dateTo.value = today.toISOString().split('T')[0]
  } else if (value === 'last_week') {
    const startOfLastWeek = new Date(today)
    startOfLastWeek.setDate(today.getDate() - today.getDay() - 7)
    const endOfLastWeek = new Date(startOfLastWeek)
    endOfLastWeek.setDate(startOfLastWeek.getDate() + 6)
    dateFrom.value = startOfLastWeek.toISOString().split('T')[0]
    dateTo.value = endOfLastWeek.toISOString().split('T')[0]
  } else if (value === 'this_month') {
    const startOfMonth = new Date(today.getFullYear(), today.getMonth(), 1)
    dateFrom.value = startOfMonth.toISOString().split('T')[0]
    dateTo.value = today.toISOString().split('T')[0]
  } else if (value === 'last_month') {
    const startOfLastMonth = new Date(today.getFullYear(), today.getMonth() - 1, 1)
    const endOfLastMonth = new Date(today.getFullYear(), today.getMonth(), 0)
    dateFrom.value = startOfLastMonth.toISOString().split('T')[0]
    dateTo.value = endOfLastMonth.toISOString().split('T')[0]
  } else if (value === 'this_year') {
    const startOfYear = new Date(today.getFullYear(), 0, 1)
    dateFrom.value = startOfYear.toISOString().split('T')[0]
    dateTo.value = today.toISOString().split('T')[0]
  } else {
    // Reset dates when "Semua" is selected
    dateFrom.value = ''
    dateTo.value = ''
  }
  
  applyFilters()
}

function applyFilters() {
  const params = {}
  if (searchQuery.value) params.search = searchQuery.value
  if (dateFrom.value) params.date_from = dateFrom.value
  if (dateTo.value) params.date_to = dateTo.value
  if (selectedDayFilter.value) params.day_filter = selectedDayFilter.value
  
  router.get(route('kasir.index'), params, {
    preserveState: true,
    replace: true
  })
}

function resetFilters() {
  searchQuery.value = ''
  dateFrom.value = ''
  dateTo.value = ''
  selectedDayFilter.value = ''
  applyFilters()
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
