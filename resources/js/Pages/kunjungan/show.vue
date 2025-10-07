<template>
  <AuthenticatedLayout>
    <Head :title="`Detail Kunjungan - ${kunjungan.nm_p}`" />
    
    <div class="min-h-screen bg-gray-50 py-8">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
          <div class="flex items-center justify-between">
            <div>
              <h1 class="text-3xl font-bold text-gray-900">Detail Kunjungan</h1>
              <p class="mt-2 text-gray-600">Informasi lengkap kunjungan pasien</p>
            </div>
            <div class="flex gap-3">
              <button
                @click="router.visit(`/pasien/${kunjungan.psn_id}/kunjungan-with-transaksi/${kunjungan.id}/edit`)"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center"
              >
                <i class="fas fa-edit mr-2"></i>
                Edit Kunjungan
              </button>
              <button
                @click="router.visit('/dokter/pasien-kunjungan')"
                class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition flex items-center"
              >
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali
              </button>
            </div>
          </div>
        </div>

        <!-- Informasi Kunjungan -->
        <div class="bg-white shadow rounded-lg p-6 mb-6">
          <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
            <i class="fas fa-user mr-2 text-blue-600"></i>
            Informasi Kunjungan
          </h2>
          
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div>
              <p class="text-sm text-gray-500">No Registrasi</p>
              <p class="font-semibold">{{ kunjungan.no_reg }}</p>
            </div>
            <div>
              <p class="text-sm text-gray-500">Tanggal Registrasi</p>
              <p class="font-semibold">{{ formatDate(kunjungan.tgl_reg) }}</p>
            </div>
            <div>
              <p class="text-sm text-gray-500">MRN</p>
              <p class="font-semibold">{{ kunjungan.mrn }}</p>
            </div>
            <div>
              <p class="text-sm text-gray-500">Nama Pasien</p>
              <p class="font-semibold">{{ kunjungan.nm_p }}</p>
            </div>
            <div>
              <p class="text-sm text-gray-500">Alamat</p>
              <p class="font-semibold">{{ kunjungan.almt_B }}</p>
            </div>
            <div>
              <p class="text-sm text-gray-500">No Invoice</p>
              <p class="font-semibold">{{ kunjungan.no_inv || '-' }}</p>
            </div>
            <div>
              <p class="text-sm text-gray-500">Tanggal Invoice</p>
              <p class="font-semibold">{{ kunjungan.tgl_inv ? formatDate(kunjungan.tgl_inv) : '-' }}</p>
            </div>
            <div>
              <p class="text-sm text-gray-500">Jenis Perawatan</p>
              <p class="font-semibold">{{ kunjungan.perawatan }}</p>
            </div>
            <div>
              <p class="text-sm text-gray-500">Penjamin</p>
              <p class="font-semibold">{{ kunjungan.penjamin }}</p>
            </div>
            <div>
              <p class="text-sm text-gray-500">No SJP</p>
              <p class="font-semibold">{{ kunjungan.no_sjp || '-' }}</p>
            </div>
            <div>
              <p class="text-sm text-gray-500">ICD Code</p>
              <p class="font-semibold">{{ kunjungan.icd || '-' }}</p>
            </div>
            <div>
              <p class="text-sm text-gray-500">Jenis Kunjungan</p>
              <p class="font-semibold">{{ kunjungan.kunjungan }}</p>
            </div>
          </div>
        </div>

        <!-- Informasi Transaksi -->
        <div v-if="kunjungan.transaksi && kunjungan.transaksi.length > 0" class="bg-white shadow rounded-lg p-6">
          <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
            <i class="fas fa-receipt mr-2 text-green-600"></i>
            Informasi Transaksi
          </h2>
          
          <div v-for="transaksi in kunjungan.transaksi" :key="transaksi.id" class="mb-6 p-4 border rounded-lg">
            <div class="flex justify-between items-center mb-4">
              <h3 class="text-lg font-semibold">Transaksi #{{ transaksi.id }}</h3>
              <div class="flex gap-2">
                <span :class="getStatusBadgeClass(transaksi.status)" class="px-3 py-1 rounded-full text-sm font-medium">
                  {{ transaksi.status }}
                </span>
                <span class="text-lg font-bold text-green-600">
                  {{ formatCurrency(transaksi.total_biaya) }}
                </span>
              </div>
            </div>
            
            <div class="text-sm text-gray-600 mb-4">
              <p><strong>Tanggal:</strong> {{ formatDate(transaksi.tanggal) }}</p>
            </div>

            <!-- Detail Transaksi -->
            <div v-if="transaksi.detailTransaksi && transaksi.detailTransaksi.length > 0" class="space-y-4">
              <div v-for="detail in transaksi.detailTransaksi" :key="detail.id" class="bg-gray-50 p-4 rounded-lg">
                <h4 class="font-semibold text-gray-800 mb-2">{{ detail.resep }}</h4>
                <p class="text-sm text-gray-600 mb-2">{{ detail.deskripsi }}</p>
                <div class="flex justify-between text-sm">
                  <span>Jumlah: {{ detail.jumlah }}</span>
                  <span class="font-semibold">{{ formatCurrency(detail.biaya) }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- No Transaction Message -->
        <div v-else class="bg-white shadow rounded-lg p-6 text-center">
          <i class="fas fa-info-circle text-4xl text-gray-300 mb-3"></i>
          <h3 class="text-lg font-medium text-gray-500 mb-2">Belum ada transaksi</h3>
          <p class="text-gray-400 mb-4">Kunjungan ini belum memiliki transaksi terkait</p>
          <button
            @click="router.visit(`/pasien/${kunjungan.psn_id}/kunjungan-with-transaksi/${kunjungan.id}/edit`)"
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition"
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

const props = defineProps({
  kunjungan: Object
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
      return 'bg-green-100 text-green-800'
    case 'pending':
      return 'bg-yellow-100 text-yellow-800'
    case 'batal':
      return 'bg-red-100 text-red-800'
    default:
      return 'bg-gray-100 text-gray-800'
  }
}
</script>
