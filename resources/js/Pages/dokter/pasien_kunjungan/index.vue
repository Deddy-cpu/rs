<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue"
import { Head, router } from "@inertiajs/vue3"
import { ref, computed, watch } from "vue"
import { useWebSocket } from '@/composables/useWebSocket'

// Dummy request for initialization (can be removed in production)
axios.get('http://localhost:8000/api/test', { withCredentials: true })

const props = defineProps({
  pasien: Object,
  filters: {
    type: Object,
    default: () => ({})
  },
  showRiwayat: {
    type: Boolean,
    default: false
  },
  uniquePenjamin: {
    type: Array,
    default: () => []
  },
  uniquePerawatan: {
    type: Array,
    default: () => []
  },
  uniqueKunjungan: {
    type: Array,
    default: () => []
  },
  uniquePoli: {
    type: Array,
    default: () => []
  },
  userRuangan: {
    type: String,
    default: null
  },
  flash: {
    type: Object,
    default: () => ({})
  }
})

// Search and filter
const searchQuery = ref(props.filters.search ?? '')
const filterPenjamin = ref(props.filters.penjamin ?? '')
const filterPerawatan = ref(props.filters.perawatan ?? '')
const filterKunjungan = ref(props.filters.kunjungan ?? '')
const filterPoli = ref(props.filters.poli ?? '')
const showRiwayat = ref(props.showRiwayat ?? false)

// State for delete modal
const isDeleteModalOpen = ref(false)
const selectedPasienDelete = ref(null)

const openDeleteModal = (pasien) => {
  selectedPasienDelete.value = pasien
  isDeleteModalOpen.value = true
}

const closeDeleteModal = () => {
  isDeleteModalOpen.value = false
  selectedPasienDelete.value = null
}

// WebSocket for real-time updates
const { setMessageHandler } = useWebSocket(['kunjungan'])

setMessageHandler((data) => {
  if (data.type === 'update') {
    if (
      data.channel === 'kunjungan' &&
      (data.event === 'kunjungan.created' || data.event === 'kunjungan.updated' || data.event === 'kunjungan.deleted')
    ) {
      router.reload({
        only: ['pasien'],
        preserveState: true,
        preserveScroll: true
      })
    }
  }
})

// Computed properties
const filteredPasien = computed(() => {
  return props.pasien?.data || []
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

// Helper functions untuk menghitung total per kategori layanan
function calculateKonsulTotal(konsuls) {
  if (!konsuls || konsuls.length === 0) return 0
  return konsuls.reduce((total, konsul) => {
    const jumlah = parseFloat(konsul.jmlh_kons) || 0
    const harga = parseFloat(String(konsul.bya_kons).replace(/[^\d]/g, '')) || 0
    return total + (jumlah * harga)
  }, 0)
}

function calculateTindakTotal(tindaks) {
  if (!tindaks || tindaks.length === 0) return 0
  return tindaks.reduce((total, tindak) => {
    const jumlah = parseFloat(tindak.jmlh_tindak) || 0
    const harga = parseFloat(String(tindak.bya_tindak).replace(/[^\d]/g, '')) || 0
    return total + (jumlah * harga)
  }, 0)
}

function calculateAlkesTotal(alkes) {
  if (!alkes || alkes.length === 0) return 0
  return alkes.reduce((total, alkesItem) => {
    const jumlah = parseFloat(alkesItem.jmlh_alkes) || 0
    const harga = parseFloat(String(alkesItem.bya_alkes).replace(/[^\d]/g, '')) || 0
    return total + (jumlah * harga)
  }, 0)
}

function calculateRspTotal(rsp) {
  if (!rsp || rsp.length === 0) return 0
  return rsp.reduce((total, rspItem) => {
    const jumlah = parseFloat(rspItem.jmlh_rsp) || 0
    const harga = parseFloat(String(rspItem.bya_rsp).replace(/[^\d]/g, '')) || 0
    return total + (jumlah * harga)
  }, 0)
}

function calculateLainnyaTotal(lainnyas) {
  if (!lainnyas || lainnyas.length === 0) return 0
  return lainnyas.reduce((total, lainnya) => {
    const jumlah = parseFloat(lainnya.jmlh_lainnya) || 0 // fixed typo jmlh_lainnaya -> jmlh_lainnya
    const harga = parseFloat(String(lainnya.bya_lainnya).replace(/[^\d]/g, '')) || 0
    return total + (jumlah * harga)
  }, 0)
}

function calculateTotalBiaya(pasien) {
  // Use the pre-calculated total_biaya from transaction if available
  if (pasien.transaksi && pasien.transaksi.length > 0) {
    return pasien.transaksi.reduce((total, transaksi) => {
      return total + (parseFloat(transaksi.total_biaya) || 0)
    }, 0)
  }

  // Fallback: calculate manually if transaction data is not available
  let total = 0

  // Hitung total konsultasi
  if (pasien.konsuls) {
    pasien.konsuls.forEach(konsul => {
      total += parseFloat(String(konsul.st_kons).replace(/[^\d]/g, '')) || 0
    })
  }

  // Hitung total tindakan
  if (pasien.tindaks) {
    pasien.tindaks.forEach(tindak => {
      total += parseFloat(String(tindak.st_tindak).replace(/[^\d]/g, '')) || 0
    })
  }

  // Hitung total alkes
  if (pasien.alkes) {
    pasien.alkes.forEach(alkes => {
      total += parseFloat(String(alkes.st_alkes).replace(/[^\d]/g, '')) || 0
    })
  }

  // Hitung total resep
  if (pasien.rsp) {
    pasien.rsp.forEach(rsp => {
      total += parseFloat(String(rsp.st_rsp).replace(/[^\d]/g, '')) || 0
    })
  }

  // Hitung total lainnya
  if (pasien.lainnyas) {
    pasien.lainnyas.forEach(lainnya => {
      total += parseFloat(String(lainnya.st_lainnya).replace(/[^\d]/g, '')) || 0
    })
  }

  return total
}

function performSearch() {
  applyFilters()
}

function applyFilters() {
  const params = new URLSearchParams()

  if (searchQuery.value) params.append('search', searchQuery.value)
  if (filterPenjamin.value) params.append('penjamin', filterPenjamin.value)
  if (filterPerawatan.value) params.append('perawatan', filterPerawatan.value)
  if (filterKunjungan.value) params.append('kunjungan', filterKunjungan.value)
  if (filterPoli.value) params.append('poli', filterPoli.value)
  if (showRiwayat.value) params.append('riwayat', '1')

  router.get(route('dokter.pasien-kunjungan'), Object.fromEntries(params), {
    preserveState: true,
    replace: true
  })
}

function toggleRiwayat() {
  showRiwayat.value = !showRiwayat.value
  applyFilters()
}

function resetFilters() {
  searchQuery.value = ''
  filterPenjamin.value = ''
  filterPerawatan.value = ''
  filterKunjungan.value = ''
  filterPoli.value = ''
  showRiwayat.value = false
  router.get(route('dokter.pasien-kunjungan'))
}

// Debounce helper
function debounce(fn, delay = 400) {
  let timeout
  return (...args) => {
    clearTimeout(timeout)
    timeout = setTimeout(() => fn(...args), delay)
  }
}

// Debounced search
const debouncedSearch = debounce(() => {
  performSearch()
}, 400)

watch(searchQuery, () => {
  debouncedSearch()
})

function getStatusColor(status) {
  switch (status?.toLowerCase()) {
    case 'selesai':
    case 'completed':
      return 'bg-[#2E7D32] text-white border-[#1B5E20]'
    case 'proses':
      return 'bg-[#FBD46D] text-[#1A2E35] border-[#F9C74F]'
    case 'pending':
      return 'bg-[#FBD46D] text-[#1A2E35] border-[#F9C74F]'
    default:
      return 'bg-[#FBD46D] text-[#1A2E35] border-[#F9C74F]'
  }
}

function getStatusLabel(status) {
  switch (status?.toLowerCase()) {
    case 'completed':
      return 'Sudah Dilayani'
    case 'pending':
      return 'Belum Dilayani'
    default:
      return status || 'Belum Dilayani'
  }
}

// Modal confirm logic for deleting kunjungan
function confirmDelete(pasien) {
  openDeleteModal(pasien)
}

function deleteKunjungan(kunjunganId) {
  if (!kunjunganId) {
    alert('ID kunjungan tidak ditemukan. Silakan coba lagi.')
    closeDeleteModal()
    return
  }

  router.delete(route('kunjungan.destroy', kunjunganId), {
    preserveState: false,
    preserveScroll: false,
    onSuccess: () => {
      closeDeleteModal()
      router.reload({
        only: ['pasien', 'flash'],
        preserveState: false,
        preserveScroll: false
      })
    },
    onError: (errors) => {
      closeDeleteModal()
      router.reload({
        only: ['flash'],
        preserveState: true
      })
    }
  })
}
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Pasien & Kunjungan - Dokter" />

    <div class="min-h-screen bg-cover bg-center p-6" style="background-image: url('/images/bg-login.png')">
      <div class="max-w-7xl mx-auto">

        <!-- Flash Messages - Only show one at a time, with auto-dismiss -->
        <transition name="slide-fade">
          <div v-if="flash.success" key="success" class="mb-6 bg-[#F2F6F7] border border-[#4CAF93] text-[#00796B] px-6 py-4 rounded-2xl shadow-lg">
            <div class="flex items-center justify-between">
              <div class="flex items-center">
                <svg class="w-6 h-6 text-[#00796B] mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                {{ flash.success }}
              </div>
              <button @click="flash.success = null" class="text-[#00796B] hover:text-[#1A2E35] ml-4" aria-label="Close">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
              </button>
            </div>
          </div>
        </transition>
        <transition name="slide-fade">
          <div v-if="flash.error && !flash.success" key="error" class="mb-6 bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-2xl shadow-lg">
            <div class="flex items-center justify-between">
              <div class="flex items-center">
                <svg class="w-6 h-6 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                {{ flash.error }}
              </div>
              <button @click="flash.error = null" class="text-red-600 hover:text-red-800 ml-4" aria-label="Close">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
              </button>
            </div>
          </div>
        </transition>

        <!-- Header Section -->
        <div class="mb-8">
          <div class="text-center mb-6">
            <h1 class="text-4xl md:text-5xl font-bold bg-gradient-to-r from-[#4CAF93] to-[#00796B] bg-clip-text text-transparent mb-2">
              <span class="inline-block align-middle mr-2"></span>
              Pasien & Kunjungan
            </h1>
            <p class="text-[#1A2E35] text-lg">
              Kelola data pasien dan kunjungan medis dengan mudah
            </p>
            <!-- Info Ruangan/Poli -->
            <div v-if="userRuangan" class="mt-4 inline-block px-4 py-2 bg-[#F2F6F7] border border-[#4CAF93] rounded-lg">
              <p class="text-sm text-[#1A2E35]">
                <span class="font-semibold">📍 Ruangan Anda:</span> {{ userRuangan }}
                <span class="text-xs text-[#00796B] ml-2">(Hanya menampilkan pasien dari ruangan ini)</span>
              </p>
            </div>
          </div>

          <!-- Toggle Aktif / Riwayat -->
          <div class="flex justify-center mb-6">
            <div class="inline-flex rounded-xl bg-[#F2F6F7] backdrop-blur-sm shadow-lg border border-[#4CAF93]/20 p-1">
              <button
                @click="toggleRiwayat"
                :class="[
                  'px-6 py-3 rounded-lg font-semibold transition-all duration-200 flex items-center gap-2',
                  !showRiwayat
                    ? 'bg-gradient-to-r from-[#4CAF93] to-[#00796B] text-white shadow-md'
                    : 'text-[#1A2E35] hover:text-[#00796B]'
                ]"
              >
                <i class="fas fa-clock"></i>
                Kunjungan Aktif
                <span v-if="!showRiwayat" class="ml-2 px-2 py-0.5 bg-white/20 rounded-full text-xs">
                  {{ pasien?.total || 0 }}
                </span>
              </button>
              <button
                @click="toggleRiwayat"
                :class="[
                  'px-6 py-3 rounded-lg font-semibold transition-all duration-200 flex items-center gap-2',
                  showRiwayat
                    ? 'bg-gradient-to-r from-[#4CAF93] to-[#00796B] text-white shadow-md'
                    : 'text-[#1A2E35] hover:text-[#00796B]'
                ]"
              >
                <i class="fas fa-history"></i>
                Riwayat Kunjungan
                <span v-if="showRiwayat" class="ml-2 px-2 py-0.5 bg-white/20 rounded-full text-xs">
                  {{ pasien?.total || 0 }}
                </span>
              </button>
            </div>
          </div>

          <!-- Action Bar -->
          <div class="flex flex-col md:flex-row justify-between items-center gap-4 bg-transparent px-1 pt-0 pb-0">
            <!-- Search -->
            <div class="flex items-center space-x-3 w-full md:w-auto">
              <div class="relative flex-1 md:flex-none">
                <input
                  v-model="searchQuery"
                  @keypress.enter="performSearch"
                  type="text"
                  placeholder="Cari pasien atau kunjungan..."
                  class="w-full md:w-96 pl-5 pr-14 py-3 border border-[#4CAF93]/30 rounded-2xl focus:ring-2 focus:ring-[#4CAF93] focus:border-[#00796B] transition-all duration-200 bg-[#F2F6F7] focus:bg-white text-lg shadow text-[#1A2E35]"
                />
                <button
                  @click="performSearch"
                  class="absolute right-3 top-1/2 transform -translate-y-1/2 p-2 text-[#00796B] hover:text-[#4CAF93] transition-colors"
                >
                  <svg
                    class="w-6 h-6"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                    ></path>
                  </svg>
                </button>
              </div>
            </div>
          </div>
        </div>


        <!-- Search and Filter -->
        <div class="bg-[#F2F6F7] backdrop-blur-sm rounded-lg shadow-sm border border-[#4CAF93]/20 p-6 mb-8 mt-2">
          <div class="flex items-center gap-3 mb-6">
            <h3 class="text-xl font-bold text-[#1A2E35]">Filter</h3>
          </div>

          <!-- Filter Bar -->
          <div
            class="flex flex-wrap items-end gap-4 bg-transparent backdrop-blur-sm p-4 rounded-xl shadow-sm border border-gray-100"
          >
            <!-- Filter Penjamin -->
            <div class="flex-1 min-w-[200px]">
              <label for="penjamin" class="block text-sm font-medium text-[#1A2E35] mb-1">
                <svg
                  class="w-4 h-4 inline mr-1"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z"
                  />
                </svg>
                Filter Penjamin
              </label>
              <select
                id="penjamin"
                v-model="filterPenjamin"
                @change="applyFilters"
                class="w-full pl-4 pr-4 py-3 border border-[#4CAF93]/30 rounded-xl focus:ring-2 focus:ring-[#4CAF93] focus:border-[#00796B] transition-all duration-200 bg-white focus:bg-[#F2F6F7] text-[#1A2E35]"
              >
                <option value="">Semua Penjamin</option>
                <option
                  v-for="penjamin in uniquePenjamin"
                  :key="penjamin"
                  :value="penjamin"
                >
                  {{ penjamin }}
                </option>
              </select>
            </div>

            <!-- Filter Kunjungan -->
            <div class="flex-1 min-w-[200px]">
              <label for="kunjungan" class="block text-sm font-medium text-gray-700 mb-1">
                <svg
                  class="w-4 h-4 inline mr-1"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                  />
                </svg>
                Filter Kunjungan
              </label>
              <select
                id="kunjungan"
                v-model="filterKunjungan"
                @change="applyFilters"
                class="w-full pl-4 pr-4 py-3 border border-[#4CAF93]/30 rounded-xl focus:ring-2 focus:ring-[#4CAF93] focus:border-[#00796B] transition-all duration-200 bg-white focus:bg-[#F2F6F7] text-[#1A2E35]"
              >
                <option value="">Semua Kunjungan</option>
                <option
                  v-for="kunjungan in uniqueKunjungan"
                  :key="kunjungan"
                  :value="kunjungan"
                >
                  {{ kunjungan }}
                </option>
              </select>
            </div>

            <!-- Filter Perawatan -->
            <div class="flex-1 min-w-[200px]">
              <label for="perawatan" class="block text-sm font-medium text-gray-700 mb-1">
                <svg
                  class="w-4 h-4 inline mr-1"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"
                  />
                </svg>
                Filter Perawatan
              </label>
              <select
                id="perawatan"
                v-model="filterPerawatan"
                @change="applyFilters"
                class="w-full pl-4 pr-4 py-3 border border-[#4CAF93]/30 rounded-xl focus:ring-2 focus:ring-[#4CAF93] focus:border-[#00796B] transition-all duration-200 bg-white focus:bg-[#F2F6F7] text-[#1A2E35]"
              >
                <option value="">Semua Perawatan</option>
                <option
                  v-for="perawatan in uniquePerawatan"
                  :key="perawatan"
                  :value="perawatan"
                >
                  {{ perawatan }}
                </option>
              </select>
            </div>

            <!-- Filter Poli -->
            <div class="flex-1 min-w-[200px]">
              <label for="poli" class="block text-sm font-medium text-gray-700 mb-1">
                <svg
                  class="w-4 h-4 inline mr-1"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"
                  />
                </svg>
                Filter Poli
              </label>
              <select
                id="poli"
                v-model="filterPoli"
                @change="applyFilters"
                class="w-full pl-4 pr-4 py-3 border border-[#4CAF93]/30 rounded-xl focus:ring-2 focus:ring-[#4CAF93] focus:border-[#00796B] transition-all duration-200 bg-white focus:bg-[#F2F6F7] text-[#1A2E35]"
              >
                <option value="">Semua Poli</option>
                <option
                  v-for="poli in uniquePoli"
                  :key="poli"
                  :value="poli"
                >
                  {{ poli }}
                </option>
              </select>
            </div>

            <!-- Tombol Reset -->
            <div class="flex items-center">
              <button
                @click="resetFilters"
                class="px-6 py-3 bg-[#4CAF93]/20 text-[#1A2E35] rounded-xl hover:bg-[#4CAF93]/30 transition-all duration-200 font-medium border border-[#4CAF93]/30"
              >
                🔄 Reset
              </button>
            </div>
          </div>
        </div>

        <!-- Pasien List -->
        <div v-if="filteredPasien.length > 0" class="space-y-4">
          <div
            v-for="(p, idx) in filteredPasien"
            :key="p.id"
            class="bg-white rounded-lg border border-gray-200 shadow-sm hover:shadow-md transition-all duration-200 overflow-hidden"
          >
            <!-- Header Section -->
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-200 px-6 py-4">
              <div class="flex items-center justify-between gap-4 flex-wrap">
                <div class="flex items-center gap-4 flex-1 min-w-0">
                  <!-- Avatar -->
                  <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white shadow-sm flex-shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                  </div>

                  <!-- Name & Status -->
                  <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-3 flex-wrap">
                      <h3 class="text-lg font-semibold text-gray-900 truncate">{{ p.nm_p }}</h3>
                      <span
                        :class="[
                          'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                          p.status_kunjungan === 'completed' 

                        ]"
                      >
                        {{ getStatusLabel(p.status_kunjungan) }}
                      </span>
                    </div>

                  </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-2 flex-shrink-0">
                  <button
                    @click="router.visit(`/pasien/${p.psn_id}`)"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors duration-200 font-medium text-sm flex items-center justify-center gap-2 whitespace-nowrap"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5
                               c4.478 0 8.268 2.943 9.542 7
                               -1.274 4.057-5.064 7-9.542 7
                               -4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    Detail Pasien
                  </button>

                  <button
                    @click="router.visit(`/pasien/${p.psn_id}/kunjungan-with-transaksi/${p.id}/edit`)"
                    class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors duration-200 font-medium text-sm flex items-center justify-center gap-2 whitespace-nowrap"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5h2m-1 0v14m-7 0a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2.586a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 0012 2H8a2 2 0 00-2 2v14z"/>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15.232 5.232l3.536 3.536"/>
                    </svg>
                    TAMBAH / EDIT Kunjungan
                  </button>

                  <button
                    @click="openDeleteModal(p)"
                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors duration-200 font-medium text-sm flex items-center justify-center gap-2 whitespace-nowrap"
                    type="button"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862
                               a2 2 0 01-1.995-1.858L5 7
                               m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1
                               h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Hapus Kunjungan
                  </button>
                </div>
              </div>
            </div>

            <!-- Content Section -->
            <div class="px-6 py-4">
              <!-- Info Grid -->
              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="flex flex-col">
                  <p class="text-xs font-medium text-gray-500 mb-1">No Registrasi</p>
                  <p class="text-sm font-semibold text-gray-900 truncate">{{ p.no_reg }}</p>
                </div>
                <div class="flex flex-col">
                  <p class="text-xs font-medium text-gray-500 mb-1">MRN</p>
                  <p class="text-sm font-semibold text-gray-900 truncate">{{ p.mrn }}</p>
                </div>
                <div class="flex flex-col">
                  <p class="text-xs font-medium text-gray-500 mb-1">Kunjungan</p>
                  <p class="text-sm font-semibold text-gray-900 truncate">{{ p.kunjungan }}</p>
                </div>
                <div class="flex flex-col">
                  <p class="text-xs font-medium text-gray-500 mb-1">Penjamin</p>
                  <p class="text-sm font-semibold text-gray-900 truncate">{{ p.penjamin }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Modal Konfirmasi Hapus -->
        <transition name="fade">
          <div v-if="isDeleteModalOpen" class="fixed inset-0 bg-black bg-opacity-40 z-50 flex items-center justify-center">
            <div class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-lg mx-auto border border-gray-200 relative animate-fade-in">
              <button @click="closeDeleteModal" class="absolute right-4 top-4 text-gray-400 hover:text-gray-700 text-xl" title="Tutup" aria-label="Tutup">
                &times;
              </button>
              <div class="flex flex-col items-center text-center">
                <svg class="w-12 h-12 text-red-500 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862
                           a2 2 0 01-1.995-1.858L5 7
                           m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1
                           h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                <h2 class="text-lg font-bold text-red-600">Konfirmasi Hapus Kunjungan</h2>
                <div class="text-gray-700 mt-2 mb-6">
                  <div v-if="selectedPasienDelete">
                    <div class="mb-2">Apakah Anda yakin ingin menghapus kunjungan untuk pasien <span class="font-semibold">{{ selectedPasienDelete.nm_p }}</span>?</div>
                    <div class="text-sm text-gray-600 mb-2">
                      No Reg: <span class="font-mono">{{ selectedPasienDelete.no_reg }}</span><br>
                      Tanggal: <span class="font-mono">{{ formatDate(selectedPasienDelete.tgl_reg) }}</span>
                    </div>
                    <div class="text-xs text-gray-500">Tindakan ini tidak dapat dibatalkan!</div>
                  </div>
                </div>
                <div class="flex flex-row gap-3 w-full mt-2">
                  <button
                    @click="closeDeleteModal"
                    type="button"
                    class="flex-1 px-5 py-3 rounded-xl bg-gray-100 text-gray-700 hover:bg-gray-200 transition-all font-semibold"
                  >
                    Batal
                  </button>
                  <button
                    @click="deleteKunjungan(selectedPasienDelete?.id)"
                    :disabled="!selectedPasienDelete || !selectedPasienDelete.id"
                    type="button"
                    class="flex-1 px-5 py-3 rounded-xl bg-red-500 text-white hover:bg-red-600 transition-all font-semibold shadow disabled:opacity-50 disabled:cursor-not-allowed"
                  >
                    Ya, Hapus
                  </button>
                </div>
              </div>
            </div>
          </div>
        </transition>

        <!-- Pagination -->
        <div v-if="pasien?.links" class="flex justify-start mt-4">
          <div class="flex space-x-2 ">
            <template v-for="link in pasien.links" :key="link.label">
              <button
                v-if="link.url"
                @click="router.visit(link.url, { preserveState: true })"
                v-html="link.label"
                class="px-3 py-1 rounded-lg text-sm transition-all duration-200"
                :class="link.active
                  ? 'bg-[#00796B] text-white shadow-md'
                  : 'bg-white border border-[#4CAF93]/30 text-[#1A2E35] hover:bg-[#F2F6F7] hover:text-[#00796B]'"
              />
              <span
                v-else
                v-html="link.label"
                class="px-3 py-1 text-gray-400 text-sm"
              />
            </template>
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="text-center py-16">
          <div class="w-32 h-32 bg-gradient-to-br from-[#4CAF93]/20 to-[#00796B]/20 rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg">
            <svg class="w-16 h-16 text-[#00796B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
          </div>
          <h3 class="text-2xl font-bold text-[#1A2E35] mb-3">Tidak ada data pasien</h3>
          <p class="text-[#00796B] mb-8 text-lg">Mulai dengan menambahkan data pasien pertama</p>
          <button
            @click="router.visit('/pasien/create')"
            class="px-8 py-4 bg-gradient-to-r from-[#4CAF93] to-[#00796B] text-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-200 font-semibold inline-flex items-center gap-3 text-lg hover:from-[#00796B] hover:to-[#4CAF93]"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Tambah Pasien Baru
          </button>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<style scoped>
/* Slide-fade transition for flash messages */
.slide-fade-enter-active {
  transition: all 0.3s ease-out;
}
.slide-fade-leave-active {
  transition: all 0.3s ease-in;
}
.slide-fade-enter-from {
  transform: translateY(-10px);
  opacity: 0;
}
.slide-fade-leave-to {
  transform: translateY(-10px);
  opacity: 0;
}
</style>
