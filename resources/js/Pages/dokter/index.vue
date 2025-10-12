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

    <div class="min-h-screen bg-cover bg-center p-6" style="background-image: url('/images/bg-login.png')">        
        <!-- Header -->
        <div class="mb-6 text-center">
          <h1 class="text-3xl font-extrabold text-blue-700 tracking-wide flex items-center gap-2 justify-center">
            👨‍⚕️ Data Dokter
          </h1>

          <!-- Action Bar -->
<div
  class="flex flex-col md:flex-row justify-between items-center gap-5
  bg-transparent px-15 pt-4 pb-0"
>
x
  <!-- Add Button -->
  <button
    v-if="isAdmin"
    class="w-full md:w-auto px-7 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-2xl hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 font-bold shadow-lg hover:shadow-2xl flex items-center justify-center gap-2 text-lg"
    @click="router.visit('/dokter/create')"
  >
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path
        stroke-linecap="round"
        stroke-linejoin="round"
        stroke-width="2"
        d="M12 6v6m0 0v6m0-6h6m-6 0H6"
      ></path>
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
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
          ></path>
        </svg>
      </button>
    </div>
  </div>
</div>

        </div>

        <!-- Table Container -->
       <div class="overflow-x-auto shadow-md rounded-xl border border-blue-200 bg-white/70 backdrop-blur-sm">
  <table class="w-full text-sm text-left text-gray-700">
    <thead class="bg-blue-700/90 text-white text-sm uppercase tracking-wide">
      <tr>
        <th class="px-6 py-3 text-center">#</th>
        <th class="px-6 py-3">Nama Dokter</th>
        <th class="px-6 py-3">Email</th>
        <th class="px-6 py-3 text-center">Status</th>
        <th class="px-6 py-3 text-center">Aksi</th>
      </tr>
    </thead>

    <tbody class="divide-y divide-gray-200">
      <tr
        v-for="(dokter, idx) in props.dokters.data"
        :key="dokter.id"
        class="hover:bg-blue-50 transition duration-150 ease-in-out"
      >
        <td class="px-6 py-3 text-center font-semibold text-gray-800">
          {{ (props.dokters.current_page - 1) * props.dokters.per_page + idx + 1 }}
        </td>

        <td class="px-6 py-3">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
              <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
              </svg>
            </div>
            <div>
              <div class="text-base font-bold text-gray-900">{{ dokter.nama_dokter }}</div>
              <div class="text-xs text-gray-500">{{ dokter.user.name }}</div>
            </div>
          </div>
        </td>

        <td class="px-6 py-3">{{ dokter.user.email }}</td>

        <td class="px-6 py-3 text-center">
          <span
            :class="dokter.aktif === 'Ya'
              ? 'bg-green-100 text-green-700 border-green-300'
              : 'bg-red-100 text-red-700 border-red-300'"
            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold border shadow-sm">
            <span
              :class="dokter.aktif === 'Ya' ? 'bg-green-500' : 'bg-red-500'"
              class="w-2.5 h-2.5 rounded-full mr-2"
            ></span>
            {{ dokter.aktif === 'Ya' ? 'Aktif' : 'Nonaktif' }}
          </span>
        </td>

        <td class="px-6 py-3 text-center space-x-2">
          <button
            @click="viewDokter(dokter.id)"
            class="px-3 py-1.5 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition shadow-sm"
          >
            👁 Detail
          </button>

          <button
            v-if="isAdmin"
            @click="editDokter(dokter.id)"
            class="px-3 py-1.5 bg-yellow-500 text-white rounded-md hover:bg-yellow-600 transition shadow-sm"
          >
            ✏️ Edit
          </button>

          <button
            v-if="isAdmin"
            @click="deleteDokter(dokter.id)"
            class="px-3 py-1.5 bg-red-600 text-white rounded-md hover:bg-red-700 transition shadow-sm"
          >
            🗑 Hapus
          </button>
        </td>
      </tr>

      <tr v-if="!props.dokters.data.length">
        <td colspan="5" class="px-6 py-10 text-center text-gray-500">
          <div class="flex flex-col items-center">
            <span class="text-4xl mb-2">📭</span>
            <p class="text-gray-600 font-medium">Belum ada dokter terdaftar.</p>
            <button
              v-if="isAdmin"
              @click="router.visit('/dokter/create')"
              class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition"
            >
              Tambah Dokter Pertama
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
    <template v-for="link in props.dokters.links" :key="link.label">
      <button
        v-if="link.url"
        @click="router.visit(link.url, { preserveState: true })"
        class="px-3 py-1 rounded-lg text-sm font-medium transition-all duration-200"
        :class="link.active
          ? 'bg-red-600 text-white shadow-md'
          : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-100 hover:text-gray-900'"
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
  </AuthenticatedLayout>
</template>
