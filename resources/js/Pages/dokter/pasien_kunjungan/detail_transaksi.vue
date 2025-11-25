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

            <!-- Last Modified Info Banner (Edit Mode Only) -->
            <div v-if="props.isEdit && props.kunjungan?.last_modified_by" class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <i class="fas fa-info-circle text-blue-400"></i>
                </div>
                <div class="ml-3">
                  <p class="text-sm text-blue-700">
                    <span class="font-medium">Informasi:</span> Data terakhir diubah oleh 
                    <span class="font-semibold">{{ props.kunjungan.last_modified_by }}</span>
                    <span v-if="props.kunjungan?.last_modified_at">
                      pada {{ formatDateTime(props.kunjungan.last_modified_at) }}
                    </span>
                  </p>
                </div>
              </div>
            </div>

            <!-- Active Editing Warning Banner -->
            <div v-if="isLockedByOther" class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6 animate-pulse">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <i class="fas fa-exclamation-triangle text-yellow-400 text-xl"></i>
                </div>
                <div class="ml-3 flex-1">
                  <p class="text-sm text-yellow-800">
                    <span class="font-bold">⚠️ PERINGATAN:</span> 
                    <span class="font-semibold text-red-600">{{ lockInfo.locked_by }}</span> 
                    sedang <span class="font-semibold">mengubah data ini</span> saat ini!
                  </p>
                  <p class="text-xs text-yellow-700 mt-1">
                    <i class="fas fa-clock mr-1"></i>
                    Dimulai sejak {{ formatDateTime(lockInfo.locked_since) }}
                  </p>
                  <p class="text-xs text-yellow-700 mt-1 font-medium">
                    Jika Anda melanjutkan mengedit, data bisa saling bertabrakan. Mohon tunggu atau koordinasi dengan dokter tersebut.
                  </p>
                </div>
                <div class="flex-shrink-0">
                  <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-200 text-yellow-800">
                    <i class="fas fa-user-edit mr-1"></i>
                    Sedang Diedit
                  </span>
                </div>
              </div>
            </div>

            <!-- Patient Name Conflict Warning Banner -->
            <transition name="slide-fade">
              <div v-if="hasPatientNameConflict && patientNameConflicts.length > 0" class="mb-6 bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-lg shadow-md">
                <div class="flex items-start">
                  <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-yellow-600 text-xl"></i>
                  </div>
                  <div class="ml-3 flex-1">
                    <h3 class="text-sm font-semibold text-yellow-800 mb-2">
                      ⚠️ Peringatan: Ada dokter lain yang sedang menginput nama pasien yang sama
                    </h3>
                    <div class="space-y-1">
                      <div 
                        v-for="(conflict, index) in patientNameConflicts" 
                        :key="index"
                        class="text-sm text-yellow-700 flex items-center"
                      >
                        <i class="fas fa-user-md mr-2"></i>
                        <span>{{ conflict.message }}</span>
                      </div>
                    </div>
                    <p class="text-xs text-yellow-600 mt-2 italic">
                      Mohon koordinasi dengan dokter tersebut untuk menghindari duplikasi data.
                    </p>
                  </div>
                </div>
              </div>
            </transition>

            <!-- Active Editors Table (Multiple Doctors) -->
            <div v-if="activeEditors.length > 0" class="mb-6 overflow-hidden rounded-2xl shadow-lg border-2 border-orange-300">
              <!-- Table Header -->
              <div class="bg-gradient-to-r from-orange-500 to-orange-600 px-6 py-4">
                <div class="flex items-center justify-between">
                  <div class="flex items-center">
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center mr-4">
                      <i class="fas fa-users-cog text-white text-2xl"></i>
                    </div>
                    <div>
                      <h3 class="text-xl font-bold text-white flex items-center">
                        <span class="mr-2">⚠️</span>
                        Dokter Lain Sedang Mengedit
                      </h3>
                      <p class="text-orange-100 text-sm mt-1">
                        {{ activeEditors.length }} dokter sedang mengakses data pasien yang sama
                      </p>
                    </div>
                  </div>
                  <div class="flex items-center space-x-2">
                    <span class="relative flex h-4 w-4">
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
                      <span class="relative inline-flex rounded-full h-4 w-4 bg-white"></span>
                    </span>
                    <span class="text-white text-xs font-bold">LIVE</span>
                  </div>
                </div>
              </div>

              <!-- Table Content -->
              <div class="bg-white">
                <table class="min-w-full divide-y divide-gray-200">
                  <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                    <tr>
                      <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                        <i class="fas fa-hashtag mr-2 text-gray-500"></i>
                        No
                      </th>
                      <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                        <i class="fas fa-user-md mr-2 text-blue-500"></i>
                        Nama Dokter
                      </th>
                      <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                        <i class="fas fa-clock mr-2 text-orange-500"></i>
                        Mulai Mengedit
                      </th>
                      <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                        <i class="fas fa-hourglass-half mr-2 text-purple-500"></i>
                        Durasi
                      </th>
                      <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                        <i class="fas fa-info-circle mr-2 text-green-500"></i>
                        Status
                      </th>
                    </tr>
                  </thead>
                  <tbody class="bg-white divide-y divide-gray-100">
                    <tr 
                      v-for="(editor, index) in activeEditors" 
                      :key="editor.user_id"
                      class="hover:bg-orange-50 transition-colors duration-200"
                      :class="{
                        'bg-red-50': index === 0,
                        'bg-yellow-50': index === 1,
                        'bg-orange-50': index >= 2
                      }"
                    >
                      <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gradient-to-br from-orange-400 to-orange-600 text-white font-bold text-sm shadow-md">
                          {{ index + 1 }}
                        </span>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                          <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center shadow-md">
                            <i class="fas fa-user-md text-white"></i>
                          </div>
                          <div class="ml-4">
                            <div class="text-sm font-bold text-gray-900">{{ editor.user_name }}</div>
                            <div class="text-xs text-gray-500">ID: {{ editor.user_id }}</div>
                          </div>
                        </div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                          <i class="fas fa-calendar-alt text-orange-500 mr-2"></i>
                          <span class="text-sm text-gray-900 font-mono">{{ formatDateTime(editor.started_at) }}</span>
                        </div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                          <i class="fas fa-stopwatch text-purple-500 mr-2"></i>
                          <span class="text-sm font-semibold text-purple-700">{{ getEditDuration(editor.started_at) }}</span>
                        </div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-gradient-to-r from-green-400 to-green-500 text-white shadow-sm animate-pulse">
                          <span class="relative flex h-2 w-2 mr-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-white"></span>
                          </span>
                          Aktif Mengedit
                        </span>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>

              <!-- Table Footer with Warning -->
              <div class="bg-gradient-to-r from-yellow-50 to-orange-50 px-6 py-4 border-t-2 border-orange-200">
                <div class="flex items-center justify-between">
                  <div class="flex items-center text-sm text-orange-800">
                    <i class="fas fa-exclamation-triangle mr-2 text-orange-600"></i>
                    <span class="font-medium">
                      Hindari konflik data dengan menunggu dokter lain selesai atau koordinasi terlebih dahulu
                    </span>
                  </div>
                  <div class="text-xs text-orange-600 font-mono">
                    <i class="fas fa-sync-alt mr-1"></i>
                    Update setiap 3 detik
                  </div>
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
                  <p class="mt-1 text-sm text-gray-900 bg-white px-3 py-2 border rounded-md">
                    {{ props.kunjungan?.penjamin || '-' }}
                    <span v-if="props.kunjungan?.eselon?.grp_eselon_id" class="text-xs text-blue-600 ml-2">
                      (ID: {{ props.kunjungan.eselon.grp_eselon_id }})
                    </span>
                  </p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">Jenis Kunjungan</label>
                  <p class="mt-1 text-sm text-gray-900 bg-white px-3 py-2 border rounded-md">{{ props.kunjungan?.kunjungan || '-' }}</p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">Alamat</label>
                  <p class="mt-1 text-sm text-gray-900 bg-white px-3 py-2 border rounded-md">{{ props.kunjungan?.almt_B || '-' }}</p>
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
                        <div>
                          <label class="block text-sm font-medium text-gray-700">ICD Code</label>
                          <input 
                            type="text" 
                            v-model="konsul.icd"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Masukkan kode ICD"
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
                        <div>
                          <label class="block text-sm font-medium text-gray-700">ICD Code</label>
                          <input 
                            type="text" 
                            v-model="tindak.icd"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Masukkan kode ICD"
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
                    <div class="flex items-center flex-1 min-w-0">
                      <span class="text-sm font-medium text-gray-700 whitespace-nowrap">Konsultasi</span>
                      <span v-if="form.konsul.length === 0" class="ml-2 text-xs text-gray-500 whitespace-nowrap">(kosong)</span>
                      <span v-else class="ml-2 text-xs text-green-600 whitespace-nowrap">({{ form.konsul.length }} item)</span>
                    </div>
                    <span class="text-sm font-semibold text-gray-900 whitespace-nowrap ml-4">Rp {{ formatCurrency(calculateKonsulTotal()) }}</span>
                  </div>
                  
                  <div class="flex justify-between items-center py-2 border-b border-gray-200">
                    <div class="flex items-center flex-1 min-w-0">
                      <span class="text-sm font-medium text-gray-700 whitespace-nowrap">Tindakan</span>
                      <span v-if="form.tindak.length === 0" class="ml-2 text-xs text-gray-500 whitespace-nowrap">(kosong)</span>
                      <span v-else class="ml-2 text-xs text-green-600 whitespace-nowrap">({{ form.tindak.length }} item)</span>
                    </div>
                    <span class="text-sm font-semibold text-gray-900 whitespace-nowrap ml-4">Rp {{ formatCurrency(calculateTindakTotal()) }}</span>
                  </div>
                  
                  <div class="flex justify-between items-center py-2 border-b border-gray-200">
                    <div class="flex items-center flex-1 min-w-0">
                      <span class="text-sm font-medium text-gray-700 whitespace-nowrap">Alat Kesehatan</span>
                      <span v-if="form.alkes.length === 0" class="ml-2 text-xs text-gray-500 whitespace-nowrap">(kosong)</span>
                      <span v-else class="ml-2 text-xs text-green-600 whitespace-nowrap">({{ form.alkes.length }} item)</span>
                    </div>
                    <span class="text-sm font-semibold text-gray-900 whitespace-nowrap ml-4">Rp {{ formatCurrency(calculateAlkesTotal()) }}</span>
                  </div>
                  
                  <div class="flex justify-between items-center py-2 border-b border-gray-200">
                    <div class="flex items-center flex-1 min-w-0">
                      <span class="text-sm font-medium text-gray-700 whitespace-nowrap">Resep</span>
                      <span v-if="form.rsp.length === 0" class="ml-2 text-xs text-gray-500 whitespace-nowrap">(kosong)</span>
                      <span v-else class="ml-2 text-xs text-green-600 whitespace-nowrap">({{ form.rsp.length }} item)</span>
                    </div>
                    <span class="text-sm font-semibold text-gray-900 whitespace-nowrap ml-4">Rp {{ formatCurrency(calculateRspTotal()) }}</span>
                  </div>
                  
                  <div class="flex justify-between items-center py-2 border-b border-gray-200">
                    <div class="flex items-center flex-1 min-w-0">
                      <span class="text-sm font-medium text-gray-700 whitespace-nowrap">Lainnya</span>
                      <span v-if="form.lainnya.length === 0" class="ml-2 text-xs text-gray-500 whitespace-nowrap">(kosong)</span>
                      <span v-else class="ml-2 text-xs text-green-600 whitespace-nowrap">({{ form.lainnya.length }} item)</span>
                    </div>
                    <span class="text-sm font-semibold text-gray-900 whitespace-nowrap ml-4">Rp {{ formatCurrency(calculateLainnyaTotal()) }}</span>
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
    <div v-if="optimisticLockModal" class="fixed z-50 inset-0 flex items-center justify-center bg-black/50 backdrop-blur-sm">
      <div class="bg-white rounded-2xl shadow-2xl max-w-3xl w-full mx-4 overflow-hidden animate-fade-in">
        <!-- Header -->
        <div class="bg-gradient-to-r from-red-500 to-red-600 px-8 py-6">
          <div class="flex items-center">
            <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center mr-5 shadow-lg">
              <i class="fas fa-exclamation-triangle text-4xl text-white"></i>
            </div>
            <div class="flex-1">
              <h2 class="text-3xl font-bold text-white flex items-center">
                <span class="mr-3">⚠️</span>
                Konflik Data Terdeteksi
              </h2>
              <p class="text-red-50 text-sm mt-2 font-medium">
                Data telah dimodifikasi oleh pengguna lain saat Anda mengedit
              </p>
            </div>
          </div>
        </div>

        <!-- Content -->
        <div class="p-8">
          <!-- Main Alert Banner -->
          <div class="bg-gradient-to-r from-red-50 to-orange-50 border-l-4 border-red-500 p-5 mb-6 rounded-r-xl shadow-sm">
            <div class="flex items-start">
              <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-red-600 text-2xl mt-1"></i>
              </div>
              <div class="ml-4 flex-1">
                <h3 class="text-base font-bold text-red-900 mb-2">
                  Perubahan Data Terdeteksi
                </h3>
                <p class="text-sm text-red-800 leading-relaxed">
                  Data transaksi pasien ini telah <span class="font-bold">diubah oleh pengguna lain</span> 
                  saat Anda sedang mengedit. Untuk mencegah kehilangan data dan konflik, 
                  Anda <span class="font-bold">harus memuat ulang halaman</span> untuk mendapatkan versi terbaru.
                </p>
              </div>
            </div>
          </div>

          <!-- Beautiful Information Table -->
          <div class="bg-white border-2 border-gray-200 rounded-2xl overflow-hidden shadow-lg mb-6">
            <!-- Table Header -->
            <div class="bg-gradient-to-r from-gray-100 to-gray-50 px-6 py-4 border-b-2 border-gray-200">
              <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider flex items-center">
                <i class="fas fa-table mr-3 text-blue-600 text-lg"></i>
                Detail Informasi Konflik
              </h3>
            </div>
            
            <!-- Table Body -->
            <div class="divide-y divide-gray-100">
              <!-- Modified By Row -->
              <div v-if="conflictInfo.last_modified_by" class="grid grid-cols-5 gap-6 px-6 py-4 hover:bg-blue-50 transition-all duration-200">
                <div class="col-span-2 flex items-center">
                  <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center mr-3">
                    <i class="fas fa-user-edit text-blue-600"></i>
                  </div>
                  <span class="text-sm font-semibold text-gray-700">Diubah Oleh</span>
                </div>
                <div class="col-span-3 flex items-center">
                  <span class="inline-flex items-center px-4 py-2 bg-red-100 text-red-700 text-sm font-bold rounded-full border-2 border-red-300 shadow-sm">
                    <i class="fas fa-user-circle mr-2"></i>
                    {{ conflictInfo.last_modified_by }}
                  </span>
                </div>
              </div>

              <!-- Modified Time Row -->
              <div v-if="conflictInfo.last_modified_at" class="grid grid-cols-5 gap-6 px-6 py-4 hover:bg-orange-50 transition-all duration-200">
                <div class="col-span-2 flex items-center">
                  <div class="w-10 h-10 bg-orange-100 rounded-xl flex items-center justify-center mr-3">
                    <i class="fas fa-clock text-orange-600"></i>
                  </div>
                  <span class="text-sm font-semibold text-gray-700">Waktu Perubahan</span>
                </div>
                <div class="col-span-3 flex items-center">
                  <span class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-900 text-sm font-mono rounded-lg border border-gray-300">
                    <i class="far fa-calendar-alt mr-2 text-orange-600"></i>
                    {{ conflictInfo.last_modified_at }}
                  </span>
                </div>
              </div>

              <!-- Server Version Row -->
              <div v-if="conflictInfo.current_version" class="grid grid-cols-5 gap-6 px-6 py-4 hover:bg-green-50 transition-all duration-200">
                <div class="col-span-2 flex items-center">
                  <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center mr-3">
                    <i class="fas fa-code-branch text-green-600"></i>
                  </div>
                  <span class="text-sm font-semibold text-gray-700">Versi Server</span>
                </div>
                <div class="col-span-3 flex items-center space-x-3">
                  <span class="inline-flex items-center px-4 py-2 bg-green-100 text-green-700 text-sm font-bold rounded-full border-2 border-green-400 shadow-sm">
                    <i class="fas fa-server mr-2"></i>
                    v{{ conflictInfo.current_version }}
                  </span>
                  <span class="inline-flex items-center px-3 py-1 bg-green-500 text-white text-xs font-bold rounded-full">
                    <i class="fas fa-check-circle mr-1"></i>
                    Terbaru
                  </span>
                </div>
              </div>

              <!-- Your Version Row -->
              <div v-if="conflictInfo.expected_version" class="grid grid-cols-5 gap-6 px-6 py-4 hover:bg-red-50 transition-all duration-200">
                <div class="col-span-2 flex items-center">
                  <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center mr-3">
                    <i class="fas fa-code-branch text-red-600"></i>
                  </div>
                  <span class="text-sm font-semibold text-gray-700">Versi Anda</span>
                </div>
                <div class="col-span-3 flex items-center space-x-3">
                  <span class="inline-flex items-center px-4 py-2 bg-red-100 text-red-700 text-sm font-bold rounded-full border-2 border-red-400 shadow-sm">
                    <i class="fas fa-laptop mr-2"></i>
                    v{{ conflictInfo.expected_version }}
                  </span>
                  <span class="inline-flex items-center px-3 py-1 bg-red-500 text-white text-xs font-bold rounded-full">
                    <i class="fas fa-times-circle mr-1"></i>
                    Kedaluwarsa
                  </span>
                </div>
              </div>

              <!-- Last Saved Row -->
              <div v-if="conflictInfo.current_updated_at" class="grid grid-cols-5 gap-6 px-6 py-4 hover:bg-purple-50 transition-all duration-200">
                <div class="col-span-2 flex items-center">
                  <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center mr-3">
                    <i class="fas fa-save text-purple-600"></i>
                  </div>
                  <span class="text-sm font-semibold text-gray-700">Terakhir Disimpan</span>
                </div>
                <div class="col-span-3 flex items-center">
                  <span class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-900 text-sm font-mono rounded-lg border border-gray-300">
                    <i class="far fa-clock mr-2 text-purple-600"></i>
                    {{ formatDateTime(conflictInfo.current_updated_at) }}
                  </span>
                </div>
              </div>
            </div>
          </div>

          <!-- Error Code Information Box -->
          <div class="bg-gradient-to-r from-yellow-50 to-amber-50 border-2 border-yellow-400 rounded-xl p-5 shadow-sm">
            <div class="flex items-start">
              <div class="flex-shrink-0">
                <i class="fas fa-code text-yellow-600 text-2xl mt-1"></i>
              </div>
              <div class="ml-4 flex-1">
                <p class="text-sm font-mono font-bold text-yellow-900 mb-2">
                  <span class="inline-flex items-center px-2 py-1 bg-yellow-200 rounded text-xs mr-2">
                    HTTP 409
                  </span>
                  OptimisticLockingException
                </p>
                <p class="text-xs text-yellow-800 leading-relaxed">
                  Data telah diperbarui oleh pengguna lain. Silakan muat ulang untuk versi terbaru.
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Footer Actions -->
        <div class="bg-gradient-to-r from-gray-100 to-gray-50 px-8 py-5 flex justify-between items-center border-t-2 border-gray-200">
          <div class="flex items-center text-sm text-gray-600">
            <i class="fas fa-shield-alt text-blue-600 mr-2"></i>
            <span class="font-medium">Proteksi Optimistic Locking Aktif</span>
          </div>
          <button 
            @click="reloadPage" 
            class="group px-8 py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-bold rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl flex items-center transform hover:scale-105 active:scale-95"
          >
            <i class="fas fa-sync-alt mr-3 group-hover:animate-spin"></i>
            <span>Muat Ulang Halaman</span>
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
                  <div v-if="patientPenjamin && patientGrpEselonId" class="mt-2">
                    <span class="inline-flex items-center px-2 py-1 rounded-md bg-white/20 text-xs font-medium text-white">
                      <i class="fas fa-filter mr-1"></i>
                      Disesuaikan dengan penjamin: {{ patientPenjamin }} (ID: {{ patientGrpEselonId }})
                    </span>
                  </div>
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
            <!-- Filter Info -->
            <div v-if="patientPenjamin && patientGrpEselonId" class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
              <div class="flex items-center text-sm text-blue-700">
                <i class="fas fa-info-circle mr-2"></i>
                <span>
                  Menampilkan tindakan tarif yang sesuai dengan penjamin pasien: 
                  <strong>{{ patientPenjamin }}</strong> (ID: {{ patientGrpEselonId }})
                </span>
              </div>
            </div>
            
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
            <!-- Loading indicator -->
            <div v-if="loadingTindakanTarifs && tindakanTarifs.length === 0" class="p-8 text-center text-gray-500">
              <i class="fas fa-spinner fa-spin text-4xl mb-4"></i>
              <p>Memuat tindakan tarif...</p>
            </div>
            
            <!-- Empty state -->
            <div v-else-if="filteredTindakanTarifs.length === 0" class="p-8 text-center text-gray-500">
              <i class="fas fa-search text-4xl mb-4"></i>
              <p v-if="patientPenjamin && patientGrpEselonId && !searchTindakanTarif">
                Tidak ada tindakan tarif yang sesuai dengan penjamin 
                <strong>{{ patientPenjamin }}</strong> (ID: {{ patientGrpEselonId }})
              </p>
              <p v-else-if="patientPenjamin && patientGrpEselonId && searchTindakanTarif">
                Tidak ada tindakan tarif yang ditemukan untuk penjamin 
                <strong>{{ patientPenjamin }}</strong> (ID: {{ patientGrpEselonId }}) dengan kata kunci 
                <strong>"{{ searchTindakanTarif }}"</strong>
              </p>
              <p v-else>
                Tidak ada tindakan tarif yang ditemukan
              </p>
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
                        <span 
                          :class="{
                            'font-medium text-blue-600 bg-blue-100 px-2 py-1 rounded': 
                              tarifMatchesPenjamin(tarif)
                          }"
                        >
                          {{ tarif.grp_eselon?.grp_eselon_desc || 'Grup tidak tersedia' }}
                          <span class="text-xs text-gray-500 ml-1">
                            (ID: {{ tarif.grp_eselon?.id || 'N/A' }})
                          </span>
                          <span 
                            v-if="tarifMatchesPenjamin(tarif)"
                            class="ml-1 text-xs text-blue-600 font-bold"
                          >
                            (Cocok)
                          </span>
                        </span>
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
              
              <!-- Load More Button -->
              <div v-if="hasMoreTindakanTarifs && !searchTindakanTarif" class="p-4 text-center">
                <button
                  @click="loadTindakanTarifs(false)"
                  :disabled="loadingTindakanTarifs"
                  class="px-4 py-2 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded-lg transition-colors disabled:opacity-50"
                >
                  <i v-if="loadingTindakanTarifs" class="fas fa-spinner fa-spin mr-2"></i>
                  {{ loadingTindakanTarifs ? 'Memuat...' : 'Muat Lebih Banyak' }}
                </button>
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
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import { useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link } from '@inertiajs/vue3'

// Editing Lock State
const isLockedByOther = ref(false)
const lockInfo = ref({
  locked_by: null,
  locked_since: null
})
const activeEditors = ref([]) // Array of all active editors
let lockCheckInterval = null

// Patient Name Conflict State
const patientNameConflicts = ref([])
const hasPatientNameConflict = ref(false)
const isCheckingPatientName = ref(false)
let patientNameCheckTimeout = null
let patientNameTrackingInterval = null

// Calculate edit duration
const getEditDuration = (startedAt) => {
  if (!startedAt) return '0 detik'
  
  const start = new Date(startedAt)
  const now = new Date()
  const diffMs = now - start
  const diffSec = Math.floor(diffMs / 1000)
  const diffMin = Math.floor(diffSec / 60)
  const diffHour = Math.floor(diffMin / 60)
  
  if (diffHour > 0) {
    return `${diffHour} jam ${diffMin % 60} menit`
  } else if (diffMin > 0) {
    return `${diffMin} menit ${diffSec % 60} detik`
  } else {
    return `${diffSec} detik`
  }
}

// Check edit lock status
const checkEditLock = async () => {
  if (!kunjunganId.value) return
  
  try {
    const response = await fetch(`/transaksi/check-edit-lock/${kunjunganId.value}`)
    const data = await response.json()
    
    if (data.is_locked) {
      isLockedByOther.value = true
      lockInfo.value = {
        locked_by: data.locked_by,
        locked_since: data.locked_since
      }
    } else {
      isLockedByOther.value = false
      lockInfo.value = {
        locked_by: null,
        locked_since: null
      }
    }
    
    // Update active editors list (all editors except current user)
    if (data.active_editors && Array.isArray(data.active_editors)) {
      activeEditors.value = data.active_editors
    } else {
      activeEditors.value = []
    }
  } catch (error) {
    console.error('Error checking edit lock:', error)
  }
}

// Release edit lock when leaving page
const releaseEditLock = async () => {
  if (!kunjunganId.value) return
  
  try {
    await fetch('/transaksi/release-edit-lock', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: JSON.stringify({
        kunjungan_id: kunjunganId.value
      })
    })
  } catch (error) {
    console.error('Error releasing edit lock:', error)
  }
}

// Get CSRF token
function getCsrfToken() {
  return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
}

// Track that user is inputting a patient name
async function trackPatientNameInputting() {
  if (!form.nm_p || form.nm_p.trim() === '') return
  
  try {
    const response = await fetch('/transaksi/track-patient-name-inputting', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': getCsrfToken(),
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: JSON.stringify({
        nm_p: form.nm_p
      })
    })
    
    if (!response.ok) {
      if (response.status === 419) {
        console.warn('CSRF token mismatch or expired. This might be due to session timeout.')
        // Optionally reload the page to get a fresh CSRF token
        // window.location.reload()
      } else {
        console.error(`HTTP error! status: ${response.status}`)
      }
    }
  } catch (error) {
    console.error('Error tracking patient name input:', error)
  }
}

// Check for patient name conflicts
async function checkPatientNameConflict() {
  if (!form.nm_p || form.nm_p.trim() === '') {
    patientNameConflicts.value = []
    hasPatientNameConflict.value = false
    return
  }
  
  isCheckingPatientName.value = true
  try {
    const response = await fetch('/transaksi/check-patient-name-conflict', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': getCsrfToken(),
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: JSON.stringify({
        nm_p: form.nm_p
      })
    })
    
    if (!response.ok) {
      if (response.status === 419) {
        console.warn('CSRF token mismatch or expired in checkPatientNameConflict. This might be due to session timeout.')
      } else {
        console.error(`HTTP error in checkPatientNameConflict! status: ${response.status}`)
      }
      return
    }
    
    const data = await response.json()
    hasPatientNameConflict.value = data.has_conflict || false
    patientNameConflicts.value = data.conflicts || []
  } catch (error) {
    console.error('Error checking patient name conflict:', error)
  } finally {
    isCheckingPatientName.value = false
  }
}

// Stop tracking patient name
async function stopTrackingPatientName() {
  try {
    const response = await fetch('/transaksi/stop-tracking-patient-name', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': getCsrfToken(),
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: JSON.stringify({
        nm_p: form.nm_p
      })
    })
    
    if (!response.ok) {
      if (response.status === 419) {
        console.warn('CSRF token mismatch or expired in stopTrackingPatientName. This might be due to session timeout.')
      } else {
        console.error(`HTTP error in stopTrackingPatientName! status: ${response.status}`)
      }
    }
  } catch (error) {
    console.error('Error stopping patient name tracking:', error)
  }
}

// Debounce helper for patient name conflict check
function debouncePatientNameCheck(func, wait) {
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(patientNameCheckTimeout)
      func(...args)
    }
    clearTimeout(patientNameCheckTimeout)
    patientNameCheckTimeout = setTimeout(later, wait)
  }
}

// Debounced check patient name conflict
const debouncedCheckPatientNameConflict = debouncePatientNameCheck(checkPatientNameConflict, 800)

// Optimistic Locking Modal State
const optimisticLockModal = ref(false)
const conflictInfo = ref({
  last_modified_by: null,
  last_modified_at: null,
  current_version: null,
  expected_version: null,
  current_updated_at: null
})

function reloadPage() {
  window.location.reload()
}

// Global error handler for JSON responses
const handleJsonError = (error) => {
  console.error('JSON Error received:', error)
  
  // Extract conflict data if available
  if (error?.conflict_data) {
    conflictInfo.value = {
      last_modified_by: error.conflict_data.last_modified_by || null,
      last_modified_at: error.conflict_data.last_modified_at || null,
      current_version: error.conflict_data.current_version || null,
      expected_version: error.conflict_data.expected_version || null,
      current_updated_at: error.conflict_data.current_updated_at || null
    }
  }
  
  // Also check errors object for current_updated_at
  if (error?.errors?.current_updated_at) {
    conflictInfo.value.current_updated_at = error.errors.current_updated_at
  }
  
  // Extract from top-level error properties if available
  if (error?.current_updated_at) {
    conflictInfo.value.current_updated_at = error.current_updated_at
  }
  
  // Check if it's a conflict error
  if (error && (
    error.message?.toLowerCase().includes('conflict') ||
    error.message?.toLowerCase().includes('modified by another user') ||
    error.message?.toLowerCase().includes('dimodifikasi oleh') ||
    error.message?.toLowerCase().includes('data has been modified') ||
    error.errors?.conflict ||
    error.error === 'optimistic_locking_conflict'
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
  auth: Object
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

// Lazy loaded data
const tindakanTarifs = ref([])
const farmalkes = ref([])
const loadingTindakanTarifs = ref(false)
const loadingFarmalkes = ref(false)
const tindakanTarifsPage = ref(1)
const farmalkesPage = ref(1)
const hasMoreTindakanTarifs = ref(true)
const hasMoreFarmalkes = ref(true)

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

// Get patient's penjamin from kunjungan data
const patientPenjamin = computed(() => {
  return props.kunjungan?.penjamin || ''
})

// Get patient's grp_eselon_id from kunjungan data
const patientGrpEselonId = computed(() => {
  return props.kunjungan?.eselon?.grp_eselon_id || null
})

// Helper function to check if tarif matches patient's grp_eselon_id
const tarifMatchesPenjamin = (tarif) => {
  if (!patientGrpEselonId.value) return false
  
  // Direct ID match - this is the primary filter
  return tarif.grp_eselon?.id === patientGrpEselonId.value
}

// Helper function to check if farmalkes matches patient's penjamin
const farmalkesMatchesPenjamin = (farmalkesItem) => {
  if (!patientPenjamin.value) return true // If no penjamin, show all
  
  // For now, return true for all items (can be customized based on farmalkes structure)
  // If farmalkes has penjamin/grp_eselon_id field, you can add filtering logic here
  return true
}

// Filtered tindakan tarifs based on search and patient's grp_eselon_id
const filteredTindakanTarifs = computed(() => {
  let filteredTarifs = tindakanTarifs.value || []
  
  // Filter by patient's grp_eselon_id first (if available)
  if (patientGrpEselonId.value) {
    filteredTarifs = filteredTarifs.filter(tarif => tarifMatchesPenjamin(tarif))
  }
  
  // Then apply search filter if search query exists
  if (searchTindakanTarif.value) {
    const searchLower = searchTindakanTarif.value.toLowerCase()
    filteredTarifs = filteredTarifs.filter(tarif => 
      tarif.tindakan_q?.tindakan_q_desc?.toLowerCase().includes(searchLower) ||
      tarif.grp_eselon?.grp_eselon_desc?.toLowerCase().includes(searchLower) ||
      tarif.tarif?.toString().includes(searchTindakanTarif.value)
    )
  }
  
  return filteredTarifs
})

// Filtered farmalkes based on search
const filteredFarmalkes = computed(() => {
  if (!farmalkes.value || !Array.isArray(farmalkes.value)) {
    return []
  }
  
  let filteredItems = [...farmalkes.value]
  
  // Filter by patient's penjamin first (if available)
  if (patientPenjamin.value) {
    filteredItems = filteredItems.filter(item => farmalkesMatchesPenjamin(item))
  }
  
  // Then apply search filter if search query exists
  if (searchFarmalkes.value && searchFarmalkes.value.trim() !== '') {
    const searchLower = searchFarmalkes.value.toLowerCase()
    filteredItems = filteredItems.filter(item => 
      item?.nama_item?.toLowerCase().includes(searchLower) ||
      item?.satuan?.toLowerCase().includes(searchLower) ||
      item?.kategori?.toLowerCase().includes(searchLower) ||
      item?.jenis?.toLowerCase().includes(searchLower) ||
      item?.harga?.toString().includes(searchFarmalkes.value)
    )
  }
  
  return filteredItems
})

// API functions for loading modal data
const loadTindakanTarifs = async (reset = false) => {
  if (loadingTindakanTarifs.value) return
  
  loadingTindakanTarifs.value = true
  
  try {
    const params = new URLSearchParams({
      page: reset ? 1 : tindakanTarifsPage.value,
      per_page: 20,
      grp_eselon_id: patientGrpEselonId.value || ''
    })
    
    const response = await fetch(`/api/modal/tindakan-tarifs?${params}`)
    const data = await response.json()
    
    if (reset) {
      tindakanTarifs.value = data.data
      tindakanTarifsPage.value = 1
    } else {
      tindakanTarifs.value = [...tindakanTarifs.value, ...data.data]
    }
    
    hasMoreTindakanTarifs.value = data.pagination.has_more
    if (hasMoreTindakanTarifs.value) {
      tindakanTarifsPage.value++
    }
  } catch (error) {
    console.error('Error loading tindakan tarifs:', error)
  } finally {
    loadingTindakanTarifs.value = false
  }
}

const loadFarmalkes = async (reset = false) => {
  if (loadingFarmalkes.value) return
  
  loadingFarmalkes.value = true
  
  try {
    const params = new URLSearchParams({
      page: reset ? 1 : farmalkesPage.value,
      per_page: 20
    })
    
    // Add jenis filter based on patient penjamin
    if (patientPenjamin.value?.toLowerCase().includes('bpjs')) {
      params.append('jenis', 'farmasi')
    } else if (patientPenjamin.value?.toLowerCase().includes('asuransi')) {
      params.append('jenis', 'alkes')
    }
    
    const response = await fetch(`/api/modal/farmalkes?${params}`)
    const data = await response.json()
    
    if (reset) {
      farmalkes.value = data.data
      farmalkesPage.value = 1
    } else {
      farmalkes.value = [...farmalkes.value, ...data.data]
    }
    
    hasMoreFarmalkes.value = data.pagination.has_more
    if (hasMoreFarmalkes.value) {
      farmalkesPage.value++
    }
  } catch (error) {
    console.error('Error loading farmalkes:', error)
  } finally {
    loadingFarmalkes.value = false
  }
}

// Modal functions
const openTindakanTarifModal = async (itemIndex, itemType) => {
  currentItemIndex.value = itemIndex
  currentItemType.value = itemType
  
  // Load data if not already loaded
  if (tindakanTarifs.value.length === 0) {
    await loadTindakanTarifs(true)
  }
  
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
const openFarmalkesModal = async (itemIndex, itemType) => {
  currentFarmalkesItemIndex.value = itemIndex
  currentFarmalkesItemType.value = itemType
  
  // Load data if not already loaded
  if (farmalkes.value.length === 0) {
    await loadFarmalkes(true)
  }
  
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
  grp_eselon_id: props.kunjungan?.eselon?.grp_eselon_id || null,
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
    form.grp_eselon_id = props.kunjungan.eselon?.grp_eselon_id || null
    form.no_sjp = props.kunjungan.no_sjp || ''
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
        tindakan_tarif_id: konsul.tindakan_tarif_id || null,
        icd: konsul.detail_transaksi?.icd || ''
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
        tindakan_tarif_id: tindak.tindakan_tarif_id || null,
        icd: tindak.detail_transaksi?.icd || ''
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
  
  // Start checking for edit locks every 3 seconds
  checkEditLock() // Check immediately
  lockCheckInterval = setInterval(checkEditLock, 3000) // Then check every 3 seconds
  
  // Track patient name inputting every 30 seconds while user is typing
  if (form.nm_p && form.nm_p.trim() !== '') {
    trackPatientNameInputting()
    checkPatientNameConflict()
  }
  patientNameTrackingInterval = setInterval(() => {
    if (form.nm_p && form.nm_p.trim() !== '') {
      trackPatientNameInputting()
    }
  }, 30000)
  
  // Watch for changes in nm_p
watch(() => form.nm_p, () => {
  if (form.nm_p && form.nm_p.trim() !== '') {
    trackPatientNameInputting()
    debouncedCheckPatientNameConflict()
  } else {
    patientNameConflicts.value = []
    hasPatientNameConflict.value = false
  }
})

// Listen for page unload to release lock
  window.addEventListener('beforeunload', releaseEditLock)
  window.addEventListener('beforeunload', stopTrackingPatientName)
})

// Cleanup when component unmounts
onUnmounted(() => {
  // Clear intervals
  if (lockCheckInterval) {
    clearInterval(lockCheckInterval)
  }
  if (patientNameCheckTimeout) {
    clearTimeout(patientNameCheckTimeout)
  }
  if (patientNameTrackingInterval) {
    clearInterval(patientNameTrackingInterval)
  }
  
  // Release edit lock
  releaseEditLock()
  
  // Stop tracking patient name
  stopTrackingPatientName()
  
  // Remove event listeners
  window.removeEventListener('beforeunload', releaseEditLock)
  window.removeEventListener('beforeunload', stopTrackingPatientName)
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
    tindakan_tarif_id: null,
    icd: ''
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
    tindakan_tarif_id: null,
    icd: ''
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

// Format date and time
const formatDateTime = (dateTime) => {
  if (!dateTime) return '-'
  const date = new Date(dateTime)
  return date.toLocaleDateString('id-ID', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
    second: '2-digit'
  })
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
const submit = async () => {
  // Prevent accidental submissions
  if (form.processing) {
    console.log('Form is already processing, ignoring submit')
    return
  }
  
  // Stop tracking patient name before submit
  await stopTrackingPatientName()
  
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
        
        // Extract conflict data from errors
        if (errors?.conflict_data) {
          conflictInfo.value = errors.conflict_data
        }
        
        // Try to handle as JSON error first
        if (handleJsonError(errors)) {
          return
        }
        
        // Handle conflict/optimistic locking errors
        if (errors && (
          errors.conflict || 
          errors.conflict_data ||
          errors.response?.status === 409 ||
          (typeof errors?.error === 'string' && errors.error.toLowerCase().includes('conflict')) ||
          (typeof errors?.error === 'string' && errors.error.toLowerCase().includes('optimisticlockingexception')) ||
          (typeof errors?.error === 'string' && errors.error.toLowerCase().includes('dimodifikasi oleh'))
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
        
        // Extract conflict data from errors
        if (errors?.conflict_data) {
          conflictInfo.value = errors.conflict_data
        }
        
        // Try to handle as JSON error first
        if (handleJsonError(errors)) {
          return
        }
        
        // Handle conflict/optimistic locking errors
        if (errors && (
          errors.conflict || 
          errors.conflict_data ||
          errors.response?.status === 409 ||
          (typeof errors?.error === 'string' && errors.error.toLowerCase().includes('conflict')) ||
          (typeof errors?.error === 'string' && errors.error.toLowerCase().includes('optimisticlockingexception')) ||
          (typeof errors?.error === 'string' && errors.error.toLowerCase().includes('dimodifikasi oleh'))
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

<style scoped>
/* Slide fade animation for warning banner */
.slide-fade-enter-active {
  transition: all 0.3s ease-out;
}

.slide-fade-leave-active {
  transition: all 0.3s ease-in;
}

.slide-fade-enter-from {
  transform: translateY(-10px);
  opacity: 0;
}

.slide-fade-leave-to {
  transform: translateY(-10px);
  opacity: 0;
}
</style>