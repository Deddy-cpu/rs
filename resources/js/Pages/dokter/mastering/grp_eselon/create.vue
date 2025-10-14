<script setup>
import { ref, computed } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

// Props untuk mendapatkan data user yang login
const props = defineProps({
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
  grp_eselon_desc: '',
  aktif: 'Y',
  update_date: new Date().toISOString().split('T')[0],
  update_by: userName.value,
})

const submitForm = () => {
  // Pastikan update_by selalu terisi user yang login saat submit
  form.update_by = userName.value
  form.post(route('grp-eselon.store'), {
    onSuccess: () => {
      // Redirect will be handled by the controller
    }
  })
}
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Tambah GRP Eselon" />
    
    <div class="min-h-screen bg-cover bg-center p-6" style="background-image: url('/images/bg-login.png')">        
      <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
          <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
              <div class="flex items-center space-x-3">
                <Link
                  :href="route('grp-eselon.index')"
                  class="p-2 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors"
                >
                  <i class="fas fa-arrow-left text-gray-600"></i>
                </Link>
                <div class="p-2 bg-indigo-100 rounded-lg">
                  <i class="fas fa-plus text-indigo-600 text-xl"></i>
                </div>
                <div>
                  <h1 class="text-2xl font-bold text-gray-900">Tambah GRP Eselon</h1>
                  <p class="text-sm text-gray-600">Buat data grup eselon baru</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Form -->
          <form @submit.prevent="submitForm" class="p-6">
            <div class="space-y-6">
              <!-- Deskripsi Eselon -->
              <div>
                <label for="grp_eselon_desc" class="block text-sm font-medium text-gray-700 mb-2">
                  Deskripsi Grup Eselon <span class="text-red-500">*</span>
                </label>
                <input
                  id="grp_eselon_desc"
                  v-model="form.grp_eselon_desc"
                  type="text"
                  required
                  class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                  placeholder="Masukkan deskripsi grup eselon"
                />
                <div v-if="form.errors.grp_eselon_desc" class="mt-1 text-sm text-red-600">
                  {{ form.errors.grp_eselon_desc }}
                </div>
              </div>

              <!-- Status Aktif -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-3">
                  Status <span class="text-red-500">*</span>
                </label>
                <div class="flex space-x-4">
                  <label class="flex items-center">
                    <input
                      type="radio"
                      v-model="form.aktif"
                      value="Y"
                      class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300"
                    />
                    <span class="ml-2 text-sm text-gray-700 flex items-center">
                      <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                      Aktif
                    </span>
                  </label>
                  <label class="flex items-center">
                    <input
                      type="radio"
                      v-model="form.aktif"
                      value="N"
                      class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300"
                    />
                    <span class="ml-2 text-sm text-gray-700 flex items-center">
                      <div class="w-3 h-3 bg-red-500 rounded-full mr-2"></div>
                      Tidak Aktif
                    </span>
                  </label>
                </div>
                <div v-if="form.errors.aktif" class="mt-1 text-sm text-red-600">
                  {{ form.errors.aktif }}
                </div>
              </div>

              <!-- Tanggal Update -->
              <div>
                <label for="update_date" class="block text-sm font-medium text-gray-700 mb-2">
                  Tanggal Update <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                  <input
                    id="update_date"
                    v-model="form.update_date"
                    type="date"
                    required
                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm bg-blue-50"
                  />
                  <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                    <i class="fas fa-calendar-alt text-blue-500"></i>
                  </div>
                </div>
                <p class="mt-1 text-xs text-blue-600 flex items-center">
                  <i class="fas fa-info-circle mr-1"></i>
                  Tanggal otomatis terisi dengan tanggal hari ini
                </p>
                <div v-if="form.errors.update_date" class="mt-1 text-sm text-red-600">
                  {{ form.errors.update_date }}
                </div>
              </div>

              <!-- Diupdate Oleh -->
              <div>
                <label for="update_by" class="block text-sm font-medium text-gray-700 mb-2">
                  Diupdate Oleh
                </label>
                <input
                  id="update_by"
                  v-model="form.update_by"
                  type="text"
                  class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                  placeholder="Masukkan nama pengupdate"
                  readonly
                />
                <div v-if="form.errors.update_by" class="mt-1 text-sm text-red-600">
                  {{ form.errors.update_by }}
                </div>
              </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end space-x-3 mt-8 pt-6 border-t border-gray-200">
              <Link
                :href="route('grp-eselon.index')"
                class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
              >
                <i class="fas fa-times mr-2"></i>
                Batal
              </Link>
              <button
                type="submit"
                :disabled="form.processing"
                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                <i v-if="form.processing" class="fas fa-spinner fa-spin mr-2"></i>
                <i v-else class="fas fa-save mr-2"></i>
                {{ form.processing ? 'Menyimpan...' : 'Simpan' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
