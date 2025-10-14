<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

// Props
const props = defineProps({
  tindakanTarif: {
    type: Object,
    required: true
  },
  auth: {
    type: Object,
    required: true
  },
  tindakanQs: {
    type: Array,
    required: true
  },
  grpEselons: {
    type: Array,
    required: true
  }
})

// Ambil nama user dari props.auth.user.name, fallback ke username/email jika tidak ada
const userName = computed(() => {
  if (props.auth && props.auth.user) {
    return props.auth.user.name || props.auth.user.username || props.auth.user.email || ''
  }
  return ''
})

const form = useForm({
  tindakan_q_id: '',
  eselon_grp_id: '',
  tarif: '',
  aktif: 'Y',
  update_date: new Date().toISOString().split('T')[0],
  update_by: '',
})

// Initialize form data when props are available
const initializeForm = () => {
  if (props.tindakanTarif) {
    form.tindakan_q_id = props.tindakanTarif.tindakan_q_id || ''
    form.eselon_grp_id = props.tindakanTarif.eselon_grp_id || ''
    form.tarif = props.tindakanTarif.tarif || ''
    form.aktif = props.tindakanTarif.aktif || 'Y'
    form.update_date = props.tindakanTarif.update_date || new Date().toISOString().split('T')[0]
    form.update_by = props.tindakanTarif.update_by || userName.value
  }
}

// Initialize form when component mounts or props change
onMounted(() => {
  initializeForm()
})

watch(() => props.tindakanTarif, () => {
  initializeForm()
}, { immediate: true })

const submitForm = () => {
  // Pastikan update_by selalu terisi user yang login saat submit
  form.update_by = userName.value
  
  // Pastikan update_date selalu terisi dengan tanggal saat ini saat submit
  form.update_date = new Date().toISOString().split('T')[0]
  
  // Check if tindakanTarif exists and has an id
  if (!props.tindakanTarif || !props.tindakanTarif.id) {
    console.error('TindakanTarif data is missing or invalid')
    return
  }
  
  form.put(route('tindakan-tarif.update', { tindakan_tarif: props.tindakanTarif.id }), {
    onSuccess: () => {
      // Redirect will be handled by the controller
    }
  })
}

// Format currency input with decimal support
const formatCurrency = (value) => {
  if (!value) return ''
  
  // Remove non-numeric characters except decimal point
  const numericValue = value.replace(/[^\d.,]/g, '')
  // Replace comma with dot for decimal separator
  const normalizedValue = numericValue.replace(',', '.')
  
  // Convert to number
  const numValue = parseFloat(normalizedValue)
  if (isNaN(numValue)) return ''
  
  // Format as currency with decimal support
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
    maximumFractionDigits: 2
  }).format(numValue)
}

// Convert formatted currency back to raw number for storage
const parseCurrency = (formattedValue) => {
  if (!formattedValue) return ''
  
  // Remove currency symbols and thousand separators
  let cleanValue = formattedValue.replace(/[Rp\s]/g, '')
  
  // Remove thousand separators (dots in Indonesian format)
  cleanValue = cleanValue.replace(/\./g, '')
  
  // Replace comma with dot for decimal separator
  cleanValue = cleanValue.replace(',', '.')
  
  // Return only numeric value
  return cleanValue
}

// Computed property untuk menampilkan nilai yang sudah diformat
const displayTarif = computed({
  get: () => {
    if (!form.tarif) return ''
    return formatCurrency(form.tarif)
  },
  set: (value) => {
    form.tarif = parseCurrency(value)
  }
})
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Edit Tindakan Tarif" />
    
    <div class="min-h-screen bg-cover bg-center p-6" style="background-image: url('/images/bg-login.png')">        
      <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
          <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
              <div class="flex items-center space-x-3">
                <Link
                  :href="route('tindakan-tarif.index')"
                  class="p-2 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors"
                >
                  <i class="fas fa-arrow-left text-gray-600"></i>
                </Link>
                <div class="p-2 bg-indigo-100 rounded-lg">
                  <i class="fas fa-edit text-indigo-600 text-xl"></i>
                </div>
                <div>
                  <h1 class="text-2xl font-bold text-gray-900">Edit Tindakan Tarif</h1>
                  <p class="text-sm text-gray-600">Ubah data tarif tindakan medis</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Form -->
          <form @submit.prevent="submitForm" class="p-6">
            <div class="space-y-6">
              <!-- Tindakan Q -->
              <div>
                <label for="tindakan_q_id" class="block text-sm font-medium text-gray-700 mb-2">
                  Tindakan Q <span class="text-red-500">*</span>
                </label>
                <select
                  id="tindakan_q_id"
                  v-model="form.tindakan_q_id"
                  required
                  class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                >
                  <option value="">Pilih Tindakan Q</option>
                  <option 
                    v-for="tindakanQ in tindakanQs" 
                    :key="tindakanQ.id" 
                    :value="tindakanQ.id"
                  >
                    {{ tindakanQ.tindakan_q_desc }}
                  </option>
                </select>
                <div v-if="form.errors.tindakan_q_id" class="mt-1 text-sm text-red-600">
                  {{ form.errors.tindakan_q_id }}
                </div>
              </div>

              <!-- Eselon Group -->
              <div>
                <label for="eselon_grp_id" class="block text-sm font-medium text-gray-700 mb-2">
                  Eselon Group <span class="text-red-500">*</span>
                </label>
                <select
                  id="eselon_grp_id"
                  v-model="form.eselon_grp_id"
                  required
                  class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                >
                  <option value="">Pilih Eselon Group</option>
                  <option 
                    v-for="grpEselon in grpEselons" 
                    :key="grpEselon.id" 
                    :value="grpEselon.id"
                  >
                    {{ grpEselon.grp_eselon_desc }}
                  </option>
                </select>
                <div v-if="form.errors.eselon_grp_id" class="mt-1 text-sm text-red-600">
                  {{ form.errors.eselon_grp_id }}
                </div>
              </div>

              <!-- Tarif -->
              <div>
                <label for="tarif" class="block text-sm font-medium text-gray-700 mb-2">
                  Tarif <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                  <input
                    id="tarif"
                    v-model="displayTarif"
                    type="text"
                    required
                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    placeholder="Masukkan tarif tindakan"
                  />
                </div>
                <div v-if="form.errors.tarif" class="mt-1 text-sm text-red-600">
                  {{ form.errors.tarif }}
                </div>
              </div>

              <!-- Status Aktif -->
              <div>
                <label for="aktif" class="block text-sm font-medium text-gray-700 mb-2">
                  Status <span class="text-red-500">*</span>
                </label>
                <select
                  id="aktif"
                  v-model="form.aktif"
                  required
                  class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                >
                  <option value="Y">Aktif</option>
                  <option value="N">Tidak Aktif</option>
                </select>
                <div v-if="form.errors.aktif" class="mt-1 text-sm text-red-600">
                  {{ form.errors.aktif }}
                </div>
              </div>

              <!-- Tanggal Update -->
              <div>
                <label for="update_date" class="block text-sm font-medium text-gray-700 mb-2">
                  Tanggal Update <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                  <input
                    id="update_date"
                    v-model="form.update_date"
                    type="date"
                    required
                    readonly
                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm bg-blue-50"
                  />
                  <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <i class="fas fa-calendar text-blue-500"></i>
                  </div>
                </div>
                <p class="mt-1 text-xs text-blue-600">
                  <i class="fas fa-info-circle mr-1"></i>
                  Tanggal akan otomatis terisi dengan tanggal saat ini
                </p>
                <div v-if="form.errors.update_date" class="mt-1 text-sm text-red-600">
                  {{ form.errors.update_date }}
                </div>
              </div>

              <!-- Diupdate Oleh -->
              <div>
                <label for="update_by" class="block text-sm font-medium text-gray-700 mb-2">
                  Diupdate Oleh
                </label>
                <input
                  id="update_by"
                  v-model="form.update_by"
                  type="text"
                  class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                  placeholder="Masukkan nama pengupdate"
                  readonly
                />
                <div v-if="form.errors.update_by" class="mt-1 text-sm text-red-600">
                  {{ form.errors.update_by }}
                </div>
              </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end space-x-3 mt-8 pt-6 border-t border-gray-200">
              <Link
                :href="route('tindakan-tarif.index')"
                class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
              >
                <i class="fas fa-times mr-2"></i>
                Batal
              </Link>
              <button
                type="submit"
                :disabled="form.processing || !props.tindakanTarif || !props.tindakanTarif.id"
                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                <i v-if="form.processing" class="fas fa-spinner fa-spin mr-2"></i>
                <i v-else class="fas fa-save mr-2"></i>
                {{ form.processing ? 'Menyimpan...' : 'Simpan' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
