<template>
  <AuthenticatedLayout>
    <Head title="Daftar Pasien & Transaksi" />

    <div class="max-w-5xl mx-auto py-8">
      <h2 class="text-2xl font-bold mb-6 flex items-center gap-2">
        📝 Daftar Pasien & Transaksi
      </h2>

      <div
        v-for="(item, index) in pasien"
        :key="index"
        class="bg-white shadow rounded-lg border mb-4 p-5"
      >
        <!-- Header pasien -->
        <div class="flex justify-between items-center border-b pb-3 mb-3">
          <div class="flex items-center gap-3">
            <span class="text-2xl">👤</span>
            <div>
              <p class="text-lg font-semibold">{{ item.nama_pasien }}</p>
              <p class="text-sm text-gray-500">Tanggal: {{ item.tanggal }}</p>
            </div>
          </div>

          <button
            @click="showDetail(item)"
            class="px-4 py-1.5 text-sm bg-blue-600 hover:bg-blue-700 text-white rounded"
          >
            Show
          </button>
        </div>

        <!-- Ringkasan transaksi -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
          <p class="text-lg font-semibold">{{ item.nama_pasien }}</p>
          <p class="text-sm text-gray-500">Tanggal: {{ item.tanggal }}</p>
          <p>Jenis Perawatan: {{ item.jns_perawatan }}</p>
          <p>Dokter: {{ item.dokter }}</p>
          <p>Tindakan: {{ item.tindakan }}</p>
          <p>Deskripsi 1: {{ item.deskripsi1 }}</p>
          <p>Resep: {{ item.resep }}</p>
          <p>Deskripsi 2: {{ item.deskripsi2 }}</p>

        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue"
import { Head } from "@inertiajs/vue3"

const props = defineProps({
  pasien: Array,
})

function showDetail(item) {
  alert(`Detail pasien: ${item.nama_pasien}\nPerawatan: ${item.jns_perawatan}`)
  // bisa diarahkan ke halaman detail:
  // router.visit(`/kasir/${item.id}`)
}
</script>

<style scoped>
</style>
