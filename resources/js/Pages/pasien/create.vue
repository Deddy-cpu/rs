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
  no_telp: '',
  nama_ayah: '',
  nama_ibu: '',
});

// Function to format NIK input (only allow numbers)
function formatNikInput(event) {
  const value = event.target.value.replace(/\D/g, ''); // Remove non-digits
  form.value.nik = value;
}

// Function to format BPJS input (only allow numbers)
function formatBpjsInput(event) {
  const value = event.target.value.replace(/\D/g, ''); // Remove non-digits
  form.value.no_bpjs = value;
}

// Function to format telepon input (only allow numbers)
function formatTeleponInput(event) {
  const value = event.target.value.replace(/\D/g, ''); // Remove non-digits
  form.value.no_telp = value;
}

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
    no_telp: '',
    nama_ayah: '',
    nama_ibu: '',
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
      // Success - redirect dengan flash message
      resetForm();
      // Redirect dengan query parameter untuk flash message
      router.visit('/pasien?success=Pasien berhasil ditambahkan');
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

<style scoped>
/* Custom animations and responsive styling */
@keyframes fade-in {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes slide-in {
  from {
    opacity: 0;
    transform: translateX(-20px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

@keyframes float {
  0%, 100% {
    transform: translateY(0px);
  }
  50% {
    transform: translateY(-10px);
  }
}

@keyframes pulse-glow {
  0%, 100% {
    box-shadow: 0 0 5px rgba(59, 130, 246, 0.3);
  }
  50% {
    box-shadow: 0 0 20px rgba(59, 130, 246, 0.6);
  }
}

.animate-fade-in {
  animation: fade-in 0.8s ease-out;
}

.animate-slide-in {
  animation: slide-in 0.6s ease-out;
}

.animate-float {
  animation: float 3s ease-in-out infinite;
}

.animate-pulse-glow {
  animation: pulse-glow 2s ease-in-out infinite;
}

.group:hover {
  transform: translateY(-2px);
}

.backdrop-blur-md {
  backdrop-filter: blur(12px);
}

.bg-clip-text {
  -webkit-background-clip: text;
  background-clip: text;
}

.shadow-3xl {
  box-shadow: 0 35px 60px -12px rgba(0, 0, 0, 0.25);
}

* {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

::-webkit-scrollbar {
  width: 8px;
}
::-webkit-scrollbar-track {
  background: #f1f5f9;
  border-radius: 4px;
}
::-webkit-scrollbar-thumb {
  background: linear-gradient(45deg, #3b82f6, #8b5cf6);
  border-radius: 4px;
}
::-webkit-scrollbar-thumb:hover {
  background: linear-gradient(45deg, #2563eb, #7c3aed);
}

input:focus {
  transform: translateY(-1px);
}
select:focus {
  transform: translateY(-1px);
}

button:hover:not(:disabled) {
  transform: translateY(-2px) scale(1.02);
}
button:active:not(:disabled) {
  transform: translateY(0) scale(0.98);
}

input:invalid {
  border-color: #ef4444;
  box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
}
input:valid {
  border-color: #10b981;
  box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
}

@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}
.animate-spin {
  animation: spin 1s linear infinite;
}

/* Responsif kustom dan perbaikan untuk android/smartphone */
@media (max-width: 600px) {
  .max-w-2xl,
  .container,
  .w-full,
  .sm\:p-8,
  .sm\:p-10,
  .sm\:flex-row {
    max-width: 100% !important;
    width: 100% !important;
    padding: 0 !important;
    flex-direction: column !important;
  }

  .p-0 {
    padding: 0 !important;
  }

  .p-6 {
    padding: 0.75rem !important;
  }

  .mt-10,
  .mb-10,
  .mt-8 {
    margin-top: 1rem !important;
    margin-bottom: 1rem !important;
  }

  /* Smaller decorative elements */
  .w-96,
  .h-96 {
    width: 7rem !important;
    height: 7rem !important;
  }

  .rounded-2xl {
    border-radius: 1rem !important;
  }

  .shadow-2xl,
  .hover\:shadow-3xl,
  .shadow-lg,
  .hover\:shadow-xl {
    box-shadow: 0 8px 20px -2px rgba(0,0,0,0.13) !important;
  }

  .flex,
  .flex-col {
    flex-direction: column !important;
  }
  .gap-4 {
    gap: 0.5rem !important;
  }
  .gap-6 {
    gap: 1rem !important;
  }

  .text-4xl {
    font-size: 1.5rem !important;
  }
  .text-3xl {
    font-size: 1.25rem !important;
  }
  .h-16, .w-16 {
    height: 48px !important;
    width: 48px !important;
    min-width: 48px !important;
  }

  .space-y-8 {
    row-gap: 1.25rem !important;
  }

  .rounded-xl {
    border-radius: 0.8rem !important;
  }

  .px-4 {
    padding-left: 0.75rem !important;
    padding-right: 0.75rem !important;
  }
  .py-3 {
    padding-top: 0.7rem !important;
    padding-bottom: 0.7rem !important;
  }
  .px-8, .py-4 {
    padding-left: 1rem !important;
    padding-right: 1rem !important;
    padding-bottom: 0.7rem !important;
    padding-top: 0.7rem !important;
    font-size: 1rem !important;
  }
  .p-8 {
    padding: 1.25rem !important;
  }
  /* Make inputs & buttons smaller on mobile */
  input, select, button {
    font-size: 1rem !important;
    min-height: 40px !important;
  }

  /* Error messages */
  .text-sm {
    font-size: 0.88rem !important;
  }

  /* Fix floating icon collision on mobile */
  .absolute.inset-y-0.right-0.flex.items-center.pr-3 {
    right: 0.2rem !important;
    pointer-events: none;
  }
}

@media (max-width: 350px) {
  .text-4xl,
  .text-3xl {
    font-size: 1.1rem !important;
  }
  .rounded-2xl {
    border-radius: 0.6rem !important;
  }
  .rounded-xl {
    border-radius: 0.3rem !important;
  }
}
</style>

<template>
  <AuthenticatedLayout>
    <Head title="Tambah Data Pasien" />
    <!-- Gunakan relative untuk parent agar absolut child bisa mengikut -->
    <div class="min-h-screen bg-cover bg-center p-6 relative" style="background-image: url('/images/bg-login.png')">
      <!-- Decorative background, minimize on mobile via media query -->
      <div class="absolute top-0 left-0 w-full h-96 bg-gradient-to-r from-blue-100/30 to-purple-100/30 transform -skew-y-1 pointer-events-none"></div>
      <div class="absolute bottom-0 right-0 w-96 h-96 bg-gradient-to-l from-green-100/20 to-blue-100/20 rounded-full transform translate-x-1/2 translate-y-1/2 pointer-events-none"></div>

      <div class="container mx-auto max-w-2xl p-0 sm:p-8 flex flex-col items-center min-h-[100vh] relative z-10">
        <div class="w-full max-w-2xl bg-white/90 backdrop-blur-md rounded-2xl shadow-2xl border border-white/20 p-0 sm:p-10 mt-10 mb-10 hover:shadow-3xl transition-all duration-300">
          <div class="flex items-center gap-4 mb-8">
            <div class="flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-blue-500 to-purple-600 text-white text-3xl shadow-lg animate-float">
              <i class="fas fa-user-plus"></i>
            </div>
            <div>
              <h1 class="text-4xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent leading-tight">
                Tambah Data Pasien
              </h1>
              <p class="text-gray-600 text-lg mt-2 flex items-center">
                <i class="fas fa-info-circle mr-2 text-blue-500"></i>
                Isi data pasien dengan lengkap dan benar
              </p>
            </div>
          </div>
          <form @submit.prevent="submitForm" class="space-y-8">
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
              <!-- Nama Pasien -->
              <div class="group">
                <label class="block text-sm font-semibold text-gray-700 mb-3 flex items-center">
                  <i class="fas fa-user text-blue-500 mr-2"></i>
                  Nama Pasien <span class="text-red-500 ml-1">*</span>
                </label>
                <div class="relative">
                  <input 
                    v-model="form.nm_p" 
                    type="text" 
                    placeholder="Nama lengkap" 
                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-300 bg-white/80 backdrop-blur-sm hover:border-blue-300 group-hover:shadow-lg" 
                    :class="{'border-red-500 focus:border-red-500 focus:ring-red-500/20': errors.nm_p}" 
                    autocomplete="off"
                  />
                  <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                    <i class="fas fa-user-tag text-gray-400"></i>
                  </div>
                </div>
                <div v-if="errors.nm_p" class="text-red-500 text-sm mt-2 flex items-center">
                  <i class="fas fa-exclamation-circle mr-1"></i>{{ errors.nm_p[0] }}
                </div>
              </div>

              <!-- NIK -->
              <div class="group">
                <label class="block text-sm font-semibold text-gray-700 mb-3 flex items-center">
                  <i class="fas fa-id-card text-green-500 mr-2"></i>
                  NIK <span class="text-red-500 ml-1">*</span>
                </label>
                <div class="relative">
                  <input 
                    v-model="form.nik" 
                    type="tel"
                    inputmode="numeric"
                    pattern="[0-9]*"
                    placeholder="16 digit NIK" 
                    maxlength="16"
                    @input="formatNikInput"
                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-green-500/20 focus:border-green-500 transition-all duration-300 bg-white/80 backdrop-blur-sm hover:border-green-300 group-hover:shadow-lg" 
                    :class="{'border-red-500 focus:border-red-500 focus:ring-red-500/20': errors.nik}" 
                    autocomplete="off"
                  />
                  <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                    <i class="fas fa-hashtag text-gray-400"></i>
                  </div>
                </div>
                <div v-if="errors.nik" class="text-red-500 text-sm mt-2 flex items-center">
                  <i class="fas fa-exclamation-circle mr-1"></i>{{ errors.nik[0] }}
                </div>
              </div>

              <!-- No BPJS -->
              <div class="group">
                <label class="block text-sm font-semibold text-gray-700 mb-3 flex items-center">
                  <i class="fas fa-shield-alt text-purple-500 mr-2"></i>
                  No BPJS
                </label>
                <div class="relative">
                  <input 
                    v-model="form.no_bpjs" 
                    type="tel"
                    inputmode="numeric"
                    pattern="[0-9]*"
                    placeholder="Nomor BPJS (opsional)" 
                    @input="formatBpjsInput"
                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-purple-500/20 focus:border-purple-500 transition-all duration-300 bg-white/80 backdrop-blur-sm hover:border-purple-300 group-hover:shadow-lg" 
                    :class="{'border-red-500 focus:border-red-500 focus:ring-red-500/20': errors.no_bpjs}" 
                    autocomplete="off"
                  />
                  <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                    <i class="fas fa-shield text-gray-400"></i>
                  </div>
                </div>
                <div v-if="errors.no_bpjs" class="text-red-500 text-sm mt-2 flex items-center">
                  <i class="fas fa-exclamation-circle mr-1"></i>{{ errors.no_bpjs[0] }}
                </div>
              </div>

              <!-- Agama -->
              <div class="group">
                <label class="block text-sm font-semibold text-gray-700 mb-3 flex items-center">
                  <i class="fas fa-pray text-orange-500 mr-2"></i>
                  Agama <span class="text-red-500 ml-1">*</span>
                </label>
                <div class="relative">
                  <select 
                    v-model="form.agm" 
                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-orange-500/20 focus:border-orange-500 transition-all duration-300 bg-white/80 backdrop-blur-sm hover:border-orange-300 group-hover:shadow-lg" 
                    :class="{'border-red-500 focus:border-red-500 focus:ring-red-500/20': errors.agm}"
                  >
                    <option value="">Pilih Agama</option>
                    <option value="islam">Islam</option>
                    <option value="kristen">Kristen</option>
                    <option value="katolik">Katolik</option>
                    <option value="hindu">Hindu</option>
                    <option value="buddha">Buddha</option>
                    <option value="dll">Lainnya</option>
                  </select>
                  <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                    <i class="fas fa-chevron-down text-gray-400"></i>
                  </div>
                </div>
                <div v-if="errors.agm" class="text-red-500 text-sm mt-2 flex items-center">
                  <i class="fas fa-exclamation-circle mr-1"></i>{{ errors.agm[0] }}
                </div>
              </div>

              <!-- Tanggal Lahir -->
              <div class="group">
                <label class="block text-sm font-semibold text-gray-700 mb-3 flex items-center">
                  <i class="fas fa-birthday-cake text-pink-500 mr-2"></i>
                  Tanggal Lahir <span class="text-red-500 ml-1">*</span>
                </label>
                <div class="relative">
                  <input 
                    v-model="form.tgl_lahir" 
                    type="date" 
                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-pink-500/20 focus:border-pink-500 transition-all duration-300 bg-white/80 backdrop-blur-sm hover:border-pink-300 group-hover:shadow-lg" 
                    :class="{'border-red-500 focus:border-red-500 focus:ring-red-500/20': errors.tgl_lahir}" 
                  />
                  <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                    <i class="fas fa-calendar text-gray-400"></i>
                  </div>
                </div>
                <div v-if="errors.tgl_lahir" class="text-red-500 text-sm mt-2 flex items-center">
                  <i class="fas fa-exclamation-circle mr-1"></i>{{ errors.tgl_lahir[0] }}
                </div>
              </div>

              <!-- Kelamin -->
              <div class="group">
                <label class="block text-sm font-semibold text-gray-700 mb-3 flex items-center">
                  <i class="fas fa-venus-mars text-indigo-500 mr-2"></i>
                  Kelamin <span class="text-red-500 ml-1">*</span>
                </label>
                <div class="relative">
                  <select 
                    v-model="form.kelamin" 
                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all duration-300 bg-white/80 backdrop-blur-sm hover:border-indigo-300 group-hover:shadow-lg" 
                    :class="{'border-red-500 focus:border-red-500 focus:ring-red-500/20': errors.kelamin}"
                  >
                    <option value="">Pilih Kelamin</option>
                    <option value="L">Laki-laki</option>
                    <option value="P">Perempuan</option>
                    <option value="kosong">Tidak Diketahui</option>
                  </select>
                  <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                    <i class="fas fa-chevron-down text-gray-400"></i>
                  </div>
                </div>
                <div v-if="errors.kelamin" class="text-red-500 text-sm mt-2 flex items-center">
                  <i class="fas fa-exclamation-circle mr-1"></i>{{ errors.kelamin[0] }}
                </div>
              </div>

              <!-- Alamat Lama -->
              <div class="group">
                <label class="block text-sm font-semibold text-gray-700 mb-3 flex items-center">
                  <i class="fas fa-map-marker-alt text-teal-500 mr-2"></i>
                  Alamat Lama <span class="text-red-500 ml-1">*</span>
                </label>
                <div class="relative">
                  <input 
                    v-model="form.almt_L" 
                    type="text" 
                    placeholder="Alamat lama" 
                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-teal-500/20 focus:border-teal-500 transition-all duration-300 bg-white/80 backdrop-blur-sm hover:border-teal-300 group-hover:shadow-lg" 
                    :class="{'border-red-500 focus:border-red-500 focus:ring-red-500/20': errors.almt_L}" 
                    autocomplete="off"
                  />
                  <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                    <i class="fas fa-home text-gray-400"></i>
                  </div>
                </div>
                <div v-if="errors.almt_L" class="text-red-500 text-sm mt-2 flex items-center">
                  <i class="fas fa-exclamation-circle mr-1"></i>{{ errors.almt_L[0] }}
                </div>
              </div>

              <!-- Alamat Baru -->
              <div class="group">
                <label class="block text-sm font-semibold text-gray-700 mb-3 flex items-center">
                  <i class="fas fa-building text-cyan-500 mr-2"></i>
                  Alamat Baru <span class="text-red-500 ml-1">*</span>
                </label>
                <div class="relative">
                  <input 
                    v-model="form.almt_B" 
                    type="text" 
                    placeholder="Alamat baru" 
                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-cyan-500/20 focus:border-cyan-500 transition-all duration-300 bg-white/80 backdrop-blur-sm hover:border-cyan-300 group-hover:shadow-lg" 
                    :class="{'border-red-500 focus:border-red-500 focus:ring-red-500/20': errors.almt_B}" 
                    autocomplete="off"
                  />
                  <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                    <i class="fas fa-map-pin text-gray-400"></i>
                  </div>
                </div>
                <div v-if="errors.almt_B" class="text-red-500 text-sm mt-2 flex items-center">
                  <i class="fas fa-exclamation-circle mr-1"></i>{{ errors.almt_B[0] }}
                </div>
              </div>

              <!-- Nomor Telepon -->
              <div class="group">
                <label class="block text-sm font-semibold text-gray-700 mb-3 flex items-center">
                  <i class="fas fa-phone text-blue-500 mr-2"></i>
                  Nomor Telepon
                </label>
                <div class="relative">
                  <input 
                    v-model="form.no_telp" 
                    type="tel"
                    inputmode="numeric"
                    pattern="[0-9]*"
                    placeholder="08xxxxxxxxxx" 
                    @input="formatTeleponInput"
                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-300 bg-white/80 backdrop-blur-sm hover:border-blue-300 group-hover:shadow-lg" 
                    :class="{'border-red-500 focus:border-red-500 focus:ring-red-500/20': errors.no_telp}" 
                    autocomplete="tel"
                  />
                  <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                    <i class="fas fa-phone-alt text-gray-400"></i>
                  </div>
                </div>
                <div v-if="errors.no_telp" class="text-red-500 text-sm mt-2 flex items-center">
                  <i class="fas fa-exclamation-circle mr-1"></i>{{ errors.no_telp[0] }}
                </div>
              </div>

              <!-- Nama Ayah -->
              <div class="group">
                <label class="block text-sm font-semibold text-gray-700 mb-3 flex items-center">
                  <i class="fas fa-male text-indigo-500 mr-2"></i>
                  Nama Ayah <span class="text-gray-400 text-xs font-normal ml-2">(opsional)</span>
                </label>
                <div class="relative">
                  <input 
                    v-model="form.nama_ayah" 
                    type="text" 
                    placeholder="Nama ayah (kosongkan jika almarhum)" 
                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all duration-300 bg-white/80 backdrop-blur-sm hover:border-indigo-300 group-hover:shadow-lg" 
                    :class="{'border-red-500 focus:border-red-500 focus:ring-red-500/20': errors.nama_ayah}" 
                    autocomplete="off"
                  />
                  <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                    <i class="fas fa-user text-gray-400"></i>
                  </div>
                </div>
                <div v-if="errors.nama_ayah" class="text-red-500 text-sm mt-2 flex items-center">
                  <i class="fas fa-exclamation-circle mr-1"></i>{{ errors.nama_ayah[0] }}
                </div>
              </div>

              <!-- Nama Ibu -->
              <div class="group">
                <label class="block text-sm font-semibold text-gray-700 mb-3 flex items-center">
                  <i class="fas fa-female text-pink-500 mr-2"></i>
                  Nama Ibu <span class="text-gray-400 text-xs font-normal ml-2">(opsional)</span>
                </label>
                <div class="relative">
                  <input 
                    v-model="form.nama_ibu" 
                    type="text" 
                    placeholder="Nama ibu (kosongkan jika almarhum)" 
                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-pink-500/20 focus:border-pink-500 transition-all duration-300 bg-white/80 backdrop-blur-sm hover:border-pink-300 group-hover:shadow-lg" 
                    :class="{'border-red-500 focus:border-red-500 focus:ring-red-500/20': errors.nama_ibu}" 
                    autocomplete="off"
                  />
                  <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                    <i class="fas fa-user text-gray-400"></i>
                  </div>
                </div>
                <div v-if="errors.nama_ibu" class="text-red-500 text-sm mt-2 flex items-center">
                  <i class="fas fa-exclamation-circle mr-1"></i>{{ errors.nama_ibu[0] }}
                </div>
              </div>
            </div>
            <!-- Action Buttons -->
            <div class="bg-white/90 backdrop-blur-sm shadow-2xl rounded-2xl p-8 border border-white/20 hover:shadow-3xl transition-all duration-300 mt-8">
              <div class="flex flex-col gap-4 sm:flex-row justify-end">
                <button
                  type="submit"
                  :disabled="loading"
                  class="px-8 py-4 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 disabled:from-gray-400 disabled:to-gray-500 text-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 flex items-center justify-center font-medium disabled:transform-none"
                >
                  <i v-if="loading" class="fas fa-spinner fa-spin mr-2"></i>
                  <i v-else class="fas fa-save mr-2"></i>
                  {{ loading ? 'Menyimpan...' : 'Simpan' }}
                </button>
                <button
                  type="button"
                  @click="resetForm"
                  class="px-8 py-4 bg-gradient-to-r from-gray-100 to-gray-200 hover:from-gray-200 hover:to-gray-300 text-gray-700 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 flex items-center justify-center font-medium border border-gray-300"
                  :disabled="loading"
                >
                  <i class="fas fa-undo mr-2"></i>
                  Reset
                </button>
                <button
                  type="button"
                  @click="router.visit('/pasien')"
                  class="px-8 py-4 bg-gradient-to-r from-gray-300 to-gray-400 hover:from-gray-400 hover:to-gray-500 text-gray-800 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 flex items-center justify-center font-medium"
                  :disabled="loading"
                >
                  <i class="fas fa-times mr-2"></i>
                  Batal
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
