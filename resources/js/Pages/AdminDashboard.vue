<script setup>
import { useAuth } from '@/composables/useAuth';
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Link } from '@inertiajs/vue3';


const { user } = useAuth();

// Mock stats data - in real app, this would come from API
const stats = {
  totalUsers: 12,
  activeUsers: 10,
  doctors: 3,
  cashiers: 2
};
</script>


<template>
  <AuthenticatedLayout>
  <div class="min-h-screen bg-cover bg-center p-6" style="background-image: url('/images/bg-login.png')">
    <div class="bg-white/70 backdrop-blur-sm rounded-xl p-6 shadow-lg">
      
      <!-- Header -->
      <div class="mb-6 text-center">
        <h1 class="text-3xl font-extrabold text-blue-700 tracking-wide flex items-center gap-2 justify-center">
          🛡️ Admin Dashboard
        </h1>
        <p class="text-gray-600 mt-2">Welcome, {{ user.name }}! You have administrative privileges.</p>
      </div>

      <!-- Admin Stats -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-blue-50 rounded-lg p-6 border border-blue-200">
          <div class="flex items-center">
            <div class="p-3 bg-blue-500 rounded-full">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
              </svg>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-600">Total Users</p>
              <p class="text-2xl font-semibold text-gray-900">{{ stats.totalUsers }}</p>
            </div>
          </div>
        </div>

        <div class="bg-green-50 rounded-lg p-6 border border-green-200">
          <div class="flex items-center">
            <div class="p-3 bg-green-500 rounded-full">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-600">Active Users</p>
              <p class="text-2xl font-semibold text-gray-900">{{ stats.activeUsers }}</p>
            </div>
          </div>
        </div>

        <div class="bg-yellow-50 rounded-lg p-6 border border-yellow-200">
          <div class="flex items-center">
            <div class="p-3 bg-yellow-500 rounded-full">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
              </svg>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-600">Doctors</p>
              <p class="text-2xl font-semibold text-gray-900">{{ stats.doctors }}</p>
            </div>
          </div>
        </div>

        <div class="bg-purple-50 rounded-lg p-6 border border-purple-200">
          <div class="flex items-center">
            <div class="p-3 bg-purple-500 rounded-full">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
              </svg>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-600">Cashiers</p>
              <p class="text-2xl font-semibold text-gray-900">{{ stats.cashiers }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <Link
            :href="route('users.index')"
            class="flex items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors"
          >
            <div class="p-2 bg-blue-500 rounded-lg mr-3">
              <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
              </svg>
            </div>
            <div>
              <p class="font-medium text-gray-900">Manage Users</p>
              <p class="text-sm text-gray-600">Add, edit, or remove users</p>
            </div>
          </Link>

          <router-link
            to="/tindakan"
            class="flex items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors"
          >
            <div class="p-2 bg-blue-500 rounded-lg mr-3">
              <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
              </svg>
            </div>
            <div>
              <p class="font-medium text-gray-900">Medical Procedures</p>
              <p class="text-sm text-gray-600">Manage medical procedures</p>
            </div>
          </router-link>

          <router-link
            to="/resep"
            class="flex items-center p-4 bg-yellow-50 rounded-lg hover:bg-yellow-100 transition-colors"
          >
            <div class="p-2 bg-yellow-500 rounded-lg mr-3">
              <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
              </svg>
            </div>
            <div>
              <p class="font-medium text-gray-900">Prescriptions</p>
              <p class="text-sm text-gray-600">Manage prescriptions</p>
            </div>
          </Link>
        </div>
      </div>
    </div>
  </div>
  </AuthenticatedLayout>
</template>

