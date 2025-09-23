<script setup lang="ts">
import { ref } from 'vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, usePage, router } from '@inertiajs/vue3'
import Swal from 'sweetalert2'

// Define User type
interface User {
    id: number
    name: string
    email: string
}

const page = usePage()
const users = ref<Array<User>>(Array.isArray(page.props.users) ? page.props.users : [])

function goToCreateUser() {
    router.visit('/users/create')
}

function deleteUser(id: number) {
  Swal.fire({
    title: "Apakah kamu yakin?",
    text: "User akan dihapus permanen!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#d33",
    cancelButtonColor: "#3085d6",
    confirmButtonText: "Ya, hapus!"
  }).then((result) => {
    if (result.isConfirmed) {
      router.delete(`/users/${id}`, {
        onSuccess: () => {
          Swal.fire("Dihapus!", "User berhasil dihapus.", "success").then(() => {
            router.reload({ only: ['users'] })
          })
        },
        onError: (err) => {
          console.error("Gagal menghapus user:", err)
          Swal.fire("Gagal!", "Terjadi kesalahan saat menghapus user.", "error")
        }
      })
    }
  })
}

function editUser(id: number) {
    router.visit(`/users/${id}/edit`)
}
</script>

<template>
  <AuthenticatedLayout>
    <Head title="User Table" />

    <!-- Background -->
    <div
      class="min-h-screen bg-cover bg-center p-6"
      style="background-image: url('/images/bg-login.png')"
    >
      <!-- Overlay transparan -->
      <div class="bg-white/70 backdrop-blur-md rounded-lg shadow-lg p-6">
        <h1 class="text-2xl font-bold mb-4 text-blue-700">Daftar User</h1>

        <div class="flex justify-between items-center mb-4">
          <input
            type="text"
            placeholder="Cari user..."
            class="px-3 py-2 border rounded-lg w-1/3 focus:outline-none focus:ring focus:ring-blue-300"
          />
          <button
            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition"
            @click="goToCreateUser"
          >
            + Tambah User
          </button>
        </div>

        <!-- Table transparan -->
        <div class="overflow-x-auto bg-white/80 rounded-lg shadow-md">
          <table class="w-full border-collapse">
            <thead class="bg-blue-600/90 text-white">
              <tr>
                <th class="px-4 py-2 border">No</th>
                <th class="px-4 py-2 border">Nama</th>
                <th class="px-4 py-2 border">Email</th>
                <th class="px-4 py-2 border">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="(user, index) in users"
                :key="user.id"
                class="border-t hover:bg-gray-100/60 transition"
              >
                <td class="px-4 py-3 text-gray-700">{{ index + 1 }}</td>
                <td class="px-4 py-3 text-gray-800 font-medium">{{ user.name }}</td>
                <td class="px-4 py-3 text-gray-600">{{ user.email }}</td>
                <td class="px-4 py-3 text-center space-x-2">
                  <button
                    class="px-3 py-1.5 bg-yellow-400 text-white rounded hover:bg-yellow-500 transition"
                    @click="editUser(user.id)"
                  >
                    Edit
                  </button>
                  <button
                    class="px-3 py-1.5 bg-red-500 text-white rounded hover:bg-red-600 transition"
                    @click="deleteUser(user.id)"
                  >
                    Hapus
                  </button>
                </td>
              </tr>

              <!-- Empty State -->
              <tr v-if="users.length === 0">
                <td colspan="4" class="px-4 py-6 text-center text-gray-500 italic">
                  Tidak ada user tersedia.
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
