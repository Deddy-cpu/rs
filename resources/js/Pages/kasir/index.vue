<template>
  <AuthenticatedLayout>
    <Head title="Kasir - Data Kunjungan" />
    
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900">
            
            <!-- Header -->
            <div class="mb-6">
              <h1 class="text-2xl font-bold text-gray-900">Kasir - Data Kunjungan</h1>
              <p class="text-gray-600 mt-1">Kelola data kunjungan pasien untuk transaksi kasir</p>
            </div>

            <!-- Search and Filter -->
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 mb-6">
              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Cari Pasien</label>
                  <input 
                    v-model="searchQuery"
                    type="text" 
                    placeholder="Nama pasien, no reg, MRN..."
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                  />
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Penjamin</label>
                  <select 
                    v-model="filterPenjamin"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                  >
                    <option value="">Semua Penjamin</option>
                    <option v-for="penjamin in uniquePenjamin" :key="penjamin" :value="penjamin">
                      {{ penjamin }}
                    </option>
                  </select>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Perawatan</label>
                  <select 
                    v-model="filterPerawatan"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                  >
                    <option value="">Semua Perawatan</option>
                    <option v-for="perawatan in uniquePerawatan" :key="perawatan" :value="perawatan">
                      {{ perawatan }}
                    </option>
                  </select>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Kunjungan</label>
                  <select 
                    v-model="filterKunjungan"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                  >
                    <option value="">Semua Kunjungan</option>
                    <option v-for="kunjungan in uniqueKunjungan" :key="kunjungan" :value="kunjungan">
                      {{ kunjungan }}
                    </option>
                  </select>
                </div>
              </div>
            </div>

            <!-- Kunjungan List -->
            <div v-if="filteredKunjungan.length > 0" class="space-y-4">
              <div
                v-for="kunjungan in filteredKunjungan"
                :key="kunjungan.id"
                class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow"
              >
                <!-- Kunjungan Header -->
                <div class="flex justify-between items-start mb-4">
                  <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center text-xl text-blue-700">
                      <i class="fas fa-user"></i>
                    </div>
                    <div>
                      <h3 class="text-lg font-semibold text-gray-900">{{ kunjungan.nm_p }}</h3>
                      <div class="text-sm text-gray-600 mt-1">
                        <p><span class="font-medium">No Reg:</span> {{ kunjungan.no_reg }}</p>
                        <p><span class="font-medium">MRN:</span> {{ kunjungan.mrn }}</p>
                        <p><span class="font-medium">Tanggal:</span> {{ formatDate(kunjungan.tgl_reg) }}</p>
                      </div>
                    </div>
                  </div>
                  
                  <div class="flex items-center gap-3">
                    <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                      {{ kunjungan.penjamin }}
                    </span>
                    <button
                      @click="viewDetail(kunjungan)"
                      class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition flex items-center"
                    >
                      <i class="fas fa-eye mr-1"></i>
                      Detail
                    </button>
                  </div>
                </div>

                <!-- Kunjungan Details -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Jenis Perawatan</label>
                    <p class="mt-1 text-sm text-gray-900 bg-gray-50 px-3 py-2 border rounded-md">{{ kunjungan.perawatan || '-' }}</p>
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Jenis Kunjungan</label>
                    <p class="mt-1 text-sm text-gray-900 bg-gray-50 px-3 py-2 border rounded-md">{{ kunjungan.kunjungan || '-' }}</p>
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Alamat</label>
                    <p class="mt-1 text-sm text-gray-900 bg-gray-50 px-3 py-2 border rounded-md">{{ kunjungan.almt_B || '-' }}</p>
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700">ICD Code</label>
                    <p class="mt-1 text-sm text-gray-900 bg-gray-50 px-3 py-2 border rounded-md">{{ kunjungan.icd || '-' }}</p>
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700">No Invoice</label>
                    <p class="mt-1 text-sm text-gray-900 bg-gray-50 px-3 py-2 border rounded-md">{{ kunjungan.no_inv || '-' }}</p>
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Total Biaya</label>
                    <p class="mt-1 text-sm font-bold text-green-600 bg-gray-50 px-3 py-2 border rounded-md">
                      {{ formatCurrency(calculateTotalBiaya(kunjungan)) }}
                    </p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Empty State -->
            <div v-else class="text-center py-12">
              <div class="w-24 h-24 mx-auto mb-4 text-gray-400">
                <i class="fas fa-search text-6xl"></i>
              </div>
              <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada data kunjungan</h3>
              <p class="text-gray-500">Coba ubah filter pencarian atau tidak ada data yang sesuai dengan kriteria.</p>
            </div>

            <!-- Pagination -->
            <div v-if="kunjungan && kunjungan.data && kunjungan.data.length > 0" class="mt-6">
              <nav class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-3 sm:px-6">
                <div class="flex flex-1 justify-between sm:hidden">
                  <Link 
                    v-if="kunjungan.prev_page_url"
                    :href="kunjungan.prev_page_url"
                    class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                  >
                    Sebelumnya
                  </Link>
                  <Link 
                    v-if="kunjungan.next_page_url"
                    :href="kunjungan.next_page_url"
                    class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                  >
                    Selanjutnya
                  </Link>
                </div>
                <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                  <div>
                    <p class="text-sm text-gray-700">
                      Menampilkan
                      <span class="font-medium">{{ kunjungan.from }}</span>
                      sampai
                      <span class="font-medium">{{ kunjungan.to }}</span>
                      dari
                      <span class="font-medium">{{ kunjungan.total }}</span>
                      hasil
                    </p>
                  </div>
                  <div>
                    <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                      <Link 
                        v-if="kunjungan.prev_page_url"
                        :href="kunjungan.prev_page_url"
                        class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0"
                      >
                        <span class="sr-only">Sebelumnya</span>
                        <i class="fas fa-chevron-left h-5 w-5"></i>
                      </Link>
                      
                      <template v-for="(link, index) in kunjungan.links" :key="index">
                        <Link 
                          v-if="link.url"
                          :href="link.url"
                          :class="[
                            link.active 
                              ? 'relative z-10 inline-flex items-center bg-blue-600 px-4 py-2 text-sm font-semibold text-white focus:z-20 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600'
                              : 'relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0'
                          ]"
                          v-html="link.label"
                        />
                        <span 
                          v-else
                          class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-500 ring-1 ring-inset ring-gray-300 focus:outline-offset-0"
                          v-html="link.label"
                        />
                      </template>
                      
                      <Link 
                        v-if="kunjungan.next_page_url"
                        :href="kunjungan.next_page_url"
                        class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0"
                      >
                        <span class="sr-only">Selanjutnya</span>
                        <i class="fas fa-chevron-right h-5 w-5"></i>
                      </Link>
                    </nav>
                  </div>
                </div>
              </nav>
            </div>

          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { router, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head } from '@inertiajs/vue3'

const props = defineProps({
  kunjungan: {
    type: Object,
    default: () => ({ data: [] })
  },
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
  }
})

// Search and filter
const searchQuery = ref(props.filters.search || '')
const filterPenjamin = ref(props.filters.penjamin || '')
const filterPerawatan = ref(props.filters.perawatan || '')
const filterKunjungan = ref(props.filters.kunjungan || '')

// Computed properties
const filteredKunjungan = computed(() => {
  let filtered = props.kunjungan?.data || []
  
  // Search filter
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(kunjungan => 
      kunjungan.nm_p?.toLowerCase().includes(query) ||
      kunjungan.no_reg?.toLowerCase().includes(query) ||
      kunjungan.mrn?.toLowerCase().includes(query)
    )
  }
  
  // Penjamin filter
  if (filterPenjamin.value) {
    filtered = filtered.filter(kunjungan => kunjungan.penjamin === filterPenjamin.value)
  }
  
  // Perawatan filter
  if (filterPerawatan.value) {
    filtered = filtered.filter(kunjungan => kunjungan.perawatan === filterPerawatan.value)
  }
  
  // Kunjungan filter
  if (filterKunjungan.value) {
    filtered = filtered.filter(kunjungan => kunjungan.kunjungan === filterKunjungan.value)
  }
  
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
  
  // Hitung total konsultasi
  if (kunjungan.konsuls && kunjungan.konsuls.length > 0) {
    kunjungan.konsuls.forEach(konsul => {
      const jumlah = parseFloat(konsul.jmlh_kons) || 0
      const biaya = parseFloat(konsul.bya_kons) || 0
      const subtotal = jumlah * biaya
      
      // Handle diskon
      let diskon = 0
      if (konsul.disc_kons && konsul.disc_kons !== '0%') {
        const discPercent = parseFloat(String(konsul.disc_kons).replace(/[^\d]/g, '')) || 0
        diskon = (subtotal * discPercent) / 100
      }
      
      total += Math.max(subtotal - diskon, 0)
    })
  }
  
  // Hitung total tindakan
  if (kunjungan.tindaks && kunjungan.tindaks.length > 0) {
    kunjungan.tindaks.forEach(tindak => {
      const jumlah = parseFloat(tindak.jmlh_tindak) || 0
      const biaya = parseFloat(tindak.bya_tindak) || 0
      const subtotal = jumlah * biaya
      
      // Handle diskon
      let diskon = 0
      if (tindak.disc_tindak && tindak.disc_tindak !== '0%') {
        const discPercent = parseFloat(String(tindak.disc_tindak).replace(/[^\d]/g, '')) || 0
        diskon = (subtotal * discPercent) / 100
      }
      
      total += Math.max(subtotal - diskon, 0)
    })
  }
  
  // Hitung total alkes
  if (kunjungan.alkes && kunjungan.alkes.length > 0) {
    kunjungan.alkes.forEach(alkes => {
      const jumlah = parseFloat(alkes.jmlh_alkes) || 0
      const biaya = parseFloat(alkes.bya_alkes) || 0
      const subtotal = jumlah * biaya
      
      // Handle diskon
      let diskon = 0
      if (alkes.disc_alkes && alkes.disc_alkes !== '0%') {
        const discPercent = parseFloat(String(alkes.disc_alkes).replace(/[^\d]/g, '')) || 0
        diskon = (subtotal * discPercent) / 100
      }
      
      total += Math.max(subtotal - diskon, 0)
    })
  }
  
  // Hitung total resep
  if (kunjungan.rsp && kunjungan.rsp.length > 0) {
    kunjungan.rsp.forEach(rsp => {
      const jumlah = parseFloat(rsp.jmlh_rsp) || 0
      const biaya = parseFloat(rsp.bya_rsp) || 0
      const subtotal = jumlah * biaya
      
      // Handle diskon
      let diskon = 0
      if (rsp.disc_rsp && rsp.disc_rsp !== '0%') {
        const discPercent = parseFloat(String(rsp.disc_rsp).replace(/[^\d]/g, '')) || 0
        diskon = (subtotal * discPercent) / 100
      }
      
      total += Math.max(subtotal - diskon, 0)
    })
  }
  
  // Hitung total lainnya
  if (kunjungan.lainnyas && kunjungan.lainnyas.length > 0) {
    kunjungan.lainnyas.forEach(lainnya => {
      const jumlah = parseFloat(lainnya.jmlh_lainnaya) || 0
      const biaya = parseFloat(lainnya.bya_lainnya) || 0
      const subtotal = jumlah * biaya
      
      // Handle diskon
      let diskon = 0
      if (lainnya.disc_lainnya && lainnya.disc_lainnya !== '0%') {
        const discPercent = parseFloat(String(lainnya.disc_lainnya).replace(/[^\d]/g, '')) || 0
        diskon = (subtotal * discPercent) / 100
      }
      
      total += Math.max(subtotal - diskon, 0)
    })
  }
  
  return total
}

function viewDetail(kunjungan) {
  // Navigate to detail page using the correct route
  router.visit(`/kasir/kunjungan/${kunjungan.id}`)
}
</script>