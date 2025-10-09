<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue"
import { Head, useForm, usePage } from "@inertiajs/vue3"
import { ref, computed } from "vue"

const props = defineProps({
  polis: Object
})

const page = usePage()

const form = useForm({
  poli_desc: props.polis?.poli_desc || '',
  aktif: props.polis?.aktif || 'Y',
  update_by: page.props.auth.user?.name || ''
})

const isSubmitting = ref(false)

function submit() {
  isSubmitting.value = true
  form.put(route('polis.update', props.polis.id), {
    onFinish: () => {
      isSubmitting.value = false
    },
    onError: () => {
      isSubmitting.value = false
    }
  })
}

function cancel() {
  form.reset()
  window.history.back()
}

function formatDate(dateString) {
  if (!dateString) return '-'
  const date = new Date(dateString)
  return date.toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Edit Polis Asuransi" />

    <div class="min-h-screen bg-cover bg-center p-6" style="background-image: url('/images/bg-login.png')">
      <!-- Header -->
      <div class="text-center mb-8">
        <h1 class="text-4xl md:text-5xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent mb-2">
          <span class="inline-block align-middle mr-2">🏥</span>
          Edit Polis Asuransi
        </h1>
        <p class="text-gray-600 text-lg">
          Ubah informasi polis asuransi
        </p>
      </div>

      <!-- Form Container -->
      <div class="max-w-4xl mx-auto">
        <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
          <!-- Form Header -->
          <div class="bg-gradient-to-r from-blue-500 to-indigo-500 px-8 py-6 text-white">
            <div class="flex items-center gap-4">
              <div class="w-12 h-12 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5h2m-1 0v14m-7 0a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2.586a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 0012 2H8a2 2 0 00-2 2v14z"></path>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536"></path>
                </svg>
              </div>
              <div>
                <h2 class="text-2xl font-bold text-white">Form Edit Polis</h2>
                <p class="text-blue-100">Ubah informasi polis asuransi</p>
              </div>
            </div>
          </div>

          <!-- Current Info Section -->
          <div class="px-8 py-6 bg-gray-50 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
              <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
              Informasi Saat Ini
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div class="bg-white rounded-lg p-4 border border-gray-200">
                <p class="text-sm text-gray-600 mb-1">ID Polis</p>
                <p class="font-semibold text-gray-800">#{{ polis.id }}</p>
              </div>
              <div class="bg-white rounded-lg p-4 border border-gray-200">
                <p class="text-sm text-gray-600 mb-1">Dibuat</p>
                <p class="font-semibold text-gray-800">{{ formatDate(polis.created_at) }}</p>
              </div>
              <div class="bg-white rounded-lg p-4 border border-gray-200">
                <p class="text-sm text-gray-600 mb-1">Terakhir Update</p>
                <p class="font-semibold text-gray-800">{{ formatDate(polis.update_date) }}</p>
              </div>
            </div>
          </div>

          <!-- Form Content -->
          <div class="p-8">
            <form @submit.prevent="submit" class="space-y-8">
              <!-- Deskripsi Polis -->
              <div class="space-y-2">
                <label for="poli_desc" class="block text-sm font-semibold text-gray-700">
                  <svg class="w-4 h-4 inline mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                  </svg>
                  Deskripsi Polis *
                </label>
                <input
                  id="poli_desc"
                  v-model="form.poli_desc"
                  type="text"
                  placeholder="Masukkan deskripsi polis asuransi..."
                  class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 bg-gray-50 focus:bg-white text-lg"
                  :class="{ 'border-red-500 focus:ring-red-500': form.errors.poli_desc }"
                  required
                />
                <div v-if="form.errors.poli_desc" class="text-red-600 text-sm mt-1 flex items-center">
                  <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                  {{ form.errors.poli_desc }}
                </div>
              </div>

              <!-- Status Aktif -->
              <div class="space-y-2">
                <label for="aktif" class="block text-sm font-semibold text-gray-700">
                  <svg class="w-4 h-4 inline mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                  Status *
                </label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <label class="relative cursor-pointer">
                    <input
                      v-model="form.aktif"
                      type="radio"
                      value="Y"
                      class="sr-only"
                    />
                    <div class="flex items-center p-4 border-2 rounded-xl transition-all duration-200"
                         :class="form.aktif === 'Y' 
                           ? 'border-green-500 bg-green-50 text-green-700' 
                           : 'border-gray-200 bg-gray-50 text-gray-600 hover:border-gray-300'">
                      <div class="flex items-center justify-center w-6 h-6 rounded-full border-2 mr-3"
                           :class="form.aktif === 'Y' 
                             ? 'border-green-500 bg-green-500' 
                             : 'border-gray-300'">
                        <div v-if="form.aktif === 'Y'" class="w-2 h-2 rounded-full bg-white"></div>
                      </div>
                      <div>
                        <div class="font-semibold">Aktif</div>
                        <div class="text-sm">Polis dapat digunakan</div>
                      </div>
                    </div>
                  </label>
                  
                  <label class="relative cursor-pointer">
                    <input
                      v-model="form.aktif"
                      type="radio"
                      value="N"
                      class="sr-only"
                    />
                    <div class="flex items-center p-4 border-2 rounded-xl transition-all duration-200"
                         :class="form.aktif === 'N' 
                           ? 'border-red-500 bg-red-50 text-red-700' 
                           : 'border-gray-200 bg-gray-50 text-gray-600 hover:border-gray-300'">
                      <div class="flex items-center justify-center w-6 h-6 rounded-full border-2 mr-3"
                           :class="form.aktif === 'N' 
                             ? 'border-red-500 bg-red-500' 
                             : 'border-gray-300'">
                        <div v-if="form.aktif === 'N'" class="w-2 h-2 rounded-full bg-white"></div>
                      </div>
                      <div>
                        <div class="font-semibold">Tidak Aktif</div>
                        <div class="text-sm">Polis tidak dapat digunakan</div>
                      </div>
                    </div>
                  </label>
                </div>
                <div v-if="form.errors.aktif" class="text-red-600 text-sm mt-1 flex items-center">
                  <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                  {{ form.errors.aktif }}
                </div>
              </div>

              <!-- Update By -->
              <div class="space-y-2">
                <label for="update_by" class="block text-sm font-semibold text-gray-700">
                  <svg class="w-4 h-4 inline mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                  </svg>
                  Diupdate Oleh
                </label>
                <input
                  id="update_by"
                  v-model="form.update_by"
                  type="text"
                  placeholder="Nama pengguna yang mengupdate..."
                  class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 bg-gray-50 focus:bg-white text-lg"
                  :class="{ 'border-red-500 focus:ring-red-500': form.errors.update_by }"
                />
                <p class="text-sm text-gray-500">Otomatis terisi dengan nama pengguna saat ini</p>
                <div v-if="form.errors.update_by" class="text-red-600 text-sm mt-1 flex items-center">
                  <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                  {{ form.errors.update_by }}
                </div>
              </div>

              <!-- Action Buttons -->
              <div class="flex flex-col sm:flex-row gap-4 pt-6 border-t border-gray-200">
                <button
                  type="button"
                  @click="cancel"
                  class="flex-1 px-6 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition-all duration-200 font-semibold flex items-center justify-center gap-2"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                  </svg>
                  Batal
                </button>
                
                <button
                  type="submit"
                  :disabled="isSubmitting || form.processing"
                  class="flex-1 px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 font-semibold flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  <svg v-if="isSubmitting || form.processing" class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                  </svg>
                  <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                  </svg>
                  <span v-if="isSubmitting || form.processing">Menyimpan...</span>
                  <span v-else>Update Polis</span>
                </button>
              </div>
            </form>
          </div>
        </div>

        <!-- Help Section -->
        <div class="mt-8 bg-blue-50/80 backdrop-blur-sm rounded-xl p-6 border border-blue-200">
          <div class="flex items-start gap-4">
            <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center flex-shrink-0">
              <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            <div>
              <h3 class="text-lg font-semibold text-blue-800 mb-2">Tips Pengeditan</h3>
              <ul class="text-blue-700 space-y-1 text-sm">
                <li>• <strong>Deskripsi Polis:</strong> Pastikan nama tetap jelas dan mudah dipahami</li>
                <li>• <strong>Status:</strong> Ubah status sesuai kebutuhan operasional</li>
                <li>• <strong>Update By:</strong> Otomatis terisi dengan nama pengguna saat ini</li>
                <li>• Semua perubahan akan tercatat dengan timestamp terbaru</li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
