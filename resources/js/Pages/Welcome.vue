<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import { ref } from 'vue'

const menuKlinik = ref([
  { title: "Pasien", desc: "Kelola data pasien", icon: "👥", color: "from-blue-500 to-blue-700", shadow: "shadow-blue-200", route: "pasien.create" },
  { title: "Kasir", desc: "Transaksi & pembayaran", icon: "💳", color: "from-orange-500 to-orange-700", shadow: "shadow-orange-200", route: "kasir.index" },
  { title: "Users", desc: "Manajemen pengguna", icon: "👥", color: "from-pink-500 to-pink-700", shadow: "shadow-pink-200", route: "users.index" },
  { title: "Dashboard", desc: "Overview sistem", icon: "📊", color: "from-indigo-500 to-indigo-700", shadow: "shadow-indigo-200", route: "dashboard" },
  { title: "Profile", desc: "Pengaturan akun", icon: "⚙️", color: "from-teal-500 to-teal-700", shadow: "shadow-teal-200", route: "profile.edit" },
  { title: "Login", desc: "Masuk ke sistem", icon: "🔐", color: "from-gray-500 to-gray-700", shadow: "shadow-gray-200", route: "login" },
])

const features = ref([
  { title: "Sistem Terintegrasi", desc: "Semua data pasien tersimpan aman dan terintegrasi", icon: "🔒", stat: "100%" },
  { title: "Akses 24/7", desc: "Akses data kapan saja, di mana saja", icon: "🌐", stat: "24/7" },
  { title: "Keamanan Data", desc: "Enkripsi tingkat tinggi untuk privasi pasien", icon: "🛡️", stat: "HIPAA" },
  { title: "Laporan Real-time", desc: "Monitoring performa klinik secara real-time", icon: "📈", stat: "Live" },
])

const stats = ref([
  { number: "10,000+", label: "Pasien Terlayani", icon: "👥" },
  { number: "50+", label: "Dokter Terdaftar", icon: "👨‍⚕️" },
  { number: "99.9%", label: "Uptime Sistem", icon: "⚡" },
  { number: "24/7", label: "Support Teknis", icon: "🛠️" },
])

const testimonials = ref([
  { name: "dr. Atika Ghassani, Sp.KFR", role: "Direktur Klinik", content: "Sistem ini telah mengubah cara kami mengelola pasien. Sangat efisien dan mudah digunakan.", avatar: "👩‍⚕️" },
  { name: "drg. Any K. Putri, Sp.Ort", role: "Dokter Spesialis", content: "Interface yang intuitif dan fitur yang lengkap. Sangat membantu dalam pekerjaan sehari-hari.", avatar: "👨‍⚕️" },
  { name: "dr. Agustiana, Sp.Rad", role: "Admin Klinik", content: "Pelaporan yang akurat dan real-time. Manajemen data pasien menjadi lebih mudah.", avatar: "👩‍💼" },
])
</script>

<template>
  <Head title="Sistem Informasi Klinik" />

  <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 relative overflow-hidden">
    <!-- Animated Medical Background Pattern -->
    <div class="absolute inset-0 opacity-10 z-0 pointer-events-none">
      <div class="absolute top-10 left-10 w-32 h-32 border-2 border-blue-300 rounded-full animate-float"></div>
      <div class="absolute top-32 right-20 w-24 h-24 border-2 border-green-300 rounded-full animate-float" style="animation-delay: 1s;"></div>
      <div class="absolute bottom-20 left-1/4 w-16 h-16 border-2 border-purple-300 rounded-full animate-float" style="animation-delay: 2s;"></div>
      <div class="absolute bottom-32 right-1/3 w-20 h-20 border-2 border-pink-300 rounded-full animate-float" style="animation-delay: 3s;"></div>
      <div class="absolute top-1/2 left-1/3 w-12 h-12 border-2 border-cyan-300 rounded-full animate-float" style="animation-delay: 4s;"></div>
      <div class="absolute top-1/3 right-1/4 w-8 h-8 border-2 border-orange-300 rounded-full animate-float" style="animation-delay: 5s;"></div>
    </div>

    <!-- Floating Medical Icons -->
    <div class="absolute inset-0 opacity-20 z-0 pointer-events-none">
      <div class="absolute top-20 right-1/4 text-4xl animate-bounce" style="animation-delay: 0.5s;">💊</div>
      <div class="absolute top-1/2 left-20 text-3xl animate-bounce" style="animation-delay: 1.5s;">🩺</div>
      <div class="absolute bottom-1/3 right-20 text-3xl animate-bounce" style="animation-delay: 2.5s;">🏥</div>
      <div class="absolute top-1/3 left-1/2 text-2xl animate-bounce" style="animation-delay: 3.5s;">⚕️</div>
    </div>

    <!-- Navbar -->
    <nav class="absolute top-0 right-0 p-6 z-20 flex gap-4">
      <Link
        :href="route('login')"
        class="px-6 py-3 rounded-xl bg-white/80 backdrop-blur-md shadow-lg hover:bg-white hover:shadow-xl transition-all duration-300 font-semibold text-gray-700 border border-gray-200"
      >
        Masuk
      </Link>
      <Link
        :href="route('register')"
        class="px-6 py-3 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg hover:from-blue-700 hover:to-indigo-700 hover:shadow-xl transition-all duration-300 font-semibold"
      >
        Daftar
      </Link>
    </nav>

    <!-- Header -->
    <header class="p-8 text-center z-10 relative pt-20">
      <div class="flex justify-center mb-8">
        <div class="relative group">
          <div class="w-28 h-28 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center shadow-2xl animate-pulse-slow group-hover:scale-110 transition-transform duration-300">
            <svg class="w-14 h-14 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
            </svg>
          </div>
          <div class="absolute -top-2 -right-2 w-10 h-10 bg-green-500 rounded-full flex items-center justify-center animate-bounce">
            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
            </svg>
          </div>
          <!-- Pulsing rings -->
          <div class="absolute inset-0 rounded-full border-4 border-blue-300 opacity-30 -z-10"></div>
          <div class="absolute inset-0 rounded-full border-2 border-blue-400 opacity-40 -z-10"></div>
        </div>
      </div>
      <h1 class="relative z-20 inline-block px-4 py-2 rounded-2xl bg-white/90 backdrop-blur-sm text-6xl md:text-8xl font-extrabold text-blue-700 drop-shadow-lg mb-6 tracking-tight">
        Sistem Informasi Klinik
      </h1>
      <p class="text-2xl md:text-3xl text-gray-700 font-bold mb-3">Solusi Terintegrasi untuk Pelayanan Kesehatan</p>
      <p class="text-lg md:text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">Kelola pasien, rekam medis, jadwal dokter, dan administrasi klinik dengan mudah, aman, dan efisien</p>
      
      <!-- CTA Buttons -->
      <div class="mt-8 flex flex-col sm:flex-row gap-4 justify-center items-center">
        <Link
          :href="route('login')"
          class="px-8 py-4 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl font-bold text-lg shadow-xl hover:shadow-2xl hover:from-blue-700 hover:to-indigo-700 transition-all duration-300 transform hover:-translate-y-1"
        >
          Mulai Sekarang
        </Link>
        <Link
          :href="route('register')"
          class="px-8 py-4 bg-white text-blue-600 rounded-xl font-bold text-lg shadow-xl hover:shadow-2xl border-2 border-blue-600 hover:bg-blue-50 transition-all duration-300 transform hover:-translate-y-1"
        >
          Pelajari Lebih Lanjut
        </Link>
      </div>
    </header>

    <!-- Content -->
    <main class="flex-1 flex flex-col items-center justify-center z-10">
      <!-- Klinik Menu -->
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6 w-full max-w-6xl px-6 py-8">
        <template v-for="item in menuKlinik" :key="item.title">
          <Link
            v-if="route().has(item.route)"
            :href="route(item.route)"
            class="group bg-white/80 backdrop-blur-md shadow-xl rounded-2xl p-6 flex flex-col items-center text-center border border-gray-200 hover:border-blue-300 transition-all duration-300 cursor-pointer hover:-translate-y-1 relative overflow-hidden hover:shadow-2xl"
            :class="item.shadow"
          >
            <div :class="`absolute top-0 left-0 w-full h-1.5 rounded-t-2xl bg-gradient-to-r ${item.color}`"></div>
            <div class="mb-4">
              <div :class="`w-16 h-16 flex items-center justify-center rounded-xl bg-gradient-to-br ${item.color} text-white text-3xl shadow-lg group-hover:scale-110 transition-transform duration-200`">
                {{ item.icon }}
              </div>
            </div>
            <h2 class="text-xl font-bold text-gray-800 mb-2 group-hover:text-blue-600 transition-colors duration-200">
              {{ item.title }}
            </h2>
            <p class="text-sm text-gray-600 font-medium">{{ item.desc }}</p>
          </Link>
          <div
            v-else
            class="group bg-white/60 backdrop-blur-md shadow-xl rounded-2xl p-6 flex flex-col items-center text-center border border-gray-200 relative overflow-hidden opacity-75"
            :class="item.shadow"
          >
            <div :class="`absolute top-0 left-0 w-full h-1.5 rounded-t-2xl bg-gradient-to-r ${item.color}`"></div>
            <div class="mb-4">
              <div :class="`w-16 h-16 flex items-center justify-center rounded-xl bg-gradient-to-br ${item.color} text-white text-3xl shadow-lg`">
                {{ item.icon }}
              </div>
            </div>
            <h2 class="text-xl font-bold text-gray-600 mb-2">
              {{ item.title }}
            </h2>
            <p class="text-sm text-gray-500 font-medium">{{ item.desc }}</p>
            <div class="mt-2 text-xs text-gray-400">Coming Soon</div>
          </div>
        </template>
      </div>

      <!-- Stats Section -->
      <div class="mt-16 max-w-5xl w-full mx-auto px-6">
        <h3 class="text-4xl font-bold text-center text-gray-800 mb-12">Pencapaian Kami</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
          <div
            v-for="stat in stats"
            :key="stat.label"
            class="bg-white/80 backdrop-blur-md rounded-2xl p-8 text-center shadow-xl hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 border border-gray-100 group"
          >
            <div class="text-5xl mb-4 group-hover:scale-110 transition-transform duration-300">{{ stat.icon }}</div>
            <div class="text-4xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600 mb-2">{{ stat.number }}</div>
            <div class="text-lg font-semibold text-gray-700">{{ stat.label }}</div>
          </div>
        </div>
      </div>

      <!-- Features Section -->
      <div class="mt-16 max-w-6xl w-full mx-auto px-6">
        <h3 class="text-4xl font-bold text-center text-gray-800 mb-12">Fitur Unggulan</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
          <div
            v-for="feature in features"
            :key="feature.title"
            class="bg-white/80 backdrop-blur-md rounded-2xl p-8 text-center shadow-xl hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 border border-gray-100 group relative overflow-hidden"
          >
            <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-blue-500/10 to-indigo-500/10 rounded-full -translate-y-10 translate-x-10"></div>
            <div class="text-5xl mb-6 group-hover:scale-110 transition-transform duration-300">{{ feature.icon }}</div>
            <div class="text-2xl font-bold text-blue-600 mb-2">{{ feature.stat }}</div>
            <h4 class="text-xl font-bold text-gray-800 mb-3">{{ feature.title }}</h4>
            <p class="text-gray-600 leading-relaxed">{{ feature.desc }}</p>
          </div>
        </div>
      </div>

      <!-- Testimonials Section -->
      <div class="mt-20 max-w-6xl w-full mx-auto px-6">
        <h3 class="text-4xl font-bold text-center text-gray-800 mb-12">Apa Kata Mereka</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <div
            v-for="testimonial in testimonials"
            :key="testimonial.name"
            class="bg-white/90 backdrop-blur-md rounded-2xl p-8 shadow-xl hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 border border-gray-100"
          >
            <div class="text-4xl mb-4 text-center">{{ testimonial.avatar }}</div>
            <p class="text-gray-700 italic mb-6 leading-relaxed">"{{ testimonial.content }}"</p>
            <div class="text-center">
              <div class="font-bold text-gray-800 text-lg">{{ testimonial.name }}</div>
              <div class="text-blue-600 font-medium">{{ testimonial.role }}</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Custom Banner: Angkatan 2025 -->
      <div class="mt-20 max-w-4xl w-full mx-auto">
        <div class="bg-white/95 backdrop-blur-md rounded-3xl shadow-2xl p-12 border border-gray-200 text-center">
          <h2 class="text-3xl md:text-4xl font-extrabold text-blue-700">Angkatan 2025 SMKN 1 Bontang</h2>
          <p class="mt-2 text-gray-600">Selamat dan sukses untuk seluruh siswa angkatan 2025</p>
        </div>
      </div>
    </main>

    <!-- Footer -->
    <footer class="p-8 text-center bg-gradient-to-r from-gray-50 to-blue-50 backdrop-blur-md text-gray-700 z-10 shadow-inner relative border-t border-gray-200">
      <div class="max-w-4xl mx-auto">
        <div class="flex items-center justify-center gap-3 mb-4">
          <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center">
            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/>
            </svg>
          </div>
          <span class="text-xl font-bold text-gray-800">Sistem Informasi Klinik</span>
        </div>
        <div class="text-lg font-semibold text-gray-600 mb-2">© 2025 - Laravel 12 + Vue 3</div>
        <div class="text-sm text-gray-500 mb-3">Dibuat dengan <span class="text-red-500 text-lg">❤</span> untuk pelayanan kesehatan yang lebih baik</div>
        <div class="flex flex-wrap justify-center gap-6 text-xs text-gray-400">
          <span>🔒 HIPAA Compliant</span>
          <span>•</span>
          <span>🌐 Cloud Ready</span>
          <span>•</span>
          <span>📱 Mobile Responsive</span>
          <span>•</span>
          <span>⚡ Real-time Updates</span>
        </div>
      </div>
    </footer>
  </div>
</template>

<style scoped>
@keyframes pulse-slow {
  0%, 100% { 
    transform: scale(1);
    opacity: 1;
  }
  50% { 
    transform: scale(1.05);
    opacity: 0.8;
  }
}
.animate-pulse-slow {
  animation: pulse-slow 3s ease-in-out infinite;
}

@keyframes float {
  0%, 100% { 
    transform: translateY(0px) rotate(0deg);
  }
  50% { 
    transform: translateY(-15px) rotate(5deg);
  }
}
.animate-float {
  animation: float 6s ease-in-out infinite;
}

@keyframes slideInUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-slide-in-up {
  animation: slideInUp 0.6s ease-out;
}

@keyframes fadeInScale {
  from {
    opacity: 0;
    transform: scale(0.9);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}

.animate-fade-in-scale {
  animation: fadeInScale 0.8s ease-out;
}

/* Medical theme hover effects */
.group:hover .medical-glow {
  box-shadow: 0 0 20px rgba(59, 130, 246, 0.3);
}

/* Gradient text animation */
.gradient-text {
  background: linear-gradient(-45deg, #3b82f6, #6366f1, #8b5cf6, #06b6d4);
  background-size: 400% 400%;
  animation: gradient-shift 4s ease infinite;
}

@keyframes gradient-shift {
  0% { background-position: 0% 50%; }
  50% { background-position: 100% 50%; }
  100% { background-position: 0% 50%; }
}
</style>