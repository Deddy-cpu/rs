<script setup lang="ts">
import { ref, watch } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import { useAuth } from "@/composables/useAuth";

interface User {
  id: number;
  name: string;
  role: 'admin' | 'dokter' | 'kasir' | 'pendaftaran' | 'kosong';
  email: string;
}

const props = defineProps({
  users: Object,
  filters: Object,
});

const search = ref(props.filters.search || "");

// Use auth composable
const { isAdmin } = useAuth();

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
          Swal.fire("Dihapus!", "User berhasil dihapus.", "success").then(() => {
            router.reload({ only: ["users"] });
          });
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

// Debounce helper
function debounce<T extends (...args: any[]) => void>(fn: T, delay = 400) {
  let timeout: ReturnType<typeof setTimeout>
  return (...args: Parameters<T>) => {
    clearTimeout(timeout)
    timeout = setTimeout(() => fn(...args), delay)
  }
}

// Debounced search
const debouncedSearch = debounce(() => {
  searchUser()
}, 400)

// Watch search and trigger debounced search
watch(search, () => {
  debouncedSearch()
})
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Data User" />

    <div class="min-h-screen bg-cover bg-center p-6" style="background-image: url('/images/bg-login.png')">

        
        <!-- Header -->
        <div class="mb-4">
  <!-- Header Tengah -->
  <h1 class="text-3xl font-bold text-gray-900 flex items-center justify-center gap-2 mb-2">
    <i class="fas fa-users-cog text-red-600"></i>
    Data User
  </h1>
  <p class="text-gray-600 text-center mb-2">
    Kelola data pengguna sistem dan hak akses
  </p>

  <!-- Baris Tombol + Search -->
  <div
    class="flex flex-col md:flex-row justify-between items-center gap-4
    bg-transparent px-1 pt-0 pb-0"
  >
    <!-- Tombol Tambah (Admin Only) -->
    <button
      v-if="isAdmin"
      @click="goToCreateUser"
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
      Tambah User
    </button>

    <!-- Search -->
    <div class="flex items-center space-x-3 w-full md:w-auto">
      <div class="relative flex-1 md:flex-none">
        <input
          v-model="search"
          type="text"
          placeholder="Cari user berdasarkan nama atau email..."
          class="w-full md:w-96 pl-5 pr-5 py-3 border border-red-200 rounded-2xl focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all duration-200 bg-red-50 focus:bg-white text-lg shadow"
        />
      </div>
    </div>
  </div>
</div>




        <!-- Table -->
        <div class="overflow-x-auto shadow-md rounded-xl border border-gray-200 bg-white/70 backdrop-blur-sm">
          <table class="w-full text-sm text-left text-gray-700">
            <thead class="bg-red-600/90 text-white text-sm uppercase tracking-wide">
              <tr>
                <th class="px-6 py-3 text-center">No</th>
                <th class="px-6 py-3">Nama</th>
                <th class="px-6 py-3">Role</th>
                <th class="px-6 py-3">Email</th>
                <th class="px-6 py-3 text-center">Aksi</th>
              </tr>
            </thead>

            <tbody class="divide-y divide-gray-200">
              <tr
                v-for="(user, index) in props.users.data"
                :key="user.id"
                class="hover:bg-red-50 transition duration-150 ease-in-out"
              >
                <td class="px-6 py-3 text-center font-semibold text-gray-800">
                  {{ (props.users.current_page - 1) * props.users.per_page + index + 1 }}
                </td>
                <td class="px-6 py-3">{{ user.name }}</td>
                <td class="px-6 py-3">{{ user.role }}</td>
                <td class="px-6 py-3">{{ user.email }}</td>
                <td class="px-6 py-3 text-center space-x-2">
                  <button
                    v-if="isAdmin"
                    @click="editUser(user.id)"
                    class="px-3 py-1.5 bg-yellow-500 text-white rounded-md hover:bg-yellow-600 transition shadow-sm"
                  >
                    ✏️ Edit
                  </button>
                  <button
                    v-if="isAdmin"
                    @click="deleteUser(user.id)"
                    class="px-3 py-1.5 bg-red-600 text-white rounded-md hover:bg-red-700 transition shadow-sm"
                  >
                    🗑 Hapus
                  </button>
                  <span v-if="!isAdmin" class="text-gray-400 text-sm">No actions</span>
                </td>
              </tr>

              <tr v-if="props.users.data.length === 0">
                <td colspan="4" class="px-6 py-10 text-center text-gray-500">
                  <div class="flex flex-col items-center">
                    <span class="text-4xl mb-2">📭</span>
                    <p class="text-gray-600 font-medium">Belum ada user terdaftar.</p>
                    <button
                      v-if="isAdmin"
                      @click="goToCreateUser"
                      class="mt-4 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition"
                    >
                      Tambah User Pertama
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
            <template v-for="link in props.users.links" :key="link.label">
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
