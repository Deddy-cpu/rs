<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3'
import { ref, computed, onMounted, onUnmounted } from 'vue'
import StatCard from '@/Components/StatCard.vue'
import SimpleBarChart from '@/Components/SimpleBarChart.vue'
import MultiBarChart from '@/Components/MultiBarChart.vue'
import { useAuth } from '@/composables/useAuth'

const { user } = useAuth();

// Sample data - dalam aplikasi nyata, ini akan diambil dari API
const stats = ref({
  pendaftaranHariIni: 45,
  pendaftaranBulanIni: 892,
  pasienBaru: 23,
  pasienLama: 22
});

const trendData = ref({
  pendaftaranHariIni: 15.3,
  pendaftaranBulanIni: 8.7,
  pasienBaru: 12.5,
  pasienLama: 18.2
});

// Chart data untuk pendaftaran per hari dalam seminggu
const pendaftaranChartData = computed(() => ({
  labels: ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'],
  datasets: [{
    label: 'Jumlah Pendaftaran',
    data: [65, 58, 72, 68, 55, 45, 35],
    backgroundColor: 'rgba(59, 130, 246, 0.8)',
    borderColor: 'rgba(59, 130, 246, 1)',
    borderWidth: 2
  }]
}));

// Chart data untuk jenis pasien
const jenisPasienChartData = computed(() => ({
  labels: ['Pasien Baru', 'Pasien Lama', 'Pasien BPJS', 'Pasien Umum'],
  datasets: [{
    label: 'Jumlah Pasien',
    data: [23, 22, 15, 7],
    backgroundColor: [
      'rgba(34, 197, 94, 0.8)',
      'rgba(59, 130, 246, 0.8)',
      'rgba(245, 158, 11, 0.8)',
      'rgba(236, 72, 153, 0.8)'
    ],
    borderColor: [
      'rgba(34, 197, 94, 1)',
      'rgba(59, 130, 246, 1)',
      'rgba(245, 158, 11, 1)',
      'rgba(236, 72, 153, 1)'
    ],
    borderWidth: 2
  }]
}));

// Jam real-time
const currentTime = ref(new Date());
let timeInterval: NodeJS.Timeout | null = null;

onMounted(() => {
  timeInterval = setInterval(() => {
    currentTime.value = new Date();
  }, 1000);
});

onUnmounted(() => {
  if (timeInterval) {
    clearInterval(timeInterval);
  }
});

const formattedTime = computed(() => {
  return currentTime.value.toLocaleTimeString('id-ID', {
    hour: '2-digit',
    minute: '2-digit',
    second: '2-digit'
  });
});

const formattedDate = computed(() => {
  return currentTime.value.toLocaleDateString('id-ID', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
});

// Menu items untuk pendaftaran
const menuItems = ref([
  { 
    title: "Daftar Pasien", 
    desc: "Kelola data pasien", 
    icon: "👥", 
    color: "from-blue-400 via-blue-500 to-blue-700", 
    shadow: "shadow-blue-200", 
    glow: "shadow-blue-400/40", 
    gradient: "bg-gradient-to-br from-blue-400 via-blue-500 to-blue-700",
    route: "pasien.index"
  },
  { 
    title: "Tambah Pasien", 
    desc: "Registrasi pasien baru", 
    icon: "➕", 
    color: "from-green-400 via-green-500 to-green-700", 
    shadow: "shadow-green-200", 
    glow: "shadow-green-400/40", 
    gradient: "bg-gradient-to-br from-green-400 via-green-500 to-green-700",
    route: "pasien.create"
  },
  { 
    title: "Kunjungan", 
    desc: "Kelola kunjungan", 
    icon: "📅", 
    color: "from-purple-400 via-purple-500 to-purple-700", 
    shadow: "shadow-purple-200", 
    glow: "shadow-purple-400/40", 
    gradient: "bg-gradient-to-br from-purple-400 via-purple-500 to-purple-700",
    route: "pasien.kunjungan.with.transaksi.create"
  },
  { 
    title: "Laporan", 
    desc: "Statistik pendaftaran", 
    icon: "📊", 
    color: "from-pink-400 via-pink-500 to-pink-700", 
    shadow: "shadow-pink-200", 
    glow: "shadow-pink-400/40", 
    gradient: "bg-gradient-to-br from-pink-400 via-pink-500 to-pink-700",
    route: "pasien.index"
  },
]);

// Queue data
const queueData = ref([
  { no: 'A001', nama: 'Budi Santoso', poli: 'Poli Umum', status: 'Menunggu', waktu: '08:30' },
  { no: 'A002', nama: 'Siti Aminah', poli: 'Poli Gigi', status: 'Sedang Dilayani', waktu: '08:45' },
  { no: 'A003', nama: 'Ahmad Wijaya', poli: 'KIA', status: 'Menunggu', waktu: '09:00' },
  { no: 'A004', nama: 'Dewi Kartika', poli: 'Poli Umum', status: 'Menunggu', waktu: '09:15' },
  { no: 'A005', nama: 'Rudi Hartono', poli: 'Laboratorium', status: 'Menunggu', waktu: '09:30' },
]);
</script>

<template>
  <Head title="Dashboard Pendaftaran" />
  <AuthenticatedLayout>
    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50 relative overflow-x-hidden">
      <!-- Decorative SVG background -->
      <svg class="absolute top-0 left-0 w-full h-72 pointer-events-none opacity-40 z-0" viewBox="0 0 1440 320">
        <defs>
          <linearGradient id="svgGradient" x1="0" y1="0" x2="1" y2="1">
            <stop offset="0%" stop-color="#3b82f6" stop-opacity="0.4"/>
            <stop offset="100%" stop-color="#8b5cf6" stop-opacity="0.3"/>
          </linearGradient>
        </defs>
        <path fill="url(#svgGradient)" d="M0,160L60,170.7C120,181,240,203,360,197.3C480,192,600,160,720,133.3C840,107,960,85,1080,101.3C1200,117,1320,171,1380,197.3L1440,224L1440,0L1380,0C1320,0,1200,0,1080,0C960,0,840,0,720,0C600,0,480,0,360,0C240,0,120,0,60,0L0,0Z"></path>
      </svg>
      
      <!-- Header -->
      <header class="bg-white/80 shadow-md border-b backdrop-blur-md z-10 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="flex justify-between items-center py-6">
            <div>
              <h1 class="text-3xl font-extrabold text-blue-700 tracking-wide flex items-center gap-3">
                <i class="fas fa-clipboard-list text-blue-600"></i>
                Dashboard Pendaftaran
              </h1>
              <p class="text-gray-600 mt-2">Selamat datang, {{ user.name }}! Kelola pendaftaran pasien dengan efisien.</p>
            </div>
            <div class="text-right">
              <div class="text-2xl font-bold text-gray-900">{{ formattedTime }}</div>
              <div class="text-sm text-gray-600">{{ formattedDate }}</div>
            </div>
          </div>
        </div>
      </header>

      <!-- Main Content -->
      <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 relative z-10">
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-10">
          <StatCard
            title="Pendaftaran Hari Ini"
            :value="stats.pendaftaranHariIni"
            subtitle="pasien terdaftar"
            icon="📋"
            border-color="border-blue-500"
            icon-bg-color="bg-blue-100"
            :trend="trendData.pendaftaranHariIni"
            trend-color="text-green-600"
            class="hover:scale-105 transition-transform duration-300 shadow-2xl hover:shadow-blue-200/80 bg-gradient-to-br from-blue-50 via-white to-blue-100"
          />
          <StatCard
            title="Pendaftaran Bulan Ini"
            :value="stats.pendaftaranBulanIni.toLocaleString()"
            subtitle="total pendaftaran"
            icon="📊"
            border-color="border-green-500"
            icon-bg-color="bg-green-100"
            :trend="trendData.pendaftaranBulanIni"
            trend-color="text-green-600"
            class="hover:scale-105 transition-transform duration-300 shadow-2xl hover:shadow-green-200/80 bg-gradient-to-br from-green-50 via-white to-green-100"
          />
          <StatCard
            title="Pasien Baru"
            :value="stats.pasienBaru"
            subtitle="pasien baru hari ini"
            icon="🆕"
            border-color="border-purple-500"
            icon-bg-color="bg-purple-100"
            :trend="trendData.pasienBaru"
            trend-color="text-green-600"
            class="hover:scale-105 transition-transform duration-300 shadow-2xl hover:shadow-purple-200/80 bg-gradient-to-br from-purple-50 via-white to-purple-100"
          />
          <StatCard
            title="Pasien Lama"
            :value="stats.pasienLama"
            subtitle="pasien lama hari ini"
            icon="🔄"
            border-color="border-orange-500"
            icon-bg-color="bg-orange-100"
            :trend="trendData.pasienLama"
            trend-color="text-green-600"
            class="hover:scale-105 transition-transform duration-300 shadow-2xl hover:shadow-orange-200/80 bg-gradient-to-br from-orange-50 via-white to-orange-100"
          />
        </div>

        <!-- Charts and Queue Section -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-10">
          <!-- Pendaftaran per Hari Chart -->
          <div class="lg:col-span-2 bg-white rounded-2xl shadow-xl p-6 border border-gray-100">
            <div class="flex items-center justify-between mb-6">
              <h3 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                <i class="fas fa-chart-line text-blue-600"></i>
                Pendaftaran per Hari
              </h3>
            </div>
            <SimpleBarChart title="Pendaftaran per Hari" :data="pendaftaranChartData" />
          </div>

          <!-- Antrian Pasien -->
          <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100">
            <div class="flex items-center justify-between mb-6">
              <h3 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                <i class="fas fa-list-ol text-green-600"></i>
                Antrian Pasien
              </h3>
              <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                {{ queueData.length }} Pasien
              </span>
            </div>
            <div class="space-y-3 max-h-80 overflow-y-auto">
              <div 
                v-for="(item, index) in queueData" 
                :key="index"
                class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors"
              >
                <div class="flex items-center space-x-3">
                  <div class="w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center text-sm font-bold">
                    {{ item.no.slice(-1) }}
                  </div>
                  <div>
                    <p class="text-sm font-medium text-gray-900">{{ item.nama }}</p>
                    <p class="text-xs text-gray-500">{{ item.poli }}</p>
                  </div>
                </div>
                <div class="text-right">
                  <span 
                    :class="{
                      'bg-yellow-100 text-yellow-800': item.status === 'Menunggu',
                      'bg-green-100 text-green-800': item.status === 'Sedang Dilayani'
                    }"
                    class="text-xs font-medium px-2 py-1 rounded-full"
                  >
                    {{ item.status }}
                  </span>
                  <p class="text-xs text-gray-500 mt-1">{{ item.waktu }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Jenis Pasien Chart -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-10">
          <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100">
            <div class="flex items-center justify-between mb-6">
              <h3 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                <i class="fas fa-chart-pie text-purple-600"></i>
                Distribusi Jenis Pasien
              </h3>
            </div>
            <SimpleBarChart title="Jenis Pasien" :data="jenisPasienChartData" />
          </div>

          <!-- Quick Actions -->
          <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100">
            <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
              <i class="fas fa-bolt text-yellow-500"></i>
              Akses Cepat
            </h3>
            <div class="grid grid-cols-2 gap-4">
              <a
                v-for="item in menuItems"
                :key="item.title"
                :href="route(item.route)"
                class="group relative overflow-hidden rounded-xl p-4 text-white transition-all duration-300 hover:scale-105 hover:shadow-lg"
                :class="[item.gradient, item.shadow]"
              >
                <div class="absolute inset-0 bg-white/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <div class="relative z-10">
                  <div class="text-2xl mb-2">{{ item.icon }}</div>
                  <h4 class="text-sm font-bold">{{ item.title }}</h4>
                </div>
              </a>
            </div>
          </div>
        </div>

        <!-- Recent Activities -->
        <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
          <h3 class="text-2xl font-bold text-gray-900 mb-6 flex items-center gap-3">
            <i class="fas fa-history text-indigo-600"></i>
            Aktivitas Terbaru
          </h3>
          <div class="space-y-4">
            <div class="flex items-center p-4 bg-green-50 rounded-lg border-l-4 border-green-500">
              <div class="flex-shrink-0">
                <i class="fas fa-user-plus text-green-600 text-xl"></i>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-900">Pasien baru terdaftar: Budi Santoso</p>
                <p class="text-sm text-gray-500">Poli Umum - 14:30 WIB</p>
              </div>
            </div>
            <div class="flex items-center p-4 bg-blue-50 rounded-lg border-l-4 border-blue-500">
              <div class="flex-shrink-0">
                <i class="fas fa-calendar-plus text-blue-600 text-xl"></i>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-900">Kunjungan baru dibuat untuk: Siti Aminah</p>
                <p class="text-sm text-gray-500">Poli Gigi - 13:45 WIB</p>
              </div>
            </div>
            <div class="flex items-center p-4 bg-purple-50 rounded-lg border-l-4 border-purple-500">
              <div class="flex-shrink-0">
                <i class="fas fa-edit text-purple-600 text-xl"></i>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-900">Data pasien diperbarui: Ahmad Wijaya</p>
                <p class="text-sm text-gray-500">Informasi kontak - 12:20 WIB</p>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>
  </AuthenticatedLayout>
</template>

<style scoped>
/* Custom animations */
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fade-in-up {
  animation: fadeInUp 0.6s ease-out;
}

/* Hover effects */
.group:hover {
  transform: translateY(-2px);
}

/* Gradient text */
.gradient-text {
  background: linear-gradient(135deg, #3b82f6, #8b5cf6);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

/* Custom scrollbar */
.overflow-y-auto::-webkit-scrollbar {
  width: 4px;
}

.overflow-y-auto::-webkit-scrollbar-track {
  background: #f1f5f9;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
  background: #e2e8f0;
  border-radius: 2px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
  background: #cbd5e1;
}
</style>
