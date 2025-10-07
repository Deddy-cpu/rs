<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue"
import { Head, Link, router } from "@inertiajs/vue3"
import { computed } from "vue"

const props = defineProps({
  pasien: Object,
  flash: {
    type: Object,
    default: () => ({})
  }
})

// Helper to convert ISO date string to readable format
function formatDate(dateStr) {
  if (!dateStr) return '-'
  if (/^\d{4}-\d{2}-\d{2}$/.test(dateStr)) return dateStr
  const d = new Date(dateStr)
  if (isNaN(d.getTime())) return '-'
  return d.toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

// Helper to extract numeric value from string like "50000 RP"
const toNumber = (val) => {
  if (!val) return 0
  const s = String(val).replace(/[^0-9]/g, '')
  return Number(s || 0)
}

// Helper to format currency
const formatCurrency = (amount) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0
  }).format(amount)
}

// Calculate totals for each category
const totalKonsul = computed(() => {
  return (props.pasien?.konsuls || []).reduce((sum, item) => sum + toNumber(item.st_kons), 0)
})

const totalTindak = computed(() => {
  return (props.pasien?.tindaks || []).reduce((sum, item) => sum + toNumber(item.st_tindak), 0)
})

const totalAlkes = computed(() => {
  return (props.pasien?.alkes || []).reduce((sum, item) => sum + toNumber(item.st_alkes), 0)
})

const totalRsp = computed(() => {
  return (props.pasien?.rsp || []).reduce((sum, item) => sum + toNumber(item.st_rsp), 0)
})

const totalLainnya = computed(() => {
  return (props.pasien?.lainnyas || []).reduce((sum, item) => sum + toNumber(item.st_lainnya), 0)
})

// Grand total
const grandTotal = computed(() => {
  return totalKonsul.value + totalTindak.value + totalAlkes.value + totalRsp.value + totalLainnya.value
})

const printPdf = () => {
  window.open(route('kasir.pdf', props.pasien.id), '_blank')
}
</script>

<template>
  <AuthenticatedLayout>
    <Head :title="`Detail Pasien - ${pasien?.nm_p || ''}`" />

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

      <!-- Header -->
      <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center mb-8 gap-4">
        <div>
          <h1 class="text-3xl font-bold text-gray-900 flex items-center">
            <i class="fas fa-user mr-3 text-blue-600"></i>
            Detail Pasien
          </h1>
          <p class="text-gray-600 mt-1">Informasi lengkap pasien dan transaksi medis</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-3">
          <Link 
            :href="route('kasir.edit', pasien.id)" 
            class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg shadow transition flex items-center justify-center"
          >
            <i class="fas fa-edit mr-2"></i>
            Edit Pasien
          </Link>
          <button 
            @click="printPdf" 
            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow transition flex items-center justify-center"
          >
            <i class="fas fa-print mr-2"></i>
            Print PDF
          </button>
        </div>
      </div>

      <!-- Info Pasien -->
      <!-- (bagian detail pasien, tabel konsultasi, tindakan, alkes, resep, lainnya — tetap sama kayak yang kamu tulis) -->

      <!-- Grand Total -->
      <div class="bg-gradient-to-r from-blue-100 to-indigo-100 shadow-lg rounded-xl p-8 mb-8">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
          <h3 class="text-2xl font-bold text-blue-800 flex items-center">
            <i class="fas fa-calculator mr-3"></i>
            Grand Total
          </h3>
          <div class="text-right">
            <p class="text-3xl font-extrabold text-blue-900 tracking-wider">
              {{ formatCurrency(grandTotal) }}
            </p>
            <p class="text-sm text-blue-600 mt-1">Total semua layanan medis</p>
          </div>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
        <Link 
          :href="route('kasir.index')" 
          class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition flex items-center"
        >
          <i class="fas fa-arrow-left mr-2"></i>
          Kembali ke Daftar
        </Link>
        
        <div class="flex gap-3">
          <Link 
            :href="route('kasir.edit', pasien.id)" 
            class="px-6 py-3 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg font-semibold transition flex items-center"
          >
            <i class="fas fa-edit mr-2"></i>
            Edit Data
          </Link>
          <button 
            @click="printPdf" 
            class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold shadow transition flex items-center"
          >
            <i class="fas fa-print mr-2"></i>
            Print PDF
          </button>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
