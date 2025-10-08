<script setup>
import { ref, watch, onMounted } from 'vue';
import { router, Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
  psn: {
    type: Object,
    required: true
  }
});

const original = ref({});
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

function hydrateFromProps() {
  const p = props.psn || {};
  const snapshot = {
    nm_p: p.nm_p ?? '',
    nik: String(p.nik ?? ''),
    no_bpjs: String(p.no_bpjs ?? ''),
    agm: p.agm ?? '',
    tgl_lahir: p.tgl_lahir ?? '',
    kelamin: p.kelamin ?? '',
    almt_L: p.almt_L ?? '',
    almt_B: p.almt_B ?? '',
  };
  original.value = { ...snapshot };
  form.value = { ...snapshot };
}

onMounted(hydrateFromProps);
watch(() => props.psn, hydrateFromProps, { deep: true });

function resetForm() {
  form.value = { ...original.value };
  errors.value = {};
}

async function submitForm() {
  loading.value = true;
  errors.value = {};
  try {
    const response = await fetch(`/api/psn/${props.psn.id}` , {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: JSON.stringify(form.value),
    });

    if (response.ok) {
      router.visit('/pasien');
    } else if (response.status === 422) {
      const data = await response.json();
      errors.value = data.errors || {};
    } else {
      const data = await response.json().catch(() => ({}));
      alert(data.message || 'Terjadi kesalahan saat memperbarui data.');
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
    <Head title="Edit Data Pasien" />

<div class="min-h-screen bg-cover bg-center flex items-center justify-center py-12" style="background-image: url('/images/bg-login.png')">
      <!-- Card Form Semi-transparan dengan Blur -->
      <div class="bg-white/60 backdrop-blur-md rounded-xl shadow-lg max-w-xl w-full p-6">
        
        <!-- Header -->
        <div class="mb-4 text-center">
          <h1 class="text-2xl font-bold text-blue-700 flex items-center justify-center gap-2 mb-1">
            ✏️ Edit Data Pasien
          </h1>
          <p class="text-gray-600 text-sm">Perbarui informasi pasien berikut.</p>
        </div>

        <!-- Form -->
        <form @submit.prevent="submitForm" class="space-y-4 text-sm">
          <div>
            <label class="block font-medium mb-1">Nama Pasien</label>
            <input
              v-model="form.nm_p"
              type="text"
              class="w-full border rounded px-3 py-2 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              :class="{'border-red-500': errors.nm_p}"
            />
            <div v-if="errors.nm_p" class="text-red-500 text-xs mt-1">{{ errors.nm_p[0] }}</div>
          </div>

          <div>
            <label class="block font-medium mb-1">NIK</label>
            <input
              v-model="form.nik"
              type="number"
              class="w-full border rounded px-3 py-2 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              :class="{'border-red-500': errors.nik}"
            />
            <div v-if="errors.nik" class="text-red-500 text-xs mt-1">{{ errors.nik[0] }}</div>
          </div>

          <div>
            <label class="block font-medium mb-1">No BPJS</label>
            <input
              v-model="form.no_bpjs"
              type="number"
              class="w-full border rounded px-3 py-2 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              :class="{'border-red-500': errors.no_bpjs}"
            />
            <div v-if="errors.no_bpjs" class="text-red-500 text-xs mt-1">{{ errors.no_bpjs[0] }}</div>
          </div>

          <div>
            <label class="block font-medium mb-1">Agama</label>
            <select
              v-model="form.agm"
              class="w-full border rounded px-3 py-2 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              :class="{'border-red-500': errors.agm}"
            >
              <option value="">Pilih Agama</option>
              <option value="islam">Islam</option>
              <option value="kristen">Kristen</option>
              <option value="katolik">Katolik</option>
              <option value="hindu">Hindu</option>
              <option value="buddha">Buddha</option>
              <option value="dll">Lainnya</option>
            </select>
            <div v-if="errors.agm" class="text-red-500 text-xs mt-1">{{ errors.agm[0] }}</div>
          </div>

          <div>
            <label class="block font-medium mb-1">Tanggal Lahir</label>
            <input
              v-model="form.tgl_lahir"
              type="date"
              class="w-full border rounded px-3 py-2 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              :class="{'border-red-500': errors.tgl_lahir}"
            />
            <div v-if="errors.tgl_lahir" class="text-red-500 text-xs mt-1">{{ errors.tgl_lahir[0] }}</div>
          </div>

          <div>
            <label class="block font-medium mb-1">Kelamin</label>
            <select
              v-model="form.kelamin"
              class="w-full border rounded px-3 py-2 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              :class="{'border-red-500': errors.kelamin}"
            >
              <option value="">Pilih Kelamin</option>
              <option value="L">Laki-laki</option>
              <option value="P">Perempuan</option>
              <option value="kosong">Tidak Diketahui</option>
            </select>
            <div v-if="errors.kelamin" class="text-red-500 text-xs mt-1">{{ errors.kelamin[0] }}</div>
          </div>

          <div>
            <label class="block font-medium mb-1">Alamat Lama</label>
            <input
              v-model="form.almt_L"
              type="text"
              class="w-full border rounded px-3 py-2 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              :class="{'border-red-500': errors.almt_L}"
            />
            <div v-if="errors.almt_L" class="text-red-500 text-xs mt-1">{{ errors.almt_L[0] }}</div>
          </div>

          <div>
            <label class="block font-medium mb-1">Alamat Baru</label>
            <input
              v-model="form.almt_B"
              type="text"
              class="w-full border rounded px-3 py-2 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              :class="{'border-red-500': errors.almt_B}"
            />
            <div v-if="errors.almt_B" class="text-red-500 text-xs mt-1">{{ errors.almt_B[0] }}</div>
          </div>

          <!-- Tombol -->
          <div class="flex gap-2 mt-4">
            <button
              type="submit"
              :disabled="loading"
              class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow transition disabled:opacity-50 text-sm"
            >
              {{ loading ? 'Menyimpan...' : 'Simpan Perubahan' }}
            </button>
            <button
              type="button"
              @click="resetForm"
              class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded transition text-sm"
            >
              Reset
            </button>
            <button
              type="button"
              @click="router.visit('/pasien')"
              class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded transition text-sm"
            >
              Batal
            </button>
          </div>
        </form>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
