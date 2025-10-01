<script setup lang="ts">
import { ref } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router } from "@inertiajs/vue3";
import Swal from "sweetalert2";

interface User {
  id: number;
  name: string;
  email: string;
}

const props = defineProps({
  users: Object,
  filters: Object,
});

const search = ref(props.filters.search || "");

function goToCreateUser() {
  router.visit("/users/create");
}

function deleteUser(id: number) {
  Swal.fire({
    title: "Apakah kamu yakin?",
    text: "User akan dihapus permanen!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#d33",
    cancelButtonColor: "#3085d6",
    confirmButtonText: "Ya, hapus!",
  }).then((result) => {
    if (result.isConfirmed) {
      router.delete(`/users/${id}`, {
        onSuccess: () => {
          Swal.fire("Dihapus!", "User berhasil dihapus.", "success");
        },
      });
    }
  });
}

function editUser(id: number) {
  router.visit(`/users/${id}/edit`);
}

function searchUser() {
  router.get(route("users.index"), { search: search.value }, { preserveState: true, replace: true });
}
</script>

<template>
  <AuthenticatedLayout>
    <Head title="User Table" />

    <div
      class="min-h-screen bg-cover bg-center p-6"
      style="background-image: url('/images/bg-login.png')"
    >
      <div class="bg-white/70 backdrop-blur-md rounded-lg shadow-lg p-6">
        <h1 class="text-2xl font-bold mb-4 text-blue-700">Daftar User</h1>

        <div class="flex justify-between items-center mb-4">
          <button
            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition"
            @click="goToCreateUser"
          >
            + Tambah User
          </button>

          <!-- Search -->
          <div class="flex gap-2">
            <input
              type="text"
              v-model="search"
              placeholder="Cari user..."
              class="px-3 py-2 border rounded-lg w-64 focus:outline-none focus:ring focus:ring-blue-300"
            />
            <button
              @click="searchUser"
              class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
            >
              🔍 Cari
            </button>
          </div>
        </div>

        <!-- Table -->
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
                v-for="(user, index) in props.users.data"
                :key="user.id"
                class="border-t hover:bg-gray-100/60 transition"
              >
                <td class="px-4 py-3 text-gray-700">
                  {{ (props.users.current_page - 1) * props.users.per_page + index + 1 }}
                </td>
                <td class="px-4 py-3 text-gray-800 font-medium">{{ user.name }}</td>
                <td class="px-4 py-3 text-gray-600">{{ user.email }}</td>
                <td class="px-4 py-3 text-center space-x-2">
                  <button
                    @click="editUser(user.id)"
                    class="px-3 py-1.5 bg-yellow-500 text-white rounded-md hover:bg-yellow-600 transition"
                  >
                    ✏️ Edit
                  </button>
                  <button
                    @click="deleteUser(user.id)"
                    class="px-3 py-1.5 bg-red-600 text-white rounded-md hover:bg-red-700 transition"
                  >
                    🗑 Hapus
                  </button>
                </td>
              </tr>

              <tr v-if="props.users.data.length === 0">
                <td colspan="4" class="px-4 py-6 text-center text-gray-500 italic">
                  Tidak ada user tersedia.
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div class="flex gap-2 mt-4 flex-wrap">
          <button
            v-for="link in props.users.links"
            :key="link.label"
            v-html="link.label"
            @click="link.url && router.get(link.url)"
            :disabled="!link.url"
            class="px-3 py-1 border rounded"
            :class="{ 'bg-blue-500 text-white': link.active, 'text-gray-400': !link.url }"
          />
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
