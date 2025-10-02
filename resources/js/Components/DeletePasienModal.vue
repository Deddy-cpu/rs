<template>
  <AnimatedDeleteModal
    :show="show"
    :loading="loading"
    title="Hapus Data Pasien"
    subtitle="Tindakan ini akan menghapus semua data terkait"
    :message="`Apakah Anda yakin ingin menghapus data pasien <strong class='text-red-600'>${pasienName}</strong>?`"
    additional-info="Data yang akan dihapus meliputi: data pasien, konsultasi, tindakan, alkes, resep, dan transaksi terkait."
    confirm-text="Ya, Hapus Pasien"
    @confirm="onConfirm"
    @cancel="onCancel"
  />
</template>

<script setup>
import AnimatedDeleteModal from './AnimatedDeleteModal.vue'

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  pasienName: {
    type: String,
    default: ''
  },
  loading: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['confirm', 'cancel'])

const onConfirm = () => {
  emit('confirm')
}

const onCancel = () => {
  emit('cancel')
}
</script>
