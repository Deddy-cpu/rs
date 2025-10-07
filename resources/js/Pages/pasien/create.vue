<script setup>
import { ref } from 'vue';
import { router, Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const form = ref({
  nm_p: '',
  nik: '',
  no_bpjs: '',
  agm: '',
  tgl_lahir: '',
  kelamin: '',
  almt_L: '',
  almt_B: '',
});

const errors = ref({});
const loading = ref(false);

function resetForm() {
  form.value = {
    nm_p: '',
    nik: '',
    no_bpjs: '',
    agm: '',
    tgl_lahir: '',
    kelamin: '',
    almt_L: '',
    almt_B: '',
  };
  errors.value = {};
}

async function submitForm() {
  loading.value = true;
  errors.value = {};
  try {
    const response = await fetch('/api/psn', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: JSON.stringify(form.value),
    });

    if (response.status === 201) {
      // Success
      resetForm();
      router.visit('/pasien');
    } else if (response.status === 422) {
      // Validation error
      const data = await response.json();
      errors.value = data.errors || {};
    } else {
      // Other error
      const data = await response.json();
      alert(data.message || 'Terjadi kesalahan saat menyimpan data.');
    }
  } catch (e) {
    alert('Gagal menghubungi server.');
  } finally {
    loading.value = false;
  }
}
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Tambah Data Pasien" />
    <div class="container mx-auto max-w-xl p-6">
      <h1 class="text-2xl font-bold mb-6 text-blue-700">Tambah Data Pasien</h1>
      <form @submit.prevent="submitForm" class="bg-white rounded-lg shadow p-6 space-y-4">
        <div>
          <label class="block font-medium mb-1">Nama Pasien</label>
          <input v-model="form.nm_p" type="text" class="w-full border rounded px-3 py-2" :class="{'border-red-500': errors.nm_p}" />
          <div v-if="errors.nm_p" class="text-red-500 text-sm mt-1">{{ errors.nm_p[0] }}</div>
        </div>
        <div>
          <label class="block font-medium mb-1">NIK</label>
          <input v-model="form.nik" type="number" class="w-full border rounded px-3 py-2" :class="{'border-red-500': errors.nik}" />
          <div v-if="errors.nik" class="text-red-500 text-sm mt-1">{{ errors.nik[0] }}</div>
        </div>
        <div>
          <label class="block font-medium mb-1">No BPJS</label>
          <input v-model="form.no_bpjs" type="number" class="w-full border rounded px-3 py-2" :class="{'border-red-500': errors.no_bpjs}" />
          <div v-if="errors.no_bpjs" class="text-red-500 text-sm mt-1">{{ errors.no_bpjs[0] }}</div>
        </div>
        <div>
          <label class="block font-medium mb-1">Agama</label>
          <select v-model="form.agm" class="w-full border rounded px-3 py-2" :class="{'border-red-500': errors.agm}">
            <option value="">Pilih Agama</option>
            <option value="islam">Islam</option>
            <option value="kristen">Kristen</option>
            <option value="katolik">Katolik</option>
            <option value="hindu">Hindu</option>
            <option value="buddha">Buddha</option>
            <option value="dll">Lainnya</option>
          </select>
          <div v-if="errors.agm" class="text-red-500 text-sm mt-1">{{ errors.agm[0] }}</div>
        </div>
        <div>
          <label class="block font-medium mb-1">Tanggal Lahir</label>
          <input v-model="form.tgl_lahir" type="date" class="w-full border rounded px-3 py-2" :class="{'border-red-500': errors.tgl_lahir}" />
          <div v-if="errors.tgl_lahir" class="text-red-500 text-sm mt-1">{{ errors.tgl_lahir[0] }}</div>
        </div>
        <div>
          <label class="block font-medium mb-1">Kelamin</label>
          <select v-model="form.kelamin" class="w-full border rounded px-3 py-2" :class="{'border-red-500': errors.kelamin}">
            <option value="">Pilih Kelamin</option>
            <option value="L">Laki-laki</option>
            <option value="P">Perempuan</option>
            <option value="kosong">Tidak Diketahui</option>
          </select>
          <div v-if="errors.kelamin" class="text-red-500 text-sm mt-1">{{ errors.kelamin[0] }}</div>
        </div>
        <div>
          <label class="block font-medium mb-1">Alamat Lama</label>
          <input v-model="form.almt_L" type="text" class="w-full border rounded px-3 py-2" :class="{'border-red-500': errors.almt_L}" />
          <div v-if="errors.almt_L" class="text-red-500 text-sm mt-1">{{ errors.almt_L[0] }}</div>
        </div>
        <div>
          <label class="block font-medium mb-1">Alamat Baru</label>
          <input v-model="form.almt_B" type="text" class="w-full border rounded px-3 py-2" :class="{'border-red-500': errors.almt_B}" />
          <div v-if="errors.almt_B" class="text-red-500 text-sm mt-1">{{ errors.almt_B[0] }}</div>
        </div>
        <div class="flex gap-2 mt-6">
          <button type="submit" :disabled="loading" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded transition disabled:opacity-50">
            {{ loading ? 'Menyimpan...' : 'Simpan' }}
          </button>
          <button type="button" @click="router.visit('/pasien')" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-5 py-2 rounded transition">
            Batal
          </button>
        </div>
      </form>
    </div>
  </AuthenticatedLayout>
</template>
