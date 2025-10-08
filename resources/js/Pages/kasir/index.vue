<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue"
import { Head, router } from "@inertiajs/vue3"
import { ref, computed } from "vue"
import DeletePasienModal from "@/Components/DeletePasienModal.vue"
import DeleteTransaksiModal from "@/Components/DeleteTransaksiModal.vue"

const props = defineProps({
  pasien: Array, // [{id, nama_pasien, alamat, Penjamin, tanggal, transaksi_id}]
  flash: {
    type: Object,
    default: () => ({})
  }
})

// Modal states
const showDeletePasienModal = ref(false)
const showDeleteTransaksiModal = ref(false)
const pasienToDelete = ref(null)
const transaksiToDelete = ref(null)
const isDeleting = ref(false)

// Search and filter
const searchQuery = ref('')
const filterPenjamin = ref('')

// Computed properties
const filteredPasien = computed(() => {
  let filtered = props.pasien || []
  
  // Filter by search query
  if (searchQuery.value) {
    filtered = filtered.filter(p => 
      p.nama_pasien?.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      p.alamat?.toLowerCase().includes(searchQuery.value.toLowerCase())
    )
  }
  
  // Filter by penjamin
  if (filterPenjamin.value) {
    filtered = filtered.filter(p => p.Penjamin === filterPenjamin.value)
  }
  
  return filtered
})

const uniquePenjamin = computed(() => {
  const penjamin = [...new Set(props.pasien?.map(p => p.Penjamin) || [])]
  return penjamin.filter(p => p)
})

// Functions
function confirmDeletePasien(pasien) {
  pasienToDelete.value = pasien
  showDeletePasienModal.value = true
}

function confirmDeleteTransaksi(transaksi) {
  transaksiToDelete.value = transaksi
  showDeleteTransaksiModal.value = true
}

function deletePasien() {
  if (!pasienToDelete.value) return
  const transaksiId = pasienToDelete.value.transaksi_id ?? pasienToDelete.value.id
  isDeleting.value = true
  router.delete(route("kasir.destroy", transaksiId), {
    onFinish: () => {
      isDeleting.value = false
      showDeletePasienModal.value = false
      pasienToDelete.value = null
    },
  })
}

function deleteTransaksi() {
  if (!transaksiToDelete.value) return
  isDeleting.value = true
  router.delete(route("kasir.destroy", transaksiToDelete.value.id), {
    onFinish: () => {
      isDeleting.value = false
      showDeleteTransaksiModal.value = false
      transaksiToDelete.value = null
      window.location.reload()
    },
  })
}

function cancelDeletePasien() {
  showDeletePasienModal.value = false
  pasienToDelete.value = null
}
function cancelDeleteTransaksi() {
  showDeleteTransaksiModal.value = false
  transaksiToDelete.value = null
}

// Improved calculation function
function calculateSubTotal(trx, detail) {
  let total = 0
  if (trx.bya && trx.jmlh) {
    const tindakanBiaya = parseFloat(trx.bya.replace(/[^\d]/g, '')) || 0
    const tindakanJumlah = parseInt(trx.jmlh) || 0
    total += tindakanBiaya * tindakanJumlah
  }
  
  // Hitung biaya resep dari detail
  if (detail && Array.isArray(detail)) {
    detail.forEach(d => {
      if (d.biaya && d.jumlah) {
        const resepBiaya = parseFloat(d.biaya.replace(/[^\d]/g, '')) || 0
        const resepJumlah = parseInt(d.jumlah) || 0
        total += resepBiaya * resepJumlah
      }
    })
  }
  
  return total
}

function formatCurrency(amount) {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0
  }).format(amount)
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
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Daftar Pasien & Transaksi" />

    <div class="min-h-screen bg-cover bg-center p-6" style="background-image: url('/images/bg-login.png')">
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

    
<!-- Header + Tombol Tambah + Search -->
 <div class="mb-4">
  <!-- Header Tengah -->
  <h1 class="text-3xl font-bold text-gray-900 flex items-center justify-center gap-2 mb-2">
    <i class="fas fa-users text-blue-600"></i>
    Daftar Pasien & Transaksi
  </h1>
  <p class="text-gray-600 text-center mb-2">
    Kelola data pasien dan transaksi medis
  </p>

  <!-- Baris Tombol + Search -->
  <div
    class="flex flex-col md:flex-row justify-between items-center gap-4
    bg-transparent px-1 pt-0 pb-0"
  >
    <!-- Tombol Tambah di kiri -->
    <a
      href="/kasir/create"
      class="w-full md:w-auto px-5 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-2xl hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 font-bold shadow-lg hover:shadow-2xl flex items-center justify-center gap-2 text-lg"
    >
      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M12 6v6m0 0v6m0-6h6m-6 0H6"
        ></path>
      </svg>
      Tambah Data Pasien
    </a>

    <!-- Search di kanan -->
    <div class="flex items-center space-x-3 w-full md:w-auto">
      <div class="relative flex-1 md:flex-none">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Cari pasien..."
          class="w-full md:w-96 pl-5 pr-14 py-3 border border-blue-200 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 bg-blue-50 focus:bg-white text-lg shadow"
        />
        <button
          @click="performSearch"
          class="absolute right-3 top-1/2 transform -translate-y-1/2 p-2 text-blue-400 hover:text-blue-700 transition-colors"
        >
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
            ></path>
          </svg>
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Filter -->
<div class="bg-white/70 backdrop-blur-sm rounded-lg shadow-sm border border-gray-200 p-6 mb-8 mt-2">
  <div class="grid grid-cols-1 gap-4">
    <div class="w-full">
      <label for="penjamin" class="block text-sm font-medium text-gray-700 mb-2">
        <i class="fas fa-filter mr-1"></i>
        Filter Penjamin
      </label>
      <select
        id="penjamin"
        v-model="filterPenjamin"
        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
      >
        <option value="">Semua Penjamin</option>
        <option
          v-for="penjamin in uniquePenjamin"
          :key="penjamin"
          :value="penjamin"
        >
          {{ penjamin }}
        </option>
      </select>
    </div>
  </div>

  <div class="mt-3 text-sm text-gray-600">
    Menampilkan {{ filteredPasien.length }} dari {{ pasien?.length || 0 }} pasien
  </div>
</div>




      <!-- Pasien List -->
      <div v-if="filteredPasien.length > 0" class="space-y-6">
        <div
          v-for="(p, idx) in filteredPasien"
          :key="p.id"
          class="bg-white shadow-lg rounded-xl border border-gray-200 overflow-hidden hover:shadow-xl transition-shadow"
        >
          <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-4 border-b border-gray-200">
            <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center gap-4">
              <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center text-xl text-blue-700">
                  <i class="fas fa-user"></i>
                </div>
                <div>
                  <h3 class="text-xl font-semibold text-gray-900">{{ p.nama_pasien }}</h3>
                  <div class="text-sm text-gray-600 mt-1">
                    <p><span class="font-medium">Alamat:</span> {{ p.alamat }}</p>
                    <p><span class="font-medium">Penjamin:</span> 
                      <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">
                        {{ p.Penjamin }}
                      </span>
                    </p>
                  </div>
                </div>
              </div>
              
              <div class="flex flex-wrap gap-2">
                <a 
                  :href="route('kasir.show', p.id)" 
                  class="px-4 py-2 text-sm bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow transition flex items-center"
                >
                  <i class="fas fa-eye mr-1"></i>
                  Detail
                </a>
                <a
                  :href="route('kasir.edit', p.transaksi_id ?? p.id)"
                  class="px-4 py-2 text-sm bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg shadow transition flex items-center"
                >
                  <i class="fas fa-edit mr-1"></i>
                  Edit
                </a>
                <button
                  type="button"
                  class="px-4 py-2 text-sm bg-red-600 hover:bg-red-700 text-white rounded-lg shadow transition flex items-center"
                  @click="confirmDeletePasien(p)"
                >
                  <i class="fas fa-trash mr-1"></i>
                  Hapus
                </button>
              </div>
            </div>
          </div>

          <!-- Transaksi Content -->
          <div class="p-6">
            <div v-if="!p.transaksi || p.transaksi.length === 0" class="text-center py-8">
              <i class="fas fa-receipt text-4xl text-gray-300 mb-3"></i>
              <p class="text-gray-500 font-medium">Belum ada transaksi untuk pasien ini</p>
            </div>
            
            <div v-else class="space-y-6">
              <div
                v-for="(trx, tIndex) in p.transaksi"
                :key="tIndex"
                class="bg-gray-50 rounded-lg p-4 border border-gray-200"
              >
                <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center mb-4 gap-3">
                  <div class="flex items-center gap-3">
                    <i class="fas fa-calendar-alt text-blue-600"></i>
                    <div>
                      <h4 class="font-semibold text-gray-800">{{ formatDate(p.tanggal) }}</h4>
                      <div class="text-sm text-gray-600">
                        <span class="font-medium">Perawatan:</span> {{ p.perawatan }}
                        <span class="mx-2">•</span>
                        <span class="font-medium">Dokter:</span> {{ trx.dokter }}
                      </div>
                    </div>
                  </div>
                  
                  <div class="flex items-center gap-2">
                    <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">
                      Total: {{ formatCurrency(calculateSubTotal(trx, trx.detail)) }}
                    </span>
                    <button
                      @click="confirmDeleteTransaksi(trx)"
                      class="px-3 py-1 text-sm bg-red-500 hover:bg-red-600 text-white rounded shadow transition"
                    >
                      <i class="fas fa-trash"></i>
                    </button>
                  </div>
                </div>

                <div v-if="trx.detail && trx.detail.length > 0" class="overflow-x-auto">
                  <table class="w-full text-sm border border-gray-200 rounded-lg overflow-hidden">
                    <thead class="bg-blue-100">
                      <tr>
                        <th class="px-3 py-2 text-left text-blue-800 font-semibold border-b">Tindakan</th>
                        <th class="px-3 py-2 text-center text-blue-800 font-semibold border-b">Jumlah</th>
                        <th class="px-3 py-2 text-left text-blue-800 font-semibold border-b">Deskripsi</th>
                        <th class="px-3 py-2 text-right text-blue-800 font-semibold border-b">Biaya</th>
                        <th class="px-3 py-2 text-left text-blue-800 font-semibold border-b">Resep</th>
                        <th class="px-3 py-2 text-center text-blue-800 font-semibold border-b">Jumlah</th>
                        <th class="px-3 py-2 text-left text-blue-800 font-semibold border-b">Deskripsi</th>
                        <th class="px-3 py-2 text-right text-blue-800 font-semibold border-b">Biaya</th>
                        <th class="px-3 py-2 text-right text-blue-800 font-semibold border-b">Subtotal</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr
                        v-for="(d, dIndex) in trx.detail"
                        :key="dIndex"
                        class="hover:bg-blue-50 transition"
                      >
                        <td class="px-3 py-2 text-gray-700 border-b">{{ trx.tindakan || '-' }}</td>
                        <td class="px-3 py-2 text-center text-gray-700 border-b">{{ trx.jmlh || '-' }}</td>
                        <td class="px-3 py-2 text-gray-700 border-b">{{ trx.dskrps || '-' }}</td>
                        <td class="px-3 py-2 text-right text-gray-700 border-b">{{ trx.bya || '-' }}</td>
                        <td class="px-3 py-2 text-gray-700 border-b">{{ d.resep || '-' }}</td>
                        <td class="px-3 py-2 text-center text-gray-700 border-b">{{ d.jumlah || '-' }}</td>
                        <td class="px-3 py-2 text-gray-700 border-b">{{ d.deskripsi || '-' }}</td>
                        <td class="px-3 py-2 text-right text-gray-700 border-b">{{ d.biaya || '-' }}</td>
                        <td class="px-3 py-2 text-right font-semibold text-blue-700 border-b">
                          {{ formatCurrency(calculateSubTotal(trx, [d])) }}
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
                <div v-else class="text-center py-4 text-gray-500">
                  <i class="fas fa-info-circle mr-2"></i>
                  Tidak ada detail transaksi
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="text-center py-12">
        <i class="fas fa-search text-6xl text-gray-300 mb-4"></i>
        <h3 class="text-lg font-medium text-gray-500 mb-2">Tidak ada data pasien</h3>
        <p class="text-gray-400 mb-6">Mulai dengan menambahkan data pasien pertama</p>
        <a
          href="/kasir/create"
          class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow transition inline-flex items-center"
        >
          <i class="fas fa-plus mr-2"></i>
          Tambah Data Pasien
        </a>
      </div>
    </div>

    <!-- Modal -->
    <DeletePasienModal
      :show="showDeletePasienModal"
      :pasien-name="pasienToDelete?.nama_pasien || ''"
      :loading="isDeleting"
      @confirm="deletePasien"
      @cancel="cancelDeletePasien"
    />  
    <DeleteTransaksiModal
      :show="showDeleteTransaksiModal"
      :loading="isDeleting"
      @confirm="deleteTransaksi"
      @cancel="cancelDeleteTransaksi"
    />
  </AuthenticatedLayout>
</template>
