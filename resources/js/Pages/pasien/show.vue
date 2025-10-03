<template>
  <AuthenticatedLayout>
    <Head :title="`Detail Pasien - ${psn?.nm_p || ''}`" />
    
    <div class="max-w-7xl mx-auto py-8 px-4">
      <!-- Flash Messages -->
      <div v-if="flash.success" class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
        <div class="flex items-center">
          <i class="fas fa-check-circle mr-2"></i>
          {{ flash.success }}
        </div>
      </div>
      
      <div v-if="flash.error" class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
        <div class="flex items-center">
          <i class="fas fa-exclamation-circle mr-2"></i>
          {{ flash.error }}
        </div>
      </div>

      <!-- Header -->
      <div class="flex justify-between items-center mb-6">
        <div>
          <h1 class="text-3xl font-bold text-gray-900">{{ psn?.nm_p }}</h1>
          <p class="text-gray-600 mt-1">Detail lengkap informasi pasien</p>
        </div>
        <div class="flex space-x-3">
          <button 
            @click="router.visit(`/pasien/${psn?.id}/edit`)"
            class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg shadow transition"
          >
            <i class="fas fa-edit mr-2"></i>Edit Pasien
          </button>
          <button 
            @click="router.visit('/pasien')"
            class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg shadow transition"
          >
            <i class="fas fa-arrow-left mr-2"></i>Kembali
          </button>
        </div>
      </div>

      <!-- Tab Navigation -->
      <TabNavigation 
        :tabs="tabs" 
        @tab-change="handleTabChange"
      >
        <template #default="{ activeTab }">
          <!-- Tab 1: Data Pasien -->
          <div v-if="activeTab === 'data-pasien'" class="space-y-6">
            <div class="bg-white shadow-lg rounded-xl p-8">
              <h2 class="text-2xl font-semibold text-gray-800 mb-6 flex items-center">
                <i class="fas fa-user mr-3 text-blue-600"></i>
                Informasi Data Pasien
              </h2>
              
              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="space-y-2">
                  <label class="text-sm font-medium text-gray-500">Nama Lengkap</label>
                  <p class="text-lg font-semibold text-gray-900">{{ psn?.nm_p }}</p>
                </div>
                
                <div class="space-y-2">
                  <label class="text-sm font-medium text-gray-500">NIK</label>
                  <p class="text-lg font-semibold text-gray-900">{{ psn?.nik }}</p>
                </div>
                
                <div class="space-y-2">
                  <label class="text-sm font-medium text-gray-500">No BPJS</label>
                  <p class="text-lg font-semibold text-gray-900">{{ psn?.no_bpjs }}</p>
                </div>
                
                <div class="space-y-2">
                  <label class="text-sm font-medium text-gray-500">Agama</label>
                  <p class="text-lg font-semibold text-gray-900">{{ psn?.agm }}</p>
                </div>
                
                <div class="space-y-2">
                  <label class="text-sm font-medium text-gray-500">Tanggal Lahir</label>
                  <p class="text-lg font-semibold text-gray-900">{{ psn?.tgl_lahir }}</p>
                </div>
                
                <div class="space-y-2">
                  <label class="text-sm font-medium text-gray-500">Jenis Kelamin</label>
                  <p class="text-lg font-semibold text-gray-900">
                    <span :class="psn?.kelamin === 'L' ? 'text-blue-600' : 'text-pink-600'">
                      {{ psn?.kelamin === 'L' ? 'Laki-laki' : psn?.kelamin === 'P' ? 'Perempuan' : 'Tidak Diketahui' }}
                    </span>
                  </p>
                </div>
                
                <div class="space-y-2 md:col-span-2 lg:col-span-3">
                  <label class="text-sm font-medium text-gray-500">Alamat Lahir</label>
                  <p class="text-lg font-semibold text-gray-900">{{ psn?.almt_L }}</p>
                </div>
                
                <div class="space-y-2 md:col-span-2 lg:col-span-3">
                  <label class="text-sm font-medium text-gray-500">Alamat Berdomisili</label>
                  <p class="text-lg font-semibold text-gray-900">{{ psn?.almt_B }}</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Tab 2: Kunjungan -->
          <div v-if="activeTab === 'kunjungan'" class="space-y-6">
            <div class="bg-white shadow-lg rounded-xl p-8">
              <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-semibold text-gray-800 flex items-center">
                  <i class="fas fa-calendar-check mr-3 text-green-600"></i>
                  Data Kunjungan
                </h2>
                <button
                  @click="router.visit(`/pasien/${psn?.id}/kunjungan/create`)"
                  class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow transition flex items-center"
                >
                  <i class="fas fa-plus mr-2"></i>Tambah Kunjungan
                </button>
              </div>
              
              <div v-if="pasienData && pasienData.length > 0" class="space-y-4">
                <div 
                  v-for="(kunjungan, index) in pasienData" 
                  :key="kunjungan.id"
                  class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow"
                >
                  <div class="flex justify-between items-start mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">
                      Kunjungan #{{ index + 1 }}
                    </h3>
                    <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                      {{ kunjungan.no_reg }}
                    </span>
                  </div>
                  
                  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div class="space-y-1">
                      <label class="text-sm font-medium text-gray-500">Tanggal Registrasi</label>
                      <p class="font-semibold text-gray-900">{{ formatDate(kunjungan.tgl_reg) }}</p>
                    </div>
                    
                    <div class="space-y-1">
                      <label class="text-sm font-medium text-gray-500">No Registrasi</label>
                      <p class="font-semibold text-gray-900">{{ kunjungan.no_reg }}</p>
                    </div>
                    
                    <div class="space-y-1">
                      <label class="text-sm font-medium text-gray-500">MRN</label>
                      <p class="font-semibold text-gray-900">{{ kunjungan.mrn }}</p>
                    </div>
                    
                    <div class="space-y-1">
                      <label class="text-sm font-medium text-gray-500">Jenis Perawatan</label>
                      <p class="font-semibold text-gray-900">{{ kunjungan.perawatan }}</p>
                    </div>
                    
                    <div class="space-y-1">
                      <label class="text-sm font-medium text-gray-500">Penjamin</label>
                      <p class="font-semibold text-gray-900">{{ kunjungan.penjamin }}</p>
                    </div>
                    
                    <div class="space-y-1">
                      <label class="text-sm font-medium text-gray-500">No SJP</label>
                      <p class="font-semibold text-gray-900">{{ kunjungan.no_sjp || '-' }}</p>
                    </div>
                  </div>
                  
                  <div v-if="kunjungan.icd" class="mt-4 pt-4 border-t border-gray-200">
                    <label class="text-sm font-medium text-gray-500">ICD Code</label>
                    <p class="font-semibold text-gray-900">{{ kunjungan.icd }}</p>
                  </div>
                  
                  <div v-if="kunjungan.kunjungan" class="mt-4 pt-4 border-t border-gray-200">
                    <label class="text-sm font-medium text-gray-500">Jenis Perawatan</label>
                    <p class="font-semibold text-gray-900">{{ kunjungan.kunjungan }}</p>
                  </div>
                </div>
              </div>
              
              <div v-else class="text-center py-12">
                <i class="fas fa-calendar-times text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-500 mb-2">Belum Ada Kunjungan</h3>
                <p class="text-gray-400">Pasien ini belum memiliki riwayat kunjungan</p>
              </div>
            </div>
          </div>

          <!-- Tab 3: Riwayat Kunjungan -->
          <div v-if="activeTab === 'riwayat'" class="space-y-6">
            <div class="bg-white shadow-lg rounded-xl p-8">
              <h2 class="text-2xl font-semibold text-gray-800 mb-6 flex items-center">
                <i class="fas fa-history mr-3 text-purple-600"></i>
                Riwayat Lengkap Kunjungan
              </h2>
              
              <div v-if="pasienData && pasienData.length > 0" class="space-y-6">
                <div 
                  v-for="(kunjungan, index) in pasienData" 
                  :key="kunjungan.id"
                  class="border border-gray-200 rounded-lg p-6"
                >
                  <div class="flex justify-between items-start mb-6">
                    <h3 class="text-xl font-semibold text-gray-800">
                      Kunjungan {{ formatDate(kunjungan.tgl_reg) }}
                    </h3>
                    <div class="flex space-x-2">
                      <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                        {{ kunjungan.no_reg }}
                      </span>
                      <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">
                        {{ kunjungan.perawatan }}
                      </span>
                    </div>
                  </div>
                  
                  <!-- Konsultasi -->
                  <div v-if="kunjungan.konsuls && kunjungan.konsuls.length > 0" class="mb-6">
                    <h4 class="text-lg font-semibold text-gray-700 mb-3 flex items-center">
                      <i class="fas fa-stethoscope mr-2 text-blue-600"></i>
                      Konsultasi Medis
                    </h4>
                    <div class="bg-blue-50 rounded-lg p-4">
                      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div v-for="konsul in kunjungan.konsuls" :key="konsul.id">
                          <div class="space-y-2">
                            <p><span class="font-medium">Dokter:</span> {{ konsul.dokter }}</p>
                            <p><span class="font-medium">Diagnosa:</span> {{ konsul.dskp_kons }}</p>
                            <p><span class="font-medium">Jumlah Konsultasi:</span> {{ konsul.jmlh_kons }}</p>
                            <p><span class="font-medium">Biaya:</span> Rp {{ formatNumber(konsul.bya_kons) }}</p>
                            <p><span class="font-medium">Status:</span> 
                              <span :class="konsul.st_kons === 'selesai' ? 'text-green-600' : 'text-yellow-600'">
                                {{ konsul.st_kons }}
                              </span>
                            </p>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  
                  <!-- Tindakan -->
                  <div v-if="kunjungan.tindaks && kunjungan.tindaks.length > 0" class="mb-6">
                    <h4 class="text-lg font-semibold text-gray-700 mb-3 flex items-center">
                      <i class="fas fa-procedures mr-2 text-green-600"></i>
                      Tindakan Medis
                    </h4>
                    <div class="bg-green-50 rounded-lg p-4">
                      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div v-for="tindak in kunjungan.tindaks" :key="tindak.id">
                          <div class="space-y-2">
                            <p><span class="font-medium">Tindakan:</span> {{ tindak.tindakan }}</p>
                            <p><span class="font-medium">Jumlah:</span> {{ tindak.jmlh_tindak }}</p>
                            <p><span class="font-medium">Biaya:</span> Rp {{ formatNumber(tindak.bya_tindak) }}</p>
                            <p><span class="font-medium">Status:</span> 
                              <span :class="tindak.st_tindak === 'selesai' ? 'text-green-600' : 'text-yellow-600'">
                                {{ tindak.st_tindak }}
                              </span>
                            </p>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  
                  <!-- Alkes -->
                  <div v-if="kunjungan.alkes && kunjungan.alkes.length > 0" class="mb-6">
                    <h4 class="text-lg font-semibold text-gray-700 mb-3 flex items-center">
                      <i class="fas fa-tools mr-2 text-orange-600"></i>
                      Alat Kesehatan
                    </h4>
                    <div class="bg-orange-50 rounded-lg p-4">
                      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div v-for="alkes in kunjungan.alkes" :key="alkes.id">
                          <div class="space-y-2">
                            <p><span class="font-medium">Alat:</span> {{ alkes.alat }}</p>
                            <p><span class="font-medium">Jumlah:</span> {{ alkes.jmlh_alkes }}</p>
                            <p><span class="font-medium">Biaya:</span> Rp {{ formatNumber(alkes.bya_alkes) }}</p>
                            <p><span class="font-medium">Status:</span> 
                              <span :class="alkes.st_alkes === 'selesai' ? 'text-green-600' : 'text-yellow-600'">
                                {{ alkes.st_alkes }}
                              </span>
                            </p>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  
                  <!-- Resep -->
                  <div v-if="kunjungan.rsp && kunjungan.rsp.length > 0" class="mb-6">
                    <h4 class="text-lg font-semibold text-gray-700 mb-3 flex items-center">
                      <i class="fas fa-pills mr-2 text-purple-600"></i>
                      Resep Obat
                    </h4>
                    <div class="bg-purple-50 rounded-lg p-4">
                      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div v-for="rsp in kunjungan.rsp" :key="rsp.id">
                          <div class="space-y-2">
                            <p><span class="font-medium">Obat:</span> {{ rsp.obat }}</p>
                            <p><span class="font-medium">Jumlah:</span> {{ rsp.jmlh_rsp }}</p>
                            <p><span class="font-medium">Biaya:</span> Rp {{ formatNumber(rsp.bya_rsp) }}</p>
                            <p><span class="font-medium">Status:</span> 
                              <span :class="rsp.st_rsp === 'selesai' ? 'text-green-600' : 'text-yellow-600'">
                                {{ rsp.st_rsp }}
                              </span>
                            </p>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  
                  <!-- Lainnya -->
                  <div v-if="kunjungan.lainnyas && kunjungan.lainnyas.length > 0" class="mb-6">
                    <h4 class="text-lg font-semibold text-gray-700 mb-3 flex items-center">
                      <i class="fas fa-list mr-2 text-gray-600"></i>
                      Layanan Lainnya
                    </h4>
                    <div class="bg-gray-50 rounded-lg p-4">
                      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div v-for="lainnya in kunjungan.lainnyas" :key="lainnya.id">
                          <div class="space-y-2">
                            <p><span class="font-medium">Layanan:</span> {{ lainnya.layanan }}</p>
                            <p><span class="font-medium">Jumlah:</span> {{ lainnya.jmlh_lainnya }}</p>
                            <p><span class="font-medium">Biaya:</span> Rp {{ formatNumber(lainnya.bya_lainnya) }}</p>
                            <p><span class="font-medium">Status:</span> 
                              <span :class="lainnya.st_lainnya === 'selesai' ? 'text-green-600' : 'text-yellow-600'">
                                {{ lainnya.st_lainnya }}
                              </span>
                            </p>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  
                  <!-- Transaksi -->
                  <div v-if="kunjungan.transaksi && kunjungan.transaksi.length > 0" class="mt-6 pt-6 border-t border-gray-200">
                    <h4 class="text-lg font-semibold text-gray-700 mb-3 flex items-center">
                      <i class="fas fa-receipt mr-2 text-indigo-600"></i>
                      Detail Transaksi
                    </h4>
                    <div class="bg-indigo-50 rounded-lg p-4">
                      <div v-for="transaksi in kunjungan.transaksi" :key="transaksi.id">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                          <div>
                            <p><span class="font-medium">Total Biaya:</span> Rp {{ formatNumber(transaksi.total_biaya) }}</p>
                          </div>
                          <div>
                            <p><span class="font-medium">Tanggal:</span> {{ formatDate(transaksi.tanggal) }}</p>
                          </div>
                          <div>
                            <p><span class="font-medium">Status:</span> 
                              <span :class="transaksi.status === 'lunas' ? 'text-green-600' : 'text-yellow-600'">
                                {{ transaksi.status }}
                              </span>
                            </p>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              
              <div v-else class="text-center py-12">
                <i class="fas fa-history text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-500 mb-2">Belum Ada Riwayat</h3>
                <p class="text-gray-400">Pasien ini belum memiliki riwayat kunjungan lengkap</p>
              </div>
            </div>
          </div>
        </template>
      </TabNavigation>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import TabNavigation from '@/Components/TabNavigation.vue'

const props = defineProps({
  psn: {
    type: Object,
    required: true
  },
  pasienData: {
    type: Array,
    default: () => []
  },
  flash: {
    type: Object,
    default: () => ({})
  }
})

const activeTab = ref('data-pasien')

const tabs = computed(() => [
  {
    id: 'data-pasien',
    name: 'Data Pasien',
    icon: 'fas fa-user',
    count: undefined
  },
  {
    id: 'kunjungan',
    name: 'Kunjungan',
    icon: 'fas fa-calendar-check',
    count: props.pasienData?.length || 0
  },
  {
    id: 'riwayat',
    name: 'Riwayat Kunjungan',
    icon: 'fas fa-history',
    count: props.pasienData?.length || 0
  }
])

const handleTabChange = (tabId) => {
  activeTab.value = tabId
}

const formatDate = (dateString) => {
  if (!dateString) return '-'
  const date = new Date(dateString)
  return date.toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const formatNumber = (number) => {
  if (!number) return '0'
  return new Intl.NumberFormat('id-ID').format(number)
}
</script>

<style scoped>
/* Custom styles if needed */
</style>
