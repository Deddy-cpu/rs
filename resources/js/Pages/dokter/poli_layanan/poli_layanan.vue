<template>
  <AuthenticatedLayout>
    <Head title="Poli Layanan" />

    <div class="min-h-screen bg-cover bg-center p-4 sm:p-6" style="background-image: url('/images/bg-login.png')">
      <div class="max-w-7xl mx-auto">

        <!-- Header -->
        <div class="mb-8">
          <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-2xl shadow-xl p-6 sm:p-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
              <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-white/20 backdrop-blur rounded-xl flex items-center justify-center">
                  <i class="fas fa-hospital text-white text-2xl"></i>
                </div>
                <div>
                  <h1 class="text-2xl sm:text-3xl font-bold text-white">Poli & Layanan</h1>
                  <p class="text-white/80 text-sm sm:text-base">Kelola layanan poli dan kunjungan pasien</p>
                </div>
              </div>
              <!-- Info Ruangan -->
              <div v-if="userRuangan" class="px-4 py-2 bg-white/20 backdrop-blur rounded-lg">
                <p class="text-white text-sm">
                  <i class="fas fa-map-marker-alt mr-2"></i>
                  <span class="font-semibold">{{ userRuangan }}</span>
            </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Tabs Navigation -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
          <!-- Tab Buttons -->
          <div class="flex border-b border-gray-200">
              <button
                type="button"
                @click.prevent="activeTab = 'polis'"
                :class="[
                'flex-1 px-6 py-4 text-center font-semibold transition-all duration-300 flex items-center justify-center gap-2',
                  activeTab === 'polis'
                  ? 'bg-gradient-to-r from-green-500 to-green-600 text-white'
                  : 'bg-gray-50 text-gray-600 hover:bg-green-50'
              ]"
            >
              <i class="fas fa-clinic-medical"></i>
              <span>Daftar Poli</span>
              <span v-if="(props.polis || []).length > 0" class="ml-1 px-2 py-0.5 rounded-full text-xs" :class="activeTab === 'polis' ? 'bg-white/20' : 'bg-gray-200'">
                {{ (props.polis || []).length }}
              </span>
              </button>
              <button
                type="button"
                @click.prevent="activeTab = 'kunjungan'"
                :class="[
                'flex-1 px-6 py-4 text-center font-semibold transition-all duration-300 flex items-center justify-center gap-2',
                  activeTab === 'kunjungan'
                  ? 'bg-gradient-to-r from-green-500 to-green-600 text-white'
                  : 'bg-gray-50 text-gray-600 hover:bg-green-50'
              ]"
            >
              <i class="fas fa-users"></i>
              <span>Kunjungan Pasien</span>
              <span v-if="selectedPoli" class="ml-1 px-2 py-0.5 rounded-full text-xs" :class="activeTab === 'kunjungan' ? 'bg-white/20' : 'bg-green-100 text-green-700'">
                  {{ selectedPoli }}
                </span>
              </button>
          </div>

          <!-- Tab Content -->
          <div class="p-6 sm:p-8">
            <!-- TAB 1: DAFTAR POLI -->
            <transition name="fade" mode="out-in">
              <div v-if="activeTab === 'polis'" key="polis">
                <!-- Search -->
                <div class="mb-6">
                  <div class="relative max-w-md mx-auto">
                    <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                  <input
                    id="searchPoli"
                    ref="searchInput"
                    v-model="search"
                    @keypress.enter.prevent="applySearch"
                    type="search"
                      placeholder="Cari poli..."
                      class="w-full pl-11 pr-10 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-300 focus:border-green-400 transition-all"
                  />
                  <button
                    v-if="search"
                    @click="search = ''"
                      class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600"
                  >
                      <i class="fas fa-times"></i>
                  </button>
                  </div>
                </div>

                <!-- Poli Grid -->
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                  <button
                    v-for="(poli, index) in filteredPolis"
                    :key="poli.id"
                    @click="selectPoli(poli)"
                    class="group bg-white border-2 border-gray-100 rounded-xl p-5 text-center hover:border-green-400 hover:shadow-lg transition-all duration-300 relative overflow-hidden"
                  >
                    <!-- Decorative gradient -->
                    <div class="absolute inset-0 bg-gradient-to-br from-green-50 to-green-100 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    
                    <div class="relative z-10">
                      <!-- Icon with color based on index -->
                      <div :class="[
                        'w-12 h-12 mx-auto rounded-xl flex items-center justify-center mb-3 transition-transform group-hover:scale-110',
                        getPoliColor(index)
                      ]">
                        <i :class="['text-xl text-white', getPoliIcon(poli.poli_desc)]"></i>
                      </div>
                      
                      <h3 class="font-semibold text-gray-800 text-sm group-hover:text-green-600 transition-colors line-clamp-2">
                      {{ poli.poli_desc }}
                    </h3>
                      
                      <!-- Status badge -->
                      <div class="mt-2">
                        <span :class="[
                          'inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium',
                          poli.aktif === 'Y' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'
                        ]">
                          <i :class="['mr-1 text-[10px]', poli.aktif === 'Y' ? 'fas fa-check-circle' : 'fas fa-times-circle']"></i>
                        {{ poli.aktif === 'Y' ? 'Aktif' : 'Non-aktif' }}
                      </span>
                      </div>
                    </div>
                  </button>
                </div>

                <!-- Empty States -->
                <div v-if="(props.polis || []).length === 0" class="text-center py-16">
                  <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-hospital text-4xl text-gray-400"></i>
                  </div>
                  <h3 class="text-lg font-semibold text-gray-800 mb-2">Tidak ada poli tersedia</h3>
                  <p class="text-gray-500">Belum ada data poli yang tersedia.</p>
                </div>

                <div v-else-if="filteredPolis.length === 0" class="text-center py-16">
                  <div class="w-20 h-20 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-search text-4xl text-yellow-500"></i>
                  </div>
                  <h3 class="text-lg font-semibold text-gray-800 mb-2">Tidak ditemukan</h3>
                  <p class="text-gray-500">Tidak ada poli yang cocok dengan "<strong>{{ search }}</strong>"</p>
                </div>
              </div>

              <!-- TAB 2: KUNJUNGAN -->
              <div v-else key="kunjungan">
                <!-- No Poli Selected -->
                <div v-if="!selectedPoli" class="text-center py-16">
                  <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-hand-pointer text-4xl text-green-500"></i>
                  </div>
                  <h3 class="text-xl font-semibold text-gray-800 mb-2">Pilih Poli Terlebih Dahulu</h3>
                  <p class="text-gray-500 mb-6">Silakan pilih poli dari tab Daftar Poli untuk melihat kunjungan</p>
                  <button
                    @click="activeTab = 'polis'"
                    class="px-6 py-3 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-xl font-medium hover:shadow-lg transition-all"
                  >
                    <i class="fas fa-arrow-left mr-2"></i>
                    Ke Daftar Poli
                  </button>
                </div>

                <!-- Poli Selected -->
                <div v-else>
                  <!-- Controls -->
                  <div class="bg-gray-50 rounded-xl p-4 mb-6">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                      <!-- Left: Poli Info & Date -->
                      <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                        <div class="flex items-center gap-2">
                          <span class="px-3 py-1.5 bg-green-500 text-white rounded-lg text-sm font-semibold">
                            <i class="fas fa-clinic-medical mr-1"></i>
                            {{ selectedPoli }}
                          </span>
                          <button
                            @click="activeTab = 'polis'"
                            class="px-3 py-1.5 bg-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-300 transition"
                          >
                            <i class="fas fa-exchange-alt mr-1"></i>
                            Ganti
                          </button>
  </div>

                        <div class="flex items-center gap-2">
                          <label class="text-sm text-gray-600 font-medium">
                            <i class="fas fa-calendar-alt mr-1"></i>
        </label>
        <input
          id="dateFilter"
          v-model="selectedDate"
          @change="handleDateChange"
          type="date"
                            class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-300 focus:border-green-400"
        />
        <button
          v-if="selectedDate !== todayDate"
          @click="loadKunjunganWithDate(todayDate)"
                            class="px-3 py-1.5 bg-green-100 text-green-700 rounded-lg text-sm font-medium hover:bg-green-200 transition"
        >
          Hari Ini
        </button>
      </div>
    </div>

                      <!-- Right: Search -->
                      <div class="relative w-full lg:w-80">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
        <input
          v-model="searchKunjungan"
          @keypress.enter.prevent="performSearchKunjungan"
          type="search"
                          placeholder="Cari pasien / No Reg / MRN..."
                          class="w-full pl-10 pr-10 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-300 focus:border-green-400"
        />
        <button
          v-if="searchKunjungan"
          @click="searchKunjungan = ''"
                          class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600"
        >
                          <i class="fas fa-times"></i>
        </button>
      </div>
    </div>
  </div>

                  <!-- Loading -->
                  <div v-if="loadingKunjungan" class="text-center py-16">
                    <div class="w-12 h-12 border-4 border-green-200 border-t-green-500 rounded-full animate-spin mx-auto"></div>
                    <p class="mt-4 text-gray-500">Memuat data kunjungan...</p>
                  </div>

                  <!-- Kunjungan List -->
                  <div v-else-if="filteredKunjungan.length > 0" class="space-y-3">
                        <div
                          v-for="(kunjungan, index) in filteredKunjungan"
                          :key="kunjungan.id"
                          @click="editKunjungan(kunjungan)"
                      class="bg-white border border-gray-200 rounded-xl p-4 hover:border-green-400 hover:shadow-md cursor-pointer transition-all duration-200 group"
                        >
                      <div class="flex items-start justify-between gap-4">
                        <!-- Left: Patient Info -->
                        <div class="flex items-start gap-4 flex-1 min-w-0">
                          <!-- Number Badge -->
                          <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-green-600 rounded-lg flex items-center justify-center text-white font-bold flex-shrink-0">
                            {{ index + 1 }}
                          </div>
                          
                          <!-- Patient Details -->
                          <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap mb-2">
                              <h4 class="font-semibold text-gray-800 group-hover:text-green-600 transition-colors">
                                  {{ getPatientName(kunjungan) }}
                                </h4>
                              <span :class="[
                                'px-2 py-0.5 rounded-full text-xs font-semibold',
                                kunjungan.status_kunjungan === 'completed' 
                                  ? 'bg-green-100 text-green-700' 
                                  : 'bg-yellow-100 text-yellow-700'
                              ]">
                                  {{ getStatusLabel(kunjungan.status_kunjungan) }}
                                </span>
                            </div>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-1 text-sm text-gray-600">
                              <div v-if="kunjungan.no_reg" class="flex items-center gap-1">
                                <i class="fas fa-hashtag text-gray-400 text-xs"></i>
                                <span class="truncate">{{ kunjungan.no_reg }}</span>
                              </div>
                              <div v-if="kunjungan.mrn" class="flex items-center gap-1">
                                <i class="fas fa-id-card text-gray-400 text-xs"></i>
                                <span class="truncate">{{ kunjungan.mrn }}</span>
                                </div>
                              <div v-if="kunjungan.tgl_reg" class="flex items-center gap-1">
                                <i class="fas fa-calendar text-gray-400 text-xs"></i>
                                <span>{{ formatDate(kunjungan.tgl_reg) }}</span>
                              </div>
                            </div>
                          </div>
                        </div>

                        <!-- Right: Action Indicator -->
                        <div class="flex items-center gap-2 flex-shrink-0">
                          <span class="hidden sm:flex items-center gap-1 px-3 py-1.5 bg-green-50 text-green-600 rounded-lg text-xs font-medium group-hover:bg-green-100 transition">
                            <i class="fas fa-edit"></i>
                            Edit
                          </span>
                          <i class="fas fa-chevron-right text-gray-400 group-hover:text-green-500 transition"></i>
                        </div>
                      </div>
                      </div>

                    <!-- Total Count -->
                    <div class="text-center pt-4 text-sm text-gray-500">
                      Total: <span class="font-semibold text-green-600">{{ filteredKunjungan.length }}</span> pasien
                    </div>
                  </div>

                  <!-- Empty State -->
                  <div v-else class="text-center py-16">
                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                      <i class="fas fa-clipboard-list text-4xl text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">Belum ada kunjungan</h3>
                    <p class="text-gray-500">Belum ada kunjungan pasien untuk poli <strong>{{ selectedPoli }}</strong></p>
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
  },
  userRuangan: {
    type: String,
    default: null
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

// Get poli color based on index
const getPoliColor = (index) => {
  const colors = [
    'bg-gradient-to-br from-green-500 to-green-600',
    'bg-gradient-to-br from-green-500 to-green-600',
    'bg-gradient-to-br from-purple-500 to-purple-600',
    'bg-gradient-to-br from-pink-500 to-pink-600',
    'bg-gradient-to-br from-orange-500 to-orange-600',
    'bg-gradient-to-br from-teal-500 to-teal-600',
    'bg-gradient-to-br from-indigo-500 to-indigo-600',
    'bg-gradient-to-br from-red-500 to-red-600',
    'bg-gradient-to-br from-cyan-500 to-cyan-600',
    'bg-gradient-to-br from-amber-500 to-amber-600'
  ]
  return colors[index % colors.length]
}

// Get poli icon based on poli name
const getPoliIcon = (poliName) => {
  const name = (poliName || '').toLowerCase()
  if (name.includes('umum')) return 'fas fa-stethoscope'
  if (name.includes('gigi')) return 'fas fa-tooth'
  if (name.includes('anak')) return 'fas fa-baby'
  if (name.includes('mata')) return 'fas fa-eye'
  if (name.includes('kia') || name.includes('ibu')) return 'fas fa-female'
  if (name.includes('dalam')) return 'fas fa-heartbeat'
  if (name.includes('bedah')) return 'fas fa-cut'
  if (name.includes('saraf')) return 'fas fa-brain'
  if (name.includes('kulit')) return 'fas fa-hand-paper'
  if (name.includes('tht')) return 'fas fa-head-side-cough'
  if (name.includes('jiwa') || name.includes('psikiatri')) return 'fas fa-brain'
  if (name.includes('lab')) return 'fas fa-vial'
  if (name.includes('apotek') || name.includes('farmasi')) return 'fas fa-pills'
  if (name.includes('radiologi') || name.includes('rontgen')) return 'fas fa-x-ray'
  if (name.includes('fisioterapi')) return 'fas fa-walking'
  if (name.includes('igd') || name.includes('emergency')) return 'fas fa-ambulance'
  return 'fas fa-clinic-medical'
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
