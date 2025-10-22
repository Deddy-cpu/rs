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
  
  // Return numeric value (number) or empty string
  const num = parseFloat(cleanValue)
  return isNaN(num) ? '' : num
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

  'AMPL',
  'nabil',
  'AMPUL',
  'BOX',
  'BTL',
  'CAN',
  'CAPL',
  'CAPS',
  'CARPL',
  'CM',
  'CYL',
  'DOZN',
  'EA',
  'EACH',
  'GALON',
  'GRAM',
  'INJ',
  'JAR',
  'KAPL',
  'KAPS',
  'KIT',
  'KG',
  'METER',
  'ML',
  'PACK',
  'PAIR',
  'PATCH',
  'PCS',
  'PEN',
  'POT',
  'PREFFILLED SYRNG',
  'PUFF',
  'ROLL',
  'SACHET',
  'SET',
  'SHT',
  'SOFTCAP',
  'SOL',
  'STRIP',
  'SUPP',
  'SYRINGE',
  'TAB',
  'TUBE',
  'UNIT',
  'VIAL'

]

</script>

<template>
  <AuthenticatedLayout>
    <Head title="Tambah Farmalkes" />

    <div class="min-h-screen bg-cover bg-center p-6" style="background-image: url('/images/bg-login.png')">
      <!-- Header -->
      <div class="text-center mb-8">
        <h1 class="text-4xl md:text-5xl font-bold bg-gradient-to-r from-red-600 to-rose-600 bg-clip-text text-transparent mb-2">
          <span class="inline-block align-middle mr-2">❤️</span>
          Tambah Farmalkes
        </h1>
        <p class="text-gray-600 text-lg">Buat data farmasi dan alat kesehatan baru</p>
      </div>

      <!-- Form Container -->
      <div class="max-w-4xl mx-auto">
        <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
          <!-- Form Header -->
          <div class="bg-gradient-to-r from-red-600 to-rose-600 px-8 py-6 text-white">
            <div class="flex items-center gap-4">
              <div class="w-12 h-12 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 4v16m8-8H4" />
                </svg>
              </div>
              <div>
                <h2 class="text-2xl font-bold">Form Tambah Farmalkes</h2>
                <p class="text-red-100">Lengkapi data farmasi dan alat kesehatan di bawah ini</p>
              </div>
            </div>
          </div>

          <!-- Form Content -->
          <div class="p-8">
            <form @submit.prevent="submitForm" class="space-y-8">
              <!-- Kode -->
              <div class="space-y-2">
                <label for="kode" class="block text-sm font-semibold text-gray-700">
                  <svg class="w-4 h-4 inline mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 12h6m2 8H7a2 2 0 01-2-2V6a2 2 0 012-2h8l6 6v8a2 2 0 01-2 2z" />
                  </svg>
                  Kode Item *
                </label>
                <input
                  id="kode"
                  v-model="form.kode"
                  type="text"
                  required
                  placeholder="Masukkan kode item (contoh: FAR001)"
                  class="w-full px-4 py-3 border border-gray-300 rounded-xl bg-gray-50 focus:ring-2 focus:ring-red-500 focus:border-transparent focus:bg-white text-lg transition-all duration-200"
                  :class="{ 'border-red-500 focus:ring-red-500': form.errors.kode }"
                />
                <div v-if="form.errors.kode" class="text-red-600 text-sm mt-1 flex items-center">
                  <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  {{ form.errors.kode }}
                </div>
              </div>

              <!-- Nama Item -->
              <div class="space-y-2">
                <label for="nama_item" class="block text-sm font-semibold text-gray-700">
                  <svg class="w-4 h-4 inline mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M5 12h14M5 12l4 4m-4-4l4-4" />
                  </svg>
                  Nama Item *
                </label>
                <input
                  id="nama_item"
                  v-model="form.nama_item"
                  type="text"
                  required
                  placeholder="Masukkan nama item"
                  class="w-full px-4 py-3 border border-gray-300 rounded-xl bg-gray-50 focus:ring-2 focus:ring-red-500 focus:border-transparent focus:bg-white text-lg transition-all duration-200"
                />
                <div v-if="form.errors.nama_item" class="text-red-600 text-sm mt-1">{{ form.errors.nama_item }}</div>
              </div>

              <!-- Jenis -->
              <div class="space-y-2">
                <label for="jenis" class="block text-sm font-semibold text-gray-700">
                  <svg class="w-4 h-4 inline mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 7h18M3 12h18m-9 5h9" />
                  </svg>
                  Jenis *
                </label>
                <select
                  id="jenis"
                  v-model="form.jenis"
                  required
                  class="w-full px-4 py-3 border border-gray-300 rounded-xl bg-gray-50 focus:ring-2 focus:ring-red-500 focus:border-transparent focus:bg-white text-lg transition-all duration-200"
                >
                  <option value="">Pilih Jenis</option>
                  <option v-for="option in jenisOptions" :key="option.value" :value="option.value">
                    {{ option.label }}
                  </option>
                </select>
              </div>

              <!-- Harga -->
              <div class="space-y-2">
                <label for="harga" class="block text-sm font-semibold text-gray-700">
                  <svg class="w-4 h-4 inline mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8v-2m0 10v2" />
                  </svg>
                  Harga *
                </label>
                <input
                  id="harga"
                  v-model="displayHarga"
                  type="text"
                  required
                  placeholder="Masukkan harga item"
                  class="w-full px-4 py-3 border border-gray-300 rounded-xl bg-gray-50 focus:ring-2 focus:ring-red-500 focus:border-transparent focus:bg-white text-lg transition-all duration-200"
                />
                <div v-if="form.errors.harga" class="text-red-600 text-sm mt-1">{{ form.errors.harga }}</div>
              </div>

              <!-- Kategori -->
              <div class="space-y-2">
                <label for="kategori" class="block text-sm font-semibold text-gray-700">Kategori *</label>
                <select
                  id="kategori"
                  v-model="form.kategori"
                  required
                  class="w-full px-4 py-3 border border-gray-300 rounded-xl bg-gray-50 focus:ring-2 focus:ring-red-500 focus:border-transparent focus:bg-white text-lg transition-all duration-200"
                >
                  <option value="">Pilih Kategori</option>
                  <option v-for="opt in kategoriOptions" :key="opt" :value="opt">{{ opt }}</option>
                </select>
                <div v-if="form.errors.kategori" class="text-red-600 text-sm mt-1">{{ form.errors.kategori }}</div>
              </div>

              <!-- Satuan -->
              <div class="space-y-2">
                <label for="satuan" class="block text-sm font-semibold text-gray-700">Satuan *</label>
                <select
                  id="satuan"
                  v-model="form.satuan"
                  required
                  class="w-full px-4 py-3 border border-gray-300 rounded-xl bg-gray-50 focus:ring-2 focus:ring-red-500 focus:border-transparent focus:bg-white text-lg transition-all duration-200"
                >
                  <option value="">Pilih Satuan</option>
                  <option v-for="opt in satuanOptions" :key="opt" :value="opt">{{ opt }}</option>
                </select>
                <div v-if="form.errors.satuan" class="text-red-600 text-sm mt-1">{{ form.errors.satuan }}</div>
              </div>

              <!-- Stok -->
              <div class="space-y-2">
                <label for="stok" class="block text-sm font-semibold text-gray-700">Stok *</label>
                <input
                  id="stok"
                  v-model.number="form.stok"
                  type="number"
                  min="0"
                  required
                  class="w-full px-4 py-3 border border-gray-300 rounded-xl bg-gray-50 focus:ring-2 focus:ring-red-500 focus:border-transparent focus:bg-white text-lg transition-all duration-200"
                />
                <div v-if="form.errors.stok" class="text-red-600 text-sm mt-1">{{ form.errors.stok }}</div>
              </div>

              <!-- Deskripsi -->
              <div class="space-y-2">
                <label for="deskripsi" class="block text-sm font-semibold text-gray-700">Deskripsi</label>
                <textarea
                  id="deskripsi"
                  v-model="form.deskripsi"
                  rows="3"
                  placeholder="Deskripsi singkat (opsional)"
                  class="w-full px-4 py-3 border border-gray-300 rounded-xl bg-gray-50 focus:ring-2 focus:ring-red-500 focus:border-transparent focus:bg-white text-lg transition-all duration-200"
                ></textarea>
                <div v-if="form.errors.deskripsi" class="text-red-600 text-sm mt-1">{{ form.errors.deskripsi }}</div>
              </div>

              <!-- Status -->
              <div class="space-y-2">
                <label class="block text-sm font-semibold text-gray-700">
                  <svg class="w-4 h-4 inline mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 12l2 2 4-4" />
                  </svg>
                  Status *
                </label>
                               <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <label class="relative cursor-pointer">
                    <input v-model="form.aktif" type="radio" value="Y" class="sr-only" />
                    <div class="flex items-center p-4 border-2 rounded-xl transition-all duration-200"
                      :class="form.aktif === 'Y'
                        ? 'border-green-500 bg-green-50 text-green-700'
                        : 'border-gray-200 bg-gray-50 text-gray-600 hover:border-gray-300'">
                      <div class="flex items-center justify-center w-6 h-6 rounded-full border-2 mr-3"
                        :class="form.aktif === 'Y' ? 'border-green-500 bg-green-500' : 'border-gray-300'">
                        <div v-if="form.aktif === 'Y'" class="w-2 h-2 rounded-full bg-white"></div>
                      </div>
                      <div>
                        <div class="font-semibold">Aktif</div>
                        <div class="text-sm">Data dapat digunakan</div>
                      </div>
                    </div>
                  </label>

                  <label class="relative cursor-pointer">
                    <input v-model="form.aktif" type="radio" value="N" class="sr-only" />
                    <div class="flex items-center p-4 border-2 rounded-xl transition-all duration-200"
                      :class="form.aktif === 'N'
                        ? 'border-red-500 bg-red-50 text-red-700'
                        : 'border-gray-200 bg-gray-50 text-gray-600 hover:border-gray-300'">
                      <div class="flex items-center justify-center w-6 h-6 rounded-full border-2 mr-3"
                        :class="form.aktif === 'N' ? 'border-red-500 bg-red-500' : 'border-gray-300'">
                        <div v-if="form.aktif === 'N'" class="w-2 h-2 rounded-full bg-white"></div>
                      </div>
                      <div>
                        <div class="font-semibold">Tidak Aktif</div>
                        <div class="text-sm">Data tidak dapat digunakan</div>
                      </div>
                    </div>
                  </label>
                </div>
              </div>

              <!-- Tombol Aksi -->
              <div class="flex flex-col sm:flex-row gap-4 pt-6 border-t border-gray-200">
                <Link
                  :href="route('farmalkes.index')"
                  class="flex-1 px-6 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition-all duration-200 font-semibold flex items-center justify-center gap-2"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M6 18L18 6M6 6l12 12" />
                  </svg>
                  Batal
                </Link>

                <button
                  type="submit"
                  :disabled="form.processing"
                  class="flex-1 px-6 py-3 bg-gradient-to-r from-red-600 to-rose-600 text-white rounded-xl hover:from-red-700 hover:to-rose-700 transition-all duration-200 font-semibold flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  <svg v-if="form.processing" class="w-5 h-5 animate-spin" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                  </svg>
                  <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M5 13l4 4L19 7" />
                  </svg>
                  <span v-if="form.processing">Menyimpan...</span>
                  <span v-else>Simpan</span>
                </button>
              </div>
            </form>
          </div>
        </div>

        <!-- Help Section -->
        <div class="mt-8 bg-red-50/80 backdrop-blur-sm rounded-xl p-6 border border-red-200">
          <div class="flex items-start gap-4">
            <div class="w-10 h-10 bg-red-500 rounded-full flex items-center justify-center flex-shrink-0">
              <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <div>
              <h3 class="text-lg font-semibold text-red-800 mb-2">Tips Pengisian Form</h3>
              <ul class="text-red-700 space-y-1 text-sm">
                <li>• <strong>Kode Item:</strong> Gunakan format unik (contoh: FAR001)</li>
                <li>• <strong>Nama Item:</strong> Gunakan nama yang deskriptif</li>
                <li>• <strong>Harga:</strong> Isi sesuai nilai jual item</li>
                <li>• <strong>Status:</strong> Pilih “Aktif” jika digunakan</li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

