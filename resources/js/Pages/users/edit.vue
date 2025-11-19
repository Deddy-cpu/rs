<script setup lang="ts">
import { ref, computed } from 'vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import PasswordInput from '@/Components/PasswordInput.vue'
import { Head, router, usePage } from '@inertiajs/vue3'

const page = usePage()
const user: any = page.props.user

// --- POLIS MODAL ---
// Perbaikan: jika data poli tidak ada, pakai fallback opsi default
const defaultPolis = [
  { id: '1', nama: 'Poli Umum' },
  { id: '2', nama: 'Poli Gigi' },
  { id: '3', nama: 'Poli Anak' },
  { id: '4', nama: 'Poli Kandungan' },
  { id: '5', nama: 'Poli Mata' },
  { id: '6', nama: 'Poli THT' },
  { id: '7', nama: 'UGD' },
  { id: '8', nama: 'Rawat Inap' },
  { id: '9', nama: 'Laboratorium' },
  { id: '10', nama: 'Farmasi' }
]

const polis = computed(() => {
  const polisRaw = page.props.polis
  if (Array.isArray(polisRaw) && polisRaw.length > 0) {
    if (typeof polisRaw[0] === 'object' && ('nama' in polisRaw[0] || 'name' in polisRaw[0] || 'poli' in polisRaw[0] || 'ruangan' in polisRaw[0])) {
      return polisRaw.map((p, idx) => ({
        id: p.id ?? String(idx+1),
        nama: p.nama ?? p.name ?? p.poli ?? p.ruangan ?? 'Poli ' + (idx+1)
      }))
    }

    if (typeof polisRaw[0] === 'string') {
      return polisRaw.map((nama, idx) => ({ id: String(idx+1), nama }))
    }
  }

  // fallback jika tidak ada data dari server
  return defaultPolis
})

const selectedPoli = ref(null)
const showPoliModal = ref(false)

const getInitRuanganId = () => {
  const initRuangan = user.dokter?.ruangan_id || user.perawat?.ruangan_id || user.ruangan_id
  if (initRuangan) return String(initRuangan)
  const initNamaRuangan = user.dokter?.ruangan || user.perawat?.ruangan || user.ruangan || ''
  if (initNamaRuangan) {
    const found = polis.value.find(p => p.nama == initNamaRuangan)
    return found ? String(found.id) : ''
  }
  return ''
}

const form = ref({
  name: user.name || '',
  role: user.role || '',
  email: user.email || '',
  ruangan: getInitRuanganId(),
  password: '',
  password_confirmation: ''
})
const errors = ref<any>({})
const showPopup = ref(false)
const popupMessage = ref("")
const popupType = ref("success")
const showConfirm = ref(false)

function openConfirmModal() {
  showConfirm.value = true
}

function closeConfirmModal() {
  showConfirm.value = false
}

function handleRoleChange() {
  if (form.value.role !== 'dokter' && form.value.role !== 'perawat') {
    form.value.ruangan = ''
  }
}

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

function confirmUpdate() {
  errors.value = {}

  // Explicitly type submitForm to allow extra properties like ruangan_id
  let submitForm: any = { ...form.value }
  if (form.value.role === 'dokter' || form.value.role === 'perawat') {
    const poliObj = polis.value.find(p => String(p.id) === String(form.value.ruangan))
    if (poliObj) {
      submitForm.ruangan_id = poliObj.id
      submitForm.ruangan = poliObj.nama
    } else {
      submitForm.ruangan_id = ''
      submitForm.ruangan = ''
    }
  } else {
    delete submitForm.ruangan_id
    submitForm.ruangan = ''
  }

  router.put(`/users/${user.id}`, submitForm, {
    onSuccess: () => {
      popupMessage.value = "User berhasil diperbarui!"
      popupType.value = "success"
      showPopup.value = true
      setTimeout(() => {
        showPopup.value = false
        router.visit('/users')
      }, 2000)
    },
    onError: (newErrors) => {
      errors.value = { ...newErrors }
      popupMessage.value = "Gagal memperbarui user!"
      popupType.value = "error"
      showPopup.value = true
      setTimeout(() => {
        showPopup.value = false
      }, 3000)
    }
  })
  closeConfirmModal()
}
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Edit User" />

    <div
      class="min-h-screen bg-cover bg-center flex items-center justify-center p-6"
      style="background-image: url('/images/bg-login.png')"
    >
      <div class="max-w-lg w-full bg-white/70 backdrop-blur-md shadow-lg rounded-lg p-6">
        <h1 class="text-2xl font-bold mb-4 text-blue-700">Edit User</h1>

        <!-- Notifikasi popup -->
        <div
          v-if="showPopup"
          :class="[
            'mb-4 px-4 py-3 rounded text-center transition-all duration-300',
            popupType === 'success' ? 'bg-green-100 text-green-800 border border-green-300' : 'bg-red-100 text-red-700 border border-red-300'
          ]"
        >
          {{ popupMessage }}
        </div>

        <form @submit.prevent="openConfirmModal" class="space-y-4">
          <!-- Name -->
          <div>
            <label class="block text-sm font-medium text-gray-700">Name</label>
            <input v-model="form.name" type="text"
                   class="mt-1 block w-full border rounded-lg px-3 py-2 shadow-sm focus:ring-blue-500 focus:border-blue-500"
                   :class="{'border-red-500': errors.name}"
            />
            <div v-if="errors.name" class="text-xs text-red-500 mt-1">{{ errors.name }}</div>
          </div>

          <!-- Role -->
          <div>
            <label class="block text-sm font-medium text-gray-700">Role</label>
            <select v-model="form.role"
                    @change="handleRoleChange"
                    class="mt-1 block w-full border rounded-lg px-3 py-2 shadow-sm focus:ring-blue-500 focus:border-blue-500"
                    :class="{'border-red-500': errors.role}"
            >
              <option disabled value="">Pilih Role</option>
              <option value="admin">Admin</option>
              <option value="kasir">Kasir</option>
              <option value="dokter">Dokter</option>
              <option value="pendaftaran">Pendaftaran</option>
              <option value="perawat">Perawat</option>
              <option value="manajemen">Manajemen</option>
            </select>
            <div v-if="errors.role" class="text-xs text-red-500 mt-1">{{ errors.role }}</div>
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
            <div v-if="errors.ruangan" class="text-xs text-red-500 mt-1">{{ errors.ruangan }}</div>
          </div>

          <!-- Email -->
          <div>
            <label class="block text-sm font-medium text-gray-700">Email</label>
            <input v-model="form.email" type="email"
                   class="mt-1 block w-full border rounded-lg px-3 py-2 shadow-sm focus:ring-blue-500 focus:border-blue-500"
                   :class="{'border-red-500': errors.email}"
            />
            <div v-if="errors.email" class="text-xs text-red-500 mt-1">{{ errors.email }}</div>
          </div>

          <!-- Password -->
          <div>
            <label class="block text-sm font-medium text-gray-700">Password (Kosongkan jika tidak ingin ubah)</label>
            <PasswordInput
              v-model="form.password"
              :class="`mt-1 w-full border rounded-lg px-3 py-2${errors.password ? ' border-red-500' : ''}`"
              autocomplete="new-password"
            />
            <div v-if="errors.password" class="text-xs text-red-500 mt-1">{{ errors.password }}</div>
          </div>

          <!-- Confirm Password -->
          <div>
            <label class="block text-sm font-medium text-gray-700">Confirm Password</label>
            <PasswordInput        
              v-model="form.password_confirmation"
              :class="`mt-1 w-full border rounded-lg px-3 py-2${errors.password_confirmation ? ' border-red-500' : ''}`"
              autocomplete="new-password"
            />
            <div v-if="errors.password_confirmation" class="text-xs text-red-500 mt-1">{{ errors.password_confirmation }}</div>
          </div>

          <div class="flex justify-end">
            <button type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
              Update
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal Konfirmasi -->
    <div v-if="showConfirm" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
      <div class="bg-white/90 backdrop-blur-md rounded-lg shadow-lg p-6 w-full max-w-md">
        <h2 class="text-lg font-semibold mb-4 text-gray-800">Konfirmasi</h2>
        <p class="text-gray-600 mb-6">Apakah Anda yakin ingin mengedit user ini?</p>
        <div class="flex justify-end space-x-3">
          <button @click="closeConfirmModal"
                  class="px-4 py-2 rounded bg-gray-300 text-gray-800 hover:bg-gray-400 transition">
            Batal
          </button>
          <button @click="confirmUpdate"
                  class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700 transition">
            Ya, Update
          </button>
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
