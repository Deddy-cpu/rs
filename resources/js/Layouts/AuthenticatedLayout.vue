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

// Use auth composable
const { isAdmin, isDokter, isKasir, isPendaftaran } = useAuth();

</script>

<template>
    <div>
        <div class="min-h-screen bg-gray-100">
            <nav
                class="border-b border-gray-100 bg-white"
            >
                <!-- Primary Navigation Menu -->
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex h-16 justify-between">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="flex shrink-0 items-center">
                                <Link :href="route('dashboard')">
                                    <ApplicationLogo
                                        class="block h-9 w-auto fill-current text-gray-800"
                                    />
                                </Link>
                            </div>

                            <!-- Navigation Links -->
                            <div
                                class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex"
                            >
                                <NavLink
                                    :href="route('dashboard')"
                                    :active="!!route().current('dashboard')"
                                >
                                    Dashboard
                                </NavLink>
                                
                                <!-- Admin-only links -->
                                <template v-if="isAdmin">
                                    <NavLink
                                        :href="route('admin.dashboard')"
                                        :active="!!route().current('admin.dashboard')"
                                    >
                                        Admin Dashboard
                                    </NavLink>
                                    <NavLink
                                        :href="route('users.index')"
                                        :active="!!route().current('users.index')"
                                    >
                                        User Management
                                    </NavLink>
                                    <NavLink
                                        :href="route('kasir.index')"
                                        :active="!!route().current('kasir.index')"
                                    >
                                        Kasir
                                    </NavLink>
                                    <NavLink
                                        :href="route('dokter.index')"
                                        :active="!!route().current('dokter.index')"
                                    >
                                        Dokter
                                    </NavLink>
                                    <NavLink
                                        :href="route('pasien.index')"
                                        :active="!!route().current('pasien.index')"
                                    >
                                        Pasien
                                    </NavLink>

                                    <NavLink
                                        :href="route('dokter.pasien-kunjungan')"
                                        :active="!!route().current('dokter.pasien-kunjungan')"
                                    >
                                        Pasien Kunjungan
                                    </NavLink>
                                </template>

                                <!-- Dokter-only links (non-admin dokter) -->
                                <template v-else-if="isDokter">
                                    <!-- Dropdown Menu untuk Poli -->
                                    <div class="relative group inline-block">
                                        <button
                                            class="flex items-center px-4 py-2 rounded-lg text-sm font-semibold text-gray-700 hover:bg-green-50 hover:text-green-700 transition-colors duration-150 group"
                                            type="button"
                                        >
                                            <i class="fas fa-hospital mr-2 text-green-600"></i>
                                            <span>Poli & Layanan</span>
                                            <i class="fas fa-chevron-down ml-2 text-xs transition-transform duration-200 group-hover:rotate-180"></i>
                                        </button>
                                        
                                        <!-- Dropdown Content -->
                                        <div class="absolute left-0 mt-8 w-64 bg-white border border-gray-200 rounded-2xl shadow-2xl opacity-0 invisible group-hover:opacity-100 group-hover:visible group-focus-within:opacity-100 group-focus-within:visible pointer-events-none group-hover:pointer-events-auto group-focus-within:pointer-events-auto z-50 transition-all duration-200 transform translate-y-4 group-hover:translate-y-0 group-focus-within:translate-y-0">
                                            <div class="py-4">
                                                <!-- Header -->
                                                <div class="px-6 py-3 border-b border-gray-100">
                                                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Poli Medis</p>
                                                </div>
                                                
                                                <!-- Poli Umum -->
                                                <NavLink
                                                    :href="route('dokter.poli_layanan.poli_umum')"
                                                    :active="route().current('dokter.poli_layanan.poli_umum')"
                                                    class="flex items-center px-6 py-4 text-base font-semibold text-gray-700 hover:bg-green-100 hover:text-green-700 transition-colors duration-150 rounded-lg"
                                                >
                                                    <i class="fas fa-user-md mr-4 text-blue-500 w-5 text-xl"></i>
                                                    <span>Poli Umum</span>
                                                </NavLink>
                                                
                                                <!-- Poli Gigi -->
                                                <NavLink
                                                    :href="route('dokter.poli_layanan.poli_gigi')"
                                                    :active="route().current('dokter.poli_layanan.poli_gigi')"
                                                    class="flex items-center px-6 py-4 text-base font-semibold text-gray-700 hover:bg-green-100 hover:text-green-700 transition-colors duration-150 rounded-lg"
                                                >
                                                    <i class="fas fa-tooth mr-4 text-purple-500 w-5 text-xl"></i>
                                                    <span>Poli Gigi</span>
                                                </NavLink>
                                                
                                                <!-- KIA -->
                                                <NavLink
                                                    :href="route('dokter.poli_layanan.kia')"
                                                    :active="route().current('dokter.poli_layanan.kia')"
                                                    class="flex items-center px-6 py-4 text-base font-semibold text-gray-700 hover:bg-green-100 hover:text-green-700 transition-colors duration-150 rounded-lg"
                                                >
                                                    <i class="fas fa-baby mr-4 text-pink-500 w-5 text-xl"></i>
                                                    <span>KIA (Kesehatan Ibu Anak)</span>
                                                </NavLink>
                                                
                                                <!-- Separator -->
                                                <div class="px-6 py-3 border-b border-gray-100 mt-2">
                                                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Layanan Pendukung</p>
                                                </div>
                                                
                                                <!-- Laboratorium -->
                                                <NavLink
                                                    :href="route('dokter.poli_layanan.laboratorium')"
                                                    :active="route().current('dokter.poli_layanan.laboratorium')"
                                                    class="flex items-center px-6 py-4 text-base font-semibold text-gray-700 hover:bg-green-100 hover:text-green-700 transition-colors duration-150 rounded-lg"
                                                >
                                                    <i class="fas fa-flask mr-4 text-orange-500 w-5 text-xl"></i>
                                                    <span>Laboratorium</span>
                                                </NavLink>
                                                
                                                <!-- Apotek -->
                                                <NavLink
                                                    :href="route('dokter.poli_layanan.apotek')"
                                                    :active="route().current('dokter.poli_layanan.apotek')"
                                                    class="flex items-center px-6 py-4 text-base font-semibold text-gray-700 hover:bg-green-100 hover:text-green-700 transition-colors duration-150 rounded-lg"
                                                >
                                                    <i class="fas fa-pills mr-4 text-green-500 w-5 text-xl"></i>
                                                    <span>Apotek</span>
                                                </NavLink>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Dropdown Menu untuk Master Data -->
                                    <div class="relative group inline-block ml-2">
                                        <button
                                            class="flex items-center px-4 py-2 rounded-lg text-sm font-semibold text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors duration-150 group"
                                            type="button"
                                        >
                                            <i class="fas fa-database mr-2 text-blue-600"></i>
                                            <span>Master Data</span>
                                            <i class="fas fa-chevron-down ml-2 text-xs transition-transform duration-200 group-hover:rotate-180"></i>
                                        </button>
                                        <!-- Dropdown Content -->
                                        <div class="absolute left-0 mt-8 w-64 bg-white border border-gray-200 rounded-2xl shadow-2xl opacity-0 invisible group-hover:opacity-100 group-hover:visible group-focus-within:opacity-100 group-focus-within:visible pointer-events-none group-hover:pointer-events-auto group-focus-within:pointer-events-auto z-50 transition-all duration-200 transform translate-y-4 group-hover:translate-y-0 group-focus-within:translate-y-0">
                                            <div class="py-4">
                                                <NavLink
                                                    :href="route('grp-eselon.index')"
                                                    :active="!!route().current('grp-eselon.*')"
                                                    class="flex items-center px-6 py-4 text-base font-semibold text-gray-700 hover:bg-blue-100 hover:text-blue-700 transition-colors duration-150 rounded-lg"
                                                >
                                                    <i class="fas fa-sitemap mr-4 text-indigo-500 w-5 text-xl"></i>
                                                    <span>GRP Eselon</span>
                                                </NavLink>

                                                <NavLink
                                                    :href="route('eselon.index')"
                                                    :active="!!route().current('eselon.*')"
                                                    class="flex items-center px-6 py-4 text-base font-semibold text-gray-700 hover:bg-blue-100 hover:text-blue-700 transition-colors duration-150 rounded-lg"
                                                >
                                                    <i class="fas fa-layer-group mr-4 text-purple-500 w-5 text-xl"></i>
                                                    <span>Eselon</span>
                                                </NavLink>

                                                <NavLink
                                                    :href="route('tindakanq.index')"
                                                    :active="!!route().current('tindakanq.*')"
                                                    class="flex items-center px-6 py-4 text-base font-semibold text-gray-700 hover:bg-blue-100 hover:text-blue-700 transition-colors duration-150 rounded-lg"
                                                >
                                                    <i class="fas fa-procedures mr-4 text-green-500 w-5 text-xl"></i>
                                                    <span>Tindakan</span>
                                                </NavLink>

                                                <NavLink
                                                    :href="route('tindakan-tarif.index')"
                                                    :active="!!route().current('tindakan-tarif.*')"
                                                    class="flex items-center px-6 py-4 text-base font-semibold text-gray-700 hover:bg-blue-100 hover:text-blue-700 transition-colors duration-150 rounded-lg"
                                                >
                                                    <i class="fas fa-dollar-sign mr-4 text-yellow-500 w-5 text-xl"></i>
                                                    <span>Tarif Tindakan</span>
                                                </NavLink>

                                                <NavLink
                                                    :href="route('farmalkes.index')"
                                                    :active="!!route().current('farmalkes.*')"
                                                    class="flex items-center px-6 py-4 text-base font-semibold text-gray-700 hover:bg-blue-100 hover:text-blue-700 transition-colors duration-150 rounded-lg"
                                                >
                                                    <i class="fas fa-pills mr-4 text-red-500 w-5 text-xl"></i>
                                                    <span>Farmalkes</span>
                                                </NavLink>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Menu Utama -->
                                    <NavLink
                                        :href="route('dokter.pasien-kunjungan')"
                                        :active="!!route().current('dokter.pasien-kunjungan')"
                                        class="flex items-center px-4 py-2 rounded-lg text-sm font-semibold text-gray-700 hover:bg-green-50 hover:text-green-700 transition-colors duration-150"
                                    >
                                        <i class="fas fa-users mr-2 text-green-600"></i>
                                        Pasien Kunjungan
                                    </NavLink>
                                    
                                    <!-- Menu Pasien -->
                                    <NavLink
                                        :href="route('pasien.index')"
                                        :active="!!route().current('pasien.*')"
                                        class="flex items-center px-4 py-2 rounded-lg text-sm font-semibold text-gray-700 hover:bg-green-50 hover:text-green-700 transition-colors duration-150"
                                    >
                                        <i class="fas fa-user-injured mr-2 text-blue-600"></i>
                                        Pasien
                                    </NavLink>
                                </template>

                                <!-- Kasir and Pendaftaran links -->
                                <template v-else-if="isKasir || isPendaftaran">
                                    <NavLink
                                        :href="route('kasir.index')"
                                        :active="!!route().current('kasir.index')"
                                    >
                                        Kasir
                                    </NavLink>

                                    <NavLink
                                        :href="route('pasien.index')"
                                        :active="!!route().current('pasien.index')"
                                    >
                                        Pasien
                                    </NavLink>
                                </template>
                               
                            </div>
                        </div>

                        <div class="hidden sm:ms-6 sm:flex sm:items-center">
                            <!-- Settings Dropdown -->
                            <div class="relative ms-3">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button
                                                type="button"
                                                class="inline-flex items-center rounded-md border border-transparent bg-white px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out hover:text-gray-700 focus:outline-none"
                                            >
                                                {{ $page.props.auth.user.name }}

                                                <svg
                                                    class="-me-0.5 ms-2 h-4 w-4"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <DropdownLink
                                            :href="route('profile.edit')"
                                        >
                                            Profile
                                        </DropdownLink>
                                        <DropdownLink
                                            :href="route('logout')"
                                            method="post"
                                            as="button"
                                        >
                                            Log Out
                                        </DropdownLink>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>

                        <!-- Hamburger -->
                        <div class="-me-2 flex items-center sm:hidden">
                            <button
                                @click="
                                    showingNavigationDropdown =
                                        !showingNavigationDropdown
                                "
                                class="inline-flex items-center justify-center rounded-md p-2 text-gray-400 transition duration-150 ease-in-out hover:bg-gray-100 hover:text-gray-500 focus:bg-gray-100 focus:text-gray-500 focus:outline-none"
                            >
                                <svg
                                    class="h-6 w-6"
                                    stroke="currentColor"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        :class="{
                                            hidden: showingNavigationDropdown,
                                            'inline-flex':
                                                !showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path
                                        :class="{
                                            hidden: !showingNavigationDropdown,
                                            'inline-flex':
                                                showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>
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
                            :active="!!route().current('dashboard')"
                        >
                            Dashboard
                        </ResponsiveNavLink>
                        
                        <!-- Admin-only responsive links -->
                        <template v-if="isAdmin">
                            <ResponsiveNavLink
                                :href="route('admin.dashboard')"
                                :active="!!route().current('admin.dashboard')"
                            >
                                Admin Dashboard
                            </ResponsiveNavLink>
                            <ResponsiveNavLink
                                :href="route('users.index')"
                                :active="!!route().current('users.index')"
                            >
                                User Management
                            </ResponsiveNavLink>
                            <ResponsiveNavLink
                                :href="route('kasir.index')"
                                :active="!!route().current('kasir.index')"
                            >
                                Kasir
                            </ResponsiveNavLink>



                        </template>

                        <!-- Dokter-only responsive links (non-admin dokter) -->
                        <template v-else-if="isDokter">
                            <ResponsiveNavLink
                                :href="route('dokter.index')"
                                :active="!!route().current('dokter.index')"
                            >
                                Dokter
                            </ResponsiveNavLink>
                            <ResponsiveNavLink
                                :href="route('kasir.index')"
                                :active="!!route().current('kasir.index')"
                            >
                                Kasir
                            </ResponsiveNavLink>
                        </template>

                        <!-- Kasir and Pendaftaran responsive links -->
                        <template v-else-if="isKasir || isPendaftaran">
                            <ResponsiveNavLink
                                :href="route('kasir.index')"
                                :active="!!route().current('kasir.index')"
                            >
                                Kasir
                            </ResponsiveNavLink>
                        </template>
                    </div>

                    <!-- Responsive Settings Options -->
                    <div
                        class="border-t border-gray-200 pb-1 pt-4"
                    >
                        <div class="px-4">
                            <div
                                class="text-base font-medium text-gray-800"
                            >
                                {{ $page.props.auth.user.name }}
                            </div>
                            <div class="text-sm font-medium text-gray-500">
                                {{ $page.props.auth.user.email }}
                            </div>
                            <div class="text-xs font-medium text-blue-600">
                                Role: {{ $page.props.auth.user.role }}
                            </div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink :href="route('profile.edit')">
                                Profile
                            </ResponsiveNavLink>
                            <ResponsiveNavLink
                                :href="route('logout')"
                                method="post"
                                as="button"
                            >
                                Log Out
                            </ResponsiveNavLink>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <header
                class="bg-white shadow"
                v-if="$slots.header"
            >
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main>
                <slot />
            </main>
        </div>
    </div>
</template>
