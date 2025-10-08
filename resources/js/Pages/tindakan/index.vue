<script setup lang="ts">
import { ref, watchEffect } from 'vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, usePage, router } from '@inertiajs/vue3'
import Swal from 'sweetalert2'

// Definisikan tipe data
interface Tindakan {
  id: number
  id_pasien: number
  dokter: string
  tindakan: string
  jumlah: number
}

const page = usePage()

// props dari controller
const props = defineProps({
  tindakan: Object,
  filters: Object,
})

// flash message dari Laravel
const flash = page.props.flash || {}

// records reactive dengan watchEffect biar auto update setelah search/pagination
const records = ref<Array<Tindakan>>([])
watchEffect(() => {
  records.value = props.tindakan?.data || []
})

const search = ref(props.filters.search || "")

// Aksi navigasi
function goToCreateTindakan() {
  router.visit('/tindakan/create')
}
function editTindakan(id: number) {
  router.visit(`/tindakan/${id}/edit`)
}

// Delete dengan konfirmasi
function deleteTindakan(id: number) {
  Swal.fire({
    title: "Apakah kamu yakin?",
    text: "Tindakan akan dihapus permanen!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#d33",
    cancelButtonColor: "#3085d6",
    confirmButtonText: "Ya, hapus!"
  }).then((result) => {
    if (result.isConfirmed) {
      router.delete(`/tindakan/${id}`, {
        onSuccess: () => {
          Swal.fire("Dihapus!", "Data berhasil dihapus.", "success").then(() => {
            router.reload({ only: ['tindakan'] })
          })
        },
        onError: (err) => {
          console.error("Gagal menghapus:", err)
          Swal.fire("Gagal!", "Terjadi kesalahan saat menghapus.", "error")
        }
      })
    }
  })
}

// Search
function performSearch() {
  router.get("/tindakan", { search: search.value }, { preserveState: true, replace: true })
}
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Data Tindakan Dokter" />

    <div class="min-h-screen bg-cover bg-center p-6" style="background-image: url('/images/bg-login.png')">
    
        <!-- 🔹 Flash Notif -->
        <div v-if="flash.success" class="mb-4 p-4 rounded-lg bg-green-100 border border-green-300 text-green-800 font-medium shadow">
          ✅ {{ flash.success }}
        </div>
        <div v-if="flash.error" class="mb-4 p-4 rounded-lg bg-red-100 border border-red-300 text-red-800 font-medium shadow">
          ⚠️ {{ flash.error }}
        </div>

        <!-- Header -->
        <div class="mb-4">
  <!-- Header Tengah -->
  <h1 class="text-3xl font-bold text-gray-900 flex items-center justify-center gap-2 mb-2">
    <i class="fas fa-stethoscope text-red-600"></i>
    Data Tindakan Dokter
  </h1>
  <p class="text-gray-600 text-center mb-2">
    Kelola data tindakan pasien oleh dokter
  </p>

  <!-- Baris Tombol + Search -->
  <div
    class="flex flex-col md:flex-row justify-between items-center gap-4
    bg-transparent px-1 pt-0 pb-0"
  >
    <!-- Tombol Tambah di kiri -->
    <button
      @click="goToCreateTindakan"
      class="w-full md:w-auto px-5 py-3 bg-gradient-to-r from-red-600 to-pink-600 text-white rounded-2xl hover:from-red-700 hover:to-pink-700 transition-all duration-200 font-bold shadow-lg hover:shadow-2xl flex items-center justify-center gap-2 text-lg"
    >
      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M12 6v6m0 0v6m0-6h6m-6 0H6"
        ></path>
      </svg>
      Tambah Tindakan Pasien
    </button>

    <!-- Search di kanan -->
    <div class="flex items-center space-x-3 w-full md:w-auto">
      <div class="relative flex-1 md:flex-none">
        <input
          v-model="search"
          type="text"
          placeholder="Cari tindakan..."
          class="w-full md:w-96 pl-5 pr-14 py-3 border border-red-200 rounded-2xl focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all duration-200 bg-red-50 focus:bg-white text-lg shadow"
        />
        <button
          @click="performSearch"
          class="absolute right-3 top-1/2 transform -translate-y-1/2 p-2 text-red-400 hover:text-red-700 transition-colors"
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

        <!-- Table -->
        <div class="overflow-x-auto shadow-md rounded-xl border border-gray-200 bg-white/70 backdrop-blur-sm">
          <table class="w-full text-sm text-left text-gray-700">
            <thead class="bg-red-600/90 text-white text-sm uppercase tracking-wide">
              <tr>
                <th class="px-6 py-3 text-center">ID</th>
                <th class="px-6 py-3">ID Pasien</th>
                <th class="px-6 py-3">Dokter</th>
                <th class="px-6 py-3">Tindakan</th>
                <th class="px-6 py-3 text-center">Jumlah</th>
                <th class="px-6 py-3 text-center">Aksi</th>
              </tr>
            </thead>

            <tbody class="divide-y divide-gray-200">
              <tr
                v-for="record in records"
                :key="record.id"
                class="hover:bg-red-50 transition duration-150 ease-in-out"
              >
                <td class="px-6 py-3 text-center font-semibold text-gray-800">
                  {{ record.id }}
                </td>
                <td class="px-6 py-3">{{ record.id_pasien }}</td>
                <td class="px-6 py-3">{{ record.dokter }}</td>
                <td class="px-6 py-3">{{ record.tindakan }}</td>
                <td class="px-6 py-3 text-center">{{ record.jumlah }}</td>
                <td class="px-6 py-3 text-center space-x-2">
                  <button
                    @click="editTindakan(record.id)"
                    class="px-3 py-1.5 bg-yellow-500 text-white rounded-md hover:bg-yellow-600 transition shadow-sm"
                  >
                    ✏️ Edit
                  </button>
                  <button
                    @click="deleteTindakan(record.id)"
                    class="px-3 py-1.5 bg-red-600 text-white rounded-md hover:bg-red-700 transition shadow-sm"
                  >
                    🗑 Hapus
                  </button>
                </td>
              </tr>

              <tr v-if="!records.length">
                <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                  <div class="flex flex-col items-center">
                    <span class="text-4xl mb-2">📭</span>
                    <p class="text-gray-600 font-medium">Belum ada data tindakan.</p>
                    <button
                      @click="goToCreateTindakan"
                      class="mt-4 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition"
                    >
                      Tambah Data Pertama
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
            <template v-for="link in props.tindakan.links" :key="link.label">
              <button
                v-if="link.url"
                @click="router.visit(link.url, { preserveState: true })"
                class="px-3 py-1 rounded-lg text-sm"
                :class="link.active ? 'bg-red-600 text-white' : 'bg-white border text-gray-700 hover:bg-gray-100'"
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
    
  </AuthenticatedLayout>
</template>
