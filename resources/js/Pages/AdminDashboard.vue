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
          <i class="fas fa-shield-alt text-blue-600"></i>
          Admin Dashboard
        </h1>
        <p class="text-gray-600 mt-2">Welcome, {{ user.name }}! You have administrative privileges.</p>
        <div class="mt-4 flex justify-center">
          <div class="bg-blue-100 text-blue-800 px-4 py-2 rounded-full text-sm font-medium">
            <i class="fas fa-crown mr-1"></i>
            Administrator Access
          </div>
        </div>
      </div>

      <!-- Admin Stats -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-blue-50 rounded-lg p-6 border border-blue-200 hover:shadow-md transition-shadow">
          <div class="flex items-center">
            <div class="p-3 bg-blue-500 rounded-full">
              <i class="fas fa-users text-white text-lg"></i>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-600">Total Users</p>
              <p class="text-2xl font-semibold text-gray-900">{{ stats.totalUsers }}</p>
            </div>
          </div>
        </div>

        <div class="bg-green-50 rounded-lg p-6 border border-green-200 hover:shadow-md transition-shadow">
          <div class="flex items-center">
            <div class="p-3 bg-green-500 rounded-full">
              <i class="fas fa-user-check text-white text-lg"></i>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-600">Active Users</p>
              <p class="text-2xl font-semibold text-gray-900">{{ stats.activeUsers }}</p>
            </div>
          </div>
        </div>

        <div class="bg-yellow-50 rounded-lg p-6 border border-yellow-200 hover:shadow-md transition-shadow">
          <div class="flex items-center">
            <div class="p-3 bg-yellow-500 rounded-full">
              <i class="fas fa-user-md text-white text-lg"></i>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-600">Doctors</p>
              <p class="text-2xl font-semibold text-gray-900">{{ stats.doctors }}</p>
            </div>
          </div>
        </div>

        <div class="bg-purple-50 rounded-lg p-6 border border-purple-200 hover:shadow-md transition-shadow">
          <div class="flex items-center">
            <div class="p-3 bg-purple-500 rounded-full">
              <i class="fas fa-cash-register text-white text-lg"></i>
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
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
          <i class="fas fa-bolt mr-2 text-yellow-500"></i>
          Quick Actions
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
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

          <Link
            :href="route('tindakanq.index')"
            class="flex items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors"
          >
            <div class="p-2 bg-green-500 rounded-lg mr-3">
              <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
              </svg>
            </div>
            <div>
              <p class="font-medium text-gray-900">Medical Procedures</p>
              <p class="text-sm text-gray-600">Manage medical procedures</p>
            </div>
          </Link>

          <Link
            :href="route('farmalkes.index')"
            class="flex items-center p-4 bg-yellow-50 rounded-lg hover:bg-yellow-100 transition-colors"
          >
            <div class="p-2 bg-yellow-500 rounded-lg mr-3">
              <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
              </svg>
            </div>
            <div>
              <p class="font-medium text-gray-900">Pharmacy & Medical Equipment</p>
              <p class="text-sm text-gray-600">Manage pharmacy and medical equipment</p>
            </div>
          </Link>

          <Link
            :href="route('kasir.index')"
            class="flex items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors"
          >
            <div class="p-2 bg-purple-500 rounded-lg mr-3">
              <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
              </svg>
            </div>
            <div>
              <p class="font-medium text-gray-900">Cashier Management</p>
              <p class="text-sm text-gray-600">Manage transactions and payments</p>
            </div>
          </Link>
        </div>
      </div>

      <!-- Recent Activity -->
      <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-200 mt-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
          <i class="fas fa-history mr-2 text-indigo-500"></i>
          Recent Activity
        </h3>
        <div class="space-y-4">
          <div class="flex items-center p-3 bg-gray-50 rounded-lg">
            <div class="p-2 bg-blue-100 rounded-full mr-3">
              <i class="fas fa-user-plus text-blue-600 text-sm"></i>
            </div>
            <div class="flex-1">
              <p class="text-sm font-medium text-gray-900">New user registered</p>
              <p class="text-xs text-gray-500">2 hours ago</p>
            </div>
          </div>
          
          <div class="flex items-center p-3 bg-gray-50 rounded-lg">
            <div class="p-2 bg-green-100 rounded-full mr-3">
              <i class="fas fa-stethoscope text-green-600 text-sm"></i>
            </div>
            <div class="flex-1">
              <p class="text-sm font-medium text-gray-900">Doctor appointment completed</p>
              <p class="text-xs text-gray-500">4 hours ago</p>
            </div>
          </div>
          
          <div class="flex items-center p-3 bg-gray-50 rounded-lg">
            <div class="p-2 bg-yellow-100 rounded-full mr-3">
              <i class="fas fa-pills text-yellow-600 text-sm"></i>
            </div>
            <div class="flex-1">
              <p class="text-sm font-medium text-gray-900">Prescription issued</p>
              <p class="text-xs text-gray-500">6 hours ago</p>
            </div>
          </div>
          
          <div class="flex items-center p-3 bg-gray-50 rounded-lg">
            <div class="p-2 bg-purple-100 rounded-full mr-3">
              <i class="fas fa-receipt text-purple-600 text-sm"></i>
            </div>
            <div class="flex-1">
              <p class="text-sm font-medium text-gray-900">Payment processed</p>
              <p class="text-xs text-gray-500">8 hours ago</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  </AuthenticatedLayout>
</template>

