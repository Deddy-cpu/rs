<template>
  <AuthenticatedLayout>
    <Head title="Kasir - Detail Kunjungan" />
    
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900">
            
            <!-- Header -->
            <div class="mb-6">
              <div class="flex justify-between items-center">
                <div>
                  <h1 class="text-2xl font-bold text-gray-900">Detail Kunjungan</h1>
                  <p class="text-gray-600 mt-1">Informasi lengkap kunjungan pasien</p>
                </div>
                <div class="flex gap-3">
                  <Link 
                    :href="route('kasir.kunjungan.print', kunjungan.id)"
                    class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600 inline-flex items-center"
                  >
                    <i class="fas fa-print mr-2"></i>
                    Print
                  </Link>
                  <Link 
                    :href="route('kasir.kunjungan.print', kunjungan.id)"
                    class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600 inline-flex items-center"
                  >
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali
                  </Link>
                </div>
              </div>
            </div>

            <!-- Patient Information Section -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
              <h2 class="text-lg font-semibold text-blue-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                Informasi Pasien
              </h2>
              
              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700">No. Registrasi</label>
                  <p class="mt-1 text-sm text-gray-900 bg-white px-3 py-2 border rounded-md">{{ kunjungan?.no_reg || '-' }}</p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">Nama Pasien</label>
                  <p class="mt-1 text-sm text-gray-900 bg-white px-3 py-2 border rounded-md">{{ kunjungan?.nm_p || '-' }}</p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">MRN</label>
                  <p class="mt-1 text-sm text-gray-900 bg-white px-3 py-2 border rounded-md">{{ kunjungan?.mrn || '-' }}</p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">Tanggal Registrasi</label>
                  <p class="mt-1 text-sm text-gray-900 bg-white px-3 py-2 border rounded-md">{{ formatDate(kunjungan?.tgl_reg) }}</p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">Jenis Perawatan</label>
                  <p class="mt-1 text-sm text-gray-900 bg-white px-3 py-2 border rounded-md">{{ kunjungan?.perawatan || '-' }}</p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">Penjamin</label>
                  <p class="mt-1 text-sm text-gray-900 bg-white px-3 py-2 border rounded-md">{{ kunjungan?.penjamin || '-' }}</p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">Jenis Kunjungan</label>
                  <p class="mt-1 text-sm text-gray-900 bg-white px-3 py-2 border rounded-md">{{ kunjungan?.kunjungan || '-' }}</p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">Alamat</label>
                  <p class="mt-1 text-sm text-gray-900 bg-white px-3 py-2 border rounded-md">{{ kunjungan?.almt_B || '-' }}</p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">ICD Code</label>
                  <p class="mt-1 text-sm text-gray-900 bg-white px-3 py-2 border rounded-md">{{ kunjungan?.icd || '-' }}</p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">No Invoice</label>
                  <p class="mt-1 text-sm text-gray-900 bg-white px-3 py-2 border rounded-md">{{ kunjungan?.no_inv || '-' }}</p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">Tanggal Invoice</label>
                  <p class="mt-1 text-sm text-gray-900 bg-white px-3 py-2 border rounded-md">{{ formatDate(kunjungan?.tgl_inv) }}</p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">No SJP</label>
                  <p class="mt-1 text-sm text-gray-900 bg-white px-3 py-2 border rounded-md">{{ kunjungan?.no_sjp || '-' }}</p>
                </div>
              </div>
            </div>

            <!-- Medical Services Tabs -->
            <div class="bg-white border border-gray-200 rounded-lg">
              <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                  <button 
                    v-for="tab in tabs" 
                    :key="tab.id"
                    @click="activeTab = tab.id"
                    :class="[
                      activeTab === tab.id 
                        ? 'border-blue-500 text-blue-600' 
                        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                      'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm'
                    ]"
                  >
                    {{ tab.name }}
                    <span v-if="getTabCount(tab.id) > 0" class="ml-2 bg-blue-100 text-blue-600 px-2 py-1 rounded-full text-xs">
                      {{ getTabCount(tab.id) }}
                    </span>
                  </button>
                </nav>
              </div>

              <div class="p-6">
                <!-- Konsultasi Tab -->
                <div v-show="activeTab === 'konsul'" class="space-y-4">
                  <h3 class="text-lg font-medium text-gray-900">Konsultasi</h3>
                  
                  <div v-if="kunjungan?.konsuls && kunjungan.konsuls.length > 0" class="space-y-4">
                    <div v-for="(konsul, index) in kunjungan.konsuls" :key="index" class="border border-gray-200 rounded-lg p-4">
                      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div>
                          <label class="block text-sm font-medium text-gray-700">Dokter</label>
                          <p class="mt-1 text-sm text-gray-900 bg-gray-50 px-3 py-2 border rounded-md">{{ konsul.dokter || '-' }}</p>
                        </div>
                        <div>
                          <label class="block text-sm font-medium text-gray-700">Deskripsi</label>
                          <p class="mt-1 text-sm text-gray-900 bg-gray-50 px-3 py-2 border rounded-md">{{ konsul.dskp_kons || '-' }}</p>
                        </div>
                        <div>
                          <label class="block text-sm font-medium text-gray-700">Jumlah</label>
                          <p class="mt-1 text-sm text-gray-900 bg-gray-50 px-3 py-2 border rounded-md">{{ konsul.jmlh_kons || '-' }}</p>
                        </div>
                        <div>
                          <label class="block text-sm font-medium text-gray-700">Biaya</label>
                          <p class="mt-1 text-sm text-gray-900 bg-gray-50 px-3 py-2 border rounded-md">{{ formatCurrency(konsul.bya_kons) }}</p>
                        </div>
                        <div>
                          <label class="block text-sm font-medium text-gray-700">Diskon</label>
                          <p class="mt-1 text-sm text-gray-900 bg-gray-50 px-3 py-2 border rounded-md">{{ konsul.disc_kons || '-' }}</p>
                        </div>
                        <div>
                          <label class="block text-sm font-medium text-gray-700">Subtotal</label>
                          <p class="mt-1 text-sm font-bold text-green-600 bg-gray-50 px-3 py-2 border rounded-md">{{ formatCurrency(konsul.st_kons) }}</p>
                        </div>
                        <div>
                          <label class="block text-sm font-medium text-gray-700">Tanggal</label>
                          <p class="mt-1 text-sm text-gray-900 bg-gray-50 px-3 py-2 border rounded-md">{{ formatDate(konsul.tanggal) }}</p>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div v-else class="text-center py-8 text-gray-500">
                    <i class="fas fa-stethoscope text-4xl mb-2"></i>
                    <p>Tidak ada data konsultasi</p>
                  </div>
                </div>

                <!-- Tindakan Tab -->
                <div v-show="activeTab === 'tindak'" class="space-y-4">
                  <h3 class="text-lg font-medium text-gray-900">Tindakan</h3>
                  
                  <div v-if="kunjungan?.tindaks && kunjungan.tindaks.length > 0" class="space-y-4">
                    <div v-for="(tindak, index) in kunjungan.tindaks" :key="index" class="border border-gray-200 rounded-lg p-4">
                      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div>
                          <label class="block text-sm font-medium text-gray-700">Dokter</label>
                          <p class="mt-1 text-sm text-gray-900 bg-gray-50 px-3 py-2 border rounded-md">{{ tindak.dktr_tindak || '-' }}</p>
                        </div>
                        <div>
                          <label class="block text-sm font-medium text-gray-700">Deskripsi</label>
                          <p class="mt-1 text-sm text-gray-900 bg-gray-50 px-3 py-2 border rounded-md">{{ tindak.dskp_tindak || '-' }}</p>
                        </div>
                        <div>
                          <label class="block text-sm font-medium text-gray-700">Jumlah</label>
                          <p class="mt-1 text-sm text-gray-900 bg-gray-50 px-3 py-2 border rounded-md">{{ tindak.jmlh_tindak || '-' }}</p>
                        </div>
                        <div>
                          <label class="block text-sm font-medium text-gray-700">Biaya</label>
                          <p class="mt-1 text-sm text-gray-900 bg-gray-50 px-3 py-2 border rounded-md">{{ formatCurrency(tindak.bya_tindak) }}</p>
                        </div>
                        <div>
                          <label class="block text-sm font-medium text-gray-700">Diskon</label>
                          <p class="mt-1 text-sm text-gray-900 bg-gray-50 px-3 py-2 border rounded-md">{{ tindak.disc_tindak || '-' }}</p>
                        </div>
                        <div>
                          <label class="block text-sm font-medium text-gray-700">Subtotal</label>
                          <p class="mt-1 text-sm font-bold text-green-600 bg-gray-50 px-3 py-2 border rounded-md">{{ formatCurrency(tindak.st_tindak) }}</p>
                        </div>
                        <div>
                          <label class="block text-sm font-medium text-gray-700">Tanggal</label>
                          <p class="mt-1 text-sm text-gray-900 bg-gray-50 px-3 py-2 border rounded-md">{{ formatDate(tindak.tanggal) }}</p>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div v-else class="text-center py-8 text-gray-500">
                    <i class="fas fa-procedures text-4xl mb-2"></i>
                    <p>Tidak ada data tindakan</p>
                  </div>
                </div>

                <!-- Alkes Tab -->
                <div v-show="activeTab === 'alkes'" class="space-y-4">
                  <h3 class="text-lg font-medium text-gray-900">Alat Kesehatan</h3>
                  
                  <div v-if="kunjungan?.alkes && kunjungan.alkes.length > 0" class="space-y-4">
                    <div v-for="(alkes, index) in kunjungan.alkes" :key="index" class="border border-gray-200 rounded-lg p-4">
                      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div>
                          <label class="block text-sm font-medium text-gray-700">Poli</label>
                          <p class="mt-1 text-sm text-gray-900 bg-gray-50 px-3 py-2 border rounded-md">{{ alkes.poli || '-' }}</p>
                        </div>
                        <div>
                          <label class="block text-sm font-medium text-gray-700">Deskripsi</label>
                          <p class="mt-1 text-sm text-gray-900 bg-gray-50 px-3 py-2 border rounded-md">{{ alkes.dskp_alkes || '-' }}</p>
                        </div>
                        <div>
                          <label class="block text-sm font-medium text-gray-700">Jumlah</label>
                          <p class="mt-1 text-sm text-gray-900 bg-gray-50 px-3 py-2 border rounded-md">{{ alkes.jmlh_alkes || '-' }}</p>
                        </div>
                        <div>
                          <label class="block text-sm font-medium text-gray-700">Biaya</label>
                          <p class="mt-1 text-sm text-gray-900 bg-gray-50 px-3 py-2 border rounded-md">{{ formatCurrency(alkes.bya_alkes) }}</p>
                        </div>
                        <div>
                          <label class="block text-sm font-medium text-gray-700">Diskon</label>
                          <p class="mt-1 text-sm text-gray-900 bg-gray-50 px-3 py-2 border rounded-md">{{ alkes.disc_alkes || '-' }}</p>
                        </div>
                        <div>
                          <label class="block text-sm font-medium text-gray-700">Subtotal</label>
                          <p class="mt-1 text-sm font-bold text-green-600 bg-gray-50 px-3 py-2 border rounded-md">{{ formatCurrency(alkes.st_alkes) }}</p>
                        </div>
                        <div>
                          <label class="block text-sm font-medium text-gray-700">Tanggal</label>
                          <p class="mt-1 text-sm text-gray-900 bg-gray-50 px-3 py-2 border rounded-md">{{ formatDate(alkes.tanggal) }}</p>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div v-else class="text-center py-8 text-gray-500">
                    <i class="fas fa-medkit text-4xl mb-2"></i>
                    <p>Tidak ada data alat kesehatan</p>
                  </div>
                </div>

                <!-- Resep Tab -->
                <div v-show="activeTab === 'rsp'" class="space-y-4">
                  <h3 class="text-lg font-medium text-gray-900">Resep</h3>
                  
                  <div v-if="kunjungan?.rsp && kunjungan.rsp.length > 0" class="space-y-4">
                    <div v-for="(rsp, index) in kunjungan.rsp" :key="index" class="border border-gray-200 rounded-lg p-4">
                      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div>
                          <label class="block text-sm font-medium text-gray-700">Deskripsi</label>
                          <p class="mt-1 text-sm text-gray-900 bg-gray-50 px-3 py-2 border rounded-md">{{ rsp.dskp_rsp || '-' }}</p>
                        </div>
                        <div>
                          <label class="block text-sm font-medium text-gray-700">Jumlah</label>
                          <p class="mt-1 text-sm text-gray-900 bg-gray-50 px-3 py-2 border rounded-md">{{ rsp.jmlh_rsp || '-' }}</p>
                        </div>
                        <div>
                          <label class="block text-sm font-medium text-gray-700">Biaya</label>
                          <p class="mt-1 text-sm text-gray-900 bg-gray-50 px-3 py-2 border rounded-md">{{ formatCurrency(rsp.bya_rsp) }}</p>
                        </div>
                        <div>
                          <label class="block text-sm font-medium text-gray-700">Diskon</label>
                          <p class="mt-1 text-sm text-gray-900 bg-gray-50 px-3 py-2 border rounded-md">{{ rsp.disc_rsp || '-' }}</p>
                        </div>
                        <div>
                          <label class="block text-sm font-medium text-gray-700">Subtotal</label>
                          <p class="mt-1 text-sm font-bold text-green-600 bg-gray-50 px-3 py-2 border rounded-md">{{ formatCurrency(rsp.st_rsp) }}</p>
                        </div>
                        <div>
                          <label class="block text-sm font-medium text-gray-700">Tanggal</label>
                          <p class="mt-1 text-sm text-gray-900 bg-gray-50 px-3 py-2 border rounded-md">{{ formatDate(rsp.tanggal) }}</p>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div v-else class="text-center py-8 text-gray-500">
                    <i class="fas fa-pills text-4xl mb-2"></i>
                    <p>Tidak ada data resep</p>
                  </div>
                </div>

                <!-- Lainnya Tab -->
                <div v-show="activeTab === 'lainnya'" class="space-y-4">
                  <h3 class="text-lg font-medium text-gray-900">Lainnya</h3>
                  
                  <div v-if="kunjungan?.lainnyas && kunjungan.lainnyas.length > 0" class="space-y-4">
                    <div v-for="(lainnya, index) in kunjungan.lainnyas" :key="index" class="border border-gray-200 rounded-lg p-4">
                      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div>
                          <label class="block text-sm font-medium text-gray-700">Deskripsi</label>
                          <p class="mt-1 text-sm text-gray-900 bg-gray-50 px-3 py-2 border rounded-md">{{ lainnya.dskp_lainnya || '-' }}</p>
                        </div>
                        <div>
                          <label class="block text-sm font-medium text-gray-700">Jumlah</label>
                          <p class="mt-1 text-sm text-gray-900 bg-gray-50 px-3 py-2 border rounded-md">{{ lainnya.jmlh_lainnaya || '-' }}</p>
                        </div>
                        <div>
                          <label class="block text-sm font-medium text-gray-700">Biaya</label>
                          <p class="mt-1 text-sm text-gray-900 bg-gray-50 px-3 py-2 border rounded-md">{{ formatCurrency(lainnya.bya_lainnya) }}</p>
                        </div>
                        <div>
                          <label class="block text-sm font-medium text-gray-700">Diskon</label>
                          <p class="mt-1 text-sm text-gray-900 bg-gray-50 px-3 py-2 border rounded-md">{{ lainnya.disc_lainnya || '-' }}</p>
                        </div>
                        <div>
                          <label class="block text-sm font-medium text-gray-700">Subtotal</label>
                          <p class="mt-1 text-sm font-bold text-green-600 bg-gray-50 px-3 py-2 border rounded-md">{{ formatCurrency(lainnya.st_lainnya) }}</p>
                        </div>
                        <div>
                          <label class="block text-sm font-medium text-gray-700">Tanggal</label>
                          <p class="mt-1 text-sm text-gray-900 bg-gray-50 px-3 py-2 border rounded-md">{{ formatDate(lainnya.tanggal) }}</p>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div v-else class="text-center py-8 text-gray-500">
                    <i class="fas fa-ellipsis-h text-4xl mb-2"></i>
                    <p>Tidak ada data lainnya</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Total Summary -->
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 mt-6">
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Ringkasan Biaya</h3>
              
              <!-- Sub Total Breakdown -->
              <div class="space-y-3 mb-6">
                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                  <div class="flex items-center">
                    <span class="text-sm font-medium text-gray-700">Konsultasi</span>
                    <span v-if="!kunjungan?.konsuls || kunjungan.konsuls.length === 0" class="ml-2 text-xs text-gray-500">(kosong)</span>
                    <span v-else class="ml-2 text-xs text-green-600">({{ kunjungan.konsuls.length }} item)</span>
                  </div>
                  <span class="text-sm font-semibold text-gray-900">{{ formatCurrency(calculateKonsulTotal()) }}</span>
                </div>
                
                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                  <div class="flex items-center">
                    <span class="text-sm font-medium text-gray-700">Tindakan</span>
                    <span v-if="!kunjungan?.tindaks || kunjungan.tindaks.length === 0" class="ml-2 text-xs text-gray-500">(kosong)</span>
                    <span v-else class="ml-2 text-xs text-green-600">({{ kunjungan.tindaks.length }} item)</span>
                  </div>
                  <span class="text-sm font-semibold text-gray-900">{{ formatCurrency(calculateTindakTotal()) }}</span>
                </div>
                
                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                  <div class="flex items-center">
                    <span class="text-sm font-medium text-gray-700">Alat Kesehatan</span>
                    <span v-if="!kunjungan?.alkes || kunjungan.alkes.length === 0" class="ml-2 text-xs text-gray-500">(kosong)</span>
                    <span v-else class="ml-2 text-xs text-green-600">({{ kunjungan.alkes.length }} item)</span>
                  </div>
                  <span class="text-sm font-semibold text-gray-900">{{ formatCurrency(calculateAlkesTotal()) }}</span>
                </div>
                
                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                  <div class="flex items-center">
                    <span class="text-sm font-medium text-gray-700">Resep</span>
                    <span v-if="!kunjungan?.rsp || kunjungan.rsp.length === 0" class="ml-2 text-xs text-gray-500">(kosong)</span>
                    <span v-else class="ml-2 text-xs text-green-600">({{ kunjungan.rsp.length }} item)</span>
                  </div>
                  <span class="text-sm font-semibold text-gray-900">{{ formatCurrency(calculateRspTotal()) }}</span>
                </div>
                
                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                  <div class="flex items-center">
                    <span class="text-sm font-medium text-gray-700">Lainnya</span>
                    <span v-if="!kunjungan?.lainnyas || kunjungan.lainnyas.length === 0" class="ml-2 text-xs text-gray-500">(kosong)</span>
                    <span v-else class="ml-2 text-xs text-green-600">({{ kunjungan.lainnyas.length }} item)</span>
                  </div>
                  <span class="text-sm font-semibold text-gray-900">{{ formatCurrency(calculateLainnyaTotal()) }}</span>
                </div>
              </div>
              
              <!-- Grand Total -->
              <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex justify-between items-center">
                  <span class="text-lg font-bold text-blue-900">Grand Total</span>
                  <span class="text-2xl font-bold text-blue-900">{{ formatCurrency(calculateTotal()) }}</span>
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link } from '@inertiajs/vue3'

const props = defineProps({
  kunjungan: Object
})

const activeTab = ref('konsul')

const tabs = [
  { id: 'konsul', name: 'Konsultasi' },
  { id: 'tindak', name: 'Tindakan' },
  { id: 'alkes', name: 'Alkes' },
  { id: 'rsp', name: 'Resep' },
  { id: 'lainnya', name: 'Lainnya' }
]

// Helper functions
function formatDate(dateString) {
  if (!dateString) return '-'
  const date = new Date(dateString)
  return date.toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}


function formatCurrency(amount) {
  if (!amount) return 'Rp 0'
  const numericAmount = parseFloat(String(amount).replace(/[^\d]/g, '')) || 0
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0
  }).format(numericAmount)
}

function getTabCount(tabId) {
  if (!props.kunjungan) return 0
  
  switch (tabId) {
    case 'konsul':
      return props.kunjungan.konsuls?.length || 0
    case 'tindak':
      return props.kunjungan.tindaks?.length || 0
    case 'alkes':
      return props.kunjungan.alkes?.length || 0
    case 'rsp':
      return props.kunjungan.rsp?.length || 0
    case 'lainnya':
      return props.kunjungan.lainnyas?.length || 0
    default:
      return 0
  }
}

// Calculate sub totals for each category
function calculateKonsulTotal() {
  if (!props.kunjungan?.konsuls || props.kunjungan.konsuls.length === 0) return 0
  
  return props.kunjungan.konsuls.reduce((total, konsul) => {
    // Calculate: (jumlah * biaya) - diskon
    const jumlah = parseFloat(konsul.jmlh_kons) || 0
    const biaya = parseFloat(konsul.bya_kons) || 0
    const subtotal = jumlah * biaya
    
    // Handle diskon
    let diskon = 0
    if (konsul.disc_kons && konsul.disc_kons !== '0%') {
      const discPercent = parseFloat(String(konsul.disc_kons).replace(/[^\d]/g, '')) || 0
      diskon = (subtotal * discPercent) / 100
    }
    
    return total + Math.max(subtotal - diskon, 0)
  }, 0)
}

function calculateTindakTotal() {
  if (!props.kunjungan?.tindaks || props.kunjungan.tindaks.length === 0) return 0
  
  return props.kunjungan.tindaks.reduce((total, tindak) => {
    // Calculate: (jumlah * biaya) - diskon
    const jumlah = parseFloat(tindak.jmlh_tindak) || 0
    const biaya = parseFloat(tindak.bya_tindak) || 0
    const subtotal = jumlah * biaya
    
    // Handle diskon
    let diskon = 0
    if (tindak.disc_tindak && tindak.disc_tindak !== '0%') {
      const discPercent = parseFloat(String(tindak.disc_tindak).replace(/[^\d]/g, '')) || 0
      diskon = (subtotal * discPercent) / 100
    }
    
    return total + Math.max(subtotal - diskon, 0)
  }, 0)
}

function calculateAlkesTotal() {
  if (!props.kunjungan?.alkes || props.kunjungan.alkes.length === 0) return 0
  
  return props.kunjungan.alkes.reduce((total, alkes) => {
    // Calculate: (jumlah * biaya) - diskon
    const jumlah = parseFloat(alkes.jmlh_alkes) || 0
    const biaya = parseFloat(alkes.bya_alkes) || 0
    const subtotal = jumlah * biaya
    
    // Handle diskon
    let diskon = 0
    if (alkes.disc_alkes && alkes.disc_alkes !== '0%') {
      const discPercent = parseFloat(String(alkes.disc_alkes).replace(/[^\d]/g, '')) || 0
      diskon = (subtotal * discPercent) / 100
    }
    
    return total + Math.max(subtotal - diskon, 0)
  }, 0)
}

function calculateRspTotal() {
  if (!props.kunjungan?.rsp || props.kunjungan.rsp.length === 0) return 0
  
  return props.kunjungan.rsp.reduce((total, rsp) => {
    // Calculate: (jumlah * biaya) - diskon
    const jumlah = parseFloat(rsp.jmlh_rsp) || 0
    const biaya = parseFloat(rsp.bya_rsp) || 0
    const subtotal = jumlah * biaya
    
    // Handle diskon
    let diskon = 0
    if (rsp.disc_rsp && rsp.disc_rsp !== '0%') {
      const discPercent = parseFloat(String(rsp.disc_rsp).replace(/[^\d]/g, '')) || 0
      diskon = (subtotal * discPercent) / 100
    }
    
    return total + Math.max(subtotal - diskon, 0)
  }, 0)
}

function calculateLainnyaTotal() {
  if (!props.kunjungan?.lainnyas || props.kunjungan.lainnyas.length === 0) return 0
  
  return props.kunjungan.lainnyas.reduce((total, lainnya) => {
    // Calculate: (jumlah * biaya) - diskon
    const jumlah = parseFloat(lainnya.jmlh_lainnaya) || 0
    const biaya = parseFloat(lainnya.bya_lainnya) || 0
    const subtotal = jumlah * biaya
    
    // Handle diskon
    let diskon = 0
    if (lainnya.disc_lainnya && lainnya.disc_lainnya !== '0%') {
      const discPercent = parseFloat(String(lainnya.disc_lainnya).replace(/[^\d]/g, '')) || 0
      diskon = (subtotal * discPercent) / 100
    }
    
    return total + Math.max(subtotal - diskon, 0)
  }, 0)
}

// Calculate total cost
function calculateTotal() {
  return calculateKonsulTotal() + 
         calculateTindakTotal() + 
         calculateAlkesTotal() + 
         calculateRspTotal() + 
         calculateLainnyaTotal()
}
</script>