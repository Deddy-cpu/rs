<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

// Props
const props = defineProps({
  tindakanQ: {
    type: Object,
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
  tindakan_q_desc: '',
  aktif: 'Y',
  update_date: new Date().toISOString().split('T')[0],
  update_by: '',
})

// Initialize form data when props are available
const initializeForm = () => {
  if (props.tindakanQ) {
    form.tindakan_q_desc = props.tindakanQ.tindakan_q_desc || ''
    form.aktif = props.tindakanQ.aktif || 'Y'
    form.update_date = props.tindakanQ.update_date || new Date().toISOString().split('T')[0]
    form.update_by = props.tindakanQ.update_by || userName.value
  }
}

// Initialize form when component mounts or props change
onMounted(() => {
  initializeForm()
})

watch(() => props.tindakanQ, () => {
  initializeForm()
}, { immediate: true })

const submitForm = () => {
  // Pastikan update_by selalu terisi user yang login saat submit
  form.update_by = userName.value
  
  // Check if tindakanQ exists and has an id
  if (!props.tindakanQ || !props.tindakanQ.id) {
    console.error('TindakanQ data is missing or invalid')
    return
  }
  
  form.put(route('tindakanq.update', { tindakanq: props.tindakanQ.id }), {
    onSuccess: () => {
      // Redirect will be handled by the controller
    }
  })
}
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Edit Tindakan Q" />
    
    <div class="min-h-screen bg-gray-50 py-6">
      <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
          <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
              <div class="flex items-center space-x-3">
                <Link
                  :href="route('tindakanq.index')"
                  class="p-2 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors"
                >
                  <i class="fas fa-arrow-left text-gray-600"></i>
                </Link>
                <div class="p-2 bg-indigo-100 rounded-lg">
                  <i class="fas fa-edit text-indigo-600 text-xl"></i>
                </div>
                <div>
                  <h1 class="text-2xl font-bold text-gray-900">Edit Tindakan Q</h1>
                  <p class="text-sm text-gray-600">Ubah data tindakan medis</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Form -->
          <form @submit.prevent="submitForm" class="p-6">
            <div class="space-y-6">
              <!-- Deskripsi Tindakan Q -->
              <div>
                <label for="tindakan_q_desc" class="block text-sm font-medium text-gray-700 mb-2">
                  Deskripsi Tindakan Q <span class="text-red-500">*</span>
                </label>
                <input
                  id="tindakan_q_desc"
                  v-model="form.tindakan_q_desc"
                  type="text"
                  required
                  class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                  placeholder="Masukkan deskripsi tindakan medis"
                />
                <div v-if="form.errors.tindakan_q_desc" class="mt-1 text-sm text-red-600">
                  {{ form.errors.tindakan_q_desc }}
                </div>
              </div>

              <!-- Status Aktif -->
              <div>
                <label for="aktif" class="block text-sm font-medium text-gray-700 mb-2">
                  Status <span class="text-red-500">*</span>
                </label>
                <select
                  id="aktif"
                  v-model="form.aktif"
                  required
                  class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                >
                  <option value="Y">Aktif</option>
                  <option value="N">Tidak Aktif</option>
                </select>
                <div v-if="form.errors.aktif" class="mt-1 text-sm text-red-600">
                  {{ form.errors.aktif }}
                </div>
              </div>

              <!-- Tanggal Update -->
              <div>
                <label for="update_date" class="block text-sm font-medium text-gray-700 mb-2">
                  Tanggal Update <span class="text-red-500">*</span>
                </label>
                <input
                  id="update_date"
                  v-model="form.update_date"
                  type="date"
                  required
                  class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                />
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
                :href="route('tindakanq.index')"
                class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
              >
                <i class="fas fa-times mr-2"></i>
                Batal
              </Link>
              <button
                type="submit"
                :disabled="form.processing || !props.tindakanQ || !props.tindakanQ.id"
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
