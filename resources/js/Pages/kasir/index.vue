<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue"
import { Head, router } from "@inertiajs/vue3"
import { ref, computed } from "vue"
import DeletePasienModal from "@/Components/DeletePasienModal.vue"
import DeleteTransaksiModal from "@/Components/DeleteTransaksiModal.vue"

const props = defineProps({
  pasien: Object,   // hasil paginate, bukan array biasa
  filters: Object,  // untuk simpan search query
  flash: {
    type: Object,
    default: () => ({})
  }
})

const showDeletePasienModal = ref(false)
const showDeleteTransaksiModal = ref(false)
const pasienToDelete = ref(null)
const transaksiToDelete = ref(null)
const isDeleting = ref(false)

// Search
const search = ref(props.filters?.search || "")

function performSearch() {
  router.get(route("kasir.index"), { search: search.value }, { preserveState: true, replace: true })
}

function confirmDeletePasien(pasien) {
  pasienToDelete.value = pasien
  showDeletePasienModal.value = true
}

function confirmDeleteTransaksi(transaksi) {
  transaksiToDelete.value = transaksi
  showDeleteTransaksiModal.value = true
}

function deletePasien() {
  if (!pasienToDelete.value) return
  const transaksiId = pasienToDelete.value.transaksi_id ?? pasienToDelete.value.id
  isDeleting.value = true
  router.delete(route("kasir.destroy", transaksiId), {
    onFinish: () => {
      isDeleting.value = false
      showDeletePasienModal.value = false
      pasienToDelete.value = null
    },
  })
}

function deleteTransaksi() {
  if (!transaksiToDelete.value) return
  isDeleting.value = true
  router.delete(route("kasir.destroy", transaksiToDelete.value.id), {
    onFinish: () => {
      isDeleting.value = false
      showDeleteTransaksiModal.value = false
      transaksiToDelete.value = null
      window.location.reload()
    },
  })
}

function cancelDeletePasien() {
  showDeletePasienModal.value = false
  pasienToDelete.value = null
}

function cancelDeleteTransaksi() {
  showDeleteTransaksiModal.value = false
  transaksiToDelete.value = null
}

function goToCreatePasien() {
  router.get("/kasir/create")   // <-- ini yang jalanin redirect
}

// Hitung total biaya
function totalBiaya(trx) {
  if (!trx || !trx.detail) return 0
  let total = 0
  if (trx.bya && trx.jmlh) {
    const tindakanBiaya = parseFloat(trx.bya.replace(/[^\d]/g, '')) || 0
    const tindakanJumlah = parseInt(trx.jmlh) || 0
    total += tindakanBiaya * tindakanJumlah
  }
  if (trx.detail && Array.isArray(trx.detail)) {
    trx.detail.forEach((d) => {
      if (d.biaya && d.jumlah) {
        const resepBiaya = parseFloat(d.biaya.replace(/[^\d]/g, '')) || 0
        const resepJumlah = parseInt(d.jumlah) || 0
        total += resepBiaya * resepJumlah
      }
    })
  }
  return total.toLocaleString("id-ID")
}

function formatDate(dateString) {
  const date = new Date(dateString)
  return date.toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Daftar Pasien & Transaksi" />

    <div
      class="min-h-screen bg-cover bg-center p-6"
      style="background-image: url('/images/bg-login.png')"
    >
      
        
        <!-- Header + Search -->
        <div class="mb-6">
          <h2 class="text-3xl font-extrabold text-red-700 tracking-wide flex items-center gap-2 justify-center text-center mb-4">
            📝 Daftar Pasien
          </h2>

          <div class="flex justify-between items-center">
            <button
              @click="goToCreatePasien"
              class="px-5 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-medium shadow"
            >
              + Tambah Pasien 
            </button>

            <div class="flex items-center space-x-2">
              <input
                v-model="search"
                type="text"
                placeholder="Cari resep..."
                class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
              />
              <button
                @click="performSearch"
                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition"
              >
                🔍 Cari
              </button>
            </div>
          </div>
        </div>

        <!-- 🔹 Empty State kalau tidak ada pasien -->
        <div
          v-if="!props.pasien?.data?.length"
          class="flex flex-col items-center justify-center py-20 text-gray-500"
        >
          <span class="text-5xl mb-4">📭</span>
          <p class="text-lg font-medium">Belum ada data pasien.</p>
          <a
            href="/kasir/create"
            class="mt-4 px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition"
          >
            ➕ Tambah Pasien Pertama
          </a>
        </div>

        <!-- Loop Pasien -->
        <div
          v-else
          v-for="(p, idx) in props.pasien.data"
          :key="p.id"
          class="bg-white/80 shadow-lg rounded-xl border border-gray-200 mb-8 p-6 hover:shadow-2xl transition"
        >
          <div class="flex justify-between items-center mb-5">
            <div class="flex items-center gap-4">
              <div
                class="w-14 h-14 rounded-full bg-blue-100 flex items-center justify-center text-3xl text-blue-700 shadow"
              >
                👤
              </div>
              <div>
                <p class="text-xl font-bold text-gray-800">{{ p.nama_pasien }}</p>
                <p class="text-sm text-gray-500 mt-1">
                  <span class="font-semibold">Alamat:</span> {{ p.alamat }}<br />
                  <span class="font-semibold">Penjamin:</span> {{ p.Penjamin }}
                </p>
              </div>
            </div>
            <div class="flex gap-2">
              <a
                :href="route('kasir.show', p.id)"
                class="px-4 py-2 text-sm bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow transition"
              >
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
                class="px-4 py-2 text-sm bg-red-600 hover:bg-red-700 text-white rounded-lg shadow transition"
                @click="confirmDeletePasien(p)"
              >
                🗑️ Hapus
              </button>
            </div>
          </div>

          <!-- Transaksi -->
          <div class="border-t border-gray-200 pt-4">
            <div
              v-if="!p.transaksi || p.transaksi.length === 0"
              class="text-gray-400 italic text-center py-6"
            >
              Tidak ada transaksi untuk pasien ini.
            </div>

            <div
              v-for="(trx, tIndex) in p.transaksi"
              :key="tIndex"
              class="mb-6 bg-gray-50 rounded-xl shadow-inner p-5 border border-gray-100"
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
                  class="px-3 py-1.5 text-sm bg-red-500 hover:bg-red-600 text-white rounded shadow transition"
                >
                  🗑️ Hapus Transaksi
                </button>
              </div>

              <!-- Detail -->
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
                    <tr class="bg-white">
                      <td class="p-2 border text-center text-gray-700">{{ trx.tindakan }}</td>
                      <td class="p-2 border text-center text-gray-700">{{ trx.jmlh }}</td>
                      <td class="p-2 border text-gray-700">{{ trx.deskripsi }}</td>
                      <td class="p-2 border text-right text-gray-700">{{ trx.bya }} RP</td>
                      <td class="p-2 border text-center text-gray-700" v-if="trx.detail && trx.detail.length > 0">
                        {{ trx.detail[0].resep }}
                      </td>
                      <td class="p-2 border text-center text-gray-700" v-else>-</td>
                      <td class="p-2 border text-center text-gray-700" v-if="trx.detail && trx.detail.length > 0">
                        {{ trx.detail[0].jumlah }}
                      </td>
                      <td class="p-2 border text-center text-gray-700" v-else>-</td>
                      <td class="p-2 border text-gray-700" v-if="trx.detail && trx.detail.length > 0">
                        {{ trx.detail[0].deskripsi }}
                      </td>
                      <td class="p-2 border text-gray-700" v-else>-</td>
                      <td class="p-2 border text-right text-gray-700" v-if="trx.detail && trx.detail.length > 0">
                        {{ trx.detail[0].biaya }} RP
                      </td>
                      <td class="p-2 border text-right text-gray-700" v-else>-</td>
                      <td class="p-2 border text-right font-bold text-blue-700 bg-blue-50">
                        {{ totalBiaya(trx) }} RP
                      </td>
                    </tr>
                    <tr v-for="(d, dIndex) in trx.detail" :key="dIndex" v-if="dIndex > 0" class="bg-gray-50">
                      <td class="p-2 border text-center text-gray-500">-</td>
                      <td class="p-2 border text-center text-gray-500">-</td>
                      <td class="p-2 border text-gray-500">-</td>
                      <td class="p-2 border text-right text-gray-500">-</td>
                      <td class="p-2 border text-center text-gray-700">{{ d.resep }}</td>
                      <td class="p-2 border text-center text-gray-700">{{ d.jumlah }}</td>
                      <td class="p-2 border text-gray-700">{{ d.deskripsi }}</td>
                      <td class="p-2 border text-right text-gray-700">{{ d.biaya }} RP</td>
                      <td class="p-2 border text-right font-bold text-blue-700 bg-blue-50">
                        {{ totalBiaya(trx) }} RP
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        <!-- Pagination -->
        <div v-if="props.pasien?.data?.length" class="flex justify-start mt-4">
          <div class="flex space-x-2">
            <template v-for="link in props.pasien.links" :key="link.label">
              <button
                v-if="link.url"
                @click="router.get(link.url)"
                :class="[
                  'px-3 py-2 text-sm rounded-lg transition',
                  link.active
                    ? 'bg-red-600 text-white shadow-lg'
                    : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-300'
                ]"
                v-html="link.label"
              ></button>
              <span
                v-else
                class="px-3 py-2 text-sm text-gray-400 bg-gray-100 rounded-lg"
                v-html="link.label"
              ></span>
            </template>
          </div>
        </div>
    </div>

    <!-- Modals -->
    <DeletePasienModal
      :show="showDeletePasienModal"
      :pasien="pasienToDelete"
      :is-deleting="isDeleting"
      @close="cancelDeletePasien"
      @confirm="deletePasien"
    />

    <DeleteTransaksiModal
      :show="showDeleteTransaksiModal"
      :transaksi="transaksiToDelete"
      :is-deleting="isDeleting"
      @close="cancelDeleteTransaksi"
      @confirm="deleteTransaksi"
    />
  </AuthenticatedLayout>
</template>