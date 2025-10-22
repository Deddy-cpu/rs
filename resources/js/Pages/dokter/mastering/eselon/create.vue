<script setup>
import { ref, computed } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

// Props
const props = defineProps({
  grpEselons: {
    type: Array,
    required: true
  },
  auth: {
    type: Object,
    required: true
  }
})

// Ambil nama user dari props.auth.user.name, fallback ke username/email jika tidak ada
const userName = computed(() => {
  if (props.auth && props.auth.user) {
    return props.auth.user.name || props.auth.user.username || props.auth.user.email || ''
  }
  return ''
})

const form = useForm({
  grp_eselon_id: '',
  eselon_desc: '',
  aktif: 'Y',
  update_date: new Date().toISOString().split('T')[0],
  update_by: userName.value,
})

const submitForm = () => {
  // Pastikan update_by selalu terisi user yang login saat submit
  form.update_by = userName.value
  form.post(route('eselon.store'), {
    onSuccess: () => {
      // Redirect will be handled by the controller
    }
  })
}
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Tambah Eselon" />

    <div class="min-h-screen bg-cover bg-center p-6" style="background-image: url('/images/bg-login.png')">
      <!-- Header -->
      <div class="text-center mb-8">
        <h1 class="text-4xl md:text-5xl font-bold bg-gradient-to-r from-purple-600 to-violet-600 bg-clip-text text-transparent mb-2">
          <span class="inline-block align-middle mr-2">💜</span>
          Tambah Eselon
        </h1>
        <p class="text-gray-600 text-lg">Tambahkan data eselon baru ke sistem</p>
      </div>

      <!-- Form Container -->
      <div class="max-w-4xl mx-auto">
        <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
          <!-- Form Header -->
          <div class="bg-gradient-to-r from-purple-500 to-violet-500 px-8 py-6 text-white">
            <div class="flex items-center gap-4">
              <div class="w-12 h-12 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 4v16m8-8H4" />
                </svg>
              </div>
              <div>
                <h2 class="text-2xl font-bold">Form Tambah Eselon</h2>
                <p class="text-purple-100">Lengkapi informasi eselon di bawah ini</p>
              </div>
            </div>
          </div>

          <!-- Form Content -->
          <div class="p-8">
            <form @submit.prevent="submitForm" class="space-y-8">
              <!-- Grup Eselon -->
              <div class="space-y-2">
                <label for="grp_eselon_id" class="block text-sm font-semibold text-gray-700">
                  <svg class="w-4 h-4 inline mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 7h18M3 12h18m-9 5h9" />
                  </svg>
                  Grup Eselon *
                </label>
                <select
                  id="grp_eselon_id"
                  v-model="form.grp_eselon_id"
                  required
                  class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-gray-50 focus:bg-white text-lg transition-all duration-200"
                  :class="{ 'border-red-500 focus:ring-red-500': form.errors.grp_eselon_id }"
                >
                  <option value="">Pilih Grup Eselon</option>
                  <option v-for="grpEselon in grpEselons" :key="grpEselon.id" :value="grpEselon.id">
                    {{ grpEselon.grp_eselon_desc }}
                  </option>
                </select>
                <div v-if="form.errors.grp_eselon_id" class="text-red-600 text-sm mt-1 flex items-center">
                  <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  {{ form.errors.grp_eselon_id }}
                </div>
              </div>

              <!-- Deskripsi Eselon -->
              <div class="space-y-2">
                <label for="eselon_desc" class="block text-sm font-semibold text-gray-700">
                  <svg class="w-4 h-4 inline mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 12h6m-6 4h6M7 8h10M5 4h14" />
                  </svg>
                  Deskripsi Eselon *
                </label>
                <input
                  id="eselon_desc"
                  v-model="form.eselon_desc"
                  type="text"
                  required
                  placeholder="Masukkan deskripsi eselon"
                  class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-gray-50 focus:bg-white text-lg transition-all duration-200"
                  :class="{ 'border-red-500 focus:ring-red-500': form.errors.eselon_desc }"
                />
                <div v-if="form.errors.eselon_desc" class="text-red-600 text-sm mt-1 flex items-center">
                  <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  {{ form.errors.eselon_desc }}
                </div>
              </div>

              <!-- Status -->
              <div class="space-y-2">
                <label class="block text-sm font-semibold text-gray-700">
                  <svg class="w-4 h-4 inline mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  Status *
                </label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <label class="relative cursor-pointer">
                    <input v-model="form.aktif" type="radio" value="Y" class="sr-only" />
                    <div class="flex items-center p-4 border-2 rounded-xl transition-all duration-200"
                      :class="form.aktif === 'Y'
                        ? 'border-green-500 bg-green-50 text-green-700'
                        : 'border-gray-200 bg-gray-50 text-gray-600 hover:border-gray-300'">
                      <div class="flex items-center justify-center w-6 h-6 rounded-full border-2 mr-3"
                        :class="form.aktif === 'Y' ? 'border-green-500 bg-green-500' : 'border-gray-300'">
                        <div v-if="form.aktif === 'Y'" class="w-2 h-2 rounded-full bg-white"></div>
                      </div>
                      <div>
                        <div class="font-semibold">Aktif</div>
                        <div class="text-sm">Data dapat digunakan</div>
                      </div>
                    </div>
                  </label>

                  <label class="relative cursor-pointer">
                    <input v-model="form.aktif" type="radio" value="N" class="sr-only" />
                    <div class="flex items-center p-4 border-2 rounded-xl transition-all duration-200"
                      :class="form.aktif === 'N'
                        ? 'border-red-500 bg-red-50 text-red-700'
                        : 'border-gray-200 bg-gray-50 text-gray-600 hover:border-gray-300'">
                      <div class="flex items-center justify-center w-6 h-6 rounded-full border-2 mr-3"
                        :class="form.aktif === 'N' ? 'border-red-500 bg-red-500' : 'border-gray-300'">
                        <div v-if="form.aktif === 'N'" class="w-2 h-2 rounded-full bg-white"></div>
                      </div>
                      <div>
                        <div class="font-semibold">Tidak Aktif</div>
                        <div class="text-sm">Data tidak dapat digunakan</div>
                      </div>
                    </div>
                  </label>
                </div>
              </div>

              <!-- Update By -->
              <div class="space-y-2">
                <label for="update_by" class="block text-sm font-semibold text-gray-700">
                  <svg class="w-4 h-4 inline mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                  </svg>
                  Diupdate Oleh
                </label>
                <input
                  id="update_by"
                  v-model="form.update_by"
                  type="text"
                  placeholder="Nama pengguna yang mengupdate..."
                  class="w-full px-4 py-3 border border-gray-300 rounded-xl bg-gray-50 focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200 text-lg"
                />
                <p class="text-sm text-gray-500">Otomatis terisi dengan nama pengguna saat ini</p>
              </div>

              <!-- Tombol Aksi -->
              <div class="flex flex-col sm:flex-row gap-4 pt-6 border-t border-gray-200">
                <Link
                  :href="route('eselon.index')"
                  class="flex-1 px-6 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition-all duration-200 font-semibold flex items-center justify-center gap-2"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M6 18L18 6M6 6l12 12" />
                  </svg>
                  Batal
                </Link>

                <button
                  type="submit"
                  :disabled="form.processing"
                  class="flex-1 px-6 py-3 bg-gradient-to-r from-purple-600 to-violet-600 text-white rounded-xl hover:from-purple-700 hover:to-violet-700 transition-all duration-200 font-semibold flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  <svg v-if="form.processing" class="w-5 h-5 animate-spin" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                  </svg>
                  <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M5 13l4 4L19 7" />
                  </svg>
                  <span v-if="form.processing">Menyimpan...</span>
                  <span v-else>Simpan</span>
                </button>
              </div>
            </form>
          </div>
        </div>

        <!-- Help Section -->
        <div class="mt-8 bg-purple-50/80 backdrop-blur-sm rounded-xl p-6 border border-purple-200">
          <div class="flex items-start gap-4">
            <div class="w-10 h-10 bg-purple-500 rounded-full flex items-center justify-center flex-shrink-0">
              <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <div>
              <h3 class="text-lg font-semibold text-purple-800 mb-2">Tips Pengisian Form</h3>
              <ul class="text-purple-700 space-y-1 text-sm">
                <li>• <strong>Grup Eselon:</strong> Pilih grup eselon yang sesuai</li>
                <li>• <strong>Deskripsi:</strong> Gunakan nama yang jelas dan spesifik</li>
                <li>• <strong>Status:</strong> Pilih “Aktif” jika digunakan</li>
                <li>• <strong>Update By:</strong> Otomatis diisi nama pengguna</li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>



