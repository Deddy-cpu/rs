<template>
  <AuthenticatedLayout>
    <Head title="Form Transaksi" />
    
    <div class="min-h-screen bg-gray-50 py-8">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
          <h1 class="text-3xl font-bold text-gray-900">
            {{ existingPasien ? 'Tambah Transaksi ke Kunjungan' : 'Detail Transaksi Pasien' }}
          </h1>
          <p class="mt-2 text-gray-600">
            {{ existingPasien ? 'Menambahkan transaksi medis ke kunjungan yang sudah ada' : 'Kelola detail transaksi medis pasien dengan lengkap' }}
          </p>
          <div v-if="existingPasien" class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
            <p class="text-sm text-blue-800">
              <i class="fas fa-info-circle mr-1"></i>
              <strong>Kunjungan sudah ada:</strong> No Reg {{ existingPasien.no_reg }} - {{ existingPasien.tgl_reg }}
            </p>
          </div>
        </div>

        <!-- Stepper -->
        <div class="bg-white shadow rounded-lg p-4 mb-6">
          <div class="flex items-center justify-between">
            <div class="flex-1 flex items-center gap-2">
              <div :class="['px-3 py-1 rounded-full text-sm font-semibold', currentStep === 1 ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700']">1. Konsultasi & Tindakan</div>
              <div class="w-8 h-px bg-gray-300"></div>
              <div :class="['px-3 py-1 rounded-full text-sm font-semibold', currentStep === 2 ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700']">2. Resep & Alkes</div>
              <div class="w-8 h-px bg-gray-300"></div>
              <div :class="['px-3 py-1 rounded-full text-sm font-semibold', currentStep === 3 ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700']">3. Layanan Lainnya</div>
              <div class="w-8 h-px bg-gray-300"></div>
              <div :class="['px-3 py-1 rounded-full text-sm font-semibold', currentStep === 4 ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700']">4. Ringkasan & Simpan</div>
            </div>
            <div class="flex items-center gap-2">
              <button @click="prevStep" :disabled="currentStep === 1" class="px-3 py-2 text-sm border rounded disabled:opacity-50">Sebelumnya</button>
              <button v-if="currentStep < 4" @click="nextStep" class="px-3 py-2 text-sm bg-blue-600 text-white rounded">Berikutnya</button>
            </div>
          </div>
        </div>

        <form @submit.prevent="submitForm" class="space-y-8">
          <!-- Informasi Pasien (selalu tampil di atas) -->
          <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
              <i class="fas fa-user mr-2 text-blue-600"></i>
              Informasi Pasien
            </h2>
            <input v-if="form.psn_id" type="hidden" v-model="form.psn_id" />
            <div v-if="form.psn_id" class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <p class="text-sm text-gray-500">Nama Pasien</p>
                <p class="font-semibold">{{ form.nm_p }}</p>
              </div>
              <div>
                <p class="text-sm text-gray-500">NIK</p>
                <p class="font-semibold">{{ psn?.nik }}</p>
              </div>
              <div>
                <p class="text-sm text-gray-500">No BPJS</p>
                <p class="font-semibold">{{ psn?.no_bpjs }}</p>
              </div>
              <div>
                <p class="text-sm text-gray-500">Alamat</p>
                <p class="font-semibold">{{ form.almt_B }}</p>
              </div>
              <div v-if="existingPasien">
                <p class="text-sm text-gray-500">No Registrasi</p>
                <p class="font-semibold text-blue-600">{{ form.no_reg }}</p>
              </div>
              <div v-if="existingPasien">
                <p class="text-sm text-gray-500">Tanggal Kunjungan</p>
                <p class="font-semibold text-blue-600">{{ form.tgl_reg }}</p>
              </div>
              <div class="md:col-span-2">
                <button type="button" @click="router.visit('/pasien')" class="inline-block mt-2 px-3 py-2 text-sm border rounded hover:bg-gray-50">
                  <i class="fas fa-edit mr-1"></i>
                  Ganti Pasien
                </button>
              </div>
            </div>
            <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Pasien</label>
                <input v-model="form.nm_p" type="text" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Masukkan nama pasien" />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Alamat</label>
                <input v-model="form.almt_B" type="text" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Masukkan alamat" />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Perawatan</label>
                <select v-model="form.perawatan" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                  <option value="">Pilih jenis perawatan</option>
                  <option value="Rawat Jalan">Rawat Jalan</option>
                  <option value="Rawat Inap">Rawat Inap</option>
                  <option value="IGD">IGD (Instalasi Gawat Darurat)</option>
                  <option value="ICU">ICU (Intensive Care Unit)</option>
                  <option value="Operasi">Operasi</option>
                  <option value="Konsultasi">Konsultasi</option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Penjamin</label>
                <select v-model="form.penjamin" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                  <option value="">Pilih penjamin</option>
                  <option value="BPJS">BPJS Kesehatan</option>
                  <option value="Asuransi Swasta">Asuransi Swasta</option>
                  <option value="Umum">Umum (Pribadi)</option>
                  <option value="Perusahaan">Perusahaan</option>
                  <option value="Pemerintah">Pemerintah</option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Registrasi</label>
                <input v-model="form.tgl_reg" type="date" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" />
              </div>
            </div>
          </div>

          <!-- STEP 1: Konsultasi & Tindakan -->
          <div v-show="currentStep === 1" class="space-y-6">
            <!-- Konsultasi Table -->
            <div class="bg-white shadow rounded-lg p-6">
              <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold text-gray-900 flex items-center">
                  <i class="fas fa-stethoscope mr-2 text-blue-600"></i>
                  Detail Konsultasi Medis
                </h2>
                <button type="button" @click="addKonsul()" class="px-3 py-2 text-sm bg-blue-600 text-white rounded hover:bg-blue-700">
                  <i class="fas fa-plus mr-1"></i>
                  Tambah Konsultasi
                </button>
              </div>
              <div class="overflow-x-auto">
                <table class="w-full text-sm border border-gray-200">
                  <thead class="bg-blue-50">
                    <tr>
                      <th class="p-2 border font-medium">Dokter</th>
                      <th class="p-2 border font-medium">Deskripsi Konsultasi</th>
                      <th class="p-2 border font-medium">Jumlah</th>
                      <th class="p-2 border font-medium">Biaya</th>
                      <th class="p-2 border font-medium">Diskon</th>
                      <th class="p-2 border font-medium">Subtotal</th>
                      <th class="p-2 border font-medium">Tanggal</th>
                      <th class="p-2 border font-medium">Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(row, i) in form.konsul" :key="i" class="hover:bg-gray-50">
                      <td class="p-2 border"><input v-model="row.dokter" class="w-full border rounded px-2 py-1" placeholder="Nama dokter" /></td>
                      <td class="p-2 border"><input v-model="row.dskp_kons" class="w-full border rounded px-2 py-1" placeholder="Deskripsi konsultasi" /></td>
                      <td class="p-2 border"><input v-model="row.jmlh_kons" type="number" min="1" class="w-full border rounded px-2 py-1" /></td>
                      <td class="p-2 border"><input v-model="row.bya_kons" type="number" min="0" class="w-full border rounded px-2 py-1" placeholder="0" /></td>
                      <td class="p-2 border"><input v-model="row.disc_kons" class="w-full border rounded px-2 py-1" placeholder="0%" /></td>
                      <td class="p-2 border"><input v-model="row.st_kons" readonly class="w-full border rounded px-2 py-1 bg-gray-50" /></td>
                      <td class="p-2 border"><input v-model="row.tanggal" type="date" class="w-full border rounded px-2 py-1" /></td>
                      <td class="p-2 border text-center">
                        <button type="button" @click="removeKonsul(i)" class="px-2 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700">
                          <i class="fas fa-trash"></i>
                        </button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <!-- Tindakan Table -->
            <div class="bg-white shadow rounded-lg p-6">
              <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold text-gray-900 flex items-center">
                  <i class="fas fa-procedures mr-2 text-green-600"></i>
                  Detail Tindakan Medis
                </h2>
                <button type="button" @click="addTindak()" class="px-3 py-2 text-sm bg-green-600 text-white rounded hover:bg-green-700">
                  <i class="fas fa-plus mr-1"></i>
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
                    <tr v-for="(row, i) in form.tindak" :key="i" class="hover:bg-gray-50">
                      <td class="p-2 border"><input v-model="row.dktr_tindak" class="w-full border rounded px-2 py-1" placeholder="Nama dokter" /></td>
                      <td class="p-2 border"><input v-model="row.dskp_tindak" class="w-full border rounded px-2 py-1" placeholder="Deskripsi tindakan" /></td>
                      <td class="p-2 border"><input v-model="row.jmlh_tindak" type="number" min="1" class="w-full border rounded px-2 py-1" /></td>
                      <td class="p-2 border"><input v-model="row.bya_tindak" type="number" min="0" class="w-full border rounded px-2 py-1" placeholder="0" /></td>
                      <td class="p-2 border"><input v-model="row.disc_tindak" class="w-full border rounded px-2 py-1" placeholder="0%" /></td>
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
            <!-- Alkes Section -->
            <div class="bg-white shadow rounded-lg p-6">
              <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold text-gray-900 flex items-center">
                  <i class="fas fa-medkit mr-2 text-purple-600"></i>
                  Alat Kesehatan
                </h2>
                <button type="button" @click="addAlkes" class="px-3 py-2 text-sm bg-purple-600 text-white rounded hover:bg-purple-700">
                  <i class="fas fa-plus mr-1"></i>
                  Tambah Alkes
                </button>
              </div>
              <div v-for="(alkes, index) in form.alkes" :key="index" class="border border-gray-200 rounded-lg p-4 mb-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Poli</label>
                    <input 
                      type="text" 
                      v-model="alkes.poli"
                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                      placeholder="Nama Poli"
                    />
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Deskripsi</label>
                    <input 
                      type="text" 
                      v-model="alkes.dskp_alkes"
                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                      placeholder="Deskripsi Alkes"
                    />
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Jumlah</label>
                    <input 
                      type="number" 
                      v-model="alkes.jmlh_alkes"
                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                      min="1"
                    />
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Biaya</label>
                    <input 
                      type="number" 
                      v-model="alkes.bya_alkes"
                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                      min="0"
                      step="0.01"
                    />
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Diskon</label>
                    <input 
                      type="text" 
                      v-model="alkes.disc_alkes"
                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                      placeholder="0%"
                    />
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Tanggal</label>
                    <input 
                      type="date" 
                      v-model="alkes.tanggal"
                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                    />
                  </div>
                </div>
                <div class="mt-4 flex justify-end">
                  <button 
                    type="button" 
                    @click="removeAlkes(index)"
                    class="bg-red-500 text-white px-3 py-1 rounded-md hover:bg-red-600 text-sm"
                  >
                    Hapus
                  </button>
                </div>
              </div>
            </div>

            <!-- Resep Section -->
            <div class="bg-white shadow rounded-lg p-6">
              <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold text-gray-900 flex items-center">
                  <i class="fas fa-pills mr-2 text-yellow-600"></i>
                  Resep Obat
                </h2>
                <button type="button" @click="addRsp" class="px-3 py-2 text-sm bg-yellow-600 text-white rounded hover:bg-yellow-700">
                  <i class="fas fa-plus mr-1"></i>
                  Tambah Resep
                </button>
              </div>
              <div v-for="(rsp, index) in form.rsp" :key="index" class="border border-gray-200 rounded-lg p-4 mb-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Dokter</label>
                    <input 
                      type="text" 
                      v-model="rsp.dktr_rsp"
                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                      placeholder="Nama Dokter"
                    />
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Deskripsi</label>
                    <input 
                      type="text" 
                      v-model="rsp.dskp_rsp"
                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                      placeholder="Deskripsi Resep"
                    />
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Jumlah</label>
                    <input 
                      type="number" 
                      v-model="rsp.jmlh_rsp"
                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                      min="1"
                    />
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Biaya</label>
                    <input 
                      type="number" 
                      v-model="rsp.bya_rsp"
                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                      min="0"
                      step="0.01"
                    />
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Diskon</label>
                    <input 
                      type="text" 
                      v-model="rsp.disc_rsp"
                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                      placeholder="0%"
                    />
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Tanggal</label>
                    <input 
                      type="date" 
                      v-model="rsp.tanggal"
                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                    />
                  </div>
                </div>
                <div class="mt-4 flex justify-end">
                  <button 
                    type="button" 
                    @click="removeRsp(index)"
                    class="bg-red-500 text-white px-3 py-1 rounded-md hover:bg-red-600 text-sm"
                  >
                    Hapus
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- STEP 3: Layanan Lainnya -->
          <div v-show="currentStep === 3" class="space-y-6">
            <div class="bg-white shadow rounded-lg p-6">
              <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold text-gray-900 flex items-center">
                  <i class="fas fa-plus-circle mr-2 text-red-600"></i>
                  Layanan Lainnya
                </h2>
                <button type="button" @click="addLainnya" class="px-3 py-2 text-sm bg-red-600 text-white rounded hover:bg-red-700">
                  <i class="fas fa-plus mr-1"></i>
                  Tambah Lainnya
                </button>
              </div>
              <div v-for="(lainnya, index) in form.lainnya" :key="index" class="border border-gray-200 rounded-lg p-4 mb-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Dokter</label>
                    <input 
                      type="text" 
                      v-model="lainnya.dktr_lainnya"
                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                      placeholder="Nama Dokter"
                    />
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Deskripsi</label>
                    <input 
                      type="text" 
                      v-model="lainnya.dskp_lainnya"
                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                      placeholder="Deskripsi Lainnya"
                    />
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Jumlah</label>
                    <input 
                      type="number" 
                      v-model="lainnya.jmlh_lainnaya"
                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                      min="1"
                    />
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Biaya</label>
                    <input 
                      type="number" 
                      v-model="lainnya.bya_lainnya"
                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                      min="0"
                      step="0.01"
                    />
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Diskon</label>
                    <input 
                      type="text" 
                      v-model="lainnya.disc_lainnya"
                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                      placeholder="0%"
                    />
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Tanggal</label>
                    <input 
                      type="date" 
                      v-model="lainnya.tanggal"
                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                    />
                  </div>
                </div>
                <div class="mt-4 flex justify-end">
                  <button 
                    type="button" 
                    @click="removeLainnya(index)"
                    class="bg-red-500 text-white px-3 py-1 rounded-md hover:bg-red-600 text-sm"
                  >
                    Hapus
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- STEP 4: Ringkasan & Simpan -->
          <div v-show="currentStep === 4" class="space-y-6">
            <div class="bg-white shadow rounded-lg p-6">
              <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-calculator mr-2 text-green-600"></i>
                Ringkasan Detail Transaksi
              </h2>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="p-4 border rounded bg-blue-50">
                  <p class="font-semibold mb-2 text-blue-800">Konsultasi Medis</p>
                  <p class="text-sm text-blue-600">Total item: {{ konsuls.length }}</p>
                  <p class="text-sm text-blue-600">Total biaya: Rp {{ formatNumber(sumCurrency(konsuls.map(r => toNumber(r.jmlh_kons) * toNumber(r.bya_kons)))) }}</p>
                </div>
                <div class="p-4 border rounded bg-green-50">
                  <p class="font-semibold mb-2 text-green-800">Tindakan Medis</p>
                  <p class="text-sm text-green-600">Total item: {{ tindaks.length }}</p>
                  <p class="text-sm text-green-600">Total biaya: Rp {{ formatNumber(sumCurrency(tindaks.map(r => toNumber(r.jmlh_tindak) * toNumber(r.bya_tindak)))) }}</p>
                </div>
                <div class="p-4 border rounded bg-purple-50">
                  <p class="font-semibold mb-2 text-purple-800">Alat Kesehatan</p>
                  <p class="text-sm text-purple-600">Total item: {{ alkesList.length }}</p>
                  <p class="text-sm text-purple-600">Total biaya: Rp {{ formatNumber(sumCurrency(alkesList.map(r => toNumber(r.jmlh_alkes) * toNumber(r.bya_alkes)))) }}</p>
                </div>
                <div class="p-4 border rounded bg-yellow-50">
                  <p class="font-semibold mb-2 text-yellow-800">Resep Obat</p>
                  <p class="text-sm text-yellow-600">Total item: {{ rspList.length }}</p>
                  <p class="text-sm text-yellow-600">Total biaya: Rp {{ formatNumber(sumCurrency(rspList.map(r => toNumber(r.jmlh_rsp) * toNumber(r.bya_rsp)))) }}</p>
                </div>
                <div class="p-4 border rounded bg-red-50">
                  <p class="font-semibold mb-2 text-red-800">Layanan Lainnya</p>
                  <p class="text-sm text-red-600">Total item: {{ lainnyas.length }}</p>
                  <p class="text-sm text-red-600">Total biaya: Rp {{ formatNumber(sumCurrency(lainnyas.map(r => toNumber(r.jmlh_lainnaya) * toNumber(r.bya_lainnya)))) }}</p>
                </div>
                <div class="p-4 border rounded md:col-span-2 bg-gray-50">
                  <p class="text-lg font-bold text-gray-800">Grand Total: Rp {{ formatNumber(grandTotal) }}</p>
                </div>
              </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-end space-x-4">
              <button type="button" @click="router.visit('/pasien')" class="px-6 py-3 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500">
                <i class="fas fa-times mr-2"></i>
                Batal
              </button>
              <button type="submit" :disabled="isSubmitting" class="px-6 py-3 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50">
                <i class="fas fa-save mr-2"></i>
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
import { ref, computed, onMounted } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link } from '@inertiajs/vue3'

const props = defineProps({
  kunjungan: Object,
  kunjunganId: Number,
  psn: Object,
  isEdit: Boolean
})

// Get the kunjungan ID from either the prop or the kunjungan object
const kunjunganId = computed(() => {
  return props.kunjunganId || props.kunjungan?.id
})

// Stepper state
const currentStep = ref(1)
const existingPasien = computed(() => props.psn || props.kunjungan?.psn)

// Navigation methods
const nextStep = () => {
  if (currentStep.value < 4) {
    currentStep.value++
  }
}

const prevStep = () => {
  if (currentStep.value > 1) {
    currentStep.value--
  }
}

// Form submission state
const isSubmitting = ref(false)

const form = useForm({
  // Kunjungan data (required for update)
  psn_id: props.psn?.id || props.kunjungan?.psn_id,
  no_reg: props.kunjungan?.no_reg || '',
  tgl_reg: props.kunjungan?.tgl_reg || new Date().toISOString().split('T')[0],
  nm_p: props.kunjungan?.nm_p || '',
  mrn: props.kunjungan?.mrn || '',
  almt_B: props.kunjungan?.almt_B || '',
  no_inv: props.kunjungan?.no_inv || '',
  tgl_inv: props.kunjungan?.tgl_inv || '',
  perawatan: props.kunjungan?.perawatan || '',
  penjamin: props.kunjungan?.penjamin || '',
  no_sjp: props.kunjungan?.no_sjp || '',
  icd: props.kunjungan?.icd || '',
  kunjungan: props.kunjungan?.kunjungan || '',
  
  // Transaction data
  kunjungan_id: props.kunjunganId || props.kunjungan?.id,
  tanggal: new Date().toISOString().split('T')[0],
  status: 'pending',
  konsul: [],
  tindak: [],
  alkes: [],
  rsp: [],
  lainnya: []
})

// Initialize with empty items for each service type
onMounted(() => {
  if (props.isEdit && props.kunjungan) {
    // Populate form with existing data
    form.psn_id = props.psn?.id || props.kunjungan.psn_id
    form.no_reg = props.kunjungan.no_reg
    form.tgl_reg = formatDateForInput(props.kunjungan.tgl_reg)
    form.nm_p = props.kunjungan.nm_p
    form.mrn = props.kunjungan.mrn
    form.almt_B = props.kunjungan.almt_B
    form.no_inv = props.kunjungan.no_inv || ''
    form.tgl_inv = formatDateForInput(props.kunjungan.tgl_inv)
    form.perawatan = props.kunjungan.perawatan
    form.penjamin = props.kunjungan.penjamin
    form.no_sjp = props.kunjungan.no_sjp || ''
    form.icd = props.kunjungan.icd || ''
    form.kunjungan = props.kunjungan.kunjungan
    
    form.tanggal = formatDateForInput(props.kunjungan.tgl_reg)
    form.status = 'completed' // or get from transaction if available
    
    // Populate medical services from existing data
    if (props.kunjungan.konsuls && props.kunjungan.konsuls.length > 0) {
      form.konsul = props.kunjungan.konsuls.map(konsul => ({
        dokter: konsul.dokter || '',
        dskp_kons: konsul.dskp_kons || '',
        jmlh_kons: konsul.jmlh_kons || 1,
        bya_kons: konsul.bya_kons || 0,
        disc_kons: konsul.disc_kons || '0%',
        st_kons: konsul.st_kons || 0,
        tanggal: formatDateForInput(konsul.tanggal)
      }))
    } else {
      addKonsul()
    }
    
    if (props.kunjungan.tindaks && props.kunjungan.tindaks.length > 0) {
      form.tindak = props.kunjungan.tindaks.map(tindak => ({
        dktr_tindak: tindak.dktr_tindak || '',
        dskp_tindak: tindak.dskp_tindak || '',
        jmlh_tindak: tindak.jmlh_tindak || 1,
        bya_tindak: tindak.bya_tindak || 0,
        disc_tindak: tindak.disc_tindak || '0%',
        st_tindak: tindak.st_tindak || 0,
        tanggal: formatDateForInput(tindak.tanggal)
      }))
    }
    
    if (props.kunjungan.alkes && props.kunjungan.alkes.length > 0) {
      form.alkes = props.kunjungan.alkes.map(alkes => ({
        poli: alkes.poli || '',
        dskp_alkes: alkes.dskp_alkes || '',
        jmlh_alkes: alkes.jmlh_alkes || 1,
        bya_alkes: alkes.bya_alkes || 0,
        disc_alkes: alkes.disc_alkes || '0%',
        st_alkes: alkes.st_alkes || 0,
        tanggal: formatDateForInput(alkes.tanggal)
      }))
    }
    
    if (props.kunjungan.rsp && props.kunjungan.rsp.length > 0) {
      form.rsp = props.kunjungan.rsp.map(rsp => ({
        dktr_rsp: rsp.dktr_rsp || '',
        dskp_rsp: rsp.dskp_rsp || '',
        jmlh_rsp: rsp.jmlh_rsp || 1,
        bya_rsp: rsp.bya_rsp || 0,
        disc_rsp: rsp.disc_rsp || '0%',
        st_rsp: rsp.st_rsp || 0,
        tanggal: formatDateForInput(rsp.tanggal)
      }))
    }
    
    if (props.kunjungan.lainnyas && props.kunjungan.lainnyas.length > 0) {
      form.lainnya = props.kunjungan.lainnyas.map(lainnya => ({
        dktr_lainnya: lainnya.dktr_lainnya || '',
        dskp_lainnya: lainnya.dskp_lainnya || '',
        jmlh_lainnaya: lainnya.jmlh_lainnaya || 1,
        bya_lainnya: lainnya.bya_lainnya || 0,
        disc_lainnya: lainnya.disc_lainnya || '0%',
        st_lainnya: lainnya.st_lainnya || 0,
        tanggal: formatDateForInput(lainnya.tanggal)
      }))
    }
  } else {
    // Create mode - start with empty konsul
    addKonsul()
  }
})

// Konsultasi methods
const addKonsul = () => {
  form.konsul.push({
    dokter: '',
    dskp_kons: '',
    jmlh_kons: 1,
    bya_kons: 0,
    disc_kons: '0%',
    st_kons: 0,
    tanggal: new Date().toISOString().split('T')[0]
  })
}

const removeKonsul = (index) => {
  form.konsul.splice(index, 1)
}

// Tindakan methods
const addTindak = () => {
  form.tindak.push({
    dktr_tindak: '',
    dskp_tindak: '',
    jmlh_tindak: 1,
    bya_tindak: 0,
    disc_tindak: '0%',
    st_tindak: 0,
    tanggal: new Date().toISOString().split('T')[0]
  })
}

const removeTindak = (index) => {
  form.tindak.splice(index, 1)
}

// Alkes methods
const addAlkes = () => {
  form.alkes.push({
    poli: '',
    dskp_alkes: '',
    jmlh_alkes: 1,
    bya_alkes: 0,
    disc_alkes: '0%',
    st_alkes: 0,
    tanggal: new Date().toISOString().split('T')[0]
  })
}

const removeAlkes = (index) => {
  form.alkes.splice(index, 1)
}

// Resep methods
const addRsp = () => {
  form.rsp.push({
    dktr_rsp: '',
    dskp_rsp: '',
    jmlh_rsp: 1,
    bya_rsp: 0,
    disc_rsp: '0%',
    st_rsp: 0,
    tanggal: new Date().toISOString().split('T')[0]
  })
}

const removeRsp = (index) => {
  form.rsp.splice(index, 1)
}

// Lainnya methods
const addLainnya = () => {
  form.lainnya.push({
    dktr_lainnya: '',
    dskp_lainnya: '',
    jmlh_lainnaya: 1,
    bya_lainnya: 0,
    disc_lainnya: '0%',
    st_lainnya: 0,
    tanggal: new Date().toISOString().split('T')[0]
  })
}

const removeLainnya = (index) => {
  form.lainnya.splice(index, 1)
}

// Calculate sub totals for each category
const calculateKonsulTotal = () => {
  let total = 0
  form.konsul.forEach(item => {
    total += (item.jmlh_kons || 0) * (item.bya_kons || 0)
  })
  return total
}

const calculateTindakTotal = () => {
  let total = 0
  form.tindak.forEach(item => {
    total += (item.jmlh_tindak || 0) * (item.bya_tindak || 0)
  })
  return total
}

const calculateAlkesTotal = () => {
  let total = 0
  form.alkes.forEach(item => {
    total += (item.jmlh_alkes || 0) * (item.bya_alkes || 0)
  })
  return total
}

const calculateRspTotal = () => {
  let total = 0
  form.rsp.forEach(item => {
    total += (item.jmlh_rsp || 0) * (item.bya_rsp || 0)
  })
  return total
}

const calculateLainnyaTotal = () => {
  let total = 0
  form.lainnya.forEach(item => {
    total += (item.jmlh_lainnaya || 0) * (item.bya_lainnya || 0)
  })
  return total
}

// Calculate total cost
const calculateTotal = () => {
  return calculateKonsulTotal() + 
         calculateTindakTotal() + 
         calculateAlkesTotal() + 
         calculateRspTotal() + 
         calculateLainnyaTotal()
}

// Summary statistics functions
const getTotalItems = () => {
  return form.konsul.length + 
         form.tindak.length + 
         form.alkes.length + 
         form.rsp.length + 
         form.lainnya.length
}

const getActiveCategories = () => {
  let count = 0
  if (form.konsul.length > 0) count++
  if (form.tindak.length > 0) count++
  if (form.alkes.length > 0) count++
  if (form.rsp.length > 0) count++
  if (form.lainnya.length > 0) count++
  return count
}

const getAveragePerItem = () => {
  const totalItems = getTotalItems()
  if (totalItems === 0) return 'Rp 0'
  const average = calculateTotal() / totalItems
  return 'Rp ' + formatCurrency(Math.round(average))
}

const getHighestCategory = () => {
  const totals = [
    { name: 'Konsultasi', total: calculateKonsulTotal() },
    { name: 'Tindakan', total: calculateTindakTotal() },
    { name: 'Alkes', total: calculateAlkesTotal() },
    { name: 'Resep', total: calculateRspTotal() },
    { name: 'Lainnya', total: calculateLainnyaTotal() }
  ]
  
  const highest = totals.reduce((max, current) => 
    current.total > max.total ? current : max
  )
  
  return highest.total > 0 ? highest.name : '-'
}

// Format currency
const formatCurrency = (amount) => {
  return new Intl.NumberFormat('id-ID').format(amount)
}

// Format date
const formatDate = (date) => {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('id-ID')
}

// Format date for HTML input
const formatDateForInput = (dateString) => {
  if (!dateString) return new Date().toISOString().split('T')[0]
  const date = new Date(dateString)
  return date.toISOString().split('T')[0]
}

// Terbilang function (simplified)
const terbilang = (angka) => {
  const bilangan = ['', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan']
  const belasan = ['sepuluh', 'sebelas', 'dua belas', 'tiga belas', 'empat belas', 'lima belas', 'enam belas', 'tujuh belas', 'delapan belas', 'sembilan belas']
  const puluhan = ['', '', 'dua puluh', 'tiga puluh', 'empat puluh', 'lima puluh', 'enam puluh', 'tujuh puluh', 'delapan puluh', 'sembilan puluh']
  
  if (angka === 0) return 'nol'
  if (angka < 10) return bilangan[angka]
  if (angka < 20) return belasan[angka - 10]
  if (angka < 100) return puluhan[Math.floor(angka / 10)] + (angka % 10 !== 0 ? ' ' + bilangan[angka % 10] : '')
  
  // Simplified for larger numbers
  if (angka < 1000) {
    const ratus = Math.floor(angka / 100)
    const sisa = angka % 100
    return (ratus === 1 ? 'seratus' : bilangan[ratus] + ' ratus') + (sisa !== 0 ? ' ' + terbilang(sisa) : '')
  }
  
  return 'jumlah besar'
}

// Computed properties for summary calculations
const konsuls = computed(() => form.konsul || [])
const tindaks = computed(() => form.tindak || [])
const alkesList = computed(() => form.alkes || [])
const rspList = computed(() => form.rsp || [])
const lainnyas = computed(() => form.lainnya || [])

// Helper functions
const toNumber = (value) => {
  if (typeof value === 'string') {
    const cleaned = value.replace(/[^\d.-]/g, '')
    return parseFloat(cleaned) || 0
  }
  return parseFloat(value) || 0
}

const sumCurrency = (values) => {
  return values.reduce((sum, value) => sum + toNumber(value), 0)
}

const formatNumber = (number) => {
  return new Intl.NumberFormat('id-ID').format(number)
}

// Calculate grand total
const grandTotal = computed(() => {
  const konsulTotal = sumCurrency(konsuls.value.map(r => toNumber(r.jmlh_kons) * toNumber(r.bya_kons)))
  const tindakTotal = sumCurrency(tindaks.value.map(r => toNumber(r.jmlh_tindak) * toNumber(r.bya_tindak)))
  const alkesTotal = sumCurrency(alkesList.value.map(r => toNumber(r.jmlh_alkes) * toNumber(r.bya_alkes)))
  const rspTotal = sumCurrency(rspList.value.map(r => toNumber(r.jmlh_rsp) * toNumber(r.bya_rsp)))
  const lainnyaTotal = sumCurrency(lainnyas.value.map(r => toNumber(r.jmlh_lainnaya) * toNumber(r.bya_lainnya)))
  
  return konsulTotal + tindakTotal + alkesTotal + rspTotal + lainnyaTotal
})

// Submit form
const submitForm = () => {
  // Prevent accidental submissions
  if (form.processing) {
    console.log('Form is already processing, ignoring submit')
    return
  }
  
  // Debug: log form data
  console.log('Form data:', form.data())
  console.log('Is edit mode:', props.isEdit)
  console.log('Kunjungan ID:', kunjunganId.value)
  console.log('PSN ID:', props.psn?.id)
  
  // Validate required fields for edit mode
  if (props.isEdit) {
    if (!form.psn_id) {
      alert('PSN ID tidak ditemukan')
      return
    }
    if (!form.no_reg) {
      alert('No Registrasi tidak boleh kosong')
      return
    }
    if (!form.nm_p) {
      alert('Nama Pasien tidak boleh kosong')
      return
    }
  }
  
  isSubmitting.value = true
  
  if (props.isEdit) {
    // Update existing kunjungan with transaction
    form.put(route('pasien.kunjungan.with.transaksi.update', { 
      psnId: props.psn?.id, 
      kunjunganId: kunjunganId.value 
    }), {
      onSuccess: () => {
        console.log('Update successful')
        isSubmitting.value = false
        // Redirect to kunjungan detail or patient show
      },
      onError: (errors) => {
        console.error('Update errors:', errors)
        isSubmitting.value = false
      }
    })
  } else {
    // Create new transaction
    form.post(route('transaksi.store'), {
      onSuccess: () => {
        console.log('Create successful')
        isSubmitting.value = false
        // Redirect to kunjungan detail or transaction list
      },
      onError: (errors) => {
        console.error('Create errors:', errors)
        isSubmitting.value = false
      }
    })
  }
}
</script>