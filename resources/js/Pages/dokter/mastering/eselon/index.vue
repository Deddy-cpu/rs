<script setup>
import { ref, computed } from 'vue'
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
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Data Eselon" />
    
    <div class="min-h-screen bg-gray-50 py-6">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
          <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
              <div class="flex items-center space-x-3">
                <div class="p-2 bg-indigo-100 rounded-lg">
                  <i class="fas fa-layer-group text-indigo-600 text-xl"></i>
                </div>
                <div>
                  <h1 class="text-2xl font-bold text-gray-900">Data Eselon</h1>
                  <p class="text-sm text-gray-600">Kelola data eselon</p>
                </div>
              </div>
              <button
                @click="goToCreateEselon"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
              >
                <i class="fas fa-plus mr-2"></i>
                + Tambah Eselon
              </button>
            </div>
          </div>

          <!-- Search and Filter -->
          <div class="px-6 py-4 bg-gray-50">
            <div class="flex flex-col sm:flex-row gap-4">
              <div class="flex-1">
                <div class="relative">
                  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                  </div>
                  <input
                    v-model="search"
                    @input="performSearch"
                    type="text"
                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    placeholder="Cari Eselon..."
                  />
                </div>
              </div>
              <div class="flex items-center space-x-2">
                <button
                  @click="toggleOrder"
                  class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                  <i class="fas fa-sort mr-2"></i>
                  {{ order === 'asc' ? 'A-Z' : 'Z-A' }}
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Table -->
        <div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    No
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Grup Eselon
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Deskripsi Eselon
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Status
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Tanggal Update
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Diupdate Oleh
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Aksi
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-if="records.length === 0">
                  <td colspan="7" class="px-6 py-12 text-center">
                    <div class="flex flex-col items-center">
                      <i class="fas fa-layer-group text-gray-300 text-4xl mb-4"></i>
                      <p class="text-gray-600 font-medium">Belum ada data Eselon.</p>
                      <button
                        @click="goToCreateEselon"
                        class="mt-4 inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                      >
                        <i class="fas fa-plus mr-2"></i>
                        Tambah Eselon Pertama
                      </button>
                    </div>
                  </td>
                </tr>
                <tr v-for="(record, idx) in records" :key="record.id" class="hover:bg-gray-50">
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ (props.eselons.current_page - 1) * props.eselons.per_page + idx + 1 }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">{{ record.grp_eselon?.eselon_desc || '-' }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">{{ record.eselon_desc }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span 
                      class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                      :class="getStatusBadge(record.aktif)"
                    >
                      {{ getStatusText(record.aktif) }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ formatDate(record.update_date) }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ record.update_by || '-' }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <div class="flex items-center space-x-2">
                      <button
                        @click="showEselon(record.id)"
                        class="text-indigo-600 hover:text-indigo-900 p-1 rounded hover:bg-indigo-50"
                        title="Lihat Detail"
                      >
                        <i class="fas fa-eye"></i>
                      </button>
                      <button
                        @click="editEselon(record.id)"
                        class="text-blue-600 hover:text-blue-900 p-1 rounded hover:bg-blue-50"
                        title="Edit"
                      >
                        <i class="fas fa-edit"></i>
                      </button>
                      <button
                        @click="deleteEselon(record.id)"
                        class="text-red-600 hover:text-red-900 p-1 rounded hover:bg-red-50"
                        title="Hapus"
                      >
                        <i class="fas fa-trash"></i>
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <div v-if="props.eselons.links && props.eselons.links.length > 3" class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
            <div class="flex-1 flex justify-between sm:hidden">
              <Link
                v-if="props.eselons.links[0].url"
                :href="props.eselons.links[0].url"
                class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
              >
                Previous
              </Link>
              <Link
                v-if="props.eselons.links[props.eselons.links.length - 1].url"
                :href="props.eselons.links[props.eselons.links.length - 1].url"
                class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
              >
                Next
              </Link>
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
              <div>
                <p class="text-sm text-gray-700">
                  Menampilkan
                  <span class="font-medium">{{ (props.eselons.current_page - 1) * props.eselons.per_page + 1 }}</span>
                  sampai
                  <span class="font-medium">{{ Math.min(props.eselons.current_page * props.eselons.per_page, props.eselons.total) }}</span>
                  dari
                  <span class="font-medium">{{ props.eselons.total }}</span>
                  hasil
                </p>
              </div>
              <div>
                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                  <template v-for="link in props.eselons.links" :key="link.label">
                    <Link
                      v-if="link.url"
                      :href="link.url"
                      v-html="link.label"
                      class="relative inline-flex items-center px-4 py-2 border text-sm font-medium"
                      :class="[
                        link.active
                          ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600'
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
