<template>
  <AuthenticatedLayout>
    <Head title="Poli Layanan" />

    <div class="min-h-screen bg-gray-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Header -->
        <div class="mb-8">
          <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
            🏥 Poli Layanan
          </h1>
          <p class="mt-2 text-gray-600">Kelola layanan poli dan kunjungan pasien</p>
        </div>

        <!-- Tabs Navigation -->
        <div class="bg-white shadow-sm rounded-lg border border-gray-200 mb-6">
          <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
              <button 
                type="button"
                @click.prevent="activeTab = 'polis'"
                :class="[
                  activeTab === 'polis' 
                    ? 'border-blue-500 text-blue-600' 
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                  'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm'
                ]"
              >
                📋 Daftar Poli
              </button>
              <button 
                type="button"
                @click.prevent="activeTab = 'kunjungan'"
                :class="[
                  activeTab === 'kunjungan' 
                    ? 'border-blue-500 text-blue-600' 
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                  'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm'
                ]"
              >
                👥 Kunjungan Pasien
                <span v-if="selectedPoli" class="ml-2 text-xs bg-blue-100 text-blue-600 px-2 py-1 rounded-full">
                  {{ selectedPoli }}
                </span>
              </button>
            </nav>
          </div>

          <div class="p-6">
            <!-- Tab 1: Daftar Poli -->
            <div v-show="activeTab === 'polis'">
              <div class="mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Pilih Poli</h2>
                <p class="text-gray-600">Klik pada poli untuk melihat daftar kunjungan pasien</p>
              </div>

              <!-- Poli Buttons Grid -->
              <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                <button
                  v-for="poli in polis"
                  :key="poli.id"
                  @click="selectPoli(poli)"
                  class="bg-white border border-gray-300 rounded-lg p-6 text-center hover:border-blue-500 hover:bg-blue-50 transition-all duration-200 group"
                >
                  <div class="text-2xl mb-3">🏥</div>
                  <h3 class="font-medium text-gray-900 group-hover:text-blue-600">
                    {{ poli.poli_desc }}
                  </h3>
                  <p class="text-sm text-gray-500 mt-1">
                    Status: {{ poli.aktif === 'Y' ? 'Aktif' : 'Non-aktif' }}
                  </p>
                </button>
              </div>

              <!-- Empty State -->
              <div v-if="polis.length === 0" class="text-center py-12">
                <div class="text-gray-400 text-6xl mb-4">🏥</div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada poli tersedia</h3>
                <p class="text-gray-500">Belum ada data poli yang tersedia.</p>
              </div>
            </div>

            <!-- Tab 2: Kunjungan Pasien -->
            <div v-show="activeTab === 'kunjungan'">
              <div v-if="!selectedPoli" class="text-center py-12">
                <div class="text-gray-400 text-6xl mb-4">👈</div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Pilih Poli Terlebih Dahulu</h3>
                <p class="text-gray-500">Silakan pilih poli dari tab pertama untuk melihat daftar kunjungan.</p>
                <button 
                  @click="activeTab = 'polis'"
                  class="mt-4 bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600"
                >
                  Kembali ke Daftar Poli
                </button>
              </div>

              <div v-else>
                <!-- Header with Poli Info -->
                <div class="mb-6 flex items-center justify-between">
                  <div>
                    <h2 class="text-xl font-semibold text-gray-900">Kunjungan Pasien</h2>
                    <p class="text-gray-600">Poli: <span class="font-medium text-blue-600">{{ selectedPoli }}</span></p>
                    <p class="text-sm text-gray-500 mt-1">💡 Klik pada nama pasien untuk edit/tambah transaksi</p>
                  </div>
                  <button 
                    @click="activeTab = 'polis'"
                    class="bg-gray-100 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-200"
                  >
                    🔙 Ganti Poli
                  </button>
                </div>

                <!-- Loading State -->
                <div v-if="loadingKunjungan" class="text-center py-8">
                  <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
                  <p class="mt-2 text-gray-500">Memuat data kunjungan...</p>
                </div>

                <!-- Kunjungan List -->
                <div v-else-if="filteredKunjungan.length > 0">
                  <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">
                      Daftar Pasien Kunjungan {{ selectedPoli }}
                    </h3>
                    
                    <!-- Simple List Format -->
                    <div class="space-y-3">
                      <div
                        v-for="(kunjungan, index) in filteredKunjungan"
                        :key="kunjungan.id"
                        @click="editKunjungan(kunjungan)"
                        class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200 hover:shadow-md hover:bg-blue-50 hover:border-blue-300 cursor-pointer transition-all duration-200 group"
                      >
                        <div class="flex items-center space-x-4">
                          <span class="text-lg font-semibold text-gray-900 min-w-[40px]">
                            {{ index + 1 }}.
                          </span>
                          <div class="flex-1">
                            <h4 class="text-lg font-medium text-gray-900 group-hover:text-blue-700">
                              {{ getPatientName(kunjungan) }}
                            </h4>
                            <div class="text-sm text-gray-600 mt-1 space-y-1">
                              <div v-if="kunjungan.no_reg">
                                <span class="font-medium">No. Reg:</span> {{ kunjungan.no_reg }}
                              </div>
                              <div v-if="kunjungan.mrn">
                                <span class="font-medium">MRN:</span> {{ kunjungan.mrn }}
                              </div>
                              <div v-if="kunjungan.tgl_reg">
                                <span class="font-medium">Tanggal:</span> {{ formatDate(kunjungan.tgl_reg) }}
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="flex-shrink-0 ml-4 flex items-center space-x-2">
                          <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 group-hover:bg-blue-200">
                            Kunjungan
                          </span>
                          <svg class="w-5 h-5 text-gray-400 group-hover:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                          </svg>
                        </div>
                      </div>
                    </div>
                    
                    <!-- Summary -->
                    <div class="mt-6 pt-4 border-t border-gray-200">
                      <p class="text-sm text-gray-600">
                        Total: <span class="font-semibold">{{ filteredKunjungan.length }}</span> pasien
                      </p>
                    </div>
                  </div>
                </div>

                <!-- Empty State for Kunjungan -->
                <div v-else class="text-center py-12">
                  <div class="text-gray-400 text-6xl mb-4">📋</div>
                  <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada kunjungan</h3>
                  <p class="text-gray-500">Belum ada kunjungan pasien untuk poli <strong>{{ selectedPoli }}</strong>.</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
  polis: {
    type: Array,
    default: () => []
  },
  kunjungan: {
    type: Object,
    default: () => ({ data: [] })
  },
  filters: {
    type: Object,
    default: () => ({})
  }
})

// Reactive state
const activeTab = ref('polis')
const selectedPoli = ref('')
const loadingKunjungan = ref(false)

// Computed properties
const filteredKunjungan = computed(() => {
  // Only show data if a poli is selected
  if (!selectedPoli.value) {
    return []
  }
  
  // Get kunjungan data
  const kunjunganData = props.kunjungan?.data || []
  
  // If no data available, return empty array
  if (!kunjunganData || kunjunganData.length === 0) {
    return []
  }
  
  // Check if the server has filtered the data for us
  if (props.filters.polis && props.filters.polis === selectedPoli.value) {
    // Server has already filtered the data for this poli
    return kunjunganData
  }
  
  // Server didn't filter or we need client-side filtering
  // Filter based on the kunjungan type or penjamin
  const filtered = kunjunganData.filter(kunjungan => {
    const matchesKunjunganType = kunjungan.kunjungan === selectedPoli.value
    const matchesPenjamin = kunjungan.penjamin === selectedPoli.value
    return matchesKunjunganType || matchesPenjamin
  })
  
  return filtered
})

// Initialize selectedPoli from filters if it exists
onMounted(() => {
  if (props.filters.polis) {
    selectedPoli.value = props.filters.polis
    activeTab.value = 'kunjungan'
  }
})

// Methods
const selectPoli = (poli) => {
  selectedPoli.value = poli.poli_desc
  activeTab.value = 'kunjungan'
  loadKunjunganForPoli(poli.poli_desc)
}

const loadKunjunganForPoli = (poliName) => {
  loadingKunjungan.value = true
  
  // Use Inertia to fetch kunjungan data for the selected poli
  router.get(route('dokter.poli_layanan'), {
    polis: poliName
  }, {
    preserveState: false, // We want to get fresh data
    preserveScroll: false,
    replace: false, // Don't replace to maintain history
    onSuccess: () => {
      loadingKunjungan.value = false
    },
    onError: () => {
      loadingKunjungan.value = false
    },
    onFinish: () => {
      loadingKunjungan.value = false
    }
  })
}

const formatDate = (dateString) => {
  if (!dateString) return ''
  
  try {
    const date = new Date(dateString)
    return date.toLocaleDateString('id-ID', {
      day: '2-digit',
      month: '2-digit',
      year: 'numeric'
    })
  } catch (error) {
    return dateString
  }
}

const getPatientName = (kunjungan) => {
  // Try different ways to get the patient name
  if (kunjungan.nm_p && kunjungan.nm_p.trim()) {
    return kunjungan.nm_p.trim()
  }
  if (kunjungan.psn?.nm_p && kunjungan.psn.nm_p.trim()) {
    return kunjungan.psn.nm_p.trim()
  }
  if (kunjungan.pasien?.nm_p && kunjungan.pasien.nm_p.trim()) {
    return kunjungan.pasien.nm_p.trim()
  }
  return 'Nama tidak tersedia'
}

const editKunjungan = (kunjungan) => {
  // Get the patient ID from kunjungan data - try multiple sources
  const psnId = kunjungan.psn_id || kunjungan.psn?.id || kunjungan.pasien?.id
  
  if (!psnId) {
    console.error('Patient ID not found for kunjungan:', kunjungan)
    alert('ID pasien tidak ditemukan. Silakan coba lagi.')
    return
  }
  
  if (!kunjungan.id) {
    console.error('Kunjungan ID not found:', kunjungan)
    alert('ID kunjungan tidak ditemukan. Silakan coba lagi.')
    return
  }
  
  // Navigate to the edit page using the same route pattern as in pasien kunjungan index
  // This route will handle both edit and add transaction functionality
  const editUrl = `/pasien/${psnId}/kunjungan-with-transaksi/${kunjungan.id}/edit`
  console.log('Navigating to:', editUrl)
  
  router.visit(editUrl)
}

// Watch for tab changes to reset selected poli if needed
watch(activeTab, (newTab) => {
  if (newTab === 'polis' && selectedPoli.value) {
    // Keep selected poli when switching back to polis tab
  }
})

// Watch for changes in kunjungan data
watch(() => props.kunjungan, (newKunjungan) => {
  // Data has changed, component will react automatically
}, { deep: true })

// Watch for changes in filters  
watch(() => props.filters, (newFilters) => {
  if (newFilters.polis && newFilters.polis !== selectedPoli.value) {
    selectedPoli.value = newFilters.polis
  }
}, { deep: true })
</script>
