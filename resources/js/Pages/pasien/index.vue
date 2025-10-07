<script setup>
import { ref, computed, watch } from "vue"
import { Head, router } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
  psns: {
    type: Array,
    default: () => []
  }
})
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Data Pasien" />
    <div class="container mx-auto p-4">
      <h1 class="text-2xl font-bold mb-4">Daftar Pasien (PSN)</h1>
      
      <div class="mb-4">
        <button 
          class="px-4 py-2 text-sm bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow transition" 
          @click="router.visit('/pasien/create')"
        >
          ➕ Tambah Data Pasien
        </button>
      </div>

      <table class="min-w-full bg-white border border-gray-200">
        <thead>
          <tr>
            <th class="py-2 px-4 border-b">#</th>
            <th class="py-2 px-4 border-b">Nama Pasien</th>
            <th class="py-2 px-4 border-b">NIK</th>
            <th class="py-2 px-4 border-b">No BPJS</th>
            <th class="py-2 px-4 border-b">Agama</th>
            <th class="py-2 px-4 border-b">Tanggal Lahir</th>
            <th class="py-2 px-4 border-b">Kelamin</th>
            <th class="py-2 px-4 border-b">Alamat Lahir</th>
            <th class="py-2 px-4 border-b">Alamat Berdomisili</th>
            <th class="py-2 px-4 border-b">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(psn, index) in psns" :key="psn.id" class="hover:bg-gray-100">
            <td class="py-2 px-4 border-b">{{ index + 1 }}</td>
            <td class="py-2 px-4 border-b">{{ psn.nm_p }}</td>
            <td class="py-2 px-4 border-b">{{ psn.nik }}</td>
            <td class="py-2 px-4 border-b">{{ psn.no_bpjs }}</td>
            <td class="py-2 px-4 border-b">{{ psn.agm }}</td>
            <td class="py-2 px-4 border-b">{{ psn.tgl_lahir }}</td>
            <td class="py-2 px-4 border-b">{{ psn.kelamin }}</td>
            <td class="py-2 px-4 border-b">{{ psn.almt_L }}</td>
            <td class="py-2 px-4 border-b">{{ psn.almt_B }}</td>
            <td class="py-2 px-4 border-b">
              <button 
                @click="router.visit(`/pasien/${psn.id}`)"
                class="text-blue-600 hover:underline mr-2"
              >
                Detail
              </button>
              <button 
                @click="router.visit(`/pasien/${psn.id}/edit`)"
                class="text-green-600 hover:underline"
              >
                Edit
              </button>
            </td>
          </tr>
          <tr v-if="psns.length === 0">
            <td colspan="10" class="text-center py-4">Tidak ada data pasien.</td>
          </tr>
        </tbody>
      </table>
    </div>
  </AuthenticatedLayout>
</template>
