<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue"
import { Head, router } from "@inertiajs/vue3"
import { ref } from "vue"

const props = defineProps({
  pasien: Array, // [{id, nama_pasien, alamat, Penjamin, tanggal, transaksi_id}]
})

const showModal = ref(false)
const loadingDetail = ref(false)
const selected = ref(null)
const deletingId = ref(null)

async function showDetail(p) {
  if (!p?.id) return
  showModal.value = true
  loadingDetail.value = true
  try {
    const res = await fetch(`/kasir/${p.id}`)
    if (!res.ok) throw new Error('Gagal mengambil data')
    selected.value = await res.json()
  } catch (e) {
    console.error(e)
  } finally {
    loadingDetail.value = false
  }
}

function closeModal() {
  showModal.value = false
  selected.value = null
}

// Perubahan utama: gunakan transaksi_id untuk delete, bukan p.id
function deletePasien(transaksiId) {
  if (!transaksiId) return
  if (confirm("Yakin ingin menghapus data pasien ini?")) {
    deletingId.value = transaksiId
    router.delete(route('kasir.destroy', transaksiId), {
      onFinish: () => {
        deletingId.value = null
      }
    })
  }
}
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Daftar Pasien & Transaksi" />

    <div class="max-w-6xl mx-auto py-8">
      <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold flex items-center gap-2">
          📝 Daftar Pasien
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
        :key="p.id"
        class="bg-white shadow rounded-lg border mb-6 p-5"
      >
        <div class="flex justify-end mb-3 gap-2">
          <a
            :href="route('kasir.edit', p.transaksi_id ?? p.id)"
            class="px-3 py-1.5 text-sm bg-yellow-600 hover:bg-yellow-700 text-white rounded"
          >
            ✏️ Edit Data Pasien
          </a>
          <button
            type="button"
            class="px-3 py-1.5 text-sm bg-red-600 hover:bg-red-700 text-white rounded"
            :disabled="deletingId === (p.transaksi_id ?? p.id)"
            @click="deletePasien(p.transaksi_id ?? p.id)"
          >
            <span v-if="deletingId === (p.transaksi_id ?? p.id)">Menghapus...</span>
            <span v-else>🗑️ Hapus</span>
          </button>
        </div>
     
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
            <a :href="route('kasir.show', p.id)" class="px-3 py-1.5 text-sm bg-blue-600 hover:bg-blue-700 text-white rounded">
              🔍 Show
            </a>
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

    <!-- Modal Show Detail (removed; using dedicated show page) -->
  </AuthenticatedLayout>
</template>
