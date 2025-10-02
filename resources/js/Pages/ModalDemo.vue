<template>
  <AuthenticatedLayout>
    <Head title="Demo Modal Konfirmasi" />
    
    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-green-50 py-12">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-12">
          <h1 class="text-4xl font-bold text-gray-900 mb-4">
            🎨 Demo Modal Konfirmasi
          </h1>
          <p class="text-xl text-gray-600">
            Tampilan peringatan yang bagus untuk konfirmasi hapus data
          </p>
        </div>

        <!-- Demo Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
          <!-- Demo Hapus Pasien -->
          <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-200">
            <div class="text-center">
              <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
              </div>
              <h3 class="text-2xl font-bold text-gray-900 mb-4">Hapus Data Pasien</h3>
              <p class="text-gray-600 mb-6">
                Demo modal konfirmasi untuk menghapus data pasien dengan semua data terkait
              </p>
              <button
                @click="showPasienModal = true"
                class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-xl font-semibold transition-all duration-200 transform hover:scale-105 hover:shadow-lg"
              >
                🗑️ Demo Hapus Pasien
              </button>
            </div>
          </div>

          <!-- Demo Hapus Transaksi -->
          <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-200">
            <div class="text-center">
              <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
              </div>
              <h3 class="text-2xl font-bold text-gray-900 mb-4">Hapus Transaksi</h3>
              <p class="text-gray-600 mb-6">
                Demo modal konfirmasi untuk menghapus data transaksi
              </p>
              <button
                @click="showTransaksiModal = true"
                class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-semibold transition-all duration-200 transform hover:scale-105 hover:shadow-lg"
              >
                🗑️ Demo Hapus Transaksi
              </button>
            </div>
          </div>
        </div>

        <!-- Features -->
        <div class="mt-16 bg-white rounded-2xl shadow-xl p-8 border border-gray-200">
          <h2 class="text-3xl font-bold text-gray-900 text-center mb-8">
            ✨ Fitur Modal Konfirmasi
          </h2>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="text-center">
              <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
              </div>
              <h3 class="text-lg font-semibold text-gray-900 mb-2">Animasi Smooth</h3>
              <p class="text-gray-600">Transisi dan animasi yang halus untuk pengalaman pengguna yang lebih baik</p>
            </div>
            <div class="text-center">
              <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <h3 class="text-lg font-semibold text-gray-900 mb-2">Responsive Design</h3>
              <p class="text-gray-600">Tampilan yang optimal di semua ukuran layar dan perangkat</p>
            </div>
            <div class="text-center">
              <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                </svg>
              </div>
              <h3 class="text-lg font-semibold text-gray-900 mb-2">Loading State</h3>
              <p class="text-gray-600">Indikator loading yang jelas saat proses penghapusan berlangsung</p>
            </div>
          </div>
        </div>

        <!-- Back Button -->
        <div class="text-center mt-12">
          <a
            href="/kasir"
            class="inline-flex items-center px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-xl font-semibold transition-all duration-200 transform hover:scale-105 hover:shadow-lg"
          >
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke Kasir
          </a>
        </div>
      </div>
    </div>

    <!-- Modal Konfirmasi Hapus Pasien -->
    <DeletePasienModal
      :show="showPasienModal"
      :pasien-name="'John Doe'"
      :loading="isDeleting"
      @confirm="handleDeletePasien"
      @cancel="showPasienModal = false"
    />

    <!-- Modal Konfirmasi Hapus Transaksi -->
    <DeleteTransaksiModal
      :show="showTransaksiModal"
      :loading="isDeleting"
      @confirm="handleDeleteTransaksi"
      @cancel="showTransaksiModal = false"
    />
  </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head } from '@inertiajs/vue3'
import DeletePasienModal from '@/Components/DeletePasienModal.vue'
import DeleteTransaksiModal from '@/Components/DeleteTransaksiModal.vue'

const showPasienModal = ref(false)
const showTransaksiModal = ref(false)
const isDeleting = ref(false)

const handleDeletePasien = () => {
  isDeleting.value = true
  
  // Simulate API call
  setTimeout(() => {
    isDeleting.value = false
    showPasienModal.value = false
    alert('Data pasien berhasil dihapus! (Demo)')
  }, 2000)
}

const handleDeleteTransaksi = () => {
  isDeleting.value = true
  
  // Simulate API call
  setTimeout(() => {
    isDeleting.value = false
    showTransaksiModal.value = false
    alert('Data transaksi berhasil dihapus! (Demo)')
  }, 2000)
}
</script>

