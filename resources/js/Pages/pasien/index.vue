<script setup>
import { ref, computed } from "vue"
import { Head, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
  psns: {
    type: Array,
    default: () => []
  },
  isAdmin: {
    type: Boolean,
    default: false
  }
})

const search = ref('')

function searchPasien() {
  // logika search, misal reload halaman dengan query
  router.get('/pasien', { search: search.value })
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
            <input
              v-model="search"
              type="text"
              placeholder="Cari pasien..."
              class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            />
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
            <tr v-for="(psn, index) in props.psns" :key="psn.id" class="hover:bg-blue-50 transition duration-150 ease-in-out">
              <td class="px-6 py-3 text-center font-semibold text-gray-800">{{ index + 1 }}</td>
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

            <tr v-if="props.psns.length === 0">
              <td colspan="10" class="px-6 py-10 text-center text-gray-500">
                <div class="flex flex-col items-center">
                  <span class="text-4xl mb-2">📭</span>
                  <p class="text-gray-600 font-medium">Belum ada data pasien.</p>
                  <button
                    @click="router.visit('/pasien/create')"
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

    </div>
  </AuthenticatedLayout>
</template>
