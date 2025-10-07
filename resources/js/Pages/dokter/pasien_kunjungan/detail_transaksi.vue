<template>
  <AuthenticatedLayout>
    <Head title="Form Transaksi" />
    
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900">
            
            <!-- Header -->
            <div class="mb-6">
              <h1 class="text-2xl font-bold text-gray-900">Form Transaksi</h1>
              <p class="text-gray-600 mt-1">Buat transaksi baru untuk kunjungan pasien</p>
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
                  <p class="mt-1 text-sm text-gray-900 bg-white px-3 py-2 border rounded-md">{{ props.kunjungan?.no_reg || '-' }}</p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">Nama Pasien</label>
                  <p class="mt-1 text-sm text-gray-900 bg-white px-3 py-2 border rounded-md">{{ props.kunjungan?.nm_p || '-' }}</p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">MRN</label>
                  <p class="mt-1 text-sm text-gray-900 bg-white px-3 py-2 border rounded-md">{{ props.kunjungan?.mrn || '-' }}</p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">Tanggal Registrasi</label>
                  <p class="mt-1 text-sm text-gray-900 bg-white px-3 py-2 border rounded-md">{{ formatDate(props.kunjungan?.tgl_reg) }}</p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">Jenis Perawatan</label>
                  <p class="mt-1 text-sm text-gray-900 bg-white px-3 py-2 border rounded-md">{{ props.kunjungan?.perawatan || '-' }}</p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">Penjamin</label>
                  <p class="mt-1 text-sm text-gray-900 bg-white px-3 py-2 border rounded-md">{{ props.kunjungan?.penjamin || '-' }}</p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">Jenis Kunjungan</label>
                  <p class="mt-1 text-sm text-gray-900 bg-white px-3 py-2 border rounded-md">{{ props.kunjungan?.kunjungan || '-' }}</p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">Alamat</label>
                  <p class="mt-1 text-sm text-gray-900 bg-white px-3 py-2 border rounded-md">{{ props.kunjungan?.almt_B || '-' }}</p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">ICD Code</label>
                  <p class="mt-1 text-sm text-gray-900 bg-white px-3 py-2 border rounded-md">{{ props.kunjungan?.icd || '-' }}</p>
                </div>
              </div>
            </div>

            <!-- Transaction Form -->
            <form @submit.prevent="submit" @keydown.enter.prevent class="space-y-6">
              
              <!-- Transaction Details -->
              <div class="bg-white border border-gray-200 rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Detail Transaksi</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Tanggal Transaksi</label>
                    <input 
                      type="date" 
                      v-model="form.tanggal"
                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                      required
                    />
                    <div v-if="form.errors.tanggal" class="text-red-500 text-sm mt-1">{{ form.errors.tanggal }}</div>
                  </div>
                  
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Status</label>
                    <select 
                      v-model="form.status"
                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                      required
                    >
                      <option value="">Pilih Status</option>
                      <option value="pending">Pending</option>
                      <option value="completed">Completed</option>
                      <option value="cancelled">Cancelled</option>
                    </select>
                    <div v-if="form.errors.status" class="text-red-500 text-sm mt-1">{{ form.errors.status }}</div>
                  </div>
                </div>
              </div>

              <!-- Medical Services Tabs -->
              <div class="bg-white border border-gray-200 rounded-lg">
                <div class="border-b border-gray-200">
                  <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                    <button 
                      type="button"
                      v-for="tab in tabs" 
                      :key="tab.id"
                      @click.prevent="activeTab = tab.id"
                      :class="[
                        activeTab === tab.id 
                          ? 'border-blue-500 text-blue-600' 
                          : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                        'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm'
                      ]"
                    >
                      {{ tab.name }}
                    </button>
                  </nav>
                </div>

                <div class="p-6">
                  <!-- Konsultasi Tab -->
                  <div v-show="activeTab === 'konsul'" class="space-y-4">
                    <div class="flex justify-between items-center">
                      <h3 class="text-lg font-medium text-gray-900">Konsultasi</h3>
                      <button 
                        type="button" 
                        @click.prevent="addKonsul"
                        class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600"
                      >
                        Tambah Konsultasi
                      </button>
                    </div>
                    
                    <div v-for="(konsul, index) in form.konsul" :key="index" class="border border-gray-200 rounded-lg p-4">
                      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div>
                          <label class="block text-sm font-medium text-gray-700">Dokter</label>
                          <input 
                            type="text" 
                            v-model="konsul.dokter"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Nama Dokter"
                          />
                        </div>
                        <div>
                          <label class="block text-sm font-medium text-gray-700">Deskripsi</label>
                          <input 
                            type="text" 
                            v-model="konsul.dskp_kons"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Deskripsi Konsultasi"
                          />
                        </div>
                        <div>
                          <label class="block text-sm font-medium text-gray-700">Jumlah</label>
                          <input 
                            type="number" 
                            v-model="konsul.jmlh_kons"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                            min="1"
                          />
                        </div>
                        <div>
                          <label class="block text-sm font-medium text-gray-700">Biaya</label>
                          <input 
                            type="number" 
                            v-model="konsul.bya_kons"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                            min="0"
                            step="0.01"
                          />
                        </div>
                        <div>
                          <label class="block text-sm font-medium text-gray-700">Diskon</label>
                          <input 
                            type="text" 
                            v-model="konsul.disc_kons"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                            placeholder="0%"
                          />
                        </div>
                        <div>
                          <label class="block text-sm font-medium text-gray-700">Tanggal</label>
                          <input 
                            type="date" 
                            v-model="konsul.tanggal"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                          />
                        </div>
                      </div>
                      <div class="mt-4 flex justify-end">
                        <button 
                          type="button" 
                          @click.prevent="removeKonsul(index)"
                          class="bg-red-500 text-white px-3 py-1 rounded-md hover:bg-red-600 text-sm"
                        >
                          Hapus
                        </button>
                      </div>
                    </div>
                  </div>

                  <!-- Tindakan Tab -->
                  <div v-show="activeTab === 'tindak'" class="space-y-4">
                    <div class="flex justify-between items-center">
                      <h3 class="text-lg font-medium text-gray-900">Tindakan</h3>
                      <button 
                        type="button" 
                        @click.prevent="addTindak"
                        class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600"
                      >
                        Tambah Tindakan
                      </button>
                    </div>
                    
                    <div v-for="(tindak, index) in form.tindak" :key="index" class="border border-gray-200 rounded-lg p-4">
                      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div>
                          <label class="block text-sm font-medium text-gray-700">Dokter</label>
                          <input 
                            type="text" 
                            v-model="tindak.dktr_tindak"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Nama Dokter"
                          />
                        </div>
                        <div>
                          <label class="block text-sm font-medium text-gray-700">Deskripsi</label>
                          <input 
                            type="text" 
                            v-model="tindak.dskp_tindak"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Deskripsi Tindakan"
                          />
                        </div>
                        <div>
                          <label class="block text-sm font-medium text-gray-700">Jumlah</label>
                          <input 
                            type="number" 
                            v-model="tindak.jmlh_tindak"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                            min="1"
                          />
                        </div>
                        <div>
                          <label class="block text-sm font-medium text-gray-700">Biaya</label>
                          <input 
                            type="number" 
                            v-model="tindak.bya_tindak"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                            min="0"
                            step="0.01"
                          />
                        </div>
                        <div>
                          <label class="block text-sm font-medium text-gray-700">Diskon</label>
                          <input 
                            type="text" 
                            v-model="tindak.disc_tindak"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                            placeholder="0%"
                          />
                        </div>
                        <div>
                          <label class="block text-sm font-medium text-gray-700">Tanggal</label>
                          <input 
                            type="date" 
                            v-model="tindak.tanggal"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                          />
                        </div>
                      </div>
                      <div class="mt-4 flex justify-end">
                        <button 
                          type="button" 
                          @click.prevent="removeTindak(index)"
                          class="bg-red-500 text-white px-3 py-1 rounded-md hover:bg-red-600 text-sm"
                        >
                          Hapus
                        </button>
                      </div>
                    </div>
                  </div>

                  <!-- Alkes Tab -->
                  <div v-show="activeTab === 'alkes'" class="space-y-4">
                    <div class="flex justify-between items-center">
                      <h3 class="text-lg font-medium text-gray-900">Alat Kesehatan</h3>
                      <button 
                        type="button" 
                        @click.prevent="addAlkes"
                        class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600"
                      >
                        Tambah Alkes
                      </button>
                    </div>
                    
                    <div v-for="(alkes, index) in form.alkes" :key="index" class="border border-gray-200 rounded-lg p-4">
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
                          @click.prevent="removeAlkes(index)"
                          class="bg-red-500 text-white px-3 py-1 rounded-md hover:bg-red-600 text-sm"
                        >
                          Hapus
                        </button>
                      </div>
                    </div>
                  </div>

                  <!-- Resep Tab -->
                  <div v-show="activeTab === 'rsp'" class="space-y-4">
                    <div class="flex justify-between items-center">
                      <h3 class="text-lg font-medium text-gray-900">Resep</h3>
                      <button 
                        type="button" 
                        @click.prevent="addRsp"
                        class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600"
                      >
                        Tambah Resep
                      </button>
                    </div>
                    
                    <div v-for="(rsp, index) in form.rsp" :key="index" class="border border-gray-200 rounded-lg p-4">
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
                          @click.prevent="removeRsp(index)"
                          class="bg-red-500 text-white px-3 py-1 rounded-md hover:bg-red-600 text-sm"
                        >
                          Hapus
                        </button>
                      </div>
                    </div>
                  </div>

                  <!-- Lainnya Tab -->
                  <div v-show="activeTab === 'lainnya'" class="space-y-4">
                    <div class="flex justify-between items-center">
                      <h3 class="text-lg font-medium text-gray-900">Lainnya</h3>
                      <button 
                        type="button" 
                        @click.prevent="addLainnya"
                        class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600"
                      >
                        Tambah Lainnya
                      </button>
                    </div>
                    
                    <div v-for="(lainnya, index) in form.lainnya" :key="index" class="border border-gray-200 rounded-lg p-4">
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
                          @click.prevent="removeLainnya(index)"
                          class="bg-red-500 text-white px-3 py-1 rounded-md hover:bg-red-600 text-sm"
                        >
                          Hapus
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Total Summary -->
              <div class="bg-gray-50 border border-gray-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Ringkasan Biaya</h3>
                
                <!-- Summary Stats -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                  <div class="bg-white rounded-lg p-3 text-center">
                    <div class="text-lg font-bold text-blue-600">{{ getTotalItems() }}</div>
                    <div class="text-xs text-gray-600">Total Item</div>
                  </div>
                  <div class="bg-white rounded-lg p-3 text-center">
                    <div class="text-lg font-bold text-green-600">{{ getActiveCategories() }}</div>
                    <div class="text-xs text-gray-600">Kategori Aktif</div>
                  </div>
                  <div class="bg-white rounded-lg p-3 text-center">
                    <div class="text-lg font-bold text-purple-600">{{ getAveragePerItem() }}</div>
                    <div class="text-xs text-gray-600">Rata-rata/Item</div>
                  </div>
                  <div class="bg-white rounded-lg p-3 text-center">
                    <div class="text-lg font-bold text-orange-600">{{ getHighestCategory() }}</div>
                    <div class="text-xs text-gray-600">Kategori Tertinggi</div>
                  </div>
                </div>
                
                <!-- Sub Total Breakdown -->
                <div class="space-y-3 mb-6">
                  <div class="flex justify-between items-center py-2 border-b border-gray-200">
                    <div class="flex items-center">
                      <span class="text-sm font-medium text-gray-700">Konsultasi</span>
                      <span v-if="form.konsul.length === 0" class="ml-2 text-xs text-gray-500">(kosong)</span>
                      <span v-else class="ml-2 text-xs text-green-600">({{ form.konsul.length }} item)</span>
                    </div>
                    <span class="text-sm font-semibold text-gray-900">Rp {{ formatCurrency(calculateKonsulTotal()) }}</span>
                  </div>
                  
                  <div class="flex justify-between items-center py-2 border-b border-gray-200">
                    <div class="flex items-center">
                      <span class="text-sm font-medium text-gray-700">Tindakan</span>
                      <span v-if="form.tindak.length === 0" class="ml-2 text-xs text-gray-500">(kosong)</span>
                      <span v-else class="ml-2 text-xs text-green-600">({{ form.tindak.length }} item)</span>
                    </div>
                    <span class="text-sm font-semibold text-gray-900">Rp {{ formatCurrency(calculateTindakTotal()) }}</span>
                  </div>
                  
                  <div class="flex justify-between items-center py-2 border-b border-gray-200">
                    <div class="flex items-center">
                      <span class="text-sm font-medium text-gray-700">Alat Kesehatan</span>
                      <span v-if="form.alkes.length === 0" class="ml-2 text-xs text-gray-500">(kosong)</span>
                      <span v-else class="ml-2 text-xs text-green-600">({{ form.alkes.length }} item)</span>
                    </div>
                    <span class="text-sm font-semibold text-gray-900">Rp {{ formatCurrency(calculateAlkesTotal()) }}</span>
                  </div>
                  
                  <div class="flex justify-between items-center py-2 border-b border-gray-200">
                    <div class="flex items-center">
                      <span class="text-sm font-medium text-gray-700">Resep</span>
                      <span v-if="form.rsp.length === 0" class="ml-2 text-xs text-gray-500">(kosong)</span>
                      <span v-else class="ml-2 text-xs text-green-600">({{ form.rsp.length }} item)</span>
                    </div>
                    <span class="text-sm font-semibold text-gray-900">Rp {{ formatCurrency(calculateRspTotal()) }}</span>
                  </div>
                  
                  <div class="flex justify-between items-center py-2 border-b border-gray-200">
                    <div class="flex items-center">
                      <span class="text-sm font-medium text-gray-700">Lainnya</span>
                      <span v-if="form.lainnya.length === 0" class="ml-2 text-xs text-gray-500">(kosong)</span>
                      <span v-else class="ml-2 text-xs text-green-600">({{ form.lainnya.length }} item)</span>
                    </div>
                    <span class="text-sm font-semibold text-gray-900">Rp {{ formatCurrency(calculateLainnyaTotal()) }}</span>
                  </div>
                </div>
                
                <!-- Grand Total -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                  <div class="flex justify-between items-center">
                    <span class="text-lg font-bold text-blue-900">Grand Total</span>
                    <span class="text-2xl font-bold text-blue-900">Rp {{ formatCurrency(calculateTotal()) }}</span>
                  </div>
                  <div class="mt-2 text-sm text-blue-700 italic">
                    {{ terbilang(calculateTotal()) }} rupiah
                  </div>
                </div>
              </div>

              <!-- Submit Buttons -->
              <div class="flex justify-end space-x-4">
                <Link 
                  v-if="kunjunganId"
                  :href="route('kunjungan.show', { kunjungan: kunjunganId })"
                  class="bg-gray-500 text-white px-6 py-2 rounded-md hover:bg-gray-600"
                >
                  Batal
                </Link>
                <button 
                  v-else
                  type="button"
                  class="bg-gray-500 text-white px-6 py-2 rounded-md opacity-50 cursor-not-allowed"
                  disabled
                >
                  Batal
                </button>
                <button 
                  type="submit" 
                  :disabled="form.processing"
                  class="bg-blue-500 text-white px-6 py-2 rounded-md hover:bg-blue-600 disabled:opacity-50"
                >
                  {{ form.processing ? 'Menyimpan...' : (props.isEdit ? 'Update Transaksi' : 'Simpan Transaksi') }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useForm } from '@inertiajs/vue3'
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

const activeTab = ref('konsul')

const tabs = [
  { id: 'konsul', name: 'Konsultasi' },
  { id: 'tindak', name: 'Tindakan' },
  { id: 'alkes', name: 'Alkes' },
  { id: 'rsp', name: 'Resep' },
  { id: 'lainnya', name: 'Lainnya' }
]

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
  kunjungan_id: kunjunganId.value,
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

// Submit form
const submit = () => {
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
  
  if (props.isEdit) {
    // Update existing kunjungan with transaction
    form.put(route('pasien.kunjungan.with.transaksi.update', { 
      psnId: props.psn?.id, 
      kunjunganId: kunjunganId.value 
    }), {
      onSuccess: () => {
        console.log('Update successful')
        // Redirect to kunjungan detail or patient show
      },
      onError: (errors) => {
        console.error('Update errors:', errors)
      }
    })
  } else {
    // Create new transaction
    form.post(route('transaksi.store'), {
      onSuccess: () => {
        console.log('Create successful')
        // Redirect to kunjungan detail or transaction list
      },
      onError: (errors) => {
        console.error('Create errors:', errors)
      }
    })
  }
}
</script>