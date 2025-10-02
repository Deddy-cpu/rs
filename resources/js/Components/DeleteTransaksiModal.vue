<template>
  <AnimatedDeleteModal
    :show="show"
    :loading="loading"
    title="Hapus Transaksi"
    subtitle="Tindakan ini akan menghapus data transaksi"
    :message="`Apakah Anda yakin ingin menghapus <strong class='text-red-600'>transaksi ini</strong>?`"
    additional-info="Data transaksi yang dihapus tidak dapat dipulihkan kembali."
    confirm-text="Ya, Hapus Transaksi"
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
