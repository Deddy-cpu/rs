<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { useAuth } from '@/composables/useAuth'

interface Dokter {
  id: number
  user_id: number
  nama_dokter: string
  aktif: string
  user: {
    id: number
    name: string
    email: string
    role: string
  }
}

const props = defineProps<{
  dokter: Dokter
}>()

const { isAdmin } = useAuth()

const form = ref({
  name: props.dokter.user.name,
  email: props.dokter.user.email,
  password: '',
  nama_dokter: props.dokter.nama_dokter,
  aktif: props.dokter.aktif,
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

function submitEdit() {
  loading.value = true
  error.value = ''
  
  // Remove empty password from form data
  const formData = { ...form.value }
  if (!formData.password) {
    delete formData.password
  }
  
  router.put(`/dokter/${props.dokter.id}`, formData, {
    onSuccess: () => {
      success.value = true
      setTimeout(() => {
        router.visit('/dokter')
      }, 1500)
    },
    onError: (errors) => {
      error.value = 'Gagal mengupdate data dokter.'
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
    <Head title="Edit Dokter" />

    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-50 p-4 md:p-6">
      <div class="max-w-2xl mx-auto">
        
        <!-- Header Section -->
        <div class="mb-8">
          <div class="text-center mb-6">
            <div class="w-20 h-20 bg-gradient-to-r from-yellow-500 to-orange-500 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
              <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
              </svg>
            </div>
            <h1 class="text-4xl md:text-5xl font-bold bg-gradient-to-r from-yellow-600 to-orange-600 bg-clip-text text-transparent mb-2">
              Edit Dokter
            </h1>
            <p class="text-gray-600 text-lg">Perbarui informasi dokter klinik</p>
          </div>
        </div>

        <!-- Form Container -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
          
          <!-- Form Header -->
          <div class="bg-gradient-to-r from-yellow-500 to-orange-500 px-6 py-4">
            <div class="flex items-center gap-3">
              <div class="p-2 bg-white/20 rounded-lg">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
              </div>
              <div>
                <h2 class="text-xl font-bold text-white">Informasi Dokter</h2>
                <p class="text-yellow-100 text-sm">Lengkapi form di bawah untuk memperbarui data</p>
              </div>
            </div>
          </div>

          <!-- Form Content -->
          <form @submit.prevent="submitEdit" class="p-6 space-y-6">
            
            <!-- Personal Information Section -->
            <div class="space-y-4">
              <div class="flex items-center gap-3 mb-4">
                <div class="p-2 bg-blue-100 rounded-lg">
                  <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                  </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900">Informasi Personal</h3>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Nama User -->
                <div class="space-y-2">
                  <div class="flex justify-between items-center">
                    <label class="block text-sm font-medium text-gray-700">Nama User</label>
                    <button
                      type="button"
                      @click="openUserModal"
                      class="px-3 py-1.5 bg-blue-500 text-white text-xs rounded-lg hover:bg-blue-600 transition-colors flex items-center gap-1"
                    >
                      <i class="fas fa-search text-xs"></i>
                      Pilih User
                    </button>
                  </div>
                  <div class="relative">
                    <input
                      v-model="form.name"
                      type="text"
                      required
                      class="w-full pl-4 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 bg-gray-50 focus:bg-white"
                      placeholder="Masukkan nama user"
                    />
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                      <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                      </svg>
                    </div>
                  </div>
                </div>

                <!-- Nama Dokter -->
                <div class="space-y-2">
                  <label class="block text-sm font-medium text-gray-700">Nama Dokter</label>
                  <div class="relative">
                    <input
                      v-model="form.nama_dokter"
                      type="text"
                      required
                      class="w-full pl-4 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 bg-gray-50 focus:bg-white"
                      placeholder="Masukkan nama dokter"
                    />
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                      <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                      </svg>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Contact Information Section -->
            <div class="space-y-4">
              <div class="flex items-center gap-3 mb-4">
                <div class="p-2 bg-green-100 rounded-lg">
                  <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                  </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900">Informasi Kontak</h3>
              </div>

              <!-- Email -->
              <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700">Email</label>
                <div class="relative">
                  <input
                    v-model="form.email"
                    type="email"
                    required
                    class="w-full pl-4 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 bg-gray-50 focus:bg-white"
                    placeholder="Masukkan email"
                  />
                  <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                  </div>
                </div>
              </div>

              <!-- Password (Optional) -->
              <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700">Password Baru (Opsional)</label>
                <div class="relative">
                  <input
                    v-model="form.password"
                    type="password"
                    class="w-full pl-4 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 bg-gray-50 focus:bg-white"
                    placeholder="Kosongkan jika tidak ingin mengubah password"
                  />
                  <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                  </div>
                </div>
                <p class="text-xs text-gray-500 flex items-center gap-1">
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                  Kosongkan jika tidak ingin mengubah password
                </p>
              </div>
            </div>

            <!-- System Information Section -->
            <div class="space-y-4">
              <div class="flex items-center gap-3 mb-4">
                <div class="p-2 bg-purple-100 rounded-lg">
                  <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                  </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900">Status Sistem</h3>
              </div>

              <!-- Status Aktif -->
              <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700">Status</label>
                <div class="relative">
                  <select
                    v-model="form.aktif"
                    class="w-full pl-4 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 bg-gray-50 focus:bg-white appearance-none"
                    required
                  >
                    <option value="Ya">Aktif</option>
                    <option value="Tidak">Nonaktif</option>
                  </select>
                  <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                  </div>
                </div>
              </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row justify-between gap-4 pt-6 border-t border-gray-200">
              <button
                type="button"
                @click="router.visit('/dokter')"
                class="flex items-center justify-center gap-2 px-6 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-all duration-200 font-semibold"
                :disabled="loading"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Batal
              </button>
              <button
                type="submit"
                class="flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl hover:from-blue-700 hover:to-blue-800 transition-all duration-200 font-semibold shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed"
                :disabled="loading"
              >
                <svg v-if="loading" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                {{ loading ? 'Menyimpan...' : 'Simpan Perubahan' }}
              </button>
            </div>
          </form>

          <!-- Messages -->
          <div v-if="error || success" class="px-6 pb-6">
            <!-- Error Message -->
            <div v-if="error" class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm">
              <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                {{ error }}
              </div>
            </div>
            
            <!-- Success Message -->
            <div v-if="success" class="p-4 rounded-xl bg-green-50 border border-green-200 text-green-700 text-sm">
              <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Data dokter berhasil diperbarui!
              </div>
            </div>
          </div>
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
