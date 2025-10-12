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
  totalPasien: 156,
  pasienHariIni: 23,
  konsultasiSelesai: 18,
  antrianMenunggu: 5
});

const trendData = ref({
  totalPasien: 12.5,
  pasienHariIni: 8.2,
  konsultasiSelesai: 15.3,
  antrianMenunggu: -5.0
});

// Chart data untuk pasien per poli
const poliChartData = computed(() => ({
  labels: ['Poli Umum', 'Poli Gigi', 'KIA', 'Laboratorium', 'Apotek'],
  datasets: [{
    label: 'Jumlah Pasien',
    data: [45, 32, 28, 25, 26],
    backgroundColor: [
      'rgba(59, 130, 246, 0.8)',
      'rgba(147, 51, 234, 0.8)',
      'rgba(236, 72, 153, 0.8)',
      'rgba(245, 158, 11, 0.8)',
      'rgba(34, 197, 94, 0.8)'
    ],
    borderColor: [
      'rgba(59, 130, 246, 1)',
      'rgba(147, 51, 234, 1)',
      'rgba(236, 72, 153, 1)',
      'rgba(245, 158, 11, 1)',
      'rgba(34, 197, 94, 1)'
    ],
    borderWidth: 2
  }]
}));

// Chart data untuk konsultasi per jam
const konsultasiChartData = computed(() => ({
  labels: ['08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00'],
  datasets: [{
    label: 'Konsultasi',
    data: [3, 5, 8, 6, 4, 7, 9, 6, 3],
    backgroundColor: 'rgba(34, 197, 94, 0.8)',
    borderColor: 'rgba(34, 197, 94, 1)',
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

// Menu items untuk dokter
const menuItems = ref([
  { 
    title: "Pasien Kunjungan", 
    desc: "Kelola kunjungan pasien", 
    icon: "👥", 
    color: "from-blue-400 via-blue-500 to-blue-700", 
    shadow: "shadow-blue-200", 
    glow: "shadow-blue-400/40", 
    gradient: "bg-gradient-to-br from-blue-400 via-blue-500 to-blue-700",
    route: "dokter.pasien-kunjungan"
  },
  { 
    title: "Poli & Layanan", 
    desc: "Akses poli medis", 
    icon: "🏥", 
    color: "from-green-400 via-green-500 to-green-700", 
    shadow: "shadow-green-200", 
    glow: "shadow-green-400/40", 
    gradient: "bg-gradient-to-br from-green-400 via-green-500 to-green-700",
    route: "dokter.poli_layanan.poli_umum"
  },
  { 
    title: "Master Data", 
    desc: "Kelola data medis", 
    icon: "📋", 
    color: "from-purple-400 via-purple-500 to-purple-700", 
    shadow: "shadow-purple-200", 
    glow: "shadow-purple-400/40", 
    gradient: "bg-gradient-to-br from-purple-400 via-purple-500 to-purple-700",
    route: "grp-eselon.index"
  },
  { 
    title: "Laporan Medis", 
    desc: "Statistik & laporan", 
    icon: "📊", 
    color: "from-pink-400 via-pink-500 to-pink-700", 
    shadow: "shadow-pink-200", 
    glow: "shadow-pink-400/40", 
    gradient: "bg-gradient-to-br from-pink-400 via-pink-500 to-pink-700",
    route: "dokter.pasien-kunjungan"
  },
]);
</script>

<template>
  <Head title="Dashboard Dokter" />
  <AuthenticatedLayout>
    <div class="min-h-screen bg-gradient-to-br from-green-50 via-white to-blue-50 relative overflow-x-hidden">
      <!-- Decorative SVG background -->
      <svg class="absolute top-0 left-0 w-full h-72 pointer-events-none opacity-40 z-0" viewBox="0 0 1440 320">
        <defs>
          <linearGradient id="svgGradient" x1="0" y1="0" x2="1" y2="1">
            <stop offset="0%" stop-color="#10b981" stop-opacity="0.4"/>
            <stop offset="100%" stop-color="#3b82f6" stop-opacity="0.3"/>
          </linearGradient>
        </defs>
        <path fill="url(#svgGradient)" d="M0,160L60,170.7C120,181,240,203,360,197.3C480,192,600,160,720,133.3C840,107,960,85,1080,101.3C1200,117,1320,171,1380,197.3L1440,224L1440,0L1380,0C1320,0,1200,0,1080,0C960,0,840,0,720,0C600,0,480,0,360,0C240,0,120,0,60,0L0,0Z"></path>
      </svg>
      
      <!-- Header -->
      <header class="bg-white/80 shadow-md border-b backdrop-blur-md z-10 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="flex justify-between items-center py-6">
            <div>
              <h1 class="text-3xl font-extrabold text-green-700 tracking-wide flex items-center gap-3">
                <i class="fas fa-user-md text-green-600"></i>
                Dashboard Dokter
              </h1>
              <p class="text-gray-600 mt-2">Selamat datang, {{ user.name }}! Kelola aktivitas medis Anda.</p>
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
            title="Total Pasien"
            :value="stats.totalPasien.toLocaleString()"
            subtitle="pasien bulan ini"
            icon="👥"
            border-color="border-blue-500"
            icon-bg-color="bg-blue-100"
            :trend="trendData.totalPasien"
            trend-color="text-green-600"
            class="hover:scale-105 transition-transform duration-300 shadow-2xl hover:shadow-blue-200/80 bg-gradient-to-br from-blue-50 via-white to-blue-100"
          />
          <StatCard
            title="Pasien Hari Ini"
            :value="stats.pasienHariIni"
            subtitle="kunjungan hari ini"
            icon="📅"
            border-color="border-green-500"
            icon-bg-color="bg-green-100"
            :trend="trendData.pasienHariIni"
            trend-color="text-green-600"
            class="hover:scale-105 transition-transform duration-300 shadow-2xl hover:shadow-green-200/80 bg-gradient-to-br from-green-50 via-white to-green-100"
          />
          <StatCard
            title="Konsultasi Selesai"
            :value="stats.konsultasiSelesai"
            subtitle="konsultasi hari ini"
            icon="✅"
            border-color="border-purple-500"
            icon-bg-color="bg-purple-100"
            :trend="trendData.konsultasiSelesai"
            trend-color="text-green-600"
            class="hover:scale-105 transition-transform duration-300 shadow-2xl hover:shadow-purple-200/80 bg-gradient-to-br from-purple-50 via-white to-purple-100"
          />
          <StatCard
            title="Antrian Menunggu"
            :value="stats.antrianMenunggu"
            subtitle="pasien menunggu"
            icon="⏳"
            border-color="border-orange-500"
            icon-bg-color="bg-orange-100"
            :trend="trendData.antrianMenunggu"
            trend-color="text-red-600"
            class="hover:scale-105 transition-transform duration-300 shadow-2xl hover:shadow-orange-200/80 bg-gradient-to-br from-orange-50 via-white to-orange-100"
          />
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-10">
          <!-- Pasien per Poli Chart -->
          <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100">
            <div class="flex items-center justify-between mb-6">
              <h3 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                <i class="fas fa-chart-pie text-green-600"></i>
                Distribusi Pasien per Poli
              </h3>
            </div>
            <SimpleBarChart :data="poliChartData" />
          </div>

          <!-- Konsultasi per Jam Chart -->
          <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100">
            <div class="flex items-center justify-between mb-6">
              <h3 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                <i class="fas fa-clock text-blue-600"></i>
                Konsultasi per Jam
              </h3>
            </div>
            <SimpleBarChart :data="konsultasiChartData" />
          </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
          <h3 class="text-2xl font-bold text-gray-900 mb-8 flex items-center gap-3">
            <i class="fas fa-bolt text-yellow-500"></i>
            Akses Cepat
          </h3>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <a
              v-for="item in menuItems"
              :key="item.title"
              :href="route(item.route)"
              class="group relative overflow-hidden rounded-2xl p-6 text-white transition-all duration-300 hover:scale-105 hover:shadow-2xl"
              :class="[item.gradient, item.shadow]"
            >
              <div class="absolute inset-0 bg-white/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
              <div class="relative z-10">
                <div class="text-4xl mb-4">{{ item.icon }}</div>
                <h4 class="text-xl font-bold mb-2">{{ item.title }}</h4>
                <p class="text-sm opacity-90">{{ item.desc }}</p>
              </div>
              <div class="absolute top-4 right-4 opacity-20 group-hover:opacity-40 transition-opacity duration-300">
                <i class="fas fa-arrow-right text-2xl"></i>
              </div>
            </a>
          </div>
        </div>

        <!-- Recent Activities -->
        <div class="mt-10 bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
          <h3 class="text-2xl font-bold text-gray-900 mb-6 flex items-center gap-3">
            <i class="fas fa-history text-indigo-600"></i>
            Aktivitas Terbaru
          </h3>
          <div class="space-y-4">
            <div class="flex items-center p-4 bg-green-50 rounded-lg border-l-4 border-green-500">
              <div class="flex-shrink-0">
                <i class="fas fa-check-circle text-green-600 text-xl"></i>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-900">Konsultasi dengan Pasien: Budi Santoso</p>
                <p class="text-sm text-gray-500">Poli Umum - 14:30 WIB</p>
              </div>
            </div>
            <div class="flex items-center p-4 bg-blue-50 rounded-lg border-l-4 border-blue-500">
              <div class="flex-shrink-0">
                <i class="fas fa-user-plus text-blue-600 text-xl"></i>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-900">Pasien baru terdaftar: Siti Aminah</p>
                <p class="text-sm text-gray-500">Poli Gigi - 13:45 WIB</p>
              </div>
            </div>
            <div class="flex items-center p-4 bg-purple-50 rounded-lg border-l-4 border-purple-500">
              <div class="flex-shrink-0">
                <i class="fas fa-prescription text-purple-600 text-xl"></i>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-900">Resep obat dibuat untuk: Ahmad Wijaya</p>
                <p class="text-sm text-gray-500">Poli Umum - 12:20 WIB</p>
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
  background: linear-gradient(135deg, #10b981, #3b82f6);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}
</style>
