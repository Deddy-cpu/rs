<script setup>
import { Head, router } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
  psns: {
    type: Array,
    default: () => []
  }
})

// UI-only search (tidak mengubah fitur backend)
const search = ref('')
const filteredPsns = computed(() => {
  const q = search.value.trim().toLowerCase()
  if (!q) return props.psns
  return props.psns.filter((p) => {
    const fields = [
      p?.nm_p,
      String(p?.nik ?? ''),
      String(p?.no_bpjs ?? ''),
      p?.agm,
      p?.tgl_lahir,
      p?.kelamin,
      p?.almt_L,
      p?.almt_B,
    ]
    return fields.some((v) => String(v ?? '').toLowerCase().includes(q))
  })
})
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Data Pasien" />
    <div class="container mx-auto p-4">
      <div class="mb-5">
        <div class="flex items-start justify-between gap-3">
          <div>
            <h1 class="text-2xl font-semibold tracking-tight text-gray-900">Daftar Pasien (PSN)</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola data pasien, lihat detail dan lakukan pengubahan.</p>
          </div>
          <div class="flex items-center gap-2 w-full sm:w-auto sm:justify-end">
            <div class="flex items-center gap-2 w-full sm:w-80">
              <input
                v-model="search"
                type="text"
                placeholder="Cari nama, NIK, BPJS, agama, alamat..."
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              />
              <button
                v-if="search"
                @click="search = ''"
                type="button"
                class="px-3 py-2 text-sm border rounded-lg hover:bg-gray-50"
              >
                Reset
              </button>
            </div>
            <button 
              class="inline-flex items-center gap-2 px-4 py-2 text-sm bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition" 
              @click="router.visit('/pasien/create')"
            >
              <span class="text-base">➕</span>
              <span>Tambah Data Pasien</span>
            </button>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
          <table class="min-w-full text-left align-middle">
            <thead class="bg-gray-50/90 backdrop-blur supports-[backdrop-filter]:bg-gray-50/70 text-gray-700 text-xs uppercase tracking-wider sticky top-0 z-10 border-b border-gray-200">
              <tr>
                <th class="py-3.5 px-4 font-semibold">#</th>
                <th class="py-3.5 px-4 font-semibold">Nama Pasien</th>
                <th class="py-3.5 px-4 font-semibold">NIK</th>
                <th class="py-3.5 px-4 font-semibold">No BPJS</th>
                <th class="py-3.5 px-4 font-semibold">Agama</th>
                <th class="py-3.5 px-4 font-semibold">Tanggal Lahir</th>
                <th class="py-3.5 px-4 font-semibold">Kelamin</th>
                <th class="py-3.5 px-4 font-semibold">Alamat Lahir</th>
                <th class="py-3.5 px-4 font-semibold">Alamat Berdomisili</th>
                <th class="py-3.5 px-4 font-semibold">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              <tr
                v-for="(psn, index) in filteredPsns"
                :key="psn.id"
                class="hover:bg-gray-50 transition-colors odd:bg-white even:bg-gray-50/40"
              >
                <td class="py-3 px-4 text-gray-700 align-top">{{ index + 1 }}</td>
                <td class="py-3 px-4 align-top">
                  <div class="font-medium text-gray-900 max-w-[16rem] truncate" :title="psn.nm_p">{{ psn.nm_p }}</div>
                </td>
                <td class="py-3 px-4 text-gray-700 align-top"><span class="max-w-[12rem] block truncate" :title="String(psn.nik)">{{ psn.nik }}</span></td>
                <td class="py-3 px-4 text-gray-700 align-top"><span class="max-w-[12rem] block truncate" :title="String(psn.no_bpjs)">{{ psn.no_bpjs }}</span></td>
                <td class="py-3 px-4 text-gray-700 align-top">
                  <span class="inline-flex items-center rounded-full bg-gray-100 text-gray-700 px-2 py-0.5 text-xs font-medium border border-gray-200">{{ psn.agm }}</span>
                </td>
                <td class="py-3 px-4 text-gray-700 align-top">
                  <span class="whitespace-nowrap">{{ psn.tgl_lahir }}</span>
                </td>
                <td class="py-3 px-4 text-gray-700 align-top">
                  <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium border"
                        :class="psn.kelamin === 'L' ? 'bg-blue-50 text-blue-700 border-blue-200' : 'bg-pink-50 text-pink-700 border-pink-200'">
                    {{ psn.kelamin }}
                  </span>
                </td>
                <td class="py-3 px-4 text-gray-700 align-top"><span class="max-w-[16rem] block truncate" :title="psn.almt_L">{{ psn.almt_L }}</span></td>
                <td class="py-3 px-4 text-gray-700 align-top"><span class="max-w-[16rem] block truncate" :title="psn.almt_B">{{ psn.almt_B }}</span></td>
                <td class="py-3 px-4">
                  <div class="flex items-center gap-2">
                    <button 
                      @click="router.visit(`/pasien/${psn.id}`)"
                      class="inline-flex items-center gap-1 rounded-md px-3 py-1.5 text-sm text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-200 transition"
                    >
                      <span>🔎</span>
                      <span>Detail</span>
                    </button>
                    <button 
                      @click="router.visit(`/pasien/${psn.id}/edit`)"
                      class="inline-flex items-center gap-1 rounded-md px-3 py-1.5 text-sm text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 transition"
                    >
                      <span>✏️</span>
                      <span>Edit</span>
                    </button>
                  </div>
                </td>
              </tr>
              <tr v-if="filteredPsns.length === 0">
                <td colspan="10" class="py-14">
                  <div class="flex flex-col items-center justify-center gap-2 text-center">
                    <div class="text-4xl">🗂️</div>
                    <div class="text-gray-900 font-medium">Belum ada data pasien</div>
                    <div class="text-sm text-gray-500">Tambahkan data baru atau ubah kata kunci pencarian.</div>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<style scoped>
.container {
  max-width: 1000px;
}
</style>
