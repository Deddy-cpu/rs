<template>
  <AuthenticatedLayout>
    <div class="max-w-md mx-auto bg-white shadow-lg rounded-xl p-6 mt-10">
      <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Add Pasient</h2>

      <form @submit.prevent="submitForm" class="space-y-4">
        <!-- id_pasien -->
        <div>
          <label for="id_pasien" class="block text-sm font-medium text-gray-700">Id pasien</label>
          <input
              v-model="form.id_pasien"
            id="id_pasien"
            type="text"
            required
            class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
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
            class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
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
            class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
          />
        </div>

        <!-- jumlah -->
        <div>
          <label for="jumlah" class="block text-sm font-medium text-gray-700">Jumlah</label>
          <input
            v-model="form.jumlah"
            id="jumlah"
            type="text"
            required
            class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
          />
        </div>

        <!-- Button -->
        <div class="flex space-x-3">
          <button
            type="submit"
            class="flex-1 bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700 transition duration-200 font-semibold"
          >
            Add Patient
          </button>
          <button
            type="button"
            @click="goBack"
            class="flex-1 bg-gray-500 text-white py-2 px-4 rounded-lg hover:bg-gray-600 transition duration-200 font-semibold"
          >
            Kembali
          </button>
        </div>
      </form>

      <!-- Success Message -->
      <div
        v-if="success"
        class="mt-4 p-3 rounded-lg bg-green-100 text-green-700 text-sm text-center"
      >
        ✅ Data berhasil ditambahkan!
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
      window.location.href = '/tindakan' // balik ke daftar tindakan
    }
  }
}
</script>
