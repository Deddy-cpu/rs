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
                            :value="userFullName"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Nama Dokter"
                            readonly
                            tabindex="-1"
                          />
                        </div>
                        <div>
                          <label class="block text-sm font-medium text-gray-700">Deskripsi</label>
                          <div class="flex">
                            <input 
                              type="text" 
                              v-model="konsul.dskp_kons"
                              class="mt-1 block w-full border-gray-300 rounded-l-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Deskripsi Konsultasi"
                            />
                            <button 
                              type="button"
                              @click.prevent="openTindakanTarifModal(index, 'konsul')"
                              class="mt-1 px-3 py-2 border border-l-0 border-gray-300 rounded-r-md bg-blue-50 hover:bg-blue-100 text-blue-600 focus:ring-blue-500 focus:border-blue-500"
                              title="Pilih Tindakan Tarif"
                            >
                              <i class="fas fa-search"></i>
                            </button>
                          </div>
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
                            :value="userFullName"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Nama Dokter"
                            readonly
                            tabindex="-1"
                          />
                        </div>
                        <div>
                          <label class="block text-sm font-medium text-gray-700">Deskripsi</label>
                          <div class="flex">
                            <input 
                              type="text" 
                              v-model="tindak.dskp_tindak"
                              class="mt-1 block w-full border-gray-300 rounded-l-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Deskripsi Tindakan"
                            />
                            <button 
                              type="button"
                              @click.prevent="openTindakanTarifModal(index, 'tindak')"
                              class="mt-1 px-3 py-2 border border-l-0 border-gray-300 rounded-r-md bg-blue-50 hover:bg-blue-100 text-blue-600 focus:ring-blue-500 focus:border-blue-500"
                              title="Pilih Tindakan Tarif"
                            >
                              <i class="fas fa-search"></i>
                            </button>
                          </div>
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
                          <div class="flex">
                            <input 
                              type="text" 
                              v-model="alkes.dskp_alkes"
                              class="mt-1 block w-full border-gray-300 rounded-l-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Deskripsi Alkes"
                            />
                            <button 
                              type="button"
                              @click.prevent="openFarmalkesModal(index, 'alkes')"
                              class="mt-1 px-3 py-2 border border-l-0 border-gray-300 rounded-r-md bg-blue-50 hover:bg-blue-100 text-blue-600 focus:ring-blue-500 focus:border-blue-500"
                              title="Pilih Farmalkes"
                            >
                              <i class="fas fa-search"></i>
                            </button>
                          </div>
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
                            :value="userFullName"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Nama Dokter"
                            readonly
                            tabindex="-1"
                          />
                        </div>
                        <div>
                          <label class="block text-sm font-medium text-gray-700">Deskripsi</label>
                          <div class="flex">
                            <input 
                              type="text" 
                              v-model="rsp.dskp_rsp"
                              class="mt-1 block w-full border-gray-300 rounded-l-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Deskripsi Resep"
                            />
                            <button 
                              type="button"
                              @click.prevent="openFarmalkesModal(index, 'rsp')"
                              class="mt-1 px-3 py-2 border border-l-0 border-gray-300 rounded-r-md bg-blue-50 hover:bg-blue-100 text-blue-600 focus:ring-blue-500 focus:border-blue-500"
                              title="Pilih Farmalkes"
                            >
                              <i class="fas fa-search"></i>
                            </button>
                          </div>
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
                            :value="userFullName"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Nama Dokter"
                            readonly
                            tabindex="-1"
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
                            v-model="lainnya.jmlh_lainnya"
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
              <div class="flex justify-end space-x-4 items-center">
                <div class="text-sm mr-2" v-if="props.isEdit">
                  <span v-if="saveStatus === 'saving'" class="text-blue-600">Menyimpan...</span>
                  <span v-else-if="saveStatus === 'saved'" class="text-green-600">Tersimpan otomatis<span v-if="lastSavedAt"> • {{ new Date(lastSavedAt).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' }) }}</span></span>
                  <span v-else-if="saveStatus === 'error'" class="text-red-600">Gagal menyimpan otomatis</span>
                </div>
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

    <!-- Optimistic Locking Modal -->
    <div v-if="optimisticLockModal" class="fixed z-50 inset-0 flex items-center justify-center bg-black/40">
      <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full mx-4">
        <div class="p-6 text-center">
          <div class="mb-4">
            <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto">
              <i class="fas fa-exclamation-triangle text-2xl text-yellow-600"></i>
            </div>
          </div>
          <h2 class="text-xl font-bold mb-3 text-gray-800">Konflik Data Terdeteksi</h2>
          <div class="text-gray-600 mb-4 space-y-2">
            <p class="text-sm">
              Data transaksi ini telah <span class="font-semibold text-red-600">diubah oleh pengguna lain</span> atau tab lain.
            </p>
            <p class="text-sm">
              Untuk mencegah kehilangan data, silakan muat ulang halaman untuk mendapatkan versi terbaru.
            </p>
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mt-3">
              <p class="text-xs text-yellow-800">
                <i class="fas fa-info-circle mr-1"></i>
                Error: OptimisticLockingException (409 Conflict)
              </p>
            </div>
          </div>
        </div>
        <div class="p-4 flex justify-center border-t border-gray-100 bg-gray-50">
          <button 
            @click="reloadPage" 
            class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors flex items-center"
          >
            <i class="fas fa-refresh mr-2"></i>
            Muat Ulang Halaman
          </button>
        </div>
      </div>
    </div>

    <!-- Tindakan Tarif Selection Modal -->
    <div v-if="showTindakanTarifModal" class="fixed inset-0 z-50 overflow-y-auto" @click.self="closeTindakanTarifModal">
      <div class="flex min-h-screen items-center justify-center p-4">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity"></div>
        
        <!-- Modal -->
        <div class="relative bg-white rounded-2xl shadow-2xl max-w-4xl w-full max-h-[80vh] overflow-hidden animate-fade-in">
          <!-- Header -->
          <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
            <div class="flex items-center justify-between">
              <div class="flex items-center">
                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center mr-3">
                  <i class="fas fa-list-alt text-white text-lg"></i>
                </div>
                <div>
                  <h3 class="text-xl font-bold text-white">Pilih Tindakan Tarif</h3>
                  <p class="text-blue-100 text-sm">Pilih tindakan untuk mengisi deskripsi dan biaya secara otomatis</p>
                </div>
              </div>
              <button
                @click="closeTindakanTarifModal"
                class="w-8 h-8 bg-white/20 hover:bg-white/30 rounded-lg flex items-center justify-center transition-colors"
              >
                <i class="fas fa-times text-white"></i>
              </button>
            </div>
          </div>

          <!-- Search -->
          <div class="p-6 border-b border-gray-200">
            <div class="relative">
              <input
                v-model="searchTindakanTarif"
                type="text"
                placeholder="Cari tindakan, grup eselon, atau tarif..."
                class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              />
              <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
            </div>
          </div>

          <!-- Content -->
          <div class="max-h-96 overflow-y-auto">
            <div v-if="filteredTindakanTarifs.length === 0" class="p-8 text-center text-gray-500">
              <i class="fas fa-search text-4xl mb-4"></i>
              <p>Tidak ada tindakan tarif yang ditemukan</p>
            </div>
            
            <div v-else class="p-4 space-y-2">
              <div
                v-for="tarif in filteredTindakanTarifs"
                :key="tarif.id"
                @click="selectTindakanTarif(tarif)"
                class="p-4 border border-gray-200 rounded-xl hover:border-blue-300 hover:bg-blue-50 cursor-pointer transition-all duration-200 hover:shadow-md"
              >
                <div class="flex items-center justify-between">
                  <div class="flex-1">
                    <h4 class="font-semibold text-gray-900">{{ tarif.tindakan_q?.tindakan_q_desc || 'Tindakan tidak tersedia' }}</h4>
                    <div class="flex items-center mt-2 space-x-4 text-sm text-gray-600">
                      <div class="flex items-center">
                        <i class="fas fa-tag mr-1"></i>
                        <span>{{ tarif.grp_eselon?.grp_eselon_desc || 'Grup tidak tersedia' }}</span>
                      </div>
                      <div class="flex items-center">
                        <i class="fas fa-money-bill-wave mr-1"></i>
                        <span class="font-medium text-green-600">Rp {{ formatCurrency(tarif.tarif) }}</span>
                      </div>
                      <div class="flex items-center">
                        <i class="fas fa-circle mr-1" :class="tarif.aktif === 'Y' ? 'text-green-500' : 'text-red-500'"></i>
                        <span>{{ tarif.aktif === 'Y' ? 'Aktif' : 'Tidak Aktif' }}</span>
                      </div>
                    </div>
                  </div>
                  <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check text-blue-600"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Footer -->
          <div class="p-6 border-t border-gray-200 bg-gray-50">
            <div class="flex justify-end">
              <button
                @click="closeTindakanTarifModal"
                class="px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition-colors"
              >
                Batal
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Farmalkes Selection Modal -->
    <div v-if="showFarmalkesModal" class="fixed inset-0 z-50 overflow-y-auto" @click.self="closeFarmalkesModal">
      <div class="flex min-h-screen items-center justify-center p-4">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity"></div>
        
        <!-- Modal -->
        <div class="relative bg-white rounded-2xl shadow-2xl max-w-4xl w-full max-h-[80vh] overflow-hidden animate-fade-in">
          <!-- Header -->
          <div class="bg-gradient-to-r from-green-500 to-green-600 px-6 py-4">
            <div class="flex items-center justify-between">
              <div class="flex items-center">
                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center mr-3">
                  <i class="fas fa-pills text-white text-lg"></i>
                </div>
                <div>
                  <h3 class="text-xl font-bold text-white">Pilih Farmalkes</h3>
                  <p class="text-green-100 text-sm">Pilih farmalkes untuk mengisi deskripsi dan biaya secara otomatis</p>
                </div>
              </div>
              <button
                @click="closeFarmalkesModal"
                class="w-8 h-8 bg-white/20 hover:bg-white/30 rounded-lg flex items-center justify-center transition-colors"
              >
                <i class="fas fa-times text-white"></i>
              </button>
            </div>
          </div>

          <!-- Search -->
          <div class="p-6 border-b border-gray-200">
            <div class="relative">
              <input
                v-model="searchFarmalkes"
                type="text"
                placeholder="Cari nama farmalkes, satuan, atau harga..."
                class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500"
              />
              <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
            </div>
          </div>

          <!-- Content -->
          <div class="max-h-96 overflow-y-auto">
            <div v-if="filteredFarmalkes.length === 0" class="p-8 text-center text-gray-500">
              <i class="fas fa-search text-4xl mb-4"></i>
              <p>Tidak ada farmalkes yang ditemukan</p>
            </div>
            
            <div v-else class="p-4 space-y-2">
              <div
                v-for="farmalkes in filteredFarmalkes"
                :key="farmalkes.id"
                @click="selectFarmalkes(farmalkes)"
                class="p-4 border border-gray-200 rounded-xl hover:border-green-300 hover:bg-green-50 cursor-pointer transition-all duration-200 hover:shadow-md"
              >
                <div class="flex items-center justify-between">
                  <div class="flex-1">
                    <h4 class="font-semibold text-gray-900">{{ farmalkes.nama_item || 'Farmalkes tidak tersedia' }}</h4>
                    <div class="flex items-center mt-2 space-x-4 text-sm text-gray-600">
                      <div class="flex items-center">
                        <i class="fas fa-tag mr-1"></i>
                        <span>{{ farmalkes.satuan || 'Satuan tidak tersedia' }}</span>
                      </div>
                      <div class="flex items-center">
                        <i class="fas fa-money-bill-wave mr-1"></i>
                        <span class="font-medium text-green-600">Rp {{ formatCurrency(farmalkes.harga) }}</span>
                      </div>
                      <div class="flex items-center">
                        <i class="fas fa-circle mr-1" :class="farmalkes.aktif === 'Y' ? 'text-green-500' : 'text-red-500'"></i>
                        <span>{{ farmalkes.aktif === 'Y' ? 'Aktif' : 'Tidak Aktif' }}</span>
                      </div>
                    </div>
                  </div>
                  <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check text-green-600"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Footer -->
          <div class="p-6 border-t border-gray-200 bg-gray-50">
            <div class="flex justify-end">
              <button
                @click="closeFarmalkesModal"
                class="px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition-colors"
              >
                Batal
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link } from '@inertiajs/vue3'

// Optimistic Locking Modal State
const optimisticLockModal = ref(false)

function reloadPage() {
  window.location.reload()
}

// Global error handler for JSON responses
const handleJsonError = (error) => {
  console.error('JSON Error received:', error)
  
  // Check if it's a conflict error
  if (error && (
    error.message?.toLowerCase().includes('conflict') ||
    error.message?.toLowerCase().includes('modified by another user') ||
    error.errors?.conflict
  )) {
    optimisticLockModal.value = true
    return true
  }
  
  return false
}

// Mengambil informasi user dari auth prop
const user = computed(() => {
  return props.auth?.user || null
})

const userFullName = computed(() => {
  if (user.value) {
    // Laravel inertia default: { name }
    return user.value.name || user.value.nama || user.value.full_name || ''
  }
  return ''
})

const props = defineProps({
  kunjungan: Object,
  kunjunganId: Number,
  psn: Object,
  isEdit: Boolean,
  auth: Object,
  tindakanTarifs: {
    type: Array,
    default: () => []
  },
  farmalkes: {
    type: Array,
    default: () => []
  }
})

// Get the kunjungan ID from either the prop or the kunjungan object
const kunjunganId = computed(() => {
  return props.kunjunganId || props.kunjungan?.id
})

const activeTab = ref('konsul')

// Modal state management
const showTindakanTarifModal = ref(false)
const selectedTindakanTarif = ref(null)
const searchTindakanTarif = ref('')
const currentItemIndex = ref(null)
const currentItemType = ref(null) // 'konsul' or 'tindak'

// Farmalkes modal state management
const showFarmalkesModal = ref(false)
const selectedFarmalkes = ref(null)
const searchFarmalkes = ref('')
const currentFarmalkesItemIndex = ref(null)
const currentFarmalkesItemType = ref(null) // 'alkes' or 'rsp'

const tabs = [
  { id: 'konsul', name: 'Konsultasi' },
  { id: 'tindak', name: 'Tindakan' },
  { id: 'alkes', name: 'Alkes' },
  { id: 'rsp', name: 'Resep' },
  { id: 'lainnya', name: 'Lainnya' }
]

// Autosave state
const saveStatus = ref('idle') // 'idle' | 'saving' | 'saved' | 'error'
const lastSavedAt = ref(null)
const saveError = ref('')
const hasInitializedAutosave = ref(false)
const enableAutosave = ref(false) // set to true to re-enable

function debounce(fn, delay = 800) {
  let timeout
  return (...args) => {
    clearTimeout(timeout)
    timeout = setTimeout(() => fn(...args), delay)
  }
}

// Filtered tindakan tarifs based on search
const filteredTindakanTarifs = computed(() => {
  if (!searchTindakanTarif.value) return props.tindakanTarifs
  
  return props.tindakanTarifs.filter(tarif => 
    tarif.tindakan_q?.tindakan_q_desc?.toLowerCase().includes(searchTindakanTarif.value.toLowerCase()) ||
    tarif.grp_eselon?.grp_eselon_desc?.toLowerCase().includes(searchTindakanTarif.value.toLowerCase()) ||
    tarif.tarif?.toString().includes(searchTindakanTarif.value)
  )
})

// Filtered farmalkes based on search
const filteredFarmalkes = computed(() => {
  if (!searchFarmalkes.value) return props.farmalkes
  
  return props.farmalkes.filter(farmalkes => 
    farmalkes.nama_item?.toLowerCase().includes(searchFarmalkes.value.toLowerCase()) ||
    farmalkes.satuan?.toLowerCase().includes(searchFarmalkes.value.toLowerCase()) ||
    farmalkes.harga?.toString().includes(searchFarmalkes.value)
  )
})

// Modal functions
const openTindakanTarifModal = (itemIndex, itemType) => {
  currentItemIndex.value = itemIndex
  currentItemType.value = itemType
  showTindakanTarifModal.value = true
}

const closeTindakanTarifModal = () => {
  showTindakanTarifModal.value = false
  searchTindakanTarif.value = ''
  currentItemIndex.value = null
  currentItemType.value = null
}

const selectTindakanTarif = (tarif) => {
  selectedTindakanTarif.value = tarif
  
  // Auto-fill description and cost based on item type
  if (currentItemType.value === 'konsul') {
    form.konsul[currentItemIndex.value].dskp_kons = tarif.tindakan_q?.tindakan_q_desc || ''
    form.konsul[currentItemIndex.value].bya_kons = parseFloat(tarif.tarif) || 0
    form.konsul[currentItemIndex.value].tindakan_tarif_id = tarif.id
    // Set dokter dengan nama user login
    form.konsul[currentItemIndex.value].dokter = userFullName.value
  } else if (currentItemType.value === 'tindak') {
    form.tindak[currentItemIndex.value].dskp_tindak = tarif.tindakan_q?.tindakan_q_desc || ''
    form.tindak[currentItemIndex.value].bya_tindak = parseFloat(tarif.tarif) || 0
    form.tindak[currentItemIndex.value].tindakan_tarif_id = tarif.id
    // Set dktr_tindak otomatis
    form.tindak[currentItemIndex.value].dktr_tindak = userFullName.value
  }
  
  closeTindakanTarifModal()
}

// Farmalkes modal functions
const openFarmalkesModal = (itemIndex, itemType) => {
  currentFarmalkesItemIndex.value = itemIndex
  currentFarmalkesItemType.value = itemType
  showFarmalkesModal.value = true
}

const closeFarmalkesModal = () => {
  showFarmalkesModal.value = false
  searchFarmalkes.value = ''
  currentFarmalkesItemIndex.value = null
  currentFarmalkesItemType.value = null
}

const selectFarmalkes = (farmalkes) => {
  selectedFarmalkes.value = farmalkes
  
  // Auto-fill description and cost based on item type
  if (currentFarmalkesItemType.value === 'alkes') {
    form.alkes[currentFarmalkesItemIndex.value].dskp_alkes = farmalkes.nama_item || ''
    form.alkes[currentFarmalkesItemIndex.value].bya_alkes = parseFloat(farmalkes.harga) || 0
    form.alkes[currentFarmalkesItemIndex.value].farmalkes_id = farmalkes.id
  } else if (currentFarmalkesItemType.value === 'rsp') {
    form.rsp[currentFarmalkesItemIndex.value].dskp_rsp = farmalkes.nama_item || ''
    form.rsp[currentFarmalkesItemIndex.value].bya_rsp = parseFloat(farmalkes.harga) || 0
    form.rsp[currentFarmalkesItemIndex.value].farmalkes_id = farmalkes.id
  }
  
  closeFarmalkesModal()
}

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
  kunjungan_version: props.kunjungan?.version || 1,
  updated_at: null,
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
    form.kunjungan_version = props.kunjungan.version || 1
    form.updated_at = props.kunjungan.updated_at || null
    
    form.tanggal = formatDateForInput(props.kunjungan.tgl_reg)
    form.status = 'completed' // or get from transaction if available
    
    // Populate medical services from existing data
    if (props.kunjungan.konsuls && props.kunjungan.konsuls.length > 0) {
      form.konsul = props.kunjungan.konsuls.map(konsul => ({
        dokter: userFullName.value || konsul.dokter || '',
        dskp_kons: konsul.dskp_kons || '',
        jmlh_kons: konsul.jmlh_kons || 1,
        bya_kons: konsul.bya_kons || 0,
        disc_kons: konsul.disc_kons || '0%',
        st_kons: konsul.st_kons || 0,
        tanggal: formatDateForInput(konsul.tanggal),
        tindakan_tarif_id: konsul.tindakan_tarif_id || null
      }))
    } else {
      addKonsul()
    }
    
    if (props.kunjungan.tindaks && props.kunjungan.tindaks.length > 0) {
      form.tindak = props.kunjungan.tindaks.map(tindak => ({
        dktr_tindak: userFullName.value || tindak.dktr_tindak || '',
        dskp_tindak: tindak.dskp_tindak || '',
        jmlh_tindak: tindak.jmlh_tindak || 1,
        bya_tindak: tindak.bya_tindak || 0,
        disc_tindak: tindak.disc_tindak || '0%',
        st_tindak: tindak.st_tindak || 0,
        tanggal: formatDateForInput(tindak.tanggal),
        tindakan_tarif_id: tindak.tindakan_tarif_id || null
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
        tanggal: formatDateForInput(alkes.tanggal),
        farmalkes_id: alkes.farmalkes_id || null
      }))
    }
    
    if (props.kunjungan.rsp && props.kunjungan.rsp.length > 0) {
      form.rsp = props.kunjungan.rsp.map(rsp => ({
        dktr_rsp: userFullName.value || rsp.dktr_rsp || '',
        dskp_rsp: rsp.dskp_rsp || '',
        jmlh_rsp: rsp.jmlh_rsp || 1,
        bya_rsp: rsp.bya_rsp || 0,
        disc_rsp: rsp.disc_rsp || '0%',
        st_rsp: rsp.st_rsp || 0,
        tanggal: formatDateForInput(rsp.tanggal),
        farmalkes_id: rsp.farmalkes_id || null
      }))
    }
    
    if (props.kunjungan.lainnyas && props.kunjungan.lainnyas.length > 0) {
      form.lainnya = props.kunjungan.lainnyas.map(lainnya => ({
        dktr_lainnya: userFullName.value || lainnya.dktr_lainnya || '',
        dskp_lainnya: lainnya.dskp_lainnya || '',
        jmlh_lainnya: lainnya.jmlh_lainnya || 1,
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

  // Mark autosave initialized after initial population
  hasInitializedAutosave.value = true
})

// Autosave logic (only in edit mode)
const performAutosave = async () => {
  if (!props.isEdit) return
  if (form.processing) return
  saveStatus.value = 'saving'
  saveError.value = ''
  form.put(route('pasien.kunjungan.with.transaksi.update', {
    psnId: props.psn?.id,
    kunjunganId: kunjunganId.value
  }), {
    preserveScroll: true,
    preserveState: true,
    replace: true,
    onSuccess: () => {
      saveStatus.value = 'saved'
      lastSavedAt.value = new Date()
      // Sync updated_at from server if returned via props reload
    },
      onError: (errors) => {
        saveStatus.value = 'error'
        saveError.value = 'Gagal menyimpan otomatis'
        console.error('Autosave errors:', errors)
        
        // Try to handle as JSON error first
        if (handleJsonError(errors)) {
          return
        }
        
        // Handle conflict/optimistic locking errors
        if (errors && (
          errors.conflict || 
          errors.response?.status === 409 ||
          (typeof errors?.error === 'string' && errors.error.toLowerCase().includes('conflict')) ||
          (typeof errors?.error === 'string' && errors.error.toLowerCase().includes('optimisticlockingexception'))
        )) {
          optimisticLockModal.value = true
        } else {
          // fallback
          alert('Gagal menyimpan otomatis.')
        }
      }
  })
}

const debouncedAutosave = debounce(performAutosave, 800)

// Watch form data deeply for changes
watch(
  () => form.data(),
  () => {
    if (!props.isEdit) return
    if (!hasInitializedAutosave.value) return
    if (!enableAutosave.value) return
    debouncedAutosave()
  },
  { deep: true }
)

// Konsultasi methods
const addKonsul = () => {
  form.konsul.push({
    dokter: userFullName.value,
    dskp_kons: '',
    jmlh_kons: 1,
    bya_kons: 0,
    disc_kons: '0%',
    st_kons: 0,
    tanggal: new Date().toISOString().split('T')[0],
    tindakan_tarif_id: null
  })
}

const removeKonsul = (index) => {
  form.konsul.splice(index, 1)
}

// Tindakan methods
const addTindak = () => {
  form.tindak.push({
    dktr_tindak: userFullName.value,
    dskp_tindak: '',
    jmlh_tindak: 1,
    bya_tindak: 0,
    disc_tindak: '0%',
    st_tindak: 0,
    tanggal: new Date().toISOString().split('T')[0],
    tindakan_tarif_id: null
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
    tanggal: new Date().toISOString().split('T')[0],
    farmalkes_id: null
  })
}

const removeAlkes = (index) => {
  form.alkes.splice(index, 1)
}

// Resep methods
const addRsp = () => {
  form.rsp.push({
    dktr_rsp: userFullName.value,
    dskp_rsp: '',
    jmlh_rsp: 1,
    bya_rsp: 0,
    disc_rsp: '0%',
    st_rsp: 0,
    tanggal: new Date().toISOString().split('T')[0],
    farmalkes_id: null
  })
}

const removeRsp = (index) => {
  form.rsp.splice(index, 1)
}

// Lainnya methods
const addLainnya = () => {
  form.lainnya.push({
    dktr_lainnya: userFullName.value,
    dskp_lainnya: '',
    jmlh_lainnya: 1,
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
    total += (item.jmlh_lainnya || 0) * (item.bya_lainnya || 0)
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
      preserveState: true,
      preserveScroll: true,
      replace: true,
      onSuccess: () => {
        console.log('Update successful')
        // Redirect to kunjungan detail or patient show
      },
      onError: (errors) => {
        console.error('Update errors:', errors)
        
        // Try to handle as JSON error first
        if (handleJsonError(errors)) {
          return
        }
        
        // Handle conflict/optimistic locking errors
        if (errors && (
          errors.conflict || 
          errors.response?.status === 409 ||
          (typeof errors?.error === 'string' && errors.error.toLowerCase().includes('conflict')) ||
          (typeof errors?.error === 'string' && errors.error.toLowerCase().includes('optimisticlockingexception'))
        )) {
          optimisticLockModal.value = true
        } else if (errors && (errors.error || errors.conflict)) {
          const errorMessage = errors.error || 'Data telah diubah oleh pengguna lain. Silakan muat ulang halaman.'
          alert(errorMessage)
          window.location.reload()
        } else {
          alert('Gagal menyimpan perubahan. Silakan coba lagi.')
        }
      }
    })
  } else {
    // Create new transaction
    form.post(route('transaksi.store'), {
      preserveState: true,
      preserveScroll: true,
      replace: true,
      onSuccess: () => {
        console.log('Create successful')
        // Redirect to kunjungan detail or transaction list
      },
      onError: (errors) => {
        console.error('Create errors:', errors)
        
        // Try to handle as JSON error first
        if (handleJsonError(errors)) {
          return
        }
        
        // Handle conflict/optimistic locking errors
        if (errors && (
          errors.conflict || 
          errors.response?.status === 409 ||
          (typeof errors?.error === 'string' && errors.error.toLowerCase().includes('conflict')) ||
          (typeof errors?.error === 'string' && errors.error.toLowerCase().includes('optimisticlockingexception'))
        )) {
          optimisticLockModal.value = true
        } else if (errors && (errors.error || errors.conflict)) {
          const errorMessage = errors.error || 'Data telah diubah oleh pengguna lain. Silakan muat ulang halaman.'
          alert(errorMessage)
          window.location.reload()
        } else {
          alert('Gagal menyimpan perubahan. Silakan coba lagi.')
        }
      }
    })
  }
}
</script>