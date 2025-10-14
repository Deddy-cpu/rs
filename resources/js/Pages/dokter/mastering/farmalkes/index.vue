<script setup>
import { ref, computed } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Swal from 'sweetalert2'

// Props
const props = defineProps({
  farmalkes: {
    type: Object,
    required: true
  },
  filters: {
    type: Object,
    required: true
  }
})

// Reactive data
const search = ref(props.filters.search || '')
const order = ref(props.filters.order || 'asc')

// Computed
const records = computed(() => props.farmalkes?.data || [])

// Methods
function goToCreateFarmalkes() {
  router.visit('/dokter/mastering/farmalkes/create')
}

function editFarmalkes(id) {
  router.visit(`/dokter/mastering/farmalkes/${id}/edit`)
}

function showFarmalkes(id) {
  router.visit(`/dokter/mastering/farmalkes/${id}`)
}

function deleteFarmalkes(id) {
  Swal.fire({
    title: "Apakah Anda yakin?",
    text: "Data Farmalkes akan dihapus permanen!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#d33",
    cancelButtonColor: "#3085d6",
    confirmButtonText: "Ya, Hapus!",
    cancelButtonText: "Batal"
  }).then((result) => {
    if (result.isConfirmed) {
      router.delete(`/dokter/mastering/farmalkes/${id}`, {
        onSuccess: () => {
          Swal.fire("Dihapus!", "Data Farmalkes berhasil dihapus.", "success").then(() => {
            router.reload({ only: ['farmalkes'] })
          })
        }
      })
    }
  })
}

function toggleOrder() {
  order.value = order.value === 'asc' ? 'desc' : 'asc'
  performSearch()
}

function performSearch() {
  router.get("/dokter/mastering/farmalkes", { 
    search: search.value,
    order: order.value 
  }, { 
    preserveState: true, 
    replace: true 
  })
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

function getStatusBadge(aktif) {
  if (aktif === 'Y') {
    return 'bg-green-100 text-green-800'
  }
  return 'bg-red-100 text-red-800'
}

function getStatusText(aktif) {
  return aktif === 'Y' ? 'Aktif' : 'Tidak Aktif'
}

function formatCurrency(amount) {
  if (amount === null || amount === undefined || amount === '') return '-'
  
  // Pastikan amount adalah string atau number
  let numericValue = amount
  
  // Jika string, bersihkan dari pemisah ribuan yang mungkin masih ada
  if (typeof amount === 'string') {
    numericValue = amount.replace(/[^\d.,]/g, '')
    numericValue = numericValue.replace(',', '.')
  }
  
  // Konversi ke float
  const floatAmount = parseFloat(numericValue)
  if (isNaN(floatAmount)) return '-'
  
  // Format dengan currency
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
    maximumFractionDigits: 2
  }).format(floatAmount)
}

function getTypeBadge(type) {
  if (type === 'farmasi') {
    return 'bg-blue-100 text-blue-800'
  } else if (type === 'alkes') {
    return 'bg-purple-100 text-purple-800'
  }
  return 'bg-gray-100 text-gray-800'
}

function getTypeText(type) {
  if (type === 'farmasi') {
    return 'Farmasi'
  } else if (type === 'alkes') {
    return 'Alkes'
  }
  return 'Lainnya'
}
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Data Farmalkes" />
    
    <div class="min-h-screen bg-cover bg-center p-6" style="background-image: url('/images/bg-login.png')">        
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
       <div class="mb-8">
  <!-- Header Tengah -->
  <div class="text-center mb-6">
    <h1
      class="text-4xl md:text-5xl font-bold bg-gradient-to-r from-red-600 to-rose-600 bg-clip-text text-transparent mb-2"
    >
      <i class="fas fa-pills mr-2 align-middle"></i>
      Data Farmalkes
    </h1>
    <p class="text-gray-600 text-lg">
      Kelola data farmasi dan alat kesehatan
    </p>
  </div>

  <!-- Action Bar -->
  <div
    class="flex flex-col md:flex-row justify-between items-center gap-4 bg-transparent px-1 pt-0 pb-0"
  >
    <!-- Tombol Tambah -->
    <button
      @click="goToCreateFarmalkes"
      class="w-full md:w-auto px-6 py-3 bg-gradient-to-r from-red-600 to-rose-600 text-white rounded-2xl hover:from-red-700 hover:to-rose-700 transition-all duration-200 font-semibold shadow-lg hover:shadow-xl flex items-center justify-center gap-2 text-lg"
    >
      <i class="fas fa-plus"></i>
      Tambah Farmalkes
    </button>

    <!-- Search & Filter -->
    <div class="flex items-center space-x-3 w-full md:w-auto">
      <!-- Input Search -->
      <div class="relative flex-1 md:flex-none">
        <input
          v-model="search"
          @keypress.enter="performSearch"
          type="text"
          placeholder="Cari Farmalkes..."
          class="w-full md:w-96 pl-5 pr-14 py-3 border border-red-200 rounded-2xl focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all duration-200 bg-red-50 focus:bg-white text-lg shadow"
        />
        <button
          @click="performSearch"
          class="absolute right-3 top-1/2 transform -translate-y-1/2 p-2 text-red-500 hover:text-red-700 transition-colors"
        >
          <svg
            class="w-6 h-6"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
            ></path>
          </svg>
        </button>
      </div>

      <!-- Sort Button -->
      <button
        @click="toggleOrder"
        class="inline-flex items-center px-5 py-3 border border-red-200 rounded-2xl bg-white text-red-600 hover:bg-red-50 font-semibold transition-all duration-200 shadow-md text-lg"
      >
        <i class="fas fa-sort mr-2"></i>
        {{ order === 'asc' ? 'A-Z' : 'Z-A' }}
      </button>
    </div>
  </div>
</div>



        <!-- Table -->
        <div class="overflow-x-auto shadow-md rounded-xl border border-gray-200 bg-white/70 backdrop-blur-sm">
  <table class="w-full text-sm text-left text-gray-700">
    <thead class="bg-red-600/90 text-white text-sm uppercase tracking-wide">
      <tr>
        <th class="px-6 py-3 text-center">No</th>
        <th class="px-6 py-3">Kode</th>
        <th class="px-6 py-3">Nama Item</th>
        <th class="px-6 py-3">Jenis</th>
        <th class="px-6 py-3">Kategori</th>
        <th class="px-6 py-3">Satuan</th>
        <th class="px-6 py-3">Harga</th>
        <th class="px-6 py-3">Stok</th>
        <th class="px-6 py-3">Status</th>
        <th class="px-6 py-3">Tanggal Update</th>
        <th class="px-6 py-3 text-center">Aksi</th>
      </tr>
    </thead>

    <tbody class="divide-y divide-gray-200">
      <!-- Jika Tidak Ada Data -->
      <tr v-if="records.length === 0">
        <td colspan="11" class="px-6 py-10 text-center text-gray-500">
          <div class="flex flex-col items-center">
            <span class="text-4xl mb-2">📭</span>
            <p class="text-gray-600 font-medium">Belum ada data Farmalkes.</p>
            <button
              @click="goToCreateFarmalkes"
              class="mt-4 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition"
            >
              Tambah Farmalkes Pertama
            </button>
          </div>
        </td>
      </tr>

      <!-- Jika Ada Data -->
      <tr
        v-for="(record, idx) in records"
        :key="record.id"
        class="hover:bg-red-50 transition duration-150 ease-in-out"
      >
        <td class="px-6 py-3 text-center font-semibold text-gray-800">
          {{ (props.farmalkes.current_page - 1) * props.farmalkes.per_page + idx + 1 }}
        </td>
        <td class="px-6 py-3">{{ record.kode || '-' }}</td>
        <td class="px-6 py-3">
          <div class="font-medium text-gray-900">{{ record.nama_item || '-' }}</div>
          <div class="text-gray-500 text-xs">{{ record.deskripsi || '' }}</div>
        </td>
        <td class="px-6 py-3">
          <span
            class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
            :class="getTypeBadge(record.jenis)"
          >
            {{ getTypeText(record.jenis) }}
          </span>
        </td>
        <td class="px-6 py-3">{{ record.kategori || '-' }}</td>
        <td class="px-6 py-3">{{ record.satuan || '-' }}</td>
        <td class="px-6 py-3 font-medium text-gray-900">{{ formatCurrency(record.harga) }}</td>
        <td class="px-6 py-3">
          <span :class="record.stok <= 10 ? 'text-red-600 font-semibold' : ''">
            {{ record.stok || 0 }}
          </span>
        </td>
        <td class="px-6 py-3">
          <div class="flex items-center">
            <div
              :class="[ 'w-2.5 h-2.5 rounded-full mr-2', record.aktif === 'Y' ? 'bg-green-500' : 'bg-red-500' ]"
            ></div>
            <span
              :class="[ 'text-sm font-medium', record.aktif === 'Y' ? 'text-green-600' : 'text-red-600' ]"
            >
              {{ getStatusText(record.aktif) }}
            </span>
          </div>
        </td>
        <td class="px-6 py-3 text-sm text-gray-500">{{ formatDate(record.updated_at) }}</td>

        <!-- Tombol Aksi -->
        <td class="px-6 py-3 text-center">
          <div class="flex justify-center items-center gap-2">
            <!-- Lihat -->
            <button
              @click="showFarmalkes(record.id)"
              class="px-3 py-1.5 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition shadow-sm flex items-center gap-1"
              title="Lihat Detail"
            >
              <i class="fas fa-eye"></i>
              <span>Show</span>
            </button>

            <!-- Edit -->
            <button
              @click="editFarmalkes(record.id)"
              class="px-3 py-1.5 bg-yellow-500 text-white rounded-md hover:bg-yellow-600 transition shadow-sm flex items-center gap-1"
              title="Edit"
            >
              <i class="fas fa-edit"></i>
              <span>Edit</span>
            </button>

            <!-- Hapus -->
            <button
              @click="deleteFarmalkes(record.id)"
              class="px-3 py-1.5 bg-red-500 text-white rounded-md hover:bg-red-600 transition shadow-sm flex items-center gap-1"
              title="Hapus"
            >
              <i class="fas fa-trash-alt"></i>
              <span>Hapus</span>
            </button>
          </div>
        </td>
      </tr>
    </tbody>
  </table>

  <!-- Pagination -->
  <div
    v-if="props.farmalkes.links && props.farmalkes.links.length > 3"
    class="bg-white/70 backdrop-blur-sm border-t border-gray-200 px-6 py-4 flex justify-between items-center"
  >
    <p class="text-sm text-gray-700">
      Menampilkan
      <span class="font-semibold">{{ (props.farmalkes.current_page - 1) * props.farmalkes.per_page + 1 }}</span>
      -
      <span class="font-semibold">{{
        Math.min(props.farmalkes.current_page * props.farmalkes.per_page, props.farmalkes.total)
      }}</span>
      dari
      <span class="font-semibold">{{ props.farmalkes.total }}</span>
      data
    </p>

    <div class="flex space-x-2">
      <template v-for="link in props.farmalkes.links" :key="link.label">
        <button
          v-if="link.url"
          @click="router.visit(link.url, { preserveState: true })"
          class="px-3 py-1 rounded-lg text-sm"
          :class="link.active ? 'bg-red-600 text-white' : 'bg-white border text-gray-700 hover:bg-gray-100'"
          v-html="link.label"
        />
        <span
          v-else
          class="px-3 py-1 text-gray-400 text-sm"
          v-html="link.label"
        />
      </template>
    </div>
  </div>
</div>

      </div>
    </div>
  </AuthenticatedLayout>
</template>
