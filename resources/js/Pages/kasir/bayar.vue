<template>
  <AuthenticatedLayout>
    <Head title="Bayar - Kasir" />
    
    <div class="min-h-screen bg-cover bg-center p-6" style="background-image: url('/images/bg-login.png')">
      <div class="max-w-4xl mx-auto">
        <!-- Flash Messages -->
        <div v-if="flash.success" class="mb-6 bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-500 text-green-700 px-6 py-4 rounded-xl shadow-lg">
          <div class="flex items-center">
            <i class="fas fa-check-circle mr-3 text-xl"></i>
            <span class="font-semibold">{{ flash.success }}</span>
          </div>
        </div>
        
        <div v-if="flash.error" class="mb-6 bg-gradient-to-r from-red-50 to-pink-50 border-l-4 border-red-500 text-red-700 px-6 py-4 rounded-xl shadow-lg">
          <div class="flex items-center">
            <i class="fas fa-exclamation-circle mr-3 text-xl"></i>
            <span class="font-semibold">{{ flash.error }}</span>
          </div>
        </div>

        <!-- Header -->
        <div class="bg-white/90 backdrop-blur-sm shadow-2xl rounded-2xl p-8 border border-white/20 hover:shadow-3xl transition-all duration-300 mb-8">
          <div class="flex items-center justify-start mb-2">
            <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg mr-4">
              <i class="fas fa-cash-register text-white text-2xl"></i>
            </div>
            <div>
              <h1 class="text-4xl font-bold bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent text-left">
                Proses Pembayaran
              </h1>
              <p class="text-gray-600 text-lg text-left mt-2">Input nomor invoice dan tanggal invoice untuk kunjungan pasien</p>
            </div>
          </div>
        </div>

        <!-- Form Pembayaran -->
        <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-xl p-8 border border-white/20">
          <!-- Info Pasien -->
          <div class="mb-8">
            <div class="flex items-center mb-6">
              <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg mr-4">
                <i class="fas fa-user text-white text-xl"></i>
              </div>
              <h3 class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
                Informasi Pasien
              </h3>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-4 border border-blue-200/50">
                <div class="flex items-center mb-2">
                  <i class="fas fa-user-circle text-blue-500 mr-2"></i>
                  <label class="text-xs font-semibold text-blue-700 uppercase tracking-wide">Nama Pasien</label>
                </div>
                <p class="text-lg font-bold text-gray-900 break-words overflow-wrap-anywhere" style="word-wrap: break-word; overflow-wrap: break-word; max-width: 100%;">{{ kunjungan?.nm_p || '-' }}</p>
              </div>
              <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-4 border border-green-200/50">
                <div class="flex items-center mb-2">
                  <i class="fas fa-hashtag text-green-500 mr-2"></i>
                  <label class="text-xs font-semibold text-green-700 uppercase tracking-wide">No Registrasi</label>
                </div>
                <p class="text-lg font-bold text-gray-900 break-words overflow-wrap-anywhere" style="word-wrap: break-word; overflow-wrap: break-word; max-width: 100%;">{{ kunjungan?.no_reg || '-' }}</p>
              </div>
              <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-4 border border-purple-200/50">
                <div class="flex items-center mb-2">
                  <i class="fas fa-id-badge text-purple-500 mr-2"></i>
                  <label class="text-xs font-semibold text-purple-700 uppercase tracking-wide">MRN</label>
                </div>
                <p class="text-lg font-bold text-gray-900 break-words overflow-wrap-anywhere" style="word-wrap: break-word; overflow-wrap: break-word; max-width: 100%;">{{ kunjungan?.mrn || '-' }}</p>
              </div>
              <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl p-4 border border-orange-200/50">
                <div class="flex items-center mb-2">
                  <i class="fas fa-calendar text-orange-500 mr-2"></i>
                  <label class="text-xs font-semibold text-orange-700 uppercase tracking-wide">Tanggal Kunjungan</label>
                </div>
                <p class="text-lg font-bold text-gray-900">{{ formatDate(kunjungan?.tgl_reg) }}</p>
              </div>
            </div>
          </div>

          <!-- Total Biaya -->
          <div class="mb-8 p-6 bg-gradient-to-br from-green-50 via-emerald-50 to-green-100 rounded-xl border-2 border-green-300/50 shadow-lg">
            <div class="flex items-center justify-between mb-4">
              <div class="flex items-center">
                <div class="w-14 h-14 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg mr-4">
                  <i class="fas fa-money-bill-wave text-white text-xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-800">Total Biaya</h3>
              </div>
              <p class="text-4xl font-bold bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent">
                {{ formatCurrency(totalBiaya) }}
              </p>
            </div>
            <!-- Status Pembayaran -->
            <div class="flex items-center justify-between pt-4 border-t border-green-200">
              <span class="text-sm font-medium text-gray-700">Status Pembayaran:</span>
              <span 
                class="px-4 py-2 rounded-full text-sm font-semibold"
                :class="paymentStatus === 'lunas' 
                  ? 'bg-green-100 text-green-800 border border-green-300' 
                  : 'bg-yellow-100 text-yellow-800 border border-yellow-300'"
              >
                {{ paymentStatus === 'lunas' ? '✅ Lunas' : '⏳ Pending' }}
              </span>
            </div>
          </div>

          <!-- Form Invoice -->
          <form @submit.prevent="submitPayment">
            <div class="space-y-6">
              <!-- Nomor Invoice -->
              <div>
                <label for="no_inv" class="block text-sm font-semibold text-gray-700 mb-3 flex items-center">
                  <i class="fas fa-file-invoice text-green-500 mr-2"></i>
                  Nomor Invoice <span class="text-red-500 ml-1">*</span>
                </label>
                <input
                  id="no_inv"
                  v-model="form.no_inv"
                  type="text"
                  required
                  class="w-full px-5 py-4 bg-gradient-to-r from-gray-50 to-white border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-300 text-gray-700 placeholder-gray-400 shadow-sm"
                  placeholder="Masukkan nomor invoice"
                />
                <p v-if="errors.no_inv" class="mt-2 text-sm text-red-600 flex items-center">
                  <i class="fas fa-exclamation-circle mr-2"></i>{{ errors.no_inv }}
                </p>
              </div>

              <!-- Tanggal Invoice -->
              <div>
                <label for="tgl_inv" class="block text-sm font-semibold text-gray-700 mb-3 flex items-center">
                  <i class="fas fa-calendar-alt text-green-500 mr-2"></i>
                  Tanggal Invoice <span class="text-red-500 ml-1">*</span>
                </label>
                <input
                  id="tgl_inv"
                  v-model="form.tgl_inv"
                  type="date"
                  required
                  class="w-full px-5 py-4 bg-gradient-to-r from-gray-50 to-white border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-300 shadow-sm"
                />
                <p v-if="errors.tgl_inv" class="mt-2 text-sm text-red-600 flex items-center">
                  <i class="fas fa-exclamation-circle mr-2"></i>{{ errors.tgl_inv }}
                </p>
              </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-4 mt-8">
              <button
                type="button"
                @click="router.visit(route('kasir.index'))"
                class="flex-1 px-6 py-4 bg-gradient-to-r from-gray-200 to-gray-300 text-gray-700 rounded-xl hover:from-gray-300 hover:to-gray-400 transition-all duration-300 transform hover:scale-105 font-bold shadow-md hover:shadow-lg flex items-center justify-center"
              >
                <i class="fas fa-times mr-2"></i>
                Batal
              </button>
              <button
                type="submit"
                :disabled="form.processing || paymentStatus === 'lunas'"
                :class="[
                  'flex-1 px-6 py-4 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl hover:from-green-700 hover:to-emerald-700 transition-all duration-300 font-bold shadow-xl hover:shadow-2xl disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center text-lg',
                  form.processing || paymentStatus === 'lunas' 
                    ? 'disabled:transform-none' 
                    : 'transform hover:scale-105'
                ]"
              >
                <span v-if="form.processing" class="flex items-center">
                  <i class="fas fa-spinner fa-spin mr-2"></i>
                  Memproses...
                </span>
                <span v-else-if="paymentStatus === 'lunas'" class="flex items-center">
                  <i class="fas fa-check-circle mr-3"></i>
                  Sudah Lunas
                </span>
                <span v-else class="flex items-center">
                  <i class="fas fa-cash-register mr-3"></i>
                  Proses Pembayaran
                </span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Head, router, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
  kunjungan: Object,
  flash: {
    type: Object,
    default: () => ({})
  },
  errors: {
    type: Object,
    default: () => ({})
  }
})

const form = useForm({
  no_inv: props.kunjungan?.no_inv || '',
  tgl_inv: props.kunjungan?.tgl_inv || new Date().toISOString().split('T')[0]
})

// Get payment status from transaction
const paymentStatus = computed(() => {
  if (!props.kunjungan || !props.kunjungan.transaksi || props.kunjungan.transaksi.length === 0) {
    return 'pending'
  }
  
  // Get status from the first transaction (usually there's only one per kunjungan)
  const firstTransaksi = props.kunjungan.transaksi[0]
  return firstTransaksi?.status || 'pending'
})

// Calculate total biaya
const totalBiaya = computed(() => {
  if (!props.kunjungan) return 0
  
  let total = 0
  
  // Calculate from transaksi
  if (props.kunjungan.transaksi && props.kunjungan.transaksi.length > 0) {
    total = props.kunjungan.transaksi.reduce((sum, transaksi) => {
      return sum + (parseFloat(transaksi.total_biaya) || 0)
    }, 0)
  }
  
  // Fallback: calculate from detail services
  if (total === 0) {
    if (props.kunjungan.konsuls) {
      props.kunjungan.konsuls.forEach(konsul => {
        total += parseFloat(String(konsul.st_kons).replace(/[^\d]/g, '')) || 0
      })
    }
    if (props.kunjungan.tindaks) {
      props.kunjungan.tindaks.forEach(tindak => {
        total += parseFloat(String(tindak.st_tindak).replace(/[^\d]/g, '')) || 0
      })
    }
    if (props.kunjungan.alkes) {
      props.kunjungan.alkes.forEach(alkes => {
        total += parseFloat(String(alkes.st_alkes).replace(/[^\d]/g, '')) || 0
      })
    }
    if (props.kunjungan.rsp) {
      props.kunjungan.rsp.forEach(rsp => {
        total += parseFloat(String(rsp.st_rsp).replace(/[^\d]/g, '')) || 0
      })
    }
    if (props.kunjungan.lainnyas) {
      props.kunjungan.lainnyas.forEach(lainnya => {
        total += parseFloat(String(lainnya.st_lainnya).replace(/[^\d]/g, '')) || 0
      })
    }
  }
  
  return total
})

function formatCurrency(amount) {
  if (!amount) return 'Rp 0'
  const numericAmount = parseFloat(String(amount).replace(/[^\d]/g, '')) || 0
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0
  }).format(numericAmount)
}

function formatDate(dateString) {
  if (!dateString) return '-'
  const date = new Date(dateString)
  return date.toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

function submitPayment() {
  form.put(route('kasir.bayar.update', props.kunjungan.id), {
    preserveScroll: true,
    onSuccess: () => {
      router.visit(route('kasir.kunjungan.print', props.kunjungan.id))
    }
  })
}
</script>

