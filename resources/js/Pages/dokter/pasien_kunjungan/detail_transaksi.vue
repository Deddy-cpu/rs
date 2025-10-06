<template>
  <AuthenticatedLayout>
    <Head title="Detail Transaksi Pasien" />
    
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
                    <tr v-for="(row, i) in konsuls" :key="i" class="hover:bg-gray-50">
                      <td class="p-2 border"><input v-model="row.dokter" class="w-full border rounded px-2 py-1" placeholder="Nama dokter" /></td>
                      <td class="p-2 border"><input v-model="row.dskp_kons" class="w-full border rounded px-2 py-1" placeholder="Deskripsi konsultasi" /></td>
                      <td class="p-2 border"><input v-model="row.jmlh_kons" type="number" min="1" class="w-full border rounded px-2 py-1" /></td>
                      <td class="p-2 border"><input v-model="row.bya_kons" @input="updateSubtotal(row, 'bya_kons','disc_kons','st_kons','jmlh_kons')" type="number" min="0" class="w-full border rounded px-2 py-1" placeholder="0" /></td>
                      <td class="p-2 border"><input v-model="row.disc_kons" @input="updateSubtotal(row, 'bya_kons','disc_kons','st_kons','jmlh_kons')" class="w-full border rounded px-2 py-1" placeholder="0%" /></td>
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
