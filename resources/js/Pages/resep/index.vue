```vue
<script setup lang="ts">
import { ref, watchEffect } from 'vue'
import { Head, usePage, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Swal from 'sweetalert2'

interface Resep {
  id: number
  obat_resep: string
  jumlah: number
  pemakaian_perhari: string
}

const page = usePage()

const props = defineProps({
  reseps: Object,
  filters: Object,
})

const flash = page.props.flash || {}

const records = ref<Array<Resep>>([])
watchEffect(() => {
  records.value = props.reseps?.data || []
})

const search = ref(props.filters.search || "")

// Navigasi
function goToCreate() {
  router.visit(route('resep.create'))
}
function goToEdit(id: number) {
  router.visit(route('resep.edit', id))
}

// Delete dengan konfirmasi
function deleteResep(id: number) {
  Swal.fire({
    title: "Apakah kamu yakin?",
    text: "Data resep akan dihapus permanen!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#d33",
    cancelButtonColor: "#3085d6",
    confirmButtonText: "Ya, hapus!"
  }).then((result) => {
    if (result.isConfirmed) {
      router.delete(route('resep.destroy', id), {
        onSuccess: () => {
          Swal.fire("Dihapus!", "Resep berhasil dihapus.", "success").then(() => {
            router.reload({ only: ['reseps'] })
          })
        },
        onError: (err) => {
          console.error("Gagal hapus:", err)
          Swal.fire("Gagal!", "Terjadi kesalahan saat menghapus.", "error")
        }
      })
    }
  })
}

// Search
function performSearch() {
  router.get(route('resep.index'), { search: search.value }, { preserveState: true, replace: true })
}
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Data Resep" />

    <div class="min-h-screen bg-cover bg-center p-6" style="background-image: url('/images/bg-login.png')">

        <!-- 🔹 Flash Message -->
        <div v-if="flash.success" class="mb-4 p-4 rounded-lg bg-green-100 border border-green-300 text-green-800 font-medium shadow">
          ✅ {{ flash.success }}
        </div>
        <div v-if="flash.error" class="mb-4 p-4 rounded-lg bg-red-100 border border-red-300 text-red-800 font-medium shadow">
          ⚠️ {{ flash.error }}
        </div>

        <!-- Header -->
        <div class="mb-6">
          <h1 class="text-3xl font-extrabold text-red-700 tracking-wide flex items-center gap-2 justify-center text-center mb-4">
            📋 Data Resep
          </h1>

          <div class="flex justify-between items-center">
            <button
              @click="goToCreate"
              class="px-5 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-medium shadow"
            >
              + Tambah Resep
            </button>

            <div class="flex items-center space-x-2">
              <input
                v-model="search"
                type="text"
                placeholder="Cari resep..."
                class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
              />
              <button
                @click="performSearch"
                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition"
              >
                🔍 Cari
              </button>
            </div>
          </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto shadow-md rounded-xl border border-gray-200 bg-white/70 backdrop-blur-sm">
          <table class="w-full text-sm text-left text-gray-700">
            <thead class="bg-red-600/90 text-white text-sm uppercase tracking-wide">
              <tr>
                <th class="px-6 py-3 text-center">#</th>
                <th class="px-6 py-3">Obat Resep</th>
                <th class="px-6 py-3 text-center">Jumlah</th>
                <th class="px-6 py-3">Pemakaian / hari</th>
                <th class="px-6 py-3 text-center">Aksi</th>
              </tr>
            </thead>

            <tbody class="divide-y divide-gray-200">
              <tr
                v-for="(record, idx) in records"
                :key="record.id"
                class="hover:bg-red-50 transition duration-150 ease-in-out"
              >
                <td class="px-6 py-3 text-center font-semibold text-gray-800">
                  {{ (props.reseps.current_page - 1) * props.reseps.per_page + idx + 1 }}
                </td>
                <td class="px-6 py-3">{{ record.obat_resep }}</td>
                <td class="px-6 py-3 text-center">{{ record.jumlah }}</td>
                <td class="px-6 py-3">{{ record.pemakaian_perhari }}</td>
                <td class="px-6 py-3 text-center space-x-2">
                  <button
                    @click="goToEdit(record.id)"
                    class="px-3 py-1.5 bg-yellow-500 text-white rounded-md hover:bg-yellow-600 transition shadow-sm"
                  >
                    ✏️ Edit
                  </button>
                  <button
                    @click="deleteResep(record.id)"
                    class="px-3 py-1.5 bg-red-600 text-white rounded-md hover:bg-red-700 transition shadow-sm"
                  >
                    🗑 Hapus
                  </button>
                </td>
              </tr>

              <tr v-if="!records.length">
                <td colspan="5" class="px-6 py-10 text-center text-gray-500">
                  <div class="flex flex-col items-center">
                    <span class="text-4xl mb-2">📭</span>
                    <p class="text-gray-600 font-medium">Belum ada data resep.</p>
                    <button
                      @click="goToCreate"
                      class="mt-4 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition"
                    >
                      Tambah Resep Pertama
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
            <template v-for="link in props.reseps.links" :key="link.label">
              <button
                v-if="link.url"
                @click="router.visit(link.url, { preserveState: true })"
                class="px-3 py-1 rounded-lg text-sm"
                :class="link.active
                  ? 'bg-red-600 text-white'
                  : 'bg-white border text-gray-700 hover:bg-gray-100'"
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
```
