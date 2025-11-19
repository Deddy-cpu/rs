<script setup lang="ts">
import { ref, computed } from 'vue'
import { Head, router, usePage } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import PasswordInput from '@/Components/PasswordInput.vue'

const page = usePage()

// --- POLIS MODAL ---
// Menggunakan data polis dari table polis
const polis = computed(() => {
  const polisRaw = page.props.polis
  if (Array.isArray(polisRaw) && polisRaw.length > 0) {
    // Data dari table polis memiliki struktur: { id, poli_desc }
    return polisRaw.map((p) => ({
      id: String(p.id),
      nama: p.poli_desc || p.nama || p.name || ''
    }))
  }

  // fallback jika tidak ada data dari server
  return []
})

const selectedPoli = ref(null)
const showPoliModal = ref(false)

const form = ref({
  name: '',
  email: '',
  password: '',
  role: '',
  ruangan: ''
})

const success = ref(false)

function openPoliModal() {
  selectedPoli.value = form.value.ruangan || null
  showPoliModal.value = true
}

function closePoliModal() {
  showPoliModal.value = false
}

function confirmPoli() {
  form.value.ruangan = selectedPoli.value
  showPoliModal.value = false
}

function handleRoleChange() {
  if (form.value.role !== 'dokter' && form.value.role !== 'perawat') {
    form.value.ruangan = ''
  }
}

const submitForm = async () => {
  try {
    // Prepare form data with ruangan_id
    let submitData: any = { ...form.value }
    if (form.value.role === 'dokter' || form.value.role === 'perawat') {
      const poliObj = polis.value.find(p => String(p.id) === String(form.value.ruangan))
      if (poliObj) {
        submitData.ruangan_id = poliObj.id
        submitData.ruangan = poliObj.nama
      } else {
        submitData.ruangan_id = ''
        submitData.ruangan = ''
      }
    } else {
      delete submitData.ruangan_id
      submitData.ruangan = ''
    }

    await router.post('/users', submitData, {
      onSuccess: () => {
        success.value = true
        form.value = { name: '', email: '', password: '', role: '', ruangan: '' }
        
        setTimeout(() => {
          success.value = false
          router.visit('/users')
        }, 1500)
      },
      onError: (errors) => {
        console.error('Validation errors:', errors)
        alert("Gagal menambahkan user!")
      }
    })
  } catch (error) {
    console.error(error)
    alert("Gagal menambahkan user!")
  }
}
</script>

<template>
  <Head title="Tambah User" />
  <AuthenticatedLayout>
    <div
      class="min-h-screen bg-cover bg-center flex items-center justify-center p-6"
      style="background-image: url('/images/bg-login.png')"
    >
      <div class="max-w-md w-full bg-white/70 backdrop-blur-md shadow-lg rounded-xl p-6">
        <!-- Header -->
        <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Tambah User</h2>

        <!-- Form -->
        <form @submit.prevent="submitForm" class="space-y-4">
          <!-- Name -->
          <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Nama</label>
            <input
              v-model="form.name"
              id="name"
              type="text"
              required
              class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg 
                     focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            />
          </div>

          <!-- Role -->
          <div>
            <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
            <select
              v-model="form.role"
              id="role"
              required
              @change="handleRoleChange"
              class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg 
                     focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            >
              <option value="" disabled>Pilih Role</option>
              <option value="admin">Admin</option>
              <option value="dokter">Dokter</option>
              <option value="kasir">Kasir</option>
              <option value="pendaftaran">Pendaftaran</option>
              <option value="perawat">Perawat</option>
              <option value="manajemen">Manajemen</option>
              <option value="kosong">Kosong</option>
            </select>
          </div>

          <!-- Ruangan/Poli (hanya untuk dokter dan perawat) -->
          <div v-if="form.role === 'dokter' || form.role === 'perawat'">
            <label class="block text-sm font-medium text-gray-700">
              Ruangan/Poli <span class="text-gray-500 text-xs">(Opsional)</span>
            </label>
            <div class="flex space-x-2">
              <button type="button"
                class="mt-1 px-3 py-2 border rounded-lg bg-blue-50 text-blue-800 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-blue-400"
                @click="openPoliModal">
                {{ 
                  polis.find(p => String(p.id) === String(form.ruangan)) 
                  ? polis.find(p => String(p.id) === String(form.ruangan)).nama 
                  : 'Pilih Poli/Ruangan' 
                }}
              </button>
              <button
                v-if="form.ruangan"
                type="button"
                class="mt-1 px-2 py-2 rounded-lg border border-gray-300 text-gray-400 hover:text-red-400"
                @click="form.ruangan=''"
                title="Hapus pilihan"
              >
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <p class="mt-1 text-xs text-gray-500">
              Jika diisi, dokter/perawat hanya akan melihat pasien dari ruangan/poli ini
            </p>
          </div>

          <!-- Email -->
          <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
            <input
              v-model="form.email"
              id="email"
              type="email"
              required
              class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg 
                     focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            />
          </div>

          <!-- Password -->
          <div>
            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
            <PasswordInput
              id="password"
              v-model="form.password"
              class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              required
              autocomplete="new-password"
            />
          </div>

          <!-- Buttons (Kembali + Add User) -->
          <div class="flex justify-between gap-2">
            <button
              type="button"
              @click="router.visit('/users')"
              class="w-1/2 bg-gray-200 text-gray-700 py-2 px-4 rounded-lg 
                     hover:bg-gray-300 transition font-medium"
            >
              ⬅ Kembali
            </button>

            <button
              type="submit"
              class="w-1/2 bg-green-600 text-white py-2 px-4 rounded-lg 
                     hover:bg-green-700 transition font-semibold"
            >
              Tambah User
            </button>
          </div>
        </form>

        <!-- Success -->
        <div
          v-if="success"
          class="mt-4 p-3 rounded-lg bg-green-100 text-green-700 text-sm text-center"
        >
          User berhasil ditambahkan.
        </div>
      </div>
    </div>

    <!-- Modal Pilih Poli/Ruangan -->
    <div v-if="showPoliModal" class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-40">
      <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
        <h2 class="text-lg font-semibold mb-4 text-gray-800">Pilih Poli/Ruangan</h2>
        <div class="space-y-2 max-h-64 overflow-y-auto">
          <button
            v-for="poli in polis"
            :key="poli.id"
            type="button"
            @click="selectedPoli = String(poli.id)"
            :class="[
              'w-full text-left px-4 py-2 rounded-md border flex items-center space-x-2',
              String(selectedPoli) === String(poli.id)
                ? 'border-blue-500 bg-blue-100 text-blue-900 font-bold'
                : 'border-gray-200 hover:bg-blue-50'
            ]"
          >
            <span>{{ poli.nama }}</span>
            <span v-if="String(selectedPoli) === String(poli.id)" class="ml-auto text-blue-700">✓</span>
          </button>
          <div v-if="!polis || polis.length === 0" class="text-gray-400 py-2 px-4">Data poli/ruangan tidak ditemukan.</div>
        </div>
        <div class="flex justify-end space-x-2 mt-6">
          <button @click="closePoliModal"
                  class="px-4 py-2 rounded bg-gray-200 text-gray-700 hover:bg-gray-300 transition">
            Batal
          </button>
          <button
            @click="confirmPoli"
            class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700 transition"
            :disabled="!selectedPoli"
            :class="{ 'opacity-60 pointer-events-none': !selectedPoli }"
          >
            Pilih
          </button>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
