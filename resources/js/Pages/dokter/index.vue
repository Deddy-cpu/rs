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
      <div class="bg-white/70 backdrop-blur-sm rounded-xl p-6 shadow-lg">
        
        <!-- Header -->
        <div class="mb-6 text-center">
          <h1 class="text-3xl font-extrabold text-blue-700 tracking-wide flex items-center gap-2 justify-center">
            👨‍⚕️ Data Dokter
          </h1>

          <div class="flex justify-between items-center">
            <!-- Tombol Tambah (Admin Only) -->
            <button
              v-if="isAdmin"
              class="px-5 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium shadow"
              @click="router.visit('/dokter/create')"
            >
              + Tambah Dokter
            </button>
            <div v-else></div>

            <!-- Search -->
            <div class="flex items-center space-x-2">
              <input
                v-model="search"
                type="text"
                placeholder="Cari dokter..."
                class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              />
              <button
                @click="searchDokter"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition"
              >
                🔍 Cari
              </button>
            </div>
          </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto shadow-md rounded-xl border border-gray-200 bg-white/70 backdrop-blur-sm">
          <table class="w-full text-sm text-left text-gray-700">
            <thead class="bg-blue-600/90 text-white text-sm uppercase tracking-wide">
              <tr>
                <th class="px-6 py-3 text-center">No</th>
                <th class="px-6 py-3">Nama Dokter</th>
                <th class="px-6 py-3">Email</th>
                <th class="px-6 py-3">Status</th>
                <th class="px-6 py-3 text-center">Aksi</th>
              </tr>
            </thead>

            <tbody class="divide-y divide-gray-200">
              <tr
                v-for="(dokter, index) in props.dokters.data"
                :key="dokter.id"
                class="hover:bg-blue-50 transition duration-150 ease-in-out"
              >
                <td class="px-6 py-3 text-center font-semibold text-gray-800">
                  {{ (props.dokters.current_page - 1) * props.dokters.per_page + index + 1 }}
                </td>
                <td class="px-6 py-3">{{ dokter.nama_dokter }}</td>
                <td class="px-6 py-3">{{ dokter.user.email }}</td>
                <td class="px-6 py-3">
                  <span
                    :class="dokter.aktif === 'Ya' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                    class="px-2 py-1 rounded-full text-xs font-semibold"
                  >
                    {{ dokter.aktif === 'Ya' ? 'Aktif' : 'Nonaktif' }}
                  </span>
                </td>
                <td class="px-6 py-3 text-center space-x-2">
                  <button
                    @click="viewDokter(dokter.id)"
                    class="px-3 py-1.5 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition shadow-sm"
                  >
                    👁️ Detail
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

              <tr v-if="props.dokters.data.length === 0">
                <td colspan="5" class="px-6 py-10 text-center text-gray-500">
                  <div class="flex flex-col items-center">
                    <span class="text-4xl mb-2">👨‍⚕️</span>
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
                class="px-3 py-1 rounded-lg text-sm"
                :class="link.active ? 'bg-blue-600 text-white' : 'bg-white border text-gray-700 hover:bg-gray-100'"
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
  </AuthenticatedLayout>
</template>
