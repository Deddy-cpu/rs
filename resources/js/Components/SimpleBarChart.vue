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
        <div class="text-xs text-gray-600 mt-2 text-center">
          {{ data.labels[index] }}
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

defineProps<Props>()
</script>
