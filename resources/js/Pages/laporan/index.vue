<template>
  <AuthenticatedLayout>
    <Head title="Laporan Transaksi" />

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
            <h1 class="text-3xl font-bold text-gray-800 mb-2">📊 Laporan Transaksi</h1>
            <p class="text-gray-600">Rekapan data transaksi berdasarkan periode</p>
          </div>

          <!-- Filter Section -->
          <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Filter Laporan</h2>
            
            <!-- Filter Type Selection -->
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Laporan:</label>
              <div class="flex flex-wrap gap-2">
                <button
                  @click="filterType = 'daily'"
                  :class="[
                    'px-4 py-2 rounded-lg text-sm font-medium transition',
                    filterType === 'daily'
                      ? 'bg-blue-600 text-white shadow-md'
                      : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                  ]"
                >
                  📅 Harian
                </button>
                <button
                  @click="filterType = 'monthly'"
                  :class="[
                    'px-4 py-2 rounded-lg text-sm font-medium transition',
                    filterType === 'monthly'
                      ? 'bg-blue-600 text-white shadow-md'
                      : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                  ]"
                >
                  📆 Bulanan
                </button>
                <button
                  @click="filterType = 'yearly'"
                  :class="[
                    'px-4 py-2 rounded-lg text-sm font-medium transition',
                    filterType === 'yearly'
                      ? 'bg-blue-600 text-white shadow-md'
                      : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                  ]"
                >
                  📅 Tahunan
                </button>
              </div>
            </div>

            <!-- Filter Inputs -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
              <!-- Daily Filter -->
              <div v-if="filterType === 'daily'">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  <i class="fas fa-calendar-alt mr-1"></i> Pilih Tanggal
                </label>
                <input
                  v-model="date"
                  type="date"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition bg-white"
                />
              </div>

              <!-- Monthly Filter -->
              <div v-if="filterType === 'monthly'">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  <i class="fas fa-calendar-check mr-1"></i> Pilih Bulan
                </label>
                <input
                  v-model="month"
                  type="month"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition bg-white"
                />
              </div>

              <!-- Yearly Filter -->
              <div v-if="filterType === 'yearly'">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  <i class="fas fa-calendar mr-1"></i> Pilih Tahun
                </label>
                <select
                  v-model="year"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition bg-white"
                >
                  <option v-for="y in years" :key="y" :value="y">{{ y }}</option>
                </select>
              </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-wrap gap-3">
              <button
                @click="applyFilter"
                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium shadow-md hover:shadow-lg"
              >
                <i class="fas fa-filter mr-2"></i>Terapkan Filter
              </button>
              <button
                @click="exportPdf"
                :disabled="loading"
                class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-medium shadow-md hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed"
              >
                <i class="fas fa-file-pdf mr-2"></i>Export PDF
              </button>
              <button
                @click="exportExcel"
                :disabled="loading"
                class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium shadow-md hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed"
              >
                <i class="fas fa-file-excel mr-2"></i>Export Excel
              </button>
            </div>
          </div>

          <!-- Statistics Cards -->
          <div v-if="filterLabel" class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-sm text-gray-600 mb-1">Total Transaksi</p>
                  <p class="text-2xl font-bold text-gray-800">{{ totalTransaksi }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                  <i class="fas fa-receipt text-blue-600 text-xl"></i>
                </div>
              </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-sm text-gray-600 mb-1">Total Pemasukan</p>
                  <p class="text-2xl font-bold text-gray-800">Rp {{ totalPemasukan }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                  <i class="fas fa-money-bill-wave text-green-600 text-xl"></i>
                </div>
              </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-purple-500">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-sm text-gray-600 mb-1">Jumlah Item Terjual</p>
                  <p class="text-2xl font-bold text-gray-800">{{ totalItemTerjual }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                  <i class="fas fa-shopping-cart text-purple-600 text-xl"></i>
                </div>
              </div>
            </div>
          </div>

          <!-- Filter Label -->
          <div v-if="filterLabel" class="mb-4">
            <p class="text-lg font-semibold text-gray-700">
              <i class="fas fa-info-circle mr-2"></i>{{ filterLabel }}
            </p>
          </div>

          <!-- Tab Navigation -->
          <div v-if="filterLabel" class="mb-4 bg-white rounded-lg shadow-md p-1">
            <div class="flex border-b border-gray-200">
              <button
                @click="activeTab = 'ringkasan'"
                :class="[
                  'flex-1 px-4 py-2 text-sm font-medium transition-colors',
                  activeTab === 'ringkasan'
                    ? 'bg-blue-600 text-white rounded-t-lg'
                    : 'text-gray-600 hover:text-gray-800 hover:bg-gray-100'
                ]"
              >
                <i class="fas fa-list mr-2"></i>Ringkasan
              </button>
              <button
                @click="activeTab = 'perincian'"
                :class="[
                  'flex-1 px-4 py-2 text-sm font-medium transition-colors',
                  activeTab === 'perincian'
                    ? 'bg-blue-600 text-white rounded-t-lg'
                    : 'text-gray-600 hover:text-gray-800 hover:bg-gray-100'
                ]"
              >
                <i class="fas fa-list-alt mr-2"></i>Perincian
              </button>
            </div>
          </div>

          <!-- Export Buttons for Detail Tab -->
          <div v-if="filterLabel && activeTab === 'perincian'" class="mb-4 flex flex-wrap gap-3">
            <button
              @click="exportPdfDetail"
              :disabled="loading"
              class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-medium shadow-md hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <i class="fas fa-file-pdf mr-2"></i>Download PDF Perincian
            </button>
            <button
              @click="exportExcelDetail"
              :disabled="loading"
              class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium shadow-md hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <i class="fas fa-file-excel mr-2"></i>Download Excel Perincian
            </button>
          </div>

          <!-- Data Table (Ringkasan) -->
          <div v-if="activeTab === 'ringkasan' && dataTransaksi && dataTransaksi.length > 0" class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      No
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Tanggal
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      No. Registrasi
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Nama Pasien
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Total Biaya
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Jumlah Item
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Status
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="(item, index) in dataTransaksi" :key="item.id" class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      {{ index + 1 }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      {{ item.tanggal }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      {{ item.no_reg }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      {{ item.nama_pasien }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-600">
                      Rp {{ item.total_biaya }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      {{ item.jumlah_item }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      <span class="px-2 py-1 text-xs rounded-full"
                        :class="item.status === 'lunas' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'">
                        {{ item.status }}
                      </span>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Data Table (Perincian) -->
          <div v-if="activeTab === 'perincian' && dataDetailTransaksi && dataDetailTransaksi.length > 0" class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      No
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Tanggal
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      No. Registrasi
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Nama Pasien
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Jenis
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Item
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Dokter
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Jumlah
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Biaya
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Diskon
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Subtotal
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="(item, index) in dataDetailTransaksi" :key="index" class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      {{ index + 1 }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      {{ item.tanggal }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      {{ item.no_reg }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      {{ item.nama_pasien }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                      <span class="px-2 py-1 text-xs rounded-full font-medium"
                        :class="{
                          'bg-blue-100 text-blue-800': item.jenis === 'Konsul',
                          'bg-purple-100 text-purple-800': item.jenis === 'Tindakan',
                          'bg-green-100 text-green-800': item.jenis === 'Alat Kesehatan',
                          'bg-yellow-100 text-yellow-800': item.jenis === 'Resep',
                          'bg-gray-100 text-gray-800': item.jenis === 'Lainnya'
                        }">
                        {{ item.jenis }}
                      </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900 max-w-xs truncate">
                      {{ item.item }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      {{ item.dokter }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      {{ item.jumlah }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      Rp {{ formatCurrency(item.biaya) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      {{ item.disc }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-600">
                      Rp {{ formatCurrency(item.subtotal) }}
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Empty State (Ringkasan) -->
          <div v-else-if="activeTab === 'ringkasan' && filterLabel" class="text-center py-16 bg-white rounded-lg shadow-md">
            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
              <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
              </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-700 mb-2">Tidak ada data</h3>
            <p class="text-gray-500">Tidak ada transaksi pada periode yang dipilih</p>
          </div>

          <!-- Empty State (Perincian) -->
          <div v-else-if="activeTab === 'perincian' && filterLabel && (!dataDetailTransaksi || dataDetailTransaksi.length === 0)" class="text-center py-16 bg-white rounded-lg shadow-md">
            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
              <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
              </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-700 mb-2">Tidak ada data perincian</h3>
            <p class="text-gray-500">Tidak ada detail transaksi pada periode yang dipilih</p>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { Head, router, usePage } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const page = usePage()
const flash = computed(() => page.props.flash || {})

const props = defineProps({
  filterType: {
    type: String,
    default: 'daily'
  },
  date: {
    type: String,
    default: () => new Date().toISOString().split('T')[0]
  },
  month: {
    type: String,
    default: () => {
      const now = new Date()
      return `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}`
    }
  },
  year: {
    type: String,
    default: () => new Date().getFullYear().toString()
  },
  filterLabel: String,
  totalTransaksi: Number,
  totalPemasukan: String,
  totalItemTerjual: Number,
  dataTransaksi: Array,
  dataDetailTransaksi: Array,
})

const filterType = ref(props.filterType)
const date = ref(props.date)
const month = ref(props.month)
const year = ref(props.year)
const loading = ref(false)
const activeTab = ref('ringkasan')

// Generate years for dropdown (current year and 5 years back)
const years = computed(() => {
  const currentYear = new Date().getFullYear()
  const yearList = []
  for (let i = 0; i < 6; i++) {
    yearList.push(currentYear - i)
  }
  return yearList
})

const applyFilter = () => {
  loading.value = true
  router.get(route('laporan.index'), {
    filter_type: filterType.value,
    date: date.value,
    month: month.value,
    year: year.value,
  }, {
    preserveState: true,
    preserveScroll: true,
    onFinish: () => {
      loading.value = false
    }
  })
}

const exportPdf = () => {
  const params = new URLSearchParams({
    filter_type: filterType.value,
    date: date.value,
    month: month.value,
    year: year.value,
  })
  
  window.location.href = route('laporan.export-pdf') + '?' + params.toString()
}

const exportExcel = () => {
  const params = new URLSearchParams({
    filter_type: filterType.value,
    date: date.value,
    month: month.value,
    year: year.value,
  })
  
  window.location.href = route('laporan.export-excel') + '?' + params.toString()
}

const exportPdfDetail = () => {
  const params = new URLSearchParams({
    filter_type: filterType.value,
    date: date.value,
    month: month.value,
    year: year.value,
    type: 'detail',
  })
  
  window.location.href = route('laporan.export-pdf') + '?' + params.toString()
}

const exportExcelDetail = () => {
  const params = new URLSearchParams({
    filter_type: filterType.value,
    date: date.value,
    month: month.value,
    year: year.value,
    type: 'detail',
  })
  
  window.location.href = route('laporan.export-excel') + '?' + params.toString()
}

const formatCurrency = (value) => {
  return new Intl.NumberFormat('id-ID').format(value || 0)
}

onMounted(() => {
  // If no filter has been applied yet, apply default filter
  if (!props.filterLabel) {
    applyFilter()
  }
})
</script>

