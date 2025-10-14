<template>
  <AuthenticatedLayout>
    <Head title="Print - Rincian Biaya Layanan" />
    
    <div class="min-h-screen bg-gray-50 py-8">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6">
          <div class="flex justify-between items-center">
            <div>
              <h1 class="text-2xl font-bold text-gray-900">Rincian Biaya Layanan</h1>
              <p class="text-gray-600 mt-1">Print dan Download PDF</p>
            </div>
            <div class="flex gap-3">
              <button
                @click="printPage"
                class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition flex items-center"
              >
                <i class="fas fa-print mr-2"></i>
                Print
              </button>
           
              <Link 
                :href="route('kasir.index')"
                class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600"
              >
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali
              </Link>
            </div>
          </div>
        </div>

        <!-- Print Content -->
        <div id="print-content" class="bg-white shadow-sm rounded-lg p-8">
          <!-- Header -->
          <div class="text-center mb-8">
            <div class="flex items-center justify-center mb-4">
              <img :src="'/Images/logo_smal.png'" alt="Logo" class="w-20 h-20 mr-4">
              <div class="text-left">
                <h3 class="text-lg font-bold mb-1">RS LNG BADAK</h3>
                <p class="text-sm text-gray-600">Kel. Satimpo, Kec. Bontang Selatan, Bontang - 75324, Kalimantan Timur</p>
              </div>
            </div>
            <h4 class="text-lg font-bold">RINCIAN BIAYA LAYANAN</h4>
          </div>

          <!-- Patient Information -->
          <div class="mb-6">
            <table class="w-full border-collapse">
              <tbody>
                <tr>
                  <td class="py-2 pr-4">No Registrasi : {{ kunjungan.no_reg }}</td>
                  <td class="py-2">No Invoice : {{ kunjungan.no_inv || 'INV-'+kunjungan.id }}</td>
                </tr>
                <tr>
                  <td class="py-2 pr-4">Tanggal : {{ formatDate(kunjungan.tgl_reg) }}</td>
                  <td class="py-2">Tanggal Invoice : {{ formatDate(kunjungan.tgl_inv || new Date()) }}</td>
                </tr>
                <tr>
                  <td class="py-2 pr-4">Nama Pasien : {{ kunjungan.nm_p }}</td>
                  <td class="py-2">Perawatan : {{ kunjungan.perawatan }}</td>
                </tr>
                <tr>
                  <td class="py-2 pr-4">MRN : {{ kunjungan.mrn || '-' }}</td>
                  <td class="py-2">Penjamin : {{ kunjungan.penjamin }}</td>
                </tr>
                <tr>
                  <td class="py-2 pr-4">Alamat : {{ kunjungan.almt_B }}</td>
                  <td class="py-2">No. SJP : {{ kunjungan.no_sjp || '-' }}</td>
                </tr>
                <tr>
                  <td class="py-2 pr-4"></td>
                  <td class="py-2">ICD : {{ kunjungan.icd || '-' }}</td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Konsul Section -->
          <div v-if="kunjungan.konsuls && kunjungan.konsuls.length > 0" class="mb-6">
            <h5 class="font-bold mb-3">Konsul</h5>
            <table class="w-full border-collapse border border-gray-300">
              <thead>
                <tr class="bg-gray-100">
                  <th class="border border-gray-300 px-2 py-1 text-center">Tanggal</th>
                  <th class="border border-gray-300 px-2 py-1 text-center">Deskripsi</th>
                  <th class="border border-gray-300 px-2 py-1 text-center">Jml</th>
                  <th class="border border-gray-300 px-2 py-1 text-center">Biaya</th>
                  <th class="border border-gray-300 px-2 py-1 text-center">Disc</th>
                  <th class="border border-gray-300 px-2 py-1 text-center">Subtotal (Rp)</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="row in kunjungan.konsuls" :key="row.id">
                  <td class="border border-gray-300 px-2 py-1 text-center">{{ formatDate(row.tanggal) }}</td>
                  <td class="border border-gray-300 px-2 py-1">{{ row.dskp_kons }}</td>
                  <td class="border border-gray-300 px-2 py-1 text-center">{{ row.jmlh_kons }}</td>
                  <td class="border border-gray-300 px-2 py-1 text-right">{{ formatCurrency(row.bya_kons) }}</td>
                  <td class="border border-gray-300 px-2 py-1 text-center">{{ row.disc_kons }}</td>
                  <td class="border border-gray-300 px-2 py-1 text-right">{{ formatCurrency(calculateSubtotal(row.jmlh_kons, row.bya_kons, row.disc_kons)) }}</td>
                </tr>
              </tbody>
            </table>
            <div class="text-right font-bold mt-2">
              Subtotal Konsul: Rp {{ formatCurrency(totals.konsul) }}
            </div>
          </div>

          <!-- Tindakan Section -->
          <div v-if="kunjungan.tindaks && kunjungan.tindaks.length > 0" class="mb-6">
            <h5 class="font-bold mb-3">Tindakan</h5>
            <table class="w-full border-collapse border border-gray-300">
              <thead>
                <tr class="bg-gray-100">
                  <th class="border border-gray-300 px-2 py-1 text-center">Tanggal</th>
                  <th class="border border-gray-300 px-2 py-1 text-center">Deskripsi</th>
                  <th class="border border-gray-300 px-2 py-1 text-center">Jml</th>
                  <th class="border border-gray-300 px-2 py-1 text-center">Biaya</th>
                  <th class="border border-gray-300 px-2 py-1 text-center">Disc</th>
                  <th class="border border-gray-300 px-2 py-1 text-center">Subtotal (Rp)</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="row in kunjungan.tindaks" :key="row.id">
                  <td class="border border-gray-300 px-2 py-1 text-center">{{ formatDate(row.tanggal) }}</td>
                  <td class="border border-gray-300 px-2 py-1">{{ row.dskp_tindak }}</td>
                  <td class="border border-gray-300 px-2 py-1 text-center">{{ row.jmlh_tindak }}</td>
                  <td class="border border-gray-300 px-2 py-1 text-right">{{ formatCurrency(row.bya_tindak) }}</td>
                  <td class="border border-gray-300 px-2 py-1 text-center">{{ row.disc_tindak }}</td>
                  <td class="border border-gray-300 px-2 py-1 text-right">{{ formatCurrency(calculateSubtotal(row.jmlh_tindak, row.bya_tindak, row.disc_tindak)) }}</td>
                </tr>
              </tbody>
            </table>
            <div class="text-right font-bold mt-2">
              Subtotal Tindakan: Rp {{ formatCurrency(totals.tindak) }}
            </div>
          </div>

          <!-- Alkes Section -->
          <div v-if="kunjungan.alkes && kunjungan.alkes.length > 0" class="mb-6">
            <h5 class="font-bold mb-3">Alkes</h5>
            <table class="w-full border-collapse border border-gray-300">
              <thead>
                <tr class="bg-gray-100">
                  <th class="border border-gray-300 px-2 py-1 text-center">Tanggal</th>
                  <th class="border border-gray-300 px-2 py-1 text-center">Deskripsi</th>
                  <th class="border border-gray-300 px-2 py-1 text-center">Jml</th>
                  <th class="border border-gray-300 px-2 py-1 text-center">Biaya</th>
                  <th class="border border-gray-300 px-2 py-1 text-center">Disc</th>
                  <th class="border border-gray-300 px-2 py-1 text-center">Subtotal (Rp)</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="row in kunjungan.alkes" :key="row.id">
                  <td class="border border-gray-300 px-2 py-1 text-center">{{ formatDate(row.tanggal) }}</td>
                  <td class="border border-gray-300 px-2 py-1">{{ row.dskp_alkes }}</td>
                  <td class="border border-gray-300 px-2 py-1 text-center">{{ row.jmlh_alkes }}</td>
                  <td class="border border-gray-300 px-2 py-1 text-right">{{ formatCurrency(row.bya_alkes) }}</td>
                  <td class="border border-gray-300 px-2 py-1 text-center">{{ row.disc_alkes }}</td>
                  <td class="border border-gray-300 px-2 py-1 text-right">{{ formatCurrency(calculateSubtotal(row.jmlh_alkes, row.bya_alkes, row.disc_alkes)) }}</td>
                </tr>
              </tbody>
            </table>
            <div class="text-right font-bold mt-2">
              Subtotal Alkes: Rp {{ formatCurrency(totals.alkes) }}
            </div>
          </div>

          <!-- Resep Section -->
          <div v-if="kunjungan.rsp && kunjungan.rsp.length > 0" class="mb-6">
            <h5 class="font-bold mb-3">Resep</h5>
            <table class="w-full border-collapse border border-gray-300">
              <thead>
                <tr class="bg-gray-100">
                  <th class="border border-gray-300 px-2 py-1 text-center">Tanggal</th>
                  <th class="border border-gray-300 px-2 py-1 text-center">Deskripsi</th>
                  <th class="border border-gray-300 px-2 py-1 text-center">Jml</th>
                  <th class="border border-gray-300 px-2 py-1 text-center">Biaya</th>
                  <th class="border border-gray-300 px-2 py-1 text-center">Disc</th>
                  <th class="border border-gray-300 px-2 py-1 text-center">Subtotal (Rp)</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="row in kunjungan.rsp" :key="row.id">
                  <td class="border border-gray-300 px-2 py-1 text-center">{{ formatDate(row.tanggal) }}</td>
                  <td class="border border-gray-300 px-2 py-1">{{ row.dskp_rsp }}</td>
                  <td class="border border-gray-300 px-2 py-1 text-center">{{ row.jmlh_rsp }}</td>
                  <td class="border border-gray-300 px-2 py-1 text-right">{{ formatCurrency(row.bya_rsp) }}</td>
                  <td class="border border-gray-300 px-2 py-1 text-center">{{ row.disc_rsp }}</td>
                  <td class="border border-gray-300 px-2 py-1 text-right">{{ formatCurrency(calculateSubtotal(row.jmlh_rsp, row.bya_rsp, row.disc_rsp)) }}</td>
                </tr>
              </tbody>
            </table>
            <div class="text-right font-bold mt-2">
              Subtotal Resep: Rp {{ formatCurrency(totals.rsp) }}
            </div>
          </div>

          <!-- Lain-lain Section -->
          <div v-if="kunjungan.lainnyas && kunjungan.lainnyas.length > 0" class="mb-6">
            <h5 class="font-bold mb-3">Lain-lain</h5>
            <table class="w-full border-collapse border border-gray-300">
              <thead>
                <tr class="bg-gray-100">
                  <th class="border border-gray-300 px-2 py-1 text-center">Tanggal</th>
                  <th class="border border-gray-300 px-2 py-1 text-center">Deskripsi</th>
                  <th class="border border-gray-300 px-2 py-1 text-center">Jml</th>
                  <th class="border border-gray-300 px-2 py-1 text-center">Biaya</th>
                  <th class="border border-gray-300 px-2 py-1 text-center">Disc</th>
                  <th class="border border-gray-300 px-2 py-1 text-center">Subtotal (Rp)</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="row in kunjungan.lainnyas" :key="row.id">
                  <td class="border border-gray-300 px-2 py-1 text-center">{{ formatDate(row.tanggal) }}</td>
                  <td class="border border-gray-300 px-2 py-1">{{ row.dskp_lainnya }}</td>
                  <td class="border border-gray-300 px-2 py-1 text-center">{{ row.jmlh_lainnaya }}</td>
                  <td class="border border-gray-300 px-2 py-1 text-right">{{ formatCurrency(row.bya_lainnya) }}</td>
                  <td class="border border-gray-300 px-2 py-1 text-center">{{ row.disc_lainnya }}</td>
                  <td class="border border-gray-300 px-2 py-1 text-right">{{ formatCurrency(calculateSubtotal(row.jmlh_lainnaya, row.bya_lainnya, row.disc_lainnya)) }}</td>
                </tr>
              </tbody>
            </table>
            <div class="text-right font-bold mt-2">
              Subtotal Lain-lain: Rp {{ formatCurrency(totals.lainnya) }}
            </div>
          </div>

          <!-- Total Summary -->
          <div class="mt-8">
            <div class="flex justify-between">
              <div class="w-1/2">
                <p class="text-sm">
                  Terbilang: #{{ terbilang(grandTotal) }} Rupiah#
                </p>
              </div>
              <div class="w-1/2">
                <table class="w-full">
                  <tbody>
                    <tr>
                      <td class="py-1">Total (Rp)</td>
                      <td class="py-1">: {{ formatCurrency(Object.values(totals).reduce((a, b) => a + b, 0)) }}</td>
                    </tr>
                    <tr>
                      <td class="py-1">Discount (Rp)</td>
                      <td class="py-1">: {{ formatCurrency(0) }}</td>
                    </tr>
                    <tr>
                      <td class="py-1">Dp (Rp)</td>
                      <td class="py-1">: {{ formatCurrency(0) }}</td>
                    </tr>
                    <tr>
                      <td class="py-1 font-bold">Biaya yang Harus Dibayar</td>
                      <td class="py-1 font-bold">: {{ formatCurrency(grandTotal) }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <!-- Signature Section -->
          <div class="mt-12">
            <div class="flex justify-center gap-24">
              <div class="w-1/3 text-center flex flex-col items-center">
                <p class="mb-8">Pasien/ Keluarga Pasien </p>
                 <p class="gray-500"> <br>
                </br>
                <br>
                </br>
              
                  <p class="gray-500"> _____________________</p>
                </p>
              
              </div>
              <div class="w-1/3 text-center flex flex-col items-center">
                <p class="mb-2">Mengetahui</p>
                <p class="mb-8">Petugas Kasir</p>
                <div class="border-t border-gray-400 mt-8 w-40"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
  kunjungan: Object,
  totals: Object,
  grandTotal: Number
})

const formatDate = (date) => {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('id-ID')
}

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('id-ID').format(amount || 0)
}

// Calculate individual subtotal for each row
const calculateSubtotal = (jumlah, biaya, diskon) => {
  const jumlahNum = parseFloat(jumlah) || 0
  const biayaNum = parseFloat(biaya) || 0
  const subtotal = jumlahNum * biayaNum
  
  let diskonNum = 0
  if (diskon && diskon !== '0%') {
    const discPercent = parseFloat(String(diskon).replace(/[^\d]/g, '')) || 0
    diskonNum = (subtotal * discPercent) / 100
  }
  
  return Math.max(subtotal - diskonNum, 0)
}

const terbilang = (angka) => {
  const bilangan = ['', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan']
  const satuan = ['', 'ribu', 'juta', 'milyar', 'triliun']
  
  if (angka === 0) return 'nol'
  
  let hasil = ''
  let i = 0
  
  while (angka > 0) {
    const ribuan = angka % 1000
    if (ribuan !== 0) {
      let temp = ''
      const ratus = Math.floor(ribuan / 100)
      const puluh = Math.floor((ribuan % 100) / 10)
      const satu = ribuan % 10
      
      if (ratus > 0) {
        if (ratus === 1) {
          temp += 'seratus '
        } else {
          temp += bilangan[ratus] + ' ratus '
        }
      }
      
      if (puluh > 0) {
        if (puluh === 1) {
          if (satu === 0) {
            temp += 'sepuluh '
          } else if (satu === 1) {
            temp += 'sebelas '
          } else {
            temp += bilangan[satu] + ' belas '
          }
        } else {
          temp += bilangan[puluh] + ' puluh '
          if (satu > 0) {
            temp += bilangan[satu] + ' '
          }
        }
      } else if (satu > 0) {
        temp += bilangan[satu] + ' '
      }
      
      temp += satuan[i] + ' '
      hasil = temp + hasil
    }
    angka = Math.floor(angka / 1000)
    i++
  }
  
  return hasil.trim()
}

const printPage = () => {
  const printContent = document.getElementById('print-content')
  const originalContent = document.body.innerHTML
  
  document.body.innerHTML = printContent.outerHTML
  window.print()
  document.body.innerHTML = originalContent
  window.location.reload()
}
</script>

<style scoped>
@media print {
  .no-print {
    display: none !important;
  }
  
  body {
    font-family: Arial, sans-serif;
    font-size: 12px;
  }
  
  table {
    width: 100%;
    border-collapse: collapse;
  }
  
  th, td {
    border: 1px solid #000;
    padding: 4px;
    font-size: 11px;
  }
  
  th {
    text-align: center;
  }
}
</style>
