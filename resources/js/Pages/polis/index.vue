<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue"
import { Head, router } from "@inertiajs/vue3"
import { ref, computed } from "vue"

const props = defineProps({
  polis: Object, // Paginated polis data
  filters: {
    type: Object,
    default: () => ({})
  },
  flash: {
    type: Object,
    default: () => ({})
  }
})

// Modal states
const showDeleteModal = ref(false)
const polisToDelete = ref(null)
const isDeleting = ref(false)

// Search and filter
const searchQuery = ref(props.filters.search || '')
const filterAktif = ref(props.filters.aktif || '')

// Computed properties
const filteredPolis = computed(() => {
  return props.polis?.data || []
})

// Functions
function confirmDelete(poli) {
  polisToDelete.value = poli
  showDeleteModal.value = true
}

function deletePolis() {
  if (!polisToDelete.value) return
  isDeleting.value = true
  // Use direct URL path to avoid Ziggy parameter issues
  const deleteUrl = `/polis/${polisToDelete.value.id}`
  router.delete(deleteUrl, {
    onFinish: () => {
      isDeleting.value = false
      showDeleteModal.value = false
      polisToDelete.value = null
    },
  })
}

function cancelDelete() {
  showDeleteModal.value = false
  polisToDelete.value = null
}

function performSearch() {
  applyFilters()
}

function applyFilters() {
  const params = new URLSearchParams()
  
  if (searchQuery.value) params.append('search', searchQuery.value)
  if (filterAktif.value) params.append('aktif', filterAktif.value)
  
  router.get(route('polis.index'), Object.fromEntries(params), {
    preserveState: true,
    replace: true
  })
}

function resetFilters() {
  searchQuery.value = ''
  filterAktif.value = ''
  router.get(route('polis.index'))
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

function getStatusBadgeClass(status) {
  return status === 'Y' || status === '1' || status === 'aktif'
    ? 'bg-green-100 text-green-800 border-green-200'
    : 'bg-red-100 text-red-800 border-red-200'
}

function getStatusText(status) {
  return status === 'Y' || status === '1' || status === 'aktif' ? 'Aktif' : 'Tidak Aktif'
}
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Daftar Polis Asuransi" />

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
      <div class="mb-8">
        <!-- Header Tengah -->
        <div class="text-center mb-6">
          <h1 class="text-4xl md:text-5xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent mb-2">
            <span class="inline-block align-middle mr-2">🏥</span>
            Manajemen Polis Asuransi
          </h1>
          <p class="text-gray-600 text-lg">
            Kelola data polis asuransi dan informasi penjamin
          </p>
        </div>

        <!-- Action Bar -->
        <div class="flex flex-col md:flex-row justify-between items-center gap-4 bg-transparent px-1 pt-0 pb-0">
          <!-- Tombol Tambah -->
          <button
            @click="router.visit('/polis/create')"
            class="w-full md:w-auto px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-2xl hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 font-semibold shadow-lg hover:shadow-xl flex items-center justify-center gap-2 text-lg"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Tambah Polis
          </button>

          <!-- Search -->
          <div class="flex items-center space-x-3 w-full md:w-auto">
            <div class="relative flex-1 md:flex-none">
              <input
                v-model="searchQuery"
                @keypress.enter="performSearch"
                type="text"
                placeholder="Cari polis..."
                class="w-full md:w-96 pl-5 pr-14 py-3 border border-blue-200 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 bg-blue-50 focus:bg-white text-lg shadow"
              />
              <button
                @click="performSearch"
                class="absolute right-3 top-1/2 transform -translate-y-1/2 p-2 text-blue-500 hover:text-blue-700 transition-colors"
              >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Filter -->
      <div class="bg-white/70 backdrop-blur-sm rounded-lg shadow-sm border border-gray-200 p-6 mb-8 mt-2">
        <div class="flex items-center gap-3 mb-6">
          <h3 class="text-xl font-bold text-gray-900">Filter</h3>
        </div>
        
        <!-- Filter Bar -->
        <div class="flex flex-wrap items-end gap-4 bg-transparent backdrop-blur-sm p-4 rounded-xl shadow-sm border border-gray-100">
          <!-- Filter Status -->
          <div class="flex-1 min-w-[200px]">
            <label for="aktif" class="block text-sm font-medium text-gray-700 mb-1">
              <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              Filter Status
            </label>
            <select
              id="aktif"
              v-model="filterAktif"
              @change="applyFilters"
              class="w-full pl-4 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 bg-gray-50 focus:bg-white"
            >
              <option value="">Semua Status</option>
              <option value="Y">Aktif</option>
              <option value="N">Tidak Aktif</option>
            </select>
          </div>

          <!-- Tombol Reset -->
          <div class="flex items-center">
            <button
              @click="resetFilters"
              class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition-all duration-200 font-medium"
            >
              🔄 Reset
            </button>
          </div>
        </div>
      </div>

      <!-- Polis List -->
      <div v-if="filteredPolis.length > 0" class="space-y-6">
        <div
          v-for="(poli, idx) in filteredPolis"
          :key="poli.id"
          class="bg-white shadow-xl rounded-2xl border border-gray-100 overflow-hidden hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1"
        >
          <!-- Polis Header -->
          <div class="bg-gradient-to-r from-blue-500 to-indigo-500 px-8 py-6 text-white">
            <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center gap-6">
              <div class="flex items-center gap-6">
                <div class="w-16 h-16 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center text-2xl text-white shadow-lg">
                  <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                  </svg>
                </div>
                <div>
                  <h3 class="text-2xl font-bold text-white mb-2">{{ poli.poli_desc }}</h3>
                  <div class="flex items-center gap-4">
                    <span class="px-3 py-1 bg-white/20 text-white rounded-full text-sm font-semibold backdrop-blur-sm">
                      ID: {{ poli.id }}
                    </span>
                    <span 
                      class="px-3 py-1 rounded-full text-sm font-semibold border"
                      :class="getStatusBadgeClass(poli.aktif)"
                    >
                      {{ getStatusText(poli.aktif) }}
                    </span>
                  </div>
                </div>
              </div>
            
              <!-- Action Buttons -->
              <div class="flex flex-col items-end gap-3 w-full max-w-md ml-auto">
                <div class="flex justify-end gap-3 w-full">
                  <!-- Edit Polis -->
                  <button
                    @click="router.visit(route('polis.edit', poli.id))"
                    class="flex-1 px-5 py-3 bg-white/20 hover:bg-white/30 text-white rounded-xl shadow-lg transition-all duration-200 font-semibold flex items-center justify-center gap-2"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5h2m-1 0v14m-7 0a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2.586a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 0012 2H8a2 2 0 00-2 2v14z"/>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15.232 5.232l3.536 3.536"/>
                    </svg>
                    Edit
                  </button>
                </div>

                <!-- Hapus Polis -->
                <button
                  @click="confirmDelete(poli)"
                  class="w-full px-5 py-3 bg-red-500/80 hover:bg-red-500 text-white rounded-xl shadow-lg transition-all duration-200 font-semibold flex items-center justify-center gap-2"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862
                             a2 2 0 01-1.995-1.858L5 7
                             m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1
                             h-4a1 1 0 00-1 1v3M4 7h16"/>
                  </svg>
                  Hapus
                </button>
              </div>
            </div>
          </div>

          <!-- Polis Info -->
          <div class="p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl p-6 shadow-lg border border-blue-100">
                <div class="flex items-center gap-4">
                  <div class="p-3 bg-blue-500 rounded-xl shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                  </div>
                  <div>
                    <p class="text-sm text-blue-600 font-semibold">Deskripsi Polis</p>
                    <p class="text-lg font-bold text-blue-800">{{ poli.poli_desc }}</p>
                  </div>
                </div>
              </div>
              
              <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-2xl p-6 shadow-lg border border-green-100">
                <div class="flex items-center gap-4">
                  <div class="p-3 bg-green-500 rounded-xl shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                  </div>
                  <div>
                    <p class="text-sm text-green-600 font-semibold">Status</p>
                    <p class="text-lg font-bold text-green-800">{{ getStatusText(poli.aktif) }}</p>
                  </div>
                </div>
              </div>
              
              <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-2xl p-6 shadow-lg border border-purple-100">
                <div class="flex items-center gap-4">
                  <div class="p-3 bg-purple-500 rounded-xl shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                  </div>
                  <div>
                    <p class="text-sm text-purple-600 font-semibold">Terakhir Update</p>
                    <p class="text-lg font-bold text-purple-800">{{ formatDate(poli.update_date) }}</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Additional Info -->
            <div class="mt-8 bg-gray-50 rounded-2xl p-6">
              <h4 class="text-lg font-semibold text-gray-800 mb-4">Informasi Tambahan</h4>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <p class="text-sm text-gray-600 mb-1">Dibuat pada:</p>
                  <p class="font-semibold text-gray-800">{{ formatDate(poli.created_at) }}</p>
                </div>
                <div>
                  <p class="text-sm text-gray-600 mb-1">Diupdate oleh:</p>
                  <p class="font-semibold text-gray-800">{{ poli.update_by || '-' }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Pagination -->
        <div v-if="polis?.links" class="flex justify-start mt-4">
          <div class="flex space-x-2">
            <template v-for="link in polis.links" :key="link.label">
              <button
                v-if="link.url"
                @click="router.visit(link.url, { preserveState: true })"
                v-html="link.label"
                class="px-3 py-1 rounded-lg text-sm transition-all duration-200"
                :class="link.active 
                  ? 'bg-blue-600 text-white shadow-md' 
                  : 'bg-white border text-gray-700 hover:bg-gray-100 hover:text-blue-600'"
              />
              <span
                v-else
                v-html="link.label"
                class="px-3 py-1 text-gray-400 text-sm"
              />
            </template>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="text-center py-16">
        <div class="w-32 h-32 bg-gradient-to-br from-blue-100 to-indigo-100 rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg">
          <svg class="w-16 h-16 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
          </svg>
        </div>
        <h3 class="text-2xl font-bold text-gray-700 mb-3">Tidak ada data polis</h3>
        <p class="text-gray-500 mb-8 text-lg">Mulai dengan menambahkan polis pertama</p>
        <button
          @click="router.visit('/polis/create')"
          class="px-8 py-4 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-200 font-semibold inline-flex items-center gap-3 text-lg"
        >
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
          </svg>
          Tambah Polis
        </button>
      </div>
    </div>

    <!-- Delete Modal -->
    <div v-if="showDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4 shadow-2xl">
        <div class="text-center">
          <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
            </svg>
          </div>
          <h3 class="text-xl font-bold text-gray-900 mb-2">Konfirmasi Hapus</h3>
          <p class="text-gray-600 mb-6">
            Apakah Anda yakin ingin menghapus polis <strong>{{ polisToDelete?.poli_desc }}</strong>?
            Tindakan ini tidak dapat dibatalkan.
          </p>
          <div class="flex gap-3">
            <button
              @click="cancelDelete"
              class="flex-1 px-4 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition-all duration-200 font-medium"
            >
              Batal
            </button>
            <button
              @click="deletePolis"
              :disabled="isDeleting"
              class="flex-1 px-4 py-3 bg-red-600 text-white rounded-xl hover:bg-red-700 transition-all duration-200 font-medium disabled:opacity-50"
            >
              <span v-if="isDeleting">Menghapus...</span>
              <span v-else>Hapus</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
