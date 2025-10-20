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

// Modal confirm logic for deleting kunjungan
function confirmDelete(pasien) {
  openDeleteModal(pasien)
}

function deleteKunjungan(kunjunganId) {
  router.delete(route('kunjungan.destroy', kunjunganId), {
    onSuccess: () => {
      // Refresh the page to show updated data
      closeDeleteModal()
      router.reload()
    },
    onError: (errors) => {
      closeDeleteModal()
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
    @click="openDeleteModal(p)"
    class="w-full px-5 py-3 bg-red-500/80 hover:bg-red-500 text-white rounded-xl shadow-lg transition-all duration-200 font-semibold flex items-center justify-center gap-2"
    type="button"
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
            @click="deleteKunjungan(selectedPasienDelete.id)"
            :disabled="!selectedPasienDelete"
            type="button"
            class="flex-1 px-5 py-3 rounded-xl bg-red-500 text-white hover:bg-red-600 transition-all font-semibold shadow"
          >
            Ya, Hapus
          </button>
        </div>
      </div>
    </div>
  </div>
</transition>

            </div>
          </div>

            <!-- Kunjungan Info -->
           
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
