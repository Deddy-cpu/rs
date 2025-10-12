<script setup>
import { ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link, router } from '@inertiajs/vue3';
import { useAuth } from '@/composables/useAuth';

const showingNavigationDropdown = ref(false);
const sidebarOpen = ref(false);
const sidebarCollapsed = ref(false);
const sidebarHovered = ref(false);

// Use auth composable
const { isAdmin, isDokter, isKasir, isPendaftaran } = useAuth();

const toggleSidebar = () => {
    sidebarCollapsed.value = !sidebarCollapsed.value;
};

const closeSidebar = () => {
    sidebarOpen.value = false;
};
</script>

<template>
  <div class="min-h-screen bg-cover bg-center bg-no-repeat" style="background-image: url('/images/bg-login.png')">
    <!-- Mobile sidebar overlay -->
    <div
      v-if="sidebarOpen"
      class="fixed inset-0 z-40 lg:hidden"
      @click="closeSidebar"
    >
      <div class="fixed inset-0 bg-gray-600 bg-opacity-75 transition-opacity"></div>
    </div>

        <!-- Sidebar -->
        <div 
            :class="[
                'fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-lg transform transition-all duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0',
                sidebarOpen ? 'translate-x-0' : '-translate-x-full',
                sidebarCollapsed ? 'lg:w-16' : 'lg:w-64'
            ]"
        >
            <!-- Sidebar Header -->
            <div class="flex items-center justify-between h-16 px-4 bg-gradient-to-r from-red-600 to-pink-600">
                <div v-if="!sidebarCollapsed" class="flex items-center">
                    <Link :href="route('dashboard')" class="flex items-center">
                        <ApplicationLogo class="h-8 w-auto fill-current text-white" />
                        <span class="ml-2 text-white font-bold text-lg">RS Ubud Medik</span>
                    </Link>
                </div>
                <div v-else class="flex justify-center w-full">
                                <Link :href="route('dashboard')">
                        <ApplicationLogo class="h-8 w-auto fill-current text-white" />
                                </Link>
                            </div>

        <!-- Toggle button (desktop only) -->
        <button
          v-if="!sidebarCollapsed"
          @click="toggleSidebar"
          class="hidden lg:block p-1 rounded-md text-white hover:bg-red-700 transition-colors"
        >
          <i class="fas fa-chevron-left text-sm"></i>
        </button>
      </div>

            <!-- Sidebar Navigation -->
            <nav class="mt-5 px-2 space-y-1 overflow-y-auto h-full pb-20">
                <!-- Dashboard -->
                <Link
                                    :href="route('dashboard')"
                    :class="[
                        'group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-colors duration-150',
                        route().current('dashboard') 
                            ? 'bg-red-100 text-red-700 border-r-2 border-red-600' 
                            : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
                    ]"
                >
                    <i class="fas fa-tachometer-alt mr-3 text-lg flex-shrink-0"></i>
                    <span v-if="!sidebarCollapsed">Dashboard</span>
                </Link>
                                
                                <!-- Admin-only links -->
                                <template v-if="isAdmin">
                    <!-- Admin Dashboard -->
                    <Link
                                        :href="route('admin.dashboard')"
                        :class="[
                            'group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-colors duration-150',
                            route().current('admin.dashboard') 
                                ? 'bg-red-100 text-red-700 border-r-2 border-red-600' 
                                : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
                        ]"
                    >
                        <i class="fas fa-cogs mr-3 text-lg flex-shrink-0"></i>
                        <span v-if="!sidebarCollapsed">Admin Dashboard</span>
                    </Link>

                    <!-- User Management -->
                    <Link
                                        :href="route('users.index')"
                        :class="[
                            'group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-colors duration-150',
                            route().current('users.*') 
                                ? 'bg-red-100 text-red-700 border-r-2 border-red-600' 
                                : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
                        ]"
                    >
                        <i class="fas fa-users mr-3 text-lg flex-shrink-0"></i>
                        <span v-if="!sidebarCollapsed">User Management</span>
                    </Link>

                    <!-- Tindakan Medis -->
                    <Link
                                        :href="route('tindakan.index')"
                        :class="[
                            'group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-colors duration-150',
                            route().current('tindakan.*') 
                                ? 'bg-red-100 text-red-700 border-r-2 border-red-600' 
                                : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
                        ]"
                    >
                        <i class="fas fa-procedures mr-3 text-lg flex-shrink-0"></i>
                        <span v-if="!sidebarCollapsed">Tindakan Medis</span>
                    </Link>

                    <!-- Resep Management -->
                    <Link
                                        :href="route('resep.index')"
                        :class="[
                            'group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-colors duration-150',
                            route().current('resep.*') 
                                ? 'bg-red-100 text-red-700 border-r-2 border-red-600' 
                                : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
                        ]"
                    >
                        <i class="fas fa-prescription-bottle-alt mr-3 text-lg flex-shrink-0"></i>
                        <span v-if="!sidebarCollapsed">Resep Management</span>
                    </Link>

                    <!-- Kasir -->
                    <Link
                                        :href="route('kasir.index')"
                        :class="[
                            'group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-colors duration-150',
                            route().current('kasir.*') 
                                ? 'bg-red-100 text-red-700 border-r-2 border-red-600' 
                                : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
                        ]"
                    >
                        <i class="fas fa-cash-register mr-3 text-lg flex-shrink-0"></i>
                        <span v-if="!sidebarCollapsed">Kasir</span>
                    </Link>

                    <!-- Dokter -->
                    <Link
                                        :href="route('dokter.index')"
                        :class="[
                            'group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-colors duration-150',
                            route().current('dokter.*') 
                                ? 'bg-red-100 text-red-700 border-r-2 border-red-600' 
                                : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
                        ]"
                    >
                        <i class="fas fa-user-md mr-3 text-lg flex-shrink-0"></i>
                        <span v-if="!sidebarCollapsed">Dokter</span>
                    </Link>

                    <!-- Pasien -->
                    <Link
                                        :href="route('pasien.index')"
                        :class="[
                            'group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-colors duration-150',
                            route().current('pasien.*') 
                                ? 'bg-red-100 text-red-700 border-r-2 border-red-600' 
                                : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
                        ]"
                    >
                        <i class="fas fa-user-injured mr-3 text-lg flex-shrink-0"></i>
                        <span v-if="!sidebarCollapsed">Pasien</span>
                    </Link>

                    <!-- Pasien Kunjungan -->
                    <Link
                        :href="route('kunjungan.index')"
                        :class="[
                            'group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-colors duration-150',
                            route().current('kunjungan.*') 
                                ? 'bg-red-100 text-red-700 border-r-2 border-red-600' 
                                : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
                        ]"
                    >
                        <i class="fas fa-calendar-check mr-3 text-lg flex-shrink-0"></i>
                        <span v-if="!sidebarCollapsed">Pasien Kunjungan</span>
                    </Link>
                                </template>

                                <!-- Kasir and Pendaftaran links -->
                                <template v-else-if="isKasir || isPendaftaran">
                    <Link
                                        :href="route('kasir.index')"
                        :class="[
                            'group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-colors duration-150',
                            route().current('kasir.*') 
                                ? 'bg-red-100 text-red-700 border-r-2 border-red-600' 
                                : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
                        ]"
                    >
                        <i class="fas fa-cash-register mr-3 text-lg flex-shrink-0"></i>
                        <span v-if="!sidebarCollapsed">Kasir</span>
                    </Link>

                    <Link
                                        :href="route('pasien.index')"
                        :class="[
                            'group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-colors duration-150',
                            route().current('pasien.*') 
                                ? 'bg-red-100 text-red-700 border-r-2 border-red-600' 
                                : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
                        ]"
                    >
                        <i class="fas fa-user-injured mr-3 text-lg flex-shrink-0"></i>
                        <span v-if="!sidebarCollapsed">Pasien</span>
                    </Link>
                </template>
            </nav>

            <!-- Sidebar Footer -->
            <div class="absolute bottom-0 w-full p-4 bg-white border-t border-gray-200">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="h-8 w-8 rounded-full bg-red-600 flex items-center justify-center">
                            <span class="text-white text-sm font-bold">
                                {{ $page.props.auth.user.name.charAt(0).toUpperCase() }}
                            </span>
                        </div>
                    </div>
                    <div v-if="!sidebarCollapsed" class="ml-3 flex-1">
                        <p class="text-sm font-medium text-gray-700">{{ $page.props.auth.user.name }}</p>
                        <p class="text-xs text-gray-500">{{ $page.props.auth.user.role }}</p>
                    </div>
                </div>
            </div>
        </div>

    <!-- Main content area -->
    <div :class="['transition-all duration-300', sidebarCollapsed ? 'lg:ml-16' : 'lg:ml-64']">
      <!-- Top bar -->
      <div class="sticky top-0 z-30 flex h-16 shrink-0 items-center gap-x-4 border-b border-gray-200 bg-white px-4 shadow-sm sm:gap-x-6 sm:px-6 lg:px-8">
        <!-- Mobile menu button -->
        <button
          @click="sidebarOpen = !sidebarOpen"
          class="lg:hidden p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 transition-colors"
        >
          <i class="fas fa-bars text-lg"></i>
        </button>

        <!-- Separator -->
        <div class="h-6 w-px bg-gray-200 lg:hidden"></div>

        <!-- Page title and breadcrumb -->
        <div class="flex flex-1 gap-x-4 self-stretch lg:gap-x-6">
          <div class="flex flex-1 items-center">
            <h1 class="text-xl font-semibold text-gray-900">
              {{ $page.props.title || 'Dashboard' }}
            </h1>
          </div>
        </div>

        <!-- User Account Menu -->
        <div class="flex items-center gap-x-4 lg:gap-x-6">
          <!-- User Account Dropdown -->
          <div class="relative">
            <Dropdown align="right" width="48">
              <template #trigger>
                <button class="flex items-center text-sm font-medium text-gray-700 hover:text-gray-900 focus:outline-none focus:text-gray-900 transition duration-150 ease-in-out">
                  <div class="flex items-center">
                    <div class="flex-shrink-0">
                      <div class="h-8 w-8 rounded-full bg-gradient-to-r from-red-600 to-pink-600 flex items-center justify-center">
                        <span class="text-white font-semibold text-sm">
                          {{ $page.props.auth.user.name.charAt(0).toUpperCase() }}
                        </span>
                      </div>
                    </div>
                    <div class="ml-3 hidden lg:block">
                      <div class="text-left">
                        <div class="text-sm font-medium text-gray-700">{{ $page.props.auth.user.name }}</div>
                        <div class="text-xs text-gray-500">{{ $page.props.auth.user.role }}</div>
                      </div>
                    </div>
                    <div class="ml-2">
                      <i class="fas fa-chevron-down text-xs text-gray-400"></i>
                    </div>
                  </div>
                </button>
              </template>

              <template #content>
                <div class="py-1">
                  <!-- User Info Header -->
                  <div class="px-4 py-3 border-b border-gray-100">
                    <div class="text-sm font-medium text-gray-900">{{ $page.props.auth.user.name }}</div>
                    <div class="text-xs text-gray-500">{{ $page.props.auth.user.email }}</div>
                    <div class="text-xs text-gray-500">{{ $page.props.auth.user.role }}</div>
                  </div>

                  <!-- Profile Link -->
                  <DropdownLink :href="route('profile.edit')" class="flex items-center">
                    <i class="fas fa-user mr-3 text-gray-400"></i>
                    Profile
                  </DropdownLink>

                  <!-- Settings Link -->
                  <DropdownLink :href="route('profile.edit')" class="flex items-center">
                    <i class="fas fa-cog mr-3 text-gray-400"></i>
                    Settings
                  </DropdownLink>

                  <!-- Divider -->
                  <div class="border-t border-gray-100"></div>

                  <!-- Logout -->
                  <button 
                    @click="logout" 
                    class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900 transition duration-150 ease-in-out"
                  >
                    <i class="fas fa-sign-out-alt mr-3 text-gray-400"></i>
                    Log Out
                  </button>
                </div>
              </template>
            </Dropdown>
          </div>
        </div>

        <!-- Responsive Navigation Menu -->
        <div
          :class="{
            block: showingNavigationDropdown,
            hidden: !showingNavigationDropdown,
          }"
          class="sm:hidden"
        >
          <div class="space-y-1 pb-3 pt-2">
            <ResponsiveNavLink
              :href="route('dashboard')"
              :active="route().current('dashboard')"
            >
              Dashboard
            </ResponsiveNavLink>

            <!-- Admin-only responsive links -->
            <template v-if="isAdmin">
              <ResponsiveNavLink
                :href="route('admin.dashboard')"
                :active="route().current('admin.dashboard')"
              >
                Admin Dashboard
              </ResponsiveNavLink>
              <ResponsiveNavLink
                :href="route('users.index')"
                :active="route().current('users.index')"
              >
                User Management
              </ResponsiveNavLink>
              <ResponsiveNavLink
                :href="route('kasir.index')"
                :active="route().current('kasir.index')"
              >
                Kasir
              </ResponsiveNavLink>
              <ResponsiveNavLink
                :href="route('dokter.index')"
                :active="route().current('dokter.index')"
              >
                Dokter
              </ResponsiveNavLink>
              <ResponsiveNavLink
                :href="route('pasien.index')"
                :active="route().current('pasien.index')"
              >
                Pasien
              </ResponsiveNavLink>
              <ResponsiveNavLink
                :href="route('dokter.pasien-kunjungan')"
                :active="route().current('dokter.pasien-kunjungan')"
              >
                Pasien Kunjungan
              </ResponsiveNavLink>
            </template>

            <!-- Dokter-only responsive links (non-admin dokter) -->
            <template v-else-if="isDokter">
              <ResponsiveNavLink
                :href="route('dokter.dashboard')"
                :active="route().current('dokter.dashboard')"
              >
                Dashboard Dokter
              </ResponsiveNavLink>
              <ResponsiveNavLink
                :href="route('dokter.pasien-kunjungan')"
                :active="route().current('dokter.pasien-kunjungan')"
              >
                Pasien Kunjungan
              </ResponsiveNavLink>
            </template>

            <!-- Pendaftaran responsive links -->
            <template v-else-if="isPendaftaran">
              <ResponsiveNavLink
                :href="route('pendaftaran.dashboard')"
                :active="route().current('pendaftaran.dashboard')"
              >
                Dashboard Pendaftaran
              </ResponsiveNavLink>
              <ResponsiveNavLink
                :href="route('pasien.index')"
                :active="route().current('pasien.index')"
              >
                Daftar Pasien
              </ResponsiveNavLink>
         
            </template>

            <!-- Kasir responsive links -->
            <template v-else-if="isKasir">
              <ResponsiveNavLink
                :href="route('kasir.dashboard')"
                :active="route().current('kasir.dashboard')"
              >
                Dashboard Kasir
              </ResponsiveNavLink>
              
            </template>
          </div>
        </div>
      </div>

      <!-- Page content -->
      <main class="py-6 min-h-screen">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
          <slot />
        </div>
      </main>
    </div>
  </div>
</template>

<style scoped>
/* Custom scrollbar for sidebar */
nav::-webkit-scrollbar {
    width: 4px;
}

nav::-webkit-scrollbar-track {
    background: #f1f5f9;
}

nav::-webkit-scrollbar-thumb {
    background: #e2e8f0;
    border-radius: 2px;
}

nav::-webkit-scrollbar-thumb:hover {
    background: #cbd5e1;
}

/* Smooth transitions */
* {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Enhanced hover animations for sidebar */
.sidebar-hover-expand {
    transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Smooth text fade-in animation */
.sidebar-text {
    transition: opacity 0.2s ease-in-out, transform 0.2s ease-in-out;
    transform: translateX(0);
}

.sidebar-text.hidden {
    opacity: 0;
    transform: translateX(-10px);
}

.sidebar-text.visible {
    opacity: 1;
    transform: translateX(0);
}

/* Hover effects for sidebar items */
.group:hover .fas {
    transform: scale(1.1);
}

/* Ensure sidebar is properly positioned */
.fixed {
    position: fixed;
}

/* Sidebar collapsed state improvements */
.lg\:w-16 {
    width: 4rem !important;
}

.lg\:w-64 {
    width: 16rem !important;
}

/* Ensure proper spacing for collapsed sidebar */
.lg\:ml-16 {
    margin-left: 4rem !important;
}

.lg\:ml-64 {
    margin-left: 16rem !important;
}

/* Smooth sidebar transitions */
.transition-all {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 300ms;
}

/* Mobile responsiveness */
@media (max-width: 1024px) {
    .lg\:ml-64 {
        margin-left: 0 !important;
    }
    .lg\:ml-16 {
        margin-left: 0 !important;
    }
}

/* Ensure proper z-index stacking */
.z-50 {
    z-index: 50;
}

.z-40 {
    z-index: 40;
}

.z-30 {
    z-index: 30;
}
</style>