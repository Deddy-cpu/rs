<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { useAuth } from '@/composables/useAuth'

const { isAdmin } = useAuth()

const form = ref({
  name: '',
  email: '',
  password: '',
  nama_dokter: '',
  aktif: 'Ya',
})

const loading = ref(false)
const error = ref('')
const success = ref(false)

// Modal state
const showUserModal = ref(false)
const users = ref([])
const loadingUsers = ref(false)
const searchQuery = ref('')

// Fetch users for modal
const fetchUsers = async () => {
  loadingUsers.value = true
  try {
    const response = await fetch('/api/users')
    const data = await response.json()
    users.value = data.users || []
    initializeFilteredUsers() // Initialize filtered users after loading
  } catch (err) {
    console.error('Error fetching users:', err)
    users.value = []
  } finally {
    loadingUsers.value = false
  }
}

// Open modal and fetch users
const openUserModal = () => {
  showUserModal.value = true
  fetchUsers()
}

// Close modal
const closeUserModal = () => {
  showUserModal.value = false
  searchQuery.value = ''
}

// Select user and fill form
const selectUser = (user) => {
  form.value.name = user.name
  form.value.email = user.email
  form.value.nama_dokter = user.name // Auto-fill nama dokter with user name
  form.value.password = '' // Leave password empty for user to fill
  
  closeUserModal()
}

// Filter users based on search query
const filteredUsers = ref([])

// Watch search query and filter users
const filterUsers = () => {
  if (!searchQuery.value) {
    filteredUsers.value = users.value
  } else {
    filteredUsers.value = users.value.filter(user => 
      user.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      user.email.toLowerCase().includes(searchQuery.value.toLowerCase())
    )
  }
}

// Initialize filtered users when users are loaded
const initializeFilteredUsers = () => {
  filteredUsers.value = users.value
}

function submitCreate() {
  loading.value = true
  error.value = ''
  
  router.post('/dokter', form.value, {
    onSuccess: () => {
      success.value = true
      setTimeout(() => {
        router.visit('/dokter')
      }, 1500)
    },
    onError: (errors) => {
      error.value = 'Gagal menambahkan dokter.'
      console.error('Validation errors:', errors)
    },
    onFinish: () => {
      loading.value = false
    }
  })
}
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Tambah Dokter" />

    <div class="min-h-screen bg-cover bg-center flex items-center justify-center" style="background-image: url('/images/bg-login.png')">
      <!-- Card Form Semi-transparan dengan Blur -->
      <div class="bg-white/60 backdrop-blur-md rounded-xl shadow-lg max-w-xl w-full p-6">
        
        <!-- Header -->
        <div class="mb-4 text-center">
          <h1 class="text-2xl font-bold text-blue-700 flex items-center justify-center gap-2 mb-1">
            👨‍⚕️ Tambah Dokter
          </h1>
          <p class="text-gray-600 text-sm">Tambahkan dokter baru ke sistem</p>
        </div>

        <!-- Form -->
        <form @submit.prevent="submitCreate" class="space-y-4 text-sm">
          <!-- User Selection Button -->
          <div class="flex justify-between items-center">
            <label class="block font-medium text-gray-700">Pilih User</label>
            <button
              type="button"
              @click="openUserModal"
              class="px-3 py-1.5 bg-blue-500 text-white text-xs rounded-lg hover:bg-blue-600 transition-colors flex items-center gap-1"
            >
              <i class="fas fa-search text-xs"></i>
              Pilih User
            </button>
          </div>

          <!-- Nama User -->
          <div>
            <label class="block font-medium text-gray-700 mb-1">Nama User</label>
            <input
              v-model="form.name"
              type="text"
              required
              placeholder="Masukkan nama user"
              class="w-full px-3 py-2 rounded-lg border border-gray-300 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-700 placeholder-gray-400 transition"
            />
          </div>

          <!-- Email -->
          <div>
            <label class="block font-medium text-gray-700 mb-1">Email</label>
            <input
              v-model="form.email"
              type="email"
              required
              placeholder="Masukkan email"
              class="w-full px-3 py-2 rounded-lg border border-gray-300 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-700 placeholder-gray-400 transition"
            />
          </div>

          <!-- Password -->
          <div>
            <label class="block font-medium text-gray-700 mb-1">Password</label>
            <input
              v-model="form.password"
              type="password"
              required
              placeholder="Masukkan password"
              class="w-full px-3 py-2 rounded-lg border border-gray-300 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-700 placeholder-gray-400 transition"
            />
          </div>

          <!-- Nama Dokter -->
          <div>
            <label class="block font-medium text-gray-700 mb-1">Nama Dokter</label>
            <input
              v-model="form.nama_dokter"
              type="text"
              required
              placeholder="Masukkan nama dokter"
              class="w-full px-3 py-2 rounded-lg border border-gray-300 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-700 placeholder-gray-400 transition"
            />
          </div>

          <!-- Status Aktif -->
          <div>
            <label class="block font-medium text-gray-700 mb-1">Status</label>
            <select
              v-model="form.aktif"
              required
              class="w-full px-3 py-2 rounded-lg border border-gray-300 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-700 transition"
            >
              <option value="Ya">Aktif</option>
              <option value="Tidak">Nonaktif</option>
            </select>
          </div>

          <!-- Tombol -->
          <div class="flex justify-between gap-2 pt-3">
            <button
              type="button"
              @click="router.visit('/dokter')"
              class="flex items-center justify-center gap-1 px-4 py-2 rounded-lg bg-gray-200 text-gray-700 font-medium hover:bg-gray-300 transition text-sm"
              :disabled="loading"
            >
              ⬅️ Batal
            </button>
            <button
              type="submit"
              class="px-4 py-2 rounded-lg bg-green-600 text-white font-semibold hover:bg-green-700 shadow transition text-sm flex items-center justify-center gap-1"
              :disabled="loading"
            >
              {{ loading ? 'Menyimpan...' : 'Simpan Dokter' }}
              <i v-if="loading" class="fas fa-spinner fa-spin text-xs"></i>
            </button>
          </div>
        </form>

        <!-- Error Message -->
        <div v-if="error" class="mt-3 p-2 rounded-lg bg-red-100 text-red-700 text-xs text-center">
          {{ error }}
        </div>
        
        <!-- Success Message -->
        <div v-if="success" class="mt-3 p-2 rounded-lg bg-green-100 text-green-700 text-xs text-center">
          ✅ Dokter berhasil ditambahkan!
        </div>
      </div>
    </div>

    <!-- User Selection Modal -->
    <div v-if="showUserModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full mx-4 max-h-[80vh] flex flex-col">
        <!-- Modal Header -->
        <div class="flex justify-between items-center p-6 border-b border-gray-200">
          <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
            <i class="fas fa-users text-blue-600"></i>
            Pilih User
          </h3>
          <button
            @click="closeUserModal"
            class="text-gray-400 hover:text-gray-600 transition-colors"
          >
            <i class="fas fa-times text-xl"></i>
          </button>
        </div>

        <!-- Search Input -->
        <div class="p-6 border-b border-gray-200">
          <div class="relative">
            <input
              v-model="searchQuery"
              @input="filterUsers"
              type="text"
              placeholder="Cari user berdasarkan nama atau email..."
              class="w-full px-4 py-2 pl-10 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            />
            <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
          </div>
        </div>

        <!-- Users List -->
        <div class="flex-1 overflow-y-auto p-6">
          <div v-if="loadingUsers" class="text-center py-8">
            <i class="fas fa-spinner fa-spin text-2xl text-blue-600 mb-2"></i>
            <p class="text-gray-600">Memuat daftar user...</p>
          </div>
          
          <div v-else-if="filteredUsers.length === 0" class="text-center py-8">
            <i class="fas fa-user-slash text-4xl text-gray-300 mb-4"></i>
            <p class="text-gray-600">Tidak ada user ditemukan</p>
          </div>

          <div v-else class="space-y-3">
            <div
              v-for="user in filteredUsers"
              :key="user.id"
              @click="selectUser(user)"
              class="p-4 border border-gray-200 rounded-lg hover:bg-blue-50 hover:border-blue-300 cursor-pointer transition-all duration-200"
            >
              <div class="flex items-center justify-between">
                <div>
                  <h4 class="font-medium text-gray-900">{{ user.name }}</h4>
                  <p class="text-sm text-gray-600">{{ user.email }}</p>
                  <span 
                    :class="{
                      'bg-green-100 text-green-800': user.role === 'admin',
                      'bg-blue-100 text-blue-800': user.role === 'dokter',
                      'bg-purple-100 text-purple-800': user.role === 'kasir',
                      'bg-orange-100 text-orange-800': user.role === 'pendaftaran',
                      'bg-gray-100 text-gray-800': user.role === 'kosong'
                    }"
                    class="inline-block px-2 py-1 text-xs font-medium rounded-full mt-1"
                  >
                    {{ user.role }}
                  </span>
                </div>
                <i class="fas fa-chevron-right text-gray-400"></i>
              </div>
            </div>
          </div>
        </div>

        <!-- Modal Footer -->
        <div class="p-6 border-t border-gray-200">
          <button
            @click="closeUserModal"
            class="w-full px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors"
          >
            Batal
          </button>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

