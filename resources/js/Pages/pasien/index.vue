<script setup>
import { ref, computed, watch } from "vue"
import { Head, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
  psns: {
    type: Object,
    default: () => ({ data: [], links: [], meta: {} })
  },
  filters: {
    type: Object,
    default: () => ({})
  },
  isAdmin: {
    type: Boolean,
    default: false
  }
})

const search = ref(props.filters.search || '')

// Computed properties for safer data access
const paginationMeta = computed(() => props.psns.meta || {})
const patientsData = computed(() => props.psns.data || [])
const paginationLinks = computed(() => props.psns.links || [])

// Watch for changes in search input and debounce the search
let searchTimeout = null
watch(search, (newValue) => {
  if (searchTimeout) {
    clearTimeout(searchTimeout)
  }
  
  searchTimeout = setTimeout(() => {
    router.get('/pasien', { search: newValue }, {
      preserveState: true,
      replace: true
    })
  }, 300) // 300ms debounce
})

function searchPasien() {
  router.get('/pasien', { search: search.value }, {
    preserveState: true,
    replace: true
  })
}

function clearSearch() {
  search.value = ''
  router.get('/pasien', {}, {
    preserveState: true,
    replace: true
  })
}
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Data Pasien" />

    <div class="min-h-screen bg-cover bg-center p-6" style="background-image: url('/images/bg-login.png')">

      <!-- Header -->
      <div class="mb-6 text-center">
        <h1 class="text-3xl font-extrabold text-blue-700 tracking-wide flex items-center gap-2 justify-center">
          👥 Data Pasien
        </h1>

        <div class="flex justify-between items-center mt-4">
          <!-- Tombol Tambah Pasien -->
          <button 
            class="px-5 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium shadow"
            @click="router.visit('/pasien/create')"
          >
            + Tambah Pasien
          </button>
          <!-- Search Pasien -->
          <div class="flex items-center space-x-2">
            <div class="relative">
              <input
                v-model="search"
                type="text"
                placeholder="Cari pasien (nama, NIK, BPJS, agama, alamat)..."
                class="px-3 py-2 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-80"
              />
              <button
                v-if="search"
                @click="clearSearch"
                class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600"
              >
                ✕
              </button>
            </div>
            <button
              @click="searchPasien"
              class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition"
            >
              🔍 Cari
            </button>
          </div>
        </div>
      </div>

      <!-- Tabel Pasien -->
      <div class="overflow-x-auto shadow-md rounded-xl border border-gray-200 bg-white/70 backdrop-blur-sm">
        <table class="w-full text-sm text-left text-gray-700">
          <thead class="bg-blue-600/90 text-white text-sm uppercase tracking-wide">
            <tr>
              <th class="px-6 py-3 text-center">#</th>
              <th class="px-6 py-3">Nama Pasien</th>
              <th class="px-6 py-3">NIK</th>
              <th class="px-6 py-3">No BPJS</th>
              <th class="px-6 py-3">Agama</th>
              <th class="px-6 py-3">Tanggal Lahir</th>
              <th class="px-6 py-3">Kelamin</th>
              <th class="px-6 py-3">Alamat Lahir</th>
              <th class="px-6 py-3">Alamat Domisili</th>
              <th class="px-6 py-3 text-center">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-for="(psn, index) in patientsData" :key="psn.id" class="hover:bg-blue-50 transition duration-150 ease-in-out">
              <td class="px-6 py-3 text-center font-semibold text-gray-800">{{ ((paginationMeta.current_page || 1) - 1) * (paginationMeta.per_page || 10) + index + 1 }}</td>
              <td class="px-6 py-3 font-semibold text-gray-900">{{ psn.nm_p }}</td>
              <td class="px-6 py-3">{{ psn.nik }}</td>
              <td class="px-6 py-3">{{ psn.no_bpjs }}</td>
              <td class="px-6 py-3">{{ psn.agm }}</td>
              <td class="px-6 py-3">{{ psn.tgl_lahir }}</td>
              <td class="px-6 py-3">
                <span 
                  class="px-2 py-1 rounded-full text-xs font-semibold"
                  :class="psn.kelamin === 'L' ? 'bg-blue-100 text-blue-700' : 'bg-pink-100 text-pink-700'">
                  {{ psn.kelamin }}
                </span>
              </td>
              <td class="px-6 py-3">{{ psn.almt_L }}</td>
              <td class="px-6 py-3">{{ psn.almt_B }}</td>
              <td class="px-6 py-3 text-center flex flex-col items-center space-y-2">
                <button 
                  @click="router.visit(`/pasien/${psn.id}`)"
                  class="px-3 py-1.5 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition shadow-sm w-full"
                >
                  👁 Detail
                </button>
                <button 
                  @click="router.visit(`/pasien/${psn.id}/edit`)"
                  class="px-3 py-1.5 bg-green-500 text-white rounded-md hover:bg-green-600 transition shadow-sm w-full"
                >
                  ✏️ Edit
                </button>
              </td>
            </tr>

            <tr v-if="patientsData.length === 0">
              <td colspan="10" class="px-6 py-10 text-center text-gray-500">
                <div class="flex flex-col items-center">
                  <span class="text-4xl mb-2">📭</span>
                  <p class="text-gray-600 font-medium">
                    {{ search ? 'Tidak ada pasien yang sesuai dengan pencarian.' : 'Belum ada data pasien.' }}
                  </p>
                  <button
                    v-if="!search"
                    @click="router.visit('/pasien/create')"
                    class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition"
                  >
                    Tambah Pasien Pertama
                  </button>
                  <button
                    v-else
                    @click="clearSearch"
                    class="mt-4 px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition"
                  >
                    Hapus Filter
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="paginationLinks.length > 3 && paginationMeta.total > 0" class="mt-6 flex items-center justify-between">
        <div class="flex items-center text-sm text-gray-700">
          <span>
            Menampilkan {{ paginationMeta.from || 0 }} sampai {{ paginationMeta.to || 0 }} 
            dari {{ paginationMeta.total || 0 }} data
          </span>
        </div>
        
        <div class="flex items-center space-x-1">
          <template v-for="(link, index) in paginationLinks" :key="index">
            <button
              v-if="link.url"
              @click="router.visit(link.url, { preserveState: true })"
              v-html="link.label"
              :class="[
                'px-3 py-2 text-sm border rounded-md transition',
                link.active 
                  ? 'bg-blue-600 text-white border-blue-600' 
                  : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'
              ]"
            ></button>
            <span
              v-else
              v-html="link.label"
              class="px-3 py-2 text-sm text-gray-400 border border-gray-300 rounded-md bg-gray-100"
            ></span>
          </template>
        </div>
      </div>

    </div>
  </AuthenticatedLayout>
</template>
