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
            <!-- Overlay supaya teks lebih jelas -->
            <div class="backdrop-brightness-95 bg-white/70 rounded-lg p-6">
                <h1 class="text-2xl font-bold mb-4 text-blue-700">User Table</h1>

                <button
                    class="mb-4 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition"
                    @click="goToCreateUser"
                >
                    Add User
                </button>

                <div class="overflow-x-auto bg-white shadow rounded-lg">
                    <table class="w-full border-collapse">
                        <thead class="bg-blue-600 text-white">
                            <tr>
                                <th class="px-4 py-2 border">ID</th>
                                <th class="px-4 py-2 border">Name</th>
                                <th class="px-4 py-2 border">Email</th>
                                <th class="px-4 py-2 border">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="user in users"
                                :key="user.id"
                                class="odd:bg-white even:bg-blue-50 hover:bg-blue-100 transition"
                            >
                                <td class="px-4 py-2 border">{{ user.id }}</td>
                                <td class="px-4 py-2 border">{{ user.name }}</td>
                                <td class="px-4 py-2 border">{{ user.email }}</td>
                                <td class="px-4 py-2 border text-center">
                                    <button
                                        class="px-2 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 transition"
                                        @click="editUser(user.id)"
                                    >
                                        Edit
                                    </button>
                                    <button
                                        class=" ml-2 px-2 py-1 bg-red-500 text-white rounded hover:bg-red-600 transition"
                                        @click="deleteUser(user.id)"
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
