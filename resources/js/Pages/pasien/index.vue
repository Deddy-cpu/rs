<script setup>
import { ref, watch, computed } from "vue"
import { Head, router, usePage } from "@inertiajs/vue3"
import Swal from 'sweetalert2'
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue"

const page = usePage()

const props = defineProps({
  psns: {
    type: Object,
    default: () => ({ data: [] })
  },
  filters: {
    type: Object,
    default: () => ({ search: "" })
  },
  flash: {
    type: Object,
    default: () => ({})
  }
})

// Get flash from props (with fallback to page props)
const flash = computed(() => props.flash || page.props.flash || {})

const search = ref(props.filters.search || "")

const performSearch = () => {
  router.get("/pasien", { search: search.value }, { preserveState: true, replace: true })
}

// Debounce helper
function debounce(fn, delay = 400) {
  let timeout
  return (...args) => {
    clearTimeout(timeout)
    timeout = setTimeout(() => fn(...args), delay)
  }
}

// Debounced search
const debouncedSearch = debounce(() => {
  performSearch()
}, 400)

// Watch search and trigger debounced search
watch(search, () => {
  debouncedSearch()
})

// Navigasi functions
const goToCreatePasien = () => {
  router.visit("/pasien/create")
}

const viewPasien = (id) => {
  router.visit(`/pasien/${id}`)
}

const editPasien = (id) => {
  router.visit(`/pasien/${id}/edit`)
}

// Clear flash message without reload
const clearFlashMessage = () => {
  if (flashTimeout) {
    clearTimeout(flashTimeout)
    flashTimeout = null
  }
  
  router.get(window.location.pathname, {}, {
    preserveState: true,
    preserveScroll: true,
    only: []
  })
}

// Auto-hide flash message after 1 minute
let flashTimeout = null
watch(() => flash.value?.success, (newVal) => {
  if (flashTimeout) {
    clearTimeout(flashTimeout)
    flashTimeout = null
  }
  
  if (newVal) {
    flashTimeout = setTimeout(() => {
      clearFlashMessage()
    }, 60000) // 1 menit
  }
})

watch(() => flash.value?.error, (newVal) => {
  if (flashTimeout) {
    clearTimeout(flashTimeout)
    flashTimeout = null
  }
  
  if (newVal) {
    flashTimeout = setTimeout(() => {
      clearFlashMessage()
    }, 60000) // 1 menit
  }
})

const deletePasien = (id) => {
  Swal.fire({
    title: 'Apakah Anda yakin?',
    text: 'Data pasien akan dihapus permanen!',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: 'Ya, Hapus!',
    cancelButtonText: 'Batal'
  }).then((result) => {
    if (result.isConfirmed) {
      router.delete(`/pasien/${id}`, {
        onSuccess: () => {
          // Reload untuk mendapatkan flash message dari server
          router.reload({ only: ['psns', 'flash'] })
        }
      })
    }
  })
}
</script>

<style scoped>
/* Flash message transitions */
.slide-fade-enter-active {
  transition: all 0.3s ease-out;
}

.slide-fade-leave-active {
  transition: all 0.3s ease-in;
}

.slide-fade-enter-from {
  transform: translateY(-20px);
  opacity: 0;
}

.slide-fade-leave-to {
  transform: translateY(-20px);
  opacity: 0;
}
</style>

<template>
  <AuthenticatedLayout>
    <Head title="Data Pasien" />

    <div class="min-h-screen bg-cover bg-center p-6" style="background-image: url('/images/bg-login.png')">
      
      <!-- Flash Messages sederhana -->
      <transition name="slide-fade">
        <div v-if="flash?.success" class="mb-4 bg-green-50 border border-green-300 text-green-700 px-4 py-3 rounded-lg">
          <div class="flex items-center justify-between">
            <div class="flex items-center">
              <i class="fas fa-check text-green-600 mr-2"></i>
              <span>{{ flash.success }}</span>
            </div>
            <button
              @click="clearFlashMessage"
              class="text-green-600 hover:text-green-800 ml-4"
              aria-label="Tutup"
            >
              <i class="fas fa-times text-sm"></i>
            </button>
          </div>
        </div>
      </transition>
      
      <transition name="slide-fade">
        <div v-if="flash?.error" class="mb-4 bg-red-50 border border-red-300 text-red-700 px-4 py-3 rounded-lg">
          <div class="flex items-center justify-between">
            <div class="flex items-center">
              <i class="fas fa-exclamation text-red-600 mr-2"></i>
              <span>{{ flash.error }}</span>
            </div>
            <button
              @click="clearFlashMessage"
              class="text-red-600 hover:text-red-800 ml-4"
              aria-label="Tutup"
            >
              <i class="fas fa-times text-sm"></i>
            </button>
          </div>
        </div>
      </transition>
      
      <!-- Header -->
      <div class="mb-4">
        <h1 class="text-3xl font-bold text-gray-900 flex items-center justify-center gap-2 mb-2">
          <i class="fas fa-user-injured text-red-600"></i>
          Data Pasien
        </h1>
        <p class="text-gray-600 text-center mb-2">
          Kelola data pasien dan informasi medis
        </p>

        <!-- Tombol & Search -->
        <div class="flex flex-col md:flex-row justify-between items-center gap-4 bg-transparent px-1 pt-0 pb-0">
          <!-- Tombol Tambah -->
          <button
            @click="goToCreatePasien"
            class="w-full md:w-auto px-5 py-3 bg-gradient-to-r from-red-600 to-pink-600 text-white rounded-2xl hover:from-red-700 hover:to-pink-700 transition-all duration-200 font-bold shadow-lg hover:shadow-2xl flex items-center justify-center gap-2 text-lg"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Tambah Pasien
          </button>

          <!-- Search -->
          <div class="flex items-center space-x-3 w-full md:w-auto">
            <div class="relative flex-1 md:flex-none">
              <input
                v-model="search"
                type="text"
                placeholder="Cari pasien berdasarkan nama, NIK, atau BPJS..."
                class="w-full md:w-96 pl-5 pr-5 py-3 border border-red-200 rounded-2xl focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all duration-200 bg-red-50 focus:bg-white text-lg shadow"
              />
            </div>
          </div>
        </div>
      </div>

      <!-- Table -->
      <div class="overflow-x-auto shadow-md rounded-xl border border-gray-200 bg-white/70 backdrop-blur-sm">
        <table class="w-full text-sm text-left text-gray-700">
          <thead class="bg-red-600/90 text-white text-sm uppercase tracking-wide">
            <tr>
              <th class="px-6 py-3 text-center">No</th>
              <th class="px-6 py-3">Nama Pasien</th>
              <th class="px-6 py-3">NIK</th>
              <th class="px-6 py-3">No BPJS</th>
              <th class="px-6 py-3">Agama</th>
              <th class="px-6 py-3">Tanggal Lahir</th>
              <th class="px-6 py-3">Kelamin</th>
              <th class="px-6 py-3">Alamat Lama</th>
              <th class="px-6 py-3">Alamat Baru</th>
              <th class="px-6 py-3 text-center">Aksi</th>
            </tr>
          </thead>

          <tbody class="divide-y divide-gray-200">
            <tr
              v-for="(psn, index) in props.psns.data"
              :key="psn.id"
              class="hover:bg-red-50 transition duration-150 ease-in-out"
            >
              <td class="px-6 py-3 text-center font-semibold text-gray-800">
                {{ (props.psns.current_page - 1) * props.psns.per_page + index + 1 }}
              </td>
              <td class="px-6 py-3">{{ psn.nm_p }}</td>
              <td class="px-6 py-3">{{ psn.nik }}</td>
              <td class="px-6 py-3">{{ psn.no_bpjs }}</td>
              <td class="px-6 py-3">{{ psn.agm }}</td>
              <td class="px-6 py-3">{{ psn.tgl_lahir }}</td>
              <td class="px-6 py-3">{{ psn.kelamin }}</td>
              <td class="px-6 py-3">{{ psn.almt_L }}</td>
              <td class="px-6 py-3">{{ psn.almt_B }}</td>
             <td class="px-6 py-3 text-center">
             <div class="flex flex-col items-center justify-center gap-2">
    <button
      @click="viewPasien(psn.id)"
      class="w-28 px-3 py-2 bg-blue-600 text-white rounded-md hover:bg-red-700 transition shadow-sm"
    >
      🔍 Detail
    </button>
    <button
      @click="editPasien(psn.id)"
      class="w-28 px-3 py-2 bg-yellow-500 text-white rounded-md hover:bg-yellow-600 transition shadow-sm"
    >
      ✏️ Edit
    </button>
    <button
      @click="deletePasien(psn.id)"
      class="w-28 px-3 py-2 bg-red-700 text-white rounded-md hover:bg-red-800 transition shadow-sm"
    >
      🗑 Hapus
    </button>
  </div>
</td>

            </tr>

            <tr v-if="props.psns.data.length === 0">
              <td colspan="10" class="px-6 py-10 text-center text-gray-500">
                <div class="flex flex-col items-center">
                  <span class="text-4xl mb-2">📭</span>
                  <p class="text-gray-600 font-medium">Belum ada data pasien.</p>
                  <button
                    @click="goToCreatePasien"
                    class="mt-4 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition"
                  >
                    Tambah Pasien Pertama
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="flex justify-start mt-4">
        <div class="flex space-x-2">
          <template v-for="link in props.psns.links" :key="link.label">
            <button
              v-if="link.url"
              @click="router.visit(link.url, { preserveState: true })"
              class="px-3 py-1 rounded-lg text-sm"
              :class="link.active ? 'bg-red-600 text-white' : 'bg-white border text-gray-700 hover:bg-gray-100'"
              v-html="link.label"
            />
            <span v-else class="px-3 py-1 text-gray-400 text-sm" v-html="link.label" />
          </template>
        </div>
      </div>
    </div>

  </AuthenticatedLayout>
</template>