<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head } from '@inertiajs/vue3'
import { ref, computed, onMounted, onUnmounted } from 'vue'
import StatCard from '@/Components/StatCard.vue'
import SimpleBarChart from '@/Components/SimpleBarChart.vue'
import { useAuth } from '@/composables/useAuth'

const { user } = useAuth()

const props = defineProps<{
  stats?: Record<string, any>,
  transaksiChartData?: { labels?: string[], data?: number[] },
  pembayaranChartData?: { labels?: string[], data?: number[] },
  recentTransactions?: Array<Record<string, any>>
}>()

// Properly fallback trend data from stats or default if no stats
const trendData = computed(() => ({
  transaksiHariIni: props.stats?.trendTransaksiHariIni ?? 0,
  pendapatanHariIni: props.stats?.trendPendapatanHariIni ?? 0,
  transaksiBulanIni: props.stats?.trendTransaksiBulanIni ?? 0,
  pendapatanBulanIni: props.stats?.trendPendapatanBulanIni ?? 0
}))

// Safe chart data fallback
const transaksiChartDataComputed = computed(() => ({
  labels: props.transaksiChartData?.labels ?? ['08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00'],
  datasets: [{
    label: 'Jumlah Transaksi',
    data: props.transaksiChartData?.data ?? Array(9).fill(0),
    backgroundColor: 'rgba(34, 197, 94, 0.8)',
    borderColor: 'rgba(34, 197, 94, 1)',
    borderWidth: 2
  }]
}))

const pembayaranChartDataComputed = computed(() => ({
  labels: props.pembayaranChartData?.labels ?? ['Tunai', 'Debit', 'Kredit', 'BPJS', 'QRIS'],
  datasets: [{
    label: 'Jumlah Transaksi',
    data: props.pembayaranChartData?.data ?? Array(5).fill(0),
    backgroundColor: [
      'rgba(34, 197, 94, 0.8)',
      'rgba(59, 130, 246, 0.8)',
      'rgba(147, 51, 234, 0.8)',
      'rgba(245, 158, 11, 0.8)',
      'rgba(236, 72, 153, 0.8)'
    ],
    borderColor: [
      'rgba(34, 197, 94, 1)',
      'rgba(59, 130, 246, 1)',
      'rgba(147, 51, 234, 1)',
      'rgba(245, 158, 11, 1)',
      'rgba(236, 72, 153, 1)'
    ],
    borderWidth: 2
  }]
}))

// Jam dan tanggal real-time
const currentTime = ref<Date>(new Date())
let timeInterval: ReturnType<typeof setInterval> | null = null

onMounted(() => {
  timeInterval = setInterval(() => {
    currentTime.value = new Date()
  }, 1000)
})
onUnmounted(() => {
  if (timeInterval) clearInterval(timeInterval)
})

const formattedTime = computed(() =>
  currentTime.value.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' })
)
const formattedDate = computed(() =>
  currentTime.value.toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })
)

// Menu kasir - ensure all have unique titles and no duplicate routes
const menuItems = [
  {
    title: "Transaksi Baru",
    desc: "Buat transaksi baru",
    icon: "💳",
    gradient: "bg-gradient-to-br from-green-400 via-green-500 to-green-700",
    shadow: "shadow-green-200",
    route: "kasir.create"
  },
  {
    title: "Daftar Transaksi",
    desc: "Lihat semua transaksi",
    icon: "📋",
    gradient: "bg-gradient-to-br from-blue-400 via-blue-500 to-blue-700",
    shadow: "shadow-blue-200",
    route: "kasir.index"
  },
  {
    title: "Manajemen Pasien",
    desc: "Kelola data pasien",
    icon: "👥",
    gradient: "bg-gradient-to-br from-purple-400 via-purple-500 to-purple-700",
    shadow: "shadow-purple-200",
    route: "pasien.index"
  },
  {
    title: "Laporan Kasir",
    desc: "Statistik & laporan",
    icon: "📊",
    gradient: "bg-gradient-to-br from-pink-400 via-pink-500 to-pink-700",
    shadow: "shadow-pink-200",
    route: "kasir.dashboard"
  }
]

// Format currency helper
function formatCurrency(amount: number | null | undefined): string {
  if (amount == null || isNaN(amount)) return 'Rp 0'
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0
  }).format(amount)
}
</script>

<template>
  <Head title="Dashboard Kasir" />
  <AuthenticatedLayout>
    <div class="min-h-screen bg-gradient-to-br from-green-50 via-white to-blue-50 relative overflow-x-hidden">
      <!-- SVG background -->
      <svg class="absolute top-0 left-0 w-full h-72 pointer-events-none opacity-40 z-0" viewBox="0 0 1440 320">
        <defs>
          <linearGradient id="svgGradient" x1="0" y1="0" x2="1" y2="1">
            <stop offset="0%" stop-color="#10b981" stop-opacity="0.4"/>
            <stop offset="100%" stop-color="#3b82f6" stop-opacity="0.3"/>
          </linearGradient>
        </defs>
        <path fill="url(#svgGradient)" d="M0,160L60,170.7C120,181,240,203,360,197.3C480,192,600,160,720,133.3C840,107,960,85,1080,101.3C1200,117,1320,171,1380,197.3L1440,224L1440,0L1380,0C1320,0,1200,0,1080,0C960,0,840,0,720,0C600,0,480,0,360,0C240,0,120,0,60,0L0,0Z"></path>
      </svg>
      
      <header class="bg-white/80 shadow-md border-b backdrop-blur-md z-10 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="flex justify-between items-center py-6">
            <div>
              <h1 class="text-3xl font-extrabold text-green-700 tracking-wide flex items-center gap-3">
                <i class="fas fa-cash-register text-green-600"></i>
                Dashboard Kasir
              </h1>
              <p class="text-gray-600 mt-2">
                Selamat datang, {{ user?.name ?? '-' }}! Kelola transaksi dan pembayaran dengan efisien.
              </p>
            </div>
            <div class="text-right">
              <div class="text-2xl font-bold text-gray-900">{{ formattedTime }}</div>
              <div class="text-sm text-gray-600">{{ formattedDate }}</div>
            </div>
          </div>
        </div>
      </header>

      <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 relative z-10">
        <!-- Statistic Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-10">
          <StatCard
            title="Transaksi Hari Ini"
            :value="(props.stats?.transaksiHariIni ?? 0).toLocaleString()"
            subtitle="transaksi selesai"
            icon="💳"
            border-color="border-green-500"
            icon-bg-color="bg-green-100"
            :trend="trendData.transaksiHariIni"
            trend-color="text-green-600"
            class="hover:scale-105 transition-transform duration-300 shadow-2xl hover:shadow-green-200/80 bg-gradient-to-br from-green-50 via-white to-green-100"
          />
          <StatCard
            title="Pendapatan Hari Ini"
            :value="formatCurrency(props.stats?.pendapatanHariIni)"
            subtitle="total pendapatan"
            icon="💰"
            border-color="border-blue-500"
            icon-bg-color="bg-blue-100"
            :trend="trendData.pendapatanHariIni"
            trend-color="text-green-600"
            class="hover:scale-105 transition-transform duration-300 shadow-2xl hover:shadow-blue-200/80 bg-gradient-to-br from-blue-50 via-white to-blue-100"
          />
          <StatCard
            title="Transaksi Bulan Ini"
            :value="(props.stats?.transaksiBulanIni ?? 0).toLocaleString()"
            subtitle="total transaksi"
            icon="📊"
            border-color="border-purple-500"
            icon-bg-color="bg-purple-100"
            :trend="trendData.transaksiBulanIni"
            trend-color="text-green-600"
            class="hover:scale-105 transition-transform duration-300 shadow-2xl hover:shadow-purple-200/80 bg-gradient-to-br from-purple-50 via-white to-purple-100"
          />
          <StatCard
            title="Pendapatan Bulan Ini"
            :value="formatCurrency(props.stats?.pendapatanBulanIni)"
            subtitle="total pendapatan"
            icon="💎"
            border-color="border-orange-500"
            icon-bg-color="bg-orange-100"
            :trend="trendData.pendapatanBulanIni"
            trend-color="text-green-600"
            class="hover:scale-105 transition-transform duration-300 shadow-2xl hover:shadow-orange-200/80 bg-gradient-to-br from-orange-50 via-white to-orange-100"
          />
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-10">
          <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100">
            <div class="flex items-center justify-between mb-6">
              <h3 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                <i class="fas fa-clock text-green-600"></i>
                Transaksi per Jam
              </h3>
            </div>
            <SimpleBarChart title="Transaksi per Jam" :data="transaksiChartDataComputed" />
          </div>

          <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100">
            <div class="flex items-center justify-between mb-6">
              <h3 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                <i class="fas fa-credit-card text-blue-600"></i>
                Metode Pembayaran
              </h3>
            </div>
            <SimpleBarChart title="Metode Pembayaran" :data="pembayaranChartDataComputed as any" />
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-10">
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

          <!-- Recent Transactions -->
          <div class="lg:col-span-2 bg-white rounded-2xl shadow-xl p-6 border border-gray-100">
            <div class="flex items-center justify-between mb-6">
              <h3 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                <i class="fas fa-history text-indigo-600"></i>
                Transaksi Terbaru
              </h3>
              <a :href="route('kasir.index')" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                Lihat Semua
              </a>
            </div>
            <div class="space-y-3">
              <template v-if="props.recentTransactions && props.recentTransactions.length > 0">
                <div 
                  v-for="transaction in props.recentTransactions" 
                  :key="transaction.id"
                  class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors"
                >
                  <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-green-500 text-white rounded-full flex items-center justify-center">
                      <i class="fas fa-receipt text-sm"></i>
                    </div>
                    <div>
                      <p class="text-sm font-medium text-gray-900">{{ transaction.pasien ?? '-' }}</p>
                      <p class="text-xs text-gray-500">{{ transaction.id }}</p>
                    </div>
                  </div>
                  <div class="text-right">
                    <p class="text-sm font-bold text-gray-900">{{ formatCurrency(transaction.amount) }}</p>
                    <div class="flex items-center space-x-2 mt-1">
                      <span 
                        :class="{
                          'bg-green-100 text-green-800': transaction.method === 'Tunai',
                          'bg-blue-100 text-blue-800': transaction.method === 'Debit',
                          'bg-purple-100 text-purple-800': transaction.method === 'Kredit',
                          'bg-yellow-100 text-yellow-800': transaction.method === 'BPJS',
                          'bg-pink-100 text-pink-800': transaction.method === 'QRIS'
                        }"
                        class="text-xs font-medium px-2 py-1 rounded-full"
                      >
                        {{ transaction.method }}
                      </span>
                      <span class="text-xs text-gray-500">{{ transaction.time }}</span>
                    </div>
                  </div>
                </div>
              </template>
              <div v-else class="text-center py-8 text-gray-500">
                <i class="fas fa-inbox text-2xl mb-2"></i>
                <p class="text-sm">Tidak ada transaksi terbaru</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Daily Summary -->
        <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
          <h3 class="text-2xl font-bold text-gray-900 mb-6 flex items-center gap-3">
            <i class="fas fa-chart-bar text-green-600"></i>
            Ringkasan Hari Ini
          </h3>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="text-center p-6 bg-green-50 rounded-xl">
              <div class="text-3xl font-bold text-green-600 mb-2">{{ props.stats?.transaksiHariIni ?? 0 }}</div>
              <div class="text-sm text-gray-600">Total Transaksi</div>
            </div>
            <div class="text-center p-6 bg-blue-50 rounded-xl">
              <div class="text-3xl font-bold text-blue-600 mb-2">{{ formatCurrency(props.stats?.pendapatanHariIni) }}</div>
              <div class="text-sm text-gray-600">Total Pendapatan</div>
            </div>
            <div class="text-center p-6 bg-purple-50 rounded-xl">
              <div class="text-3xl font-bold text-purple-600 mb-2">{{
                formatCurrency(
                  (props.stats?.pendapatanHariIni ?? 0) /
                  (props.stats?.transaksiHariIni ? props.stats.transaksiHariIni : 1)
                )
              }}</div>
              <div class="text-sm text-gray-600">Rata-rata per Transaksi</div>
            </div>
          </div>
        </div>
      </main>
    </div>
  </AuthenticatedLayout>
</template>

<style scoped>
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

.group:hover {
  transform: translateY(-2px);
}

.gradient-text {
  background: linear-gradient(135deg, #10b981, #3b82f6);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}
</style>
