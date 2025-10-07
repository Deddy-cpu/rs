<script setup lang="ts">
import { ref } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { useAuth } from '@/composables/useAuth'

interface Dokter {
  id: number
  user_id: number
  nama_dokter: string
  aktif: string
  user: {
    id: number
    name: string
    email: string
    role: string
  }
}

const props = defineProps<{
  dokters: {
    data: Dokter[]
    current_page: number
    per_page: number
    last_page: number
    total: number
    links: any[]
  }
  filters: {
    search?: string
  }
}>()

const { isAdmin } = useAuth()
const search = ref(props.filters.search || '')

function searchDokter() {
  router.get(route('dokter.index'), { search: search.value }, { preserveState: true, replace: true })
}

function editDokter(id: number) {
  router.visit(`/dokter/${id}/edit`)
}

function viewDokter(id: number) {
  router.visit(`/dokter/${id}`)
}

function deleteDokter(id: number) {
  if (confirm('Apakah Anda yakin ingin menghapus dokter ini?')) {
    router.delete(`/dokter/${id}`, {
      onSuccess: () => {
        router.reload({ only: ['dokters'] })
      }
    })
  }
}
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Data Dokter" />

    <div class="min-h-screen bg-gradient-to-br from-blue-100 via-white to-indigo-100 p-4 md:p-8">
      <div class="max-w-6xl mx-auto">
        <!-- Header Section -->
        <div class="mb-10">
          <div class="text-center mb-8">
            <h1 class="text-5xl font-extrabold bg-gradient-to-r from-blue-700 to-indigo-700 bg-clip-text text-transparent mb-3 drop-shadow-lg">
              <span class="inline-block align-middle mr-2">👨‍⚕️</span> Data Dokter
            </h1>
            <p class="text-gray-500 text-lg font-medium">Kelola informasi dokter klinik dengan mudah dan elegan</p>
          </div>

          <!-- Action Bar -->
          <div class="flex flex-col md:flex-row justify-between items-center gap-4 bg-white rounded-3xl p-6 shadow-2xl border border-blue-100">
            <!-- Add Button -->
            <button
              v-if="isAdmin"
              class="w-full md:w-auto px-7 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-2xl hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 font-bold shadow-lg hover:shadow-2xl flex items-center justify-center gap-2 text-lg"
              @click="router.visit('/dokter/create')"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
              </svg>
              Tambah Dokter
            </button>
            <div v-else class="w-full md:w-auto"></div>

            <!-- Search Bar -->
            <div class="flex items-center space-x-3 w-full md:w-auto">
              <div class="relative flex-1 md:flex-none">
                <input
                  v-model="search"
                  type="text"
                  placeholder="Cari dokter..."
                  class="w-full md:w-96 pl-5 pr-14 py-3 border border-blue-200 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 bg-blue-50 focus:bg-white text-lg shadow"
                />
                <button
                  @click="searchDokter"
                  class="absolute right-3 top-1/2 transform -translate-y-1/2 p-2 text-blue-400 hover:text-blue-700 transition-colors"
                >
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                  </svg>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-10">
          <div class="bg-gradient-to-br from-blue-500/10 to-blue-100 rounded-3xl p-7 shadow-xl border border-blue-100 flex items-center gap-4">
            <div class="p-4 bg-blue-600 rounded-2xl flex items-center justify-center shadow-lg">
              <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
              </svg>
            </div>
            <div>
              <p class="text-base font-medium text-blue-700">Total Dokter</p>
              <p class="text-4xl font-extrabold text-blue-700">{{ props.dokters.total }}</p>
            </div>
          </div>
          
          <div class="bg-gradient-to-br from-green-500/10 to-green-100 rounded-3xl p-7 shadow-xl border border-green-100 flex items-center gap-4">
            <div class="p-4 bg-green-600 rounded-2xl flex items-center justify-center shadow-lg">
              <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            <div>
              <p class="text-base font-medium text-green-700">Dokter Aktif</p>
              <p class="text-4xl font-extrabold text-green-700">{{ props.dokters.data.filter(d => d.aktif === 'Ya').length }}</p>
            </div>
          </div>
          
          <div class="bg-gradient-to-br from-indigo-500/10 to-indigo-100 rounded-3xl p-7 shadow-xl border border-indigo-100 flex items-center gap-4">
            <div class="p-4 bg-indigo-600 rounded-2xl flex items-center justify-center shadow-lg">
              <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
              </svg>
            </div>
            <div>
              <p class="text-base font-medium text-indigo-700">Halaman</p>
              <p class="text-4xl font-extrabold text-indigo-700">{{ props.dokters.current_page }}/{{ props.dokters.last_page }}</p>
            </div>
          </div>
        </div>

        <!-- Table Container -->
        <div class="bg-white rounded-3xl shadow-2xl border border-blue-100 overflow-hidden">
          <div class="overflow-x-auto">
            <table class="w-full">
              <thead class="bg-gradient-to-r from-blue-700 to-indigo-700 text-white">
                <tr>
                  <th class="px-7 py-5 text-left text-base font-bold uppercase tracking-wider">No</th>
                  <th class="px-7 py-5 text-left text-base font-bold uppercase tracking-wider">Nama Dokter</th>
                  <th class="px-7 py-5 text-left text-base font-bold uppercase tracking-wider">Email</th>
                  <th class="px-7 py-5 text-left text-base font-bold uppercase tracking-wider">Status</th>
                  <th class="px-7 py-5 text-center text-base font-bold uppercase tracking-wider">Aksi</th>
                </tr>
              </thead>

              <tbody class="divide-y divide-blue-50">
                <tr
                  v-for="(dokter, index) in props.dokters.data"
                  :key="dokter.id"
                  class="hover:bg-blue-50/80 transition-all duration-200 group"
                >
                  <td class="px-7 py-5 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="w-9 h-9 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-full flex items-center justify-center text-white text-base font-bold shadow">
                        {{ (props.dokters.current_page - 1) * props.dokters.per_page + index + 1 }}
                      </div>
                    </div>
                  </td>
                  <td class="px-7 py-5 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="w-12 h-12 bg-gradient-to-r from-blue-100 to-indigo-100 rounded-full flex items-center justify-center mr-4 shadow">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                      </div>
                      <div>
                        <div class="text-lg font-bold text-gray-900">{{ dokter.nama_dokter }}</div>
                        <div class="text-sm text-gray-500">{{ dokter.user.name }}</div>
                      </div>
                    </div>
                  </td>
                  <td class="px-7 py-5 whitespace-nowrap">
                    <div class="text-base text-gray-900">{{ dokter.user.email }}</div>
                  </td>
                  <td class="px-7 py-5 whitespace-nowrap">
                    <span
                      :class="dokter.aktif === 'Ya' 
                        ? 'bg-green-100 text-green-800 border-green-200' 
                        : 'bg-red-100 text-red-800 border-red-200'"
                      class="inline-flex items-center px-4 py-1.5 rounded-full text-sm font-bold border shadow"
                    >
                      <div 
                        :class="dokter.aktif === 'Ya' ? 'bg-green-400' : 'bg-red-400'"
                        class="w-3 h-3 rounded-full mr-2"
                      ></div>
                      {{ dokter.aktif === 'Ya' ? 'Aktif' : 'Nonaktif' }}
                    </span>
                  </td>
                  <td class="px-7 py-5 whitespace-nowrap text-center">
                    <div class="flex items-center justify-center space-x-2">
                      <button
                        @click="viewDokter(dokter.id)"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-all duration-200 text-base font-semibold shadow hover:shadow-lg"
                      >
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        Detail
                      </button>
                      <button
                        v-if="isAdmin"
                        @click="editDokter(dokter.id)"
                        class="inline-flex items-center px-4 py-2 bg-yellow-500 text-white rounded-xl hover:bg-yellow-600 transition-all duration-200 text-base font-semibold shadow hover:shadow-lg"
                      >
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit
                      </button>
                      <button
                        v-if="isAdmin"
                        @click="deleteDokter(dokter.id)"
                        class="inline-flex items-center px-4 py-2 bg-red-500 text-white rounded-xl hover:bg-red-600 transition-all duration-200 text-base font-semibold shadow hover:shadow-lg"
                      >
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Hapus
                      </button>
                    </div>
                  </td>
                </tr>

                <tr v-if="props.dokters.data.length === 0">
                  <td colspan="5" class="px-7 py-24 text-center bg-blue-50">
                    <div class="flex flex-col items-center">
                      <div class="w-24 h-24 bg-blue-100 rounded-full flex items-center justify-center mb-5 shadow">
                        <svg class="w-12 h-12 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                      </div>
                      <h3 class="text-2xl font-bold text-blue-700 mb-2">Belum ada dokter terdaftar</h3>
                      <p class="text-gray-500 mb-6 text-lg">Mulai dengan menambahkan dokter pertama ke sistem</p>
                      <button
                        v-if="isAdmin"
                        @click="router.visit('/dokter/create')"
                        class="px-7 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-2xl hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 font-bold shadow-lg hover:shadow-2xl text-lg"
                      >
                        Tambah Dokter Pertama
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Pagination -->
        <div class="flex justify-center mt-10">
          <div class="flex items-center space-x-2 bg-white rounded-2xl p-3 shadow-xl border border-blue-100">
            <template v-for="link in props.dokters.links" :key="link.label">
              <button
                v-if="link.url"
                @click="router.visit(link.url, { preserveState: true })"
                class="px-5 py-2 rounded-xl text-base font-semibold transition-all duration-200"
                :class="link.active 
                  ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg' 
                  : 'text-blue-700 hover:bg-blue-50 hover:text-blue-800'"
                v-html="link.label"
              />
              <span
                v-else
                class="px-5 py-2 text-gray-400 text-base"
                v-html="link.label"
              />
            </template>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
