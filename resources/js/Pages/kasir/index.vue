<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue"
import { Head, router } from "@inertiajs/vue3"
import { ref } from "vue"
import DeletePasienModal from "@/Components/DeletePasienModal.vue"
import DeleteTransaksiModal from "@/Components/DeleteTransaksiModal.vue"

const props = defineProps({
  pasien: Array, // [{id, nama_pasien, alamat, Penjamin, tanggal, transaksi_id}]
})

const showModal = ref(false)
const loadingDetail = ref(false)
const selected = ref(null)
const deletingId = ref(null)

// Modal states
const showDeletePasienModal = ref(false)
const showDeleteTransaksiModal = ref(false)
const pasienToDelete = ref(null)
const transaksiToDelete = ref(null)
const isDeleting = ref(false)

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

// Fungsi untuk menampilkan modal konfirmasi hapus pasien
function confirmDeletePasien(pasien) {
  pasienToDelete.value = pasien
  showDeletePasienModal.value = true
}

// Fungsi untuk menampilkan modal konfirmasi hapus transaksi
function confirmDeleteTransaksi(transaksi) {
  transaksiToDelete.value = transaksi
  showDeleteTransaksiModal.value = true
}

// Fungsi untuk menghapus pasien setelah konfirmasi
function deletePasien() {
  if (!pasienToDelete.value) return
  
  const transaksiId = pasienToDelete.value.transaksi_id ?? pasienToDelete.value.id
  isDeleting.value = true
  
  router.delete(route('kasir.destroy', transaksiId), {
    onFinish: () => {
      isDeleting.value = false
      showDeletePasienModal.value = false
      pasienToDelete.value = null
    },
    onError: () => {
      isDeleting.value = false
    }
  })
}

// Fungsi untuk menghapus transaksi setelah konfirmasi
function deleteTransaksi() {
  if (!transaksiToDelete.value) return
  
  isDeleting.value = true
  
  router.delete(route('kasir.destroy', transaksiToDelete.value.id), {
    onFinish: () => {
      isDeleting.value = false
      showDeleteTransaksiModal.value = false
      transaksiToDelete.value = null
      // Refresh halaman atau update data
      window.location.reload()
    },
    onError: () => {
      isDeleting.value = false
    }
  })
}

// Fungsi untuk membatalkan hapus
function cancelDeletePasien() {
  showDeletePasienModal.value = false
  pasienToDelete.value = null
}

function cancelDeleteTransaksi() {
  showDeleteTransaksiModal.value = false
  transaksiToDelete.value = null
}

// Fungsi untuk menghitung total biaya
function totalBiaya(trx) {
  if (!trx || !trx.detail) return 0
  
  let total = 0
  
  // Hitung biaya tindakan
  if (trx.bya && trx.jmlh) {
    total += parseFloat(trx.bya) * parseInt(trx.jmlh)
  }
  
  // Hitung biaya resep dari detail
  if (trx.detail && Array.isArray(trx.detail)) {
    trx.detail.forEach(d => {
      if (d.biaya && d.jumlah) {
        total += parseFloat(d.biaya) * parseInt(d.jumlah)
      }
    })
  }
  
  return total.toLocaleString('id-ID')
}

</script>

<template>
  <AuthenticatedLayout>
    <Head title="Daftar Pasien & Transaksi" />

    <div class="max-w-6xl mx-auto py-8">
      <div class="flex justify-between items-center mb-8">
        <h2 class="text-3xl font-extrabold flex items-center gap-2 text-blue-800 drop-shadow">
          📝 Daftar Pasien
        </h2>
        <a
          href="/kasir/create"
          class="px-5 py-2 bg-gradient-to-r from-blue-600 to-blue-500 text-white rounded-lg shadow hover:from-blue-700 hover:to-blue-600 transition"
        >
          ➕ Tambah Data Pasien
        </a>
      </div>

      <!-- Loop pasien -->
      <div
        v-for="(p, idx) in pasien"
        :key="p.id"
        class="bg-white/80 shadow-xl rounded-2xl border border-gray-200 mb-10 p-7 transition hover:shadow-2xl"
      >
        <div class="flex justify-between items-center mb-5">
          <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-full bg-blue-100 flex items-center justify-center text-3xl text-blue-700 shadow">
              👤
            </div>
            <div>
              <p class="text-xl font-bold text-gray-800">{{ p.nama_pasien }}</p>
              <p class="text-sm text-gray-500 mt-1">
                <span class="font-semibold">Alamat:</span> {{ p.alamat }}<br>
                <span class="font-semibold">Penjamin:</span> {{ p.Penjamin }}
              </p>
            </div>
          </div>
          <div class="flex gap-2">
            <a :href="route('kasir.show', p.id)" class="px-4 py-2 text-sm bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow transition">
              🔍 Detail
            </a>
            <a
              :href="route('kasir.edit', p.transaksi_id ?? p.id)"
              class="px-4 py-2 text-sm bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg shadow transition"
            >
              ✏️ Edit
            </a>
            <button
              type="button"
              class="px-4 py-2 text-sm bg-red-600 hover:bg-red-700 text-white rounded-lg shadow transition-all duration-200 hover:scale-105 hover:shadow-lg"
              @click="confirmDeletePasien(p)"
            >
              🗑️ Hapus Pasien
            </button>
          </div>
        </div>

        <div class="border-t border-gray-200 pt-5">
          <div v-if="!p.transaksi || p.transaksi.length === 0" class="text-gray-400 italic text-center py-6">
            Tidak ada transaksi untuk pasien ini.
          </div>
          <!-- Loop transaksi -->
          <div
            v-for="(trx, tIndex) in p.transaksi"
            :key="tIndex"
            class="mb-8 bg-gray-50 rounded-xl shadow-inner p-5 border border-gray-100"
          >
            <div class="flex justify-between items-center mb-3">
              <h3 class="font-semibold text-lg text-blue-700 flex items-center gap-2">
                <span>📅</span>
                <span>{{ p.tanggal }}</span>
                <span class="text-gray-500">|</span>
                <span>{{ p.perawatan }}</span>
                <span class="text-gray-500">|</span>
                <span class="italic text-gray-600">Dokter: {{ trx.dokter }}</span>
              </h3>
              <button
                @click="confirmDeleteTransaksi(trx)"
                class="px-3 py-1.5 text-sm bg-red-500 hover:bg-red-600 text-white rounded shadow transition-all duration-200 hover:scale-105 hover:shadow-lg"
              >
                🗑️ Hapus Transaksi
              </button>
            </div>

            <!-- Loop detail transaksi -->
            <div class="overflow-x-auto">
              <table class="w-full text-sm border border-gray-200 rounded-xl overflow-hidden shadow">
                <thead class="bg-gradient-to-r from-blue-100 to-blue-200">
                  <tr>
                    <th class="p-2 border text-blue-800 font-semibold">Tindakan</th>
                    <th class="p-2 border text-blue-800 font-semibold">Jumlah</th>
                    <th class="p-2 border text-blue-800 font-semibold">Deskripsi</th>
                    <th class="p-2 border text-blue-800 font-semibold">Biaya</th>
                    <th class="p-2 border text-blue-800 font-semibold">Resep</th>
                    <th class="p-2 border text-blue-800 font-semibold">Jumlah</th>
                    <th class="p-2 border text-blue-800 font-semibold">Deskripsi</th>
                    <th class="p-2 border text-blue-800 font-semibold">Biaya</th>
                    <th class="p-2 border text-blue-800 font-semibold">SubTotal (Rp)</th>
                  </tr>
                </thead>
                <tbody>
                  <tr
                    v-for="(d, dIndex) in trx.detail"
                    :key="dIndex"
                    class="hover:bg-blue-50 transition"
                  >
                    <td class="p-2 border text-gray-700">{{ trx.tindakan }}</td>
                    <td class="p-2 border text-center text-gray-700">{{ trx.jmlh }}</td>
                    <td class="p-2 border text-gray-700">{{ trx.dskrps }}</td>
                    <td class="p-2 border text-right text-gray-700">{{ trx.bya }} RP</td>
                    <td class="p-2 border text-gray-700">{{ d.resep }}</td>
                    <td class="p-2 border text-center text-gray-700">{{ d.jumlah }}</td>
                    <td class="p-2 border text-gray-700">{{ d.deskripsi }}</td>
                    <td class="p-2 border text-right text-gray-700">{{ d.biaya }} RP</td>
                    <td class="p-2 border text-right font-bold text-blue-700">
                      {{ totalBiaya(trx) }} RP
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Konfirmasi Hapus Pasien -->
    <DeletePasienModal
      :show="showDeletePasienModal"
      :pasien-name="pasienToDelete?.nama_pasien || ''"
      :loading="isDeleting"
      @confirm="deletePasien"
      @cancel="cancelDeletePasien"
    />

    <!-- Modal Konfirmasi Hapus Transaksi -->
    <DeleteTransaksiModal
      :show="showDeleteTransaksiModal"
      :loading="isDeleting"
      @confirm="deleteTransaksi"
      @cancel="cancelDeleteTransaksi"
    />
  </AuthenticatedLayout>
</template>
