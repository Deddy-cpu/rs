<script setup>
import { ref, computed, watch } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Swal from 'sweetalert2'

// Props
const props = defineProps({
  eselons: {
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
const records = computed(() => props.eselons?.data || [])

// Methods
function goToCreateEselon() {
  router.visit('/dokter/mastering/eselon/create')
}

function editEselon(id) {
  router.visit(`/dokter/mastering/eselon/${id}/edit`)
}

function showEselon(id) {
  router.visit(`/dokter/mastering/eselon/${id}`)
}

function deleteEselon(id) {
  Swal.fire({
    title: "Apakah Anda yakin?",
    text: "Data Eselon akan dihapus permanen!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#d33",
    cancelButtonColor: "#3085d6",
    confirmButtonText: "Ya, Hapus!",
    cancelButtonText: "Batal"
  }).then((result) => {
    if (result.isConfirmed) {
      router.delete(`/dokter/mastering/eselon/${id}`, {
        onSuccess: () => {
          Swal.fire("Dihapus!", "Data Eselon berhasil dihapus.", "success").then(() => {
            router.reload({ only: ['eselons'] })
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
  router.get("/dokter/mastering/eselon", { 
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
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Data Eselon" />
    
    <div class="min-h-screen bg-cover bg-center p-6" style="background-image: url('/images/bg-login.png')">        
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
       <div class="mb-8">
  <!-- Header Tengah -->
  <div class="text-center mb-6">
    <h1 class="text-4xl md:text-5xl font-bold text-purple-600 mb-2">
      <i class="fas fa-layer-group mr-2 align-middle"></i>
      Data Eselon
    </h1>
    <p class="text-gray-600 text-lg">
      Kelola data eselon
    </p>
  </div>

  <!-- Action Bar -->
  <div class="flex flex-col md:flex-row justify-between items-center gap-4 bg-transparent px-1 pt-0 pb-0">
    <!-- Tombol Tambah -->
    <button
      @click="goToCreateEselon"
      class="w-full md:w-auto px-6 py-3 bg-purple-600 text-white rounded-2xl hover:bg-purple-700 transition-all duration-200 font-semibold shadow-lg hover:shadow-xl flex items-center justify-center gap-2 text-lg"
    >
      <i class="fas fa-plus"></i>
      Tambah Eselon
    </button>

    <!-- Search & Filter -->
    <div class="flex items-center space-x-3 w-full md:w-auto">
      <!-- Input Search -->
      <div class="relative flex-1 md:flex-none">
        <input
          v-model="search"
          @keypress.enter="performSearch"
          type="text"
          placeholder="Cari Eselon..."
          class="w-full md:w-96 pl-5 pr-14 py-3 border border-purple-300 rounded-2xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200 bg-purple-50 focus:bg-white text-lg shadow"
        />
        <button
          @click="performSearch"
          class="absolute right-3 top-1/2 transform -translate-y-1/2 p-2 text-purple-600 hover:text-purple-800 transition-colors"
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
        class="inline-flex items-center px-5 py-3 border border-purple-300 rounded-2xl bg-white text-purple-600 hover:bg-purple-50 font-semibold transition-all duration-200 shadow-md text-lg"
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
    <!-- Header -->
    <thead class="bg-purple-600/90 text-white text-sm uppercase tracking-wide">
      <tr>
        <th class="px-6 py-3 text-center">No</th>
        <th class="px-6 py-3">Grup Eselon</th>
        <th class="px-6 py-3">Deskripsi Eselon</th>
        <th class="px-6 py-3">Status</th>
        <th class="px-6 py-3">Tanggal Update</th>
        <th class="px-6 py-3">Diupdate Oleh</th>
        <th class="px-6 py-3 text-center">Aksi</th>
      </tr>
    </thead>

    <!-- Body -->
    <tbody class="divide-y divide-gray-200">
      <!-- Jika Tidak Ada Data -->
      <tr v-if="props.eselons.data.length === 0">
        <td colspan="7" class="px-6 py-10 text-center text-gray-500">
          <div class="flex flex-col items-center">
            <i class="fas fa-layer-group text-purple-300 text-4xl mb-2"></i>
            <p class="text-gray-600 font-medium">Belum ada data Eselon.</p>
            <button
              @click="goToCreateEselon"
              class="mt-4 px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition shadow-sm"
            >
              <i class="fas fa-plus mr-2"></i>
              Tambah Eselon Pertama
            </button>
          </div>
        </td>
      </tr>

      <!-- Jika Ada Data -->
      <tr
        v-for="(record, index) in props.eselons.data"
        :key="record.id"
        class="hover:bg-purple-50 transition duration-150 ease-in-out"
      >
        <td class="px-6 py-3 text-center font-semibold text-gray-800">
          {{ (props.eselons.current_page - 1) * props.eselons.per_page + index + 1 }}
        </td>
        <td class="px-6 py-3">{{ record.grp_eselon?.grp_eselon_desc || '-' }}</td>
        <td class="px-6 py-3">{{ record.eselon_desc }}</td>
        <td class="px-6 py-3">
          <span
            class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
            :class="getStatusBadge(record.aktif)"
          >
            {{ getStatusText(record.aktif) }}
          </span>
        </td>
        <td class="px-6 py-3 text-gray-600">{{ formatDate(record.update_date) }}</td>
        <td class="px-6 py-3 text-gray-600">{{ record.update_by || '-' }}</td>

        <!-- Tombol Aksi -->
        <td class="px-6 py-3 text-center">
          <div class="flex justify-center items-center gap-2">

            <button
              @click="editEselon(record.id)"
              class="px-3 py-1.5 bg-yellow-500 text-white rounded-md hover:bg-yellow-600 transition shadow-sm flex items-center gap-1"
              title="Edit"
            >
              <i class="fas fa-edit"></i>
              <span>Edit</span>
            </button>

            <button
              @click="deleteEselon(record.id)"
              class="px-3 py-1.5 bg-red-600 text-white rounded-md hover:bg-red-700 transition shadow-sm flex items-center gap-1"
              title="Hapus"
            >
              <i class="fas fa-trash"></i>
              <span>Hapus</span>
            </button>
          </div>
        </td>
      </tr>
    </tbody>
  </table>

  <!-- Pagination -->
  <div
  v-if="props.eselons.links && props.eselons.links.length > 3"
  class="bg-white/70 backdrop-blur-sm border-t border-gray-200 px-6 py-4 flex justify-between items-center"
>
  <p class="text-sm text-gray-700">
    Menampilkan
    <span class="font-semibold">{{ (props.eselons.current_page - 1) * props.eselons.per_page + 1 }}</span>
    -
    <span class="font-semibold">{{
      Math.min(props.eselons.current_page * props.eselons.per_page, props.eselons.total)
    }}</span>
    dari
    <span class="font-semibold">{{ props.eselons.total }}</span>
    data
  </p>

  <div class="flex space-x-2">
    <template v-for="link in props.eselons.links" :key="link.label">
      <button
        v-if="link.url"
        @click="router.visit(link.url, { preserveState: true })"
        class="px-3 py-1 rounded-lg text-sm"
        :class="link.active ? 'bg-purple-600 text-white' : 'bg-white border text-gray-700 hover:bg-gray-100'"
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
