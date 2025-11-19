<template>
  <AuthenticatedLayout>
    <Head title="Tambah Kunjungan Pasien" />
    
    <!-- Background dengan gradient dan pattern -->
    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-green-50 relative overflow-hidden">
      <!-- Decorative background elements -->
      <div class="absolute top-0 left-0 w-full h-96 bg-gradient-to-r from-blue-100/30 to-purple-100/30 transform -skew-y-1"></div>
      <div class="absolute bottom-0 right-0 w-96 h-96 bg-gradient-to-l from-green-100/20 to-blue-100/20 rounded-full transform translate-x-1/2 translate-y-1/2"></div>
      
      <div class="max-w-4xl mx-auto py-8 px-4 relative z-10">
        <!-- Header dengan glassmorphism effect -->
        <div class="bg-white/80 backdrop-blur-md rounded-2xl shadow-2xl border border-white/20 p-8 mb-8 animate-fade-in">
          <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">
            <div class="flex items-center space-x-4">
              <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl flex items-center justify-center shadow-lg">
                <i class="fas fa-calendar-plus text-white text-2xl"></i>
              </div>
              <div>
                <h1 class="text-4xl font-bold bg-gradient-to-r from-gray-900 to-gray-700 bg-clip-text text-transparent">
                  Tambah Kunjungan
                </h1>
                <p class="text-gray-600 mt-2 text-lg flex items-center">
                  <i class="fas fa-user mr-2 text-blue-500"></i>
                  Tambah kunjungan baru untuk <span class="font-semibold text-blue-600">{{ psn?.nm_p }}</span>
                </p>
              </div>
            </div>
            <button 
              @click="router.visit(`/pasien/${psn?.id}`)"
              class="px-6 py-3 bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 flex items-center font-medium"
            >
              <i class="fas fa-arrow-left mr-2"></i>Kembali ke Detail
            </button>
          </div>
        </div>

        <!-- Patient Name Conflict Warning Banner -->
        <transition name="slide-fade">
          <div v-if="hasPatientNameConflict && patientNameConflicts.length > 0" class="mb-8 bg-red-50 border-l-4 border-red-500 p-6 rounded-lg shadow-md">
            <div class="flex items-start">
              <div class="flex-shrink-0">
                <i class="fas fa-ban text-red-600 text-xl"></i>
              </div>
              <div class="ml-3 flex-1">
                <h3 class="text-sm font-bold text-red-800 mb-2">
                  🚫 FORM TIDAK DAPAT DIIISI: Ada dokter lain yang sedang menginput nama pasien yang sama
                </h3>
                <div class="space-y-1">
                  <div 
                    v-for="(conflict, index) in patientNameConflicts" 
                    :key="index"
                    class="text-sm text-red-700 flex items-center"
                  >
                    <i class="fas fa-user-md mr-2"></i>
                    <span>{{ conflict.message }}</span>
                  </div>
                </div>
                <p class="text-sm text-red-800 mt-3 font-semibold bg-red-100 p-3 rounded border border-red-300">
                  ⚠️ Form input data pasien telah dinonaktifkan. Mohon tunggu dokter lain selesai atau koordinasi terlebih dahulu sebelum melanjutkan.
                </p>
              </div>
            </div>
          </div>
        </transition>

        <!-- Tombol Enum Kunjungan dengan desain menarik -->
        <div class="bg-white/90 backdrop-blur-sm shadow-2xl rounded-2xl p-8 border border-white/20 mb-8 hover:shadow-3xl transition-all duration-300">
          <div class="flex items-center mb-6">
            <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl flex items-center justify-center shadow-lg mr-4">
              <i class="fas fa-list text-white text-lg"></i>
            </div>
            <h3 class="text-2xl font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent">
              Pilih Jenis Kunjungan
            </h3>
          </div>
          <div class="flex flex-wrap gap-4">
            <button
              v-for="(item, idx) in enumKunjungan"
              :key="item.value"
              type="button"
              @click="form.kunjungan = item.value"
              :disabled="isFormDisabled"
              :class="[
                'group px-6 py-4 rounded-xl border-2 transition-all duration-300 transform hover:scale-105 flex items-center font-medium text-sm',
                form.kunjungan === item.value
                  ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white border-blue-600 shadow-lg shadow-blue-200'
                  : 'bg-white text-gray-700 border-gray-300 hover:bg-gradient-to-r hover:from-blue-50 hover:to-purple-50 hover:border-blue-300 hover:shadow-md',
                isFormDisabled ? 'opacity-50 cursor-not-allowed' : ''
              ]"
            >
              <i :class="[item.icon, form.kunjungan === item.value ? 'text-white' : 'text-blue-500 group-hover:text-blue-600']" class="mr-3 text-lg"></i>
              {{ item.label }}
            </button>
          </div>
        </div>

        <!-- Form -->
        <div class="relative">
          <!-- Overlay when form is disabled -->
          <div v-if="isFormDisabled" class="absolute inset-0 bg-gray-100/80 backdrop-blur-sm z-50 rounded-lg flex items-center justify-center" style="min-height: 400px;">
            <div class="bg-white p-6 rounded-lg shadow-xl border-2 border-red-500 max-w-md text-center">
              <i class="fas fa-ban text-red-500 text-4xl mb-4"></i>
              <h3 class="text-lg font-bold text-red-800 mb-2">Form Dinonaktifkan</h3>
              <p class="text-sm text-gray-700 mb-4">
                Form tidak dapat diisi karena ada dokter lain yang sedang menginput nama pasien yang sama.
              </p>
              <p class="text-xs text-gray-600">
                Mohon tunggu atau koordinasi dengan dokter tersebut terlebih dahulu.
              </p>
            </div>
          </div>
          
          <form @submit.prevent="submitForm" class="space-y-8" :class="{ 'opacity-50 pointer-events-none': isFormDisabled }">
          <!-- Informasi Pasien -->
          <div class="bg-white/90 backdrop-blur-sm shadow-2xl rounded-2xl p-8 border border-white/20 hover:shadow-3xl transition-all duration-300">
            <div class="flex items-center mb-8">
              <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg mr-4">
                <i class="fas fa-user text-white text-xl"></i>
              </div>
              <h2 class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-blue-700 bg-clip-text text-transparent">
                Informasi Pasien
              </h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-6 border border-blue-200/50 hover:shadow-lg transition-all duration-300">
                <div class="flex items-center mb-3">
                  <i class="fas fa-user-tag text-blue-500 mr-2"></i>
                  <label class="text-sm font-semibold text-blue-700 uppercase tracking-wide">Nama Pasien</label>
                </div>
                <p class="text-xl font-bold text-gray-900">{{ psn?.nm_p }}</p>
              </div>
              <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-6 border border-green-200/50 hover:shadow-lg transition-all duration-300">
                <div class="flex items-center mb-3">
                  <i class="fas fa-id-card text-green-500 mr-2"></i>
                  <label class="text-sm font-semibold text-green-700 uppercase tracking-wide">NIK</label>
                </div>
                <p class="text-xl font-bold text-gray-900">{{ psn?.nik }}</p>
              </div>
              <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-6 border border-purple-200/50 hover:shadow-lg transition-all duration-300">
                <div class="flex items-center mb-3">
                  <i class="fas fa-shield-alt text-purple-500 mr-2"></i>
                  <label class="text-sm font-semibold text-purple-700 uppercase tracking-wide">No BPJS</label>
                </div>
                <p class="text-xl font-bold text-gray-900">{{ psn?.no_bpjs }}</p>
              </div>
              <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl p-6 border border-orange-200/50 hover:shadow-lg transition-all duration-300">
                <div class="flex items-center mb-3">
                  <i class="fas fa-map-marker-alt text-orange-500 mr-2"></i>
                  <label class="text-sm font-semibold text-orange-700 uppercase tracking-wide">Alamat</label>
                </div>
                <p class="text-lg font-bold text-gray-900">{{ psn?.almt_B }}</p>
              </div>
            </div>
          </div>

          <!-- Data Kunjungan -->
          <div class="bg-white/90 backdrop-blur-sm shadow-2xl rounded-2xl p-8 border border-white/20 hover:shadow-3xl transition-all duration-300">
            <div class="flex items-center mb-8">
              <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg mr-4">
                <i class="fas fa-calendar-plus text-white text-xl"></i>
              </div>
              <h2 class="text-3xl font-bold bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent">
                Data Kunjungan
              </h2>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- No Registrasi -->
              <div class="group">
                <label for="no_reg" class="block text-sm font-semibold text-gray-700 mb-3 flex items-center">
                  <i class="fas fa-hashtag text-blue-500 mr-2"></i>
                  No Registrasi <span class="text-red-500 ml-1">*</span>
                </label>
                <div class="relative">
                  <input
                    type="text"
                    id="no_reg"
                    v-model="form.no_reg"
                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-300 bg-white/80 backdrop-blur-sm hover:border-blue-300 group-hover:shadow-lg"
                    placeholder="Masukkan nomor registrasi"
                    required
                  />
                  <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                    <i class="fas fa-edit text-gray-400"></i>
                  </div>
                </div>
                <div v-if="errors.no_reg" class="text-red-500 text-sm mt-2 flex items-center">
                  <i class="fas fa-exclamation-circle mr-1"></i>{{ errors.no_reg }}
                </div>
              </div>

              <!-- Tanggal Registrasi -->
              <div class="group">
                <label for="tgl_reg" class="block text-sm font-semibold text-gray-700 mb-3 flex items-center">
                  <i class="fas fa-calendar text-green-500 mr-2"></i>
                  Tanggal Registrasi <span class="text-red-500 ml-1">*</span>
                </label>
                <div class="relative">
                  <input
                    type="date"
                    id="tgl_reg"
                    v-model="form.tgl_reg"
                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-green-500/20 focus:border-green-500 transition-all duration-300 bg-white/80 backdrop-blur-sm hover:border-green-300 group-hover:shadow-lg"
                    required
                  />
                  <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                    <i class="fas fa-calendar-alt text-gray-400"></i>
                  </div>
                </div>
                <div v-if="errors.tgl_reg" class="text-red-500 text-sm mt-2 flex items-center">
                  <i class="fas fa-exclamation-circle mr-1"></i>{{ errors.tgl_reg }}
                </div>
              </div>

              <!-- MRN -->
              <div class="group">
                <label for="mrn" class="block text-sm font-semibold text-gray-700 mb-3 flex items-center">
                  <i class="fas fa-id-badge text-purple-500 mr-2"></i>
                  MRN (Medical Record Number) <span class="text-red-500 ml-1">*</span>
                </label>
                <div class="relative">
                  <input
                    type="text"
                    id="mrn"
                    v-model="form.mrn"
                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-purple-500/20 focus:border-purple-500 transition-all duration-300 bg-white/80 backdrop-blur-sm hover:border-purple-300 group-hover:shadow-lg"
                    placeholder="Masukkan MRN"
                    required
                  />
                  <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                    <i class="fas fa-barcode text-gray-400"></i>
                  </div>
                </div>
                <div v-if="errors.mrn" class="text-red-500 text-sm mt-2 flex items-center">
                  <i class="fas fa-exclamation-circle mr-1"></i>{{ errors.mrn }}
                </div>
              </div>

            <!-- No Invoice -->
            <div>
              <label for="no_inv" class="block text-sm font-medium text-gray-700 mb-2">
                No Invoice
              </label>
              <input
                type="text"
                id="no_inv"
                v-model="form.no_inv"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                placeholder="Masukkan nomor invoice"
              />
              <div v-if="errors.no_inv" class="text-red-500 text-sm mt-1">{{ errors.no_inv }}</div>
            </div>

            <!-- Tanggal Invoice -->
            <div>
              <label for="tgl_inv" class="block text-sm font-medium text-gray-700 mb-2">
                Tanggal Invoice
              </label>
              <input
                type="date"
                id="tgl_inv"
                v-model="form.tgl_inv"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              />
              <div v-if="errors.tgl_inv" class="text-red-500 text-sm mt-1">{{ errors.tgl_inv }}</div>
            </div>

            <!-- Jenis Perawatan -->
            <div>
              <label for="perawatan" class="block text-sm font-medium text-gray-700 mb-2">
                Jenis Perawatan <span class="text-red-500">*</span>
              </label>
              <select
                id="perawatan"
                v-model="form.perawatan"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                required
              >
                <option value="">Pilih jenis perawatan</option>
                <option value="Rawat Jalan">Rawat Jalan</option>
                <option value="Rawat Inap">Rawat Inap</option>
                <option value="IGD">IGD (Instalasi Gawat Darurat)</option>
                <option value="ICU">ICU (Intensive Care Unit)</option>
                <option value="Operasi">Operasi</option>
                <option value="Konsultasi">Konsultasi</option>
              </select>
              <div v-if="errors.perawatan" class="text-red-500 text-sm mt-1">{{ errors.perawatan }}</div>
            </div>

            <!-- Penjamin -->
            <div class="group">
              <label for="penjamin" class="block text-sm font-semibold text-gray-700 mb-3 flex items-center">
                <i class="fas fa-shield-alt text-purple-500 mr-2"></i>
                Penjamin <span class="text-red-500 ml-1">*</span>
              </label>
              <div class="relative">
                <button
                  type="button"
                  @click="openEselonModal"
                  :class="[
                    'w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-purple-500/20 focus:border-purple-500 transition-all duration-300 bg-white/80 backdrop-blur-sm hover:border-purple-300 group-hover:shadow-lg text-left flex items-center justify-between',
                    selectedEselon ? 'border-purple-500 bg-purple-50' : 'border-gray-200'
                  ]"
                >
                  <span :class="selectedEselon ? 'text-purple-700 font-medium' : 'text-gray-500'">
                    {{ selectedEselon ? selectedEselon.eselon_desc : 'Pilih Penjamin' }}
                    <span v-if="selectedEselon?.grp_eselon?.id" class="text-xs text-blue-600 ml-2">
                      (ID: {{ selectedEselon.grp_eselon.id }})
                    </span>
                  </span>
                  <i class="fas fa-chevron-down text-gray-400"></i>
                </button>
                <div v-if="selectedEselon && selectedEselon.grp_eselon" class="mt-2 text-sm text-gray-600">
                  <i class="fas fa-tag mr-1"></i>
                  {{ selectedEselon.grp_eselon.grp_eselon_desc }}
                </div>
              </div>
              <div v-if="errors.penjamin" class="text-red-500 text-sm mt-2 flex items-center">
                <i class="fas fa-exclamation-circle mr-1"></i>{{ errors.penjamin }}
              </div>
            </div>

            <!-- No SJP -->
            <div>
              <label for="no_sjp" class="block text-sm font-medium text-gray-700 mb-2">
                No SJP (Surat Jaminan Pelayanan)
              </label>
              <input
                type="text"
                id="no_sjp"
                v-model="form.no_sjp"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                placeholder="Masukkan nomor SJP"
              />
              <div v-if="errors.no_sjp" class="text-red-500 text-sm mt-1">{{ errors.no_sjp }}</div>
            </div>

          </div>
        </div>

          <!-- Action Buttons -->
          <div class="bg-white/90 backdrop-blur-sm shadow-2xl rounded-2xl p-8 border border-white/20 hover:shadow-3xl transition-all duration-300">
            <div class="flex flex-col sm:flex-row justify-end gap-4">
              <button
                type="button"
                @click="router.visit(`/pasien/${psn?.id}`)"
                class="px-8 py-4 bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 flex items-center justify-center font-medium"
              >
                <i class="fas fa-times mr-2"></i>Batal
              </button>
              <button
                type="submit"
                :disabled="isSubmitting || isFormDisabled"
                class="px-8 py-4 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 disabled:from-gray-400 disabled:to-gray-500 text-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 flex items-center justify-center font-medium disabled:transform-none"
              >
                <i v-if="isSubmitting" class="fas fa-spinner fa-spin mr-2"></i>
                <i v-else class="fas fa-save mr-2"></i>
                {{ isSubmitting ? 'Menyimpan...' : 'Simpan Kunjungan' }}
              </button>
            </div>
          </div>
        </form>
        </div>
      </div>
    </div>

    <!-- Eselon Selection Modal -->
    <div v-if="showEselonModal" class="fixed inset-0 z-50 overflow-y-auto" @click.self="closeEselonModal">
      <div class="flex min-h-screen items-center justify-center p-4">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity"></div>
        
        <!-- Modal -->
        <div class="relative bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[80vh] overflow-hidden animate-fade-in">
          <!-- Header -->
          <div class="bg-gradient-to-r from-purple-500 to-purple-600 px-6 py-4">
            <div class="flex items-center justify-between">
              <div class="flex items-center">
                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center mr-3">
                  <i class="fas fa-shield-alt text-white text-lg"></i>
                </div>
                <div>
                  <h3 class="text-xl font-bold text-white">Pilih Penjamin</h3>
                  <p class="text-purple-100 text-sm">Pilih jenis penjamin dari daftar eselon</p>
                </div>
              </div>
              <button
                @click="closeEselonModal"
                class="w-8 h-8 bg-white/20 hover:bg-white/30 rounded-lg flex items-center justify-center transition-colors"
              >
                <i class="fas fa-times text-white"></i>
              </button>
            </div>
          </div>

          <!-- Search -->
          <div class="p-6 border-b border-gray-200">
            <div class="relative">
              <input
                v-model="searchEselon"
                type="text"
                placeholder="Cari penjamin..."
                class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
              />
              <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
            </div>
          </div>

          <!-- Content -->
          <div class="max-h-96 overflow-y-auto">
            <div v-if="filteredEselons.length === 0" class="p-8 text-center text-gray-500">
              <i class="fas fa-search text-4xl mb-4"></i>
              <p>Tidak ada penjamin yang ditemukan</p>
            </div>
            
            <div v-else class="p-4 space-y-2">
              <div
                v-for="eselon in filteredEselons"
                :key="eselon.id"
                @click="selectEselon(eselon)"
                class="p-4 border border-gray-200 rounded-xl hover:border-purple-300 hover:bg-purple-50 cursor-pointer transition-all duration-200 hover:shadow-md"
              >
                <div class="flex items-center justify-between">
                  <div class="flex-1">
                    <h4 class="font-semibold text-gray-900">
                      {{ eselon.eselon_desc }}
                      <span v-if="eselon.grp_eselon?.id" class="text-xs text-blue-600 ml-2">
                        (ID: {{ eselon.grp_eselon.id }})
                      </span>
                    </h4>
                    <p v-if="eselon.grp_eselon" class="text-sm text-gray-600 mt-1">
                      <i class="fas fa-tag mr-1"></i>
                      {{ eselon.grp_eselon.grp_eselon_desc }}
                    </p>
                  </div>
                  <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-shield-alt text-purple-600"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Footer -->
          <div class="p-6 border-t border-gray-200 bg-gray-50">
            <div class="flex justify-end">
              <button
                @click="closeEselonModal"
                class="px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition-colors"
              >
                Batal
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, reactive, onMounted, onUnmounted, computed, watch } from 'vue'
import { Head, router, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
  psn: {
    type: Object,
    required: true
  },
  polis: {
    type: Array,
    default: () => []
  },
  eselons: {
    type: Array,
    default: () => []
  }
})

const isSubmitting = ref(false)
const errors = ref({})
const showEselonModal = ref(false)
const selectedEselon = ref(null)
const searchEselon = ref('')

// Patient Name Conflict State
const patientNameConflicts = ref([])
const hasPatientNameConflict = ref(false)
const isCheckingPatientName = ref(false)
let patientNameCheckTimeout = null
let patientNameTrackingInterval = null

// Computed property to check if form should be disabled
const isFormDisabled = computed(() => {
  return hasPatientNameConflict.value && patientNameConflicts.value.length > 0
})

// Get CSRF token
function getCsrfToken() {
  return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
}

// Dynamic polis data for kunjungan selection
const enumKunjungan = computed(() => {
  return props.polis.map(poli => ({
    value: poli.poli_desc,
    label: poli.poli_desc,
    icon: getPoliIcon(poli.poli_desc)
  }))
})

// Filtered eselons based on search
const filteredEselons = computed(() => {
  if (!searchEselon.value) return props.eselons
  
  return props.eselons.filter(eselon => 
    eselon.eselon_desc.toLowerCase().includes(searchEselon.value.toLowerCase()) ||
    eselon.grp_eselon?.grp_eselon_desc?.toLowerCase().includes(searchEselon.value.toLowerCase())
  )
})

// Modal functions
const openEselonModal = () => {
  showEselonModal.value = true
}

const closeEselonModal = () => {
  showEselonModal.value = false
  searchEselon.value = ''
}

const selectEselon = (eselon) => {
  selectedEselon.value = eselon
  form.penjamin = eselon.eselon_desc
  form.grp_eselon_id = eselon.grp_eselon ? eselon.grp_eselon.id : null
  closeEselonModal()
}

// Function to get appropriate icon based on polis description
function getPoliIcon(poliDesc) {
  const desc = poliDesc.toLowerCase()
  if (desc.includes('umum')) return 'fas fa-user-md'
  if (desc.includes('gigi')) return 'fas fa-tooth'
  if (desc.includes('kia') || desc.includes('ibu') || desc.includes('anak')) return 'fas fa-baby'
  if (desc.includes('lab') || desc.includes('laboratorium')) return 'fas fa-vials'
  if (desc.includes('apotek') || desc.includes('obat')) return 'fas fa-pills'
  if (desc.includes('jantung')) return 'fas fa-heartbeat'
  if (desc.includes('mata')) return 'fas fa-eye'
  if (desc.includes('kulit')) return 'fas fa-hand-paper'
  if (desc.includes('saraf')) return 'fas fa-brain'
  if (desc.includes('bedah')) return 'fas fa-cut'
  return 'fas fa-hospital' // Default icon
}

// Set default values
const form = reactive({
  psn_id: props.psn.id,
  no_reg: '',
  tgl_reg: new Date().toISOString().split('T')[0], // Today's date
  nm_p: props.psn.nm_p,
  mrn: '',
  almt_B: props.psn.almt_B,
  no_inv: '',
  tgl_inv: '',
  perawatan: '',
  penjamin: '',
  grp_eselon_id: null,
  no_sjp: '',
  kunjungan: props.polis.length > 0 ? props.polis[0].poli_desc : '' // Default to first polis
})

// Generate automatic MRN if empty
const generateMRN = () => {
  if (!form.mrn) {
    const today = new Date()
    const year = today.getFullYear()
    const month = String(today.getMonth() + 1).padStart(2, '0')
    const day = String(today.getDate()).padStart(2, '0')
    const random = Math.floor(Math.random() * 1000).toString().padStart(3, '0')
    form.mrn = `MRN${year}${month}${day}${random}`
  }
}

// Generate automatic No Registrasi if empty
const generateNoReg = () => {
  if (!form.no_reg) {
    const today = new Date()
    const year = today.getFullYear()
    const month = String(today.getMonth() + 1).padStart(2, '0')
    const day = String(today.getDate()).padStart(2, '0')
    const hours = String(today.getHours()).padStart(2, '0')
    const minutes = String(today.getMinutes()).padStart(2, '0')
    const seconds = String(today.getSeconds()).padStart(2, '0')
    const random = Math.floor(Math.random() * 1000).toString().padStart(3, '0')
    form.no_reg = `REG${year}${month}${day}${hours}${minutes}${seconds}${random}`
  }
}

// Track patient name inputting
async function trackPatientNameInputting() {
  if (!form.nm_p || form.nm_p.trim() === '') return
  
  try {
    await fetch('/transaksi/track-patient-name-inputting', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': getCsrfToken(),
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: JSON.stringify({
        nm_p: form.nm_p
      })
    })
  } catch (error) {
    console.error('Error tracking patient name input:', error)
  }
}

// Check for patient name conflicts
async function checkPatientNameConflict() {
  if (!form.nm_p || form.nm_p.trim() === '') {
    patientNameConflicts.value = []
    hasPatientNameConflict.value = false
    return
  }
  
  isCheckingPatientName.value = true
  try {
    const response = await fetch('/transaksi/check-patient-name-conflict', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': getCsrfToken(),
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: JSON.stringify({
        nm_p: form.nm_p
      })
    })
    
    const data = await response.json()
    hasPatientNameConflict.value = data.has_conflict || false
    patientNameConflicts.value = data.conflicts || []
  } catch (error) {
    console.error('Error checking patient name conflict:', error)
  } finally {
    isCheckingPatientName.value = false
  }
}

// Stop tracking patient name
async function stopTrackingPatientName() {
  try {
    await fetch('/transaksi/stop-tracking-patient-name', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': getCsrfToken(),
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: JSON.stringify({
        nm_p: form.nm_p
      })
    })
  } catch (error) {
    console.error('Error stopping patient name tracking:', error)
  }
}

// Debounce helper
function debounce(func, wait) {
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(patientNameCheckTimeout)
      func(...args)
    }
    clearTimeout(patientNameCheckTimeout)
    patientNameCheckTimeout = setTimeout(later, wait)
  }
}

const debouncedCheckPatientNameConflict = debounce(checkPatientNameConflict, 800)

const submitForm = async () => {
  // Prevent submission if form is disabled due to patient name conflict
  if (isFormDisabled.value) {
    alert('Form tidak dapat disubmit karena ada dokter lain yang sedang menginput nama pasien yang sama. Mohon tunggu atau koordinasi terlebih dahulu.')
    return
  }
  
  isSubmitting.value = true
  errors.value = {}

  // Validate kunjungan selection
  if (!form.kunjungan || form.kunjungan.trim() === '') {
    errors.value.kunjungan = 'Jenis kunjungan harus dipilih'
    isSubmitting.value = false
    return
  }

  // Validate penjamin selection
  if (!selectedEselon.value || !form.grp_eselon_id) {
    errors.value.penjamin = 'Penjamin harus dipilih'
    isSubmitting.value = false
    return
  }

  // Stop tracking patient name before submit
  await stopTrackingPatientName()

  try {
    // Generate automatic values if empty
    generateMRN()
    generateNoReg()

    // Convert reactive form to plain object
    const formData = {
      psn_id: form.psn_id,
      no_reg: form.no_reg,
      tgl_reg: form.tgl_reg,
      nm_p: form.nm_p,
      mrn: form.mrn,
      almt_B: form.almt_B,
      no_inv: form.no_inv || '',
      tgl_inv: form.tgl_inv || '',
      perawatan: form.perawatan,
      penjamin: form.penjamin,
      grp_eselon_id: form.grp_eselon_id,
      no_sjp: form.no_sjp || '',
      kunjungan: form.kunjungan.trim()
    }

    // Debug: log form data before submission
    console.log('Form data being submitted:', formData)
    console.log('Selected eselon:', selectedEselon.value)

    await router.post('/pasien/kunjungan', formData, {
      onSuccess: async () => {
        // Stop tracking patient name after successful create
        await stopTrackingPatientName()
        
        // Redirect to patient detail page
        router.visit(`/pasien/${props.psn.id}`)
      },
      onError: (err) => {
        console.error('Form submission errors:', err)
        errors.value = err
      },
      onFinish: () => {
        isSubmitting.value = false
      }
    })
  } catch (error) {
    console.error('Error submitting form:', error)
    isSubmitting.value = false
  }
}

onMounted(() => {
  // Set default values when component mounts
  generateMRN()
  generateNoReg()
  
  // Track patient name inputting and check conflicts
  if (form.nm_p && form.nm_p.trim() !== '') {
    trackPatientNameInputting()
    checkPatientNameConflict()
  }
  
  // Set up periodic tracking (every 30 seconds)
  patientNameTrackingInterval = setInterval(() => {
    if (form.nm_p && form.nm_p.trim() !== '') {
      trackPatientNameInputting()
    }
  }, 30000)
  
  // Watch for changes in nm_p
  watch(() => form.nm_p, () => {
    if (form.nm_p && form.nm_p.trim() !== '') {
      trackPatientNameInputting()
      debouncedCheckPatientNameConflict()
    } else {
      patientNameConflicts.value = []
      hasPatientNameConflict.value = false
    }
  })
  
  // Listen for page unload to stop tracking
  window.addEventListener('beforeunload', stopTrackingPatientName)
})

onUnmounted(() => {
  // Clear intervals
  if (patientNameCheckTimeout) {
    clearTimeout(patientNameCheckTimeout)
  }
  if (patientNameTrackingInterval) {
    clearInterval(patientNameTrackingInterval)
  }
  
  // Stop tracking patient name
  stopTrackingPatientName()
  
  // Remove event listeners
  window.removeEventListener('beforeunload', stopTrackingPatientName)
})
</script>

<style scoped>
/* Custom animations */
.slide-fade-enter-active {
  transition: all 0.3s ease-out;
}

.slide-fade-leave-active {
  transition: all 0.3s cubic-bezier(1, 0.5, 0.8, 1);
}

.slide-fade-enter-from,
.slide-fade-leave-to {
  transform: translateX(20px);
  opacity: 0;
}

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

/* Hover effects */
.group:hover {
  transform: translateY(-2px);
}

/* Glassmorphism effect */
.backdrop-blur-md {
  backdrop-filter: blur(12px);
}

/* Custom gradient text */
.bg-clip-text {
  -webkit-background-clip: text;
  background-clip: text;
}

/* Enhanced shadows */
.shadow-3xl {
  box-shadow: 0 35px 60px -12px rgba(0, 0, 0, 0.25);
}

/* Smooth transitions */
* {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Custom scrollbar */
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

/* Input focus effects */
input:focus {
  transform: translateY(-1px);
}

select:focus {
  transform: translateY(-1px);
}

/* Button hover effects */
button:hover:not(:disabled) {
  transform: translateY(-2px) scale(1.02);
}

button:active:not(:disabled) {
  transform: translateY(0) scale(0.98);
}

/* Form validation styles */
input:invalid {
  border-color: #ef4444;
  box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
}

input:valid {
  border-color: #10b981;
  box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
}
</style>
