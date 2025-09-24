<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue"
import { Head } from "@inertiajs/vue3"

const props = defineProps({
  pasien: Array, // [{id, nama_pasien, ... transaksi: [{... detail: []}]}]
})

function showDetail(item) {
  alert(`Detail pasien: ${item.nama_pasien}`)
  // bisa diarahkan ke halaman detail jika mau:
  // router.visit(`/kasir/${item.id}`)
}
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Daftar Pasien & Transaksi" />

    <div
      class="min-h-screen bg-cover bg-center p-6"
      style="background-image: url('/images/bg-login.png')"
    >
      <div class="max-w-6xl mx-auto">
        <div
          class="bg-white/70 backdrop-blur-sm rounded-xl shadow-lg p-6 mb-6"
        >
          <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold flex items-center gap-2">
              📝 Daftar Pasien & Transaksi
            </h2>
            <a
              href="/kasir/create"
              class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
            >
              ➕ Tambah Data Pasien
            </a>
          </div>

          <!-- Loop pasien -->
          <div
            v-for="(p, idx) in pasien"
            :key="idx"
            class="bg-white/70 backdrop-blur-sm shadow rounded-lg border mb-6 p-5"
          >
            <!-- Header pasien -->
            <div class="flex justify-between items-center border-b pb-3 mb-4">
              <div class="flex items-center gap-3">
                <span class="text-2xl">👤</span>
                <div>
                  <p class="text-lg font-semibold">{{ p.nama_pasien }}</p>
                  <p class="text-sm text-gray-500">
                    ALAMAT: {{ p.alamat }}, PENJAMIN: {{ p.Penjamin }}
                  </p>
                </div>
              </div>

              <button
                @click="showDetail(p)"
                class="px-4 py-1.5 text-sm bg-blue-600 hover:bg-blue-700 text-white rounded"
              >
                Show
              </button>
            </div>

            <!-- Loop transaksi -->
            <div v-for="(trx, tIndex) in p.transaksi" :key="tIndex" class="mb-4">
              <h3 class="font-semibold mb-2">
                📅 {{ trx.tanggal }} — {{ trx.perawatan }} ({{ trx.dokter }})
              </h3>

              <!-- Loop detail transaksi -->
              <div
                class="overflow-x-auto rounded-lg shadow border border-gray-200 bg-white/70 backdrop-blur-sm"
              >
                <table class="w-full text-sm">
                  <thead class="bg-gray-200/80">
                    <tr>
                      <th class="p-2 border">Tindakan</th>
                      <th class="p-2 border">Jumlah</th>
                      <th class="p-2 border">Deskripsi</th>
                      <th class="p-2 border">Resep</th>
                      <th class="p-2 border">Jumlah</th>
                      <th class="p-2 border">Deskripsi</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr
                      v-for="(d, dIndex) in trx.detail"
                      :key="dIndex"
                      class="hover:bg-gray-50/70 transition"
                    >
                      <td class="p-2 border">{{ trx.tindakan }}</td>
                      <td class="p-2 border text-center">{{ trx.jmlh }}</td>
                      <td class="p-2 border">{{ trx.dskrps }}</td>
                      <td class="p-2 border">{{ d.resep }}</td>
                      <td class="p-2 border text-center">{{ d.jumlah }}</td>
                      <td class="p-2 border">{{ d.deskripsi }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
