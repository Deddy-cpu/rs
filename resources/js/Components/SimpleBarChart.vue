<template>
  <div class="bg-white rounded-lg shadow-lg p-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ title }}</h3>
    <div class="h-80 flex items-end justify-between space-x-2">
      <div 
        v-for="(value, index) in data.datasets[0].data" 
        :key="index"
        class="flex flex-col items-center flex-1"
      >
        <div 
          class="w-full rounded-t transition-all duration-500 hover:opacity-80"
          :style="{
            height: `${(value / Math.max(...data.datasets[0].data)) * 100}%`,
            backgroundColor: data.datasets[0].backgroundColor,
            minHeight: '4px'
          }"
        ></div>
        <div class="text-[10px] text-gray-600 mt-2 text-center w-full px-0.5 break-words leading-tight min-h-[2.5rem] max-h-[3rem] flex items-center justify-center overflow-hidden" :title="data.labels[index]">
          <span class="line-clamp-2">{{ formatLabel(data.labels[index]) }}</span>
        </div>
        <div class="text-xs font-semibold text-gray-800 mt-1">
          {{ value }}
        </div>
      </div>
    </div>
    <div class="mt-4 flex items-center justify-center">
      <div class="flex items-center space-x-2">
        <div 
          class="w-4 h-4 rounded"
          :style="{ backgroundColor: data.datasets[0].backgroundColor }"
        ></div>
        <span class="text-sm text-gray-600">{{ data.datasets[0].label }}</span>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
interface Props {
  title: string
  data: {
    labels: string[]
    datasets: {
      label: string
      data: number[]
      backgroundColor: string
      borderColor: string
      borderWidth: number
    }[]
  }
}

const props = defineProps<Props>()

// Helper function untuk format label - tidak dipotong, biarkan wrap natural
const formatLabel = (label: string) => {
  return label
}
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  word-break: break-word;
}
</style>
