<script setup>
import { ref, computed } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Swal from 'sweetalert2'

// Props
const props = defineProps({
  tindakanQs: {
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
const records = computed(() => props.tindakanQs?.data || [])

// Methods
function goToCreateTindakanQ() {
  router.visit('/dokter/mastering/tindakanq/create')
}

function editTindakanQ(id) {
  router.visit(`/dokter/mastering/tindakanq/${id}/edit`)
}

function showTindakanQ(id) {
  router.visit(`/dokter/mastering/tindakanq/${id}`)
}

function deleteTindakanQ(id) {
  Swal.fire({
    title: "Apakah Anda yakin?",
    text: "Data Tindakan Q akan dihapus permanen!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#d33",
    cancelButtonColor: "#3085d6",
    confirmButtonText: "Ya, Hapus!",
    cancelButtonText: "Batal"
  }).then((result) => {
    if (result.isConfirmed) {
      router.delete(`/dokter/mastering/tindakanq/${id}`, {
        onSuccess: () => {
          Swal.fire("Dihapus!", "Data Tindakan Q berhasil dihapus.", "success").then(() => {
            router.reload({ only: ['tindakanQs'] })
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
  router.get("/dokter/mastering/tindakanq", { 
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
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Data Tindakan Q" />
    
    <div class="min-h-screen bg-cover bg-center p-6" style="background-image: url('/images/bg-login.png')">        
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
<div class="mb-8">
  <!-- Header Tengah -->
  <div class="text-center mb-6">
    <h1 class="text-4xl md:text-5xl font-bold text-green-600 mb-2">
      <i class="fas fa-procedures mr-2 align-middle"></i>
      Data Tindakan Q
    </h1>
    <p class="text-gray-600 text-lg">
      Kelola data tindakan medis
    </p>
  </div>

  <!-- Action Bar -->
  <div class="flex flex-col md:flex-row justify-between items-center gap-4 bg-transparent px-1 pt-0 pb-0">
    <!-- Tombol Tambah -->
    <button
      @click="goToCreateTindakanQ"
      class="w-full md:w-auto px-6 py-3 bg-green-600 text-white rounded-2xl hover:bg-green-700 transition-all duration-200 font-semibold shadow-lg hover:shadow-xl flex items-center justify-center gap-2 text-lg"
    >
      <i class="fas fa-plus"></i>
      Tambah Tindakan Q
    </button>

    <!-- Search & Filter -->
    <div class="flex items-center space-x-3 w-full md:w-auto">
      <!-- Input Search -->
      <div class="relative flex-1 md:flex-none">
        <input
          v-model="search"
          @keypress.enter="performSearch"
          type="text"
          placeholder="Cari Tindakan Q..."
          class="w-full md:w-96 pl-5 pr-14 py-3 border border-green-300 rounded-2xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200 bg-green-50 focus:bg-white text-lg shadow"
        />
        <button
          @click="performSearch"
          class="absolute right-3 top-1/2 transform -translate-y-1/2 p-2 text-green-600 hover:text-green-800 transition-colors"
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
        class="inline-flex items-center px-5 py-3 border border-green-300 rounded-2xl bg-white text-green-600 hover:bg-green-50 font-semibold transition-all duration-200 shadow-md text-lg"
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
    <thead class="bg-green-600/90 text-white text-sm uppercase tracking-wide">
      <tr>
        <th class="px-6 py-3 text-center">No</th>
        <th class="px-6 py-3">Deskripsi Tindakan Q</th>
        <th class="px-6 py-3">Status</th>
        <th class="px-6 py-3">Tanggal Update</th>
        <th class="px-6 py-3">Diupdate Oleh</th>
        <th class="px-6 py-3 text-center">Aksi</th>
      </tr>
    </thead>

    <tbody class="divide-y divide-gray-200">
      <!-- Jika tidak ada data -->
      <tr v-if="records.length === 0">
        <td colspan="6" class="px-6 py-10 text-center text-gray-500">
          <div class="flex flex-col items-center">
            <span class="text-4xl mb-2">📭</span>
            <p class="text-gray-600 font-medium">Belum ada data Tindakan Q.</p>
            <button
              @click="goToCreateTindakanQ"
              class="mt-4 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition"
            >
              <i class="fas fa-plus mr-2"></i>
              Tambah Tindakan Q Pertama
            </button>
          </div>
        </td>
      </tr>

      <!-- Jika ada data -->
      <tr
        v-for="(record, index) in records"
        :key="record.id"
        class="hover:bg-green-50 transition duration-150 ease-in-out"
      >
        <td class="px-6 py-3 text-center font-semibold text-gray-800">
          {{ (props.tindakanQs.current_page - 1) * props.tindakanQs.per_page + index + 1 }}
        </td>
        <td class="px-6 py-3">{{ record.tindakan_q_desc }}</td>
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
              @click="editTindakanQ(record.id)"
              class="px-3 py-1.5 bg-yellow-500 text-white rounded-md hover:bg-yellow-600 transition shadow-sm flex items-center gap-1"
              title="Edit"
            >
              <i class="fas fa-edit"></i>
              <span>Edit</span>
            </button>

            <!-- Hapus -->
            <button
              @click="deleteTindakanQ(record.id)"
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
    v-if="props.tindakanQs.links && props.tindakanQs.links.length > 3"
    class="bg-white/60 backdrop-blur-sm px-4 py-3 flex items-center justify-between border-t border-gray-200 rounded-b-xl sm:px-6"
  >
    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
      <div>
        <p class="text-sm text-gray-700">
          Menampilkan
          <span class="font-medium">{{ (props.tindakanQs.current_page - 1) * props.tindakanQs.per_page + 1 }}</span>
          sampai
          <span class="font-medium">{{ Math.min(props.tindakanQs.current_page * props.tindakanQs.per_page, props.tindakanQs.total) }}</span>
          dari
          <span class="font-medium">{{ props.tindakanQs.total }}</span>
          hasil
        </p>
      </div>
      <div>
        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
          <template v-for="link in props.tindakanQs.links" :key="link.label">
            <Link
              v-if="link.url"
              :href="link.url"
              v-html="link.label"
              class="relative inline-flex items-center px-4 py-2 border text-sm font-medium"
              :class="[
                link.active
                  ? 'z-10 bg-green-50 border-green-500 text-green-700'
                  : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
                link.label.includes('Previous') ? 'rounded-l-md' : '',
                link.label.includes('Next') ? 'rounded-r-md' : ''
              ]"
            />
            <span
              v-else
              v-html="link.label"
              class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-500"
            />
          </template>
        </nav>
      </div>
    </div>
  </div>
</div>

      </div>
    </div>
  </AuthenticatedLayout>
</template>
