<template>
  <AuthenticatedLayout>
    <Head title="Detail Transaksi Pasien" />
    
    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-50 py-8">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
          <div class="text-center mb-6">
            <h1 class="text-4xl md:text-5xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent mb-2">
              <span class="inline-block align-middle mr-2">💊</span>
            {{ existingPasien ? 'Tambah Transaksi ke Kunjungan' : 'Detail Transaksi Pasien' }}
          </h1>
            <p class="text-gray-600 text-lg">
            {{ existingPasien ? 'Menambahkan transaksi medis ke kunjungan yang sudah ada' : 'Kelola detail transaksi medis pasien dengan lengkap' }}
            </p>
          </div>
          
          <div v-if="existingPasien" class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-2xl p-6 shadow-lg">
            <div class="flex items-center gap-3">
              <div class="p-3 bg-blue-500 rounded-xl shadow-lg">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
              <div>
                <p class="text-sm font-semibold text-blue-800">Kunjungan sudah ada</p>
                <p class="text-lg font-bold text-blue-900">No Reg {{ existingPasien.no_reg }} - {{ existingPasien.tgl_reg }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Stepper -->
        <div class="bg-white shadow-xl rounded-2xl p-6 mb-8 border border-gray-100">
          <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div class="flex-1">
              <div class="flex items-center justify-center lg:justify-start gap-2 lg:gap-4">
                <div :class="['flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200', 
                  currentStep === 1 ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg' : 'bg-gray-100 text-gray-700']">
                  <div :class="['w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold', 
                    currentStep === 1 ? 'bg-white text-blue-600' : 'bg-gray-300 text-gray-600']">1</div>
                  <span class="hidden sm:inline">Konsultasi & Tindakan</span>
                  <span class="sm:hidden">Konsultasi</span>
                </div>
                <div class="w-8 h-px bg-gray-300 hidden lg:block"></div>
                <div :class="['flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200', 
                  currentStep === 2 ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg' : 'bg-gray-100 text-gray-700']">
                  <div :class="['w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold', 
                    currentStep === 2 ? 'bg-white text-blue-600' : 'bg-gray-300 text-gray-600']">2</div>
                  <span class="hidden sm:inline">Resep & Alkes</span>
                  <span class="sm:hidden">Resep</span>
                </div>
                <div class="w-8 h-px bg-gray-300 hidden lg:block"></div>
                <div :class="['flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200', 
                  currentStep === 3 ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg' : 'bg-gray-100 text-gray-700']">
                  <div :class="['w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold', 
                    currentStep === 3 ? 'bg-white text-blue-600' : 'bg-gray-300 text-gray-600']">3</div>
                  <span class="hidden sm:inline">Layanan Lainnya</span>
                  <span class="sm:hidden">Lainnya</span>
                </div>
                <div class="w-8 h-px bg-gray-300 hidden lg:block"></div>
                <div :class="['flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200', 
                  currentStep === 4 ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg' : 'bg-gray-100 text-gray-700']">
                  <div :class="['w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold', 
                    currentStep === 4 ? 'bg-white text-blue-600' : 'bg-gray-300 text-gray-600']">4</div>
                  <span class="hidden sm:inline">Ringkasan & Simpan</span>
                  <span class="sm:hidden">Simpan</span>
                </div>
              </div>
            </div>
            <div class="flex items-center gap-3">
              <button @click="prevStep" :disabled="currentStep === 1" 
                class="px-6 py-3 text-sm font-semibold border border-gray-300 rounded-xl hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Sebelumnya
              </button>
              <button v-if="currentStep < 4" @click="nextStep" 
                class="px-6 py-3 text-sm font-semibold bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl hover:from-blue-700 hover:to-indigo-700 shadow-lg hover:shadow-xl transition-all duration-200 flex items-center gap-2">
                Berikutnya
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                </svg>
              </button>
            </div>
          </div>
        </div>

        <form @submit.prevent="submitForm" class="space-y-8">
          <!-- Informasi Pasien (selalu tampil di atas) -->
          <div class="bg-white shadow-xl rounded-2xl p-8 border border-gray-100">
            <div class="flex items-center gap-3 mb-6">
              <div class="p-3 bg-blue-100 rounded-xl">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
              </div>
              <h2 class="text-2xl font-bold text-gray-900">Informasi Pasien</h2>
            </div>
            <input v-if="form.psn_id" type="hidden" v-model="form.psn_id" />
            <div v-if="form.psn_id" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-4 border border-blue-100">
                <p class="text-sm font-medium text-blue-600 mb-1">Nama Pasien</p>
                <p class="text-lg font-bold text-blue-900">{{ form.nm_p }}</p>
              </div>
              <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-4 border border-green-100">
                <p class="text-sm font-medium text-green-600 mb-1">NIK</p>
                <p class="text-lg font-bold text-green-900">{{ psn?.nik || '-' }}</p>
              </div>
              <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-4 border border-purple-100">
                <p class="text-sm font-medium text-purple-600 mb-1">No BPJS</p>
                <p class="text-lg font-bold text-purple-900">{{ psn?.no_bpjs || '-' }}</p>
              </div>
              <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl p-4 border border-orange-100 md:col-span-2 lg:col-span-1">
                <p class="text-sm font-medium text-orange-600 mb-1">Alamat</p>
                <p class="text-lg font-bold text-orange-900">{{ form.almt_B }}</p>
              </div>
              <div v-if="existingPasien" class="bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-xl p-4 border border-indigo-100">
                <p class="text-sm font-medium text-indigo-600 mb-1">No Registrasi</p>
                <p class="text-lg font-bold text-indigo-900">{{ form.no_reg }}</p>
              </div>
              <div v-if="existingPasien" class="bg-gradient-to-br from-teal-50 to-teal-100 rounded-xl p-4 border border-teal-100">
                <p class="text-sm font-medium text-teal-600 mb-1">Tanggal Kunjungan</p>
                <p class="text-lg font-bold text-teal-900">{{ form.tgl_reg }}</p>
              </div>
              <div class="md:col-span-2 lg:col-span-3 flex justify-center">
                <button type="button" @click="router.visit('/pasien')" 
                  class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-all duration-200 font-semibold flex items-center gap-2">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                  </svg>
                  Ganti Pasien
                </button>
              </div>
            </div>
            <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700">Nama Pasien</label>
                <div class="relative">
                  <input v-model="form.nm_p" type="text" required 
                    class="w-full pl-4 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 bg-gray-50 focus:bg-white" 
                    placeholder="Masukkan nama pasien" />
                  <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                  </div>
                </div>
              </div>
              <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700">Alamat</label>
                <div class="relative">
                  <input v-model="form.almt_B" type="text" required 
                    class="w-full pl-4 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 bg-gray-50 focus:bg-white" 
                    placeholder="Masukkan alamat" />
                  <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                  </div>
                </div>
              </div>
              <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700">Jenis Perawatan</label>
                <div class="relative">
                  <select v-model="form.perawatan" required 
                    class="w-full pl-4 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 bg-gray-50 focus:bg-white appearance-none">
                  <option value="">Pilih jenis perawatan</option>
                  <option value="Rawat Jalan">Rawat Jalan</option>
                  <option value="Rawat Inap">Rawat Inap</option>
                  <option value="IGD">IGD (Instalasi Gawat Darurat)</option>
                  <option value="ICU">ICU (Intensive Care Unit)</option>
                  <option value="Operasi">Operasi</option>
                  <option value="Konsultasi">Konsultasi</option>
                </select>
                  <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                  </div>
                </div>
              </div>
              <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700">Penjamin</label>
                <div class="relative">
                  <select v-model="form.penjamin" required 
                    class="w-full pl-4 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 bg-gray-50 focus:bg-white appearance-none">
                  <option value="">Pilih penjamin</option>
                  <option value="BPJS">BPJS Kesehatan</option>
                  <option value="Asuransi Swasta">Asuransi Swasta</option>
                  <option value="Umum">Umum (Pribadi)</option>
                  <option value="Perusahaan">Perusahaan</option>
                  <option value="Pemerintah">Pemerintah</option>
                </select>
                  <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                  </div>
                </div>
              </div>
              <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700">Tanggal Registrasi</label>
                <div class="relative">
                  <input v-model="form.tgl_reg" type="date" required 
                    class="w-full pl-4 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 bg-gray-50 focus:bg-white" />
                  <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- STEP 1: Konsultasi & Tindakan -->
          <div v-show="currentStep === 1" class="space-y-8">
            <!-- Konsultasi Table -->
            <div class="bg-white shadow-xl rounded-2xl p-8 border border-gray-100">
              <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                  <div class="p-3 bg-blue-100 rounded-xl">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                    </svg>
                  </div>
                  <h2 class="text-2xl font-bold text-gray-900">Detail Konsultasi Medis</h2>
                </div>
                <button type="button" @click="addKonsul()" 
                  class="px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 font-semibold shadow-lg hover:shadow-xl flex items-center gap-2">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                  </svg>
                  Tambah Konsultasi
                </button>
              </div>
              <div class="overflow-x-auto">
                <table class="w-full text-sm">
                  <thead class="bg-gradient-to-r from-blue-500 to-indigo-500 text-white">
                    <tr>
                      <th class="px-4 py-3 text-left font-semibold">Dokter</th>
                      <th class="px-4 py-3 text-left font-semibold">Deskripsi Konsultasi</th>
                      <th class="px-4 py-3 text-left font-semibold">Jumlah</th>
                      <th class="px-4 py-3 text-left font-semibold">Biaya</th>
                      <th class="px-4 py-3 text-left font-semibold">Diskon</th>
                      <th class="px-4 py-3 text-left font-semibold">Subtotal</th>
                      <th class="px-4 py-3 text-left font-semibold">Tanggal</th>
                      <th class="px-4 py-3 text-center font-semibold">Aksi</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-gray-200">
                    <tr v-for="(row, i) in konsuls" :key="i" class="hover:bg-blue-50/50 transition-colors">
                      <td class="px-4 py-3">
                        <input v-model="row.dokter" 
                          class="w-full border border-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" 
                          placeholder="Nama dokter" />
                      </td>
                      <td class="px-4 py-3">
                        <input v-model="row.dskp_kons" 
                          class="w-full border border-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" 
                          placeholder="Deskripsi konsultasi" />
                      </td>
                      <td class="px-4 py-3">
                        <input v-model="row.jmlh_kons" type="number" min="1" 
                          class="w-full border border-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" />
                      </td>
                      <td class="px-4 py-3">
                        <input v-model="row.bya_kons" @input="updateSubtotal(row, 'bya_kons','disc_kons','st_kons','jmlh_kons')" type="number" min="0" 
                          class="w-full border border-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" 
                          placeholder="0" />
                      </td>
                      <td class="px-4 py-3">
                        <input v-model="row.disc_kons" @input="updateSubtotal(row, 'bya_kons','disc_kons','st_kons','jmlh_kons')" 
                          class="w-full border border-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" 
                          placeholder="0%" />
                      </td>
                      <td class="px-4 py-3">
                        <input v-model="row.st_kons" readonly 
                          class="w-full border border-gray-200 rounded-lg px-3 py-2 bg-gray-50 text-gray-700 font-semibold" />
                      </td>
                      <td class="px-4 py-3">
                        <input v-model="row.tanggal" type="date" 
                          class="w-full border border-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" />
                      </td>
                      <td class="px-4 py-3 text-center">
                        <button type="button" @click="removeKonsul(i)" 
                          class="px-3 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-all duration-200 shadow-sm hover:shadow-md">
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                          </svg>
                        </button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <!-- Tindakan Table -->
            <div class="bg-white shadow-xl rounded-2xl p-8 border border-gray-100">
              <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                  <div class="p-3 bg-green-100 rounded-xl">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                    </svg>
                  </div>
                  <h2 class="text-2xl font-bold text-gray-900">Detail Tindakan Medis</h2>
                </div>
                <button type="button" @click="addTindak()" 
                  class="px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl hover:from-green-700 hover:to-emerald-700 transition-all duration-200 font-semibold shadow-lg hover:shadow-xl flex items-center gap-2">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                  </svg>
                  Tambah Tindakan
                </button>
              </div>
              <div class="overflow-x-auto">
                <table class="w-full text-sm border border-gray-200">
                  <thead class="bg-green-50">
                    <tr>
                      <th class="p-2 border font-medium">Dokter</th>
                      <th class="p-2 border font-medium">Deskripsi Tindakan</th>
                      <th class="p-2 border font-medium">Jumlah</th>
                      <th class="p-2 border font-medium">Biaya</th>
                      <th class="p-2 border font-medium">Diskon</th>
                      <th class="p-2 border font-medium">Subtotal</th>
                      <th class="p-2 border font-medium">Tanggal</th>
                      <th class="p-2 border font-medium">Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(row, i) in tindaks" :key="i" class="hover:bg-gray-50">
                      <td class="p-2 border"><input v-model="row.dktr_tindak" class="w-full border rounded px-2 py-1" placeholder="Nama dokter" /></td>
                      <td class="p-2 border"><input v-model="row.dskp_tindak" class="w-full border rounded px-2 py-1" placeholder="Deskripsi tindakan" /></td>
                      <td class="p-2 border"><input v-model="row.jmlh_tindak" type="number" min="1" class="w-full border rounded px-2 py-1" /></td>
                      <td class="p-2 border"><input v-model="row.bya_tindak" @input="updateSubtotal(row, 'bya_tindak','disc_tindak','st_tindak','jmlh_tindak')" type="number" min="0" class="w-full border rounded px-2 py-1" placeholder="0" /></td>
                      <td class="p-2 border"><input v-model="row.disc_tindak" @input="updateSubtotal(row, 'bya_tindak','disc_tindak','st_tindak','jmlh_tindak')" class="w-full border rounded px-2 py-1" placeholder="0%" /></td>
                      <td class="p-2 border"><input v-model="row.st_tindak" readonly class="w-full border rounded px-2 py-1 bg-gray-50" /></td>
                      <td class="p-2 border"><input v-model="row.tanggal" type="date" class="w-full border rounded px-2 py-1" /></td>
                      <td class="p-2 border text-center">
                        <button type="button" @click="removeTindak(i)" class="px-2 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700">
                          <i class="fas fa-trash"></i>
                        </button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <!-- STEP 2: Resep & Alkes -->
          <div v-show="currentStep === 2" class="space-y-6">
            <!-- Alkes Table -->
            <div class="bg-white shadow rounded-lg p-6">
              <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold text-gray-900 flex items-center">
                  <i class="fas fa-tools mr-2 text-purple-600"></i>
                  Detail Alat Kesehatan
                </h2>
                <button type="button" @click="addAlkes()" class="px-3 py-2 text-sm bg-purple-600 text-white rounded hover:bg-purple-700">
                  <i class="fas fa-plus mr-1"></i>
                  Tambah Alkes
                </button>
              </div>
              <div class="overflow-x-auto">
                <table class="w-full text-sm border border-gray-200">
                  <thead class="bg-purple-50">
                    <tr>
                      <th class="p-2 border font-medium">Poli</th>
                      <th class="p-2 border font-medium">Deskripsi Alkes</th>
                      <th class="p-2 border font-medium">Jumlah</th>
                      <th class="p-2 border font-medium">Biaya</th>
                      <th class="p-2 border font-medium">Diskon</th>
                      <th class="p-2 border font-medium">Subtotal</th>
                      <th class="p-2 border font-medium">Tanggal</th>
                      <th class="p-2 border font-medium">Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(row, i) in alkesList" :key="i" class="hover:bg-gray-50">
                      <td class="p-2 border"><input v-model="row.poli" class="w-full border rounded px-2 py-1" placeholder="Nama poli" /></td>
                      <td class="p-2 border"><input v-model="row.dskp_alkes" class="w-full border rounded px-2 py-1" placeholder="Deskripsi alkes" /></td>
                      <td class="p-2 border"><input v-model="row.jmlh_alkes" type="number" min="1" class="w-full border rounded px-2 py-1" /></td>
                      <td class="p-2 border"><input v-model="row.bya_alkes" @input="updateSubtotal(row, 'bya_alkes','disc_alkes','st_alkes','jmlh_alkes')" type="number" min="0" class="w-full border rounded px-2 py-1" placeholder="0" /></td>
                      <td class="p-2 border"><input v-model="row.disc_alkes" @input="updateSubtotal(row, 'bya_alkes','disc_alkes','st_alkes','jmlh_alkes')" class="w-full border rounded px-2 py-1" placeholder="0%" /></td>
                      <td class="p-2 border"><input v-model="row.st_alkes" readonly class="w-full border rounded px-2 py-1 bg-gray-50" /></td>
                      <td class="p-2 border"><input v-model="row.tanggal" type="date" class="w-full border rounded px-2 py-1" /></td>
                      <td class="p-2 border text-center">
                        <button type="button" @click="removeAlkes(i)" class="px-2 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700">
                          <i class="fas fa-trash"></i>
                        </button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <!-- Resep (RSP) Table -->
            <div class="bg-white shadow rounded-lg p-6">
              <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold text-gray-900 flex items-center">
                  <i class="fas fa-pills mr-2 text-yellow-600"></i>
                  Detail Resep Obat
                </h2>
                <button type="button" @click="addRsp()" class="px-3 py-2 text-sm bg-yellow-600 text-white rounded hover:bg-yellow-700">
                  <i class="fas fa-plus mr-1"></i>
                  Tambah Resep
                </button>
              </div>
              <div class="overflow-x-auto">
                <table class="w-full text-sm border border-gray-200">
                  <thead class="bg-yellow-50">
                    <tr>
                      <th class="p-2 border font-medium">Deskripsi Obat</th>
                      <th class="p-2 border font-medium">Jumlah</th>
                      <th class="p-2 border font-medium">Biaya</th>
                      <th class="p-2 border font-medium">Diskon</th>
                      <th class="p-2 border font-medium">Subtotal</th>
                      <th class="p-2 border font-medium">Tanggal</th>
                      <th class="p-2 border font-medium">Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(row, i) in rspList" :key="i" class="hover:bg-gray-50">
                      <td class="p-2 border"><input v-model="row.dskp_rsp" class="w-full border rounded px-2 py-1" placeholder="Nama obat" /></td>
                      <td class="p-2 border"><input v-model="row.jmlh_rsp" type="number" min="1" class="w-full border rounded px-2 py-1" /></td>
                      <td class="p-2 border"><input v-model="row.bya_rsp" @input="updateSubtotal(row, 'bya_rsp','disc_rsp','st_rsp','jmlh_rsp')" type="number" min="0" class="w-full border rounded px-2 py-1" placeholder="0" /></td>
                      <td class="p-2 border"><input v-model="row.disc_rsp" @input="updateSubtotal(row, 'bya_rsp','disc_rsp','st_rsp','jmlh_rsp')" class="w-full border rounded px-2 py-1" placeholder="0%" /></td>
                      <td class="p-2 border"><input v-model="row.st_rsp" readonly class="w-full border rounded px-2 py-1 bg-gray-50" /></td>
                      <td class="p-2 border"><input v-model="row.tanggal" type="date" class="w-full border rounded px-2 py-1" /></td>
                      <td class="p-2 border text-center">
                        <button type="button" @click="removeRsp(i)" class="px-2 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700">
                          <i class="fas fa-trash"></i>
                        </button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <!-- STEP 3: Layanan Lainnya -->
          <div v-show="currentStep === 3" class="space-y-6">
            <div class="bg-white shadow rounded-lg p-6">
              <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold text-gray-900 flex items-center">
                  <i class="fas fa-list mr-2 text-red-600"></i>
                  Detail Layanan Lainnya
                </h2>
                <button type="button" @click="addLainnya()" class="px-3 py-2 text-sm bg-red-600 text-white rounded hover:bg-red-700">
                  <i class="fas fa-plus mr-1"></i>
                  Tambah Layanan
                </button>
              </div>
              <div class="overflow-x-auto">
                <table class="w-full text-sm border border-gray-200">
                  <thead class="bg-red-50">
                    <tr>
                      <th class="p-2 border font-medium">Deskripsi Layanan</th>
                      <th class="p-2 border font-medium">Jumlah</th>
                      <th class="p-2 border font-medium">Biaya</th>
                      <th class="p-2 border font-medium">Diskon</th>
                      <th class="p-2 border font-medium">Subtotal</th>
                      <th class="p-2 border font-medium">Tanggal</th>
                      <th class="p-2 border font-medium">Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(row, i) in lainnyas" :key="i" class="hover:bg-gray-50">
                      <td class="p-2 border"><input v-model="row.dskp_lainnya" class="w-full border rounded px-2 py-1" placeholder="Deskripsi layanan" /></td>
                      <td class="p-2 border"><input v-model="row.jmlh_lainnaya" type="number" min="1" class="w-full border rounded px-2 py-1" /></td>
                      <td class="p-2 border"><input v-model="row.bya_lainnya" @input="updateSubtotal(row, 'bya_lainnya','disc_lainnya','st_lainnya','jmlh_lainnaya')" type="number" min="0" class="w-full border rounded px-2 py-1" placeholder="0" /></td>
                      <td class="p-2 border"><input v-model="row.disc_lainnya" @input="updateSubtotal(row, 'bya_lainnya','disc_lainnya','st_lainnya','jmlh_lainnaya')" class="w-full border rounded px-2 py-1" placeholder="0%" /></td>
                      <td class="p-2 border"><input v-model="row.st_lainnya" readonly class="w-full border rounded px-2 py-1 bg-gray-50" /></td>
                      <td class="p-2 border"><input v-model="row.tanggal" type="date" class="w-full border rounded px-2 py-1" /></td>
                      <td class="p-2 border text-center">
                        <button type="button" @click="removeLainnya(i)" class="px-2 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700">
                          <i class="fas fa-trash"></i>
                        </button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <!-- STEP 4: Ringkasan & Simpan -->
          <div v-show="currentStep === 4" class="space-y-8">
            <div class="bg-white shadow-xl rounded-2xl p-8 border border-gray-100">
              <div class="flex items-center gap-3 mb-8">
                <div class="p-3 bg-green-100 rounded-xl">
                  <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                  </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-900">Ringkasan Detail Transaksi</h2>
              </div>
              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl p-6 border border-blue-100 shadow-lg">
                  <div class="flex items-center gap-3 mb-4">
                    <div class="p-2 bg-blue-500 rounded-lg">
                      <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                      </svg>
                    </div>
                    <h3 class="text-lg font-bold text-blue-800">Konsultasi Medis</h3>
                  </div>
                  <p class="text-sm text-blue-600 mb-1">Total item: <span class="font-semibold">{{ konsuls.length }}</span></p>
                  <p class="text-lg font-bold text-blue-800">Rp {{ formatNumber(sumCurrency(konsuls.map(r => toNumber(r.jmlh_kons) * toNumber(r.bya_kons)))) }}</p>
                </div>
                <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-2xl p-6 border border-green-100 shadow-lg">
                  <div class="flex items-center gap-3 mb-4">
                    <div class="p-2 bg-green-500 rounded-lg">
                      <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                      </svg>
                    </div>
                    <h3 class="text-lg font-bold text-green-800">Tindakan Medis</h3>
                  </div>
                  <p class="text-sm text-green-600 mb-1">Total item: <span class="font-semibold">{{ tindaks.length }}</span></p>
                  <p class="text-lg font-bold text-green-800">Rp {{ formatNumber(sumCurrency(tindaks.map(r => toNumber(r.jmlh_tindak) * toNumber(r.bya_tindak)))) }}</p>
                </div>
                <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-2xl p-6 border border-purple-100 shadow-lg">
                  <div class="flex items-center gap-3 mb-4">
                    <div class="p-2 bg-purple-500 rounded-lg">
                      <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                      </svg>
                    </div>
                    <h3 class="text-lg font-bold text-purple-800">Alat Kesehatan</h3>
                  </div>
                  <p class="text-sm text-purple-600 mb-1">Total item: <span class="font-semibold">{{ alkesList.length }}</span></p>
                  <p class="text-lg font-bold text-purple-800">Rp {{ formatNumber(sumCurrency(alkesList.map(r => toNumber(r.jmlh_alkes) * toNumber(r.bya_alkes)))) }}</p>
                </div>
                <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-2xl p-6 border border-yellow-100 shadow-lg">
                  <div class="flex items-center gap-3 mb-4">
                    <div class="p-2 bg-yellow-500 rounded-lg">
                      <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                      </svg>
                    </div>
                    <h3 class="text-lg font-bold text-yellow-800">Resep Obat</h3>
                  </div>
                  <p class="text-sm text-yellow-600 mb-1">Total item: <span class="font-semibold">{{ rspList.length }}</span></p>
                  <p class="text-lg font-bold text-yellow-800">Rp {{ formatNumber(sumCurrency(rspList.map(r => toNumber(r.jmlh_rsp) * toNumber(r.bya_rsp)))) }}</p>
                </div>
                <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-2xl p-6 border border-red-100 shadow-lg">
                  <div class="flex items-center gap-3 mb-4">
                    <div class="p-2 bg-red-500 rounded-lg">
                      <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                      </svg>
                    </div>
                    <h3 class="text-lg font-bold text-red-800">Layanan Lainnya</h3>
                  </div>
                  <p class="text-sm text-red-600 mb-1">Total item: <span class="font-semibold">{{ lainnyas.length }}</span></p>
                  <p class="text-lg font-bold text-red-800">Rp {{ formatNumber(sumCurrency(lainnyas.map(r => toNumber(r.jmlh_lainnaya) * toNumber(r.bya_lainnya)))) }}</p>
                </div>
                <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-2xl p-6 border border-gray-200 shadow-lg md:col-span-2 lg:col-span-3">
                  <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                      <div class="p-3 bg-gray-600 rounded-xl">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                      </div>
                      <div>
                        <h3 class="text-xl font-bold text-gray-800">Grand Total</h3>
                        <p class="text-sm text-gray-600">Total semua layanan medis</p>
                      </div>
                    </div>
                    <div class="text-right">
                      <p class="text-3xl font-extrabold text-gray-900">Rp {{ formatNumber(grandTotal) }}</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Actions -->
            <div class="flex flex-col sm:flex-row justify-end gap-4">
              <button type="button" @click="router.visit('/pasien')" 
                class="px-8 py-4 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-all duration-200 font-semibold flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                Batal
              </button>
              <button type="submit" :disabled="isSubmitting" 
                class="px-8 py-4 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 shadow-lg hover:shadow-xl transition-all duration-200 font-semibold flex items-center justify-center gap-2">
                <svg v-if="!isSubmitting" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3-3m0 0l-3 3m3-3v12"></path>
                </svg>
                <svg v-else class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                {{ isSubmitting ? 'Menyimpan...' : (existingPasien ? 'Tambah Transaksi ke Kunjungan' : 'Simpan Detail Transaksi') }}
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { Head, router, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
  psn: Object,
  existingPasien: Object
})

const isSubmitting = ref(false)
const currentStep = ref(1)

// Informasi Pasien: sinkronisasi props.psn ke form jika berubah
const form = useForm({
  psn_id: props.psn?.id || null,
  no_reg: props.existingPasien?.no_reg || '',
  tgl_reg: props.existingPasien?.tgl_reg || new Date().toISOString().split('T')[0],
  nm_p: props.existingPasien?.nm_p || props.psn?.nm_p || '',
  mrn: props.existingPasien?.mrn || '',
  almt_B: props.existingPasien?.almt_B || props.psn?.almt_B || '',
  no_inv: props.existingPasien?.no_inv || '',
  tgl_inv: props.existingPasien?.tgl_inv || '',
  perawatan: props.existingPasien?.perawatan || 'Rawat Jalan',
  penjamin: props.existingPasien?.penjamin || 'Umum',
  no_sjp: props.existingPasien?.no_sjp || '',
  icd: props.existingPasien?.icd || '',
  kunjungan: props.existingPasien?.kunjungan || 'umum',

  konsul: [],
  tindak: [],
  alkes: [],
  rsp: [],
  lainnya: []
})

// Sinkronisasi data pasien jika props.psn atau props.existingPasien berubah
watch(
  [() => props.psn, () => props.existingPasien],
  ([newPsn, newExistingPasien]) => {
    if (newPsn) {
      form.psn_id = newPsn.id || null
      
      // Use existing pasien data if available, otherwise use psn data
      if (newExistingPasien) {
        form.no_reg = newExistingPasien.no_reg || ''
        form.tgl_reg = newExistingPasien.tgl_reg || new Date().toISOString().split('T')[0]
        form.nm_p = newExistingPasien.nm_p || newPsn.nm_p || ''
        form.mrn = newExistingPasien.mrn || ''
        form.almt_B = newExistingPasien.almt_B || newPsn.almt_B || ''
        form.no_inv = newExistingPasien.no_inv || ''
        form.tgl_inv = newExistingPasien.tgl_inv || ''
        form.perawatan = newExistingPasien.perawatan || 'Rawat Jalan'
        form.penjamin = newExistingPasien.penjamin || 'Umum'
        form.no_sjp = newExistingPasien.no_sjp || ''
        form.icd = newExistingPasien.icd || ''
        form.kunjungan = newExistingPasien.kunjungan || 'umum'
      } else {
        form.nm_p = newPsn.nm_p || ''
        form.almt_B = newPsn.almt_B || ''
      }
    } else {
      form.psn_id = null
      form.nm_p = ''
      form.almt_B = ''
    }
  },
  { immediate: true }
)

const nextStep = () => { if (currentStep.value < 4) currentStep.value += 1 }
const prevStep = () => { if (currentStep.value > 1) currentStep.value -= 1 }

// Helpers
const toNumber = (val) => {
  if (!val) return 0
  const m = String(val).replace(/[^0-9,.]/g, '')
  return Number(m || 0)
}

const formatNumber = (num) => {
  return new Intl.NumberFormat('id-ID').format(num || 0)
}

const sumCurrency = (arr) => arr.reduce((s, v) => s + toNumber(v), 0)

/**
 * Update subtotal: (biaya * jumlah) - diskon
 */
function updateSubtotal(row, biayaKey, discKey, subtotalKey, jumlahKey) {
  const biaya = toNumber(row[biayaKey])
  const jumlah = toNumber(row[jumlahKey])
  const total = biaya * (jumlah || 1)
  const discValue = row[discKey]
  // Jika discount tidak ada atau null, subtotal = jumlah * biaya
  if (discValue === undefined || discValue === null || discValue === '' || discValue === '0%' || discValue === 0) {
    row[subtotalKey] = total
  } else {
    const discPercent = Number(String(discValue).replace(/[^0-9]/g, ''))
    const potongan = Math.floor((total * (discPercent || 0)) / 100)
    const subtotal = Math.max(total - potongan, 0)
    row[subtotalKey] = subtotal
  }
}

// Default row generator
const getDefaultRow = (keys) => {
  const defaultData = {}
  keys.forEach(key => {
    if (key.includes('disc')) {
      defaultData[key] = '0%'
    } else if (key.includes('st_')) {
      defaultData[key] = 0
    } else if (key.includes('jmlh_')) {
      defaultData[key] = 1
    } else {
      defaultData[key] = ''
    }
  })
  defaultData.tanggal = new Date().toISOString().split('T')[0]
  return defaultData
}

const konsulKeys = ['dokter', 'dskp_kons', 'jmlh_kons', 'bya_kons', 'disc_kons', 'st_kons', 'tanggal']
const tindakKeys = ['dktr_tindak', 'dskp_tindak', 'jmlh_tindak', 'bya_tindak', 'disc_tindak', 'st_tindak', 'tanggal']
const alkesKeys = ['poli', 'dskp_alkes', 'jmlh_alkes', 'bya_alkes', 'disc_alkes', 'st_alkes', 'tanggal']
const rspKeys = ['dskp_rsp', 'jmlh_rsp', 'bya_rsp', 'disc_rsp', 'st_rsp', 'tanggal']
const lainnyaKeys = ['dskp_lainnya', 'jmlh_lainnaya', 'bya_lainnya', 'disc_lainnya', 'st_lainnya', 'tanggal']

const konsuls = ref([getDefaultRow(konsulKeys)])
const tindaks = ref([getDefaultRow(tindakKeys)])
const alkesList = ref([getDefaultRow(alkesKeys)])
const rspList = ref([getDefaultRow(rspKeys)])
const lainnyas = ref([getDefaultRow(lainnyaKeys)])

function addKonsul() { konsuls.value.push(getDefaultRow(konsulKeys)) }
function removeKonsul(i) { if (konsuls.value.length > 1) { konsuls.value.splice(i, 1) } }
function addTindak() { tindaks.value.push(getDefaultRow(tindakKeys)) }
function removeTindak(i) { if (tindaks.value.length > 1) { tindaks.value.splice(i, 1) } }
function addAlkes() { alkesList.value.push(getDefaultRow(alkesKeys)) }
function removeAlkes(i) { if (alkesList.value.length > 1) { alkesList.value.splice(i, 1) } }
function addRsp() { rspList.value.push(getDefaultRow(rspKeys)) }
function removeRsp(i) { if (rspList.value.length > 1) { rspList.value.splice(i, 1) } }
function addLainnya() { lainnyas.value.push(getDefaultRow(lainnyaKeys)) }
function removeLainnya(i) { if (lainnyas.value.length > 1) { lainnyas.value.splice(i, 1) } }

// Grand total: sum semua subtotal (sudah dikali jumlah dan diskon)
const grandTotal = computed(() => {
  // Helper: parse number safely
  const safeNumber = (val) => {
    if (typeof val === 'number') return val
    if (!val) return 0
    // Remove non-numeric except dot and minus
    return Number(String(val).replace(/[^0-9.-]/g, '')) || 0
  }

  // Only sum rows with valid biaya and jumlah
  const sumGroup = (rows, jumlahKey, biayaKey, discKey) => {
    return (rows.value || []).reduce((total, row) => {
      const jumlah = safeNumber(row[jumlahKey])
      const biaya = safeNumber(row[biayaKey])
      const discRaw = row[discKey] || '0'
      // Accept both "10%" and "10" as 10 percent
      const discPercent = safeNumber(discRaw)
      if (!biaya || !jumlah) return total // skip empty rows
      const subtotal = biaya * jumlah
      const potongan = Math.floor((subtotal * discPercent) / 100)
      return total + Math.max(subtotal - potongan, 0)
    }, 0)
  }

  let total = 0
  total += sumGroup(konsuls, 'jmlh_kons', 'bya_kons', 'disc_kons')
  total += sumGroup(tindaks, 'jmlh_tindak', 'bya_tindak', 'disc_tindak')
  total += sumGroup(alkesList, 'jmlh_alkes', 'bya_alkes', 'disc_alkes')
  total += sumGroup(rspList, 'jmlh_rsp', 'bya_rsp', 'disc_rsp')
  total += sumGroup(lainnyas, 'jmlh_lainnaya', 'bya_lainnya', 'disc_lainnya')
  return total
})

const submitForm = () => {
  isSubmitting.value = true

  // Generate automatic MRN if empty
  if (!form.mrn) {
    const today = new Date()
    const year = today.getFullYear()
    const month = String(today.getMonth() + 1).padStart(2, '0')
    const day = String(today.getDate()).padStart(2, '0')
    const random = Math.floor(Math.random() * 1000).toString().padStart(3, '0')
    form.mrn = `MRN${year}${month}${day}${random}`
  }

  // Generate automatic No Registrasi if empty
  if (!form.no_reg) {
    const today = new Date()
    const year = today.getFullYear()
    const month = String(today.getMonth() + 1).padStart(2, '0')
    const day = String(today.getDate()).padStart(2, '0')
    const random = Math.floor(Math.random() * 100).toString().padStart(2, '0')
    form.no_reg = `REG${year}${month}${day}${random}`
  }

  // Only send plain JS objects, not refs, to Inertia
  const filterAndClean = (list, key) => {
    // Defensive: if list is a ref, get .value
    const arr = Array.isArray(list) ? list : (list?.value || [])
    // Fix: filter out rows where ALL fields except 'tanggal' are empty or default
    return arr.filter(row => {
      // Exclude 'tanggal' from check
      const keysToCheck = Object.keys(row).filter(k => k !== 'tanggal')
      // If at least one field (other than tanggal) is not empty string, not '0', not '0%', not '0 RP', not null/undefined
      return keysToCheck.some(k => {
        const v = (row[k] ?? '').toString().trim()
        return v !== '' && v !== '0' && v !== '0%' && v !== '0 RP'
      })
    }).map(row => {
      const cleanedRow = { ...row }
      const biayaKey = Object.keys(row).find(k => k.startsWith('bya_'))
      const subtotalKey = Object.keys(row).find(k => k.startsWith('st_'))
      const jumlahKey = Object.keys(row).find(k => k.startsWith('jmlh_'))

      // Pastikan biaya, jumlah, subtotal dikonversi ke number
      if (biayaKey) cleanedRow[biayaKey] = toNumber(row[biayaKey])
      if (jumlahKey) cleanedRow[jumlahKey] = toNumber(row[jumlahKey])
      if (subtotalKey) cleanedRow[subtotalKey] = toNumber(row[subtotalKey])

      Object.keys(cleanedRow).forEach(k => {
        if (typeof cleanedRow[k] === 'string') {
           cleanedRow[k] = cleanedRow[k].replace(/ RP| %/g, '').trim()
        }
      })

      return cleanedRow
    })
  }

  // Always send plain arrays, not refs, to Inertia
  form.konsul = filterAndClean(konsuls, 'dskp_kons')
  form.tindak = filterAndClean(tindaks, 'dskp_tindak')
  form.alkes = filterAndClean(alkesList, 'dskp_alkes')
  form.rsp = filterAndClean(rspList, 'dskp_rsp')
  form.lainnya = filterAndClean(lainnyas, 'dskp_lainnya')

  if (form.konsul.length === 0) delete form.konsul
  if (form.tindak.length === 0) delete form.tindak
  if (form.alkes.length === 0) delete form.alkes
  if (form.rsp.length === 0) delete form.rsp
  if (form.lainnya.length === 0) delete form.lainnya

  // Remove any Vue refs from form before sending
  const plainForm = {}
  Object.keys(form).forEach(key => {
    if (typeof form[key]?.value !== 'undefined') {
      plainForm[key] = form[key].value
    } else {
      plainForm[key] = form[key]
    }
  })

  // Use form.post, but pass plainForm as data
  form.post('/pasien/kunjungan-with-transaksi', {
    data: plainForm,
    onFinish: () => {
      isSubmitting.value = false
    },
    onError: (errors) => {
       console.error("Validation Errors:", errors);
       if (errors.konsul || errors.tindak) currentStep.value = 1;
       else if (errors.alkes || errors.rsp) currentStep.value = 2;
       else if (errors.lainnya) currentStep.value = 3;
    },
    onSuccess: () => {
      router.visit('/pasien')
    }
  })
}

defineExpose({ sumCurrency, grandTotal, updateSubtotal, toNumber })
</script>
