<script setup>
import { ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link } from '@inertiajs/vue3';
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

const handleSidebarMouseEnter = () => {
    if (sidebarCollapsed.value) {
        sidebarHovered.value = true;
    }
};

const handleSidebarMouseLeave = () => {
    sidebarHovered.value = false;
};
</script>

<template>
    <div class="min-h-screen bg-gray-100">
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
                sidebarCollapsed ? 'lg:w-16' : 'lg:w-64',
                sidebarHovered && sidebarCollapsed ? 'lg:w-64' : ''
            ]"
            @mouseenter="handleSidebarMouseEnter"
            @mouseleave="handleSidebarMouseLeave"
        >
            <!-- Sidebar Header -->
            <div class="flex items-center justify-between h-16 px-4 bg-gradient-to-r from-red-600 to-pink-600">
                <div v-if="!sidebarCollapsed || sidebarHovered" class="flex items-center">
                    <Link :href="route('dashboard')" class="flex items-center">
                        <ApplicationLogo class="h-8 w-auto fill-current text-white" />
                        <span class="ml-2 text-white font-bold text-lg">Klinik Mulfa</span>
                    </Link>
                </div>
                <div v-else class="flex justify-center w-full">
                                <Link :href="route('dashboard')">
                        <ApplicationLogo class="h-8 w-auto fill-current text-white" />
                                </Link>
                            </div>

                <!-- Toggle button (desktop only) -->
                <button 
                    @click="toggleSidebar"
                    class="hidden lg:block p-1 rounded-md text-white hover:bg-red-700 transition-colors"
                >
                    <i :class="sidebarCollapsed ? 'fas fa-chevron-right' : 'fas fa-chevron-left'" class="text-sm"></i>
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
                    <span v-if="!sidebarCollapsed || sidebarHovered">Dashboard</span>
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
                        <span v-if="!sidebarCollapsed || sidebarHovered">Admin Dashboard</span>
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
                        <span v-if="!sidebarCollapsed || sidebarHovered">User Management</span>
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
                        <span v-if="!sidebarCollapsed || sidebarHovered">Tindakan Medis</span>
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
                        <span v-if="!sidebarCollapsed || sidebarHovered">Resep Management</span>
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
                        <span v-if="!sidebarCollapsed || sidebarHovered">Kasir</span>
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
                        <span v-if="!sidebarCollapsed || sidebarHovered">Dokter</span>
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
                        <span v-if="!sidebarCollapsed || sidebarHovered">Pasien</span>
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
                        <span v-if="!sidebarCollapsed || sidebarHovered">Pasien Kunjungan</span>
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
                        <span v-if="!sidebarCollapsed || sidebarHovered">Kasir</span>
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
                        <span v-if="!sidebarCollapsed || sidebarHovered">Pasien</span>
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
                    <div v-if="!sidebarCollapsed || sidebarHovered" class="ml-3 flex-1">
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

                <!-- User menu -->
                <div class="flex items-center gap-x-4 lg:gap-x-6">
                    <!-- Profile dropdown -->
                    <Dropdown align="right" width="48">
                        <template #trigger>
                            <button class="flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                <div class="h-8 w-8 rounded-full bg-red-600 flex items-center justify-center">
                                    <span class="text-white text-sm font-bold">
                                        {{ $page.props.auth.user.name.charAt(0).toUpperCase() }}
                                    </span>
                                </div>
                                <span class="ml-2 text-gray-700 font-medium hidden sm:block">{{ $page.props.auth.user.name }}</span>
                                <i class="fas fa-chevron-down ml-1 text-gray-400"></i>
                            </button>
                        </template>

                        <template #content>
                            <div class="px-4 py-2 border-b border-gray-200">
                                <div class="text-sm font-medium text-gray-800">{{ $page.props.auth.user.name }}</div>
                                <div class="text-sm text-gray-500">{{ $page.props.auth.user.email }}</div>
                                <div class="text-xs text-red-600 font-medium">{{ $page.props.auth.user.role }}</div>
                            </div>
                            
                            <DropdownLink :href="route('profile.edit')">
                                <i class="fas fa-user mr-2"></i>Profile
                            </DropdownLink>
                            
                            <DropdownLink :href="route('logout')" method="post" as="button">
                                <i class="fas fa-sign-out-alt mr-2"></i>Log Out
                            </DropdownLink>
                        </template>
                    </Dropdown>
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

/* Mobile responsiveness */
@media (max-width: 1024px) {
    .lg\\:ml-64 {
        margin-left: 0 !important;
    }
    
    .lg\\:ml-16 {
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