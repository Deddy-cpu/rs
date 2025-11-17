<script setup>
import { ref, computed, watch } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Swal from 'sweetalert2'

// Props
const props = defineProps({
  tindakanTarifs: {
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
let searchTimeout = null
// Computed
const records = computed(() => props.tindakanTarifs?.data || [])

// Methods
function goToCreateTindakanTarif() {
  router.visit('/dokter/mastering/tindakan-tarif/create')
}

function editTindakanTarif(id) {
  router.visit(`/dokter/mastering/tindakan-tarif/${id}/edit`)
}

function showTindakanTarif(id) {
  router.visit(`/dokter/mastering/tindakan-tarif/${id}`)
}

function deleteTindakanTarif(id) {
  Swal.fire({
    title: "Apakah Anda yakin?",
    text: "Data Tindakan Tarif akan dihapus permanen!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#d33",
    cancelButtonColor: "#3085d6",
    confirmButtonText: "Ya, Hapus!",
    cancelButtonText: "Batal"
  }).then((result) => {
    if (result.isConfirmed) {
      router.delete(`/dokter/mastering/tindakan-tarif/${id}`, {
        onSuccess: () => {
          Swal.fire("Dihapus!", "Data Tindakan Tarif berhasil dihapus.", "success").then(() => {
            router.reload({ only: ['tindakanTarifs'] })
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
  router.get("/dokter/mastering/tindakan-tarif", { 
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

watch(search, (newSearch) => {
  if (searchTimeout) {
    clearTimeout(searchTimeout)
  }
  searchTimeout = setTimeout(() => {
    performSearch()
  }, 500)
})

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
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Data Tindakan Tarif" />
    
    <div class="min-h-screen bg-cover bg-center p-6" style="background-image: url('/images/bg-login.png')">        
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
<div class="mb-8">
  <!-- Header Tengah -->
  <div class="text-center mb-6">
    <h1 class="text-4xl md:text-5xl font-bold bg-gradient-to-r from-yellow-500 to-amber-600 bg-clip-text text-transparent mb-2">
      <i class="fas fa-money-bill-wave mr-2 align-middle"></i>
      Data Tindakan Tarif
    </h1>
    <p class="text-gray-600 text-lg">
      Kelola tarif tindakan medis berdasarkan eselon
    </p>
  </div>

  <!-- Action Bar -->
  <div class="flex flex-col md:flex-row justify-between items-center gap-4 bg-transparent px-1 pt-0 pb-0">
    <!-- Tombol Tambah -->
    <button
      @click="goToCreateTindakanTarif"
      class="w-full md:w-auto px-6 py-3 bg-gradient-to-r from-yellow-500 to-amber-600 text-white rounded-2xl hover:from-yellow-600 hover:to-amber-700 transition-all duration-200 font-semibold shadow-lg hover:shadow-xl flex items-center justify-center gap-2 text-lg"
    >
      <i class="fas fa-plus"></i>
      Tambah Tindakan Tarif
    </button>

    <!-- Search & Filter -->
    <div class="flex items-center space-x-3 w-full md:w-auto">
      <!-- Input Search -->
      <div class="relative flex-1 md:flex-none">
        <input
          v-model="search"
          @keypress.enter="performSearch"
          type="text"
          placeholder="Cari Tindakan Tarif..."
          class="w-full md:w-96 pl-5 pr-14 py-3 border border-yellow-300 rounded-2xl focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition-all duration-200 bg-yellow-50 focus:bg-white text-lg shadow"
        />
        <button
          @click="performSearch"
          class="absolute right-3 top-1/2 transform -translate-y-1/2 p-2 text-yellow-600 hover:text-amber-700 transition-colors"
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

      <!-- Sort Button -->
      <button
        @click="toggleOrder"
        class="inline-flex items-center px-5 py-3 border border-yellow-300 rounded-2xl bg-white text-yellow-700 hover:bg-yellow-50 font-semibold transition-all duration-200 shadow-md text-lg"
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
    <thead class="bg-yellow-500/90 text-white text-sm uppercase tracking-wide">
      <tr>
        <th class="px-6 py-3 text-center">No</th>
        <th class="px-6 py-3">Tindakan Q</th>
        <th class="px-6 py-3">Eselon</th>
        <th class="px-6 py-3">Tarif</th>
        <th class="px-6 py-3">Status</th>
        <th class="px-6 py-3">Tanggal Update</th>
        <th class="px-6 py-3">Diupdate Oleh</th>
        <th class="px-6 py-3 text-center">Aksi</th>
      </tr>
    </thead>

    <tbody class="divide-y divide-gray-200">
      <!-- Jika tidak ada data -->
      <tr v-if="records.length === 0">
        <td colspan="8" class="px-6 py-10 text-center text-gray-500">
          <div class="flex flex-col items-center">
            <span class="text-4xl mb-2">📭</span>
            <p class="text-gray-600 font-medium">Belum ada data Tindakan Tarif.</p>
            <button
              @click="goToCreateTindakanTarif"
              class="mt-4 px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition"
            >
              <i class="fas fa-plus mr-2"></i>
              Tambah Tindakan Tarif Pertama
            </button>
          </div>
        </td>
      </tr>

      <!-- Jika ada data -->
      <tr
        v-for="(record, index) in records"
        :key="record.id"
        class="hover:bg-yellow-50 transition duration-150 ease-in-out"
      >
        <td class="px-6 py-3 text-center font-semibold text-gray-800">
          {{ (props.tindakanTarifs.current_page - 1) * props.tindakanTarifs.per_page + index + 1 }}
        </td>
        <td class="px-6 py-3">{{ record.tindakan_q?.tindakan_q_desc || '-' }}</td>
        <td class="px-6 py-3">{{ record.grp_eselon?.grp_eselon_desc || '-' }}</td>
        <td class="px-6 py-3 font-semibold text-gray-900">{{ formatCurrency(record.tarif) }}</td>
        <td class="px-6 py-3">
          <span
            class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
            :class="record.aktif === 'Y' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
          >
            {{ record.aktif === 'Y' ? 'Aktif' : 'Tidak Aktif' }}
          </span>
        </td>
        <td class="px-6 py-3 text-gray-600">{{ formatDate(record.update_date) }}</td>
        <td class="px-6 py-3 text-gray-600">{{ record.update_by || '-' }}</td>

        <!-- Tombol aksi -->
        <td class="px-6 py-3 text-center">
          <div class="flex justify-center items-center gap-2">

            <!-- Edit -->
            <button
              @click="editTindakanTarif(record.id)"
              class="px-3 py-1.5 bg-yellow-500 text-white rounded-md hover:bg-yellow-600 transition shadow-sm flex items-center gap-1"
              title="Edit"
            >
              <i class="fas fa-edit"></i>
              <span>Edit</span>
            </button>

            <!-- Hapus -->
            <button
              @click="deleteTindakanTarif(record.id)"
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
  v-if="props.tindakanTarifs.links && props.tindakanTarifs.links.length > 3"
  class="bg-white/70 backdrop-blur-sm border-t border-gray-200 px-6 py-4 flex justify-between items-center rounded-b-xl"
>
  <p class="text-sm text-gray-700">
    Menampilkan
    <span class="font-semibold">{{ (props.tindakanTarifs.current_page - 1) * props.tindakanTarifs.per_page + 1 }}</span>
    -
    <span class="font-semibold">{{
      Math.min(props.tindakanTarifs.current_page * props.tindakanTarifs.per_page, props.tindakanTarifs.total)
    }}</span>
    dari
    <span class="font-semibold">{{ props.tindakanTarifs.total }}</span>
    data
  </p>

  <div class="flex space-x-2">
    <template v-for="link in props.tindakanTarifs.links" :key="link.label">
      <button
        v-if="link.url"
        @click="router.visit(link.url, { preserveState: true })"
        class="px-3 py-1 rounded-lg text-sm"
        :class="link.active ? 'bg-yellow-500 text-white' : 'bg-white border text-gray-700 hover:bg-gray-100'"
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
