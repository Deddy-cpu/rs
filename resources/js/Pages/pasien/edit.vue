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

function resetForm() {
  form.clearErrors();
  form.reset();
  form.setData(originalValues.value);
}

function submitForm() {
  form.put(route('pasien.update', props.psn.id), {
    preserveScroll: false,
  });
}
</script>

<template>
  <AuthenticatedLayout>
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
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<style scoped>
@keyframes slide-in {
  from {
    opacity: 0;
    transform: translateX(20px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

.animate-slide-in {
  animation: slide-in 0.3s ease-out;
}
</style>
