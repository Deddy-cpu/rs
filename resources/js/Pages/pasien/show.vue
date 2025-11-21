<template>
  <AuthenticatedLayout>
    <Head :title="`Detail Pasien - ${psn?.nm_p || ''}`" />
    
    <!-- Background dengan gradient dan pattern -->
    <div class="min-h-screen bg-cover bg-center overflow-x-hidden w-full" style="background-image: url('/images/bg-login.png'); padding: 1.5rem; box-sizing: border-box;">
      <!-- Decorative background elements -->
      <div class="absolute top-0 left-0 h-96 bg-gradient-to-r from-red-100/30 to-pink-100/30 transform -skew-y-1 overflow-hidden pointer-events-none" style="width: 100vw; max-width: 100%;"></div>
      <div class="absolute bottom-0 right-0 w-96 h-96 bg-gradient-to-l from-red-100/20 to-pink-100/20 rounded-full overflow-hidden pointer-events-none" style="transform: translateY(50%); right: 0; max-width: 50vw;"></div>
      
      <div class="mx-auto py-8 relative z-10 w-full" style="max-width: min(1280px, calc(100vw - 3rem)); box-sizing: border-box;">
        <!-- Flash Messages sederhana -->
        <transition name="slide-fade">
          <div v-if="flash.success" class="mb-4 bg-green-50 border border-green-300 text-green-700 px-4 py-3 rounded-lg">
            <div class="flex items-center justify-between">
              <div class="flex items-center">
                <i class="fas fa-check text-green-600 mr-2"></i>
                <span>{{ flash.success }}</span>
              </div>
              <button
                @click="clearFlashMessage"
                class="text-green-600 hover:text-green-800 ml-4"
                aria-label="Tutup"
              >
                <i class="fas fa-times text-sm"></i>
              </button>
            </div>
          </div>
        </transition>
        
        <transition name="slide-fade">
          <div v-if="flash.error" class="mb-4 bg-red-50 border border-red-300 text-red-700 px-4 py-3 rounded-lg">
            <div class="flex items-center justify-between">
              <div class="flex items-center">
                <i class="fas fa-exclamation text-red-600 mr-2"></i>
                <span>{{ flash.error }}</span>
              </div>
              <button
                @click="clearFlashMessage"
                class="text-red-600 hover:text-red-800 ml-4"
                aria-label="Tutup"
              >
                <i class="fas fa-times text-sm"></i>
              </button>
            </div>
          </div>
        </transition>

        <!-- Header dengan glassmorphism effect -->
        <div class="bg-white/80 backdrop-blur-md rounded-2xl shadow-2xl border border-white/20 p-8 mb-8 animate-fade-in w-full overflow-x-hidden" style="box-sizing: border-box;">
          <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 w-full">
            <div class="flex items-center space-x-4 w-full min-w-0 flex-shrink">
              <div class="w-16 h-16 bg-gradient-to-br from-red-500 to-pink-600 rounded-2xl flex items-center justify-center shadow-lg flex-shrink-0">
                <i class="fas fa-user text-white text-2xl"></i>
              </div>
              <div class="min-w-0 flex-1">
                <h1 class="text-4xl font-bold bg-gradient-to-r from-gray-900 to-gray-700 bg-clip-text text-transparent break-words">
                  {{ psn?.nm_p }}
                </h1>
                <p class="text-gray-600 mt-2 text-lg flex items-center break-words">
                  <i class="fas fa-info-circle mr-2 text-red-500 flex-shrink-0"></i>
                  <span class="break-words">Detail lengkap informasi pasien</span>
                </p>
              </div>
            </div>
            <div class="flex flex-col sm:flex-row gap-3 flex-shrink-0">
              <button 
                @click="router.visit(`/pasien/${psn?.id}/edit`)"
                class="px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 flex items-center font-medium"
              >
                <i class="fas fa-edit mr-2"></i>Edit Pasien
              </button>
              <button 
                @click="router.visit(backRoute)"
                class="px-6 py-3 bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 flex items-center font-medium"
              >
                <i class="fas fa-arrow-left mr-2"></i>Kembali
              </button>
            </div>
          </div>
        </div>

      <!-- Tab Navigation -->
      <TabNavigation 
        :tabs="tabs" 
        @tab-change="handleTabChange"
      >
        <template #default="{ activeTab }">
          <!-- Tab 1: Data Pasien -->
          <div v-if="activeTab === 'data-pasien'" class="space-y-6 w-full overflow-x-hidden">
            <div class="bg-white/90 backdrop-blur-sm shadow-2xl rounded-2xl p-8 border border-white/20 hover:shadow-3xl transition-all duration-300 w-full overflow-x-hidden" style="box-sizing: border-box;">
              <div class="flex items-center mb-8 w-full">
                <div class="w-12 h-12 bg-gradient-to-br from-red-500 to-red-600 rounded-xl flex items-center justify-center shadow-lg mr-4 flex-shrink-0">
                  <i class="fas fa-user text-white text-xl"></i>
                </div>
                <h2 class="text-3xl font-bold bg-gradient-to-r from-red-600 to-pink-600 bg-clip-text text-transparent break-words">
                  Informasi Data Pasien
                </h2>
              </div>
              
              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 w-full" style="max-width: 100%;">
                <div class="group bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-6 hover:shadow-lg transition-all duration-300 border border-blue-200/50">
                  <div class="flex items-center mb-3">
                    <i class="fas fa-user-tag text-blue-500 mr-2"></i>
                    <label class="text-sm font-semibold text-blue-700 uppercase tracking-wide">Nama Lengkap</label>
                  </div>
                  <p class="text-xl font-bold text-gray-900 group-hover:text-blue-800 transition-colors">{{ psn?.nm_p }}</p>
                </div>
                
                <div class="group bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-6 hover:shadow-lg transition-all duration-300 border border-green-200/50">
                  <div class="flex items-center mb-3">
                    <i class="fas fa-id-card text-green-500 mr-2"></i>
                    <label class="text-sm font-semibold text-green-700 uppercase tracking-wide">NIK</label>
                  </div>
                  <p class="text-xl font-bold text-gray-900 group-hover:text-green-800 transition-colors">{{ psn?.nik }}</p>
                </div>
                
                <div class="group bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-6 hover:shadow-lg transition-all duration-300 border border-purple-200/50">
                  <div class="flex items-center mb-3">
                    <i class="fas fa-shield-alt text-purple-500 mr-2"></i>
                    <label class="text-sm font-semibold text-purple-700 uppercase tracking-wide">No BPJS</label>
                  </div>
                  <p class="text-xl font-bold text-gray-900 group-hover:text-purple-800 transition-colors">{{ psn?.no_bpjs }}</p>
                </div>
                
                <div class="group bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl p-6 hover:shadow-lg transition-all duration-300 border border-orange-200/50">
                  <div class="flex items-center mb-3">
                    <i class="fas fa-pray text-orange-500 mr-2"></i>
                    <label class="text-sm font-semibold text-orange-700 uppercase tracking-wide">Agama</label>
                  </div>
                  <p class="text-xl font-bold text-gray-900 group-hover:text-orange-800 transition-colors">{{ psn?.agm }}</p>
                </div>
                
                <div class="group bg-gradient-to-br from-pink-50 to-pink-100 rounded-xl p-6 hover:shadow-lg transition-all duration-300 border border-pink-200/50">
                  <div class="flex items-center mb-3">
                    <i class="fas fa-birthday-cake text-pink-500 mr-2"></i>
                    <label class="text-sm font-semibold text-pink-700 uppercase tracking-wide">Tanggal Lahir</label>
                  </div>
                  <p class="text-xl font-bold text-gray-900 group-hover:text-pink-800 transition-colors">{{ psn?.tgl_lahir }}</p>
                </div>
                
                <div class="group bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-xl p-6 hover:shadow-lg transition-all duration-300 border border-indigo-200/50">
                  <div class="flex items-center mb-3">
                    <i class="fas fa-venus-mars text-indigo-500 mr-2"></i>
                    <label class="text-sm font-semibold text-indigo-700 uppercase tracking-wide">Jenis Kelamin</label>
                  </div>
                  <p class="text-xl font-bold">
                    <span :class="psn?.kelamin === 'L' ? 'text-blue-600' : 'text-pink-600'" class="group-hover:scale-105 transition-transform">
                      {{ psn?.kelamin === 'L' ? 'Laki-laki' : psn?.kelamin === 'P' ? 'Perempuan' : 'Tidak Diketahui' }}
                    </span>
                  </p>
                </div>
                
                <div class="group bg-gradient-to-br from-teal-50 to-teal-100 rounded-xl p-6 hover:shadow-lg transition-all duration-300 border border-teal-200/50 md:col-span-2 lg:col-span-3 w-full overflow-hidden">
                  <div class="flex items-center mb-3">
                    <i class="fas fa-map-marker-alt text-teal-500 mr-2 flex-shrink-0"></i>
                    <label class="text-sm font-semibold text-teal-700 uppercase tracking-wide break-words">Alamat Lahir</label>
                  </div>
                  <p class="text-lg font-semibold text-gray-900 group-hover:text-teal-800 transition-colors break-words">{{ psn?.almt_L }}</p>
                </div>
                
                <div class="group bg-gradient-to-br from-cyan-50 to-cyan-100 rounded-xl p-6 hover:shadow-lg transition-all duration-300 border border-cyan-200/50 md:col-span-2 lg:col-span-3 w-full overflow-hidden">
                  <div class="flex items-center mb-3">
                    <i class="fas fa-home text-cyan-500 mr-2 flex-shrink-0"></i>
                    <label class="text-sm font-semibold text-cyan-700 uppercase tracking-wide break-words">Alamat Berdomisili</label>
                  </div>
                  <p class="text-lg font-semibold text-gray-900 group-hover:text-cyan-800 transition-colors break-words">{{ psn?.almt_B }}</p>
                </div>
              </div>
            </div>
          </div>

            <!-- Tab 2: Kunjungan -->
            <div v-if="activeTab === 'kunjungan'" class="space-y-6 w-full overflow-x-hidden">
              <div class="bg-white/90 backdrop-blur-sm shadow-2xl rounded-2xl p-8 border border-white/20 hover:shadow-3xl transition-all duration-300 w-full overflow-x-hidden" style="box-sizing: border-box;">
                <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-6 gap-4 w-full">
                <div class="flex items-center">
                  <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg mr-4">
                    <i class="fas fa-calendar-check text-white text-xl"></i>
                  </div>
                  <h2 class="text-3xl font-bold bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent">
                    Data Kunjungan
                  </h2>
                </div>
                <button
                  v-if="isPendaftaran"
                  @click="router.visit(`/pasien/${psn?.id}/kunjungan/create`)"
                  class="px-6 py-3 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 flex items-center font-medium"
                >
                  <i class="fas fa-plus mr-2"></i>Tambah Kunjungan
                </button>
              </div>
              
              <!-- Search Box -->
              <div class="bg-white/90 backdrop-blur-sm shadow-xl rounded-2xl p-6 border border-white/20 w-full overflow-x-hidden" style="box-sizing: border-box;">
                <div class="relative">
                  <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400 text-lg"></i>
                  </div>
                  <input
                    v-model="searchQuery"
                    type="text"
                    placeholder="Cari kunjungan berdasarkan No Registrasi, Tanggal, Perawatan, Penjamin, MRN, No SJP, ICD..."
                    class="w-full pl-12 pr-4 py-3 bg-gradient-to-r from-gray-50 to-white border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-300 text-gray-700 placeholder-gray-400"
                    autocomplete="off"
                  />
                  <button
                    v-if="searchQuery"
                    @click="searchQuery = ''"
                    class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-red-500 transition-colors"
                  >
                    <i class="fas fa-times text-lg"></i>
                  </button>
                </div>
                <div v-if="searchQuery" class="mt-3 flex items-center text-sm text-gray-600">
                  <i class="fas fa-info-circle mr-2 text-green-500"></i>
                  <span>Menampilkan {{ filteredKunjungan.length }} dari {{ pasienData?.length || 0 }} kunjungan</span>
                </div>
              </div>
              
              <div v-if="filteredKunjungan && filteredKunjungan.length > 0" class="space-y-6">
                <div 
                  v-for="(kunjungan, index) in filteredKunjungan" 
                  :key="kunjungan.id"
                  class="group bg-gradient-to-br from-white to-gray-50 rounded-2xl p-8 border border-gray-200/50 hover:shadow-xl hover:border-red-300/50 transition-all duration-300 hover:-translate-y-1"
                >
                  <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-6 gap-4">
                    <div class="flex items-center">
                      <div class="w-10 h-10 bg-gradient-to-br from-red-500 to-pink-600 rounded-lg flex items-center justify-center shadow-md mr-4">
                        <span class="text-white font-bold text-lg">{{ getOriginalIndex(kunjungan.id) + 1 }}</span>
                      </div>
                      <h3 class="text-2xl font-bold text-gray-800 group-hover:text-red-600 transition-colors">
                        Kunjungan #{{ getOriginalIndex(kunjungan.id) + 1 }}
                      </h3>
                    </div>
                    <div class="flex items-center space-x-3">
                      <button
                        v-if="isPendaftaran"
                        @click="router.visit(`/pasien/${psn?.id}/kunjungan/${kunjungan.id}/edit`)"
                        class="px-4 py-2 bg-gradient-to-r from-yellow-500 to-orange-500 hover:from-yellow-600 hover:to-orange-600 text-white rounded-lg shadow-md hover:shadow-lg transition-all duration-300 transform hover:scale-105 flex items-center font-medium"
                      >
                        <i class="fas fa-edit mr-2"></i>Edit
                      </button>
                      <span class="px-4 py-2 bg-gradient-to-r from-red-100 to-red-200 text-red-800 rounded-xl text-sm font-semibold shadow-md">
                        {{ kunjungan.no_reg }}
                      </span>
                    </div>
                  </div>
                  
                  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 w-full" style="max-width: 100%;">
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-4 border border-blue-200/50">
                      <div class="flex items-center mb-2">
                        <i class="fas fa-calendar text-blue-500 mr-2"></i>
                        <label class="text-sm font-semibold text-blue-700 uppercase tracking-wide">Tanggal Registrasi</label>
                      </div>
                      <p class="text-lg font-bold text-gray-900">{{ formatDate(kunjungan.tgl_reg) }}</p>
                    </div>
                    
                    <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-4 border border-green-200/50">
                      <div class="flex items-center mb-2">
                        <i class="fas fa-hashtag text-green-500 mr-2"></i>
                        <label class="text-sm font-semibold text-green-700 uppercase tracking-wide">No Registrasi</label>
                      </div>
                      <p class="text-lg font-bold text-gray-900">{{ kunjungan.no_reg }}</p>
                    </div>
                    
                    <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-4 border border-purple-200/50">
                      <div class="flex items-center mb-2">
                        <i class="fas fa-id-badge text-purple-500 mr-2"></i>
                        <label class="text-sm font-semibold text-purple-700 uppercase tracking-wide">MRN</label>
                      </div>
                      <p class="text-lg font-bold text-gray-900">{{ kunjungan.mrn }}</p>
                    </div>
                    
                    <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl p-4 border border-orange-200/50">
                      <div class="flex items-center mb-2">
                        <i class="fas fa-stethoscope text-orange-500 mr-2"></i>
                        <label class="text-sm font-semibold text-orange-700 uppercase tracking-wide">Jenis Perawatan</label>
                      </div>
                      <p class="text-lg font-bold text-gray-900">{{ kunjungan.perawatan }}</p>
                    </div>
                    
                    <div class="bg-gradient-to-br from-pink-50 to-pink-100 rounded-xl p-4 border border-pink-200/50">
                      <div class="flex items-center mb-2">
                        <i class="fas fa-shield-alt text-pink-500 mr-2"></i>
                        <label class="text-sm font-semibold text-pink-700 uppercase tracking-wide">Penjamin</label>
                      </div>
                      <p class="text-lg font-bold text-gray-900">{{ kunjungan.penjamin }}</p>
                    </div>
                    
                    <div class="bg-gradient-to-br from-teal-50 to-teal-100 rounded-xl p-4 border border-teal-200/50">
                      <div class="flex items-center mb-2">
                        <i class="fas fa-file-medical text-teal-500 mr-2"></i>
                        <label class="text-sm font-semibold text-teal-700 uppercase tracking-wide">No SJP</label>
                      </div>
                      <p class="text-lg font-bold text-gray-900">{{ kunjungan.no_sjp || '-' }}</p>
                    </div>
                  </div>
                  
                  <div v-if="kunjungan.icd" class="mt-6 pt-6 border-t border-gray-200">
                    <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-xl p-4 border border-indigo-200/50">
                      <div class="flex items-center mb-2">
                        <i class="fas fa-code text-indigo-500 mr-2"></i>
                        <label class="text-sm font-semibold text-indigo-700 uppercase tracking-wide">ICD Code</label>
                      </div>
                      <p class="text-lg font-bold text-gray-900">{{ kunjungan.icd }}</p>
                    </div>
                  </div>
                  
                  <div v-if="kunjungan.kunjungan" class="mt-6 pt-6 border-t border-gray-200">
                    <div class="bg-gradient-to-br from-cyan-50 to-cyan-100 rounded-xl p-4 border border-cyan-200/50">
                      <div class="flex items-center mb-2">
                        <i class="fas fa-hospital text-cyan-500 mr-2"></i>
                        <label class="text-sm font-semibold text-cyan-700 uppercase tracking-wide">Jenis Perawatan</label>
                      </div>
                      <p class="text-lg font-bold text-gray-900">{{ kunjungan.kunjungan }}</p>
                    </div>
                  </div>
                </div>
              </div>
              
              <div v-else-if="pasienData && pasienData.length === 0" class="text-center py-16">
                <div class="w-24 h-24 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center mx-auto mb-6">
                  <i class="fas fa-calendar-times text-4xl text-gray-400"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-600 mb-3">Belum Ada Kunjungan</h3>
                <p class="text-gray-500 text-lg">Pasien ini belum memiliki riwayat kunjungan</p>
              </div>
              
              <div v-else-if="searchQuery && filteredKunjungan.length === 0" class="text-center py-16">
                <div class="w-24 h-24 bg-gradient-to-br from-red-100 to-pink-100 rounded-full flex items-center justify-center mx-auto mb-6">
                  <i class="fas fa-search text-4xl text-red-400"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-600 mb-3">Tidak Ditemukan</h3>
                <p class="text-gray-500 text-lg">Tidak ada kunjungan yang sesuai dengan pencarian "<strong>{{ searchQuery }}</strong>"</p>
                <button
                  @click="searchQuery = ''"
                  class="mt-4 px-6 py-2 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white rounded-lg shadow-md hover:shadow-lg transition-all duration-300 flex items-center mx-auto"
                >
                  <i class="fas fa-times mr-2"></i>Hapus Pencarian
                </button>
              </div>
            </div>
          </div>

            <!-- Tab 3: Riwayat Kunjungan -->
            <div v-if="activeTab === 'riwayat'" class="space-y-6 w-full overflow-x-hidden">
              <div class="bg-white/90 backdrop-blur-sm shadow-2xl rounded-2xl p-8 border border-white/20 hover:shadow-3xl transition-all duration-300 w-full overflow-x-hidden" style="box-sizing: border-box;">
              <div class="flex items-center mb-8">
                <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl flex items-center justify-center shadow-lg mr-4">
                  <i class="fas fa-history text-white text-xl"></i>
                </div>
                <h2 class="text-3xl font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent">
                  Riwayat Lengkap Kunjungan
                </h2>
              </div>
              
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
                      <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm font-medium">
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
                    <div class="bg-blue-50 rounded-lg p-4 w-full overflow-x-hidden">
                      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 w-full" style="max-width: 100%;">
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
                    <div class="bg-green-50 rounded-lg p-4 w-full overflow-x-hidden">
                      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 w-full" style="max-width: 100%;">
                        <div v-for="tindak in kunjungan.tindaks" :key="tindak.id">
                          <div class="space-y-2">
                            <p><span class="font-medium">Dokter:</span> {{ tindak.dktr_tindak }}</p>
                            <p><span class="font-medium">Deskripsi:</span> {{ tindak.dskp_tindak }}</p>
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
                    <div class="bg-orange-50 rounded-lg p-4 w-full overflow-x-hidden">
                      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 w-full" style="max-width: 100%;">
                        <div v-for="alkes in kunjungan.alkes" :key="alkes.id">
                          <div class="space-y-2">
                            <p><span class="font-medium">Alat:</span> {{ alkes.dskp_alkes }}</p>
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
                    <div class="bg-purple-50 rounded-lg p-4 w-full overflow-x-hidden">
                      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 w-full" style="max-width: 100%;">
                        <div v-for="rsp in kunjungan.rsp" :key="rsp.id">
                          <div class="space-y-2">
                            <p><span class="font-medium">Obat:</span> {{ rsp.dskp_rsp }}</p>
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
                    <div class="bg-gray-50 rounded-lg p-4 w-full overflow-x-hidden">
                      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 w-full" style="max-width: 100%;">
                        <div v-for="lainnya in kunjungan.lainnyas" :key="lainnya.id">
                          <div class="space-y-2">
                            <p><span class="font-medium">Layanan:</span> {{ lainnya.dskp_lainnya }}</p>
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
                    <div class="bg-indigo-50 rounded-lg p-4 w-full overflow-x-hidden">
                      <div v-for="transaksi in kunjungan.transaksi" :key="transaksi.id">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 w-full" style="max-width: 100%;">
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
              
              <div v-else class="text-center py-16">
                <div class="w-24 h-24 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center mx-auto mb-6">
                  <i class="fas fa-history text-4xl text-gray-400"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-600 mb-3">Belum Ada Riwayat</h3>
                <p class="text-gray-500 text-lg">Pasien ini belum memiliki riwayat kunjungan lengkap</p>
              </div>
              </div>
            </div>
          </template>
        </TabNavigation>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import TabNavigation from '@/Components/TabNavigation.vue'
import { useAuth } from '@/composables/useAuth'

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

const { isPendaftaran, isDokter } = useAuth()

const activeTab = ref('data-pasien')
const searchQuery = ref('')

// Determine back route based on user role
const backRoute = computed(() => {
  if (isDokter.value) {
    return '/dokter/pasien-kunjungan'
  }
  return '/pasien'
})

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

// Clear flash message without reload
const clearFlashMessage = () => {
  // Clear any pending timeout
  if (flashTimeout) {
    clearTimeout(flashTimeout)
    flashTimeout = null
  }
  
  // Clear flash message by visiting same page without flash
  router.get(window.location.pathname, {}, {
    preserveState: true,
    preserveScroll: true,
    only: []
  })
}

// Auto-hide flash message after 1 minute (60 seconds)
let flashTimeout = null
watch(() => props.flash?.success, (newVal) => {
  // Clear previous timeout
  if (flashTimeout) {
    clearTimeout(flashTimeout)
    flashTimeout = null
  }
  
  if (newVal) {
    flashTimeout = setTimeout(() => {
      clearFlashMessage()
    }, 60000) // 1 menit (60 detik)
  }
})

// Also watch for error messages
watch(() => props.flash?.error, (newVal) => {
  // Clear previous timeout
  if (flashTimeout) {
    clearTimeout(flashTimeout)
    flashTimeout = null
  }
  
  if (newVal) {
    flashTimeout = setTimeout(() => {
      clearFlashMessage()
    }, 60000) // 1 menit (60 detik)
  }
})

// Filter kunjungan berdasarkan search query
const filteredKunjungan = computed(() => {
  if (!props.pasienData || props.pasienData.length === 0) return []
  if (!searchQuery.value || searchQuery.value.trim() === '') return props.pasienData
  
  const query = searchQuery.value.toLowerCase().trim()
  
  return props.pasienData.filter(kunjungan => {
    // Search berdasarkan berbagai field
    const noReg = (kunjungan.no_reg || '').toLowerCase()
    const tglReg = formatDate(kunjungan.tgl_reg).toLowerCase()
    const mrn = (kunjungan.mrn || '').toLowerCase()
    const perawatan = (kunjungan.perawatan || '').toLowerCase()
    const penjamin = (kunjungan.penjamin || '').toLowerCase()
    const noSjp = (kunjungan.no_sjp || '').toLowerCase()
    const icd = (kunjungan.icd || '').toLowerCase()
    const jenisKunjungan = (kunjungan.kunjungan || '').toLowerCase()
    
    return noReg.includes(query) ||
           tglReg.includes(query) ||
           mrn.includes(query) ||
           perawatan.includes(query) ||
           penjamin.includes(query) ||
           noSjp.includes(query) ||
           icd.includes(query) ||
           jenisKunjungan.includes(query)
  })
})

// Get original index dari array pasienData
const getOriginalIndex = (kunjunganId) => {
  if (!props.pasienData) return 0
  return props.pasienData.findIndex(k => k.id === kunjunganId)
}

// Clear search ketika tab berubah
const handleTabChange = (tabId) => {
  activeTab.value = tabId
  if (tabId !== 'kunjungan') {
    searchQuery.value = ''
  }
}
</script>

<style scoped>
/* Flash message transitions */
.slide-fade-enter-active {
  transition: all 0.3s ease-out;
}

.slide-fade-leave-active {
  transition: all 0.3s ease-in;
}

.slide-fade-enter-from {
  transform: translateY(-20px);
  opacity: 0;
}

.slide-fade-leave-to {
  transform: translateY(-20px);
  opacity: 0;
}

/* Custom animations */
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
    box-shadow: 0 0 5px rgba(239, 68, 68, 0.3);
  }
  50% {
    box-shadow: 0 0 20px rgba(239, 68, 68, 0.6);
  }
}

.animate-slide-in {
  animation: slide-in 0.6s ease-out;
}

.animate-fade-in {
  animation: fade-in 0.8s ease-out;
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

/* Prevent horizontal scroll - Global */
:deep(html),
:deep(body),
:deep(#app) {
  overflow-x: hidden !important;
  max-width: 100vw !important;
  width: 100% !important;
  position: relative;
}

/* Smooth transitions */
* {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  box-sizing: border-box !important;
}

/* Ensure all containers don't overflow */
:deep(div),
div[class*="max-w"],
div[class*="container"] {
  max-width: 100% !important;
  overflow-x: hidden !important;
  box-sizing: border-box !important;
}

/* Ensure grid and flex containers don't overflow */
.grid,
.flex,
:deep(.grid),
:deep(.flex) {
  max-width: 100% !important;
  overflow-x: hidden !important;
  box-sizing: border-box !important;
}

/* Ensure text wraps properly */
p, span, div, h1, h2, h3, h4, h5, h6, label {
  word-wrap: break-word !important;
  overflow-wrap: break-word !important;
  max-width: 100% !important;
}

/* Prevent any element from causing horizontal scroll */
* {
  max-width: 100% !important;
  box-sizing: border-box !important;
}

/* Ensure images and media don't overflow */
img, video, iframe, svg {
  max-width: 100% !important;
  height: auto !important;
}

/* Prevent tables from causing overflow */
table {
  width: 100% !important;
  max-width: 100% !important;
  table-layout: auto !important;
  overflow-x: auto !important;
  display: block !important;
}

/* Hide scrollbar completely - Global */
:deep(::-webkit-scrollbar),
:deep(::-webkit-scrollbar-track),
:deep(::-webkit-scrollbar-thumb) {
  display: none !important;
  width: 0 !important;
  height: 0 !important;
  background: transparent !important;
}

:deep(html),
:deep(body),
:deep(#app),
:deep(*) {
  -ms-overflow-style: none !important;
  scrollbar-width: none !important;
}

/* Hide scrollbar for all elements */
::-webkit-scrollbar,
::-webkit-scrollbar-track,
::-webkit-scrollbar-thumb {
  display: none !important;
  width: 0 !important;
  height: 0 !important;
  background: transparent !important;
}

html, body, #app, * {
  -ms-overflow-style: none !important;
  scrollbar-width: none !important;
}
</style>
