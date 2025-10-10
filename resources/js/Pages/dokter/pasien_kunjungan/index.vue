<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue"
import { Head, router } from "@inertiajs/vue3"
import { ref, computed, watch } from "vue"

const props = defineProps({
  pasien: Object,
  filters: {
    type: Object,
    default: () => ({})
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
  flash: {
    type: Object,
    default: () => ({})
  }
})

// Search and filter
const searchQuery = ref(props.filters.search || '')
const filterPenjamin = ref(props.filters.penjamin || '')
const filterPerawatan = ref(props.filters.perawatan || '')
const filterKunjungan = ref(props.filters.kunjungan || '')

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
    const jumlah = parseFloat(lainnya.jmlh_lainnaya) || 0
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
  
  router.get(route('dokter.pasien-kunjungan'), Object.fromEntries(params), {
    preserveState: true,
    replace: true
  })
}

function resetFilters() {
  searchQuery.value = ''
  filterPenjamin.value = ''
  filterPerawatan.value = ''
  filterKunjungan.value = ''
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

// Watch search and trigger debounced search
watch(searchQuery, () => {
  debouncedSearch()
})

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

function confirmDelete(pasien) {
  if (confirm(`Apakah Anda yakin ingin menghapus kunjungan untuk pasien ${pasien.nm_p}?\n\nNo Reg: ${pasien.no_reg}\nTanggal: ${formatDate(pasien.tgl_reg)}\n\nTindakan ini tidak dapat dibatalkan!`)) {
    deleteKunjungan(pasien.id)
  }
}

function deleteKunjungan(kunjunganId) {
  router.delete(route('kasir.destroy', kunjunganId), {
    onSuccess: () => {
      // Refresh the page to show updated data
      router.reload()
    },
    onError: (errors) => {
      console.error('Error deleting kunjungan:', errors)
      alert('Gagal menghapus kunjungan. Silakan coba lagi.')
    }
  })
}
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Pasien & Kunjungan - Dokter" />

    <div class="min-h-screen bg-cover bg-center p-6" style="background-image: url('/images/bg-login.png')">
      <div class="max-w-7xl mx-auto">
        
        <!-- Flash Messages -->
        <div v-if="flash.success" class="mb-6 bg-green-50 border border-green-200 text-green-700 px-6 py-4 rounded-2xl shadow-lg">
          <div class="flex items-center">
            <svg class="w-6 h-6 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            {{ flash.success }}
          </div>
        </div>
        
        <div v-if="flash.error" class="mb-6 bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-2xl shadow-lg">
          <div class="flex items-center">
            <svg class="w-6 h-6 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            {{ flash.error }}
          </div>
        </div>

        <!-- Header Section -->
        <div class="mb-8">
  <!-- Header Tengah -->
  <div class="text-center mb-6">
    <h1
      class="text-4xl md:text-5xl font-bold bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent mb-2"
    >
      <span class="inline-block align-middle mr-2"></span>
      Pasien & Kunjungan
    </h1>
    <p class="text-gray-600 text-lg">
      Kelola data pasien dan kunjungan medis dengan mudah
    </p>
  </div>

  <!-- Action Bar -->
  <div
    class="flex flex-col md:flex-row justify-between items-center gap-4 bg-transparent px-1 pt-0 pb-0"
  >
    <!-- Tombol Tambah -->
    <!-- <button
      @click="router.visit('/pasien/create')"
      class="w-full md:w-auto px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-2xl hover:from-green-700 hover:to-emerald-700 transition-all duration-200 font-semibold shadow-lg hover:shadow-xl flex items-center justify-center gap-2 text-lg"
    >
      <svg
        class="w-5 h-5"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M12 6v6m0 0v6m0-6h6m-6 0H6"
        ></path>
      </svg>
      Tambah Pasien Baru
    </button> -->

    <!-- Search -->
    <div class="flex items-center space-x-3 w-full md:w-auto">
      <div class="relative flex-1 md:flex-none">
        <input
          v-model="searchQuery"
          @keypress.enter="performSearch"
          type="text"
          placeholder="Cari pasien atau kunjungan..."
          class="w-full md:w-96 pl-5 pr-14 py-3 border border-green-200 rounded-2xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200 bg-green-50 focus:bg-white text-lg shadow"
        />
        <button
          @click="performSearch"
          class="absolute right-3 top-1/2 transform -translate-y-1/2 p-2 text-green-500 hover:text-green-700 transition-colors"
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
        <div class="bg-white/70 backdrop-blur-sm rounded-lg shadow-sm border border-gray-200 p-6 mb-8 mt-2">
          <div class="flex items-center gap-3 mb-6">
            
            <h3 class="text-xl font-bold text-gray-900">Filter</h3>
          </div>
          
          <!-- Filter Bar -->
<div
class="flex flex-wrap items-end gap-4 bg-transparent backdrop-blur-sm p-4 rounded-xl shadow-sm border border-gray-100"
>
  <!-- Filter Penjamin -->
  <div class="flex-1 min-w-[200px]">
    <label for="penjamin" class="block text-sm font-medium text-gray-700 mb-1">
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
      class="w-full pl-4 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200 bg-gray-50 focus:bg-white"
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
      class="w-full pl-4 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200 bg-gray-50 focus:bg-white"
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
      class="w-full pl-4 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200 bg-gray-50 focus:bg-white"
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

  <!-- Tombol Reset -->
  <div class="flex items-center">
    <button
      @click="resetFilters"
      class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition-all duration-200 font-medium"
    >
      🔄 Reset
    </button>
  </div>
</div>
        </div>

        <!-- Pasien List -->
        <div v-if="filteredPasien.length > 0" class="space-y-8">
          <div
            v-for="(p, idx) in filteredPasien"
            :key="p.id"
            class="bg-white shadow-xl rounded-2xl border border-gray-100 overflow-hidden hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1"
          >
            <!-- Pasien Header -->
            <div class="bg-gradient-to-r from-green-500 to-emerald-500 px-8 py-6 text-white">
              <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center gap-6">
                <div class="flex items-center gap-6">
                  <div class="w-16 h-16 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center text-2xl text-white shadow-lg">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                  </div>
                  <div>
                    <h3 class="text-2xl font-bold text-white mb-2">{{ p.nm_p }}</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm text-green-100">
                      <p><span class="font-semibold">No Reg:</span> {{ p.no_reg }}</p>
                      <p><span class="font-semibold">MRN:</span> {{ p.mrn }}</p>
                      <p><span class="font-semibold">Kunjungan:</span> {{ p.kunjungan }}</p>
                      <p><span class="font-semibold">Penjamin:</span> 
                        <span class="px-3 py-1 bg-white/20 text-white rounded-full text-xs font-semibold backdrop-blur-sm">
                          {{ p.penjamin }}
                        </span>
                      </p>
                    </div>
                  </div>
                </div>
              
               <!-- Action Buttons -->
<div class="flex flex-col items-end gap-3 w-full max-w-md ml-auto">
  <div class="flex justify-end gap-3 w-full">
    <!-- Detail Pasien -->
    <button 
      @click="router.visit(`/pasien/${p.psn_id}`)" 
      class="flex-1 px-5 py-3 bg-white/20 hover:bg-white/30 text-white rounded-xl shadow-lg transition-all duration-200 font-semibold flex items-center justify-center gap-2"
    >
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

    <!-- Edit Kunjungan -->
    <button
      @click="router.visit(`/pasien/${p.psn_id}/kunjungan-with-transaksi/${p.id}/edit`)"
      class="flex-1 px-5 py-3 bg-white/20 hover:bg-white/30 text-white rounded-xl shadow-lg transition-all duration-200 font-semibold flex items-center justify-center gap-2"
    >
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M11 5h2m-1 0v14m-7 0a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2.586a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 0012 2H8a2 2 0 00-2 2v14z"/>
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M15.232 5.232l3.536 3.536"/>
      </svg>
      TAMBAH / EDIT Kunjungan
    </button>
  </div>

  <!-- Hapus Kunjungan -->
  <button
    @click="confirmDelete(p)"
    class="w-full px-5 py-3 bg-red-500/80 hover:bg-red-500 text-white rounded-xl shadow-lg transition-all duration-200 font-semibold flex items-center justify-center gap-2"
  >
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

            <!-- Kunjungan Info -->
            <div class="p-8">
              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl p-6 shadow-lg border border-blue-100">
                  <div class="flex items-center gap-4">
                    <div class="p-3 bg-blue-500 rounded-xl shadow-lg">
                      <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                      </svg>
                    </div>
                    <div>
                      <p class="text-sm text-blue-600 font-semibold">Tanggal Kunjungan</p>
                      <p class="text-xl font-bold text-blue-800">{{ formatDate(p.tgl_reg) }}</p>
                    </div>
                  </div>
                </div>
                
                <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-2xl p-6 shadow-lg border border-green-100">
                  <div class="flex items-center gap-4">
                    <div class="p-3 bg-green-500 rounded-xl shadow-lg">
                      <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                      </svg>
                    </div>
                    <div>
                      <p class="text-sm text-green-600 font-semibold">Jenis Perawatan</p>
                      <p class="text-xl font-bold text-green-800">{{ p.perawatan }}</p>
                    </div>
                  </div>
                </div>
                
                <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-2xl p-6 shadow-lg border border-purple-100">
                  <div class="flex items-center gap-4">
                    <div class="p-3 bg-purple-500 rounded-xl shadow-lg">
                      <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                      </svg>
                    </div>
                    <div>
                      <p class="text-sm text-purple-600 font-semibold">No Invoice</p>
                      <p class="text-xl font-bold text-purple-800">{{ p.no_inv || '-' }}</p>
                    </div>
                  </div>
                </div>
                
                <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-2xl p-6 shadow-lg border border-orange-100">
                  <div class="flex items-center gap-4">
                    <div class="p-3 bg-orange-500 rounded-xl shadow-lg">
                      <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                      </svg>
                    </div>
                    <div>
                      <p class="text-sm text-orange-600 font-semibold">Total Biaya</p>
                      <p class="text-xl font-bold text-orange-800">{{ formatCurrency(calculateTotalBiaya(p)) }}</p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Detail Layanan -->
              <div class="space-y-6">
                <!-- Konsultasi -->
                <div v-if="p.konsuls && p.konsuls.length > 0" class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl p-6 shadow-lg border border-blue-100">
                  <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-3">
                      <div class="p-3 bg-blue-500 rounded-xl shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                        </svg>
                      </div>
                      <h4 class="text-xl font-bold text-blue-800">Konsultasi Medis</h4>
                    </div>
                    <div class="text-right">
                      <p class="text-sm text-blue-600 font-medium">Total Konsultasi</p>
                      <p class="text-lg font-bold text-blue-800">{{ formatCurrency(calculateKonsulTotal(p.konsuls)) }}</p>
                    </div>
                  </div>
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div v-for="konsul in p.konsuls" :key="konsul.id" class="bg-white rounded-xl p-4 shadow-sm border border-blue-100">
                      <p class="font-semibold text-gray-800 mb-2">{{ konsul.dokter }}</p>
                      <p class="text-sm text-gray-600 mb-2">{{ konsul.dskp_kons }}</p>
                      <div class="flex justify-between items-center">
                        <p class="text-xs text-gray-500">Jumlah: {{ konsul.jmlh_kons }} × {{ formatCurrency(konsul.bya_kons) }}</p>
                        <p class="text-sm text-blue-600 font-semibold bg-blue-50 px-3 py-1 rounded-lg">{{ formatCurrency((konsul.jmlh_kons || 0) * (parseFloat(String(konsul.bya_kons).replace(/[^\d]/g, '')) || 0)) }}</p>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Tindakan -->
                <div v-if="p.tindaks && p.tindaks.length > 0" class="bg-gradient-to-br from-green-50 to-green-100 rounded-2xl p-6 shadow-lg border border-green-100">
                  <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-3">
                      <div class="p-3 bg-green-500 rounded-xl shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                        </svg>
                      </div>
                      <h4 class="text-xl font-bold text-green-800">Tindakan Medis</h4>
                    </div>
                    <div class="text-right">
                      <p class="text-sm text-green-600 font-medium">Total Tindakan</p>
                      <p class="text-lg font-bold text-green-800">{{ formatCurrency(calculateTindakTotal(p.tindaks)) }}</p>
                    </div>
                  </div>
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div v-for="tindak in p.tindaks" :key="tindak.id" class="bg-white rounded-xl p-4 shadow-sm border border-green-100">
                      <p class="font-semibold text-gray-800 mb-2">{{ tindak.dktr_tindak }}</p>
                      <p class="text-sm text-gray-600 mb-2">{{ tindak.dskp_tindak }}</p>
                      <div class="flex justify-between items-center">
                        <p class="text-xs text-gray-500">Jumlah: {{ tindak.jmlh_tindak }} × {{ formatCurrency(tindak.bya_tindak) }}</p>
                        <p class="text-sm text-green-600 font-semibold bg-green-50 px-3 py-1 rounded-lg">{{ formatCurrency((tindak.jmlh_tindak || 0) * (parseFloat(String(tindak.bya_tindak).replace(/[^\d]/g, '')) || 0)) }}</p>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Resep -->
                <div v-if="p.rsp && p.rsp.length > 0" class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-2xl p-6 shadow-lg border border-purple-100">
                  <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-3">
                      <div class="p-3 bg-purple-500 rounded-xl shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                        </svg>
                      </div>
                      <h4 class="text-xl font-bold text-purple-800">Resep Obat</h4>
                    </div>
                    <div class="text-right">
                      <p class="text-sm text-purple-600 font-medium">Total Resep</p>
                      <p class="text-lg font-bold text-purple-800">{{ formatCurrency(calculateRspTotal(p.rsp)) }}</p>
                    </div>
                  </div>
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div v-for="rsp in p.rsp" :key="rsp.id" class="bg-white rounded-xl p-4 shadow-sm border border-purple-100">
                      <p class="font-semibold text-gray-800 mb-2">{{ rsp.dskp_rsp }}</p>
                      <div class="flex justify-between items-center">
                        <p class="text-xs text-gray-500">Jumlah: {{ rsp.jmlh_rsp }} × {{ formatCurrency(rsp.bya_rsp) }}</p>
                        <p class="text-sm text-purple-600 font-semibold bg-purple-50 px-3 py-1 rounded-lg">{{ formatCurrency((rsp.jmlh_rsp || 0) * (parseFloat(String(rsp.bya_rsp).replace(/[^\d]/g, '')) || 0)) }}</p>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Alkes -->
                <div v-if="p.alkes && p.alkes.length > 0" class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-2xl p-6 shadow-lg border border-orange-100">
                  <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-3">
                      <div class="p-3 bg-orange-500 rounded-xl shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                      </div>
                      <h4 class="text-xl font-bold text-orange-800">Alat Kesehatan</h4>
                    </div>
                    <div class="text-right">
                      <p class="text-sm text-orange-600 font-medium">Total Alkes</p>
                      <p class="text-lg font-bold text-orange-800">{{ formatCurrency(calculateAlkesTotal(p.alkes)) }}</p>
                    </div>
                  </div>
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div v-for="alkes in p.alkes" :key="alkes.id" class="bg-white rounded-xl p-4 shadow-sm border border-orange-100">
                      <p class="font-semibold text-gray-800 mb-2">{{ alkes.poli }}</p>
                      <p class="text-sm text-gray-600 mb-2">{{ alkes.dskp_alkes }}</p>
                      <div class="flex justify-between items-center">
                        <p class="text-xs text-gray-500">Jumlah: {{ alkes.jmlh_alkes }} × {{ formatCurrency(alkes.bya_alkes) }}</p>
                        <p class="text-sm text-orange-600 font-semibold bg-orange-50 px-3 py-1 rounded-lg">{{ formatCurrency((alkes.jmlh_alkes || 0) * (parseFloat(String(alkes.bya_alkes).replace(/[^\d]/g, '')) || 0)) }}</p>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Lainnya -->
                <div v-if="p.lainnyas && p.lainnyas.length > 0" class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-2xl p-6 shadow-lg border border-gray-100">
                  <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-3">
                      <div class="p-3 bg-gray-500 rounded-xl shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                        </svg>
                      </div>
                      <h4 class="text-xl font-bold text-gray-800">Layanan Lainnya</h4>
                    </div>
                    <div class="text-right">
                      <p class="text-sm text-gray-600 font-medium">Total Lainnya</p>
                      <p class="text-lg font-bold text-gray-800">{{ formatCurrency(calculateLainnyaTotal(p.lainnyas)) }}</p>
                    </div>
                  </div>
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div v-for="lainnya in p.lainnyas" :key="lainnya.id" class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                      <p class="font-semibold text-gray-800 mb-2">{{ lainnya.dskp_lainnya }}</p>
                      <div class="flex justify-between items-center">
                        <p class="text-xs text-gray-500">Jumlah: {{ lainnya.jmlh_lainnaya }} × {{ formatCurrency(lainnya.bya_lainnya) }}</p>
                        <p class="text-sm text-gray-600 font-semibold bg-gray-50 px-3 py-1 rounded-lg">{{ formatCurrency((lainnya.jmlh_lainnaya || 0) * (parseFloat(String(lainnya.bya_lainnya).replace(/[^\d]/g, '')) || 0)) }}</p>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- No Data Message -->
                <div v-if="!p.konsuls?.length && !p.tindaks?.length && !p.rsp?.length && !p.alkes?.length && !p.lainnyas?.length" class="text-center py-12 bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200">
                  <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                  </div>
                  <h3 class="text-lg font-semibold text-gray-600 mb-2">Belum ada layanan medis</h3>
                  <p class="text-gray-500">Belum ada layanan medis untuk kunjungan ini</p>
                </div>
              </div>
            </div>
          </div>

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
          ? 'bg-green-600 text-white shadow-md' 
          : 'bg-white border text-gray-700 hover:bg-gray-100 hover:text-green-600'"
      />
      <span
        v-else
        v-html="link.label"
        class="px-3 py-1 text-gray-400 text-sm"
      />
    </template>
  </div>
</div>

      </div>

        <!-- Empty State -->
        <div v-else class="text-center py-16">
          <div class="w-32 h-32 bg-gradient-to-br from-green-100 to-emerald-100 rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg">
            <svg class="w-16 h-16 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
          </div>
          <h3 class="text-2xl font-bold text-gray-700 mb-3">Tidak ada data pasien</h3>
          <p class="text-gray-500 mb-8 text-lg">Mulai dengan menambahkan data pasien pertama</p>
          <button
            @click="router.visit('/pasien/create')"
            class="px-8 py-4 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-200 font-semibold inline-flex items-center gap-3 text-lg"
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
