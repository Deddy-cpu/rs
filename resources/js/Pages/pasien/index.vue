<script setup>
import { ref, computed } from "vue"
import { Head, router } from "@inertiajs/vue3"
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue"

const props = defineProps({
  psns: {
    type: Object,
    default: () => ({ data: [] })
  }
})

const search = ref("")

const performSearch = () => {
  router.get("/pasien", { search: search.value }, { preserveState: true })
}
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Data Pasien" />

    <div class="min-h-screen bg-cover bg-center p-6" style="background-image: url('/images/bg-login.png')">
      
      <!-- Header -->
      <div class="mb-4">
        <h1 class="text-3xl font-bold text-gray-900 flex items-center justify-center gap-2 mb-2">
          <i class="fas fa-user-injured text-blue-600"></i>
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
            class="w-full md:w-auto px-5 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-2xl hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 font-bold shadow-lg hover:shadow-2xl flex items-center justify-center gap-2 text-lg"
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
                placeholder="Cari pasien..."
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

      <!-- Table -->
      <div class="overflow-x-auto shadow-md rounded-xl border border-gray-200 bg-white/70 backdrop-blur-sm">
        <table class="w-full text-sm text-left text-gray-700">
          <thead class="bg-blue-600/90 text-white text-sm uppercase tracking-wide">
            <tr>
              <th class="px-6 py-3 text-center">No</th>
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
            <tr
              v-for="(psn, index) in props.psns.data"
              :key="psn.id"
              class="hover:bg-blue-50 transition duration-150 ease-in-out"
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
      class="w-28 px-3 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition shadow-sm"
    >
      🔍 Detail
    </button>
    <button
      @click="editPasien(psn.id)"
      class="w-28 px-3 py-2 bg-yellow-500 text-white rounded-md hover:bg-yellow-600 transition shadow-sm"
    >
      ✏️ Edit
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
                    class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition"
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
              :class="link.active ? 'bg-blue-600 text-white' : 'bg-white border text-gray-700 hover:bg-gray-100'"
              v-html="link.label"
            />
            <span v-else class="px-3 py-1 text-gray-400 text-sm" v-html="link.label" />
          </template>
        </div>
      </div>
    </div>

  </AuthenticatedLayout>
</template>