<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue"
import { Head, router } from "@inertiajs/vue3"
import { ref, computed, watch } from "vue"

const props = defineProps({
  kunjungan: Object,
  polis: Array,
  filters: {
    type: Object,
    default: () => ({})
  },
  flash: {
    type: Object,
    default: () => ({})
  },
  debug: {
    type: Object,
    default: () => ({})
  }
})

// Active tab state
const activeTab = ref('kunjungan')

// Debug state
const showDebug = ref(false)

// Filter states
const searchQuery = ref(props.filters.search || '')
const dateFilter = ref(props.filters.date || '')
const polisFilter = ref(props.filters.polis || '')
const statusFilter = ref(props.filters.status || '')

// Polis-specific filter states
const polisSearchQuery = ref(props.filters.polis_search || '')
const polisStatusFilter = ref(props.filters.polis_status || '')
const polisSortBy = ref(props.filters.polis_sort || 'poli_desc')

// Service type definitions
const serviceTypes = {
  umum: {
    name: 'Poli Umum',
    icon: 'fas fa-stethoscope',
    color: 'blue',
    bgColor: 'bg-blue-50',
    textColor: 'text-blue-800',
    borderColor: 'border-blue-200'
  },
  gigi: {
    name: 'Poli Gigi',
    icon: 'fas fa-tooth',
    color: 'green',
    bgColor: 'bg-green-50',
    textColor: 'text-green-800',
    borderColor: 'border-green-200'
  },
  kia: {
    name: 'KIA (Kesehatan Ibu & Anak)',
    icon: 'fas fa-baby',
    color: 'pink',
    bgColor: 'bg-pink-50',
    textColor: 'text-pink-800',
    borderColor: 'border-pink-200'
  },
  laboratorium: {
    name: 'Laboratorium',
    icon: 'fas fa-flask',
    color: 'purple',
    bgColor: 'bg-purple-50',
    textColor: 'text-purple-800',
    borderColor: 'border-purple-200'
  },
  apotek: {
    name: 'Apotek',
    icon: 'fas fa-pills',
    color: 'orange',
    bgColor: 'bg-orange-50',
    textColor: 'text-orange-800',
    borderColor: 'border-orange-200'
  }
}

// Map for quick access poli by id
const poliIdMap = computed(() => {
  const map = {}
  if (props.polis) {
    props.polis.forEach(poli => {
      map[String(poli.id)] = poli
    })
  }
  return map
})

// Computed properties for visits
const filteredKunjungan = computed(() => {
  // Validate data structure
  if (!props.kunjungan || !props.kunjungan.data || !Array.isArray(props.kunjungan.data)) {
    console.warn('Invalid kunjungan data structure:', props.kunjungan)
    return []
  }

  return props.kunjungan.data.filter(kunjungan => {
    // Search filter - case insensitive
    const matchesSearch = !searchQuery.value ||
      (kunjungan.nm_p && kunjungan.nm_p.toLowerCase().includes(searchQuery.value.toLowerCase())) ||
      (kunjungan.no_reg && kunjungan.no_reg.toLowerCase().includes(searchQuery.value.toLowerCase())) ||
      (kunjungan.mrn && kunjungan.mrn.toLowerCase().includes(searchQuery.value.toLowerCase()))

    // Date filter - exact match
    const matchesDate = !dateFilter.value || kunjungan.tgl_reg === dateFilter.value

    // Poli filter - handle both string and number types
    const matchesPolis = !polisFilter.value || 
      (kunjungan.polis_id !== null && kunjungan.polis_id !== undefined && 
       String(kunjungan.polis_id) === String(polisFilter.value))

    // Status filter - exact match
    const matchesStatus = !statusFilter.value || kunjungan.status === statusFilter.value

    return matchesSearch && matchesDate && matchesPolis && matchesStatus
  })
})

// Computed properties for polis
const filteredPolis = computed(() => {
  // Validate data structure
  if (!props.polis || !Array.isArray(props.polis)) {
    console.warn('Invalid polis data structure:', props.polis)
    return []
  }

  let filtered = props.polis.filter(poli => {
    // Search filter - case insensitive
    const matchesSearch = !polisSearchQuery.value ||
      (poli.poli_desc && poli.poli_desc.toLowerCase().includes(polisSearchQuery.value.toLowerCase())) ||
      (poli.update_by && poli.update_by.toLowerCase().includes(polisSearchQuery.value.toLowerCase()))

    // Status filter
    const matchesStatus = !polisStatusFilter.value ||
      (polisStatusFilter.value === 'active' && poli.aktif === 'Y') ||
      (polisStatusFilter.value === 'inactive' && poli.aktif === 'N')

    return matchesSearch && matchesStatus
  })

  // Sort filtered results
  filtered.sort((a, b) => {
    switch (polisSortBy.value) {
      case 'poli_desc':
        return String(a.poli_desc || '').localeCompare(String(b.poli_desc || ''))
      case 'update_date':
        return new Date(b.update_date || 0) - new Date(a.update_date || 0)
      case 'update_by':
        return String(a.update_by || '').localeCompare(String(b.update_by || ''))
      default:
        return 0
    }
  })

  return filtered
})

// Helper functions
function formatDate(dateString) {
  if (!dateString) return '-'
  const date = new Date(dateString)
  return date.toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
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

function calculateTotalBiaya(kunjungan) {
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

function getServiceInfo(kunjungan) {
  return serviceTypes[kunjungan.kunjungan] || serviceTypes.umum
}

function getStatusColor(status) {
  switch (status?.toLowerCase()) {
    case 'selesai':
      return 'bg-green-100 text-green-800'
    case 'proses':
      return 'bg-yellow-100 text-yellow-800'
    case 'pending':
      return 'bg-gray-100 text-gray-800'
    default:
      return 'bg-blue-100 text-blue-800'
  }
}

// Filter functions
function applyFilters() {
  const params = {}

  if (searchQuery.value.trim()) params.search = searchQuery.value.trim()
  if (dateFilter.value) params.date = dateFilter.value
  if (polisFilter.value) params.polis = polisFilter.value
  if (statusFilter.value) params.status = statusFilter.value

  router.get(route('dokter.poli_layanan'), params, {
    preserveState: true,
    replace: true
  })
}

function clearFilters() {
  searchQuery.value = ''
  dateFilter.value = ''
  polisFilter.value = ''
  statusFilter.value = ''
  router.get(route('dokter.poli_layanan'), {}, {
    preserveState: true,
    replace: true
  })
}

// Polis-specific filter functions
function applyPolisFilters() {
  const params = {}

  if (polisSearchQuery.value.trim()) params.polis_search = polisSearchQuery.value.trim()
  if (polisStatusFilter.value) params.polis_status = polisStatusFilter.value
  if (polisSortBy.value && polisSortBy.value !== 'poli_desc') params.polis_sort = polisSortBy.value

  router.get(route('dokter.poli_layanan'), params, {
    preserveState: true,
    replace: true
  })
}

function clearPolisFilters() {
  polisSearchQuery.value = ''
  polisStatusFilter.value = ''
  polisSortBy.value = 'poli_desc'
  router.get(route('dokter.poli_layanan'), {}, {
    preserveState: true,
    replace: true
  })
}

// Tab switching
function switchTab(tab) {
  activeTab.value = tab
}

// Debounced filter application
let filterTimeout = null
let polisFilterTimeout = null

// Watch for filter changes with debounce
watch([searchQuery, dateFilter, polisFilter, statusFilter], () => {
  if (filterTimeout) clearTimeout(filterTimeout)
  filterTimeout = setTimeout(() => {
    applyFilters()
  }, 500) // 500ms debounce
})

// Watch for polis filter changes with debounce
watch([polisSearchQuery, polisStatusFilter, polisSortBy], () => {
  if (polisFilterTimeout) clearTimeout(polisFilterTimeout)
  polisFilterTimeout = setTimeout(() => {
    applyPolisFilters()
  }, 500) // 500ms debounce
})

// Get display label for poli
function getPoliLabelById(id) {
  // Prefer poli_desc if available, fallback to nama_poli
  const poli = poliIdMap.value[String(id)]
  if (poli) {
    if (poli.poli_desc && poli.poli_desc.trim() !== '') {
      return poli.poli_desc
    }
    if (poli.nama_poli && poli.nama_poli.trim() !== '') {
      return poli.nama_poli
    }
  }
  return '-'
}
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Poli Layanan - Manajemen Kunjungan & Polis" />

    <div class="max-w-7xl mx-auto py-8 px-4">
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

      <!-- Debug Information (only show in development) -->
      <div v-if="debug && Object.keys(debug).length > 0" class="mb-6 bg-gray-100 border border-gray-300 text-gray-700 px-4 py-3 rounded-lg">
        <div class="flex items-center justify-between">
          <div class="flex items-center">
            <i class="fas fa-info-circle mr-2"></i>
            <span class="font-medium">Debug Info:</span>
          </div>
          <button @click="showDebug = !showDebug" class="text-sm text-blue-600 hover:text-blue-800">
            {{ showDebug ? 'Hide' : 'Show' }}
          </button>
        </div>
        <div v-if="showDebug" class="mt-3 text-sm">
          <p><strong>Kunjungan Count:</strong> {{ debug.kunjungan_count || 0 }}</p>
          <p><strong>Polis Count:</strong> {{ debug.polis_count || 0 }}</p>
          <div v-if="debug.filters_applied">
            <p><strong>Filters Applied:</strong></p>
            <ul class="ml-4 list-disc">
              <li v-for="(value, key) in debug.filters_applied" :key="key">
                {{ key }}: {{ value || 'None' }}
              </li>
            </ul>
          </div>
          <div v-if="debug.error" class="text-red-600">
            <p><strong>Error:</strong> {{ debug.error }}</p>
          </div>
        </div>
      </div>

      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-6 flex items-center">
          <i class="fas fa-hospital mr-3 text-green-600"></i>
          Poli Layanan
        </h1>

        <!-- Tab Navigation -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-2">
          <div class="flex space-x-1">
            <button
              @click="switchTab('kunjungan')"
              :class="[
                'px-6 py-3 rounded-lg font-medium transition-all duration-200 flex items-center',
                activeTab === 'kunjungan'
                  ? 'bg-blue-50 text-blue-800 border-blue-200 border-2 shadow-md'
                  : 'bg-gray-50 text-gray-600 hover:bg-gray-100 border-2 border-transparent'
              ]"
            >
              <i class="fas fa-calendar-alt mr-2"></i>
              Kunjungan Pasien
            </button>
            <button
              @click="switchTab('polis')"
              :class="[
                'px-6 py-3 rounded-lg font-medium transition-all duration-200 flex items-center',
                activeTab === 'polis'
                  ? 'bg-green-50 text-green-800 border-green-200 border-2 shadow-md'
                  : 'bg-gray-50 text-gray-600 hover:bg-gray-100 border-2 border-transparent'
              ]"
            >
              <i class="fas fa-clipboard-list mr-2"></i>
              Manajemen Polis
            </button>
          </div>
        </div>
      </div>

      <!-- Kunjungan Tab Content -->
      <div v-if="activeTab === 'kunjungan'">
        <!-- Search and Filter -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Search -->
            <div>
              <label for="search" class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-search mr-1"></i>
                Cari Pasien
              </label>
              <input
                type="text"
                id="search"
                v-model="searchQuery"
                @keyup.enter="applyFilters"
                placeholder="Cari berdasarkan nama, No Reg, atau MRN..."
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              />
            </div>

            <!-- Date Filter -->
            <div>
              <label for="date" class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-calendar mr-1"></i>
                Filter Tanggal
              </label>
              <input
                type="date"
                id="date"
                v-model="dateFilter"
                @change="applyFilters"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              />
            </div>

            <!-- Polis Filter -->
            <div>
              <label for="polis" class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-shield-alt mr-1"></i>
                Filter Poli
              </label>
              <select
                id="polis"
                v-model="polisFilter"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              >
                <option value="">Semua Poli</option>
                <option
                  v-for="poli in polis"
                  :key="poli.id"
                  :value="String(poli.id)"
                >
                  {{ getPoliLabelById(poli.id) }}
                </option>
              </select>
            </div>

            <!-- Status Filter -->
            <div>
              <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-info-circle mr-1"></i>
                Filter Status
              </label>
              <select
                id="status"
                v-model="statusFilter"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              >
                <option value="">Semua Status</option>
                <option value="pending">Pending</option>
                <option value="proses">Proses</option>
                <option value="selesai">Selesai</option>
              </select>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="flex gap-2 mt-4">
            <button
              @click="applyFilters"
              class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm transition flex items-center"
            >
              <i class="fas fa-search mr-1"></i>
              Cari
            </button>
            <button
              @click="clearFilters"
              class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg text-sm transition flex items-center"
            >
              <i class="fas fa-times mr-1"></i>
              Reset
            </button>
          </div>

          <!-- Results count -->
          <div class="mt-4 text-sm text-gray-600">
            <span v-if="props.kunjungan?.data">
              Menampilkan {{ filteredKunjungan.length }} dari {{ props.kunjungan.total || 0 }} kunjungan
            </span>
            <span v-else class="text-red-500">
              <i class="fas fa-exclamation-triangle mr-1"></i>
              Data kunjungan tidak tersedia
            </span>
          </div>
        </div>

        <!-- Kunjungan List -->
        <div v-if="filteredKunjungan.length > 0" class="space-y-6">
          <div
            v-for="(k, idx) in filteredKunjungan"
            :key="k.id"
            class="bg-white shadow-lg rounded-xl border border-gray-200 overflow-hidden hover:shadow-xl transition-shadow"
          >
            <!-- Kunjungan Header -->
            <div :class="[getServiceInfo(k).bgColor, 'px-6 py-4 border-b border-gray-200']">
              <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center gap-4">
                <div class="flex items-center gap-4">
                  <div :class="[`bg-${getServiceInfo(k).color}-100`, 'w-12 h-12 rounded-full flex items-center justify-center text-xl']">
                    <i :class="[getServiceInfo(k).icon, `text-${getServiceInfo(k).color}-700`]"></i>
                  </div>
                  <div>
                    <h3 class="text-xl font-semibold text-gray-900">{{ k.nm_p }}</h3>
                    <div class="text-sm text-gray-600 mt-1">
                      <p><span class="font-medium">No Reg:</span> {{ k.no_reg }}</p>
                      <p><span class="font-medium">MRN:</span> {{ k.mrn }}</p>
                      <p><span class="font-medium">Poli:</span>
                        <span :class="[`bg-${getServiceInfo(k).color}-100`, `text-${getServiceInfo(k).color}-800`, 'px-2 py-1 rounded-full text-xs font-medium']">
                          <!-- Tampilkan nama atau deskripsi poli berdasarkan id -->
                          {{
                            getPoliLabelById(k.polis_id) !== '-' ? getPoliLabelById(k.polis_id) : (k.penjamin || '-')
                          }}
                        </span>
                      </p>
                    </div>
                  </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-wrap gap-2">
                  <button
                    @click="router.visit(`/pasien/${k.psn_id}`)"
                    class="px-4 py-2 text-sm bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow transition flex items-center"
                  >
                    <i class="fas fa-eye mr-1"></i>
                    Detail Pasien
                  </button>
                  <button
                    @click="router.visit(`/kasir/${k.id}`)"
                    class="px-4 py-2 text-sm bg-purple-600 hover:bg-purple-700 text-white rounded-lg shadow transition flex items-center"
                  >
                    <i class="fas fa-file-medical mr-1"></i>
                    Detail Kunjungan
                  </button>
                </div>
              </div>
            </div>

            <!-- Kunjungan Info -->
            <div class="p-6">
              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="bg-blue-50 rounded-lg p-4">
                  <div class="flex items-center">
                    <i class="fas fa-calendar-alt text-blue-600 text-xl mr-3"></i>
                    <div>
                      <p class="text-sm text-blue-600 font-medium">Tanggal Kunjungan</p>
                      <p class="text-lg font-semibold text-blue-800">{{ formatDate(k.tgl_reg) }}</p>
                    </div>
                  </div>
                </div>

                <div class="bg-green-50 rounded-lg p-4">
                  <div class="flex items-center">
                    <i class="fas fa-hospital text-green-600 text-xl mr-3"></i>
                    <div>
                      <p class="text-sm text-green-600 font-medium">Jenis Perawatan</p>
                      <p class="text-lg font-semibold text-green-800">{{ k.perawatan }}</p>
                    </div>
                  </div>
                </div>

                <div class="bg-purple-50 rounded-lg p-4">
                  <div class="flex items-center">
                    <i class="fas fa-file-invoice text-purple-600 text-xl mr-3"></i>
                    <div>
                      <p class="text-sm text-purple-600 font-medium">No Invoice</p>
                      <p class="text-lg font-semibold text-purple-800">{{ k.no_inv || '-' }}</p>
                    </div>
                  </div>
                </div>

                <div class="bg-orange-50 rounded-lg p-4">
                  <div class="flex items-center">
                    <i class="fas fa-calculator text-orange-600 text-xl mr-3"></i>
                    <div>
                      <p class="text-sm text-orange-600 font-medium">Total Biaya</p>
                      <p class="text-lg font-semibold text-orange-800">{{ formatCurrency(calculateTotalBiaya(k)) }}</p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Service Information -->
              <div :class="[getServiceInfo(k).bgColor, 'rounded-lg p-4 mb-4']">
                <h4 :class="[getServiceInfo(k).textColor, 'text-lg font-semibold mb-3 flex items-center']">
                  <i :class="[getServiceInfo(k).icon, 'mr-2']"></i>
                  Informasi {{ getServiceInfo(k).name }}
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <p class="text-sm font-medium text-gray-700">Jenis Kunjungan</p>
                    <p :class="[getServiceInfo(k).textColor, 'font-semibold']">{{ k.kunjungan }}</p>
                  </div>
                  <div>
                    <p class="text-sm font-medium text-gray-700">ICD Code</p>
                    <p class="text-gray-600">{{ k.icd || '-' }}</p>
                  </div>
                </div>
              </div>

              <!-- Medical Services Details -->
              <div class="space-y-4">
                <!-- Konsultasi -->
                <div v-if="k.konsuls && k.konsuls.length > 0" class="bg-blue-50 rounded-lg p-4">
                  <h4 class="text-lg font-semibold text-blue-800 mb-3 flex items-center">
                    <i class="fas fa-stethoscope mr-2"></i>
                    Konsultasi Medis
                  </h4>
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div v-for="konsul in k.konsuls" :key="konsul.id" class="bg-white rounded-lg p-3">
                      <p class="font-medium text-gray-800">{{ konsul.dokter }}</p>
                      <p class="text-sm text-gray-600">{{ konsul.dskp_kons }}</p>
                      <p class="text-sm text-blue-600 font-medium">{{ konsul.st_kons }}</p>
                    </div>
                  </div>
                </div>

                <!-- Tindakan -->
                <div v-if="k.tindaks && k.tindaks.length > 0" class="bg-green-50 rounded-lg p-4">
                  <h4 class="text-lg font-semibold text-green-800 mb-3 flex items-center">
                    <i class="fas fa-procedures mr-2"></i>
                    Tindakan Medis
                  </h4>
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div v-for="tindak in k.tindaks" :key="tindak.id" class="bg-white rounded-lg p-3">
                      <p class="font-medium text-gray-800">{{ tindak.dktr_tindak }}</p>
                      <p class="text-sm text-gray-600">{{ tindak.dskp_tindak }}</p>
                      <p class="text-sm text-green-600 font-medium">{{ tindak.st_tindak }}</p>
                    </div>
                  </div>
                </div>

                <!-- Resep -->
                <div v-if="k.rsp && k.rsp.length > 0" class="bg-purple-50 rounded-lg p-4">
                  <h4 class="text-lg font-semibold text-purple-800 mb-3 flex items-center">
                    <i class="fas fa-pills mr-2"></i>
                    Resep Obat
                  </h4>
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div v-for="rsp in k.rsp" :key="rsp.id" class="bg-white rounded-lg p-3">
                      <p class="font-medium text-gray-800">{{ rsp.dskp_rsp }}</p>
                      <p class="text-sm text-gray-600">Jumlah: {{ rsp.jmlh_rsp }}</p>
                      <p class="text-sm text-purple-600 font-medium">{{ rsp.st_rsp }}</p>
                    </div>
                  </div>
                </div>

                <!-- Alkes -->
                <div v-if="k.alkes && k.alkes.length > 0" class="bg-orange-50 rounded-lg p-4">
                  <h4 class="text-lg font-semibold text-orange-800 mb-3 flex items-center">
                    <i class="fas fa-tools mr-2"></i>
                    Alat Kesehatan
                  </h4>
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div v-for="alkes in k.alkes" :key="alkes.id" class="bg-white rounded-lg p-3">
                      <p class="font-medium text-gray-800">{{ alkes.poli }}</p>
                      <p class="text-sm text-gray-600">{{ alkes.dskp_alkes }}</p>
                      <p class="text-sm text-orange-600 font-medium">{{ alkes.st_alkes }}</p>
                    </div>
                  </div>
                </div>

                <!-- Lainnya -->
                <div v-if="k.lainnyas && k.lainnyas.length > 0" class="bg-gray-50 rounded-lg p-4">
                  <h4 class="text-lg font-semibold text-gray-800 mb-3 flex items-center">
                    <i class="fas fa-list mr-2"></i>
                    Layanan Lainnya
                  </h4>
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div v-for="lainnya in k.lainnyas" :key="lainnya.id" class="bg-white rounded-lg p-3">
                      <p class="font-medium text-gray-800">{{ lainnya.dskp_lainnya }}</p>
                      <p class="text-sm text-gray-600">Jumlah: {{ lainnya.jmlh_lainnaya }}</p>
                      <p class="text-sm text-gray-600 font-medium">{{ lainnya.st_lainnya }}</p>
                    </div>
                  </div>
                </div>

                <!-- No Data Message -->
                <div v-if="!k.konsuls?.length && !k.tindaks?.length && !k.rsp?.length && !k.alkes?.length && !k.lainnyas?.length" class="text-center py-8">
                  <i class="fas fa-info-circle text-4xl text-gray-300 mb-3"></i>
                  <p class="text-gray-500 font-medium">Belum ada layanan medis untuk kunjungan ini</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Pagination -->
          <div v-if="kunjungan?.links" class="flex justify-center mt-8">
            <nav class="flex space-x-2">
              <template v-for="link in kunjungan.links" :key="link.label">
                <button
                  v-if="link.url"
                  @click="router.get(link.url)"
                  v-html="link.label"
                  :class="[
                    'px-3 py-2 text-sm border rounded-lg transition',
                    link.active
                      ? 'bg-blue-600 text-white border-blue-600'
                      : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'
                  ]"
                ></button>
                <span
                  v-else
                  v-html="link.label"
                  class="px-3 py-2 text-sm text-gray-500"
                ></span>
              </template>
            </nav>
          </div>
        </div>

        <!-- Empty State for Kunjungan -->
        <div v-else class="text-center py-12">
          <div class="bg-blue-50 w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-calendar-alt text-4xl text-blue-600"></i>
          </div>
          <h3 class="text-lg font-medium text-gray-500 mb-2">
            {{ props.kunjungan?.data ? 'Tidak ada kunjungan yang sesuai filter' : 'Data kunjungan tidak tersedia' }}
          </h3>
          <p class="text-gray-400 mb-6">
            {{ props.kunjungan?.data ? 'Coba ubah filter pencarian atau tanggal' : 'Periksa koneksi database atau hubungi administrator' }}
          </p>
          <div class="flex gap-3 justify-center">
            <button
              v-if="props.kunjungan?.data"
              @click="clearFilters"
              class="px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-lg shadow transition inline-flex items-center"
            >
              <i class="fas fa-refresh mr-2"></i>
              Reset Filter
            </button>
            <button
              @click="router.visit('/pasien/create')"
              class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow transition inline-flex items-center"
            >
              <i class="fas fa-plus mr-2"></i>
              Tambah Pasien Baru
            </button>
          </div>
        </div>
      </div>

      <!-- Polis Tab Content -->
      <div v-if="activeTab === 'polis'">
        <!-- Search and Filter for Polis -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Search -->
            <div>
              <label for="search-polis" class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-search mr-1"></i>
                Cari Polis
              </label>
              <input
                type="text"
                id="search-polis"
                v-model="polisSearchQuery"
                @keyup.enter="applyPolisFilters"
                placeholder="Cari berdasarkan nama polis atau update by..."
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
              />
            </div>

            <!-- Status Filter -->
            <div>
              <label for="polis-status" class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-filter mr-1"></i>
                Status
              </label>
              <select
                id="polis-status"
                v-model="polisStatusFilter"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
              >
                <option value="">Semua Status</option>
                <option value="active">Aktif</option>
                <option value="inactive">Tidak Aktif</option>
              </select>
            </div>

            <!-- Sort By -->
            <div>
              <label for="polis-sort" class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-sort mr-1"></i>
                Urutkan Berdasarkan
              </label>
              <select
                id="polis-sort"
                v-model="polisSortBy"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
              >
                <option value="poli_desc">Nama Polis</option>
                <option value="update_date">Tanggal Update</option>
                <option value="update_by">Update By</option>
              </select>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col gap-2">
              <label class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-cog mr-1"></i>
                Aksi Filter
              </label>
              <div class="flex gap-2">
                <button
                  @click="applyPolisFilters"
                  class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm transition flex items-center"
                >
                  <i class="fas fa-search mr-1"></i>
                  Cari
                </button>
                <button
                  @click="clearPolisFilters"
                  class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg text-sm transition flex items-center"
                >
                  <i class="fas fa-times mr-1"></i>
                  Reset
                </button>
              </div>
            </div>
          </div>

          <!-- Results count -->
          <div class="mt-4 text-sm text-gray-600">
            <span v-if="props.polis">
              Menampilkan {{ filteredPolis.length }} dari {{ props.polis.length }} polis
              <span v-if="polisSearchQuery || polisStatusFilter" class="text-green-600 font-medium">
                (hasil filter)
              </span>
            </span>
            <span v-else class="text-red-500">
              <i class="fas fa-exclamation-triangle mr-1"></i>
              Data polis tidak tersedia
            </span>
          </div>
        </div>

        <!-- Polis List -->
        <div v-if="filteredPolis.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div
            v-for="poli in filteredPolis"
            :key="poli.id"
            class="bg-white shadow-lg rounded-xl border border-gray-200 overflow-hidden hover:shadow-xl transition-shadow"
          >
            <!-- Polis Header -->
            <div class="bg-green-50 px-6 py-4 border-b border-gray-200">
              <div class="flex items-center gap-4">
                <div class="bg-green-100 w-12 h-12 rounded-full flex items-center justify-center text-xl">
                  <i class="fas fa-shield-alt text-green-700"></i>
                </div>
                <div>
                  <h3 class="text-xl font-semibold text-gray-900">{{ poli.poli_desc }}</h3>
                  <p class="text-sm text-green-600">ID: {{ poli.id }}</p>
                </div>
              </div>
            </div>

            <!-- Polis Info -->
            <div class="p-6">
              <div class="space-y-4">
                <div class="bg-blue-50 rounded-lg p-4">
                  <div class="flex items-center">
                    <i class="fas fa-info-circle text-blue-600 text-xl mr-3"></i>
                    <div>
                      <p class="text-sm text-blue-600 font-medium">Status</p>
                      <p class="text-lg font-semibold text-blue-800">
                        <span :class="poli.aktif === 'Y' ? 'text-green-600' : 'text-red-600'">
                          {{ poli.aktif === 'Y' ? 'Aktif' : 'Tidak Aktif' }}
                        </span>
                      </p>
                    </div>
                  </div>
                </div>

                <div class="bg-purple-50 rounded-lg p-4">
                  <div class="flex items-center">
                    <i class="fas fa-calendar text-purple-600 text-xl mr-3"></i>
                    <div>
                      <p class="text-sm text-purple-600 font-medium">Terakhir Diupdate</p>
                      <p class="text-lg font-semibold text-purple-800">{{ formatDate(poli.update_date) }}</p>
                    </div>
                  </div>
                </div>

                <div class="bg-gray-50 rounded-lg p-4">
                  <div class="flex items-center">
                    <i class="fas fa-user text-gray-600 text-xl mr-3"></i>
                    <div>
                      <p class="text-sm text-gray-600 font-medium">Diupdate Oleh</p>
                      <p class="text-lg font-semibold text-gray-800">{{ poli.update_by || 'System' }}</p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Action Buttons -->
              <div class="flex gap-2 mt-6">
                <button
                  @click="router.visit(route('polis.edit', poli.id))"
                  class="px-4 py-2 text-sm bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow transition flex items-center flex-1 justify-center"
                >
                  <i class="fas fa-edit mr-1"></i>
                  Edit
                </button>
                <button
                  @click="router.visit(route('polis.show', poli.id))"
                  class="px-4 py-2 text-sm bg-green-600 hover:bg-green-700 text-white rounded-lg shadow transition flex items-center flex-1 justify-center"
                >
                  <i class="fas fa-eye mr-1"></i>
                  Detail
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State for Polis -->
        <div v-else class="text-center py-12">
          <div class="bg-green-50 w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-shield-alt text-4xl text-green-600"></i>
          </div>
          <h3 class="text-lg font-medium text-gray-500 mb-2">Tidak ada polis</h3>
          <p class="text-gray-400 mb-6">Belum ada polis yang terdaftar</p>
          <button
            @click="router.visit(route('polis.create'))"
            class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg shadow transition inline-flex items-center"
          >
            <i class="fas fa-plus mr-2"></i>
            Tambah Polis Baru
          </button>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
