<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3'
import { ref, onMounted, onUnmounted, computed, watch } from 'vue'
import type { PropType } from 'vue'
import { useWebSocket } from '@/composables/useWebSocket'

// Type definitions
interface Kunjungan {
  id: number | string;
  nama: string;
  poli: string;
  status: string;
  status_raw?: string;
  waktu: string;
}

interface RiwayatKunjungan {
  tanggal: string;
  tanggalFormatted: string;
  kunjungan: Kunjungan[];
  total: number;
}

// Props from controller
const props = defineProps({
  kunjunganHariIni: {
    type: Array as PropType<Kunjungan[]>,
    default: () => []
  },
  riwayatKunjungan: {
    type: Array as PropType<RiwayatKunjungan[]>,
    default: () => []
  },
  totalHariIni: {
    type: Number,
    default: 0
  },
  poliList: {
    type: Array as PropType<string[]>,
    default: () => ['Semua Poli']
  }
});

// Reactive data untuk kunjungan (bisa di-update via WebSocket)
// Data langsung dari query table kunjungans via controller
const kunjunganData = ref<Kunjungan[]>([...props.kunjunganHariIni]);
const riwayatData = ref<RiwayatKunjungan[]>([...props.riwayatKunjungan]);

// WebSocket connection
// PERBAIKAN BUG 2: Subscribe ke kedua channel karena ada handlers untuk keduanya
const { isConnected, setMessageHandler } = useWebSocket(['antrian-dashboard', 'kunjungan']);

// Handle WebSocket messages
setMessageHandler((data) => {
  console.log('📨 Dashboard received:', data);
  
  if (data.channel === 'antrian-dashboard') {
    // Update status antrian langsung tanpa reload
    if (data.event === 'antrian.status_changed' && data.data) {
      const { kunjungan_id, status, nama_pasien } = data.data;
      const index = kunjunganData.value.findIndex(k => k.id === kunjungan_id);
      if (index !== -1) {
        kunjunganData.value[index].status = getStatusLabel(status);
        kunjunganData.value[index].status_raw = status;
      }
    }
    
    // Reload data when kunjungan changes (created, updated, deleted)
    if (data.event === 'kunjungan.created' || data.event === 'kunjungan.updated' || data.event === 'kunjungan.deleted') {
      // Reload data when kunjungan changes
      console.log('🔄 Reloading data due to kunjungan event:', data.event);
      router.reload({ only: ['kunjunganHariIni', 'riwayatKunjungan', 'totalHariIni', 'poliList'] });
    }
  }
  
  // Handle kunjungan events from kunjungan channel
  if (data.channel === 'kunjungan') {
    if (data.event === 'kunjungan.created' || data.event === 'kunjungan.updated' || data.event === 'kunjungan.deleted') {
      // Reload data when kunjungan changes
      console.log('🔄 Reloading data due to kunjungan channel event:', data.event);
      router.reload({ only: ['kunjunganHariIni', 'riwayatKunjungan', 'totalHariIni', 'poliList'] });
    }
  }
});

// Watch for prop changes - update reactive data ketika props berubah
watch(() => props.kunjunganHariIni, (newVal, oldVal) => {
  console.log('👀 Watch kunjunganHariIni triggered:', {
    newValLength: newVal?.length || 0,
    oldValLength: oldVal?.length || 0,
    newVal: newVal,
    isArray: Array.isArray(newVal)
  });
  
  if (newVal && Array.isArray(newVal)) {
    kunjunganData.value = [...newVal];
    console.log('✅ Updated kunjunganData:', kunjunganData.value.length, 'items', kunjunganData.value);
  } else {
    console.warn('⚠️ kunjunganHariIni is not an array or is null:', typeof newVal, newVal);
    kunjunganData.value = [];
  }
}, { deep: true, immediate: true });

watch(() => props.riwayatKunjungan, (newVal) => {
  if (newVal && Array.isArray(newVal)) {
    riwayatData.value = [...newVal];
    console.log('🔄 Updated riwayatData:', riwayatData.value.length, 'days');
  }
}, { deep: true, immediate: true });

// Toggle view
const showRiwayat = ref(false);

// Filter poli
const selectedPoli = ref('Semua Poli');

// Filter tanggal - Initialize from URL params
const urlParams = typeof window !== 'undefined' ? new URLSearchParams(window.location.search) : null;
const dateParam = urlParams?.get('date') || '';
const dayFilterParam = urlParams?.get('day_filter') || 'today';

const selectedDate = ref<string>(dateParam);
const selectedDayFilter = ref<string>(dayFilterParam || 'today'); // Default: hari ini

// Day filter options
const dayFilters = [
  { value: 'today', label: 'Hari Ini' },
  { value: 'yesterday', label: 'Kemarin' },
  { value: 'this_week', label: 'Minggu Ini' },
  { value: 'last_week', label: 'Minggu Lalu' },
  { value: 'this_month', label: 'Bulan Ini' },
  { value: 'last_month', label: 'Bulan Lalu' },
  { value: 'this_year', label: 'Tahun Ini' }
];

// Function to apply date filter
function applyDateFilter() {
  const params: any = {};
  
  if (selectedDate.value) {
    params.date = selectedDate.value;
    params.day_filter = ''; // Clear day filter if date is selected
  } else if (selectedDayFilter.value) {
    params.day_filter = selectedDayFilter.value;
    params.date = ''; // Clear date if day filter is selected
  }
  
  router.get('/dashboard', params, {
    preserveState: false,
    preserveScroll: false,
    only: ['kunjunganHariIni', 'riwayatKunjungan', 'totalHariIni', 'poliList']
  });
}

// Function to reset date filter
function resetDateFilter() {
  selectedDate.value = '';
  selectedDayFilter.value = 'today';
  router.get('/dashboard', {}, {
    preserveState: false,
    preserveScroll: false,
    only: ['kunjunganHariIni', 'riwayatKunjungan', 'totalHariIni', 'poliList']
  });
}


// Scroll container ref
const scrollContainer = ref<HTMLElement | null>(null);

// Scroll functions
function scrollLeft() {
  if (scrollContainer.value) {
    scrollContainer.value.scrollBy({ left: -200, behavior: 'smooth' });
  }
}

function scrollRight() {
  if (scrollContainer.value) {
    scrollContainer.value.scrollBy({ left: 200, behavior: 'smooth' });
  }
}

// Jam real-time
const currentTime = ref(new Date());
let timeInterval: NodeJS.Timeout | null = null;

// Get status label
function getStatusLabel(status: string) {
  switch(status) {
    case 'pending': return 'Menunggu';
    case 'in_progress': return 'Sedang Dilayani';
    case 'completed': return 'Selesai';
    default: return status;
  }
}

// Format tanggal hari ini
const formattedDate = computed(() => {
  return currentTime.value.toLocaleDateString('id-ID', { 
    weekday: 'long', 
    year: 'numeric', 
    month: 'long', 
    day: 'numeric' 
  });
});

// Format tanggal yang dipilih untuk display
const selectedDateFormatted = computed(() => {
  if (selectedDate.value) {
    const date = new Date(selectedDate.value);
    return date.toLocaleDateString('id-ID', { 
      weekday: 'long', 
      year: 'numeric', 
      month: 'long', 
      day: 'numeric' 
    });
  }
  
  const filter = dayFilters.find(f => f.value === selectedDayFilter.value);
  return filter ? filter.label : 'Hari Ini';
});

const formattedTime = computed(() => {
  return currentTime.value.toLocaleTimeString('id-ID', { 
    hour: '2-digit', 
    minute: '2-digit', 
    second: '2-digit' 
  });
});

// Filtered kunjungan hari ini - data dari query table kunjungans
const filteredKunjunganHariIni = computed(() => {
  console.log('🔍 filteredKunjunganHariIni computed:', {
    kunjunganDataLength: kunjunganData.value?.length || 0,
    selectedPoli: selectedPoli.value,
    kunjunganData: kunjunganData.value
  });
  
  if (!kunjunganData.value || kunjunganData.value.length === 0) {
    console.log('⚠️ kunjunganData is empty');
    return [];
  }
  
  if (selectedPoli.value === 'Semua Poli') {
    console.log('✅ Returning all kunjungan:', kunjunganData.value.length);
    return kunjunganData.value;
  }
  
  const filtered = kunjunganData.value.filter(k => k.poli === selectedPoli.value);
  console.log('🔍 Filtered by poli:', {
    selectedPoli: selectedPoli.value,
    filteredCount: filtered.length,
    allPolis: kunjunganData.value.map(k => k.poli)
  });
  return filtered;
});

// Filtered riwayat kunjungan - data dari query table kunjungans
const filteredRiwayatKunjungan = computed(() => {
  if (!riwayatData.value || riwayatData.value.length === 0) {
    return [];
  }
  
  if (selectedPoli.value === 'Semua Poli') {
    return riwayatData.value;
  }
  
  return riwayatData.value
    .map(hari => ({
      ...hari,
      kunjungan: hari.kunjungan.filter(k => k.poli === selectedPoli.value),
      total: hari.kunjungan.filter(k => k.poli === selectedPoli.value).length
    }))
    .filter(hari => hari.kunjungan.length > 0);
});

// Total filtered hari ini
const totalFiltered = computed(() => {
  return filteredKunjunganHariIni.value.length;
});

// Count by status
const countByStatus = computed(() => {
  const menunggu = kunjunganData.value.filter(k => k.status === 'Menunggu' || k.status_raw === 'pending').length;
  const sedangDilayani = kunjunganData.value.filter(k => k.status === 'Sedang Dilayani' || k.status_raw === 'in_progress').length;
  const selesai = kunjunganData.value.filter(k => k.status === 'Selesai' || k.status_raw === 'completed').length;
  return { menunggu, sedangDilayani, selesai };
});

onMounted(() => {
  // Debug: Log data saat component mount
  console.log('📊 Dashboard Data:', {
    kunjunganHariIni: props.kunjunganHariIni?.length || 0,
    totalHariIni: props.totalHariIni,
    kunjunganData: props.kunjunganHariIni,
    poliList: props.poliList?.length || 0,
    riwayatKunjungan: props.riwayatKunjungan?.length || 0
  });
  
  // Initialize data dari props (data dari query table kunjungans)
  // RESET: Pastikan data di-load dengan benar
  if (props.kunjunganHariIni) {
    if (Array.isArray(props.kunjunganHariIni)) {
      kunjunganData.value = props.kunjunganHariIni.length > 0 ? [...props.kunjunganHariIni] : [];
      console.log('✅ Loaded kunjungan data:', kunjunganData.value.length, 'items');
    } else {
      kunjunganData.value = [];
      console.warn('⚠️ kunjunganHariIni is not an array:', typeof props.kunjunganHariIni);
    }
  } else {
    kunjunganData.value = [];
    console.warn('⚠️ kunjunganHariIni is undefined or null');
  }
  
  if (props.riwayatKunjungan) {
    if (Array.isArray(props.riwayatKunjungan)) {
      riwayatData.value = props.riwayatKunjungan.length > 0 ? [...props.riwayatKunjungan] : [];
    } else {
      riwayatData.value = [];
    }
  } else {
    riwayatData.value = [];
  }
  
  // Update jam setiap detik
  timeInterval = setInterval(() => {
    currentTime.value = new Date();
  }, 1000);
});

onUnmounted(() => {
  if (timeInterval) {
    clearInterval(timeInterval);
  }
});

// Get status badge class
function getStatusClass(status: string) {
  switch(status) {
    case 'Selesai':
    case 'completed':
      return 'bg-green-100 text-green-700 border-green-300';
    case 'Sedang Dilayani':
    case 'in_progress':
      return 'bg-blue-100 text-blue-700 border-blue-300 animate-pulse';
    case 'Menunggu':
    case 'pending':
    default:
      return 'bg-yellow-100 text-yellow-700 border-yellow-300';
  }
}

// Get poli color
function getPoliColor(poli: string) {
  const colors: Record<string, string> = {
    'Semua Poli': 'bg-gray-500',
    'Apotek': 'bg-pink-500',
    'KIA (Kesehatan Ibu dan Anak)': 'bg-purple-500',
    'Laboratorium': 'bg-indigo-500',
    'Poli Bedah': 'bg-red-500',
    'Poli Gigi': 'bg-cyan-500',
    'Poli Jantung': 'bg-rose-500',
    'Poli Kulit dan Kelamin': 'bg-amber-500',
    'Poli Mata': 'bg-teal-500',
    'Poli THT': 'bg-orange-500',
    'Poli Umum': 'bg-blue-500',
  };
  return colors[poli] || 'bg-gray-500';
}

// Get display name for poli (untuk card)
function getPoliDisplayName(poli: string) {
  const displayNames: Record<string, string> = {
    'Semua Poli': 'Semua Poli',
    'Apotek': 'Apotek',
    'KIA (Kesehatan Ibu dan Anak)': 'KIA (Kesehatan Ibu dan Anak)',
    'Laboratorium': 'Laboratorium',
    'Poli Bedah': 'Poli Bedah',
    'Poli Gigi': 'Poli Gigi',
    'Poli Jantung': 'Poli Jantung',
    'Poli Kulit dan Kelamin': 'Poli Kulit dan Kelamin',
    'Poli Mata': 'Poli Mata',
    'Poli THT': 'Poli THT',
    'Poli Umum': 'Poli Umum',
  };
  return displayNames[poli] || poli;
}

// Get short poli name for button
function getShortPoliName(poli: string) {
  const shortNames: Record<string, string> = {
    'Semua Poli': 'Semua',
    'Apotek': 'Apotek',
    'KIA (Kesehatan Ibu dan Anak)': 'KIA',
    'Laboratorium': 'Lab',
    'Poli Bedah': 'Bedah',
    'Poli Gigi': 'Gigi',
    'Poli Jantung': 'Jantung',
    'Poli Kulit dan Kelamin': 'Kulit',
    'Poli Mata': 'Mata',
    'Poli THT': 'THT',
    'Poli Umum': 'Umum',
  };
  
  // Jika ada di mapping, gunakan itu
  if (shortNames[poli]) {
    return shortNames[poli];
  }
  
  // Jika tidak ada, buat singkatan otomatis
  // Ambil kata pertama atau beberapa karakter pertama
  if (poli.startsWith('Poli ')) {
    return poli.replace('Poli ', '').substring(0, 8);
  }
  
  // Untuk poli lain, ambil 8 karakter pertama
  return poli.length > 8 ? poli.substring(0, 8) + '...' : poli;
}
</script>

<template>
  <Head title="Riwayat Kunjungan" />
  <AuthenticatedLayout>
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50">
      <!-- Main Content - Full width for TV -->
      <div class="max-w-7xl mx-auto px-6 py-8">
        
        <!-- Header -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-6">
          <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg">
              <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            <div>
              <div class="flex items-center gap-2">
                <h1 class="text-3xl font-bold text-gray-900">Riwayat Kunjungan</h1>
                <!-- WebSocket Indicator -->
                <div 
                  :class="[
                    'w-3 h-3 rounded-full',
                    isConnected ? 'bg-green-500 animate-pulse' : 'bg-red-500'
                  ]"
                  :title="isConnected ? 'Realtime Connected' : 'Disconnected'"
                ></div>
              </div>
              <p class="text-gray-500 text-lg">{{ formattedDate }} • {{ formattedTime }}</p>
            </div>
          </div>
          
          <!-- Toggle Buttons -->
          <div class="flex bg-white rounded-xl shadow-md p-1 border border-gray-200">
            <button
              @click="showRiwayat = false"
              :class="[
                'px-6 py-3 rounded-lg text-base font-semibold transition-all duration-200',
                !showRiwayat
                  ? 'bg-blue-500 text-white shadow-md'
                  : 'text-gray-600 hover:bg-gray-100'
              ]"
            >
              Hari Ini
            </button>
            <button
              @click="showRiwayat = true"
              :class="[
                'px-6 py-3 rounded-lg text-base font-semibold transition-all duration-200',
                showRiwayat
                  ? 'bg-blue-500 text-white shadow-md'
                  : 'text-gray-600 hover:bg-gray-100'
              ]"
            >
              Riwayat
            </button>
          </div>
        </div>

        <!-- Filter Tanggal -->
        <div v-if="!showRiwayat" class="bg-white/80 backdrop-blur-sm rounded-xl shadow-md border border-gray-100 p-4 mb-6">
          <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
            <div class="flex items-center gap-2">
              <span class="text-lg">📅</span>
              <span class="font-semibold text-gray-700">Filter Tanggal:</span>
            </div>
            
            <!-- Day Filter Buttons -->
            <div class="flex flex-wrap items-center gap-2 flex-1">
              <button
                v-for="filter in dayFilters"
                :key="filter.value"
                @click="selectedDayFilter = filter.value; selectedDate = ''; applyDateFilter()"
                :class="[
                  'px-4 py-2 rounded-lg text-sm font-semibold transition-all duration-200',
                  selectedDayFilter === filter.value && !selectedDate
                    ? 'bg-blue-500 text-white shadow-md'
                    : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                ]"
              >
                {{ filter.label }}
              </button>
            </div>
            
            <!-- Date Picker -->
            <div class="flex items-center gap-2">
              <input
                type="date"
                v-model="selectedDate"
                @change="selectedDayFilter = ''; applyDateFilter()"
                class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                :max="new Date().toISOString().split('T')[0]"
              />
              <button
                v-if="selectedDate || selectedDayFilter !== 'today'"
                @click="resetDateFilter"
                class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg text-sm font-semibold transition-all duration-200"
              >
                Reset
              </button>
            </div>
          </div>
        </div>

        <!-- Status Summary Cards -->
        <div v-if="!showRiwayat" class="grid grid-cols-3 gap-4 mb-6">
          <!-- Menunggu -->
          <div class="bg-gradient-to-r from-yellow-50 to-orange-50 rounded-xl p-4 border border-yellow-200 shadow-sm">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-yellow-500 rounded-full flex items-center justify-center">
                  <span class="text-white text-lg">⏳</span>
                </div>
                <span class="font-semibold text-gray-700">Menunggu</span>
              </div>
              <span class="text-3xl font-bold text-yellow-600">{{ countByStatus.menunggu }}</span>
            </div>
          </div>
          
          <!-- Sedang Dilayani -->
          <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-4 border border-blue-200 shadow-sm">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center animate-pulse">
                  <span class="text-white text-lg">🩺</span>
                </div>
                <span class="font-semibold text-gray-700">Sedang Dilayani</span>
              </div>
              <span class="text-3xl font-bold text-blue-600">{{ countByStatus.sedangDilayani }}</span>
            </div>
          </div>
          
          <!-- Selesai -->
          <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl p-4 border border-green-200 shadow-sm">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center">
                  <span class="text-white text-lg">✅</span>
                </div>
                <span class="font-semibold text-gray-700">Selesai</span>
              </div>
              <span class="text-3xl font-bold text-green-600">{{ countByStatus.selesai }}</span>
            </div>
          </div>
        </div>

        <!-- Filter Poli - Horizontal Scroll with Arrows -->
        <div class="bg-white/80 backdrop-blur-sm rounded-xl shadow-md border border-gray-100 p-4 mb-6">
          <div class="flex items-center gap-2">
            <!-- Arrow Left -->
            <button
              @click="scrollLeft"
              class="flex-none w-10 h-10 bg-gray-100 hover:bg-gray-200 rounded-full flex items-center justify-center text-gray-600 hover:text-gray-800 transition-all duration-200 shadow-sm hover:shadow"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
              </svg>
            </button>

            <!-- Scroll Container -->
            <div 
              ref="scrollContainer"
              class="flex-1 flex items-center gap-3 overflow-x-hidden"
            >
              <!-- Card Semua Poli -->
              <div
                @click="selectedPoli = 'Semua Poli'"
                :class="[
                  'flex-none flex items-center gap-2 px-4 py-2 rounded-lg cursor-pointer transition-all duration-200 border-2',
                  selectedPoli === 'Semua Poli'
                    ? 'bg-blue-500 text-white border-blue-500 shadow-md'
                    : 'bg-white text-gray-700 border-gray-200 hover:border-blue-300 hover:bg-blue-50'
                ]"
              >
                <span class="text-lg">📋</span>
                <span class="font-semibold text-sm whitespace-nowrap">Semua</span>
              </div>

              <!-- Card Per Poli -->
              <div
                v-for="poli in props.poliList.filter(p => p !== 'Semua Poli')"
                :key="poli"
                @click="selectedPoli = poli"
                :class="[
                  'flex-none flex items-center gap-2 px-4 py-2 rounded-lg cursor-pointer transition-all duration-200 border-2',
                  selectedPoli === poli
                    ? 'bg-green-500 text-white border-green-500 shadow-md'
                    : 'bg-white text-gray-700 border-gray-200 hover:border-green-300 hover:bg-green-50'
                ]"
              >
                <span class="text-lg">🏥</span>
                <div class="flex flex-col">
                  <span class="font-semibold text-sm whitespace-nowrap">{{ getShortPoliName(poli) }}</span>
                  <span :class="['text-xs', selectedPoli === poli ? 'text-green-100' : 'text-green-600']">Aktif</span>
                </div>
              </div>
            </div>

            <!-- Arrow Right -->
            <button
              @click="scrollRight"
              class="flex-none w-10 h-10 bg-gray-100 hover:bg-gray-200 rounded-full flex items-center justify-center text-gray-600 hover:text-gray-800 transition-all duration-200 shadow-sm hover:shadow"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
              </svg>
            </button>
          </div>
        </div>

        <!-- Kunjungan Hari Ini -->
        <div v-if="!showRiwayat" class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
          <!-- Header Hari Ini -->
          <div class="bg-gradient-to-r from-blue-500 to-indigo-600 px-8 py-5 flex items-center justify-between">
            <div class="flex items-center gap-3">
              <span class="text-2xl">📋</span>
              <h2 class="text-xl font-bold text-white">
                Kunjungan {{ selectedDayFilter === 'today' && !selectedDate ? 'Hari Ini' : selectedDateFormatted }}
                <span v-if="selectedPoli !== 'Semua Poli'" class="text-blue-200 text-base font-normal ml-2">
                  - {{ selectedPoli }}
                </span>
              </h2>
            </div>
            <div class="bg-white/20 backdrop-blur-sm px-4 py-2 rounded-full">
              <span class="text-white font-bold text-lg">{{ totalFiltered }} Pasien</span>
            </div>
          </div>
          
          <!-- List Pasien Hari Ini -->
          <div class="divide-y divide-gray-100">
            <div 
              v-for="(pasien, index) in filteredKunjunganHariIni" 
              :key="pasien.id"
              class="flex items-center justify-between px-8 py-5 hover:bg-gray-50 transition-colors"
            >
              <div class="flex items-center gap-5">
                <div 
                  :class="[
                    'w-12 h-12 rounded-full flex items-center justify-center text-white font-bold text-lg shadow-md',
                    getPoliColor(pasien.poli)
                  ]"
                >
                  {{ index + 1 }}
                </div>
                <div>
                  <h3 class="text-xl font-semibold text-gray-900">{{ pasien.nama }}</h3>
                  <p class="text-gray-500 text-base">{{ pasien.poli }}</p>
                </div>
              </div>
              <div class="flex items-center gap-4">
                <span 
                  :class="[
                    'px-4 py-2 rounded-full text-sm font-semibold border',
                    getStatusClass(pasien.status)
                  ]"
                >
                  {{ pasien.status }}
                </span>
                <span class="text-gray-400 text-base font-medium">{{ pasien.waktu }}</span>
              </div>
            </div>

            <!-- Empty State -->
            <div v-if="filteredKunjunganHariIni.length === 0" class="px-8 py-16 text-center">
              <div class="text-6xl mb-4">📭</div>
              <h3 class="text-xl font-semibold text-gray-700 mb-2">
                {{ selectedPoli === 'Semua Poli' 
                  ? `Belum Ada Kunjungan ${selectedDayFilter === 'today' && !selectedDate ? 'Hari Ini' : selectedDateFormatted}` 
                  : `Belum Ada Kunjungan di ${selectedPoli} ${selectedDayFilter === 'today' && !selectedDate ? '' : 'pada ' + selectedDateFormatted}` }}
              </h3>
              <p class="text-gray-500">Kunjungan pasien akan ditampilkan di sini</p>
            </div>
          </div>
        </div>

        <!-- Riwayat Kunjungan -->
        <div v-else class="space-y-6">
          <div 
            v-for="hari in filteredRiwayatKunjungan" 
            :key="hari.tanggal"
            class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden"
          >
            <!-- Header Per Hari -->
            <div class="bg-gradient-to-r from-gray-700 to-gray-800 px-8 py-4 flex items-center justify-between">
              <h2 class="text-lg font-bold text-white">
                {{ hari.tanggalFormatted }}
                <span v-if="selectedPoli !== 'Semua Poli'" class="text-gray-300 text-base font-normal ml-2">
                  - {{ selectedPoli }}
                </span>
              </h2>
              <div class="bg-white/20 backdrop-blur-sm px-4 py-1.5 rounded-full">
                <span class="text-white font-semibold">{{ hari.total }} Pasien</span>
              </div>
            </div>
            
            <!-- List Pasien Per Hari -->
            <div class="divide-y divide-gray-100">
              <div 
                v-for="(pasien, index) in hari.kunjungan" 
                :key="pasien.id"
                class="flex items-center justify-between px-8 py-4 hover:bg-gray-50 transition-colors"
              >
                <div class="flex items-center gap-4">
                  <div 
                    :class="[
                      'w-10 h-10 rounded-full flex items-center justify-center text-white font-bold shadow',
                      getPoliColor(pasien.poli)
                    ]"
                  >
                    {{ index + 1 }}
                  </div>
                  <div>
                    <h3 class="text-lg font-semibold text-gray-900">{{ pasien.nama }}</h3>
                    <p class="text-gray-500 text-sm">{{ pasien.poli }}</p>
                  </div>
                </div>
                <div class="flex items-center gap-4">
                  <span 
                    :class="[
                      'px-3 py-1.5 rounded-full text-sm font-semibold border',
                      getStatusClass(pasien.status)
                    ]"
                  >
                    {{ pasien.status }}
                  </span>
                  <span class="text-gray-400 text-sm font-medium">{{ pasien.waktu }}</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Empty State Riwayat -->
          <div v-if="filteredRiwayatKunjungan.length === 0" class="bg-white rounded-2xl shadow-xl border border-gray-100 px-8 py-16 text-center">
            <div class="text-6xl mb-4">📭</div>
            <h3 class="text-xl font-semibold text-gray-700 mb-2">
              {{ selectedPoli === 'Semua Poli' ? 'Belum Ada Riwayat Kunjungan' : `Belum Ada Riwayat di ${selectedPoli}` }}
            </h3>
            <p class="text-gray-500">Riwayat kunjungan akan ditampilkan di sini</p>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<style scoped>
/* Smooth transitions */
.divide-y > div {
  transition: background-color 0.2s ease;
}

/* Poli scroll container */
.poli-scroll-container {
  -webkit-overflow-scrolling: touch;
  scroll-behavior: smooth;
  scrollbar-width: thin;
  scrollbar-color: #94a3b8 #e2e8f0;
}

.poli-scroll-container::-webkit-scrollbar {
  height: 8px;
}

.poli-scroll-container::-webkit-scrollbar-track {
  background: #e2e8f0;
  border-radius: 4px;
}

.poli-scroll-container::-webkit-scrollbar-thumb {
  background-color: #94a3b8;
  border-radius: 4px;
}

.poli-scroll-container::-webkit-scrollbar-thumb:hover {
  background-color: #64748b;
}
</style>
