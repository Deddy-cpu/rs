<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import { ref } from 'vue'

const menuKasir = ref([
  { title: "Penjualan", desc: "Transaksi & kasir", icon: "💳", color: "from-blue-400 to-blue-600", shadow: "shadow-blue-200" },
  { title: "Barang", desc: "Kelola stok & produk", icon: "📦", color: "from-green-400 to-green-600", shadow: "shadow-green-200" },
  { title: "Laporan", desc: "Statistik & laporan", icon: "📊", color: "from-pink-400 to-pink-600", shadow: "shadow-pink-200" },
  { title: "Pengaturan", desc: "Akun & preferensi", icon: "⚙", color: "from-yellow-400 to-yellow-600", shadow: "shadow-yellow-200" },
])
</script>

<template>
  <Head title="Welcome" />

  <div
    class="min-h-screen flex flex-col relative overflow-hidden"
    style="background-image: url('/images/bg-login.png'); background-size: cover; background-position: center; background-repeat: no-repeat;"
  >
    <!-- Navbar -->
    <nav class="absolute top-0 right-0 p-6 z-20 flex gap-4">
      <Link
        :href="route('login')"
        class="px-4 py-2 rounded-lg bg-white/70 backdrop-blur-md shadow hover:bg-white transition font-semibold text-gray-700"
      >
        Login
      </Link>
      <Link
        :href="route('register')"
        class="px-4 py-2 rounded-lg bg-indigo-600 text-white shadow hover:bg-indigo-700 transition font-semibold"
      >
        Register
      </Link>
    </nav>

    <!-- Decorative Background Circles -->
    <div class="absolute -top-32 -left-32 w-96 h-96 bg-blue-200 opacity-30 rounded-full blur-2xl z-0"></div>
    <div class="absolute -bottom-40 -right-40 w-[30rem] h-[30rem] bg-pink-200 opacity-30 rounded-full blur-2xl z-0"></div>

    <!-- Header -->
    <header class="p-8 text-center z-10 relative mt-16">
      <div class="flex justify-center mb-4">
        <img src="https://img.icons8.com/color/96/000000/cash-register.png" alt="Kasir Icon" class="w-20 h-20 drop-shadow-lg animate-bounce-slow" />
      </div>
      <h1 class="text-5xl md:text-6xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-blue-600 via-pink-500 to-yellow-400 drop-shadow-lg mb-2 tracking-tight">
        Selamat Datang
      </h1>
      <p class="text-lg md:text-xl text-gray-700 font-medium">Aplikasi Mesin Kasir Modern</p>
    </header>

    <!-- Content -->
    <main class="flex-1 flex flex-col items-center justify-center z-10">
      <!-- Kasir Menu -->
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-8 w-full max-w-5xl px-6 py-8">
        <div
          v-for="item in menuKasir"
          :key="item.title"
          class="group bg-white/60 backdrop-blur-md shadow-2xl rounded-3xl p-8 flex flex-col items-center text-center border border-gray-200 hover:border-blue-400 transition-all duration-300 cursor-pointer hover:-translate-y-2 relative overflow-hidden"
          :class="item.shadow"
        >
          <div :class="`absolute top-0 left-0 w-full h-2 rounded-t-3xl bg-gradient-to-r ${item.color}`"></div>
          <div class="mb-4">
            <div :class="`w-20 h-20 flex items-center justify-center rounded-full bg-gradient-to-br ${item.color} text-white text-5xl shadow-lg group-hover:scale-110 transition-transform duration-200 animate-pop`">
              {{ item.icon }}
            </div>
          </div>
          <h2 class="text-2xl font-bold text-gray-800 mb-2 group-hover:text-blue-600 transition-colors duration-200">
            {{ item.title }}
          </h2>
          <p class="text-base text-gray-600 font-medium">{{ item.desc }}</p>
        </div>
      </div>

      <!-- Motivational Quote -->
      <div class="mt-8 max-w-xl w-full mx-auto bg-white/50 backdrop-blur-md rounded-2xl shadow-lg p-6 flex items-center gap-4 border border-blue-100">
        <svg class="w-10 h-10 text-yellow-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 20h.01M12 4v16m8-8H4"></path>
        </svg>
        <div>
          <p class="text-lg font-semibold text-gray-700">"Kesuksesan adalah hasil dari persiapan, kerja keras, dan belajar dari kegagalan."</p>
          <span class="text-sm text-gray-400">- Colin Powell</span>
        </div>
      </div>
    </main>

    <!-- Footer -->
    <footer class="p-4 text-center bg-white/60 backdrop-blur-md text-gray-700 text-sm z-10 shadow-inner relative">
      <div class="flex items-center justify-center gap-2 mb-1">
        <svg class="w-5 h-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
          <path d="M2.003 9.25C2.003 5.798 5.134 3 9 3s6.997 2.798 6.997 6.25c0 2.386-1.386 4.463-3.5 5.74V17a1 1 0 01-1.447.894l-2-1A1 1 0 019 16v-1.01c-2.114-1.277-3.5-3.354-3.5-5.74z"/>
        </svg>
        <span>© 2025 Mesin Kasir App - Laravel 12 + Vue 3</span>
      </div>
      <div class="text-xs text-gray-400">Dibuat dengan <span class="text-pink-500">❤</span> oleh Tim Anda</div>
    </footer>
  </div>
</template>

<style scoped>
@keyframes pop {
  0% { transform: scale(0.95);}
  60% { transform: scale(1.08);}
  100% { transform: scale(1);}
}
.animate-pop {
  animation: pop 0.5s cubic-bezier(.68,-0.55,.27,1.55) both;
}
@keyframes bounce-slow {
  0%, 100% { transform: translateY(0);}
  50% { transform: translateY(-12px);}
}
.animate-bounce-slow {
  animation: bounce-slow 2.5s infinite;
}
</style>
