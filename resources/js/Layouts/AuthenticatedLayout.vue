<script setup>
import { ref, computed, onMounted, watch } from 'vue';
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
const masteringExpanded = ref(true);

// Use auth composable
const { isAdmin, isDokter, isKasir, isPendaftaran, isPerawat } = useAuth();

// Helper to check if user is dokter or perawat (same access)
const isDokterOrPerawat = computed(() => isDokter.value || isPerawat.value);

const toggleSidebar = () => {
  sidebarCollapsed.value = !sidebarCollapsed.value;
};

// Persist sidebar collapsed state for Admin role across reloads.
// We only persist for admin users to avoid affecting other roles' defaults.
onMounted(() => {
  try {
    if (isAdmin.value) {
      const saved = localStorage.getItem('sidebarCollapsedAdmin');
      sidebarCollapsed.value = saved === 'true';
      const savedMastering = localStorage.getItem('masteringExpandedAdmin');
      masteringExpanded.value = savedMastering === null ? true : savedMastering === 'true';
    } else if (isDokter.value) {
      const saved = localStorage.getItem('sidebarCollapsedDokter');
      sidebarCollapsed.value = saved === 'true';
    } else if (isPerawat.value) {
      // Perawat uses same sidebar state key as dokter for consistency
      const saved = localStorage.getItem('sidebarCollapsedPerawat');
      sidebarCollapsed.value = saved === 'true';
    } else if (isKasir.value) {
      const saved = localStorage.getItem('sidebarCollapsedKasir');
      sidebarCollapsed.value = saved === 'true';
    } else if (isPendaftaran.value) {
      const saved = localStorage.getItem('sidebarCollapsedPendaftaran');
      sidebarCollapsed.value = saved === 'true';
    }
  } catch (e) {
    // ignore storage errors (e.g., in private browsing)
    // console.warn(e)
  }
});

// Save changes to localStorage whenever sidebarCollapsed changes (only for admin).
watch(sidebarCollapsed, (val) => {
  try {
    if (isAdmin.value) {
      localStorage.setItem('sidebarCollapsedAdmin', val ? 'true' : 'false');
    } else if (isDokter.value) {
      localStorage.setItem('sidebarCollapsedDokter', val ? 'true' : 'false');
    } else if (isPerawat.value) {
      localStorage.setItem('sidebarCollapsedPerawat', val ? 'true' : 'false');
    } else if (isKasir.value) {
      localStorage.setItem('sidebarCollapsedKasir', val ? 'true' : 'false');
    } else if (isPendaftaran.value) {
      localStorage.setItem('sidebarCollapsedPendaftaran', val ? 'true' : 'false');
    }
  } catch (e) {
    // ignore storage errors
  }
});

// Persist Mastering dropdown state for Admin role across reloads
watch(masteringExpanded, (val) => {
  try {
    if (isAdmin.value) {
      localStorage.setItem('masteringExpandedAdmin', val ? 'true' : 'false');
    }
  } catch (e) {
    // ignore storage errors
  }
});


const closeSidebar = () => {
    sidebarOpen.value = false;
};

const logout = () => {
    router.post(route('logout'));
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
        'fixed inset-y-0 left-0 z-50 bg-white shadow-lg transform transition-all duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0',
        sidebarOpen ? 'translate-x-0 w-64' : '-translate-x-full w-64',
        sidebarCollapsed ? 'lg:w-16' : 'lg:w-64'
      ]"
      style="display: flex; flex-direction: column;"
    >
      <!-- Sidebar Header -->
      <div class="relative flex items-center h-16 bg-gradient-to-r from-red-600 to-pink-600" :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-4'">
        <!-- Logo and Title (when expanded) -->
        <div v-if="!sidebarCollapsed" class="flex items-center">
          <Link :href="route('dashboard')" class="flex items-center">
            <ApplicationLogo class="h-8 w-auto fill-current text-white" />
            <span class="ml-2 text-white font-bold text-lg">RS Ubud Medik</span>
          </Link>
        </div>
        <!-- Logo only (when collapsed) -->
        <div v-else class="flex items-center cursor-pointer hover:opacity-80 transition-opacity" @click="toggleSidebar">
          <ApplicationLogo class="h-8 w-auto fill-current text-white" />
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
      <nav class="mt-5 px-2 space-y-1 overflow-y-auto flex-1 pb-4" style="max-height: calc(100vh - 64px);">
        <!-- Dashboard - Hide for admin, show for other roles -->
        <template v-if="!isAdmin">
          <NavLink
            :href="route('dashboard')"
            :active="route().current('dashboard')"
            :class="[
              'flex items-center py-2 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition-colors duration-150',
              sidebarCollapsed ? 'px-2 justify-center' : 'px-4'
            ]"
          >
            <i class="fas fa-home text-gray-600" :class="sidebarCollapsed ? '' : 'mr-2'"></i>
            <span v-if="!sidebarCollapsed">Dashboard</span>
          </NavLink>
        </template>

        <!-- Admin Dashboard - Only for admin -->
        <template v-if="isAdmin">
          <NavLink
            :href="route('admin.dashboard')"
            :active="route().current('admin.dashboard')"
            :class="[
              'flex items-center py-2 rounded-lg text-sm font-semibold text-gray-700 hover:bg-red-50 hover:text-red-700 transition-colors duration-150',
              sidebarCollapsed ? 'px-2 justify-center' : 'px-4'
            ]"
          >
            <i class="fas fa-tachometer-alt text-red-600" :class="sidebarCollapsed ? '' : 'mr-2'"></i>
            <span v-if="!sidebarCollapsed">Admin Dashboard</span>
          </NavLink>

          <!-- User Management -->
          <NavLink
            :href="route('users.index')"
            :active="route().current('users.*')"
            :class="[
              'flex items-center py-2 rounded-lg text-sm font-semibold group transition-colors duration-150',
              route().current('users.*') 
                ? 'bg-red-100 text-red-700 border-r-2 border-red-600' 
                : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900',
              sidebarCollapsed ? 'px-2 justify-center' : 'px-4'
            ]"
          >
            <i class="fas fa-users text-lg flex-shrink-0" :class="sidebarCollapsed ? '' : 'mr-3'"></i>
            <span v-if="!sidebarCollapsed">User Management</span>
          </NavLink>

          <!-- Kasir -->
          <NavLink
            :href="route('kasir.index')"
            :active="route().current('kasir.*')"
            :class="[
              'flex items-center py-2 rounded-lg text-sm font-semibold group transition-colors duration-150',
              route().current('kasir.*')
                ? 'bg-red-100 text-red-700 border-r-2 border-red-600'
                : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900',
              sidebarCollapsed ? 'px-2 justify-center' : 'px-4'
            ]"
          >
            <i class="fas fa-cash-register text-lg flex-shrink-0" :class="sidebarCollapsed ? '' : 'mr-3'"></i>
            <span v-if="!sidebarCollapsed">Kasir</span>
          </NavLink>

          <!-- Laporan -->
          <NavLink
            :href="route('laporan.index')"
            :active="route().current('laporan.*')"
            :class="[
              'flex items-center py-2 rounded-lg text-sm font-semibold group transition-colors duration-150',
              route().current('laporan.*')
                ? 'bg-red-100 text-red-700 border-r-2 border-red-600'
                : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900',
              sidebarCollapsed ? 'px-2 justify-center' : 'px-4'
            ]"
          >
            <i class="fas fa-chart-bar text-lg flex-shrink-0" :class="sidebarCollapsed ? '' : 'mr-3'"></i>
            <span v-if="!sidebarCollapsed">Laporan</span>
          </NavLink>

          <!-- Dokter -->
          <NavLink
            :href="route('dokter.index')"
            :active="route().current('dokter.index')"
            :class="[
              'flex items-center py-2 rounded-lg text-sm font-semibold group transition-colors duration-150',
              route().current('dokter.index')
                ? 'bg-red-100 text-red-700 border-r-2 border-red-600'
                : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900',
              sidebarCollapsed ? 'px-2 justify-center' : 'px-4'
              ]"
            >
            <i class="fas fa-user-md text-lg flex-shrink-0" :class="sidebarCollapsed ? '' : 'mr-3'"></i>
            <span v-if="!sidebarCollapsed">Dokter</span>
          </NavLink>

          <!-- Pasien -->
          <NavLink
            :href="route('pasien.index')"
            :active="route().current('pasien.*')"
            :class="[
              'flex items-center py-2 rounded-lg text-sm font-semibold group transition-colors duration-150',
              route().current('pasien.*')
                ? 'bg-red-100 text-red-700 border-r-2 border-red-600'
                : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900',
              sidebarCollapsed ? 'px-2 justify-center' : 'px-4'
            ]"
          >
            <i class="fas fa-user-injured text-lg flex-shrink-0" :class="sidebarCollapsed ? '' : 'mr-3'"></i>
            <span v-if="!sidebarCollapsed">Pasien</span>
          </NavLink>

          <!-- Pasien Kunjungan -->
          <NavLink
            :href="route('kunjungan.index')"
            :active="route().current('kunjungan.*') || route().current('dokter.pasien-kunjungan')"
            :class="[
              'flex items-center py-2 rounded-lg text-sm font-semibold group transition-colors duration-150',
              (route().current('kunjungan.*') || route().current('dokter.pasien-kunjungan'))
                ? 'bg-red-100 text-red-700 border-r-2 border-red-600'
                : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900',
              sidebarCollapsed ? 'px-2 justify-center' : 'px-4'
            ]"
          >
            <i class="fas fa-calendar-check text-lg flex-shrink-0" :class="sidebarCollapsed ? '' : 'mr-3'"></i>
            <span v-if="!sidebarCollapsed">Pasien Kunjungan</span>
          </NavLink>

          <!-- Polis -->
          <NavLink
            :href="route('polis.index')"
            :active="route().current('polis.*')"
            :class="[
              'flex items-center py-2 rounded-lg text-sm font-semibold group transition-colors duration-150',
              route().current('polis.*')
                ? 'bg-red-100 text-red-700 border-r-2 border-red-600'
                : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900',
              sidebarCollapsed ? 'px-2 justify-center' : 'px-4'
            ]"
          >
            <i class="fas fa-hospital text-lg flex-shrink-0" :class="sidebarCollapsed ? '' : 'mr-3'"></i>
            <span v-if="!sidebarCollapsed">Polis</span>
          </NavLink>

          <!-- Mastering Dropdown -->
          <div class="relative group">
            <button
              @click="masteringExpanded = !masteringExpanded"
              :class="[
                'flex items-center rounded-lg text-sm font-semibold transition-colors duration-150 group w-full',
                (route().current('grp-eselon.*') || route().current('eselon.*') || route().current('tindakanq.*') || route().current('tindakan-tarif.*') || route().current('farmalkes.*'))
                  ? 'bg-red-100 text-red-700 border-r-2 border-red-600'
                  : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900',
                sidebarCollapsed ? 'px-2 py-2 justify-center' : 'px-4 py-2'
              ]"
              type="button"
              tabindex="0"
            >
              <i class="fas fa-cogs text-lg flex-shrink-0" :class="sidebarCollapsed ? '' : 'mr-3'"></i>
              <span v-if="!sidebarCollapsed" class="flex-1 text-left">Mastering</span>
              <i v-if="!sidebarCollapsed" class="fas fa-chevron-down text-xs transition-transform duration-200" :class="masteringExpanded ? '' : 'rotate-180'"></i>
            </button>

            <!-- Submenu Items (collapsible) -->
            <div v-if="!sidebarCollapsed && masteringExpanded" class="ml-4 mt-1 space-y-1">
              <NavLink
                :href="route('grp-eselon.index')"
                :active="!!route().current('grp-eselon.*')"
                class="flex items-center py-2 rounded-lg text-sm font-semibold text-gray-700 hover:bg-red-50 hover:text-red-700 transition-colors duration-150 group"
              >
                <i class="fas fa-sitemap text-indigo-500 mr-3 text-lg flex-shrink-0"></i>
                <span>GRP Eselon</span>
              </NavLink>

              <NavLink
                :href="route('eselon.index')"
                :active="!!route().current('eselon.*')"
                class="flex items-center py-2 rounded-lg text-sm font-semibold text-gray-700 hover:bg-red-50 hover:text-red-700 transition-colors duration-150 group"
              >
                <i class="fas fa-layer-group text-purple-500 mr-3 text-lg flex-shrink-0"></i>
                <span>Eselon</span>
              </NavLink>

              <NavLink
                :href="route('farmalkes.index')"
                :active="!!route().current('farmalkes.*')"
                class="flex items-center py-2 rounded-lg text-sm font-semibold text-gray-700 hover:bg-red-50 hover:text-red-700 transition-colors duration-150 group"
              >
                <i class="fas fa-pills text-red-500 mr-3 text-lg flex-shrink-0"></i>
                <span>Farmalkes</span>
              </NavLink>

              <NavLink
                :href="route('tindakanq.index')"
                :active="!!route().current('tindakanq.*')"
                class="flex items-center py-2 rounded-lg text-sm font-semibold text-gray-700 hover:bg-red-50 hover:text-red-700 transition-colors duration-150 group"
              >
                <i class="fas fa-procedures text-green-500 mr-3 text-lg flex-shrink-0"></i>
                <span>Tindakan Q</span>
              </NavLink>

              <NavLink
                :href="route('tindakan-tarif.index')"
                :active="!!route().current('tindakan-tarif.*')"
                class="flex items-center py-2 rounded-lg text-sm font-semibold text-gray-700 hover:bg-red-50 hover:text-red-700 transition-colors duration-150 group"
              >
                <i class="fas fa-dollar-sign text-yellow-500 mr-3 text-lg flex-shrink-0"></i>
                <span>Tarif Tindakan</span>
              </NavLink>
            </div>
          </div>
        </template>

        <!-- Dokter & Perawat links (non-admin) -->
        <template v-else-if="isDokterOrPerawat">
          <!-- Main Poli & Layanan Link -->
          <NavLink
            :href="route('dokter.poli_layanan')"
            :active="route().current('dokter.poli_layanan')"
            :class="[
              'flex items-center py-2 rounded-lg text-sm font-semibold text-gray-700 hover:bg-green-50 hover:text-green-700 transition-colors duration-150',
              sidebarCollapsed ? 'px-2 justify-center' : 'px-4'
            ]"
          >
            <i class="fas fa-hospital text-green-600" :class="sidebarCollapsed ? '' : 'mr-2'"></i>
            <span v-if="!sidebarCollapsed">Poli & Layanan</span>
          </NavLink>

          <!-- Menu Utama -->
          <NavLink
            :href="route('dokter.pasien-kunjungan')"
            :active="route().current('dokter.pasien-kunjungan')"
            :class="[
              'flex items-center py-2 rounded-lg text-sm font-semibold text-gray-700 hover:bg-green-50 hover:text-green-700 transition-colors duration-150',
              sidebarCollapsed ? 'px-2 justify-center' : 'px-4'
            ]"
          >
            <i class="fas fa-users text-green-600" :class="sidebarCollapsed ? '' : 'mr-2'"></i>
            <span v-if="!sidebarCollapsed">Pasien Kunjungan</span>
          </NavLink>

        </template>

        <!-- Pendaftaran-only links -->
        <template v-else-if="isPendaftaran">
          <NavLink
            :href="route('pasien.index')"
            :active="route().current('pasien.index')"
            :class="[
              'flex items-center py-2 rounded-lg text-sm font-semibold text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors duration-150',
              sidebarCollapsed ? 'px-2 justify-center' : 'px-4'
            ]"
          >
            <i class="fas fa-users text-blue-600" :class="sidebarCollapsed ? '' : 'mr-2'"></i>
            <span v-if="!sidebarCollapsed">Daftar Pasien</span>
          </NavLink>
        </template>

        <!-- Kasir-only links -->
        <template v-else-if="isKasir">
          <NavLink
            :href="route('kasir.index')"
            :active="route().current('kasir.index')"
            :class="[
              'flex items-center py-2 rounded-lg text-sm font-semibold text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors duration-150',
              sidebarCollapsed ? 'px-2 justify-center' : 'px-4'
            ]"
          >
            <i class="fas fa-users text-blue-600" :class="sidebarCollapsed ? '' : 'mr-2'"></i>
            <span v-if="!sidebarCollapsed">Daftar Transaksi</span>
          </NavLink>
          <NavLink
            :href="route('laporan.index')"
            :active="route().current('laporan.*')"
            :class="[
              'flex items-center py-2 rounded-lg text-sm font-semibold text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors duration-150',
              sidebarCollapsed ? 'px-2 justify-center' : 'px-4'
            ]"
          >
            <i class="fas fa-chart-bar text-blue-600" :class="sidebarCollapsed ? '' : 'mr-2'"></i>
            <span v-if="!sidebarCollapsed">Laporan</span>
          </NavLink>
        </template>
      </nav>

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
            <!-- Dashboard - Hide for admin, show for other roles -->
            <template v-if="!isAdmin">
              <ResponsiveNavLink
                :href="route('dashboard')"
                :active="route().current('dashboard')"
              >
                Dashboard
              </ResponsiveNavLink>
            </template>

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
              <ResponsiveNavLink
                :href="route('laporan.index')"
                :active="route().current('laporan.*')"
              >
                Laporan
              </ResponsiveNavLink>
            </template>

            <!-- Dokter & Perawat responsive links (non-admin) -->
            <template v-else-if="isDokterOrPerawat">
              <ResponsiveNavLink
                :href="route('dokter.dashboard')"
                :active="route().current('dokter.dashboard')"
              >
                {{ isPerawat ? 'Dashboard Perawat' : 'Dashboard Dokter' }}
              </ResponsiveNavLink>
              <ResponsiveNavLink
                :href="route('dokter.poli_layanan')"
                :active="route().current('dokter.poli_layanan')"
              >
                Poli & Layanan
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
              <ResponsiveNavLink
                :href="route('laporan.index')"
                :active="route().current('laporan.*')"
              >
                Laporan
              </ResponsiveNavLink>
            </template>
          </div>
        </div>
      </div>

      <!-- Page content -->
      <main class="py-6 min-h-screen overflow-y-auto no-scrollbar">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
          <slot />
        </div>
      </main>
    </div>
  </div>
</template>

<style scoped>
/* Enable scroll for sidebar navigation */
nav {
    overflow-y: auto !important;
    overflow-x: hidden !important;
    -ms-overflow-style: auto !important;
    scrollbar-width: thin !important;
    scrollbar-color: rgba(0, 0, 0, 0.2) transparent !important;
    max-height: calc(100vh - 64px) !important;
    min-height: 0 !important;
}

/* Enable flex layout for sidebar container */
div[class*="fixed"][class*="inset-y-0"] {
    display: flex !important;
    flex-direction: column !important;
    overflow: visible !important;
}

/* Custom scrollbar for sidebar - Webkit browsers */
nav::-webkit-scrollbar {
    width: 6px !important;
    display: block !important;
}
nav::-webkit-scrollbar-track {
    background: transparent !important;
}
nav::-webkit-scrollbar-thumb {
    background: rgba(0, 0, 0, 0.2) !important;
    border-radius: 3px !important;
}
nav::-webkit-scrollbar-thumb:hover {
    background: rgba(0, 0, 0, 0.3) !important;
}
/* Smooth transitions */
* {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
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

/* Global hide scrollbar class */
.no-scrollbar::-webkit-scrollbar {
    display: none !important;
    width: 0 !important;
    height: 0 !important;
}

.no-scrollbar {
    -ms-overflow-style: none !important;
    scrollbar-width: none !important;
}

/* Hide all scrollbars globally */
:deep(::-webkit-scrollbar) {
    display: none !important;
    width: 0 !important;
    height: 0 !important;
}

:deep(*) {
    -ms-overflow-style: none !important;
    scrollbar-width: none !important;
}
</style>