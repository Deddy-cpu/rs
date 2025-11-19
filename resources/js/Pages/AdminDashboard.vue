<script setup lang="ts">
import { useAuth } from '@/composables/useAuth';
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import StatCard from '@/Components/StatCard.vue';
import SimpleBarChart from '@/Components/SimpleBarChart.vue';

const { user } = useAuth();
const page = usePage();

// Props from backend
const props = defineProps({
  stats: {
    type: Object,
    default: () => ({})
  },
  totalPasien: Number,
  pasienBulanIni: Number,
  totalKunjungan: Number,
  kunjunganHariIni: Number,
  kunjunganBulanIni: Number,
  totalTransaksi: Number,
  transaksiHariIni: Number,
  pendapatanHariIni: Number,
  pendapatanBulanIni: Number,
  distribusiPoli: {
    type: Array,
    default: () => []
  },
  konsultasiPerJam: {
    type: Array,
    default: () => []
  },
  transaksiPerJam: {
    type: Array,
    default: () => []
  },
  pendaftaranPerHari: {
    type: Array,
    default: () => []
  },
  recentKunjungan: {
    type: Array,
    default: () => []
  },
});

// Chart data untuk distribusi pasien per poli
const poliChartData = computed(() => {
  const labels = props.distribusiPoli && props.distribusiPoli.length > 0
    ? props.distribusiPoli.map((item: any) => item.poli)
    : ['Poli Umum', 'Poli Gigi', 'KIA', 'Laboratorium', 'Apotek'];
  const data = props.distribusiPoli && props.distribusiPoli.length > 0
    ? props.distribusiPoli.map((item: any) => item.total)
    : [0, 0, 0, 0, 0];
  
  return {
    labels,
    datasets: [{
      label: 'Jumlah Pasien',
      data,
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
  };
});

// Chart data untuk konsultasi per jam
const konsultasiChartData = computed(() => {
  const defaultLabels = ['08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00'];
  const labels = props.konsultasiPerJam && props.konsultasiPerJam.length > 0
    ? props.konsultasiPerJam.map((item: any) => item.jam || item)
    : defaultLabels;
  const data = props.konsultasiPerJam && props.konsultasiPerJam.length > 0
    ? props.konsultasiPerJam.map((item: any) => typeof item === 'object' ? item.total : item)
    : Array(defaultLabels.length).fill(0);
  
  return {
    labels,
    datasets: [{
      label: 'Konsultasi',
      data,
      backgroundColor: 'rgba(34, 197, 94, 0.8)',
      borderColor: 'rgba(34, 197, 94, 1)',
      borderWidth: 2
    }]
  };
});

// Chart data untuk transaksi per jam
const transaksiChartData = computed(() => {
  const defaultLabels = ['08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00'];
  const labels = props.transaksiPerJam && props.transaksiPerJam.length > 0
    ? props.transaksiPerJam.map((item: any) => item.jam || item)
    : defaultLabels;
  const data = props.transaksiPerJam && props.transaksiPerJam.length > 0
    ? props.transaksiPerJam.map((item: any) => typeof item === 'object' ? item.total : item)
    : Array(defaultLabels.length).fill(0);
  
  return {
    labels,
    datasets: [{
      label: 'Transaksi',
      data,
      backgroundColor: 'rgba(59, 130, 246, 0.8)',
      borderColor: 'rgba(59, 130, 246, 1)',
      borderWidth: 2
    }]
  };
});

// Format currency
function formatCurrency(amount: number | undefined | null): string {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0
  }).format(amount ?? 0);
}
</script>

<template>
  <Head title="Admin Dashboard - Monitoring" />
  <AuthenticatedLayout>
    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-50 relative overflow-x-hidden">
      <!-- Decorative SVG background -->
      <svg class="absolute top-0 left-0 w-full h-72 pointer-events-none opacity-40 z-0" viewBox="0 0 1440 320">
        <defs>
          <linearGradient id="svgGradient" x1="0" y1="0" x2="1" y2="1">
            <stop offset="0%" stop-color="#3b82f6" stop-opacity="0.4"/>
            <stop offset="100%" stop-color="#6366f1" stop-opacity="0.3"/>
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
                <i class="fas fa-shield-alt text-blue-600"></i>
                Admin Dashboard - Monitoring
              </h1>
              <p class="text-gray-600 mt-2">Selamat datang, {{ user.name }}! Dashboard monitoring sistem klinik (Read-Only).</p>
              <div class="mt-2">
                <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-xs font-medium">
                  <i class="fas fa-eye mr-1"></i>
                  Mode Monitoring - Hanya Lihat
                </span>
              </div>
            </div>
          </div>
        </div>
      </header>

      <!-- Main Content -->
      <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 relative z-10">
        <!-- User Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
          <div class="bg-blue-50 rounded-lg p-6 border border-blue-200 hover:shadow-md transition-shadow">
            <div class="flex items-center">
              <div class="p-3 bg-blue-500 rounded-full">
                <i class="fas fa-users text-white text-lg"></i>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Total Users</p>
                <p class="text-2xl font-semibold text-gray-900">{{ props.stats?.totalUsers || 0 }}</p>
              </div>
            </div>
          </div>

          <div class="bg-green-50 rounded-lg p-6 border border-green-200 hover:shadow-md transition-shadow">
            <div class="flex items-center">
              <div class="p-3 bg-green-500 rounded-full">
                <i class="fas fa-user-check text-white text-lg"></i>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Active Users</p>
                <p class="text-2xl font-semibold text-gray-900">{{ props.stats?.activeUsers || 0 }}</p>
              </div>
            </div>
          </div>

          <div class="bg-yellow-50 rounded-lg p-6 border border-yellow-200 hover:shadow-md transition-shadow">
            <div class="flex items-center">
              <div class="p-3 bg-yellow-500 rounded-full">
                <i class="fas fa-user-md text-white text-lg"></i>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Doctors</p>
                <p class="text-2xl font-semibold text-gray-900">{{ props.stats?.doctors || 0 }}</p>
              </div>
            </div>
          </div>

          <div class="bg-purple-50 rounded-lg p-6 border border-purple-200 hover:shadow-md transition-shadow">
            <div class="flex items-center">
              <div class="p-3 bg-purple-500 rounded-full">
                <i class="fas fa-cash-register text-white text-lg"></i>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Cashiers</p>
                <p class="text-2xl font-semibold text-gray-900">{{ props.stats?.cashiers || 0 }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- System Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
          <StatCard
            title="Total Pasien"
            :value="(props.totalPasien || 0).toLocaleString()"
            subtitle="pasien terdaftar"
            icon="👥"
            border-color="border-blue-500"
            icon-bg-color="bg-blue-100"
            class="hover:scale-105 transition-transform duration-300 shadow-2xl hover:shadow-blue-200/80 bg-gradient-to-br from-blue-50 via-white to-blue-100"
          />
          <StatCard
            title="Kunjungan Hari Ini"
            :value="(props.kunjunganHariIni || 0).toLocaleString()"
            subtitle="kunjungan hari ini"
            icon="📅"
            border-color="border-green-500"
            icon-bg-color="bg-green-100"
            class="hover:scale-105 transition-transform duration-300 shadow-2xl hover:shadow-green-200/80 bg-gradient-to-br from-green-50 via-white to-green-100"
          />
          <StatCard
            title="Transaksi Hari Ini"
            :value="(props.transaksiHariIni || 0).toLocaleString()"
            subtitle="transaksi hari ini"
            icon="💳"
            border-color="border-purple-500"
            icon-bg-color="bg-purple-100"
            class="hover:scale-105 transition-transform duration-300 shadow-2xl hover:shadow-purple-200/80 bg-gradient-to-br from-purple-50 via-white to-purple-100"
          />
          <StatCard
            title="Pendapatan Hari Ini"
            :value="formatCurrency(props.pendapatanHariIni)"
            subtitle="total pendapatan"
            icon="💰"
            border-color="border-orange-500"
            icon-bg-color="bg-orange-100"
            class="hover:scale-105 transition-transform duration-300 shadow-2xl hover:shadow-orange-200/80 bg-gradient-to-br from-orange-50 via-white to-orange-100"
          />
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-10">
          <!-- Distribusi Pasien per Poli Chart -->
          <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100">
            <div class="flex items-center justify-between mb-6">
              <h3 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                <i class="fas fa-chart-pie text-green-600"></i>
                Distribusi Pasien per Poli
              </h3>
            </div>
            <SimpleBarChart title="Distribusi Pasien per Poli" :data="poliChartData as any" />
            <div class="mt-3 flex flex-wrap gap-2">
              <div 
                v-for="(item, index) in props.distribusiPoli" 
                :key="index"
                class="flex items-center gap-1 px-2 py-1 rounded"
                :class="{
                  'bg-green-100': item.naik,
                  'bg-red-100': item.turun,
                  'bg-gray-100': item.selisih === 0
                }"
              >
                <span class="text-xs font-medium text-gray-700">{{ item.poli }}</span>
                <span 
                  v-if="item.selisih !== 0"
                  :class="{
                    'text-green-600': item.naik,
                    'text-red-600': item.turun
                  }"
                  class="text-xs font-bold"
                >
                  <i :class="item.naik ? 'fas fa-arrow-up' : 'fas fa-arrow-down'"></i>
                  {{ item.selisih > 0 ? '+' : '' }}{{ item.selisih }}
                </span>
              </div>
            </div>
          </div>

          <!-- Konsultasi per Jam Chart -->
          <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100">
            <div class="flex items-center justify-between mb-6">
              <h3 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                <i class="fas fa-clock text-blue-600"></i>
                Konsultasi per Jam
              </h3>
            </div>
            <SimpleBarChart title="Konsultasi per Jam" :data="konsultasiChartData" />
            <div class="mt-3 flex flex-wrap gap-1.5">
              <div 
                v-for="(item, index) in props.konsultasiPerJam" 
                :key="index"
                class="flex items-center gap-1 px-2 py-0.5 rounded text-xs"
                :class="{
                  'bg-green-100': item.naik,
                  'bg-red-100': item.turun,
                  'bg-gray-100': item.selisih === 0
                }"
              >
                <span class="text-gray-600">{{ item.jam }}</span>
                <span 
                  v-if="item.selisih !== 0"
                  :class="{
                    'text-green-600': item.naik,
                    'text-red-600': item.turun
                  }"
                  class="font-bold"
                >
                  <i :class="item.naik ? 'fas fa-arrow-up' : 'fas fa-arrow-down'"></i>
                  {{ item.selisih > 0 ? '+' : '' }}{{ item.selisih }}
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Transaksi per Jam Chart -->
        <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100 mb-10">
          <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-gray-900 flex items-center gap-2">
              <i class="fas fa-chart-line text-purple-600"></i>
              Transaksi per Jam
            </h3>
          </div>
          <SimpleBarChart title="Transaksi per Jam" :data="transaksiChartData" />
          <div class="mt-3 flex flex-wrap gap-1.5">
            <div 
              v-for="(item, index) in props.transaksiPerJam" 
              :key="index"
              class="flex items-center gap-1 px-2 py-0.5 rounded text-xs"
              :class="{
                'bg-green-100': item.naik,
                'bg-red-100': item.turun,
                'bg-gray-100': item.selisih === 0
              }"
            >
              <span class="text-gray-600">{{ item.jam }}</span>
              <span 
                v-if="item.selisih !== 0"
                :class="{
                  'text-green-600': item.naik,
                  'text-red-600': item.turun
                }"
                class="font-bold"
              >
                <i :class="item.naik ? 'fas fa-arrow-up' : 'fas fa-arrow-down'"></i>
                {{ item.selisih > 0 ? '+' : '' }}{{ item.selisih }}
              </span>
            </div>
          </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-200 mb-6">
          <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <i class="fas fa-bolt mr-2 text-yellow-500"></i>
            Quick Actions
          </h3>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <Link
              :href="route('users.index')"
              class="flex items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors"
            >
              <div class="p-2 bg-blue-500 rounded-lg mr-3">
                <i class="fas fa-users text-white"></i>
              </div>
              <div>
                <p class="font-medium text-gray-900">Manage Users</p>
                <p class="text-sm text-gray-600">Kelola pengguna sistem</p>
              </div>
            </Link>

            <Link
              :href="route('tindakanq.index')"
              class="flex items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors"
            >
              <div class="p-2 bg-green-500 rounded-lg mr-3">
                <i class="fas fa-stethoscope text-white"></i>
              </div>
              <div>
                <p class="font-medium text-gray-900">Medical Procedures</p>
                <p class="text-sm text-gray-600">Kelola prosedur medis</p>
              </div>
            </Link>

            <Link
              :href="route('farmalkes.index')"
              class="flex items-center p-4 bg-yellow-50 rounded-lg hover:bg-yellow-100 transition-colors"
            >
              <div class="p-2 bg-yellow-500 rounded-lg mr-3">
                <i class="fas fa-pills text-white"></i>
              </div>
              <div>
                <p class="font-medium text-gray-900">Pharmacy & Equipment</p>
                <p class="text-sm text-gray-600">Kelola farmasi & alat</p>
              </div>
            </Link>

            <Link
              :href="route('kasir.index')"
              class="flex items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors"
            >
              <div class="p-2 bg-purple-500 rounded-lg mr-3">
                <i class="fas fa-cash-register text-white"></i>
              </div>
              <div>
                <p class="font-medium text-gray-900">Cashier Management</p>
                <p class="text-sm text-gray-600">Kelola transaksi</p>
              </div>
            </Link>
          </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-200">
          <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <i class="fas fa-history mr-2 text-indigo-500"></i>
            Aktivitas Terbaru
          </h3>
          <div class="space-y-4">
            <div 
              v-for="(activity, index) in props.recentKunjungan" 
              :key="index"
              class="flex items-center p-3 bg-gray-50 rounded-lg"
            >
              <div class="p-2 bg-blue-100 rounded-full mr-3">
                <i class="fas fa-user-injured text-blue-600 text-sm"></i>
              </div>
              <div class="flex-1">
                <p class="text-sm font-medium text-gray-900">Kunjungan: {{ activity['pasien'] }}</p>
                <p class="text-xs text-gray-500">{{ activity['poli'] }} - {{ activity['waktu'] }} WIB</p>
              </div>
            </div>
            <div v-if="!props.recentKunjungan || props.recentKunjungan.length === 0" class="text-center py-8 text-gray-500">
              <i class="fas fa-inbox text-4xl mb-2"></i>
              <p>Tidak ada aktivitas terbaru</p>
            </div>
          </div>
        </div>
      </main>
    </div>
  </AuthenticatedLayout>
</template>
