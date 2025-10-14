<script setup lang="ts">
import { ref } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const form = ref({
  name: '',
  email: '',
  password: '',
  role: ''
})

const success = ref(false)
const showPassword = ref(false)

const togglePassword = () => {
  showPassword.value = !showPassword.value
}

const submitForm = async () => {
  try {
    await router.post('/users', form.value, {
      onSuccess: () => {
        success.value = true
        form.value = { name: '', email: '', password: '', role: '' }
        
        setTimeout(() => {
          success.value = false
          router.visit('/users')
        }, 1500)
      },
      onError: (errors) => {
        console.error('Validation errors:', errors)
        alert("Gagal menambahkan user!")
      }
    })
  } catch (error) {
    console.error(error)
    alert("Gagal menambahkan user!")
  }
}
</script>

<template>
  <Head title="Tambah User" />
  <AuthenticatedLayout>
    <div
      class="min-h-screen bg-cover bg-center flex items-center justify-center p-6"
      style="background-image: url('/images/bg-login.png')"
    >
      <div class="max-w-md w-full bg-white/70 backdrop-blur-md shadow-lg rounded-xl p-6">
        <!-- Header -->
        <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Tambah User</h2>

        <!-- Form -->
        <form @submit.prevent="submitForm" class="space-y-4">
          <!-- Name -->
          <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Nama</label>
            <input
              v-model="form.name"
              id="name"
              type="text"
              required
              class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg 
                     focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            />
          </div>

          <!-- Role -->
          <div>
            <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
            <select
              v-model="form.role"
              id="role"
              required
              class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg 
                     focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            >
              <option value="" disabled>Pilih Role</option>
              <option value="admin">Admin</option>
              <option value="dokter">Dokter</option>
              <option value="kasir">Kasir</option>
              <option value="pendaftaran">Pendaftaran</option>
              <option value="perawat">Perawat</option>
              <option value="manajemen">Manajemen</option>
              <option value="kosong">Kosong</option>
            </select>
          </div>

          <!-- Email -->
          <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
            <input
              v-model="form.email"
              id="email"
              type="email"
              required
              class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg 
                     focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            />
          </div>

          <!-- Password -->
          <div>
            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
            <div class="mt-1 relative">
              <input
                v-model="form.password"
                id="password"
                :type="showPassword ? 'text' : 'password'"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-lg pr-12 
                       focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              />
              <!-- Toggle eye -->
              <button
                type="button"
                @click="togglePassword"
                class="absolute inset-y-0 right-0 pr-3 flex items-center text-sm"
              >
                <template v-if="!showPassword">
                  <!-- eye -->
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                       viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 
                             2.943 9.542 7-1.274 4.057-5.065 7-9.542 
                             7-4.477 0-8.268-2.943-9.542-7z" />
                  </svg>
                </template>
                <template v-else>
                  <!-- eye-off -->
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                       viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M13.875 18.825A10.05 10.05 0 0112 
                             19c-4.477 0-8.268-2.943-9.542-7a9.97 
                             9.97 0 012.223-3.607M6.1 6.1A9.965 
                             9.965 0 0112 5c4.477 0 8.268 2.943 
                             9.542 7a9.987 9.987 0 01-1.33 
                             2.705M3 3l18 18"/>
                  </svg>
                </template>
              </button>
            </div>
          </div>

          <!-- Buttons (Kembali + Add User) -->
          <div class="flex justify-between gap-2">
            <button
              type="button"
              @click="router.visit('/users')"
              class="w-1/2 bg-gray-200 text-gray-700 py-2 px-4 rounded-lg 
                     hover:bg-gray-300 transition font-medium"
            >
              ⬅ Kembali
            </button>

            <button
              type="submit"
              class="w-1/2 bg-green-600 text-white py-2 px-4 rounded-lg 
                     hover:bg-green-700 transition font-semibold"
            >
              Tambah User
            </button>
          </div>
        </form>

        <!-- Success -->
        <div
          v-if="success"
          class="mt-4 p-3 rounded-lg bg-green-100 text-green-700 text-sm text-center"
        >
          User berhasil ditambahkan.
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
