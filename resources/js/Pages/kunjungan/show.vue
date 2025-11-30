<template>
  <AuthenticatedLayout>
    <Head :title="`Detail Kunjungan - ${kunjungan.nm_p}`" />
    
    <div class="min-h-screen bg-cover bg-center p-4 sm:p-6" style="background-image: url('/images/bg-login.png')">
      <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
          <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-2xl shadow-xl p-6 sm:p-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
              <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-white/20 backdrop-blur rounded-xl flex items-center justify-center">
                  <i class="fas fa-clipboard-list text-white text-2xl"></i>
                </div>
                <div>
                  <h1 class="text-2xl sm:text-3xl font-bold text-white">Detail Kunjungan</h1>
                  <p class="text-white/80 text-sm sm:text-base mt-1">Informasi lengkap kunjungan pasien</p>
                </div>
              </div>
              <div class="flex flex-wrap gap-3">
                <button
                  v-if="isDokter"
                  @click="router.visit(`/pasien/${kunjungan.psn_id}/kunjungan-with-transaksi/${kunjungan.id}/edit`)"
                  class="px-4 py-2.5 bg-white/20 backdrop-blur text-white rounded-xl hover:bg-white/30 transition flex items-center font-medium"
                >
                  <i class="fas fa-edit mr-2"></i>
                  Edit Kunjungan
                </button>
                <button
                  @click="router.visit(backRoute)"
                  class="px-4 py-2.5 bg-white/20 backdrop-blur text-white rounded-xl hover:bg-white/30 transition flex items-center font-medium"
                >
                  <i class="fas fa-arrow-left mr-2"></i>
                  Kembali
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Informasi Kunjungan -->
        <div class="bg-white rounded-2xl shadow-lg p-6 sm:p-8 mb-6">
          <div class="flex items-center mb-6">
            <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center mr-4">
              <i class="fas fa-user text-white text-xl"></i>
            </div>
            <h2 class="text-xl sm:text-2xl font-bold text-gray-900">Informasi Kunjungan</h2>
          </div>
          
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
            <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-4 border border-green-200">
              <div class="flex items-center mb-2">
                <i class="fas fa-hashtag text-green-600 mr-2"></i>
                <p class="text-xs font-semibold text-green-700 uppercase tracking-wide">No Registrasi</p>
              </div>
              <p class="text-lg font-bold text-gray-900 break-all">{{ kunjungan.no_reg || '-' }}</p>
            </div>
            
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-4 border border-blue-200">
              <div class="flex items-center mb-2">
                <i class="fas fa-calendar-alt text-blue-600 mr-2"></i>
                <p class="text-xs font-semibold text-blue-700 uppercase tracking-wide">Tanggal Registrasi</p>
              </div>
              <p class="text-lg font-bold text-gray-900">{{ formatDate(kunjungan.tgl_reg) }}</p>
            </div>
            
            <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-4 border border-purple-200">
              <div class="flex items-center mb-2">
                <i class="fas fa-id-card text-purple-600 mr-2"></i>
                <p class="text-xs font-semibold text-purple-700 uppercase tracking-wide">MRN</p>
              </div>
              <p class="text-lg font-bold text-gray-900 break-all">{{ kunjungan.mrn || '-' }}</p>
            </div>
            
            <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-xl p-4 border border-indigo-200">
              <div class="flex items-center mb-2">
                <i class="fas fa-user text-indigo-600 mr-2"></i>
                <p class="text-xs font-semibold text-indigo-700 uppercase tracking-wide">Nama Pasien</p>
              </div>
              <p class="text-lg font-bold text-gray-900 break-words">{{ kunjungan.nm_p || '-' }}</p>
            </div>
            
            <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl p-4 border border-orange-200">
              <div class="flex items-center mb-2">
                <i class="fas fa-map-marker-alt text-orange-600 mr-2"></i>
                <p class="text-xs font-semibold text-orange-700 uppercase tracking-wide">Alamat</p>
              </div>
              <p class="text-sm font-semibold text-gray-900 break-words">{{ kunjungan.almt_B || '-' }}</p>
            </div>
            
            <div class="bg-gradient-to-br from-teal-50 to-teal-100 rounded-xl p-4 border border-teal-200">
              <div class="flex items-center mb-2">
                <i class="fas fa-file-invoice text-teal-600 mr-2"></i>
                <p class="text-xs font-semibold text-teal-700 uppercase tracking-wide">No Invoice</p>
              </div>
              <p class="text-lg font-bold text-gray-900 break-all">{{ kunjungan.no_inv || '-' }}</p>
            </div>
            
            <div class="bg-gradient-to-br from-pink-50 to-pink-100 rounded-xl p-4 border border-pink-200">
              <div class="flex items-center mb-2">
                <i class="fas fa-calendar-check text-pink-600 mr-2"></i>
                <p class="text-xs font-semibold text-pink-700 uppercase tracking-wide">Tanggal Invoice</p>
              </div>
              <p class="text-lg font-bold text-gray-900">{{ kunjungan.tgl_inv ? formatDate(kunjungan.tgl_inv) : '-' }}</p>
            </div>
            
            <div class="bg-gradient-to-br from-cyan-50 to-cyan-100 rounded-xl p-4 border border-cyan-200">
              <div class="flex items-center mb-2">
                <i class="fas fa-hospital text-cyan-600 mr-2"></i>
                <p class="text-xs font-semibold text-cyan-700 uppercase tracking-wide">Jenis Perawatan</p>
              </div>
              <p class="text-lg font-bold text-gray-900">{{ kunjungan.perawatan || '-' }}</p>
            </div>
            
            <div class="bg-gradient-to-br from-amber-50 to-amber-100 rounded-xl p-4 border border-amber-200">
              <div class="flex items-center mb-2">
                <i class="fas fa-shield-alt text-amber-600 mr-2"></i>
                <p class="text-xs font-semibold text-amber-700 uppercase tracking-wide">Penjamin</p>
              </div>
              <p class="text-lg font-bold text-gray-900">{{ kunjungan.penjamin || '-' }}</p>
            </div>
            
            <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-xl p-4 border border-red-200">
              <div class="flex items-center mb-2">
                <i class="fas fa-file-medical text-red-600 mr-2"></i>
                <p class="text-xs font-semibold text-red-700 uppercase tracking-wide">No SJP</p>
              </div>
              <p class="text-lg font-bold text-gray-900 break-all">{{ kunjungan.no_sjp || '-' }}</p>
            </div>
            
            <div class="bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-xl p-4 border border-emerald-200">
              <div class="flex items-center mb-2">
                <i class="fas fa-stethoscope text-emerald-600 mr-2"></i>
                <p class="text-xs font-semibold text-emerald-700 uppercase tracking-wide">ICD Code</p>
              </div>
              <p class="text-lg font-bold text-gray-900 break-all">{{ kunjungan.icd || '-' }}</p>
            </div>
            
            <div class="bg-gradient-to-br from-violet-50 to-violet-100 rounded-xl p-4 border border-violet-200">
              <div class="flex items-center mb-2">
                <i class="fas fa-clinic-medical text-violet-600 mr-2"></i>
                <p class="text-xs font-semibold text-violet-700 uppercase tracking-wide">Jenis Kunjungan</p>
              </div>
              <p class="text-lg font-bold text-gray-900">{{ kunjungan.kunjungan || '-' }}</p>
            </div>
          </div>
        </div>

        <!-- Informasi Transaksi -->
        <div v-if="kunjungan.transaksi && kunjungan.transaksi.length > 0" class="bg-white rounded-2xl shadow-lg p-6 sm:p-8">
          <div class="flex items-center mb-6">
            <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center mr-4">
              <i class="fas fa-receipt text-white text-xl"></i>
            </div>
            <h2 class="text-xl sm:text-2xl font-bold text-gray-900">Informasi Transaksi</h2>
          </div>
          
          <div v-for="(transaksi, index) in kunjungan.transaksi" :key="transaksi.id" class="mb-6 last:mb-0">
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl p-6 border-2 border-gray-200">
              <!-- Transaksi Header -->
              <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6 pb-4 border-b border-gray-300">
                <div class="flex items-center gap-4">
                  <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-lg flex items-center justify-center text-white font-bold text-lg">
                    {{ index + 1 }}
                  </div>
                  <div>
                    <h3 class="text-lg sm:text-xl font-bold text-gray-900">Transaksi #{{ transaksi.id }}</h3>
                    <p class="text-sm text-gray-600 mt-1">
                      <i class="fas fa-calendar-alt mr-1"></i>
                      {{ formatDate(transaksi.tanggal) }}
                    </p>
                  </div>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                  <span :class="getStatusBadgeClass(transaksi.status)" class="px-4 py-2 rounded-lg text-sm font-semibold flex items-center">
                    <i :class="getStatusIcon(transaksi.status)" class="mr-2"></i>
                    {{ transaksi.status }}
                  </span>
                  <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg px-4 py-2">
                    <p class="text-xs font-semibold text-white uppercase tracking-wide mb-1">Total Biaya</p>
                    <p class="text-xl font-bold text-white">{{ formatCurrency(transaksi.total_biaya) }}</p>
                  </div>
                </div>
              </div>

              <!-- Detail Transaksi -->
              <div v-if="transaksi.detailTransaksi && transaksi.detailTransaksi.length > 0" class="space-y-3">
                <h4 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-3">
                  <i class="fas fa-list mr-2 text-green-600"></i>
                  Detail Item ({{ transaksi.detailTransaksi.length }})
                </h4>
                <div v-for="detail in transaksi.detailTransaksi" :key="detail.id" class="bg-white border border-gray-200 rounded-lg p-4 hover:border-green-400 hover:shadow-md transition-all">
                  <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                    <div class="flex-1">
                      <h5 class="font-bold text-gray-900 mb-2 flex items-center">
                        <i class="fas fa-pills text-green-600 mr-2"></i>
                        {{ detail.resep || 'Item' }}
                      </h5>
                      <p class="text-sm text-gray-600 mb-2 break-words">{{ detail.deskripsi || '-' }}</p>
                      <div class="flex flex-wrap items-center gap-4 text-sm">
                        <span class="text-gray-600">
                          <i class="fas fa-box text-gray-400 mr-1"></i>
                          Jumlah: <span class="font-semibold text-gray-900">{{ detail.jumlah || 0 }}</span>
                        </span>
                        <span v-if="detail.icd" class="text-gray-600">
                          <i class="fas fa-stethoscope text-gray-400 mr-1"></i>
                          ICD: <span class="font-semibold text-gray-900">{{ detail.icd }}</span>
                        </span>
                      </div>
                    </div>
                    <div class="sm:text-right">
                      <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Biaya</p>
                      <p class="text-lg font-bold text-green-600">{{ formatCurrency(detail.biaya) }}</p>
                    </div>
                  </div>
                </div>
              </div>
              
              <div v-else class="text-center py-8 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                <i class="fas fa-inbox text-4xl text-gray-300 mb-3"></i>
                <p class="text-gray-500 font-medium">Tidak ada detail transaksi</p>
              </div>
            </div>
          </div>
        </div>

        <!-- No Transaction Message -->
        <div v-else class="bg-white rounded-2xl shadow-lg p-8 sm:p-12 text-center">
          <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-info-circle text-4xl text-gray-400"></i>
          </div>
          <h3 class="text-xl font-bold text-gray-800 mb-2">Belum ada transaksi</h3>
          <p class="text-gray-500 mb-6">Kunjungan ini belum memiliki transaksi terkait</p>
          <button
            v-if="isDokter"
            @click="router.visit(`/pasien/${kunjungan.psn_id}/kunjungan-with-transaksi/${kunjungan.id}/edit`)"
            class="px-6 py-3 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-xl font-medium hover:shadow-lg transition-all inline-flex items-center"
          >
            <i class="fas fa-plus mr-2"></i>
            Tambah Transaksi
          </button>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { Head, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { useAuth } from '@/composables/useAuth'
import { computed } from 'vue'

const props = defineProps({
  kunjungan: Object
})

const { isPendaftaran, isDokter } = useAuth()

// Determine back route based on user role
const backRoute = computed(() => {
  if (isPendaftaran.value) {
    // For pendaftaran role, go to patient detail page
    return `/pasien/${props.kunjungan?.psn_id}`
  } else if (isDokter.value) {
    // For dokter role, go to dokter pasien kunjungan
    return '/dokter/pasien-kunjungan'
  }
  // Default fallback
  return '/pasien'
})

// Helper functions
function formatDate(dateString) {
  if (!dateString) return '-'
  const date = new Date(dateString)
  return date.toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

function formatCurrency(amount) {
  if (!amount) return 'Rp 0'
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0
  }).format(amount)
}

function getStatusBadgeClass(status) {
  switch (status?.toLowerCase()) {
    case 'lunas':
      return 'bg-green-100 text-green-800 border border-green-300'
    case 'pending':
      return 'bg-yellow-100 text-yellow-800 border border-yellow-300'
    case 'batal':
      return 'bg-red-100 text-red-800 border border-red-300'
    default:
      return 'bg-gray-100 text-gray-800 border border-gray-300'
  }
}

function getStatusIcon(status) {
  switch (status?.toLowerCase()) {
    case 'lunas':
      return 'fas fa-check-circle'
    case 'pending':
      return 'fas fa-clock'
    case 'batal':
      return 'fas fa-times-circle'
    default:
      return 'fas fa-question-circle'
  }
}
</script>
