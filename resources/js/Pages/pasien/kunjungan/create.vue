<template>
  <AuthenticatedLayout>
    <Head title="Tambah Kunjungan Pasien" />
    
    <div class="max-w-4xl mx-auto py-8 px-4">
      <!-- Header -->
      <div class="flex justify-between items-center mb-6">
        <div>
          <h1 class="text-3xl font-bold text-gray-900">Tambah Kunjungan</h1>
          <p class="text-gray-600 mt-1">Tambah kunjungan baru untuk {{ psn?.nm_p }}</p>
        </div>
        <button 
          @click="router.visit(`/pasien/${psn?.id}`)"
          class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg shadow transition"
        >
          <i class="fas fa-arrow-left mr-2"></i>Kembali ke Detail
        </button>
      </div>

      <!-- Tombol Enum Kunjungan -->
      <div class="mb-6 flex flex-wrap gap-2">
        <span class="font-semibold text-gray-700 mr-2">Enum Kunjungan:</span>
        <button
          v-for="(item, idx) in enumKunjungan"
          :key="item.value"
          type="button"
          @click="form.kunjungan = item.value"
          :class="[
            'px-3 py-1 rounded-lg border transition text-sm',
            form.kunjungan === item.value
              ? 'bg-blue-600 text-white border-blue-700'
              : 'bg-white text-gray-700 border-gray-300 hover:bg-blue-50'
          ]"
        >
          <i :class="item.icon" class="mr-1"></i>{{ item.label }}
        </button>
      </div>

      <!-- Form -->
      <form @submit.prevent="submitForm" class="space-y-8">
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
            <i class="fas fa-calendar-plus mr-3 text-green-600"></i>
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
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
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
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
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
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
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
            <div>
              <label for="penjamin" class="block text-sm font-medium text-gray-700 mb-2">
                Penjamin <span class="text-red-500">*</span>
              </label>
              <select
                id="penjamin"
                v-model="form.penjamin"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
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
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                placeholder="Masukkan nomor SJP"
              />
              <div v-if="errors.no_sjp" class="text-red-500 text-sm mt-1">{{ errors.no_sjp }}</div>
            </div>

            <!-- ICD Code -->
            <div>
              <label for="icd" class="block text-sm font-medium text-gray-700 mb-2">
                ICD Code (International Classification of Diseases)
              </label>
              <input
                type="text"
                id="icd"
                v-model="form.icd"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                placeholder="Masukkan kode ICD"
              />
              <div v-if="errors.icd" class="text-red-500 text-sm mt-1">{{ errors.icd }}</div>
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
            :disabled="isSubmitting"
            class="px-6 py-3 bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white rounded-lg shadow transition flex items-center"
          >
            <i v-if="isSubmitting" class="fas fa-spinner fa-spin mr-2"></i>
            <i v-else class="fas fa-save mr-2"></i>
            {{ isSubmitting ? 'Menyimpan...' : 'Simpan Kunjungan' }}
          </button>
        </div>
      </form>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { Head, router, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
  psn: {
    type: Object,
    required: true
  }
})

const isSubmitting = ref(false)
const errors = ref({})

// Enum kunjungan (jenis perawatan) untuk tombol
const enumKunjungan = [
  { value: "umum", label: "Poli Umum", icon: "fas fa-user-md" },
  { value: "gigi", label: "Poli Gigi", icon: "fas fa-tooth" },
  { value: "kia", label: "Poli KIA", icon: "fas fa-baby" },
  { value: "laboratorium", label: "Laboratorium", icon: "fas fa-vials" },
  { value: "apotek", label: "Apotek", icon: "fas fa-pills" },
]

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
  no_sjp: '',
  icd: '',
  kunjungan: 'umum' // Default to umum
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
    const random = Math.floor(Math.random() * 100).toString().padStart(2, '0')
    form.no_reg = `REG${year}${month}${day}${random}`
  }
}

const submitForm = async () => {
  isSubmitting.value = true
  errors.value = {}

  try {
    // Generate automatic values if empty
    generateMRN()
    generateNoReg()

    await router.post('/pasien/kunjungan', form, {
      onSuccess: () => {
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
  generateMRN()
  generateNoReg()
})
</script>

<style scoped>
/* Custom styles if needed */
</style>
