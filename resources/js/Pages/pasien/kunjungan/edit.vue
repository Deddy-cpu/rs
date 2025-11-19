<template>
  <AuthenticatedLayout>
    <Head title="Edit Kunjungan Pasien" />
    
    <div class="max-w-4xl mx-auto py-8 px-4">
      <!-- Header -->
      <div class="flex justify-between items-center mb-6">
        <div>
          <h1 class="text-3xl font-bold text-gray-900">Edit Kunjungan</h1>
          <p class="text-gray-600 mt-1">Edit kunjungan untuk {{ psn?.nm_p }}</p>
        </div>
        <button 
          @click="router.visit(`/pasien/${psn?.id}`)"
          class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg shadow transition"
        >
          <i class="fas fa-arrow-left mr-2"></i>Kembali ke Detail
        </button>
      </div>

      <!-- Active Editing Warning Banner -->
      <div v-if="isLockedByOther" class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6 animate-pulse">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <i class="fas fa-exclamation-triangle text-yellow-400 text-xl"></i>
          </div>
          <div class="ml-3 flex-1">
            <p class="text-sm text-yellow-800">
              <span class="font-bold">⚠️ PERINGATAN:</span> 
              <span class="font-semibold text-red-600">{{ lockInfo.locked_by }}</span> 
              sedang <span class="font-semibold">mengubah data ini</span> saat ini!
            </p>
            <p class="text-xs text-yellow-700 mt-1">
              <i class="fas fa-clock mr-1"></i>
              Dimulai sejak {{ formatDateTime(lockInfo.locked_since) }}
            </p>
            <p class="text-xs text-yellow-700 mt-1 font-medium">
              Jika Anda melanjutkan mengedit, data bisa saling bertabrakan. Mohon tunggu atau koordinasi dengan dokter tersebut.
            </p>
          </div>
        </div>
      </div>

      <!-- Patient Name Conflict Warning Banner -->
      <transition name="slide-fade">
        <div v-if="hasPatientNameConflict && patientNameConflicts.length > 0" class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg shadow-md">
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

      <!-- Tombol Enum Kunjungan -->
      <div class="mb-6 flex flex-wrap gap-2">
        <span class="font-semibold text-gray-700 mr-2">Enum Kunjungan:</span>
        <button
          v-for="(item, idx) in enumKunjungan"
          :key="item.value"
          type="button"
          @click="form.kunjungan = item.value"
          :disabled="isFormDisabled"
          :class="[
            'px-3 py-1 rounded-lg border transition text-sm',
            form.kunjungan === item.value
              ? 'bg-blue-600 text-white border-blue-700'
              : 'bg-white text-gray-700 border-gray-300 hover:bg-blue-50',
            isFormDisabled ? 'opacity-50 cursor-not-allowed' : ''
          ]"
        >
          <i :class="item.icon" class="mr-1"></i>{{ item.label }}
        </button>
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
        <div class="bg-blue-50 rounded-xl p-6">
          <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-user mr-3 text-blue-600"></i>
            Informasi Pasien
          </h2>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Nama Pasien</label>
              <p class="text-lg font-semibold text-gray-900">{{ psn?.nm_p }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">NIK</label>
              <p class="text-lg font-semibold text-gray-900">{{ psn?.nik }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">No BPJS</label>
              <p class="text-lg font-semibold text-gray-900">{{ psn?.no_bpjs }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
              <p class="text-lg font-semibold text-gray-900">{{ psn?.almt_B }}</p>
            </div>
          </div>
        </div>

        <!-- Data Kunjungan -->
        <div class="bg-white shadow-lg rounded-xl p-8">
          <h2 class="text-2xl font-semibold text-gray-800 mb-6 flex items-center">
            <i class="fas fa-calendar-edit mr-3 text-green-600"></i>
            Data Kunjungan
          </h2>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- No Registrasi -->
            <div>
              <label for="no_reg" class="block text-sm font-medium text-gray-700 mb-2">
                No Registrasi <span class="text-red-500">*</span>
              </label>
              <input
                type="text"
                id="no_reg"
                v-model="form.no_reg"
                :disabled="isFormDisabled"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100 disabled:cursor-not-allowed"
                placeholder="Masukkan nomor registrasi"
                required
              />
              <div v-if="errors.no_reg" class="text-red-500 text-sm mt-1">{{ errors.no_reg }}</div>
            </div>

            <!-- Tanggal Registrasi -->
            <div>
              <label for="tgl_reg" class="block text-sm font-medium text-gray-700 mb-2">
                Tanggal Registrasi <span class="text-red-500">*</span>
              </label>
              <input
                type="date"
                id="tgl_reg"
                v-model="form.tgl_reg"
                :disabled="isFormDisabled"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100 disabled:cursor-not-allowed"
                required
              />
              <div v-if="errors.tgl_reg" class="text-red-500 text-sm mt-1">{{ errors.tgl_reg }}</div>
            </div>

            <!-- MRN -->
            <div>
              <label for="mrn" class="block text-sm font-medium text-gray-700 mb-2">
                MRN (Medical Record Number) <span class="text-red-500">*</span>
              </label>
              <input
                type="text"
                id="mrn"
                v-model="form.mrn"
                :disabled="isFormDisabled"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100 disabled:cursor-not-allowed"
                placeholder="Masukkan MRN"
                required
              />
              <div v-if="errors.mrn" class="text-red-500 text-sm mt-1">{{ errors.mrn }}</div>
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
                :disabled="isFormDisabled"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100 disabled:cursor-not-allowed"
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
                :disabled="isFormDisabled"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100 disabled:cursor-not-allowed"
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
                :disabled="isFormDisabled"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100 disabled:cursor-not-allowed"
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
            <div>
              <label for="penjamin" class="block text-sm font-medium text-gray-700 mb-2">
                Penjamin <span class="text-red-500">*</span>
              </label>
              <select
                id="penjamin"
                v-model="form.penjamin"
                :disabled="isFormDisabled"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100 disabled:cursor-not-allowed"
                required
              >
                <option value="">Pilih penjamin</option>
                <option value="BPJS">BPJS Kesehatan</option>
                <option value="Asuransi Swasta">Asuransi Swasta</option>
                <option value="Umum">Umum (Pribadi)</option>
                <option value="Perusahaan">Perusahaan</option>
                <option value="Pemerintah">Pemerintah</option>
              </select>
              <div v-if="errors.penjamin" class="text-red-500 text-sm mt-1">{{ errors.penjamin }}</div>
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
                :disabled="isFormDisabled"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100 disabled:cursor-not-allowed"
                placeholder="Masukkan nomor SJP"
              />
              <div v-if="errors.no_sjp" class="text-red-500 text-sm mt-1">{{ errors.no_sjp }}</div>
            </div>

          </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-end space-x-4">
          <button
            type="button"
            @click="router.visit(`/pasien/${psn?.id}`)"
            class="px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white rounded-lg shadow transition"
          >
            <i class="fas fa-times mr-2"></i>Batal
          </button>
          <button
            type="submit"
            :disabled="isSubmitting || isFormDisabled"
            class="px-6 py-3 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed text-white rounded-lg shadow transition flex items-center"
          >
            <i v-if="isSubmitting" class="fas fa-spinner fa-spin mr-2"></i>
            <i v-else class="fas fa-save mr-2"></i>
            {{ isSubmitting ? 'Menyimpan...' : 'Update Kunjungan' }}
          </button>
        </div>
      </form>
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
  kunjungan: {
    type: Object,
    required: true
  },
  polis: {
    type: Array,
    default: () => []
  }
})

const isSubmitting = ref(false)
const errors = ref({})

// Editing Lock State
const isLockedByOther = ref(false)
const lockInfo = ref({
  locked_by: null,
  locked_since: null
})
const activeEditors = ref([])
let lockCheckInterval = null

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

// Get kunjungan ID
const kunjunganId = computed(() => {
  return props.kunjungan?.id
})

// Get CSRF token
function getCsrfToken() {
  return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
}

// Format date time
function formatDateTime(dateString) {
  if (!dateString) return '-'
  try {
    const date = new Date(dateString)
    return date.toLocaleString('id-ID', {
      day: '2-digit',
      month: 'long',
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    })
  } catch (error) {
    return dateString
  }
}

// Check edit lock status
const checkEditLock = async () => {
  if (!kunjunganId.value) return
  
  try {
    const response = await fetch(`/transaksi/check-edit-lock/${kunjunganId.value}`)
    const data = await response.json()
    
    if (data.is_locked) {
      isLockedByOther.value = true
      lockInfo.value = {
        locked_by: data.locked_by,
        locked_since: data.locked_since
      }
    } else {
      isLockedByOther.value = false
      lockInfo.value = {
        locked_by: null,
        locked_since: null
      }
    }
    
    if (data.active_editors && Array.isArray(data.active_editors)) {
      activeEditors.value = data.active_editors
    } else {
      activeEditors.value = []
    }
    
    // Also check patient name conflict when checking lock
    if (form.nm_p && form.nm_p.trim() !== '') {
      await checkPatientNameConflict()
    }
  } catch (error) {
    console.error('Error checking edit lock:', error)
  }
}

// Acquire edit lock
const acquireEditLock = async () => {
  if (!kunjunganId.value) return
  
  try {
    await fetch('/transaksi/acquire-edit-lock', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': getCsrfToken(),
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: JSON.stringify({
        kunjungan_id: kunjunganId.value
      })
    })
  } catch (error) {
    console.error('Error acquiring edit lock:', error)
  }
}

// Release edit lock
const releaseEditLock = async () => {
  if (!kunjunganId.value) return
  
  try {
    await fetch('/transaksi/release-edit-lock', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': getCsrfToken()
      },
      body: JSON.stringify({
        kunjungan_id: kunjunganId.value
      })
    })
  } catch (error) {
    console.error('Error releasing edit lock:', error)
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

// Dynamic polis data for kunjungan selection
const enumKunjungan = computed(() => {
  return props.polis.map(poli => ({
    value: poli.poli_desc,
    label: poli.poli_desc,
    icon: getPoliIcon(poli.poli_desc)
  }))
})

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

// Initialize form with existing kunjungan data
const form = reactive({
  psn_id: props.psn.id,
  no_reg: props.kunjungan.no_reg || '',
  tgl_reg: props.kunjungan.tgl_reg || '',
  nm_p: props.psn.nm_p,
  mrn: props.kunjungan.mrn || '',
  almt_B: props.psn.almt_B,
  no_inv: props.kunjungan.no_inv || '',
  tgl_inv: props.kunjungan.tgl_inv || '',
  perawatan: props.kunjungan.perawatan || '',
  penjamin: props.kunjungan.penjamin || '',
  no_sjp: props.kunjungan.no_sjp || '',
  kunjungan: props.kunjungan.kunjungan || (props.polis.length > 0 ? props.polis[0].poli_desc : '')
})

const submitForm = async () => {
  // Prevent submission if form is disabled due to patient name conflict
  if (isFormDisabled.value) {
    alert('Form tidak dapat disubmit karena ada dokter lain yang sedang menginput nama pasien yang sama. Mohon tunggu atau koordinasi terlebih dahulu.')
    return
  }
  
  isSubmitting.value = true
  errors.value = {}
  
  // Stop tracking patient name before submit
  await stopTrackingPatientName()

  try {
    await router.put(`/pasien/${props.psn.id}/kunjungan/${props.kunjungan.id}`, form, {
      onSuccess: async () => {
        // Release edit lock and stop tracking after successful update
        await releaseEditLock()
        await stopTrackingPatientName()
        
        // Redirect to patient detail page
        router.visit(`/pasien/${props.psn.id}`)
      },
      onError: (err) => {
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
  if (!form.tgl_reg) {
    form.tgl_reg = new Date().toISOString().split('T')[0]
  }
  
  // Acquire edit lock when page loads
  if (kunjunganId.value) {
    acquireEditLock()
  }
  
  // Start checking for edit locks every 3 seconds
  checkEditLock()
  lockCheckInterval = setInterval(checkEditLock, 3000)
  
  // Track patient name inputting and check conflicts
  if (form.nm_p && form.nm_p.trim() !== '') {
    trackPatientNameInputting()
    checkPatientNameConflict()
  }
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
  
  // Listen for page unload to release lock
  window.addEventListener('beforeunload', releaseEditLock)
  window.addEventListener('beforeunload', stopTrackingPatientName)
})

onUnmounted(() => {
  // Clear intervals
  if (lockCheckInterval) {
    clearInterval(lockCheckInterval)
  }
  if (patientNameCheckTimeout) {
    clearTimeout(patientNameCheckTimeout)
  }
  if (patientNameTrackingInterval) {
    clearInterval(patientNameTrackingInterval)
  }
  
  // Release edit lock
  releaseEditLock()
  
  // Stop tracking patient name
  stopTrackingPatientName()
  
  // Remove event listeners
  window.removeEventListener('beforeunload', releaseEditLock)
  window.removeEventListener('beforeunload', stopTrackingPatientName)
})
</script>

<style scoped>
/* Custom styles if needed */
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
</style>
