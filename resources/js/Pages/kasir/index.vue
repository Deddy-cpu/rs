<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue"
import { Head } from "@inertiajs/vue3"
import { router } from "@inertiajs/vue3"

const props = defineProps({
  pasien: Array, // [{id, nama_pasien, ... transaksi: [{... detail: []}]}]
})

function AddTransaksi(p) {
  const id = p?.id
  if (!id) {
    console.error("Pasien id tidak ditemukan", p)
    return
  }
  router.visit(`/kasir/${id}/create`)
}

function showDetail(p) {
  alert(`Detail pasien: ${p.nama_pasien}`)
}


function
totalBiaya(trx) {
  const detailSum = trx.detail.reduce((sum, d) => sum + (Number(d.biaya) || 0), 0)
  return (Number(trx.bya) || 0) + detailSum
}

function deleteTransaksi(trx) {
  if (!confirm("Yakin ingin menghapus transaksi ini?")) return

  router.delete(`/kasir/${trx.id}`, {
    onSuccess: () => {
      alert("Transaksi berhasil dihapus!")
    },
    onError: (err) => {
      console.error("Gagal hapus transaksi", err)
    },
  })
}



</script>

<template>
  <AuthenticatedLayout>
    <Head title="Daftar Pasien & Transaksi" />

    <div class="max-w-6xl mx-auto py-8">
      <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold flex items-center gap-2">
          📝 Daftar Pasien & Transaksi
        </h2>
        <a
          href="/pasien/create"
          class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
        >
          ➕ Tambah Data Pasien
        </a>
      </div>

      <!-- Loop pasien -->
      <div
        v-for="(p, idx) in pasien"
        :key="idx"
        class="bg-white shadow rounded-lg border mb-6 p-5"
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
      <div class="flex gap-2">
            <button
              @click="AddTransaksi(p)"
              class="px-3 py-1.5 text-sm bg-green-600 hover:bg-green-700 text-white rounded"
            >
              ➕ Tambah Transaksi
            </button>
            <button
              @click="showDetail(p)"
              class="px-3 py-1.5 text-sm bg-blue-600 hover:bg-blue-700 text-white rounded"
            >
              🔍 Show
            </button>
          </div>
        </div>

        <!-- Loop transaksi -->
        <div v-for="(trx, tIndex) in p.transaksi" :key="tIndex" class="mb-4">

           <button
    @click="deleteTransaksi(trx)"
    class=" mb-2 px-3 py-1.5 text-sm bg-red-600 hover:bg-red-700 text-white rounded"
  >
    🗑️ Hapus
  </button>
          <h3 class="font-semibold mb-2">
            📅 {{ p.tanggal }} — {{ p.perawatan }} ({{ trx.dokter }})
          </h3>


          
          <!-- Loop detail transaksi -->
          <table class="w-full text-sm border border-gray-200">
            <thead class="bg-gray-100">
              <tr>
                <th class="p-2 border">Tindakan</th>
                <th class="p-2 border">Jumlah</th>
                <th class="p-2 border">Deskripsi</th>
                <th class="p-2 border">Biaya</th>
                <th class="p-2 border">Resep</th>
                <th class="p-2 border">Jumlah</th>
                <th class="p-2 border">Deskripsi</th>
                <th class="p-2 border">Biaya</th>
                <th class="p-2 border">SubTotal (Rp)</th>
                
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="(d, dIndex) in trx.detail"
                :key="dIndex"
                class="hover:bg-gray-50"
              >
                <td class="p-2 border">{{ trx.tindakan }}</td>
                <td class="p-2 border text-center">{{ trx.jmlh }}</td>
                <td class="p-2 border">{{ trx.dskrps }}</td>
                <td class="p-2 border">{{ trx.bya }} RP</td>
                <td class="p-2 border">{{ d.resep }}</td>
                <td class="p-2 border text-center">{{ d.jumlah }}</td>
                <td class="p-2 border">{{ d.deskripsi }}</td>
                <td class="p-2 border">{{ d.biaya }} RP</td>
                <td class="p-2 border">
                  {{
                    totalBiaya(trx)
                  }} RP
                </td>
               
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
