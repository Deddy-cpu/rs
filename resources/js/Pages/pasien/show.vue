<template>
  <AuthenticatedLayout>
    <Head :title="`Detail Pasien - ${psn?.nm_p || ''}`" />
    
    <!-- Background dengan gradient dan pattern -->
    <div class="min-h-screen bg-cover bg-center overflow-x-hidden w-full" style="background-image: url('/images/bg-login.png'); padding: 1.5rem; box-sizing: border-box;">
      <!-- Decorative background elements -->
      <div class="absolute top-0 left-0 h-96 bg-gradient-to-r from-[#CFF7E3]/30 to-[#DCEBFF]/30 transform -skew-y-1 overflow-hidden pointer-events-none" style="width: 100vw; max-width: 100%;"></div>
      <div class="absolute bottom-0 right-0 w-96 h-96 bg-gradient-to-l from-[#E7E4FF]/20 to-[#FFE5E0]/20 rounded-full overflow-hidden pointer-events-none" style="transform: translateY(50%); right: 0; max-width: 50vw;"></div>
      
      <div class="mx-auto py-8 relative z-10 w-full" style="max-width: min(1280px, calc(100vw - 3rem)); box-sizing: border-box;">
        <!-- Flash Messages dengan animasi -->
        <transition name="slide-fade">
          <div v-if="flash.success" class="mb-6 bg-[#CFF7E3] border border-[#2ECC71] text-[#2ECC71] px-6 py-4 rounded-xl shadow-lg animate-slide-in">
            <div class="flex items-center justify-between">
              <div class="flex items-center">
                <div class="w-8 h-8 bg-[#2ECC71] rounded-full flex items-center justify-center mr-3 animate-pulse">
                  <i class="fas fa-check text-white text-sm"></i>
                </div>
                <span class="font-medium">{{ flash.success }}</span>
              </div>
              <button
                @click="clearFlashMessage"
                class="text-[#2ECC71] hover:text-[#27AE60] ml-4"
                aria-label="Tutup"
              >
                <i class="fas fa-times text-sm"></i>
              </button>
            </div>
          </div>
        </transition>
        
        <transition name="slide-fade">
          <div v-if="flash.error" class="mb-6 bg-[#FFE5E0] border border-[#FF6B6B] text-[#D32F2F] px-6 py-4 rounded-xl shadow-lg animate-slide-in">
            <div class="flex items-center justify-between">
              <div class="flex items-center">
                <div class="w-8 h-8 bg-[#D32F2F] rounded-full flex items-center justify-center mr-3 animate-pulse">
                  <i class="fas fa-exclamation text-white text-sm"></i>
                </div>
                <span class="font-medium">{{ flash.error }}</span>
              </div>
              <button
                @click="clearFlashMessage"
                class="text-[#D32F2F] hover:text-[#B71C1C] ml-4"
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
              <div class="w-16 h-16 bg-gradient-to-br from-[#2ECC71] to-[#1976D2] rounded-2xl flex items-center justify-center shadow-lg flex-shrink-0">
                <i class="fas fa-user text-white text-2xl"></i>
              </div>
              <div class="min-w-0 flex-1">
                <h1 class="text-4xl font-bold text-[#1A1A1A] break-words">
                  {{ psn?.nm_p }}
                </h1>
                <p class="text-[#666666] mt-2 text-lg flex items-center break-words">
                  <i class="fas fa-info-circle mr-2 text-[#1976D2] flex-shrink-0"></i>
                  <span class="break-words">Detail lengkap informasi pasien</span>
                </p>
              </div>
            </div>
            <div class="flex flex-col sm:flex-row gap-3 flex-shrink-0">
              <button 
                v-if="isPendaftaran"
                @click="router.visit(`/pasien/${psn?.id}/edit`)"
                class="px-6 py-3 bg-[#2ECC71] hover:bg-[#27AE60] text-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 flex items-center font-medium"
              >
                <i class="fas fa-edit mr-2"></i>Edit Pasien
              </button>
              <button 
                @click="openDeletePasienModal"
                class="px-6 py-3 bg-[#FF6B6B] hover:bg-[#FF5252] text-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 flex items-center font-medium"
              >
                <i class="fas fa-trash mr-2"></i>Hapus Pasien
              </button>
              <button 
                @click="router.visit(backRoute)"
                class="px-6 py-3 bg-[#1976D2] hover:bg-[#1565C0] text-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 flex items-center font-medium"
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
                <div class="w-12 h-12 bg-gradient-to-br from-[#2ECC71] to-[#1976D2] rounded-xl flex items-center justify-center shadow-lg mr-4 flex-shrink-0">
                  <i class="fas fa-user text-white text-xl"></i>
                </div>
                <h2 class="text-3xl font-bold text-[#1A1A1A] break-words">
                  Informasi Data Pasien
                </h2>
              </div>
              
              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 w-full" style="max-width: 100%;">
                <div class="group bg-[#DCEBFF] rounded-xl p-6 hover:shadow-lg transition-all duration-300 border border-[#1976D2]/20">
                  <div class="flex items-center mb-3">
                    <i class="fas fa-user-tag text-[#1976D2] mr-2"></i>
                    <label class="text-sm font-semibold text-[#1976D2] uppercase tracking-wide">Nama Lengkap</label>
                  </div>
                  <p class="text-xl font-bold text-[#1A1A1A] group-hover:text-[#1976D2] transition-colors">{{ psn?.nm_p }}</p>
                </div>
                
                <div class="group bg-[#CFF7E3] rounded-xl p-6 hover:shadow-lg transition-all duration-300 border border-[#2ECC71]/20">
                  <div class="flex items-center mb-3">
                    <i class="fas fa-id-card text-[#2ECC71] mr-2"></i>
                    <label class="text-sm font-semibold text-[#2ECC71] uppercase tracking-wide">NIK</label>
                  </div>
                  <p class="text-xl font-bold text-[#1A1A1A] group-hover:text-[#2ECC71] transition-colors">{{ psn?.nik }}</p>
                </div>
                
                <div class="group bg-[#E7E4FF] rounded-xl p-6 hover:shadow-lg transition-all duration-300 border border-[#9C88FF]/20">
                  <div class="flex items-center mb-3">
                    <i class="fas fa-shield-alt text-[#7B68EE] mr-2"></i>
                    <label class="text-sm font-semibold text-[#7B68EE] uppercase tracking-wide">No BPJS</label>
                  </div>
                  <p class="text-xl font-bold text-[#1A1A1A] group-hover:text-[#7B68EE] transition-colors">{{ psn?.no_bpjs }}</p>
                </div>
                
                <div class="group bg-[#FFE5E0] rounded-xl p-6 hover:shadow-lg transition-all duration-300 border border-[#FF8A80]/20">
                  <div class="flex items-center mb-3">
                    <i class="fas fa-pray text-[#FF6B6B] mr-2"></i>
                    <label class="text-sm font-semibold text-[#FF6B6B] uppercase tracking-wide">Agama</label>
                  </div>
                  <p class="text-xl font-bold text-[#1A1A1A] group-hover:text-[#FF6B6B] transition-colors">{{ psn?.agm }}</p>
                </div>
                
                <div class="group bg-[#DCEBFF] rounded-xl p-6 hover:shadow-lg transition-all duration-300 border border-[#1976D2]/20">
                  <div class="flex items-center mb-3">
                    <i class="fas fa-birthday-cake text-[#1976D2] mr-2"></i>
                    <label class="text-sm font-semibold text-[#1976D2] uppercase tracking-wide">Tanggal Lahir</label>
                  </div>
                  <p class="text-xl font-bold text-[#1A1A1A] group-hover:text-[#1976D2] transition-colors">{{ psn?.tgl_lahir }}</p>
                </div>
                
                <div class="group bg-[#CFF7E3] rounded-xl p-6 hover:shadow-lg transition-all duration-300 border border-[#2ECC71]/20">
                  <div class="flex items-center mb-3">
                    <i class="fas fa-venus-mars text-[#2ECC71] mr-2"></i>
                    <label class="text-sm font-semibold text-[#2ECC71] uppercase tracking-wide">Jenis Kelamin</label>
                  </div>
                  <p class="text-xl font-bold">
                    <span :class="psn?.kelamin === 'L' ? 'text-[#1976D2]' : 'text-[#FF6B6B]'" class="group-hover:scale-105 transition-transform">
                      {{ psn?.kelamin === 'L' ? 'Laki-laki' : psn?.kelamin === 'P' ? 'Perempuan' : 'Tidak Diketahui' }}
                    </span>
                  </p>
                </div>
                
                <div class="group bg-[#F7F9FB] rounded-xl p-6 hover:shadow-lg transition-all duration-300 border border-gray-200 md:col-span-2 lg:col-span-3 w-full overflow-hidden">
                  <div class="flex items-center mb-3">
                    <i class="fas fa-map-marker-alt text-[#666666] mr-2 flex-shrink-0"></i>
                    <label class="text-sm font-semibold text-[#666666] uppercase tracking-wide break-words">Alamat Lama</label>
                  </div>
                  <p class="text-lg font-semibold text-[#1A1A1A] group-hover:text-[#1976D2] transition-colors break-words">{{ psn?.almt_L }}</p>
                </div>
                
                <div class="group bg-[#F7F9FB] rounded-xl p-6 hover:shadow-lg transition-all duration-300 border border-gray-200 md:col-span-2 lg:col-span-3 w-full overflow-hidden">
                  <div class="flex items-center mb-3">
                    <i class="fas fa-home text-[#666666] mr-2 flex-shrink-0"></i>
                    <label class="text-sm font-semibold text-[#666666] uppercase tracking-wide break-words">Alamat Baru</label>
                  </div>
                  <p class="text-lg font-semibold text-[#1A1A1A] group-hover:text-[#1976D2] transition-colors break-words">{{ psn?.almt_B }}</p>
                </div>

                <div class="group bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-6 hover:shadow-lg transition-all duration-300 border border-blue-200/50">
                  <div class="flex items-center mb-3">
                    <i class="fas fa-phone text-blue-500 mr-2"></i>
                    <label class="text-sm font-semibold text-blue-700 uppercase tracking-wide">Nomor Telepon</label>
                  </div>
                  <p class="text-xl font-bold text-gray-900 group-hover:text-blue-800 transition-colors">{{ psn?.no_telp || '-' }}</p>
                </div>

                <div class="group bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-xl p-6 hover:shadow-lg transition-all duration-300 border border-indigo-200/50">
                  <div class="flex items-center mb-3">
                    <i class="fas fa-male text-indigo-500 mr-2"></i>
                    <label class="text-sm font-semibold text-indigo-700 uppercase tracking-wide">Nama Ayah</label>
                  </div>
                  <p class="text-xl font-bold text-gray-900 group-hover:text-indigo-800 transition-colors">{{ psn?.nama_ayah || '-' }}</p>
                </div>

                <div class="group bg-gradient-to-br from-pink-50 to-pink-100 rounded-xl p-6 hover:shadow-lg transition-all duration-300 border border-pink-200/50">
                  <div class="flex items-center mb-3">
                    <i class="fas fa-female text-pink-500 mr-2"></i>
                    <label class="text-sm font-semibold text-pink-700 uppercase tracking-wide">Nama Ibu</label>
                  </div>
                  <p class="text-xl font-bold text-gray-900 group-hover:text-pink-800 transition-colors">{{ psn?.nama_ibu || '-' }}</p>
                </div>
              </div>
            </div>
          </div>

            <!-- Tab 2: Kunjungan -->
            <div v-if="activeTab === 'kunjungan'" class="space-y-6 w-full overflow-x-hidden">
              <div class="bg-white/90 backdrop-blur-sm shadow-2xl rounded-2xl p-8 border border-white/20 hover:shadow-3xl transition-all duration-300 w-full overflow-x-hidden" style="box-sizing: border-box;">
                <div class="flex items-center justify-between mb-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-gradient-to-br from-[#2ECC71] to-[#1976D2] rounded-xl flex items-center justify-center shadow-lg mr-4">
                    <i class="fas fa-calendar-check text-white text-xl"></i>
                  </div>
                    <h2 class="text-3xl font-bold text-[#1A1A1A]">
                    Data Kunjungan
                  </h2>
                </div>
                  <!-- Tombol Tambah Kunjungan -->
                <button
                  @click="router.visit(`/pasien/${psn?.id}/kunjungan/create`)"
                    class="px-6 py-3 bg-[#2ECC71] hover:bg-[#27AE60] text-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 flex items-center font-medium"
                >
                  <i class="fas fa-plus mr-2"></i>Tambah Kunjungan
                </button>
                </div>
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
                    class="w-full pl-12 pr-4 py-3 bg-[#F7F9FB] border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#2ECC71] focus:border-transparent transition-all duration-300 text-[#1A1A1A] placeholder-gray-400"
                    autocomplete="off"
                  />
                  <button
                    v-if="searchQuery"
                    @click="searchQuery = ''"
                    class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-[#FF6B6B] transition-colors"
                  >
                    <i class="fas fa-times text-lg"></i>
                  </button>
                </div>
                <div v-if="searchQuery" class="mt-3 flex items-center text-sm text-[#666666]">
                  <i class="fas fa-info-circle mr-2 text-[#2ECC71]"></i>
                  <span>Menampilkan {{ filteredKunjungan.length }} dari {{ pasienData?.length || 0 }} kunjungan</span>
                </div>
              </div>
              
              <div v-if="filteredKunjungan && filteredKunjungan.length > 0" class="space-y-4">
                <div 
                  v-for="(kunjungan, index) in filteredKunjungan" 
                  :key="kunjungan.id"
                  class="group bg-white rounded-xl p-6 border border-gray-200/50 hover:shadow-lg hover:border-[#2ECC71]/30 transition-all duration-300 relative"
                  @click.self="(e) => { console.log('🟠 Card clicked directly'); closeDropdown() }"
                >
                  <!-- Debug info -->
                  <!-- {{ console.log('🔄 Rendering kunjungan:', kunjungan.id, 'type:', typeof kunjungan.id) || '' }} -->
                  <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <!-- Informasi Kunjungan -->
                    <div class="flex-1 grid grid-cols-1 md:grid-cols-3 gap-4">
                      <!-- Tanggal -->
                      <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-[#DCEBFF] rounded-lg flex items-center justify-center flex-shrink-0">
                          <i class="fas fa-calendar text-[#1976D2]"></i>
                      </div>
                        <div>
                          <p class="text-xs text-[#666666] mb-1">Tanggal</p>
                          <p class="text-sm font-semibold text-[#1A1A1A]">{{ formatDate(kunjungan.tgl_reg) }}</p>
                    </div>
                  </div>
                  
                      <!-- No Registrasi -->
                      <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-[#CFF7E3] rounded-lg flex items-center justify-center flex-shrink-0">
                          <i class="fas fa-hashtag text-[#2ECC71]"></i>
                      </div>
                        <div>
                          <p class="text-xs text-[#666666] mb-1">No Registrasi</p>
                          <p class="text-sm font-semibold text-[#1A1A1A]">{{ kunjungan.no_reg }}</p>
                    </div>
                    </div>
                    
                      <!-- Jenis Perawatan -->
                      <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-[#FFE5E0] rounded-lg flex items-center justify-center flex-shrink-0">
                          <i class="fas fa-stethoscope text-[#FF6B6B]"></i>
                      </div>
                        <div>
                          <p class="text-xs text-[#666666] mb-1">Jenis Perawatan</p>
                          <p class="text-sm font-semibold text-[#1A1A1A]">{{ kunjungan.perawatan }}</p>
                    </div>
                      </div>
                    </div>
                    
                    <!-- Tombol Detail, Edit dan Hapus -->
                    <div class="flex items-center justify-end gap-2 flex-shrink-0">
                      <button
                        @click="router.visit(`/kunjungan/${kunjungan.id}`)"
                        type="button"
                        class="px-4 py-2 bg-[#7B68EE] hover:bg-[#6A5ACD] text-white rounded-lg shadow-md hover:shadow-lg transition-all duration-300 flex items-center gap-2 font-medium"
                        title="Detail Kunjungan"
                      >
                        <i class="fas fa-eye"></i>
                        <span class="hidden sm:inline">Detail</span>
                      </button>
                      <button
                        @click="router.visit(`/pasien/${psn?.id}/kunjungan/${kunjungan.id}/edit`)"
                        type="button"
                        class="px-4 py-2 bg-[#1976D2] hover:bg-[#1565C0] text-white rounded-lg shadow-md hover:shadow-lg transition-all duration-300 flex items-center gap-2 font-medium"
                        title="Edit Kunjungan"
                      >
                        <i class="fas fa-edit"></i>
                        <span class="hidden sm:inline">Edit</span>
                      </button>
                      <button
                        @click="openDeleteModal(kunjungan)"
                        type="button"
                        class="px-4 py-2 bg-[#FF6B6B] hover:bg-[#FF5252] text-white rounded-lg shadow-md hover:shadow-lg transition-all duration-300 flex items-center gap-2 font-medium"
                        title="Hapus Kunjungan"
                      >
                        <i class="fas fa-trash"></i>
                        <span class="hidden sm:inline">Hapus</span>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
                  
              <div v-else-if="pasienData && pasienData.length === 0" class="text-center py-16">
                <div class="w-24 h-24 bg-[#F7F9FB] rounded-full flex items-center justify-center mx-auto mb-6">
                  <i class="fas fa-calendar-times text-4xl text-[#666666]"></i>
                      </div>
                <h3 class="text-2xl font-bold text-[#1A1A1A] mb-3">Belum Ada Kunjungan</h3>
                <p class="text-[#666666] text-lg">Pasien ini belum memiliki riwayat kunjungan</p>
                    </div>
              
              <div v-else-if="searchQuery && filteredKunjungan.length === 0" class="text-center py-16">
                <div class="w-24 h-24 bg-[#FFE5E0] rounded-full flex items-center justify-center mx-auto mb-6">
                  <i class="fas fa-search text-4xl text-[#FF6B6B]"></i>
                </div>
                <h3 class="text-2xl font-bold text-[#1A1A1A] mb-3">Tidak Ditemukan</h3>
                <p class="text-[#666666] text-lg">Tidak ada kunjungan yang sesuai dengan pencarian "<strong>{{ searchQuery }}</strong>"</p>
                <button
                  @click="searchQuery = ''"
                  class="mt-4 px-6 py-2 bg-[#1976D2] hover:bg-[#1565C0] text-white rounded-lg shadow-md hover:shadow-lg transition-all duration-300 flex items-center mx-auto"
                >
                  <i class="fas fa-times mr-2"></i>Hapus Pencarian
                </button>
                  </div>
                  
              <!-- Modal Konfirmasi Hapus Kunjungan -->
              <transition name="fade">
                <div v-if="isDeleteModalOpen" class="fixed inset-0 bg-black bg-opacity-40 z-50 flex items-center justify-center" @click="closeDeleteModal">
                  <div class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-lg mx-auto border border-gray-200 relative animate-fade-in" @click.stop>
                    <button @click="closeDeleteModal" class="absolute right-4 top-4 text-gray-400 hover:text-gray-700 text-xl" title="Tutup" aria-label="Tutup">
                      &times;
                    </button>
                    <div class="flex flex-col items-center text-center">
                      <svg class="w-12 h-12 text-[#FF6B6B] mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862
                                 a2 2 0 01-1.995-1.858L5 7
                                 m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1
                                 h-4a1 1 0 00-1 1v3M4 7h16"/>
                      </svg>
                      <h2 class="text-lg font-bold text-[#1A1A1A] mb-2">Konfirmasi Hapus Kunjungan</h2>
                      <div class="text-[#666666] mt-2 mb-6">
                        <div v-if="selectedKunjunganDelete">
                          <div class="mb-2">Apakah Anda yakin ingin menghapus kunjungan untuk pasien <span class="font-semibold">{{ psn?.nm_p }}</span>?</div>
                          <div class="text-sm text-[#666666] mb-2">
                            No Reg: <span class="font-mono">{{ selectedKunjunganDelete.no_reg }}</span><br>
                            Tanggal: <span class="font-mono">{{ formatDate(selectedKunjunganDelete.tgl_reg) }}</span>
                      </div>
                          <div class="text-xs text-[#999999]">Tindakan ini tidak dapat dibatalkan!</div>
                    </div>
                  </div>
                      <div class="flex flex-row gap-3 w-full mt-2">
                        <button
                          @click="closeDeleteModal"
                          type="button"
                          class="flex-1 px-5 py-3 rounded-xl bg-[#F7F9FB] text-[#1A1A1A] hover:bg-gray-200 transition-all font-semibold border border-gray-300"
                        >
                          Batal
                        </button>
                        <button
                          @click="deleteKunjungan(selectedKunjunganDelete?.id)"
                          :disabled="!selectedKunjunganDelete || !selectedKunjunganDelete.id"
                          type="button"
                          class="flex-1 px-5 py-3 rounded-xl bg-[#FF6B6B] text-white hover:bg-[#FF5252] transition-all font-semibold shadow disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                          Ya, Hapus
                        </button>
                </div>
              </div>
                </div>
              </div>
              </transition>

              <!-- Modal Konfirmasi Hapus Pasien -->
              <transition name="fade">
                <div v-if="isDeletePasienModalOpen" class="fixed inset-0 bg-black bg-opacity-40 z-50 flex items-center justify-center" @click="closeDeletePasienModal">
                  <div class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-lg mx-auto border border-gray-200 relative animate-fade-in" @click.stop>
                    <button @click="closeDeletePasienModal" class="absolute right-4 top-4 text-gray-400 hover:text-gray-700 text-xl" title="Tutup" aria-label="Tutup">
                      &times;
                    </button>
                    <div class="flex flex-col items-center text-center">
                      <svg class="w-16 h-16 text-[#FF6B6B] mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862
                                 a2 2 0 01-1.995-1.858L5 7
                                 m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1
                                 h-4a1 1 0 00-1 1v3M4 7h16"/>
                      </svg>
                      <h2 class="text-2xl font-bold text-[#1A1A1A] mb-3">Konfirmasi Hapus Pasien</h2>
                      <div class="text-[#666666] mt-2 mb-6">
                        <div class="mb-2">Apakah Anda yakin ingin menghapus data pasien <span class="font-semibold text-[#1A1A1A]">{{ psn?.nm_p }}</span>?</div>
                        <div class="text-sm text-[#666666] mb-2">
                          <p class="mb-1">Data yang akan dihapus:</p>
                          <ul class="list-disc list-inside text-left space-y-1">
                            <li>Data biodata pasien</li>
                            <li>Semua kunjungan terkait</li>
                            <li>Semua transaksi terkait</li>
                          </ul>
                </div>
                        <div class="text-xs text-[#FF6B6B] font-semibold mt-3">Tindakan ini tidak dapat dibatalkan!</div>
                      </div>
                      <div class="flex flex-row gap-3 w-full mt-2">
                <button
                          @click="closeDeletePasienModal"
                          type="button"
                          class="flex-1 px-5 py-3 rounded-xl bg-[#F7F9FB] text-[#1A1A1A] hover:bg-gray-200 transition-all font-semibold border border-gray-300"
                        >
                          Batal
                        </button>
                        <button
                          @click="deletePasien"
                          :disabled="!psn || !psn.id"
                          type="button"
                          class="flex-1 px-5 py-3 rounded-xl bg-[#FF6B6B] text-white hover:bg-[#FF5252] transition-all font-semibold shadow disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                          Ya, Hapus
                </button>
              </div>
            </div>
                  </div>
                </div>
              </transition>
          </div>

            <!-- Tab 3: Riwayat Kunjungan -->
            <div v-if="activeTab === 'riwayat'" class="space-y-6 w-full overflow-x-hidden">
              <div class="bg-white/90 backdrop-blur-sm shadow-2xl rounded-2xl p-8 border border-white/20 hover:shadow-3xl transition-all duration-300 w-full overflow-x-hidden" style="box-sizing: border-box;">
              <div class="flex items-center mb-8">
                <div class="w-12 h-12 bg-gradient-to-br from-[#2ECC71] to-[#1976D2] rounded-xl flex items-center justify-center shadow-lg mr-4">
                  <i class="fas fa-history text-white text-xl"></i>
                </div>
                <h2 class="text-3xl font-bold text-[#1A1A1A]">
                  Riwayat Lengkap Kunjungan
                </h2>
              </div>
              
              <div v-if="pasienData && pasienData.length > 0" class="space-y-6">
                <div 
                  v-for="(kunjungan, index) in pasienData" 
                  :key="kunjungan.id"
                  class="border border-gray-200 rounded-lg p-6"
                >
                  <div class="flex flex-col md:flex-row md:justify-between md:items-start gap-4 mb-6">
                    <h3 class="text-xl font-semibold text-gray-800">
                      Kunjungan {{ formatDate(kunjungan.tgl_reg) }}
                    </h3>
                    <div class="flex flex-wrap items-center gap-2">
                      <span class="px-3 py-1 bg-[#CFF7E3] text-[#2ECC71] rounded-full text-sm font-medium border border-[#2ECC71]/20">
                        {{ kunjungan.no_reg }}
                      </span>
                      <span class="px-3 py-1 bg-[#DCEBFF] text-[#1976D2] rounded-full text-sm font-medium border border-[#1976D2]/20">
                        {{ kunjungan.perawatan }}
                      </span>
                      <button
                        @click="router.visit(`/kunjungan/${kunjungan.id}`)"
                        class="px-3 py-1 bg-[#7B68EE] hover:bg-[#6A5ACD] text-white rounded-full text-sm font-medium transition-colors flex items-center gap-1"
                      >
                        <i class="fas fa-eye text-xs"></i>
                        Detail Kunjungan
                      </button>
                    </div>
                  </div>
                  
                  <!-- Konsultasi -->
                  <div v-if="kunjungan.konsuls && kunjungan.konsuls.length > 0" class="mb-6">
                    <h4 class="text-lg font-semibold text-gray-700 mb-3 flex items-center">
                      <i class="fas fa-stethoscope mr-2 text-[#1976D2]"></i>
                      Konsultasi Medis
                    </h4>
                    <div class="bg-[#DCEBFF] rounded-lg p-4 w-full overflow-x-hidden">
                      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 w-full" style="max-width: 100%;">
                        <div v-for="konsul in kunjungan.konsuls" :key="konsul.id">
                          <div class="space-y-2">
                            <p><span class="font-medium">Dokter:</span> {{ konsul.dokter }}</p>
                            <p><span class="font-medium">Diagnosa:</span> {{ konsul.dskp_kons }}</p>
                            <p><span class="font-medium">Jumlah Konsultasi:</span> {{ konsul.jmlh_kons }}</p>
                            <p><span class="font-medium">Biaya:</span> Rp {{ formatNumber(konsul.bya_kons) }}</p>
                            <p><span class="font-medium">Status:</span> 
                              <span :class="konsul.st_kons === 'selesai' ? 'text-[#2ECC71]' : 'text-[#FFA726]'">
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
                      <i class="fas fa-procedures mr-2 text-[#2ECC71]"></i>
                      Tindakan Medis
                    </h4>
                    <div class="bg-[#CFF7E3] rounded-lg p-4 w-full overflow-x-hidden">
                      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 w-full" style="max-width: 100%;">
                        <div v-for="tindak in kunjungan.tindaks" :key="tindak.id">
                          <div class="space-y-2">
                            <p><span class="font-medium">Dokter:</span> {{ tindak.dktr_tindak }}</p>
                            <p><span class="font-medium">Deskripsi:</span> {{ tindak.dskp_tindak }}</p>
                            <p><span class="font-medium">Jumlah:</span> {{ tindak.jmlh_tindak }}</p>
                            <p><span class="font-medium">Biaya:</span> Rp {{ formatNumber(tindak.bya_tindak) }}</p>
                            <p><span class="font-medium">Status:</span> 
                              <span :class="tindak.st_tindak === 'selesai' ? 'text-[#2ECC71]' : 'text-[#FFA726]'">
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
                      <i class="fas fa-tools mr-2 text-[#FF6B6B]"></i>
                      Alat Kesehatan
                    </h4>
                    <div class="bg-[#FFE5E0] rounded-lg p-4 w-full overflow-x-hidden">
                      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 w-full" style="max-width: 100%;">
                        <div v-for="alkes in kunjungan.alkes" :key="alkes.id">
                          <div class="space-y-2">
                            <p><span class="font-medium">Alat:</span> {{ alkes.dskp_alkes }}</p>
                            <p><span class="font-medium">Jumlah:</span> {{ alkes.jmlh_alkes }}</p>
                            <p><span class="font-medium">Biaya:</span> Rp {{ formatNumber(alkes.bya_alkes) }}</p>
                            <p><span class="font-medium">Status:</span> 
                              <span :class="alkes.st_alkes === 'selesai' ? 'text-[#2ECC71]' : 'text-[#FFA726]'">
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
                      <i class="fas fa-pills mr-2 text-[#7B68EE]"></i>
                      Resep Obat
                    </h4>
                    <div class="bg-[#E7E4FF] rounded-lg p-4 w-full overflow-x-hidden">
                      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 w-full" style="max-width: 100%;">
                        <div v-for="rsp in kunjungan.rsp" :key="rsp.id">
                          <div class="space-y-2">
                            <p><span class="font-medium">Obat:</span> {{ rsp.dskp_rsp }}</p>
                            <p><span class="font-medium">Jumlah:</span> {{ rsp.jmlh_rsp }}</p>
                            <p><span class="font-medium">Biaya:</span> Rp {{ formatNumber(rsp.bya_rsp) }}</p>
                            <p><span class="font-medium">Status:</span> 
                              <span :class="rsp.st_rsp === 'selesai' ? 'text-[#2ECC71]' : 'text-[#FFA726]'">
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
                    <div class="bg-[#F7F9FB] rounded-lg p-4 w-full overflow-x-hidden">
                      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 w-full" style="max-width: 100%;">
                        <div v-for="lainnya in kunjungan.lainnyas" :key="lainnya.id">
                          <div class="space-y-2">
                            <p><span class="font-medium">Layanan:</span> {{ lainnya.dskp_lainnya }}</p>
                            <p><span class="font-medium">Jumlah:</span> {{ lainnya.jmlh_lainnya }}</p>
                            <p><span class="font-medium">Biaya:</span> Rp {{ formatNumber(lainnya.bya_lainnya) }}</p>
                            <p><span class="font-medium">Status:</span> 
                              <span :class="lainnya.st_lainnya === 'selesai' ? 'text-[#2ECC71]' : 'text-[#FFA726]'">
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
                      <i class="fas fa-receipt mr-2 text-[#1976D2]"></i>
                      Detail Transaksi
                    </h4>
                    <div class="bg-[#E7E4FF] rounded-lg p-4 w-full overflow-x-hidden">
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
                              <span :class="transaksi.status === 'lunas' ? 'text-[#2ECC71]' : 'text-[#FFA726]'">
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
                <div class="w-24 h-24 bg-[#F7F9FB] rounded-full flex items-center justify-center mx-auto mb-6">
                  <i class="fas fa-history text-4xl text-[#666666]"></i>
                </div>
                <h3 class="text-2xl font-bold text-[#1A1A1A] mb-3">Belum Ada Riwayat</h3>
                <p class="text-[#666666] text-lg">Pasien ini belum memiliki riwayat kunjungan lengkap</p>
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
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
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
const openDropdown = ref(null)
const isDeleteModalOpen = ref(false)
const selectedKunjunganDelete = ref(null)
const isDeletePasienModalOpen = ref(false)

// Watch openDropdown for debugging
watch(openDropdown, (newVal, oldVal) => {
  console.log('👀 openDropdown changed from', oldVal, 'to', newVal)
  console.trace('Stack trace:')
}, { deep: true })

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

// Helper function to check if dropdown should be open
const isDropdownOpen = (kunjunganId) => {
  const current = openDropdown.value
  const target = kunjunganId
  
  // Try both strict and number comparison
  const isOpen = current === target || 
                 Number(current) === Number(target) ||
                 String(current) === String(target)
  
  if (isOpen) {
    console.log('✅ Dropdown is open for kunjungan:', target, 'openDropdown:', current)
  }
  
  return isOpen
}

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
  openDropdown.value = null
}

// Handle dropdown click with debug
const handleDropdownClick = (kunjunganId, event) => {
  console.log('🟢 handleDropdownClick called!')
  console.log('🟢 kunjunganId:', kunjunganId, 'type:', typeof kunjunganId)
  console.log('🟢 event:', event)
  console.log('🟢 event.target:', event.target)
  console.log('🟢 event.currentTarget:', event.currentTarget)
  event.stopPropagation()
  event.preventDefault()
  
  // Ensure kunjunganId is converted to the same type
  const id = Number(kunjunganId)
  console.log('🟢 Converted id:', id)
  toggleDropdown(id)
}

// Toggle dropdown
const toggleDropdown = (kunjunganId) => {
  console.log('🔵 toggleDropdown called with kunjunganId:', kunjunganId, 'type:', typeof kunjunganId)
  console.log('🔵 Current openDropdown value:', openDropdown.value, 'type:', typeof openDropdown.value)
  
  // Convert both to same type for comparison
  const currentId = openDropdown.value !== null ? Number(openDropdown.value) : null
  const newId = Number(kunjunganId)
  
  console.log('🔵 Comparing currentId:', currentId, 'with newId:', newId)
  
  if (currentId === newId) {
    openDropdown.value = null
    console.log('🔵 Closed dropdown - same ID')
  } else {
    openDropdown.value = newId
    console.log('🔵 Opened dropdown with ID:', newId)
  }
  
  console.log('🔵 Final openDropdown value:', openDropdown.value, 'type:', typeof openDropdown.value)
}

// Close dropdown ketika klik di luar
const closeDropdown = () => {
  console.log('🔴 closeDropdown called')
  openDropdown.value = null
}

// Buka modal hapus
const openDeleteModal = (kunjungan) => {
  selectedKunjunganDelete.value = kunjungan
  isDeleteModalOpen.value = true
  openDropdown.value = null
}

// Tutup modal hapus
const closeDeleteModal = () => {
  isDeleteModalOpen.value = false
  selectedKunjunganDelete.value = null
}

// Hapus kunjungan
const deleteKunjungan = (kunjunganId) => {
  if (!kunjunganId) {
    alert('ID kunjungan tidak ditemukan. Silakan coba lagi.')
    closeDeleteModal()
    return
  }

  router.delete(route('kunjungan.destroy', kunjunganId), {
    preserveState: false,
    preserveScroll: false,
    onSuccess: () => {
      closeDeleteModal()
      router.reload({
        only: ['pasienData', 'flash'],
        preserveState: false,
        preserveScroll: false
      })
    },
    onError: (errors) => {
      closeDeleteModal()
      router.reload({
        only: ['flash'],
        preserveState: true
      })
    }
  })
}

// Modal Hapus Pasien
const openDeletePasienModal = () => {
  isDeletePasienModalOpen.value = true
}

const closeDeletePasienModal = () => {
  isDeletePasienModalOpen.value = false
}

// Hapus pasien
const deletePasien = () => {
  if (!props.psn || !props.psn.id) {
    alert('ID pasien tidak ditemukan. Silakan coba lagi.')
    closeDeletePasienModal()
    return
  }

  router.delete(route('pasien.destroy', props.psn.id), {
    preserveState: false,
    preserveScroll: false,
    onSuccess: () => {
      // Redirect to pasien index page after successful deletion
      router.visit('/pasien')
    },
    onError: (errors) => {
      closeDeletePasienModal()
      router.reload({
        only: ['flash'],
        preserveState: true
      })
    }
  })
}

// Close dropdown on escape key
const handleEscape = (e) => {
  if (e.key === 'Escape') {
    closeDropdown()
    closeDeleteModal()
    closeDeletePasienModal()
  }
}

onMounted(() => {
  console.log('✅ Component mounted')
  document.addEventListener('keydown', handleEscape)
  
  // Close dropdown when clicking outside
  const handleClickOutside = (e) => {
    // Wait for current event handlers to finish
    setTimeout(() => {
      const target = e.target
      console.log('🟡 Click detected on:', target)
      console.log('🟡 target.tagName:', target.tagName)
      console.log('🟡 target.classList:', target.classList)
      const dropdownContainer = target.closest('.dropdown-menu-wrapper')
      const button = target.closest('button[aria-label="Menu"]')
      console.log('🟡 dropdownContainer:', dropdownContainer)
      console.log('🟡 button:', button)
      console.log('🟡 openDropdown.value:', openDropdown.value)
      
      // Don't close if clicking on dropdown button or its container
      if ((dropdownContainer || button) && openDropdown.value !== null) {
        console.log('🟡 Not closing dropdown - click is on dropdown button')
        return
      }
      
      if (openDropdown.value !== null) {
        console.log('🟡 Closing dropdown because click is outside')
        closeDropdown()
      } else {
        console.log('🟡 Not closing - dropdown already closed')
      }
    }, 0)
  }
  
  // Don't use capture phase - let button handle click first
  document.addEventListener('click', handleClickOutside)
  
  // Store handler for cleanup
  window._dropdownClickHandler = handleClickOutside
})

onUnmounted(() => {
  console.log('❌ Component unmounted')
  document.removeEventListener('keydown', handleEscape)
  if (window._dropdownClickHandler) {
    document.removeEventListener('click', window._dropdownClickHandler)
    delete window._dropdownClickHandler
  }
})
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

@keyframes fade {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from, .fade-leave-to {
  opacity: 0;
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
