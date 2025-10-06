<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3'
import { ref, computed, onMounted, onUnmounted } from 'vue'
import StatCard from '@/Components/StatCard.vue'
import SimpleBarChart from '@/Components/SimpleBarChart.vue'
import MultiBarChart from '@/Components/MultiBarChart.vue'

// Menu items
const menuKasir = ref([
  { title: "Penjualan", desc: "Transaksi & kasir", icon: "💳", color: "from-blue-400 via-blue-500 to-blue-700", shadow: "shadow-blue-200", glow: "shadow-blue-400/40", gradient: "bg-gradient-to-br from-blue-400 via-blue-500 to-blue-700" },
  { title: "Barang", desc: "Kelola stok & produk", icon: "📦", color: "from-green-400 via-green-500 to-green-700", shadow: "shadow-green-200", glow: "shadow-green-400/40", gradient: "bg-gradient-to-br from-green-400 via-green-500 to-green-700" },
  { title: "Laporan", desc: "Statistik & laporan", icon: "📊", color: "from-pink-400 via-pink-500 to-pink-700", shadow: "shadow-pink-200", glow: "shadow-pink-400/40", gradient: "bg-gradient-to-br from-pink-400 via-pink-500 to-pink-700" },
  { title: "Pengaturan", desc: "Akun & preferensi", icon: "⚙", color: "from-yellow-400 via-yellow-500 to-yellow-700", shadow: "shadow-yellow-200", glow: "shadow-yellow-400/40", gradient: "bg-gradient-to-br from-yellow-400 via-yellow-500 to-yellow-700" },
]);

// Sample data - dalam aplikasi nyata, ini akan diambil dari API
const stats = ref({
  obatMasuk: 1250,
  obatKeluar: 980,
  totalUser: 45,
  totalTransaksi: 320
});

// Jam real-time
const currentTime = ref(new Date());
let timeInterval: NodeJS.Timeout | null = null;

const trendData = ref({
  obatMasuk: 12.5,
  obatKeluar: -8.2,
  totalUser: 25.0,
  totalTransaksi: 15.3
});

// Chart data untuk obat masuk/keluar per bulan
const obatChartData = computed(() => ({
  labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
  datasets: [
    {
      label: 'Obat Masuk',
      data: [120, 150, 180, 200, 160, 220, 190, 210, 180, 250, 230, 280],
      backgroundColor: 'rgba(59, 130, 246, 0.8)',
      borderColor: 'rgba(59, 130, 246, 1)',
      borderWidth: 2
    },
    {
      label: 'Obat Keluar',
      data: [100, 130, 160, 180, 140, 200, 170, 190, 160, 220, 200, 250],
      backgroundColor: 'rgba(16, 185, 129, 0.8)',
      borderColor: 'rgba(16, 185, 129, 1)',
      borderWidth: 2
    }
  ]
}));

// Chart data untuk aktivitas pengguna
const userChartData = computed(() => ({
  labels: ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'],
  datasets: [
    {
      label: 'Pengguna Aktif',
      data: [25, 30, 28, 35, 32, 20, 15],
      backgroundColor: 'rgba(168, 85, 247, 0.8)',
      borderColor: 'rgba(168, 85, 247, 1)',
      borderWidth: 2
    }
  ]
}));

// Chart data untuk transaksi harian
const transaksiChartData = computed(() => ({
  labels: ['00:00', '04:00', '08:00', '12:00', '16:00', '20:00'],
  datasets: [
    {
      label: 'Jumlah Transaksi',
      data: [5, 8, 25, 45, 30, 15],
      backgroundColor: 'rgba(245, 158, 11, 0.8)',
      borderColor: 'rgba(245, 158, 11, 1)',
      borderWidth: 2
    }
  ]
}));

// Confetti animation
function confetti() {
  const colors = ['#3b82f6', '#10b981', '#f59e0b', '#a855f7', '#f43f5e', '#fbbf24', '#6366f1'];
  for (let i = 0; i < 40; i++) {
    const conf = document.createElement('div');
    conf.className = 'confetti';
    conf.style.left = Math.random() * 100 + 'vw';
    conf.style.background = colors[Math.floor(Math.random() * colors.length)];
    conf.style.animationDuration = 1.2 + Math.random() * 1.8 + 's';
    conf.style.width = 10 + Math.random() * 8 + 'px';
    conf.style.height = 10 + Math.random() * 8 + 'px';
    conf.style.borderRadius = Math.random() > 0.5 ? '50%' : '20%';
    document.body.appendChild(conf);
    setTimeout(() => conf.remove(), 3500);
  }
}
onMounted(() => {
  setTimeout(confetti, 400);
  
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
</script>

<template>
  <Head title="Dashboard" />
  <AuthenticatedLayout>
    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-green-50 relative overflow-x-hidden">
      <!-- Decorative SVG background -->
      <svg class="absolute top-0 left-0 w-full h-72 pointer-events-none opacity-40 z-0" viewBox="0 0 1440 320">
        <defs>
          <linearGradient id="svgGradient" x1="0" y1="0" x2="1" y2="1">
            <stop offset="0%" stop-color="#a5b4fc" stop-opacity="0.4"/>
            <stop offset="100%" stop-color="#6ee7b7" stop-opacity="0.3"/>
          </linearGradient>
        </defs>
        <path fill="url(#svgGradient)" d="M0,160L60,170.7C120,181,240,203,360,197.3C480,192,600,160,720,133.3C840,107,960,85,1080,101.3C1200,117,1320,171,1380,197.3L1440,224L1440,0L1380,0C1320,0,1200,0,1080,0C960,0,840,0,720,0C600,0,480,0,360,0C240,0,120,0,60,0L0,0Z"></path>
      </svg>
      <!-- Header -->
      <header class="bg-white/80 shadow-md border-b backdrop-blur-md z-10 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="flex flex-col md:flex-row justify-between items-center py-8 gap-4">
            <div>
              <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Dashboard</h1>
              <p class="text-gray-600 mt-2 text-lg flex items-center gap-2 font-medium">
                <span>Selamat datang di sistem manajemen klinik</span>
                <span class="animate-bounce text-2xl">🎉</span>
              </p>
            </div>
            <div class="flex items-center space-x-4">
              <div class="text-right">
                <p class="text-sm text-gray-500">Hari ini</p>
                <p class="text-2xl font-bold text-gray-900 animate-pulse">{{ new Date().toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }) }}</p>
                <p class="text-lg font-semibold text-gray-900 mt-1">{{ currentTime.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' }) }}</p>
              </div>
            </div>
          </div>
        </div>
      </header>

      <!-- Main Content -->
      <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 relative z-10">
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-10">
          <StatCard
            title="Obat Masuk"
            :value="stats.obatMasuk.toLocaleString()"
            subtitle="item bulan ini"
            icon="📦"
            border-color="border-blue-500"
            icon-bg-color="bg-blue-100"
            :trend="trendData.obatMasuk"
            trend-color="text-green-600"
            class="hover:scale-105 transition-transform duration-300 shadow-2xl hover:shadow-blue-200/80 bg-gradient-to-br from-blue-50 via-white to-blue-100"
          />
          <StatCard
            title="Obat Keluar"
            :value="stats.obatKeluar.toLocaleString()"
            subtitle="item bulan ini"
            icon="🚚"
            border-color="border-green-500"
            icon-bg-color="bg-green-100"
            :trend="trendData.obatKeluar"
            trend-color="text-red-600"
            class="hover:scale-105 transition-transform duration-300 shadow-2xl hover:shadow-green-200/80 bg-gradient-to-br from-green-50 via-white to-green-100"
          />
          <StatCard
            title="Total Pengguna"
            :value="stats.totalUser"
            subtitle="pengguna aktif"
            icon="👥"
            border-color="border-purple-500"
            icon-bg-color="bg-purple-100"
            :trend="trendData.totalUser"
            trend-color="text-green-600"
            class="hover:scale-105 transition-transform duration-300 shadow-2xl hover:shadow-purple-200/80 bg-gradient-to-br from-purple-50 via-white to-purple-100"
          />
          <StatCard
            title="Total Transaksi"
            :value="stats.totalTransaksi.toLocaleString()"
            subtitle="transaksi hari ini"
            icon="💳"
            border-color="border-yellow-500"
            icon-bg-color="bg-yellow-100"
            :trend="trendData.totalTransaksi"
            trend-color="text-green-600"
            class="hover:scale-105 transition-transform duration-300 shadow-2xl hover:shadow-yellow-200/80 bg-gradient-to-br from-yellow-50 via-white to-yellow-100"
          />
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 mb-10">
          <!-- Obat Masuk/Keluar Chart -->
          <div class="bg-white/95 rounded-3xl shadow-2xl p-8 hover:shadow-blue-100/80 transition-shadow duration-300 animate-fadein-up border border-blue-100/30">
            <MultiBarChart
              title="Statistik Obat Masuk & Keluar (Per Bulan)"
              :data="obatChartData"
            />
          </div>
          <!-- User Activity Chart -->
          <div class="bg-white/95 rounded-3xl shadow-2xl p-8 hover:shadow-purple-100/80 transition-shadow duration-300 animate-fadein-up border border-purple-100/30">
            <SimpleBarChart
              title="Aktivitas Pengguna (Per Hari)"
              :data="userChartData"
            />
          </div>
        </div>

        <!-- Transaksi Harian Chart -->
        <div class="mb-10 bg-white/95 rounded-3xl shadow-2xl p-8 hover:shadow-yellow-100/80 transition-shadow duration-300 animate-fadein-up border border-yellow-100/30">
          <SimpleBarChart
            title="Distribusi Transaksi Harian (Per Jam)"
            :data="transaksiChartData"
          />
        </div>

        <!-- Quick Actions Menu -->
        <div class="bg-gradient-to-br from-white via-blue-50 to-green-50 rounded-3xl shadow-2xl p-10 border border-blue-100/40 mb-10">
          <h3 class="text-2xl font-extrabold text-gray-900 mb-8 flex items-center gap-2 tracking-wide">
            <span>Menu Cepat</span>
            <span class="text-yellow-400 animate-spin-slow">⚡</span>
          </h3>
          <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-8">
            <div
              v-for="item in menuKasir"
              :key="item.title"
              class="group rounded-2xl p-7 text-center hover:shadow-2xl transition-all duration-300 cursor-pointer hover:-translate-y-2 border border-gray-200 hover:border-blue-300 relative overflow-hidden"
              :class="[item.glow, 'bg-white/80', 'hover:scale-105']"
            >
              <div :class="`w-20 h-20 mx-auto mb-5 flex items-center justify-center rounded-full ${item.gradient} text-white text-4xl shadow-lg group-hover:scale-110 transition-transform duration-200 ring-4 ring-white/40 animate-pop`">
                {{ item.icon }}
              </div>
              <h4 class="text-xl font-bold text-gray-800 mb-2 group-hover:text-blue-600 transition-colors duration-200 tracking-wide">
                {{ item.title }}
              </h4>
              <p class="text-base text-gray-600">{{ item.desc }}</p>
              <span class="absolute -top-2 -right-2 text-xs bg-gradient-to-r from-blue-400 to-green-400 text-white px-2 py-0.5 rounded-full shadow group-hover:scale-110 transition-transform duration-200" v-if="item.title === 'Penjualan'">Baru!</span>
            </div>
          </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-gradient-to-br from-white via-purple-50 to-blue-50 rounded-3xl shadow-2xl p-10 border border-purple-100/40">
          <h3 class="text-2xl font-extrabold text-gray-900 mb-8 flex items-center gap-2 tracking-wide">
            <span>Aktivitas Terbaru</span>
            <span class="animate-pulse text-pink-400">🕒</span>
          </h3>
          <div class="space-y-5">
            <div class="flex items-center space-x-5 p-5 bg-blue-50/90 rounded-xl shadow hover:scale-105 transition-transform duration-200 border-l-4 border-blue-400/60">
              <div class="w-14 h-14 bg-blue-500 rounded-full flex items-center justify-center shadow-lg animate-pop">
                <span class="text-white text-2xl">📦</span>
              </div>
              <div class="flex-1">
                <p class="text-lg font-semibold text-gray-900">Stok obat diperbarui</p>
                <p class="text-xs text-gray-500">2 menit yang lalu</p>
              </div>
            </div>
            <div class="flex items-center space-x-5 p-5 bg-green-50/90 rounded-xl shadow hover:scale-105 transition-transform duration-200 border-l-4 border-green-400/60">
              <div class="w-14 h-14 bg-green-500 rounded-full flex items-center justify-center shadow-lg animate-pop">
                <span class="text-white text-2xl">💳</span>
              </div>
              <div class="flex-1">
                <p class="text-lg font-semibold text-gray-900">Transaksi baru sebesar <span class="text-green-600 font-bold">Rp 150.000</span></p>
                <p class="text-xs text-gray-500">5 menit yang lalu</p>
              </div>
            </div>
            <div class="flex items-center space-x-5 p-5 bg-purple-50/90 rounded-xl shadow hover:scale-105 transition-transform duration-200 border-l-4 border-purple-400/60">
              <div class="w-14 h-14 bg-purple-500 rounded-full flex items-center justify-center shadow-lg animate-pop">
                <span class="text-white text-2xl">👤</span>
              </div>
              <div class="flex-1">
                <p class="text-lg font-semibold text-gray-900">User baru mendaftar</p>
                <p class="text-xs text-gray-500">10 menit yang lalu</p>
              </div>
            </div>
          </div>
        </div>
        <!-- Confetti container (for animation) -->
        <div class="pointer-events-none fixed inset-0 z-50"></div>
      </main>
    </div>
  </AuthenticatedLayout>
</template>

<style scoped>
/* Custom animations for cards */
.group:hover .group-hover\:scale-110 {
  transform: scale(1.1);
}
@keyframes pop {
  0% { transform: scale(0.7); opacity: 0.5; }
  60% { transform: scale(1.15); opacity: 1; }
  100% { transform: scale(1); }
}
.animate-pop {
  animation: pop 0.5s cubic-bezier(.68,-0.55,.27,1.55);
}
@keyframes fadein {
  from { opacity: 0; transform: translateY(-16px);}
  to { opacity: 1; transform: translateY(0);}
}
.animate-fadein {
  animation: fadein 1s ease;
}
@keyframes fadein-up {
  from { opacity: 0; transform: translateY(32px);}
  to { opacity: 1; transform: translateY(0);}
}
.animate-fadein-up {
  animation: fadein-up 1s cubic-bezier(.68,-0.55,.27,1.55);
}
@keyframes spin-slow {
  0% { transform: rotate(0deg);}
  100% { transform: rotate(360deg);}
}
.animate-spin-slow {
  animation: spin-slow 2.5s linear infinite;
}
.confetti {
  position: fixed;
  top: -20px;
  width: 14px;
  height: 14px;
  border-radius: 50%;
  opacity: 0.85;
  z-index: 9999;
  animation: confetti-fall linear forwards;
  filter: blur(0.5px);
}
@keyframes confetti-fall {
  to {
    transform: translateY(110vh) rotate(360deg);
    opacity: 0.2;
  }
}
</style>
