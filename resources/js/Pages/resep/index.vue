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
            // reload page or only the reseps prop
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
      <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-bold text-blue-700">Data Resep</h1>
        <div class="flex gap-2">
          <button
            @click="goToCreate"
            class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition"
          >
            + Tambah Resep
          </button>
        </div>
      </div>

      <div class="overflow-x-auto bg-white shadow rounded-lg">
        <table class="w-full text-sm border-collapse">
          <thead class="bg-indigo-600 text-white">
            <tr>
              <th class="px-4 py-3 border">#</th>
              <th class="px-4 py-3 border text-left">Obat Resep</th>
              <th class="px-4 py-3 border text-center">Jumlah</th>
              <th class="px-4 py-3 border text-left">Pemakaian / hari</th>
              <th class="px-4 py-3 border text-center">Aksi</th>
            </tr>
          </thead>

          <tbody>
            <tr
              v-for="(r, idx) in reseps"
              :key="r.id"
              class="odd:bg-white even:bg-indigo-50 hover:bg-indigo-100 transition"
            >
              <td class="px-4 py-3 border text-center">{{ idx + 1 }}</td>
              <td class="px-4 py-3 border">{{ r.obat_resep }}</td>
              <td class="px-4 py-3 border text-center">{{ r.jumlah }}</td>
              <td class="px-4 py-3 border">{{ r.pemakaian_perhari }}</td>
              <td class="px-4 py-3 border text-center">
                <div class="inline-flex gap-2">
                  <button
                    @click="goToEdit(r.id)"
                    class="px-2 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600 transition"
                  >
                    Edit
                  </button>
                  <button
                    @click="deleteResep(r.id)"
                    class="px-2 py-1 bg-red-500 text-white rounded hover:bg-red-600 transition"
                  >
                    Delete
                  </button>
                </div>
              </td>
            </tr>

            <tr v-if="!reseps.length">
              <td colspan="5" class="px-4 py-6 text-center text-gray-500">Belum ada data resep.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
