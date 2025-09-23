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

    <!-- Background -->
    <div
      class="min-h-screen bg-cover bg-center p-6"
      style="background-image: url('/images/bg-login.png')"
    >
      <!-- Overlay biar teks tetap jelas -->
      <div class="backdrop-brightness-95 bg-white/80 rounded-lg p-6 shadow-lg">
        <h1 class="text-2xl font-bold mb-4 text-green-700">Data Tindakan Dokter</h1>

        <button
          class="mb-4 px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition"
          @click="goToCreateTindakan"
        >
          Add Tindakan Pasien
        </button>

        <div class="overflow-x-auto bg-white shadow rounded-lg">
          <table class="w-full border-collapse">
            <div class="overflow-x-auto bg-white shadow-lg rounded-xl border border-gray-200">
  <table class="min-w-full text-sm text-left text-gray-600">
    <!-- Header -->
    <thead class="bg-gradient-to-r from-green-600 to-green-700 text-white text-sm uppercase tracking-wide">
      <tr>
        <th class="px-4 py-3 text-center">ID</th>
        <th class="px-4 py-3 text-center">ID Pasien</th>
        <th class="px-4 py-3">Dokter</th>
        <th class="px-4 py-3">Tindakan</th>
        <th class="px-4 py-3 text-center">Jumlah</th>
        <th class="px-4 py-3 text-center">Aksi</th>
      </tr>
    </thead>

    <!-- Body -->
    <tbody class="divide-y divide-gray-200">
      <tr
        v-for="record in records"
        :key="record.id"
        class="hover:bg-green-50 transition"
      >
        <td class="px-4 py-3 text-center font-semibold text-gray-700">{{ record.id }}</td>
        <td class="px-4 py-3 text-center">{{ record.id_pasien }}</td>
        <td class="px-4 py-3">{{ record.dokter }}</td>
        <td class="px-4 py-3">{{ record.tindakan }}</td>
        <td class="px-4 py-3 text-center">{{ record.jumlah }}</td>
        <td class="px-4 py-3 flex items-center justify-center space-x-2">
          <button
            class="px-3 py-1 bg-blue-500 text-white rounded-lg shadow hover:bg-blue-600 transition"
            @click="editTindakan(record.id)"
          >
            Edit
          </button>
          <button
            class="px-3 py-1 bg-red-500 text-white rounded-lg shadow hover:bg-red-600 transition"
            @click="deleteTindakan(record.id)"
          >
            Delete
          </button>
        </td>
      </tr>
    </tbody>
  </table>
</div>
            <tbody>
              <tr
                v-for="record in records"
                :key="record.id"
                class="odd:bg-white even:bg-green-50 hover:bg-green-100 transition"
              >
                <td class="px-4 py-2 border">{{ record.id }}</td>
                <td class="px-4 py-2 border">{{ record.id_pasien }}</td>
                <td class="px-4 py-2 border">{{ record.dokter }}</td>
                <td class="px-4 py-2 border">{{ record.tindakan }}</td>
                <td class="px-4 py-2 border text-center">{{ record.jumlah }}</td>
                <td class="px-4 py-2 border text-center">
                  <button
                    class="px-2 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 transition"
                    @click="editTindakan(record.id)"
                  >
                    Edit
                  </button>
                  <button
                    class="ml-2 px-2 py-1 bg-red-500 text-white rounded hover:bg-red-600 transition"
                    @click="deleteTindakan(record.id)"
                  >
                    Delete
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
