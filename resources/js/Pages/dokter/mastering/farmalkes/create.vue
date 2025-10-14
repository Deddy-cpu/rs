<script setup>
import { ref, computed } from 'vue'
import { Head, Link, useForm, usePage } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

// Props
const props = defineProps({})

// Ambil nama user dari global auth data
const userName = computed(() => {
  const auth = usePage().props.auth
  if (auth && auth.user) {
    return auth.user.name || auth.user.username || auth.user.email || ''
  }
  return ''
})

const form = useForm({
  kode: '',
  nama_item: '',
  deskripsi: '',
  jenis: 'farmasi',
  kategori: '',
  satuan: '',
  harga: '',
  stok: 0,
  aktif: 'Y',
  update_by: userName.value,
})

const submitForm = () => {
  // Pastikan update_by selalu terisi user yang login saat submit
  form.update_by = userName.value
  form.post(route('farmalkes.store'), {
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
const displayHarga = computed({
  get: () => {
    if (!form.harga) return ''
    return formatCurrency(form.harga)
  },
  set: (value) => {
    form.harga = parseCurrency(value)
  }
})

// Jenis options
const jenisOptions = [
  { value: 'farmasi', label: 'Farmasi' },
  { value: 'alkes', label: 'Alat Kesehatan' }
]

// Kategori options based on jenis
const kategoriOptions = computed(() => {
  if (form.jenis === 'farmasi') {
    return [
      'Analgesik',
      'Antibiotik',
      'Vitamin',
      'Antiseptik',
      'Antihistamin',
      'Antasida',
      'Lainnya'
    ]
  } else if (form.jenis === 'alkes') {
    return [
      'Diagnostik',
      'Terapi',
      'Bedah',
      'Monitoring',
      'Rehabilitasi',
      'Lainnya'
    ]
  }
  return []
})

// Satuan options
const satuanOptions = [
  'Tablet',
  'Kapsul',
  'Botol',
  'Tube',
  'Unit',
  'Set',
  'Box',
  'Strip',
  'Vial',
  'Ampul',
  'Lainnya'
]
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Tambah Farmalkes" />
    
    <div class="min-h-screen bg-cover bg-center p-6" style="background-image: url('/images/bg-login.png')">        
      <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
          <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
              <div class="flex items-center space-x-3">
                <Link
                  :href="route('farmalkes.index')"
                  class="p-2 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors"
                >
                  <i class="fas fa-arrow-left text-gray-600"></i>
                </Link>
                <div class="p-2 bg-indigo-100 rounded-lg">
                  <i class="fas fa-plus text-indigo-600 text-xl"></i>
                </div>
                <div>
                  <h1 class="text-2xl font-bold text-gray-900">Tambah Farmalkes</h1>
                  <p class="text-sm text-gray-600">Buat data farmasi dan alat kesehatan baru</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Form -->
          <form @submit.prevent="submitForm" class="p-6">
            <div class="space-y-6">
              <!-- Kode -->
              <div>
                <label for="kode" class="block text-sm font-medium text-gray-700 mb-2">
                  Kode Item <span class="text-red-500">*</span>
                </label>
                <input
                  id="kode"
                  v-model="form.kode"
                  type="text"
                  required
                  class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                  placeholder="Masukkan kode item (contoh: FAR001)"
                />
                <div v-if="form.errors.kode" class="mt-1 text-sm text-red-600">
                  {{ form.errors.kode }}
                </div>
              </div>

              <!-- Nama Item -->
              <div>
                <label for="nama_item" class="block text-sm font-medium text-gray-700 mb-2">
                  Nama Item <span class="text-red-500">*</span>
                </label>
                <input
                  id="nama_item"
                  v-model="form.nama_item"
                  type="text"
                  required
                  class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                  placeholder="Masukkan nama item"
                />
                <div v-if="form.errors.nama_item" class="mt-1 text-sm text-red-600">
                  {{ form.errors.nama_item }}
                </div>
              </div>

              <!-- Deskripsi -->
              <div>
                <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-2">
                  Deskripsi
                </label>
                <textarea
                  id="deskripsi"
                  v-model="form.deskripsi"
                  rows="3"
                  class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                  placeholder="Masukkan deskripsi item"
                ></textarea>
                <div v-if="form.errors.deskripsi" class="mt-1 text-sm text-red-600">
                  {{ form.errors.deskripsi }}
                </div>
              </div>

              <!-- Jenis -->
              <div>
                <label for="jenis" class="block text-sm font-medium text-gray-700 mb-2">
                  Jenis <span class="text-red-500">*</span>
                </label>
                <select
                  id="jenis"
                  v-model="form.jenis"
                  required
                  class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                >
                  <option 
                    v-for="option in jenisOptions" 
                    :key="option.value" 
                    :value="option.value"
                  >
                    {{ option.label }}
                  </option>
                </select>
                <div v-if="form.errors.jenis" class="mt-1 text-sm text-red-600">
                  {{ form.errors.jenis }}
                </div>
              </div>

              <!-- Kategori -->
              <div>
                <label for="kategori" class="block text-sm font-medium text-gray-700 mb-2">
                  Kategori <span class="text-red-500">*</span>
                </label>
                <select
                  id="kategori"
                  v-model="form.kategori"
                  required
                  class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                >
                  <option value="">Pilih Kategori</option>
                  <option 
                    v-for="kategori in kategoriOptions" 
                    :key="kategori" 
                    :value="kategori"
                  >
                    {{ kategori }}
                  </option>
                </select>
                <div v-if="form.errors.kategori" class="mt-1 text-sm text-red-600">
                  {{ form.errors.kategori }}
                </div>
              </div>

              <!-- Satuan -->
              <div>
                <label for="satuan" class="block text-sm font-medium text-gray-700 mb-2">
                  Satuan <span class="text-red-500">*</span>
                </label>
                <select
                  id="satuan"
                  v-model="form.satuan"
                  required
                  class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                >
                  <option value="">Pilih Satuan</option>
                  <option 
                    v-for="satuan in satuanOptions" 
                    :key="satuan" 
                    :value="satuan"
                  >
                    {{ satuan }}
                  </option>
                </select>
                <div v-if="form.errors.satuan" class="mt-1 text-sm text-red-600">
                  {{ form.errors.satuan }}
                </div>
              </div>

              <!-- Harga -->
              <div>
                <label for="harga" class="block text-sm font-medium text-gray-700 mb-2">
                  Harga <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                  <input
                    id="harga"
                    v-model="displayHarga"
                    type="text"
                    required
                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    placeholder="Masukkan harga item"
                  />
                </div>
                <div v-if="form.errors.harga" class="mt-1 text-sm text-red-600">
                  {{ form.errors.harga }}
                </div>
              </div>

              <!-- Stok -->
              <div>
                <label for="stok" class="block text-sm font-medium text-gray-700 mb-2">
                  Stok <span class="text-red-500">*</span>
                </label>
                <input
                  id="stok"
                  v-model.number="form.stok"
                  type="number"
                  min="0"
                  required
                  class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                  placeholder="Masukkan jumlah stok"
                />
                <div v-if="form.errors.stok" class="mt-1 text-sm text-red-600">
                  {{ form.errors.stok }}
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
                :href="route('farmalkes.index')"
                class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
              >
                <i class="fas fa-times mr-2"></i>
                Batal
              </Link>
              <button
                type="submit"
                :disabled="form.processing"
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
