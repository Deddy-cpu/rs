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

<<<<<<< HEAD
     <div
      class="min-h-screen bg-cover bg-center flex items-center justify-center p-6"
      style="background-image: url('/images/bg-login.png')"
    >

    <div class="bg-white/70 backdrop-blur-sm rounded-xl p-6 shadow-lg w-full">
    <div class="flex justify-center items-center mb-8">
          <h2 class="text-3xl font-extrabold text-blue-700 tracking-tight">
    Edit Data Pasien
  </h2>
=======
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
>>>>>>> daf935dd1c37511b1a31fbb7777979201442d99a
      </div>

      <!-- Info Pasien -->
      <div class="bg-gradient-to-r from-blue-50 to-indigo-50 shadow-lg rounded-xl p-8 mb-8">
        <h2 class="text-2xl font-semibold text-gray-800 mb-6 flex items-center">
          <i class="fas fa-info-circle mr-3 text-blue-600"></i>
          Informasi Pasien
        </h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div class="space-y-2">
            <label class="text-sm font-medium text-gray-500">Nama Pasien</label>
            <p class="text-lg font-semibold text-gray-900">{{ pasien?.nm_p }}</p>
          </div>
          
          <div class="space-y-2">
            <label class="text-sm font-medium text-gray-500">No Registrasi</label>
            <p class="text-lg font-semibold text-gray-900">{{ pasien?.no_reg }}</p>
          </div>
          
          <div class="space-y-2">
            <label class="text-sm font-medium text-gray-500">MRN</label>
            <p class="text-lg font-semibold text-gray-900">{{ pasien?.mrn }}</p>
          </div>
          
          <div class="space-y-2">
            <label class="text-sm font-medium text-gray-500">Tanggal Registrasi</label>
            <p class="text-lg font-semibold text-gray-900">{{ formatDate(pasien?.tgl_reg) }}</p>
          </div>
          
          <div class="space-y-2">
            <label class="text-sm font-medium text-gray-500">Jenis Perawatan</label>
            <p class="text-lg font-semibold text-gray-900">{{ pasien?.perawatan }}</p>
          </div>
          
          <div class="space-y-2">
            <label class="text-sm font-medium text-gray-500">Penjamin</label>
            <p class="text-lg font-semibold text-gray-900">{{ pasien?.penjamin }}</p>
          </div>
          
          <div class="space-y-2">
            <label class="text-sm font-medium text-gray-500">No Invoice</label>
            <p class="text-lg font-semibold text-gray-900">{{ pasien?.no_inv || '-' }}</p>
          </div>
          
          <div class="space-y-2">
            <label class="text-sm font-medium text-gray-500">Tanggal Invoice</label>
            <p class="text-lg font-semibold text-gray-900">{{ formatDate(pasien?.tgl_inv) }}</p>
          </div>
          
          <div class="space-y-2">
            <label class="text-sm font-medium text-gray-500">No SJP</label>
            <p class="text-lg font-semibold text-gray-900">{{ pasien?.no_sjp || '-' }}</p>
          </div>
          
          <div class="space-y-2 md:col-span-2 lg:col-span-3">
            <label class="text-sm font-medium text-gray-500">Alamat</label>
            <p class="text-lg font-semibold text-gray-900">{{ pasien?.almt_B }}</p>
          </div>
          
          <div class="space-y-2 md:col-span-2 lg:col-span-3">
            <label class="text-sm font-medium text-gray-500">ICD Code</label>
            <p class="text-lg font-semibold text-gray-900">{{ pasien?.icd || '-' }}</p>
          </div>
        </div>
      </div>

      <!-- Konsultasi -->
      <div v-if="pasien?.konsuls?.length" class="bg-white shadow-lg rounded-xl p-8 mb-8">
        <div class="flex justify-between items-center mb-6">
          <h3 class="text-2xl font-semibold text-gray-800 flex items-center">
            <i class="fas fa-stethoscope mr-3 text-blue-600"></i>
            Konsultasi Medis
          </h3>
          <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
            Total: {{ formatCurrency(totalKonsul) }}
          </span>
        </div>
        
        <div class="overflow-x-auto">
          <table class="w-full text-sm border border-gray-200 rounded-lg overflow-hidden">
            <thead class="bg-blue-100">
              <tr>
                <th class="px-4 py-3 text-left text-blue-800 font-semibold border-b">Dokter</th>
                <th class="px-4 py-3 text-left text-blue-800 font-semibold border-b">Deskripsi</th>
                <th class="px-4 py-3 text-center text-blue-800 font-semibold border-b">Jumlah</th>
                <th class="px-4 py-3 text-right text-blue-800 font-semibold border-b">Biaya</th>
                <th class="px-4 py-3 text-right text-blue-800 font-semibold border-b">Diskon</th>
                <th class="px-4 py-3 text-right text-blue-800 font-semibold border-b">Subtotal</th>
                <th class="px-4 py-3 text-center text-blue-800 font-semibold border-b">Tanggal</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(item, index) in pasien.konsuls" :key="index" class="hover:bg-blue-50 transition">
                <td class="px-4 py-3 text-gray-700 border-b">{{ item.dokter }}</td>
                <td class="px-4 py-3 text-gray-700 border-b">{{ item.dskp_kons }}</td>
                <td class="px-4 py-3 text-center text-gray-700 border-b">{{ item.jmlh_kons }}</td>
                <td class="px-4 py-3 text-right text-gray-700 border-b">{{ item.bya_kons }}</td>
                <td class="px-4 py-3 text-right text-gray-700 border-b">{{ item.disc_kons }}</td>
                <td class="px-4 py-3 text-right font-semibold text-blue-700 border-b">{{ item.st_kons }}</td>
                <td class="px-4 py-3 text-center text-gray-700 border-b">{{ formatDate(item.tanggal) }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Tindakan -->
      <div v-if="pasien?.tindaks?.length" class="bg-white shadow-lg rounded-xl p-8 mb-8">
        <div class="flex justify-between items-center mb-6">
          <h3 class="text-2xl font-semibold text-gray-800 flex items-center">
            <i class="fas fa-procedures mr-3 text-green-600"></i>
            Tindakan Medis
          </h3>
          <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">
            Total: {{ formatCurrency(totalTindak) }}
          </span>
        </div>
        
        <div class="overflow-x-auto">
          <table class="w-full text-sm border border-gray-200 rounded-lg overflow-hidden">
            <thead class="bg-green-100">
              <tr>
                <th class="px-4 py-3 text-left text-green-800 font-semibold border-b">Dokter</th>
                <th class="px-4 py-3 text-left text-green-800 font-semibold border-b">Deskripsi</th>
                <th class="px-4 py-3 text-center text-green-800 font-semibold border-b">Jumlah</th>
                <th class="px-4 py-3 text-right text-green-800 font-semibold border-b">Biaya</th>
                <th class="px-4 py-3 text-right text-green-800 font-semibold border-b">Diskon</th>
                <th class="px-4 py-3 text-right text-green-800 font-semibold border-b">Subtotal</th>
                <th class="px-4 py-3 text-center text-green-800 font-semibold border-b">Tanggal</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(item, index) in pasien.tindaks" :key="index" class="hover:bg-green-50 transition">
                <td class="px-4 py-3 text-gray-700 border-b">{{ item.dktr_tindak }}</td>
                <td class="px-4 py-3 text-gray-700 border-b">{{ item.dskp_tindak }}</td>
                <td class="px-4 py-3 text-center text-gray-700 border-b">{{ item.jmlh_tindak }}</td>
                <td class="px-4 py-3 text-right text-gray-700 border-b">{{ item.bya_tindak }}</td>
                <td class="px-4 py-3 text-right text-gray-700 border-b">{{ item.disc_tindak }}</td>
                <td class="px-4 py-3 text-right font-semibold text-green-700 border-b">{{ item.st_tindak }}</td>
                <td class="px-4 py-3 text-center text-gray-700 border-b">{{ formatDate(item.tanggal) }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Alkes -->
      <div v-if="pasien?.alkes?.length" class="bg-white shadow-lg rounded-xl p-8 mb-8">
        <div class="flex justify-between items-center mb-6">
          <h3 class="text-2xl font-semibold text-gray-800 flex items-center">
            <i class="fas fa-tools mr-3 text-orange-600"></i>
            Alat Kesehatan
          </h3>
          <span class="px-3 py-1 bg-orange-100 text-orange-800 rounded-full text-sm font-medium">
            Total: {{ formatCurrency(totalAlkes) }}
          </span>
        </div>
        
        <div class="overflow-x-auto">
          <table class="w-full text-sm border border-gray-200 rounded-lg overflow-hidden">
            <thead class="bg-orange-100">
              <tr>
                <th class="px-4 py-3 text-left text-orange-800 font-semibold border-b">Poli</th>
                <th class="px-4 py-3 text-left text-orange-800 font-semibold border-b">Deskripsi</th>
                <th class="px-4 py-3 text-center text-orange-800 font-semibold border-b">Jumlah</th>
                <th class="px-4 py-3 text-right text-orange-800 font-semibold border-b">Biaya</th>
                <th class="px-4 py-3 text-right text-orange-800 font-semibold border-b">Diskon</th>
                <th class="px-4 py-3 text-right text-orange-800 font-semibold border-b">Subtotal</th>
                <th class="px-4 py-3 text-center text-orange-800 font-semibold border-b">Tanggal</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(item, index) in pasien.alkes" :key="index" class="hover:bg-orange-50 transition">
                <td class="px-4 py-3 text-gray-700 border-b">{{ item.poli }}</td>
                <td class="px-4 py-3 text-gray-700 border-b">{{ item.dskp_alkes }}</td>
                <td class="px-4 py-3 text-center text-gray-700 border-b">{{ item.jmlh_alkes }}</td>
                <td class="px-4 py-3 text-right text-gray-700 border-b">{{ item.bya_alkes }}</td>
                <td class="px-4 py-3 text-right text-gray-700 border-b">{{ item.disc_alkes }}</td>
                <td class="px-4 py-3 text-right font-semibold text-orange-700 border-b">{{ item.st_alkes }}</td>
                <td class="px-4 py-3 text-center text-gray-700 border-b">{{ formatDate(item.tanggal) }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Resep -->
      <div v-if="pasien?.rsp?.length" class="bg-white shadow-lg rounded-xl p-8 mb-8">
        <div class="flex justify-between items-center mb-6">
          <h3 class="text-2xl font-semibold text-gray-800 flex items-center">
            <i class="fas fa-pills mr-3 text-purple-600"></i>
            Resep Obat
          </h3>
          <span class="px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-sm font-medium">
            Total: {{ formatCurrency(totalRsp) }}
          </span>
        </div>
        
        <div class="overflow-x-auto">
          <table class="w-full text-sm border border-gray-200 rounded-lg overflow-hidden">
            <thead class="bg-purple-100">
              <tr>
                <th class="px-4 py-3 text-left text-purple-800 font-semibold border-b">Deskripsi</th>
                <th class="px-4 py-3 text-center text-purple-800 font-semibold border-b">Jumlah</th>
                <th class="px-4 py-3 text-right text-purple-800 font-semibold border-b">Biaya</th>
                <th class="px-4 py-3 text-right text-purple-800 font-semibold border-b">Diskon</th>
                <th class="px-4 py-3 text-right text-purple-800 font-semibold border-b">Subtotal</th>
                <th class="px-4 py-3 text-center text-purple-800 font-semibold border-b">Tanggal</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(item, index) in pasien.rsp" :key="index" class="hover:bg-purple-50 transition">
                <td class="px-4 py-3 text-gray-700 border-b">{{ item.dskp_rsp }}</td>
                <td class="px-4 py-3 text-center text-gray-700 border-b">{{ item.jmlh_rsp }}</td>
                <td class="px-4 py-3 text-right text-gray-700 border-b">{{ item.bya_rsp }}</td>
                <td class="px-4 py-3 text-right text-gray-700 border-b">{{ item.disc_rsp }}</td>
                <td class="px-4 py-3 text-right font-semibold text-purple-700 border-b">{{ item.st_rsp }}</td>
                <td class="px-4 py-3 text-center text-gray-700 border-b">{{ formatDate(item.tanggal) }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Lainnya -->
      <div v-if="pasien?.lainnyas?.length" class="bg-white shadow-lg rounded-xl p-8 mb-8">
        <div class="flex justify-between items-center mb-6">
          <h3 class="text-2xl font-semibold text-gray-800 flex items-center">
            <i class="fas fa-list mr-3 text-gray-600"></i>
            Layanan Lainnya
          </h3>
          <span class="px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-sm font-medium">
            Total: {{ formatCurrency(totalLainnya) }}
          </span>
        </div>
        
        <div class="overflow-x-auto">
          <table class="w-full text-sm border border-gray-200 rounded-lg overflow-hidden">
            <thead class="bg-gray-100">
              <tr>
                <th class="px-4 py-3 text-left text-gray-800 font-semibold border-b">Deskripsi</th>
                <th class="px-4 py-3 text-center text-gray-800 font-semibold border-b">Jumlah</th>
                <th class="px-4 py-3 text-right text-gray-800 font-semibold border-b">Biaya</th>
                <th class="px-4 py-3 text-right text-gray-800 font-semibold border-b">Diskon</th>
                <th class="px-4 py-3 text-right text-gray-800 font-semibold border-b">Subtotal</th>
                <th class="px-4 py-3 text-center text-gray-800 font-semibold border-b">Tanggal</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(item, index) in pasien.lainnyas" :key="index" class="hover:bg-gray-50 transition">
                <td class="px-4 py-3 text-gray-700 border-b">{{ item.dskp_lainnya }}</td>
                <td class="px-4 py-3 text-center text-gray-700 border-b">{{ item.jmlh_lainnaya }}</td>
                <td class="px-4 py-3 text-right text-gray-700 border-b">{{ item.bya_lainnya }}</td>
                <td class="px-4 py-3 text-right text-gray-700 border-b">{{ item.disc_lainnya }}</td>
                <td class="px-4 py-3 text-right font-semibold text-gray-700 border-b">{{ item.st_lainnya }}</td>
                <td class="px-4 py-3 text-center text-gray-700 border-b">{{ formatDate(item.tanggal) }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Grand Total -->
      <div class="bg-gradient-to-r from-blue-100 to-indigo-100 shadow-lg rounded-xl p-8 mb-8">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
<<<<<<< HEAD
          <h3 class="text-lg font-bold text-blue-700">Grand Total</h3>
          <p class="text-2xl font-extrabold text-blue-900 tracking-wider">Rp. {{ totalSemuaSubtotal().toLocaleString() }} <span class="text-base font-semibold"></span></p>
=======
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
>>>>>>> daf935dd1c37511b1a31fbb7777979201442d99a
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
     </div>
  </AuthenticatedLayout>
</template>
