<script setup lang="ts">
import { ref } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import PasswordInput from '@/Components/PasswordInput.vue'

const form = ref({
  name: '',
  email: '',
  password: '',
  role: '',
  ruangan: ''
})

const success = ref(false)

const submitForm = async () => {
  try {
    await router.post('/users', form.value, {
      onSuccess: () => {
        success.value = true
        form.value = { name: '', email: '', password: '', role: '', ruangan: '' }
        
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
              @change="form.ruangan = ''"
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

          <!-- Ruangan/Poli (hanya untuk dokter dan perawat) -->
          <div v-if="form.role === 'dokter' || form.role === 'perawat'">
            <label for="ruangan" class="block text-sm font-medium text-gray-700">
              Ruangan/Poli <span class="text-gray-500 text-xs">(Opsional)</span>
            </label>
            <select
              v-model="form.ruangan"
              id="ruangan"
              class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg 
                     focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            >
              <option value="">Pilih Poli/Ruangan</option>
              <option value="Poli Umum">Poli Umum</option>
              <option value="Poli Gigi">Poli Gigi</option>
              <option value="Poli Anak">Poli Anak</option>
              <option value="Poli Kandungan">Poli Kandungan</option>
              <option value="Poli Mata">Poli Mata</option>
              <option value="Poli THT">Poli THT</option>
              <option value="UGD">UGD</option>
              <option value="Rawat Inap">Rawat Inap</option>
              <option value="Laboratorium">Laboratorium</option>
              <option value="Farmasi">Farmasi</option>
              <!-- tambahkan opsi poli/ruangan lain sesuai kebutuhan -->
            </select>
            <p class="mt-1 text-xs text-gray-500">
              Jika diisi, dokter/perawat hanya akan melihat pasien dari ruangan/poli ini
            </p>
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
            <PasswordInput
              id="password"
              v-model="form.password"
              class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              required
              autocomplete="new-password"
            />
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
