<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue"
import { Head, router } from "@inertiajs/vue3"
import { ref, computed } from "vue"
import DeletePasienModal from "@/Components/DeletePasienModal.vue"
import DeleteTransaksiModal from "@/Components/DeleteTransaksiModal.vue"

const props = defineProps({
<<<<<<< HEAD
  pasien: Object,   // hasil paginate, bukan array biasa
  filters: Object,  // untuk simpan search query
})

=======
  pasien: Array, // [{id, nama_pasien, alamat, Penjamin, tanggal, transaksi_id}]
  flash: {
    type: Object,
    default: () => ({})
  }
})

// Modal states
>>>>>>> daf935dd1c37511b1a31fbb7777979201442d99a
const showDeletePasienModal = ref(false)
const showDeleteTransaksiModal = ref(false)
const pasienToDelete = ref(null)
const transaksiToDelete = ref(null)
const isDeleting = ref(false)

<<<<<<< HEAD
// Search
const search = ref(props.filters?.search || "")

function performSearch() {
  router.get(route("kasir.index"), { search: search.value }, { preserveState: true, replace: true })
}

// Konfirmasi hapus pasien
=======
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
>>>>>>> daf935dd1c37511b1a31fbb7777979201442d99a
function confirmDeletePasien(pasien) {
  pasienToDelete.value = pasien
  showDeletePasienModal.value = true
}

<<<<<<< HEAD
// Konfirmasi hapus transaksi
=======
>>>>>>> daf935dd1c37511b1a31fbb7777979201442d99a
function confirmDeleteTransaksi(transaksi) {
  transaksiToDelete.value = transaksi
  showDeleteTransaksiModal.value = true
}

<<<<<<< HEAD
// Hapus pasien
=======
>>>>>>> daf935dd1c37511b1a31fbb7777979201442d99a
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

<<<<<<< HEAD
// Hapus transaksi
=======
>>>>>>> daf935dd1c37511b1a31fbb7777979201442d99a
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

<<<<<<< HEAD
// Cancel modal
=======
>>>>>>> daf935dd1c37511b1a31fbb7777979201442d99a
function cancelDeletePasien() {
  showDeletePasienModal.value = false
  pasienToDelete.value = null
}
function cancelDeleteTransaksi() {
  showDeleteTransaksiModal.value = false
  transaksiToDelete.value = null
}

<<<<<<< HEAD
function goToCreatePasien() {
  router.get("/kasir/create")   // <-- ini yang jalanin redirect
}

// Hitung total biaya
function totalBiaya(trx) {
  if (!trx || !trx.detail) return 0
=======
// Improved calculation function
function calculateSubTotal(trx, detail) {
>>>>>>> daf935dd1c37511b1a31fbb7777979201442d99a
  let total = 0
  if (trx.bya && trx.jmlh) {
    const tindakanBiaya = parseFloat(trx.bya.replace(/[^\d]/g, '')) || 0
    const tindakanJumlah = parseInt(trx.jmlh) || 0
    total += tindakanBiaya * tindakanJumlah
  }
<<<<<<< HEAD
  if (trx.detail && Array.isArray(trx.detail)) {
    trx.detail.forEach((d) => {
=======
  
  // Hitung biaya resep dari detail
  if (detail && Array.isArray(detail)) {
    detail.forEach(d => {
>>>>>>> daf935dd1c37511b1a31fbb7777979201442d99a
      if (d.biaya && d.jumlah) {
        const resepBiaya = parseFloat(d.biaya.replace(/[^\d]/g, '')) || 0
        const resepJumlah = parseInt(d.jumlah) || 0
        total += resepBiaya * resepJumlah
      }
    })
  }
<<<<<<< HEAD
  return total.toLocaleString("id-ID")
=======
  
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
>>>>>>> daf935dd1c37511b1a31fbb7777979201442d99a
}
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Daftar Pasien & Transaksi" />

<<<<<<< HEAD
    <div
      class="min-h-screen bg-cover bg-center p-6"
      style="background-image: url('/images/bg-login.png')"
    >
      
        
        <!-- Header + Search -->
        <div class="mb-6">
          <h2 class="text-3xl font-extrabold text-red-700 tracking-wide flex items-center gap-2 justify-center text-center mb-4">
            📝 Daftar Pasien
          </h2>

          <div class="flex justify-between items-center">
            <button
              @click="goToCreatePasien"
              class="px-5 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-medium shadow"
            >
              + Tambah Pasien 
            </button>

            <div class="flex items-center space-x-2">
              <input
                v-model="search"
                type="text"
                placeholder="Cari resep..."
                class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
              />
              <button
                @click="performSearch"
                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition"
              >
                🔍 Cari
              </button>
            </div>
          </div>
        </div>

        <!-- 🔹 Empty State kalau tidak ada pasien -->
        <div
          v-if="!props.pasien.data.length"
          class="flex flex-col items-center justify-center py-20 text-gray-500"
        >
          <span class="text-5xl mb-4">📭</span>
          <p class="text-lg font-medium">Belum ada data pasien.</p>
          <a
            href="/kasir/create"
            class="mt-4 px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition"
          >
            ➕ Tambah Pasien Pertama
          </a>
        </div>

        <!-- Loop Pasien -->
        <div
          v-else
          v-for="(p, idx) in props.pasien.data"
          :key="p.id"
          class="bg-white/80 shadow-lg rounded-xl border border-gray-200 mb-8 p-6 hover:shadow-2xl transition"
        >
          <div class="flex justify-between items-center mb-5">
            <div class="flex items-center gap-4">
              <div
                class="w-14 h-14 rounded-full bg-blue-100 flex items-center justify-center text-3xl text-blue-700 shadow"
              >
                👤
              </div>
              <div>
                <p class="text-xl font-bold text-gray-800">{{ p.nama_pasien }}</p>
                <p class="text-sm text-gray-500 mt-1">
                  <span class="font-semibold">Alamat:</span> {{ p.alamat }}<br />
                  <span class="font-semibold">Penjamin:</span> {{ p.Penjamin }}
                </p>
              </div>
            </div>
            <div class="flex gap-2">
              <a
                :href="route('kasir.show', p.id)"
                class="px-4 py-2 text-sm bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow transition"
              >
                🔍 Detail
              </a>
              <a
                :href="route('kasir.edit', p.transaksi_id ?? p.id)"
                class="px-4 py-2 text-sm bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg shadow transition"
              >
                ✏️ Edit
              </a>
              <button
                type="button"
                class="px-4 py-2 text-sm bg-red-600 hover:bg-red-700 text-white rounded-lg shadow transition"
                @click="confirmDeletePasien(p)"
              >
                🗑️ Hapus
              </button>
            </div>
          </div>

          <!-- Transaksi -->
          <div class="border-t border-gray-200 pt-4">
            <div
              v-if="!p.transaksi || p.transaksi.length === 0"
              class="text-gray-400 italic text-center py-6"
            >
              Tidak ada transaksi untuk pasien ini.
            </div>

            <div
              v-for="(trx, tIndex) in p.transaksi"
              :key="tIndex"
              class="mb-6 bg-gray-50 rounded-xl shadow-inner p-5 border border-gray-100"
            >
              <div class="flex justify-between items-center mb-3">
                <h3 class="font-semibold text-lg text-blue-700 flex items-center gap-2">
                  <span>📅</span>
                  <span>{{ p.tanggal }}</span>
                  <span class="text-gray-500">|</span>
                  <span>{{ p.perawatan }}</span>
                  <span class="text-gray-500">|</span>
                  <span class="italic text-gray-600">Dokter: {{ trx.dokter }}</span>
                </h3>
                <button
                  @click="confirmDeleteTransaksi(trx)"
                  class="px-3 py-1.5 text-sm bg-red-500 hover:bg-red-600 text-white rounded shadow transition"
                >
                  🗑️ Hapus Transaksi
                </button>
              </div>

              <!-- Detail -->
              <div class="overflow-x-auto">
                <table class="w-full text-sm border border-gray-200 rounded-xl overflow-hidden shadow">
                  <thead class="bg-gradient-to-r from-blue-100 to-blue-200">
                    <tr>
                      <th class="p-2 border text-blue-800 font-semibold">Tindakan</th>
                      <th class="p-2 border text-blue-800 font-semibold">Jumlah</th>
                      <th class="p-2 border text-blue-800 font-semibold">Deskripsi</th>
                      <th class="p-2 border text-blue-800 font-semibold">Biaya</th>
                      <th class="p-2 border text-blue-800 font-semibold">Resep</th>
                      <th class="p-2 border text-blue-800 font-semibold">Jumlah</th>
                      <th class="p-2 border text-blue-800 font-semibold">Deskripsi</th>
                      <th class="p-2 border text-blue-800 font-semibold">Biaya</th>
                      <th class="p-2 border text-blue-800 font-semibold">SubTotal (Rp)</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr
                      v-for="(d, dIndex) in trx.detail"
                      :key="dIndex"
                      class="hover:bg-blue-50 transition"
                    >
                      <td class="p-2 border text-gray-700">{{ trx.tindakan }}</td>
                      <td class="p-2 border text-center text-gray-700">{{ trx.jmlh }}</td>
                      <td class="p-2 border text-gray-700">{{ trx.dskrps }}</td>
                      <td class="p-2 border text-right text-gray-700">{{ trx.bya }} RP</td>
                      <td class="p-2 border text-gray-700">{{ d.resep }}</td>
                      <td class="p-2 border text-center text-gray-700">{{ d.jumlah }}</td>
                      <td class="p-2 border text-gray-700">{{ d.deskripsi }}</td>
                      <td class="p-2 border text-right text-gray-700">{{ d.biaya }} RP</td>
                      <td class="p-2 border text-right font-bold text-blue-700 bg-blue-50">
                        {{ totalBiaya(trx) }} RP
                      </td>
                    </tr>
                  </tbody>
                </table>
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
            <i class="fas fa-users mr-3 text-blue-600"></i>
            Daftar Pasien & Transaksi
          </h1>
          <p class="text-gray-600 mt-1">Kelola data pasien dan transaksi medis</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-3">
          <a
            href="/kasir/create"
            class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow transition flex items-center justify-center"
          >
            <i class="fas fa-plus mr-2"></i>
            Tambah Data Pasien
          </a>
        </div>
      </div>

      <!-- Search and Filter -->
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <!-- Search -->
          <div>
            <label for="search" class="block text-sm font-medium text-gray-700 mb-2">
              <i class="fas fa-search mr-1"></i>
              Cari Pasien
            </label>
            <input
              type="text"
              id="search"
              v-model="searchQuery"
              placeholder="Cari berdasarkan nama atau alamat..."
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            />
          </div>
          
          <!-- Filter Penjamin -->
          <div>
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
              <option v-for="penjamin in uniquePenjamin" :key="penjamin" :value="penjamin">
                {{ penjamin }}
              </option>
            </select>
          </div>
        </div>
        
        <!-- Results count -->
        <div class="mt-4 text-sm text-gray-600">
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
          <!-- Pasien Header -->
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
              
              <!-- Action Buttons -->
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
            
            <!-- Transaksi List -->
            <div v-else class="space-y-6">
              <div
                v-for="(trx, tIndex) in p.transaksi"
                :key="tIndex"
                class="bg-gray-50 rounded-lg p-4 border border-gray-200"
              >
                <!-- Transaksi Header -->
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

                <!-- Detail Transaksi Table -->
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
                
                <!-- No Detail Message -->
                <div v-else class="text-center py-4 text-gray-500">
                  <i class="fas fa-info-circle mr-2"></i>
                  Tidak ada detail transaksi
                </div>
>>>>>>> daf935dd1c37511b1a31fbb7777979201442d99a
              </div>
            </div>
          </div>
        </div>
<<<<<<< HEAD
=======
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
>>>>>>> daf935dd1c37511b1a31fbb7777979201442d99a

        <!-- Pagination -->
        <div v-if="props.pasien.data.length" class="flex justify-start mt-4">
          <div class="flex space-x-2">
            <template v-for="link in props.pasien.links" :key="link.label">
              <button
                v-if="link.url"
                @click="router.visit(link.url, { preserveState: true })"
                class="px-3 py-1 rounded-lg text-sm"
                :class="link.active
                  ? 'bg-blue-600 text-white'
                  : 'bg-white border text-gray-700 hover:bg-gray-100'"
                v-html="link.label"
              />
              <span
                v-else
                class="px-3 py-1 text-gray-400 text-sm"
                v-html="link.label"
              ></span>
            </template>
          </div>
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
