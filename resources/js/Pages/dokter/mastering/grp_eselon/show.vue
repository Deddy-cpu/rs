<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
  grpEselon: {
    type: Object,
    required: true
  }
})

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
    <Head title="Detail GRP Eselon" />
    
    <div class="min-h-screen bg-gray-50 py-6">
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
                  <i class="fas fa-eye text-indigo-600 text-xl"></i>
                </div>
                <div>
                  <h1 class="text-2xl font-bold text-gray-900">Detail GRP Eselon</h1>
                  <p class="text-sm text-gray-600">Informasi lengkap grup eselon</p>
                </div>
              </div>
              <Link
                :href="route('grp-eselon.edit', props.grpEselon.id)"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
              >
                <i class="fas fa-edit mr-2"></i>
                Edit
              </Link>
            </div>
          </div>

          <!-- Content -->
          <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Deskripsi Eselon -->
              <div class="bg-gray-50 rounded-lg p-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Deskripsi Eselon
                </label>
                <p class="text-lg font-semibold text-gray-900">{{ props.grpEselon.eselon_desc }}</p>
              </div>

              <!-- Tanggal Update -->
              <div class="bg-gray-50 rounded-lg p-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Tanggal Update
                </label>
                <p class="text-lg font-semibold text-gray-900">{{ formatDate(props.grpEselon.update_date) }}</p>
              </div>

              <!-- Diupdate Oleh -->
              <div class="bg-gray-50 rounded-lg p-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Diupdate Oleh
                </label>
                <p class="text-lg font-semibold text-gray-900">{{ props.grpEselon.updated_by || '-' }}</p>
              </div>

              <!-- Tanggal Dibuat -->
              <div class="bg-gray-50 rounded-lg p-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Tanggal Dibuat
                </label>
                <p class="text-lg font-semibold text-gray-900">{{ formatDate(props.grpEselon.created_at) }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
