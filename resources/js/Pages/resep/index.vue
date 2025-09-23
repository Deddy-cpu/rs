<script setup lang="ts">
import { ref } from 'vue'
import { Head, router, usePage } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Swal from 'sweetalert2'

// define type
interface Resep {
  id: number
  obat_resep: string
  jumlah: number
  pemakaian_perhari: string
  created_at?: string
  updated_at?: string
}

// get props from server (Inertia)
const page = usePage()
const reseps = ref<Array<Resep>>(Array.isArray(page.props.reseps) ? page.props.reseps : [])

// navigation
function goToCreate() {
  router.visit(route('resep.create'))
}
function goToEdit(id: number) {
  router.visit(route('resep.edit', id))
}

// delete with confirmation
function deleteResep(id: number) {
  Swal.fire({
    title: 'Yakin menghapus?',
    text: 'Data resep akan dihapus permanen.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Ya, hapus',
    cancelButtonText: 'Batal',
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
  }).then((res) => {
    if (res.isConfirmed) {
      router.delete(route('resep.destroy', id), {
        preserveState: false,
        onSuccess: () => {
          Swal.fire('Terhapus', 'Resep berhasil dihapus.', 'success').then(() => {
            router.reload({ only: ['reseps'] })
          })
        },
        onError: (err) => {
          console.error(err)
          Swal.fire('Gagal', 'Terjadi kesalahan saat menghapus.', 'error')
        }
      })
    }
  })
}
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Data Resep" />

    <div class="p-6">
      <!-- Header -->
      <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-extrabold text-blue-700 tracking-wide">
          📋 Data Resep
        </h1>
        <button
          @click="goToCreate"
          class="px-5 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium shadow"
        >
          + Tambah Resep
        </button>
      </div>

      <!-- Table -->
      <div class="overflow-hidden rounded-xl shadow border border-gray-200 bg-white">
        <table class="w-full text-sm">
          <thead class="bg-blue-600 text-white">
            <tr>
              <th class="px-4 py-3 text-center">#</th>
              <th class="px-4 py-3 text-left">Obat Resep</th>
              <th class="px-4 py-3 text-center">Jumlah</th>
              <th class="px-4 py-3 text-left">Pemakaian / hari</th>
              <th class="px-4 py-3 text-center">Aksi</th>
            </tr>
          </thead>

          <tbody>
            <tr
              v-for="(r, idx) in reseps"
              :key="r.id"
              class="odd:bg-white even:bg-blue-50 hover:bg-blue-100 transition"
            >
              <td class="px-4 py-3 text-center font-medium text-gray-700">{{ idx + 1 }}</td>
              <td class="px-4 py-3 text-gray-800">{{ r.obat_resep }}</td>
              <td class="px-4 py-3 text-center">{{ r.jumlah }}</td>
              <td class="px-4 py-3">{{ r.pemakaian_perhari }}</td>
              <td class="px-4 py-3 text-center">
                <div class="inline-flex gap-2">
                  <button
                    @click="goToEdit(r.id)"
                    class="px-3 py-1.5 bg-yellow-500 text-white rounded-md hover:bg-yellow-600 transition shadow-sm flex items-center gap-1"
                  >
                    ✏️ Edit
                  </button>
                  <button
                    @click="deleteResep(r.id)"
                    class="px-3 py-1.5 bg-red-600 text-white rounded-md hover:bg-red-700 transition shadow-sm flex items-center gap-1"
                  >
                    🗑 Hapus
                  </button>
                </div>
              </td>
            </tr>

            <tr v-if="!reseps.length">
              <td colspan="5" class="px-6 py-10 text-center text-gray-500">
                <div class="flex flex-col items-center">
                  <span class="text-4xl mb-2">📭</span>
                  <p class="text-gray-600 font-medium">Belum ada data resep.</p>
                  <button
                    @click="goToCreate"
                    class="mt-4 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition"
                  >
                    Tambah Resep Pertama
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
