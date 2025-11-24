<template>
  <AuthenticatedLayout>
    <Head title="Kasir - Daftar Transaksi" />

    <div class="min-h-screen bg-cover bg-center p-6" style="background-image: url('/images/bg-login.png')">
      <div class="max-w-7xl mx-auto">
        <!-- Flash Messages -->
        <div v-if="flash.success" class="mb-6 bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-500 text-green-700 px-6 py-4 rounded-xl shadow-lg">
          <div class="flex items-center">
            <i class="fas fa-check-circle mr-3 text-xl"></i>
            <span class="font-semibold">{{ flash.success }}</span>
          </div>
        </div>
        
        <div v-if="flash.error" class="mb-6 bg-gradient-to-r from-red-50 to-pink-50 border-l-4 border-red-500 text-red-700 px-6 py-4 rounded-xl shadow-lg">
          <div class="flex items-center">
            <i class="fas fa-exclamation-circle mr-3 text-xl"></i>
            <span class="font-semibold">{{ flash.error }}</span>
          </div>
        </div>

        <!-- Header -->
        <div class="mb-6">
          <div class="bg-white/90 backdrop-blur-sm shadow-2xl rounded-2xl p-8 border border-white/20 hover:shadow-3xl transition-all duration-300 mb-6">
            <div class="flex items-center justify-start mb-2">
              <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg mr-4">
                <i class="fas fa-cash-register text-white text-2xl"></i>
              </div>
              <div>
                <h1 class="text-4xl font-bold bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent text-left">
                  Kasir
                </h1>
                <p class="text-gray-600 text-lg text-left">Daftar transaksi yang perlu diproses</p>
              </div>
            </div>
          </div>

          <!-- Filters Section -->
          <div class="bg-white/90 backdrop-blur-sm shadow-xl rounded-2xl p-6 mb-6 border border-white/20">
            <!-- Search -->
            <div class="mb-6">
              <div class="relative w-full">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                  <i class="fas fa-search text-gray-400 text-lg"></i>
                </div>
                <input
                  v-model="searchQuery"
                  @keypress.enter="applyFilters"
                  type="text"
                  placeholder="Cari pasien, no registrasi, atau MRN..."
                  class="w-full pl-12 pr-4 py-3 bg-gradient-to-r from-gray-50 to-white border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-300 text-gray-700 placeholder-gray-400"
                  autocomplete="off"
                />
                <button
                  v-if="searchQuery"
                  @click="searchQuery = ''; applyFilters()"
                  class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-red-500 transition-colors"
                >
                  <i class="fas fa-times text-lg"></i>
                </button>
              </div>
              <div v-if="searchQuery" class="mt-3 flex items-center text-sm text-gray-600">
                <i class="fas fa-info-circle mr-2 text-green-500"></i>
                <span>Mencari: "{{ searchQuery }}"</span>
              </div>
            </div>

            <!-- Filter by Day (Quick Filters) -->
            <div class="mb-6">
              <label class="block text-sm font-semibold text-gray-700 mb-3 flex items-center">
                <i class="fas fa-filter mr-2 text-green-500"></i>
                Filter Cepat:
              </label>
              <div class="flex flex-wrap gap-2 justify-center">
                <button
                  v-for="dayFilter in dayFilters"
                  :key="dayFilter.value"
                  @click="selectDayFilter(dayFilter.value)"
                  :class="[
                    'px-5 py-2.5 rounded-xl text-sm font-medium transition-all duration-300 transform hover:scale-105 shadow-md',
                    selectedDayFilter === dayFilter.value
                      ? 'bg-gradient-to-r from-green-600 to-emerald-600 text-white shadow-lg'
                      : 'bg-gradient-to-r from-gray-100 to-gray-50 text-gray-700 hover:from-gray-200 hover:to-gray-100 border border-gray-200'
                  ]"
                >
                  {{ dayFilter.label }}
                </button>
              </div>
            </div>

            <!-- Date Range Filter -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                  <i class="fas fa-calendar-alt mr-2 text-green-500"></i>
                  Dari Tanggal
                </label>
                <input
                  v-model="dateFrom"
                  type="date"
                  class="w-full px-4 py-3 bg-gradient-to-r from-gray-50 to-white border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-300 shadow-sm"
                />
              </div>
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                  <i class="fas fa-calendar-check mr-2 text-green-500"></i>
                  Sampai Tanggal
                </label>
                <input
                  v-model="dateTo"
                  type="date"
                  class="w-full px-4 py-3 bg-gradient-to-r from-gray-50 to-white border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-300 shadow-sm"
                />
              </div>
              <div class="flex items-end">
                <button
                  @click="applyFilters"
                  class="w-full px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl hover:from-green-700 hover:to-emerald-700 transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl font-semibold flex items-center justify-center"
                >
                  <i class="fas fa-filter mr-2"></i>Terapkan Filter
                </button>
              </div>
            </div>

            <!-- Reset Filter -->
            <div class="text-center">
              <button
                @click="resetFilters"
                class="text-sm text-gray-600 hover:text-red-600 transition-colors font-medium flex items-center justify-center mx-auto"
              >
                <i class="fas fa-redo mr-2"></i>Reset Filter
              </button>
            </div>
          </div>
        </div>

        <!-- Daftar Transaksi -->
        <div v-if="filteredKunjungan.length > 0" class="space-y-6">
          <div
            v-for="k in filteredKunjungan"
            :key="k.id"
            class="group bg-gradient-to-br from-white to-gray-50 rounded-2xl p-8 border border-gray-200/50 hover:shadow-2xl hover:border-green-300/50 transition-all duration-300 hover:-translate-y-1 w-full overflow-hidden"
            style="max-width: 100%; box-sizing: border-box;"
          >
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6 w-full overflow-hidden" style="max-width: 100%; box-sizing: border-box;">
              <!-- Info Pasien -->
              <div class="flex-1 overflow-hidden" style="max-width: 100%; box-sizing: border-box;">
                <div class="flex items-center justify-between mb-4 overflow-hidden" style="max-width: 100%;">
                  <div class="flex items-center flex-1 overflow-hidden" style="max-width: 100%;">
                    <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg mr-4 flex-shrink-0">
                      <i class="fas fa-user text-white text-lg"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 group-hover:text-green-600 transition-colors break-words overflow-wrap-anywhere" style="word-wrap: break-word; overflow-wrap: break-word; max-width: 100%;">
                      {{ k.nm_p }}
                    </h3>
                  </div>
                  <!-- Status Badge -->
                  <span 
                    class="px-3 py-1.5 rounded-full text-xs font-semibold flex-shrink-0 ml-4"
                    :class="getPaymentStatus(k) === 'lunas' 
                      ? 'bg-green-100 text-green-800 border border-green-300' 
                      : 'bg-yellow-100 text-yellow-800 border border-yellow-300'"
                  >
                    {{ getPaymentStatus(k) === 'lunas' ? '✅ Lunas' : '⏳ Pending' }}
                  </span>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 w-full overflow-hidden" style="max-width: 100%; box-sizing: border-box;">
                  <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-4 border border-blue-200/50 overflow-hidden" style="box-sizing: border-box; max-width: 100%;">
                    <div class="flex items-center mb-2">
                      <i class="fas fa-hashtag text-blue-500 mr-2 flex-shrink-0"></i>
                      <label class="text-xs font-semibold text-blue-700 uppercase tracking-wide whitespace-nowrap">No Registrasi</label>
                    </div>
                    <p class="text-lg font-bold text-gray-900 break-words overflow-wrap-anywhere" style="word-wrap: break-word; overflow-wrap: break-word; word-break: break-all; max-width: 100%;">{{ k.no_reg }}</p>
                  </div>
                  
                  <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-4 border border-purple-200/50 overflow-hidden" style="box-sizing: border-box; max-width: 100%;">
                    <div class="flex items-center mb-2">
                      <i class="fas fa-id-badge text-purple-500 mr-2 flex-shrink-0"></i>
                      <label class="text-xs font-semibold text-purple-700 uppercase tracking-wide whitespace-nowrap">MRN</label>
                    </div>
                    <p class="text-lg font-bold text-gray-900 break-words overflow-wrap-anywhere" style="word-wrap: break-word; overflow-wrap: break-word; word-break: break-all; max-width: 100%;">{{ k.mrn }}</p>
                  </div>
                  
                  <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl p-4 border border-orange-200/50 overflow-hidden" style="box-sizing: border-box; max-width: 100%;">
                    <div class="flex items-center mb-2">
                      <i class="fas fa-calendar text-orange-500 mr-2 flex-shrink-0"></i>
                      <label class="text-xs font-semibold text-orange-700 uppercase tracking-wide whitespace-nowrap">Tanggal</label>
                    </div>
                    <p class="text-lg font-bold text-gray-900 break-words overflow-wrap-anywhere" style="word-wrap: break-word; overflow-wrap: break-word; word-break: break-all; max-width: 100%;">{{ formatDate(k.tgl_reg) }}</p>
                  </div>
                  
                  <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-4 border border-green-200/50 overflow-hidden" style="box-sizing: border-box; max-width: 100%;">
                    <div class="flex items-center mb-2">
                      <i class="fas fa-money-bill-wave text-green-500 mr-2 flex-shrink-0"></i>
                      <label class="text-xs font-semibold text-green-700 uppercase tracking-wide whitespace-nowrap">Total Biaya</label>
                    </div>
                    <p class="text-lg font-bold text-green-600 break-words overflow-wrap-anywhere" style="word-wrap: break-word; overflow-wrap: break-word; word-break: break-all; max-width: 100%;">{{ formatCurrency(calculateTotalBiaya(k)) }}</p>
                  </div>
                </div>
              </div>

              <!-- Tombol Bayar -->
              <div class="flex items-center justify-center lg:justify-end">
                <button
                  @click="router.visit(route('kasir.bayar', k.id))"
                  :disabled="getPaymentStatus(k) === 'lunas'"
                  :class="[
                    'px-8 py-4 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl hover:from-green-700 hover:to-emerald-700 transition-all duration-300 font-bold shadow-xl hover:shadow-2xl flex items-center text-lg',
                    getPaymentStatus(k) === 'lunas' 
                      ? 'opacity-50 cursor-not-allowed transform-none' 
                      : 'transform hover:scale-105'
                  ]"
                >
                  <i :class="[
                    'mr-3',
                    getPaymentStatus(k) === 'lunas' ? 'fas fa-check-circle' : 'fas fa-cash-register'
                  ]"></i>
                  {{ getPaymentStatus(k) === 'lunas' ? 'Sudah Lunas' : 'Bayar' }}
                </button>
              </div>
            </div>
          </div>

          <!-- Pagination -->
          <div v-if="kunjungan?.links" class="flex justify-center mt-8">
            <div class="flex space-x-2 bg-white/90 backdrop-blur-sm rounded-xl p-2 shadow-lg border border-gray-200/50">
              <template v-for="link in kunjungan.links" :key="link.label">
                <button
                  v-if="link.url"
                  @click="router.visit(link.url, { preserveState: true })"
                  v-html="link.label"
                  class="px-5 py-2.5 rounded-lg text-sm font-medium transition-all duration-300 transform hover:scale-105"
                  :class="link.active 
                    ? 'bg-gradient-to-r from-green-600 to-emerald-600 text-white shadow-md' 
                    : 'bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 hover:border-green-300'"
                />
                <span
                  v-else
                  v-html="link.label"
                  class="px-5 py-2.5 text-gray-400 text-sm"
                />
              </template>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="text-center py-20 bg-white/90 backdrop-blur-sm rounded-2xl shadow-xl border border-white/20">
          <div class="w-32 h-32 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center mx-auto mb-6">
            <i class="fas fa-receipt text-5xl text-gray-400"></i>
          </div>
          <h3 class="text-3xl font-bold text-gray-700 mb-3">Tidak Ada Transaksi</h3>
          <p class="text-gray-500 text-lg mb-6">Belum ada transaksi yang perlu diproses</p>
          <button
            @click="resetFilters"
            class="px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl hover:from-green-700 hover:to-emerald-700 transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl font-semibold flex items-center mx-auto"
          >
            <i class="fas fa-redo mr-2"></i>
            Reset Filter
          </button>
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
