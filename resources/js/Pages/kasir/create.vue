<template>
  <AuthenticatedLayout>
    <Head title="Tambah Transaksi / Pasien" />

    <div class="max-w-4xl mx-auto py-8">
      <h2 class="text-2xl font-bold mb-6">➕ Tambah Pasien & Transaksi</h2>

      <!-- ====== Form Patient (opsional: jika halaman untuk pasien tertentu, props.pasien diisi) ====== -->
      <div class="bg-white shadow rounded-lg p-5 mb-6">
        <h3 class="font-semibold mb-4">Data Pasien</h3>

        <!-- Jika props.pasien tersedia, tampilkan nama & gunakan pasien_id otomatis -->
        <div v-if="propsPasien">
          <p class="mb-2"><strong>Pasien:</strong> {{ propsPasien.nama_pasien }}</p>
          <input type="hidden" v-model="form.pasien_id" />
        </div>

        <!-- Jika tidak ada pasien preset, tampilkan input untuk pilih atau buat baru -->
        <div v-else>
          <label class="block text-sm font-medium">Pilih Pasien (atau kosongkan untuk buat baru)</label>
          <select v-model="form.pasien_id" class="w-full border p-2 rounded mb-3">
            <option value="">-- Buat Pasien Baru / Pilih --</option>
            <option v-for="p in pasienList" :key="p.id" :value="p.id">
              {{ p.nama_pasien }} — {{ p.alamat || '-' }}
            </option>
          </select>

          <div class="mb-3">
            <label class="block text-sm font-medium">Nama Pasien (jika membuat baru)</label>
            <input v-model="form.nama_pasien" class="w-full border p-2 rounded" />
          </div>

          <div class="grid grid-cols-2 gap-3 mb-3">
            <div>
              <label class="block text-sm font-medium">Alamat</label>
              <input v-model="form.alamat" class="w-full border p-2 rounded" />
            </div>
            <div>
              <label class="block text-sm font-medium">Perawatan</label>
              <input v-model="form.perawatan" class="w-full border p-2 rounded" />
            </div>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-sm font-medium">Penjamin</label>
              <input v-model="form.Penjamin" class="w-full border p-2 rounded" />
            </div>
            <div>
              <label class="block text-sm font-medium">Tanggal</label>
              <input type="date" v-model="form.tanggal" class="w-full border p-2 rounded" />
            </div>
          </div>
        </div>
      </div>

      <!-- ====== Form Transaksi (bisa multiple) ====== -->
      <div class="bg-white shadow rounded-lg p-5 mb-6">
        <h3 class="font-semibold mb-4">Transaksi (bisa tambah lebih dari 1)</h3>

        <div v-for="(trx, tIndex) in form.transaksi" :key="tIndex" class="border p-4 rounded mb-4 bg-gray-50">
          <div class="flex justify-between items-start mb-3">
            <h4 class="font-semibold">Transaksi {{ tIndex + 1 }}</h4>
            <div class="flex gap-2">
              <button
                v-if="form.transaksi.length > 1"
                @click="removeTransaksi(tIndex)"
                type="button"
                class="px-2 py-1 bg-red-600 text-white rounded"
              >
                Hapus Transaksi
              </button>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-sm">Dokter <span class="text-red-600">*</span></label>
              <input v-model="trx.dokter" type="text" class="w-full border p-2 rounded" placeholder="Nama dokter" />
              <p v-if="form.errors['dokter'] && tIndex === 0" class="text-red-600 text-sm mt-1">{{ form.errors.dokter }}</p>
            </div>

            <div>
              <label class="block text-sm">Tindakan <span class="text-red-600">*</span></label>
              <input v-model="trx.tindakan" type="text" class="w-full border p-2 rounded" placeholder="Nama tindakan" />
              <p v-if="form.errors['tindakan'] && tIndex === 0" class="text-red-600 text-sm mt-1">{{ form.errors.tindakan }}</p>
            </div>

            <div>
              <label class="block text-sm">Jumlah <span class="text-red-600">*</span></label>
              <input v-model.number="trx.jmlh" type="number" min="1" class="w-full border p-2 rounded" />
              <p v-if="form.errors['jmlh'] && tIndex === 0" class="text-red-600 text-sm mt-1">{{ form.errors.jmlh }}</p>
            </div>

            <div>
              <label class="block text-sm">Biaya (Rp) <span class="text-red-600">*</span></label>
              <input v-model.number="trx.bya" type="number" min="0" class="w-full border p-2 rounded" />
              <p v-if="form.errors['bya'] && tIndex === 0" class="text-red-600 text-sm mt-1">{{ form.errors.bya }}</p>
            </div>
          </div>

          <div class="mt-3">
            <label class="block text-sm">Deskripsi</label>
            <textarea v-model="trx.dskrps" class="w-full border p-2 rounded"></textarea>
          </div>

          <!-- detail resep (per transaksi) -->
          <div class="mt-4">
            <h5 class="font-medium mb-2">Detail Resep</h5>

            <div v-for="(d, dIndex) in trx.detail" :key="dIndex" class="grid grid-cols-5 gap-2 mb-2 items-center">
              <input v-model="d.resep" placeholder="Resep" class="border p-2 rounded" />
              <input v-model.number="d.jumlah" type="number" min="1" placeholder="Jumlah" class="border p-2 rounded" />
              <input v-model="d.deskripsi" placeholder="Deskripsi" class="border p-2 rounded" />
              <input v-model.number="d.biaya" type="number" min="0" placeholder="Biaya" class="border p-2 rounded" />
              <button v-if="trx.detail.length > 1" type="button" @click="removeDetail(tIndex, dIndex)" class="px-2 py-1 bg-red-500 text-white rounded">🗑</button>
            </div>

            <button type="button" @click="addDetail(tIndex)" class="px-3 py-1 bg-green-600 text-white rounded">➕ Tambah Detail</button>
          </div>
        </div>

        <div class="flex gap-3">
          <button type="button" @click="addTransaksi" class="px-4 py-2 bg-indigo-600 text-white rounded">➕ Tambah Transaksi</button>
        </div>
      </div>

      <!-- Submit -->
      <div class="text-right">
        <button @click="submit" class="px-6 py-2 bg-green-600 text-white rounded">💾 Simpan</button>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Head, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

// props: optionally pass pasien (object) when creating transaksi untuk pasien tertentu
// optionally pass pasienList (array) when you want a select list
const props = defineProps({
  pasien: Object,      // optional single pasien (page used under /pasien/{id}/transaksi/create)
  pasienList: { type: Array, default: () => [] } // optional list for select
})
const propsPasien = props.pasien ?? null

// useForm dari Inertia: akan otomatis handle errors -> form.errors
const form = useForm({
  // if page has an existing pasien, fill pasien_id
  pasien_id: propsPasien?.id ?? '',
  // patient create fields (only if user wants to create new patient inline)
  nama_pasien: '',
  alamat: '',
  perawatan: '',
  Penjamin: '',
  tanggal: '',

  // transactions array (support multiple)
  transaksi: [
    {
      dokter: '-',    // default value (not null)
      tindakan: '-',
      jmlh: 1,
      dskrps: '',
      bya: 0,
      detail: [
        { resep: '', jumlah: 1, deskripsi: '', biaya: 0 }
      ]
    }
  ]
})

// helper: akses errors (form.errors)
const errors = computed(() => form.errors)

// CRUD helpers
function addTransaksi() {
  form.transaksi.push({
    dokter: '-',
    tindakan: '-',
    jmlh: 1,
    dskrps: '',
    bya: 0,
    detail: [{ resep: '', jumlah: 1, deskripsi: '', biaya: 0 }]
  })
}
function removeTransaksi(i) {
  form.transaksi.splice(i, 1)
}
function addDetail(tIndex) {
  form.transaksi[tIndex].detail.push({ resep: '', jumlah: 1, deskripsi: '', biaya: 0 })
}
function removeDetail(tIndex, dIndex) {
  form.transaksi[tIndex].detail.splice(dIndex, 1)
}

// submit: send payload. For backward compatibility we also send dokter/tindakan/jmlh/bya from first transaction at top-level.
function submit() {
  // quick client-side check for pasien_id or nama_pasien
  if (!form.pasien_id && !form.nama_pasien) {
    alert('Harap pilih pasien atau isi data pasien baru.')
    return
  }

  // ensure transaksi[0] has required fields (front-end guard)
  const t0 = form.transaksi[0]
  if (!t0?.dokter || !t0?.tindakan) {
    alert('Isi minimal dokter dan tindakan pada transaksi pertama.')
    return
  }

  // prepare payload: use form directly, but also add top-level keys for compatibility
  const payload = {
    ...form,
    dokter: t0.dokter,
    tindakan: t0.tindakan,
    jmlh: t0.jmlh,
    bya: t0.bya
  }

  // post (adjust url if your route is different)
  form.post('/kasir', {
    data: payload,
    onError: (errs) => {
      // Inertia will populate form.errors automatically; optional console log
      console.log('Validation errors:', errs)
    },
    onSuccess: () => {
      alert('Sukses: data tersimpan.')
      // reset form or redirect as you need
    }
  })
}
</script>

<style scoped>
/* kecilkan style supaya rapi */
</style>
