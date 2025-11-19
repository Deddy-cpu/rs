<template>
  <AuthenticatedLayout>
    <Head title="Bayar - Kasir" />
    
    <div class="min-h-screen bg-cover bg-center p-6" style="background-image: url('/images/bg-login.png')">
      <div class="max-w-4xl mx-auto">
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
        <div class="text-center mb-8">
          <h1 class="text-4xl font-bold text-gray-800 mb-2">💳 Proses Pembayaran</h1>
          <p class="text-gray-600">Input nomor invoice dan tanggal invoice untuk kunjungan pasien</p>
        </div>

        <!-- Form Pembayaran -->
        <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-200">
          <!-- Info Pasien -->
          <div class="mb-8 p-6 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl border border-blue-100">
            <h3 class="text-xl font-bold text-gray-800 mb-4">📋 Informasi Pasien</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <p class="text-sm text-gray-600">Nama Pasien</p>
                <p class="text-lg font-semibold text-gray-800">{{ kunjungan?.nm_p || '-' }}</p>
              </div>
              <div>
                <p class="text-sm text-gray-600">No. Registrasi</p>
                <p class="text-lg font-semibold text-gray-800">{{ kunjungan?.no_reg || '-' }}</p>
              </div>
              <div>
                <p class="text-sm text-gray-600">MRN</p>
                <p class="text-lg font-semibold text-gray-800">{{ kunjungan?.mrn || '-' }}</p>
              </div>
              <div>
                <p class="text-sm text-gray-600">Tanggal Kunjungan</p>
                <p class="text-lg font-semibold text-gray-800">{{ formatDate(kunjungan?.tgl_reg) }}</p>
              </div>
            </div>
          </div>

          <!-- Total Biaya -->
          <div class="mb-8 p-6 bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl border border-green-100">
            <div class="flex justify-between items-center">
              <h3 class="text-xl font-bold text-gray-800">💰 Total Biaya</h3>
              <p class="text-3xl font-bold text-green-600">{{ formatCurrency(totalBiaya) }}</p>
            </div>
          </div>

          <!-- Form Invoice -->
          <form @submit.prevent="submitPayment">
            <div class="space-y-6">
              <!-- Nomor Invoice -->
              <div>
                <label for="no_inv" class="block text-sm font-medium text-gray-700 mb-2">
                  Nomor Invoice <span class="text-red-500">*</span>
                </label>
                <input
                  id="no_inv"
                  v-model="form.no_inv"
                  type="text"
                  required
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                  placeholder="Masukkan nomor invoice"
                />
                <p v-if="errors.no_inv" class="mt-1 text-sm text-red-600">{{ errors.no_inv }}</p>
              </div>

              <!-- Tanggal Invoice -->
              <div>
                <label for="tgl_inv" class="block text-sm font-medium text-gray-700 mb-2">
                  Tanggal Invoice <span class="text-red-500">*</span>
                </label>
                <input
                  id="tgl_inv"
                  v-model="form.tgl_inv"
                  type="date"
                  required
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                />
                <p v-if="errors.tgl_inv" class="mt-1 text-sm text-red-600">{{ errors.tgl_inv }}</p>
              </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-4 mt-8">
              <button
                type="button"
                @click="router.visit(route('kasir.index'))"
                class="flex-1 px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-semibold"
              >
                Batal
              </button>
              <button
                type="submit"
                :disabled="form.processing"
                class="flex-1 px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-lg hover:from-green-700 hover:to-emerald-700 transition font-semibold disabled:opacity-50 disabled:cursor-not-allowed"
              >
                <span v-if="form.processing">Memproses...</span>
                <span v-else>💳 Proses Pembayaran</span>
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

