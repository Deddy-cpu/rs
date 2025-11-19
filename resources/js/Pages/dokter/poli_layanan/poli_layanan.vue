<template>
  <AuthenticatedLayout>
    <Head title="Poli Layanan" />

        <div class="min-h-screen bg-cover bg-center p-6" style="background-image: url('/images/bg-login.png')">

      <!-- Subtle Background Pattern -->
      <div class="absolute inset-0 opacity-10 bg-[url('/images/bg-login.png')] bg-cover bg-center"></div>

      <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        <!-- Header -->
        <div class="mb-10 text-center animate-fade-in">
          <h1
            class="text-4xl font-extrabold text-gray-800 flex items-center justify-center gap-3"
          >
            🏥 Poli Layanan
          </h1>
          <p class="mt-3 text-gray-600 text-lg">
            Kelola layanan poli dan kunjungan pasien dengan mudah
          </p>
        </div>

        <!-- Tabs Navigation -->
        <div
          class="bg-white/80 backdrop-blur-sm shadow-lg rounded-xl border border-gray-200 mb-6 overflow-hidden"
        >
          <div class="border-b border-gray-200 bg-gradient-to-r from-blue-50 to-indigo-100">
            <nav class="flex justify-center space-x-10 py-4" aria-label="Tabs">
              <button
                type="button"
                @click.prevent="activeTab = 'polis'"
                :class="[
                  activeTab === 'polis'
                    ? 'border-b-4 border-blue-600 text-blue-700 font-semibold'
                    : 'text-gray-500 hover:text-blue-600 hover:border-blue-300',
                  'transition-all duration-200 text-base pb-2'
                ]"
              >
                📋 Daftar Poli
              </button>
              <button
                type="button"
                @click.prevent="activeTab = 'kunjungan'"
                :class="[
                  activeTab === 'kunjungan'
                    ? 'border-b-4 border-blue-600 text-blue-700 font-semibold'
                    : 'text-gray-500 hover:text-blue-600 hover:border-blue-300',
                  'transition-all duration-200 text-base pb-2'
                ]"
              >
                👥 Kunjungan Pasien
                <span
                  v-if="selectedPoli"
                  class="ml-2 text-xs bg-blue-100 text-blue-600 px-2 py-1 rounded-full shadow-sm"
                >
                  {{ selectedPoli }}
                </span>
              </button>
            </nav>
          </div>

          <div class="p-8 transition-all duration-500">
            <!-- TAB 1: DAFTAR POLI -->
            <transition name="fade" mode="out-in">
              <div v-if="activeTab === 'polis'" key="polis">
                <div class="mb-6 text-center">
                  <h2 class="text-2xl font-bold text-gray-800 mb-2">Pilih Poli</h2>
                  <p class="text-gray-600">
                    Klik pada poli untuk melihat daftar kunjungan pasien
                  </p>
                </div>

                <!-- Search -->
                <div class="mb-6 flex justify-center gap-3">
                  <input
                    id="searchPoli"
                    ref="searchInput"
                    v-model="search"
                    @keypress.enter.prevent="applySearch"
                    type="search"
                    placeholder="🔍 Cari poli (mis. Gigi, Anak, Umum)"
                    class="flex-1 sm:w-1/2 border border-gray-300 rounded-md px-4 py-2 shadow-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-400 transition-all"
                  />
                  <button
                    v-if="search"
                    @click="search = ''"
                    class="bg-gray-100 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-200 transition"
                  >
                    Reset
                  </button>
                </div>

                <!-- Poli Buttons Grid -->
                <div
                  class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6"
                >
                  <button
                    v-for="poli in filteredPolis"
                    :key="poli.id"
                    @click="selectPoli(poli)"
                    class="bg-gradient-to-br from-white to-blue-50 border border-gray-200 rounded-xl p-6 text-center hover:shadow-lg hover:-translate-y-1 hover:border-blue-500 transition-all duration-300 group"
                  >
                    <div class="text-3xl mb-3 animate-pulse-slow">🏥</div>
                    <h3
                      class="font-semibold text-gray-800 text-lg group-hover:text-blue-700"
                    >
                      {{ poli.poli_desc }}
                    </h3>
                    <p class="text-sm text-gray-500 mt-1">
                      Status:
                      <span
                        :class="poli.aktif === 'Y' ? 'text-green-600' : 'text-red-500'"
                      >
                        {{ poli.aktif === 'Y' ? 'Aktif' : 'Non-aktif' }}
                      </span>
                    </p>
                  </button>
                </div>

                <!-- Empty States -->
                <div
                  v-if="(props.polis || []).length === 0"
                  class="text-center py-12"
                >
                  <div class="text-gray-400 text-6xl mb-4">🏥</div>
                  <h3 class="text-lg font-medium text-gray-900 mb-2">
                    Tidak ada poli tersedia
                  </h3>
                  <p class="text-gray-500">Belum ada data poli yang tersedia.</p>
                </div>

                <div v-else-if="filteredPolis.length === 0" class="text-center py-12">
                  <div class="text-gray-400 text-6xl mb-4">🔎</div>
                  <h3 class="text-lg font-medium text-gray-900 mb-2">
                    Tidak ditemukan
                  </h3>
                  <p class="text-gray-500">
                    Tidak ada poli yang cocok dengan pencarian
                    <strong>"{{ search }}"</strong>.
                  </p>
                </div>
              </div>

              <!-- TAB 2: KUNJUNGAN -->
              <div v-else key="kunjungan" class="animate-fade-in">
                <div v-if="!selectedPoli" class="text-center py-12">
                  <div class="text-gray-400 text-6xl mb-4 animate-bounce">👈</div>
                  <h3 class="text-lg font-medium text-gray-900 mb-2">
                    Pilih Poli Terlebih Dahulu
                  </h3>
                  <p class="text-gray-500">
                    Silakan pilih poli dari tab pertama untuk melihat daftar kunjungan.
                  </p>
                  <button
                    @click="activeTab = 'polis'"
                    class="mt-6 bg-blue-600 text-white px-5 py-2 rounded-md hover:bg-blue-700 transition"
                  >
                    Kembali ke Daftar Poli
                  </button>
                </div>

                <div v-else>
                  <!-- Header with Poli Info -->
                  <div class="mb-10">
  <!-- Judul Tengah -->
  <div class="text-center mb-6">
    <h2 class="text-3xl font-bold text-gray-800">Kunjungan Pasien</h2>
    <p class="text-gray-600 mt-1">
      Poli: <span class="font-semibold text-blue-700">{{ selectedPoli }}</span>
    </p>
    <p class="text-sm text-gray-500 mt-1">
      💡 Klik pada nama pasien untuk edit/tambah transaksi
    </p>
  </div>

  <!-- Baris Tombol di Bawah -->
  <div class="flex flex-col gap-4">
    <!-- Baris Pertama: Filter Tanggal dan Tombol Ganti Poli -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <!-- Kiri: Filter Tanggal -->
      <div class="flex justify-center sm:justify-start items-center gap-3">
        <label for="dateFilter" class="text-sm font-medium text-gray-700 whitespace-nowrap">
          📅 Filter Tanggal:
        </label>
        <input
          id="dateFilter"
          v-model="selectedDate"
          @change="handleDateChange"
          type="date"
          class="border border-gray-300 rounded-md px-4 py-2 shadow-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-400 transition-all"
        />
        <button
          v-if="selectedDate !== todayDate"
          @click="loadKunjunganWithDate(todayDate)"
          class="bg-blue-100 text-blue-700 px-3 py-2 rounded-md hover:bg-blue-200 transition text-sm whitespace-nowrap"
          title="Kembali ke hari ini"
        >
          Hari Ini
        </button>
      </div>

      <!-- Kanan: Tombol Ganti Poli -->
      <div class="flex justify-center sm:justify-end">
        <button
          @click="activeTab = 'polis'"
          class="bg-gray-100 text-gray-700 px-5 py-2 rounded-md hover:bg-gray-200 shadow-sm transition-all duration-200 flex items-center gap-2"
        >
          🔙 Ganti Poli
        </button>
      </div>
    </div>

    <!-- Baris Kedua: Pencarian Pasien -->
    <div class="flex justify-center sm:justify-end w-full">
      <div class="flex gap-3 items-center w-full sm:w-auto">
        <input
          v-model="searchKunjungan"
          @keypress.enter.prevent="performSearchKunjungan"
          type="search"
          placeholder="🔍  pasien / no reg / MRN"
          class="w-full sm:w-[400px] border border-gray-300 rounded-md px-4 py-2 shadow-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-400 transition-all"
        />
        
        <button
          v-if="searchKunjungan"
          @click="searchKunjungan = ''"
          class="bg-gray-100 text-gray-700 px-3 py-2 rounded-md hover:bg-gray-200 transition"
        >
          Reset
        </button>
      </div>
    </div>
  </div>
</div>


                  <!-- Loading -->
                  <div v-if="loadingKunjungan" class="text-center py-10">
                    <div
                      class="inline-block animate-spin rounded-full h-10 w-10 border-b-4 border-blue-500"
                    ></div>
                    <p class="mt-3 text-gray-500">Memuat data kunjungan...</p>
                  </div>

                  <!-- List Kunjungan -->
                  <div v-else-if="filteredKunjungan.length > 0">
                    <div
                      class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm hover:shadow-md transition"
                    >
                      <h3 class="text-lg font-semibold text-gray-800 mb-6">
                        Daftar Pasien Kunjungan {{ selectedPoli }}
                      </h3>

                      <div class="space-y-3">
                        <div
                          v-for="(kunjungan, index) in filteredKunjungan"
                          :key="kunjungan.id"
                          @click="editKunjungan(kunjungan)"
                          class="flex items-center justify-between p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg border border-gray-200 hover:shadow-md hover:-translate-y-0.5 cursor-pointer transition-all duration-200 group"
                        >
                          <div class="flex items-center space-x-4">
                            <span
                              class="text-lg font-semibold text-gray-800 min-w-[30px]"
                              >{{ index + 1 }}.</span
                            >
                            <div>
                              <div class="flex items-center gap-2 mb-1">
                                <h4
                                  class="text-lg font-medium text-gray-900 group-hover:text-blue-700"
                                >
                                  {{ getPatientName(kunjungan) }}
                                </h4>
                                <!-- Status Tag -->
                                <span
                                  :class="[
                                    'inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold border',
                                    getStatusClass(kunjungan.status_kunjungan)
                                  ]"
                                >
                                  {{ getStatusLabel(kunjungan.status_kunjungan) }}
                                </span>
                              </div>
                              <div
                                class="text-sm text-gray-600 mt-1 space-y-1 leading-tight"
                              >
                                <div v-if="kunjungan.no_reg">
                                  <span class="font-medium">No. Reg:</span>
                                  {{ kunjungan.no_reg }}
                                </div>
                                <div v-if="kunjungan.mrn">
                                  <span class="font-medium">MRN:</span>
                                  {{ kunjungan.mrn }}
                                </div>
                                <div v-if="kunjungan.tgl_reg">
                                  <span class="font-medium">Tanggal:</span>
                                  {{ formatDate(kunjungan.tgl_reg) }}
                                </div>
                              </div>
                            </div>
                          </div>
                          <span
                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 group-hover:bg-blue-200"
                          >
                            Kunjungan
                          </span>
                        </div>
                      </div>

                      <div class="mt-6 pt-4 border-t border-gray-200 text-sm text-gray-600">
                        Total:
                        <span class="font-semibold text-blue-700">
                          {{ filteredKunjungan.length }}
                        </span>
                        pasien
                      </div>
                    </div>
                  </div>

                  <!-- Empty -->
                  <div v-else class="text-center py-12">
                    <div class="text-gray-400 text-6xl mb-4">📋</div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">
                      Belum ada kunjungan
                    </h3>
                    <p class="text-gray-500">
                      Belum ada kunjungan pasien untuk poli
                      <strong>{{ selectedPoli }}</strong>.
                    </p>
                  </div>
                </div>
              </div>
            </transition>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, watch, onMounted, onBeforeUnmount } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { useWebSocket } from '@/composables/useWebSocket'

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
const search = ref('')
const searchInput = ref(null)
const searchKunjungan = ref('')
const selectedDate = ref('')
const dateCheckInterval = ref(null)

// Computed properties
const todayDate = computed(() => {
  return new Date().toISOString().split('T')[0]
})

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

// WebSocket for real-time updates
const { isConnected, setMessageHandler } = useWebSocket(['poli', 'kunjungan'])

// Set up message handler for updates
setMessageHandler((data) => {
  if (data.type === 'update') {
    console.log('📢 Real-time update received:', data.event)
    
    // Handle poli updates
    if (data.channel === 'poli' && (data.event === 'poli.created' || data.event === 'poli.updated' || data.event === 'poli.deleted')) {
      console.log('🔄 Refreshing poli list...')
      router.reload({
        only: ['polis'],
        preserveState: true,
        preserveScroll: true
      })
    }
    
    // Handle kunjungan updates
    if (data.channel === 'kunjungan' && (data.event === 'kunjungan.created' || data.event === 'kunjungan.updated' || data.event === 'kunjungan.deleted')) {
      console.log('🔄 Refreshing kunjungan list...')
      
      // Only refresh if we're on the kunjungan tab
      if (activeTab.value === 'kunjungan' && selectedPoli.value) {
        loadKunjunganForPoli(selectedPoli.value)
      } else {
        // Just reload the kunjungan data with current filters (including date)
        const params = {}
        if (selectedDate.value) params.date = selectedDate.value
        if (selectedPoli.value) params.polis = selectedPoli.value
        if (searchKunjungan.value) params.search = searchKunjungan.value.toString().trim()
        
        router.get(route('dokter.poli_layanan'), params, {
          only: ['kunjungan'],
          preserveState: true,
          preserveScroll: true
        })
      }
    }
  }
})

// Initialize selectedPoli and selectedDate from filters if they exist
onMounted(() => {
  // Initialize date from filters or default to today
  const today = new Date().toISOString().split('T')[0]
  selectedDate.value = props.filters.date || today
  
  // If no date in filters, ensure we load with today's date
  if (!props.filters.date) {
    // Only load if we're on kunjungan tab and have a selected poli
    // Otherwise, the default date will be used when user selects a poli
  }
  
  if (props.filters.polis) {
    selectedPoli.value = props.filters.polis
    activeTab.value = 'kunjungan'
  }
  
  // Set up auto-refresh when date changes (check every minute)
  dateCheckInterval.value = setInterval(() => {
    const currentToday = new Date().toISOString().split('T')[0]
    // If selected date is today or no date selected, check if day has changed
    if (!selectedDate.value || selectedDate.value === currentToday) {
      // If the day has changed from what's in filters, refresh the data
      if (props.filters.date && props.filters.date !== currentToday) {
        loadKunjunganWithDate(currentToday)
      } else if (!props.filters.date && activeTab.value === 'kunjungan' && selectedPoli.value) {
        // If no date in filters but we're viewing today, refresh to ensure we have today's date
        loadKunjunganWithDate(currentToday)
      }
    }
  }, 60000) // Check every minute
})

// Filter poli list using search input
const filteredPolis = computed(() => {
  if (!search.value) return props.polis || []
  const q = search.value.toLowerCase().trim()
  return (props.polis || []).filter(p => {
    return (p.poli_desc || '').toLowerCase().includes(q) || (p.keterangan || '').toLowerCase().includes(q)
  })
})

const resetSearch = () => { search.value = '' }

const applySearch = () => {
  search.value = (search.value || '').toString().trim()
  // blur input to close mobile keyboards and indicate action
  if (searchInput.value && typeof searchInput.value.blur === 'function') {
    searchInput.value.blur()
  }
}

const performSearchKunjungan = () => {
  // include current selectedPoli and date to keep context
  const params = {}
  if (selectedPoli.value) params.polis = selectedPoli.value
  if (searchKunjungan.value) params.search = searchKunjungan.value.toString().trim()
  if (selectedDate.value) params.date = selectedDate.value

  router.get(route('dokter.poli_layanan'), params, { preserveState: true, replace: true })
}

// Function to load kunjungan with date filter
const loadKunjunganWithDate = (date) => {
  selectedDate.value = date
  loadingKunjungan.value = true
  
  const params = { date: date }
  if (selectedPoli.value) params.polis = selectedPoli.value
  if (searchKunjungan.value) params.search = searchKunjungan.value.toString().trim()
  
  router.get(route('dokter.poli_layanan'), params, {
    preserveState: false,
    preserveScroll: false,
    replace: false,
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

// Handle date change
const handleDateChange = (event) => {
  const newDate = event.target.value
  if (newDate) {
    loadKunjunganWithDate(newDate)
  }
}

// Auto-search while typing (debounced)
function debounce(fn, delay = 400) {
  let t
  const wrapped = (...args) => {
    clearTimeout(t)
    t = setTimeout(() => fn(...args), delay)
  }
  wrapped.cancel = () => clearTimeout(t)
  return wrapped
}

const debouncedPerformSearch = debounce(() => performSearchKunjungan(), 400)
const stopWatchSearchKunjungan = watch(searchKunjungan, () => {
  debouncedPerformSearch()
})

onBeforeUnmount(() => {
  if (debouncedPerformSearch && debouncedPerformSearch.cancel) debouncedPerformSearch.cancel()
  if (stopWatchSearchKunjungan) stopWatchSearchKunjungan()
  if (dateCheckInterval.value) {
    clearInterval(dateCheckInterval.value)
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
  const params = { polis: poliName }
  if (selectedDate.value) params.date = selectedDate.value
  if (searchKunjungan.value) params.search = searchKunjungan.value.toString().trim()
  
  router.get(route('dokter.poli_layanan'), params, {
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

const getStatusLabel = (status) => {
  switch (status?.toLowerCase()) {
    case 'completed':
      return 'Sudah Dilayani'
    case 'pending':
      return 'Belum Dilayani'
    default:
      return status || 'Belum Dilayani'
  }
}

const getStatusClass = (status) => {
  switch (status?.toLowerCase()) {
    case 'completed':
      return 'bg-green-500 text-white border-green-600'
    case 'pending':
      return 'bg-red-500 text-white border-red-600'
    default:
      return 'bg-red-500 text-white border-red-600'
  }
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
  if (newFilters.date && newFilters.date !== selectedDate.value) {
    selectedDate.value = newFilters.date
  }
}, { deep: true })
</script>
