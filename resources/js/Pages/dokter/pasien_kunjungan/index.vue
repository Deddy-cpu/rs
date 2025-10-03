<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue"
import { Head, router } from "@inertiajs/vue3"
import { ref, computed } from "vue"

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

function calculateTotalBiaya(pasien) {
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

function clearFilters() {
  searchQuery.value = ''
  filterPenjamin.value = ''
  filterPerawatan.value = ''
  router.get(route('dokter.pasien-kunjungan'))
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
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Pasien & Kunjungan - Dokter" />

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

      <!-- Header -->
      <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center mb-8 gap-4">
        <div>
          <h1 class="text-3xl font-bold text-gray-900 flex items-center">
            <i class="fas fa-user-md mr-3 text-green-600"></i>
            Pasien & Kunjungan
          </h1>
          <p class="text-gray-600 mt-1">Kelola data pasien dan kunjungan medis</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-3">
          <button
            @click="router.visit('/pasien/create')"
            class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg shadow transition flex items-center justify-center"
          >
            <i class="fas fa-plus mr-2"></i>
            Tambah Pasien Baru
          </button>
        </div>
      </div>

      <!-- Search and Filter -->
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
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
              placeholder="Cari berdasarkan nama, NIK, atau MRN..."
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
            />
          </div>
          
          <!-- Filter Penjamin -->
          <div>
            <label for="penjamin" class="block text-sm font-medium text-gray-700 mb-2">
              <i class="fas fa-filter mr-1"></i>
              Filter Penjamin
            </label>
            <select
              id="penjamin"
              v-model="filterPenjamin"
              @change="applyFilters"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
            >
              <option value="">Semua Penjamin</option>
              <option v-for="penjamin in uniquePenjamin" :key="penjamin" :value="penjamin">
                {{ penjamin }}
              </option>
            </select>
          </div>


          
          <!-- Filter Kunjungan -->
          <div>
            <label for="kunjungan" class="block text-sm font-medium text-gray-700 mb-2">
              <i class="fas fa-filter mr-1"></i>
              Filter Kunjungan
            </label>
          
          <select
              id="kunjungan"
              v-model="filterKunjungan"
              @change="applyFilters"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
            >
              <option value="">Semua Kunjungan</option>
              <option v-for="kunjungan in uniqueKunjungan" :key="kunjungan" :value="kunjungan">
                {{ kunjungan }}
              </option>
            </select>
          </div>
          
          <!-- Filter Perawatan -->
           <div>
            <label for="perawatan" class="block text-sm font-medium text-gray-700 mb-2">
              <i class="fas fa-hospital mr-1"></i>
              Filter Perawatan
            </label>
            <select
              id="perawatan"
              v-model="filterPerawatan"
              @change="applyFilters"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
            >
              <option value="">Semua Perawatan</option>
              <option v-for="perawatan in uniquePerawatan" :key="perawatan" :value="perawatan">
                {{ perawatan }}
              </option>
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
                @click="applyFilters"
                class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm transition flex items-center"
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
          </div>
        </div>
        
        <!-- Results count -->
        <div class="mt-4 text-sm text-gray-600">
          Menampilkan {{ filteredPasien.length }} dari {{ pasien?.total || 0 }} pasien
        </div>
      </div>

      <!-- Pasien List -->
      <div v-if="filteredPasien.length > 0" class="space-y-6">
        <div
          v-for="(p, idx) in filteredPasien"
          :key="p.id"
          class="bg-white shadow-lg rounded-xl border border-gray-200 overflow-hidden hover:shadow-xl transition-shadow"
        >
          <!-- Pasien Header -->
          <div class="bg-gradient-to-r from-green-50 to-emerald-50 px-6 py-4 border-b border-gray-200">
            <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center gap-4">
              <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center text-xl text-green-700">
                  <i class="fas fa-user"></i>
                </div>
                <div>
                  <h3 class="text-xl font-semibold text-gray-900">{{ p.nm_p }}</h3>
                  <div class="text-sm text-gray-600 mt-1">
                    <p><span class="font-medium">No Reg:</span> {{ p.no_reg }}</p>
                    <p><span class="font-medium">MRN:</span> {{ p.mrn }}</p>
                    <p><span class="font-medium">Kunjungan:</span> {{ p.kunjungan }}</p>
                    <p><span class="font-medium">Penjamin:</span> 
                      <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">
                        {{ p.penjamin }}
                      </span>
                    </p>
                  </div>
                </div>
              </div>
              
              <!-- Action Buttons -->
              <div class="flex flex-wrap gap-2">
                <button 
                  @click="router.visit(`/pasien/${p.psn_id}`)" 
                  class="px-4 py-2 text-sm bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow transition flex items-center"
                >
                  <i class="fas fa-eye mr-1"></i>
                  Detail Pasien
                </button>
                <button
                  @click="router.visit(`/kasir/${p.id}`)"
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
                    <p class="text-lg font-semibold text-blue-800">{{ formatDate(p.tgl_reg) }}</p>
                  </div>
                </div>
              </div>
              
              <div class="bg-green-50 rounded-lg p-4">
                <div class="flex items-center">
                  <i class="fas fa-hospital text-green-600 text-xl mr-3"></i>
                  <div>
                    <p class="text-sm text-green-600 font-medium">Jenis Perawatan</p>
                    <p class="text-lg font-semibold text-green-800">{{ p.perawatan }}</p>
                  </div>
                </div>
              </div>
              
              <div class="bg-purple-50 rounded-lg p-4">
                <div class="flex items-center">
                  <i class="fas fa-file-invoice text-purple-600 text-xl mr-3"></i>
                  <div>
                    <p class="text-sm text-purple-600 font-medium">No Invoice</p>
                    <p class="text-lg font-semibold text-purple-800">{{ p.no_inv || '-' }}</p>
                  </div>
                </div>
              </div>
              
              <div class="bg-orange-50 rounded-lg p-4">
                <div class="flex items-center">
                  <i class="fas fa-calculator text-orange-600 text-xl mr-3"></i>
                  <div>
                    <p class="text-sm text-orange-600 font-medium">Total Biaya</p>
                    <p class="text-lg font-semibold text-orange-800">{{ formatCurrency(calculateTotalBiaya(p)) }}</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Detail Layanan -->
            <div class="space-y-4">
              <!-- Konsultasi -->
              <div v-if="p.konsuls && p.konsuls.length > 0" class="bg-blue-50 rounded-lg p-4">
                <h4 class="text-lg font-semibold text-blue-800 mb-3 flex items-center">
                  <i class="fas fa-stethoscope mr-2"></i>
                  Konsultasi Medis
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                  <div v-for="konsul in p.konsuls" :key="konsul.id" class="bg-white rounded-lg p-3">
                    <p class="font-medium text-gray-800">{{ konsul.dokter }}</p>
                    <p class="text-sm text-gray-600">{{ konsul.dskp_kons }}</p>
                    <p class="text-sm text-blue-600 font-medium">{{ konsul.st_kons }}</p>
                  </div>
                </div>
              </div>

              <!-- Tindakan -->
              <div v-if="p.tindaks && p.tindaks.length > 0" class="bg-green-50 rounded-lg p-4">
                <h4 class="text-lg font-semibold text-green-800 mb-3 flex items-center">
                  <i class="fas fa-procedures mr-2"></i>
                  Tindakan Medis
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                  <div v-for="tindak in p.tindaks" :key="tindak.id" class="bg-white rounded-lg p-3">
                    <p class="font-medium text-gray-800">{{ tindak.dktr_tindak }}</p>
                    <p class="text-sm text-gray-600">{{ tindak.dskp_tindak }}</p>
                    <p class="text-sm text-green-600 font-medium">{{ tindak.st_tindak }}</p>
                  </div>
                </div>
              </div>

              <!-- Resep -->
              <div v-if="p.rsp && p.rsp.length > 0" class="bg-purple-50 rounded-lg p-4">
                <h4 class="text-lg font-semibold text-purple-800 mb-3 flex items-center">
                  <i class="fas fa-pills mr-2"></i>
                  Resep Obat
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                  <div v-for="rsp in p.rsp" :key="rsp.id" class="bg-white rounded-lg p-3">
                    <p class="font-medium text-gray-800">{{ rsp.dskp_rsp }}</p>
                    <p class="text-sm text-gray-600">Jumlah: {{ rsp.jmlh_rsp }}</p>
                    <p class="text-sm text-purple-600 font-medium">{{ rsp.st_rsp }}</p>
                  </div>
                </div>
              </div>

              <!-- Alkes -->
              <div v-if="p.alkes && p.alkes.length > 0" class="bg-orange-50 rounded-lg p-4">
                <h4 class="text-lg font-semibold text-orange-800 mb-3 flex items-center">
                  <i class="fas fa-tools mr-2"></i>
                  Alat Kesehatan
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                  <div v-for="alkes in p.alkes" :key="alkes.id" class="bg-white rounded-lg p-3">
                    <p class="font-medium text-gray-800">{{ alkes.poli }}</p>
                    <p class="text-sm text-gray-600">{{ alkes.dskp_alkes }}</p>
                    <p class="text-sm text-orange-600 font-medium">{{ alkes.st_alkes }}</p>
                  </div>
                </div>
              </div>

              <!-- Lainnya -->
              <div v-if="p.lainnyas && p.lainnyas.length > 0" class="bg-gray-50 rounded-lg p-4">
                <h4 class="text-lg font-semibold text-gray-800 mb-3 flex items-center">
                  <i class="fas fa-list mr-2"></i>
                  Layanan Lainnya
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                  <div v-for="lainnya in p.lainnyas" :key="lainnya.id" class="bg-white rounded-lg p-3">
                    <p class="font-medium text-gray-800">{{ lainnya.dskp_lainnya }}</p>
                    <p class="text-sm text-gray-600">Jumlah: {{ lainnya.jmlh_lainnaya }}</p>
                    <p class="text-sm text-gray-600 font-medium">{{ lainnya.st_lainnya }}</p>
                  </div>
                </div>
              </div>

              <!-- No Data Message -->
              <div v-if="!p.konsuls?.length && !p.tindaks?.length && !p.rsp?.length && !p.alkes?.length && !p.lainnyas?.length" class="text-center py-8">
                <i class="fas fa-info-circle text-4xl text-gray-300 mb-3"></i>
                <p class="text-gray-500 font-medium">Belum ada layanan medis untuk kunjungan ini</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Pagination -->
        <div v-if="pasien?.links" class="flex justify-center mt-8">
          <nav class="flex space-x-2">
            <template v-for="link in pasien.links" :key="link.label">
              <button
                v-if="link.url"
                @click="router.get(link.url)"
                v-html="link.label"
                :class="[
                  'px-3 py-2 text-sm border rounded-lg transition',
                  link.active 
                    ? 'bg-green-600 text-white border-green-600' 
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

      <!-- Empty State -->
      <div v-else class="text-center py-12">
        <i class="fas fa-search text-6xl text-gray-300 mb-4"></i>
        <h3 class="text-lg font-medium text-gray-500 mb-2">Tidak ada data pasien</h3>
        <p class="text-gray-400 mb-6">Mulai dengan menambahkan data pasien pertama</p>
        <button
          @click="router.visit('/pasien/create')"
          class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg shadow transition inline-flex items-center"
        >
          <i class="fas fa-plus mr-2"></i>
          Tambah Pasien Baru
        </button>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
