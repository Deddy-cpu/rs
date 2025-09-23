<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
</script>

<template>
  <AuthenticatedLayout>
    <div class="max-w-md mx-auto bg-white shadow-lg rounded-xl p-6 mt-10">
      <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Add User</h2>

      <form @submit.prevent="submitForm" class="space-y-4">
        <!-- Name -->
        <div>
          <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
          <input
            v-model="form.name"
            id="name"
            type="text"
            required
            class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
          />
        </div>

        <!-- Email -->
        <div>
          <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
          <input
            v-model="form.email"
            id="email"
            type="email"
            required
            class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
          />
        </div>

        <!-- Password with visibility toggle -->
        <div>
          <label for="password" class="block text-sm font-medium text-gray-700">Password</label>

          <div class="mt-1 relative">
            <input
              v-model="form.password"
              id="password"
              :type="showPassword ? 'text' : 'password'"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-lg pr-12 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            />

            <!-- Toggle button -->
            <button
              type="button"
              @click="togglePassword"
              :aria-pressed="showPassword.toString()"
              class="absolute inset-y-0 right-0 pr-3 flex items-center text-sm"
              aria-label="Toggle password visibility"
            >
              <!-- simple eye / eye-off SVG icons -->
              <template v-if="!showPassword">
                <!-- eye (show) -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
              </template>
              <template v-else>
                <!-- eye-off (hide) -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.97 9.97 0 012.223-3.607M6.1 6.1A9.965 9.965 0 0112 5c4.477 0 8.268 2.943 9.542 7a9.987 9.987 0 01-1.33 2.705M3 3l18 18"/>
                </svg>
              </template>
            </button>
          </div>
        </div>

        <!-- Button -->
        <button
          type="submit"
          class="w-full bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700 transition duration-200 font-semibold"
        >
          Add User
        </button>
      </form>

      <!-- Success Message -->
      <div
        v-if="success"
        class="mt-4 p-3 rounded-lg bg-green-100 text-green-700 text-sm text-center"
      >
        User berhasil ditambahkan.
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script lang="ts">
import axios from "axios"

export default {
  data() {
    return {
      form: {
        name: '',
        email: '',
        password: ''
      },
      success: false,
      showPassword: false
    }
  },
  methods: {
    togglePassword() {
      this.showPassword = !this.showPassword
    },

    async submitForm() {
      try {
        await axios.post('/users', this.form) // panggil Laravel API
        this.success = true
        this.form = { name: '', email: '', password: '' }

        // tampilkan pesan success sebentar lalu redirect
        setTimeout(() => {
          this.success = false
          window.location.href = '/users'; // Redirect to users list page
        }, 1500)

      } catch (error) {
        console.error(error.response?.data || error.message)
        alert("Gagal menambahkan user!")
      }
    }
  },
}
</script>