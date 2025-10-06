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
  flash: {
    type: Object,
    default: () => ({})
  }
})

// Service type definitions with icons and colors
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

// Filter state
const selectedService = ref(props.filters.kunjungan || 'umum')
const searchQuery = ref(props.filters.search || '')
const dateFilter = ref(props.filters.date || '')

// Computed properties
const filteredPasien = computed(() => {
  if (!props.pasien?.data) return []
  
  return props.pasien.data.filter(pasien => {
    const matchesService = pasien.kunjungan === selectedService.value
    const matchesSearch = !searchQuery.value || 
      pasien.nm_p.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      pasien.no_reg.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      pasien.mrn.toLowerCase().includes(searchQuery.value.toLowerCase())
    
    return matchesService && matchesSearch
  })
})

const currentServiceInfo = computed(() => {
  return serviceTypes[selectedService.value] || serviceTypes.umum
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
  
  if (pasien.konsuls) {
    pasien.konsuls.forEach(konsul => {
      total += parseFloat(String(konsul.st_kons).replace(/[^\d]/g, '')) || 0
    })
  }
  
  if (pasien.tindaks) {
    pasien.tindaks.forEach(tindak => {
      total += parseFloat(String(tindak.st_tindak).replace(/[^\d]/g, '')) || 0
    })
  }
  
  if (pasien.alkes) {
    pasien.alkes.forEach(alkes => {
      total += parseFloat(String(alkes.st_alkes).replace(/[^\d]/g, '')) || 0
    })
  }
  
  if (pasien.rsp) {
    pasien.rsp.forEach(rsp => {
      total += parseFloat(String(rsp.st_rsp).replace(/[^\d]/g, '')) || 0
    })
  }
  
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
  if (selectedService.value) params.append('kunjungan', selectedService.value)
  if (dateFilter.value) params.append('date', dateFilter.value)
  
  // Map service types to correct route names
  const routeMap = {
    'umum': 'dokter.poli_layanan.poli_umum',
    'gigi': 'dokter.poli_layanan.poli_gigi',
    'kia': 'dokter.poli_layanan.kia',
    'laboratorium': 'dokter.poli_layanan.laboratorium',
    'apotek': 'dokter.poli_layanan.apotek'
  }
  
  const routeName = routeMap[selectedService.value] || 'dokter.poli_layanan.poli_umum'
  router.get(route(routeName), Object.fromEntries(params), {
    preserveState: true,
    replace: true
  })
}

function clearFilters() {
  searchQuery.value = ''
  dateFilter.value = ''
  selectedService.value = 'umum'
  router.get(route('dokter.poli_layanan.poli_umum'))
}

function switchService(serviceType) {
  selectedService.value = serviceType
  applyFilters()
}

// Watch for service changes
watch(selectedService, () => {
  applyFilters()
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
</script>

<template>
  <AuthenticatedLayout>
    <Head :title="`${currentServiceInfo.name} - Poli Layanan`" />

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

      <!-- Service Type Navigation -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-6 flex items-center">
          <i class="fas fa-hospital mr-3 text-green-600"></i>
          Poli Layanan
        </h1>
        
        <!-- Service Type Tabs -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-2">
          <div class="flex flex-wrap gap-2">
            <button
              v-for="(service, key) in serviceTypes"
              :key="key"
              @click="switchService(key)"
              :class="[
                'px-4 py-3 rounded-lg font-medium transition-all duration-200 flex items-center',
                selectedService === key
                  ? `${service.bgColor} ${service.textColor} ${service.borderColor} border-2 shadow-md`
                  : 'bg-gray-50 text-gray-600 hover:bg-gray-100 border-2 border-transparent'
              ]"
            >
              <i :class="[service.icon, 'mr-2']"></i>
              {{ service.name }}
            </button>
          </div>
        </div>
      </div>

      <!-- Current Service Header -->
      <div :class="[currentServiceInfo.bgColor, 'rounded-lg p-6 mb-8 border', currentServiceInfo.borderColor]">
        <div class="flex items-center justify-between">
          <div class="flex items-center">
            <div :class="[`bg-${currentServiceInfo.color}-100`, 'w-16 h-16 rounded-full flex items-center justify-center text-2xl mr-4']">
              <i :class="[currentServiceInfo.icon, `text-${currentServiceInfo.color}-600`]"></i>
            </div>
            <div>
              <h2 :class="[currentServiceInfo.textColor, 'text-2xl font-bold']">
                {{ currentServiceInfo.name }}
              </h2>
              <p :class="[currentServiceInfo.textColor.replace('800', '600'), 'text-sm']">
                Kelola pasien untuk layanan {{ currentServiceInfo.name.toLowerCase() }}
              </p>
            </div>
          </div>
          <div class="text-right">
            <p :class="[currentServiceInfo.textColor, 'text-sm font-medium']">Total Pasien Hari Ini</p>
            <p :class="[currentServiceInfo.textColor, 'text-3xl font-bold']">{{ filteredPasien.length }}</p>
          </div>
        </div>
      </div>

      <!-- Search and Filter -->
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
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
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
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
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
            />
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
          Menampilkan {{ filteredPasien.length }} pasien untuk layanan {{ currentServiceInfo.name }}
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
          <div :class="[currentServiceInfo.bgColor, 'px-6 py-4 border-b border-gray-200']">
            <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center gap-4">
              <div class="flex items-center gap-4">
                <div :class="[`bg-${currentServiceInfo.color}-100`, 'w-12 h-12 rounded-full flex items-center justify-center text-xl']">
                  <i :class="[currentServiceInfo.icon, `text-${currentServiceInfo.color}-700`]"></i>
                </div>
                <div>
                  <h3 class="text-xl font-semibold text-gray-900">{{ p.nm_p }}</h3>
                  <div class="text-sm text-gray-600 mt-1">
                    <p><span class="font-medium">No Reg:</span> {{ p.no_reg }}</p>
                    <p><span class="font-medium">MRN:</span> {{ p.mrn }}</p>
                    <p><span class="font-medium">Penjamin:</span> 
                      <span :class="[`bg-${currentServiceInfo.color}-100`, `text-${currentServiceInfo.color}-800`, 'px-2 py-1 rounded-full text-xs font-medium']">
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

            <!-- Service-specific Information -->
            <div :class="[currentServiceInfo.bgColor, 'rounded-lg p-4 mb-4']">
              <h4 :class="[currentServiceInfo.textColor, 'text-lg font-semibold mb-3 flex items-center']">
                <i :class="[currentServiceInfo.icon, 'mr-2']"></i>
                Informasi {{ currentServiceInfo.name }}
              </h4>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <p class="text-sm font-medium text-gray-700">Jenis Kunjungan</p>
                  <p :class="[currentServiceInfo.textColor, 'font-semibold']">{{ p.kunjungan }}</p>
                </div>
                <div>
                  <p class="text-sm font-medium text-gray-700">ICD Code</p>
                  <p class="text-gray-600">{{ p.icd || '-' }}</p>
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
        <div :class="[currentServiceInfo.bgColor, 'w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-4']">
          <i :class="[currentServiceInfo.icon, `text-4xl ${currentServiceInfo.textColor}`]"></i>
        </div>
        <h3 class="text-lg font-medium text-gray-500 mb-2">Tidak ada pasien untuk {{ currentServiceInfo.name }}</h3>
        <p class="text-gray-400 mb-6">Belum ada pasien yang terdaftar untuk layanan ini</p>
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
