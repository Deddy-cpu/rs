<script setup>
import { computed, watch } from 'vue';
import { useForm, Head, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
  psn: {
    type: Object,
    required: true
  },
  flash: {
    type: Object,
    default: () => ({})
  }
});

const form = useForm({
  nm_p: props.psn?.nm_p || '',
  nik: String(props.psn?.nik || ''),
  no_bpjs: String(props.psn?.no_bpjs || ''),
  agm: props.psn?.agm || '',
  tgl_lahir: props.psn?.tgl_lahir || '',
  kelamin: props.psn?.kelamin || '',
  almt_L: props.psn?.almt_L || '',
  almt_B: props.psn?.almt_B || '',
});

// Watch for prop changes and update form
watch(() => props.psn, (newPsn) => {
  if (newPsn) {
    form.nm_p = newPsn.nm_p || '';
    form.nik = String(newPsn.nik || '');
    form.no_bpjs = String(newPsn.no_bpjs || '');
    form.agm = newPsn.agm || '';
    form.tgl_lahir = newPsn.tgl_lahir || '';
    form.kelamin = newPsn.kelamin || '';
    form.almt_L = newPsn.almt_L || '';
    form.almt_B = newPsn.almt_B || '';
  }
}, { immediate: true, deep: true });

<<<<<<< HEAD
const originalValues = computed(() => ({
  nm_p: props.psn?.nm_p || '',
  nik: String(props.psn?.nik || ''),
  no_bpjs: String(props.psn?.no_bpjs || ''),
  agm: props.psn?.agm || '',
  tgl_lahir: props.psn?.tgl_lahir || '',
  kelamin: props.psn?.kelamin || '',
  almt_L: props.psn?.almt_L || '',
  almt_B: props.psn?.almt_B || '',
}));
=======
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
>>>>>>> 7b932bc53439d394833a41147cd1b6df81f4850c

function resetForm() {
  form.clearErrors();
  form.reset();
  form.setData(originalValues.value);
}

<<<<<<< HEAD
function submitForm() {
  form.put(route('pasien.update', props.psn.id), {
    preserveScroll: false,
  });
=======
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
      // Success - redirect dengan flash message
      router.visit('/pasien?success=Pasien berhasil diupdate');
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
>>>>>>> 7b932bc53439d394833a41147cd1b6df81f4850c
}
</script>

<template>
  <AuthenticatedLayout>
<<<<<<< HEAD
    <Head :title="`Edit Data Pasien - ${psn?.nm_p || ''}`" />
    
    <!-- Background dengan gradient dan pattern -->
    <div class="min-h-screen bg-cover bg-center overflow-x-hidden w-full" style="background-image: url('/images/bg-login.png'); padding: 1.5rem; box-sizing: border-box;">
      <!-- Decorative background elements -->
      <div class="absolute top-0 left-0 h-96 bg-gradient-to-r from-[#CFF7E3]/30 to-[#DCEBFF]/30 transform -skew-y-1 overflow-hidden pointer-events-none" style="width: 100vw; max-width: 100%;"></div>
      <div class="absolute bottom-0 right-0 w-96 h-96 bg-gradient-to-l from-[#E7E4FF]/20 to-[#FFE5E0]/20 rounded-full overflow-hidden pointer-events-none" style="transform: translateY(50%); right: 0; max-width: 50vw;"></div>
      
      <div class="mx-auto py-8 relative z-10 w-full flex items-center justify-center" style="max-width: min(900px, calc(100vw - 3rem)); box-sizing: border-box;">
        <!-- Flash Messages -->
        <div v-if="flash.success" class="fixed top-4 right-4 bg-[#CFF7E3] border border-[#2ECC71] text-[#2ECC71] px-6 py-4 rounded-xl shadow-lg z-50 animate-slide-in">
          <div class="flex items-center">
            <div class="w-8 h-8 bg-[#2ECC71] rounded-full flex items-center justify-center mr-3">
              <i class="fas fa-check text-white text-sm"></i>
            </div>
            <span class="font-medium">{{ flash.success }}</span>
          </div>
        </div>
        
        <div v-if="flash.error" class="fixed top-4 right-4 bg-[#FFE5E0] border border-[#FF6B6B] text-[#D32F2F] px-6 py-4 rounded-xl shadow-lg z-50 animate-slide-in">
          <div class="flex items-center">
            <div class="w-8 h-8 bg-[#D32F2F] rounded-full flex items-center justify-center mr-3">
              <i class="fas fa-exclamation text-white text-sm"></i>
            </div>
            <span class="font-medium">{{ flash.error }}</span>
          </div>
        </div>

        <!-- Card Form -->
        <div class="bg-white/90 backdrop-blur-sm shadow-2xl rounded-2xl p-8 border border-white/20 w-full">
          <!-- Header -->
          <div class="flex items-center mb-8">
            <div class="w-12 h-12 bg-gradient-to-br from-[#2ECC71] to-[#1976D2] rounded-xl flex items-center justify-center shadow-lg mr-4">
              <i class="fas fa-user-edit text-white text-xl"></i>
            </div>
            <div>
              <h1 class="text-3xl font-bold text-[#1A1A1A]">
                Edit Data Pasien
              </h1>
              <p class="text-[#666666] mt-1">Perbarui informasi pasien berikut</p>
            </div>
          </div>

          <!-- Form -->
          <form @submit.prevent="submitForm" class="space-y-6">
            <!-- Nama Pasien -->
            <div>
              <label class="block font-semibold text-[#1A1A1A] mb-2">
                <i class="fas fa-user-tag text-[#1976D2] mr-2"></i>Nama Pasien
              </label>
              <input
                v-model="form.nm_p"
                type="text"
                class="w-full border border-gray-300 rounded-xl px-4 py-3 bg-[#F7F9FB] focus:outline-none focus:ring-2 focus:ring-[#2ECC71] focus:border-transparent transition-all duration-300 text-[#1A1A1A]"
                :class="{'border-[#FF6B6B]': form.errors.nm_p}"
                placeholder="Masukkan nama pasien"
              />
              <div v-if="form.errors.nm_p" class="text-[#FF6B6B] text-sm mt-1 flex items-center">
                <i class="fas fa-exclamation-circle mr-1"></i>{{ form.errors.nm_p }}
              </div>
            </div>

            <!-- NIK dan No BPJS -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="block font-semibold text-[#1A1A1A] mb-2">
                  <i class="fas fa-id-card text-[#2ECC71] mr-2"></i>NIK
                </label>
                <input
                  v-model="form.nik"
                  type="text"
                  class="w-full border border-gray-300 rounded-xl px-4 py-3 bg-[#F7F9FB] focus:outline-none focus:ring-2 focus:ring-[#2ECC71] focus:border-transparent transition-all duration-300 text-[#1A1A1A]"
                  :class="{'border-[#FF6B6B]': form.errors.nik}"
                  placeholder="Masukkan NIK"
                />
                <div v-if="form.errors.nik" class="text-[#FF6B6B] text-sm mt-1 flex items-center">
                  <i class="fas fa-exclamation-circle mr-1"></i>{{ form.errors.nik }}
                </div>
              </div>

              <div>
                <label class="block font-semibold text-[#1A1A1A] mb-2">
                  <i class="fas fa-shield-alt text-[#7B68EE] mr-2"></i>No BPJS
                </label>
                <input
                  v-model="form.no_bpjs"
                  type="text"
                  class="w-full border border-gray-300 rounded-xl px-4 py-3 bg-[#F7F9FB] focus:outline-none focus:ring-2 focus:ring-[#2ECC71] focus:border-transparent transition-all duration-300 text-[#1A1A1A]"
                  :class="{'border-[#FF6B6B]': form.errors.no_bpjs}"
                  placeholder="Masukkan No BPJS"
                />
                <div v-if="form.errors.no_bpjs" class="text-[#FF6B6B] text-sm mt-1 flex items-center">
                  <i class="fas fa-exclamation-circle mr-1"></i>{{ form.errors.no_bpjs }}
                </div>
              </div>
            </div>

            <!-- Agama dan Tanggal Lahir -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="block font-semibold text-[#1A1A1A] mb-2">
                  <i class="fas fa-pray text-[#FF6B6B] mr-2"></i>Agama
                </label>
                <select
                  v-model="form.agm"
                  class="w-full border border-gray-300 rounded-xl px-4 py-3 bg-[#F7F9FB] focus:outline-none focus:ring-2 focus:ring-[#2ECC71] focus:border-transparent transition-all duration-300 text-[#1A1A1A]"
                  :class="{'border-[#FF6B6B]': form.errors.agm}"
                >
                  <option value="">Pilih Agama</option>
                  <option value="islam">Islam</option>
                  <option value="kristen">Kristen</option>
                  <option value="katolik">Katolik</option>
                  <option value="hindu">Hindu</option>
                  <option value="buddha">Buddha</option>
                  <option value="dll">Lainnya</option>
                </select>
                <div v-if="form.errors.agm" class="text-[#FF6B6B] text-sm mt-1 flex items-center">
                  <i class="fas fa-exclamation-circle mr-1"></i>{{ form.errors.agm }}
                </div>
              </div>

              <div>
                <label class="block font-semibold text-[#1A1A1A] mb-2">
                  <i class="fas fa-birthday-cake text-[#1976D2] mr-2"></i>Tanggal Lahir
                </label>
                <input
                  v-model="form.tgl_lahir"
                  type="date"
                  class="w-full border border-gray-300 rounded-xl px-4 py-3 bg-[#F7F9FB] focus:outline-none focus:ring-2 focus:ring-[#2ECC71] focus:border-transparent transition-all duration-300 text-[#1A1A1A]"
                  :class="{'border-[#FF6B6B]': form.errors.tgl_lahir}"
                />
                <div v-if="form.errors.tgl_lahir" class="text-[#FF6B6B] text-sm mt-1 flex items-center">
                  <i class="fas fa-exclamation-circle mr-1"></i>{{ form.errors.tgl_lahir }}
                </div>
              </div>
            </div>

            <!-- Kelamin -->
            <div>
              <label class="block font-semibold text-[#1A1A1A] mb-2">
                <i class="fas fa-venus-mars text-[#2ECC71] mr-2"></i>Jenis Kelamin
              </label>
              <select
                v-model="form.kelamin"
                class="w-full border border-gray-300 rounded-xl px-4 py-3 bg-[#F7F9FB] focus:outline-none focus:ring-2 focus:ring-[#2ECC71] focus:border-transparent transition-all duration-300 text-[#1A1A1A]"
                :class="{'border-[#FF6B6B]': form.errors.kelamin}"
              >
                <option value="">Pilih Jenis Kelamin</option>
                <option value="L">Laki-laki</option>
                <option value="P">Perempuan</option>
                <option value="kosong">Tidak Diketahui</option>
              </select>
              <div v-if="form.errors.kelamin" class="text-[#FF6B6B] text-sm mt-1 flex items-center">
                <i class="fas fa-exclamation-circle mr-1"></i>{{ form.errors.kelamin }}
              </div>
            </div>

            <!-- Alamat Lahir -->
            <div>
              <label class="block font-semibold text-[#1A1A1A] mb-2">
                <i class="fas fa-map-marker-alt text-[#666666] mr-2"></i>Alamat Lahir
              </label>
              <input
                v-model="form.almt_L"
                type="text"
                class="w-full border border-gray-300 rounded-xl px-4 py-3 bg-[#F7F9FB] focus:outline-none focus:ring-2 focus:ring-[#2ECC71] focus:border-transparent transition-all duration-300 text-[#1A1A1A]"
                :class="{'border-[#FF6B6B]': form.errors.almt_L}"
                placeholder="Masukkan alamat lahir"
              />
              <div v-if="form.errors.almt_L" class="text-[#FF6B6B] text-sm mt-1 flex items-center">
                <i class="fas fa-exclamation-circle mr-1"></i>{{ form.errors.almt_L }}
              </div>
            </div>

            <!-- Alamat Berdomisili -->
            <div>
              <label class="block font-semibold text-[#1A1A1A] mb-2">
                <i class="fas fa-home text-[#666666] mr-2"></i>Alamat Berdomisili
              </label>
              <input
                v-model="form.almt_B"
                type="text"
                class="w-full border border-gray-300 rounded-xl px-4 py-3 bg-[#F7F9FB] focus:outline-none focus:ring-2 focus:ring-[#2ECC71] focus:border-transparent transition-all duration-300 text-[#1A1A1A]"
                :class="{'border-[#FF6B6B]': form.errors.almt_B}"
                placeholder="Masukkan alamat berdomisili"
              />
              <div v-if="form.errors.almt_B" class="text-[#FF6B6B] text-sm mt-1 flex items-center">
                <i class="fas fa-exclamation-circle mr-1"></i>{{ form.errors.almt_B }}
              </div>
            </div>

            <!-- Tombol Action -->
            <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-gray-200">
              <button
                type="submit"
                :disabled="form.processing"
                class="flex-1 px-6 py-3 bg-[#2ECC71] hover:bg-[#27AE60] text-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 flex items-center justify-center font-semibold disabled:opacity-50 disabled:cursor-not-allowed"
              >
                <i class="fas fa-save mr-2"></i>
                {{ form.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}
              </button>
              <button
                type="button"
                @click="resetForm"
                :disabled="form.processing"
                class="px-6 py-3 bg-[#F7F9FB] hover:bg-gray-200 text-[#1A1A1A] rounded-xl transition-all duration-300 font-semibold border border-gray-300 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                <i class="fas fa-redo mr-2"></i>Reset
              </button>
              <button
                type="button"
                @click="router.visit(`/pasien/${psn?.id}`)"
                :disabled="form.processing"
                class="px-6 py-3 bg-[#1976D2] hover:bg-[#1565C0] text-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 font-semibold disabled:opacity-50 disabled:cursor-not-allowed"
              >
                <i class="fas fa-times mr-2"></i>Batal
              </button>
            </div>
          </form>
        </div>
=======
    <Head title="Edit Data Pasien" />
    <!-- Gunakan relative untuk parent agar absolut child bisa mengikut -->
    <div class="min-h-screen bg-cover bg-center p-6 relative" style="background-image: url('/images/bg-login.png')">
      <!-- Decorative background, minimize on mobile via media query -->
      <div class="absolute top-0 left-0 w-full h-96 bg-gradient-to-r from-blue-100/30 to-purple-100/30 transform -skew-y-1 pointer-events-none"></div>
      <div class="absolute bottom-0 right-0 w-96 h-96 bg-gradient-to-l from-green-100/20 to-blue-100/20 rounded-full transform translate-x-1/2 translate-y-1/2 pointer-events-none"></div>

      <div class="container mx-auto max-w-2xl p-0 sm:p-8 flex flex-col items-center min-h-[100vh] relative z-10">
        <div class="w-full max-w-2xl bg-white/90 backdrop-blur-md rounded-2xl shadow-2xl border border-white/20 p-0 sm:p-10 mt-10 mb-10 hover:shadow-3xl transition-all duration-300">
          <div class="flex items-center gap-4 mb-8">
            <div class="flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-blue-500 to-purple-600 text-white text-3xl shadow-lg animate-float">
              <i class="fas fa-user-edit"></i>
            </div>
            <div>
              <h1 class="text-4xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent leading-tight">
                Edit Data Pasien
              </h1>
              <p class="text-gray-600 text-lg mt-2 flex items-center">
                <i class="fas fa-info-circle mr-2 text-blue-500"></i>
                Perbarui data pasien dengan lengkap dan benar
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

              <!-- Alamat Lahir -->
              <div class="group">
                <label class="block text-sm font-semibold text-gray-700 mb-3 flex items-center">
                  <i class="fas fa-map-marker-alt text-teal-500 mr-2"></i>
                  Alamat Lahir <span class="text-red-500 ml-1">*</span>
                </label>
                <div class="relative">
                  <input 
                    v-model="form.almt_L" 
                    type="text" 
                    placeholder="Alamat saat lahir" 
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

              <!-- Alamat Berdomisili -->
              <div class="group">
                <label class="block text-sm font-semibold text-gray-700 mb-3 flex items-center">
                  <i class="fas fa-building text-cyan-500 mr-2"></i>
                  Alamat Berdomisili <span class="text-red-500 ml-1">*</span>
                </label>
                <div class="relative">
                  <input 
                    v-model="form.almt_B" 
                    type="text" 
                    placeholder="Alamat sekarang" 
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
                  {{ loading ? 'Menyimpan...' : 'Simpan Perubahan' }}
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
>>>>>>> 7b932bc53439d394833a41147cd1b6df81f4850c
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<style scoped>
<<<<<<< HEAD
@keyframes slide-in {
  from {
    opacity: 0;
    transform: translateX(20px);
=======
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
>>>>>>> 7b932bc53439d394833a41147cd1b6df81f4850c
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

<<<<<<< HEAD
.animate-slide-in {
  animation: slide-in 0.3s ease-out;
=======
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
  animation: fade-in 0.6s ease-out;
}

.animate-slide-in {
  animation: slide-in 0.5s ease-out;
}

.animate-float {
  animation: float 3s ease-in-out infinite;
}

.bg-clip-text {
  -webkit-background-clip: text;
  background-clip: text;
}

.shadow-3xl {
  box-shadow: 0 35px 60px -12px rgba(0, 0, 0, 0.25);
}

/* Responsive adjustments */
@media (max-width: 640px) {
  .container {
    padding: 1rem;
  }
  
  .rounded-2xl {
    border-radius: 1rem;
  }
  
  .shadow-2xl,
  .hover\:shadow-3xl,
  .shadow-lg,
  .hover\:shadow-xl {
    box-shadow: 0 8px 20px -2px rgba(0,0,0,0.13) !important;
  }
  
  h1 {
    font-size: 1.75rem;
  }
  
  .text-4xl {
    font-size: 2rem;
  }
  
  .text-lg {
    font-size: 0.95rem;
  }
  
  .px-8 {
    padding-left: 1rem;
    padding-right: 1rem;
  }
  
  .py-4 {
    padding-top: 0.75rem;
    padding-bottom: 0.75rem;
  }
  
  .rounded-xl {
    border-radius: 0.75rem;
  }
  
  .rounded-2xl {
    border-radius: 1rem;
  }
  
  .rounded-xl {
    border-radius: 0.75rem;
  }
>>>>>>> 7b932bc53439d394833a41147cd1b6df81f4850c
}
</style>
