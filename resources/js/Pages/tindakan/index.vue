<script setup lang="ts">
import { ref } from 'vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, usePage, router } from '@inertiajs/vue3'
import Swal from 'sweetalert2'

// Definisikan tipe data
interface Record {
  id: number
  id_pasien: number
  dokter: string
  tindakan: string
  jumlah: number
}

const records = ref<Array<Record>>([])
const page = usePage()
records.value = Array.isArray(page.props.tindakan) ? page.props.tindakan : []

function goToCreateTindakan() {
  router.visit('/tindakan/create')
}

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

function editTindakan(id: number) {
  router.visit(`/tindakan/${id}/edit`)
}
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Data Tindakan Dokter" />

    <div
      class="min-h-screen bg-cover bg-center p-6"
      style="background-image: url('/images/bg-login.png')"
    >
      <div class="backdrop-brightness-95 bg-white/90 rounded-xl p-6 shadow-lg">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
          <h1 class="text-3xl font-extrabold text-green-700 tracking-wide">
            🩺 Data Tindakan Dokter
          </h1>
          <button
            class="px-5 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium shadow"
            @click="goToCreateTindakan"
          >
            + Tambah Tindakan Pasien
          </button>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto bg-white shadow-md rounded-xl border border-gray-200">
          <table class="w-full text-sm text-left text-gray-700">
            <thead class="bg-green-600 text-white text-sm uppercase tracking-wide">
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
                class="hover:bg-green-50 transition duration-150 ease-in-out"
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
                    class="px-3 py-1.5 bg-yellow-500 text-white rounded-md hover:bg-yellow-600 transition shadow-sm flex items-center gap-1 inline-flex"
                  >
                    ✏️ Edit
                  </button>
                  <button
                    @click="deleteTindakan(record.id)"
                    class="px-3 py-1.5 bg-red-600 text-white rounded-md hover:bg-red-700 transition shadow-sm flex items-center gap-1 inline-flex"
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
                      class="mt-4 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition"
                    >
                      Tambah Data Pertama
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
