<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
</script>

<template>
  <AuthenticatedLayout>
    <div
      class="min-h-screen bg-cover bg-center flex items-center justify-center p-6"
      style="background-image: url('/images/bg-login.png')"
    >
      <!-- Ubah max-w-md ke max-w-lg biar lebih lebar -->
      <div class="w-full max-w-lg mx-auto bg-white/40 backdrop-blur-md shadow-xl rounded-2xl p-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Add Patient</h2>

        <form @submit.prevent="submitForm" class="space-y-4">
          <!-- id_pasien -->
          <div>
            <label for="id_pasien" class="block text-sm font-medium text-gray-700">Id Pasien</label>
            <input
              v-model="form.id_pasien"
              id="id_pasien"
              type="text"
              required
              class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg 
                     focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            />
          </div>

          <!-- dokter -->
          <div>
            <label for="dokter" class="block text-sm font-medium text-gray-700">Dokter</label>
            <input
              v-model="form.dokter"
              id="dokter"
              type="text"
              required
              class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg 
                     focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            />
          </div>

          <!-- tindakan -->
          <div>
            <label for="tindakan" class="block text-sm font-medium text-gray-700">Tindakan</label>
            <input
              v-model="form.tindakan"
              id="tindakan"
              type="text"
              required
              class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg 
                     focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            />
          </div>

          <!-- jumlah -->
          <div>
            <label for="jumlah" class="block text-sm font-medium text-gray-700">Jumlah</label>
            <input
              v-model="form.jumlah"
              id="jumlah"
              type="number"
              required
              class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg 
                     focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            />
          </div>

          <!-- Buttons -->
          <div class="flex justify-between gap-3">
            <button
              type="button"
              @click="goBack"
              class="w-1/2 bg-gray-200 text-gray-700 py-2 px-4 rounded-lg 
                     hover:bg-gray-300 transition font-medium flex items-center justify-center"
            >
              ⬅ Kembali
            </button>

            <button
              type="submit"
              class="w-1/2 bg-green-600 text-white py-2 px-4 rounded-lg 
                     hover:bg-green-700 transition font-semibold flex items-center justify-center"
            >
              Add Patient
            </button>
          </div>
        </form>

        <!-- Success Message -->
        <div
          v-if="success"
          class="mt-4 p-3 rounded-lg bg-green-100/80 text-green-700 text-sm text-center shadow"
        >
          ✅ Data berhasil ditambahkan!
        </div>
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
        id_pasien: '',
        dokter: '',
        tindakan: '',
        jumlah: ''
      },
      success: false
    }
  },
  methods: {
    async submitForm() {
      try {
        await axios.post('/tindakan', this.form)
        this.success = true
        this.form = { id_pasien: '', dokter: '', tindakan: '', jumlah: '' }
        setTimeout(() => (this.success = false), 3000)
        window.location.href = '/tindakan'
      } catch (error) {
        console.error(error.response?.data || error.message)
        alert("Gagal menambahkan pasien!")
      }
    },
    goBack() {
      window.location.href = '/tindakan'
    }
  }
}
</script>
