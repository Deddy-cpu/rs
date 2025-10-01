<template>
  <div class="bg-white rounded-lg shadow-lg p-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ title }}</h3>
    <div class="h-80 flex items-end justify-between space-x-1">
      <div 
        v-for="(label, index) in data.labels" 
        :key="index"
        class="flex flex-col items-center flex-1 space-y-1"
      >
        <!-- Multiple bars for each label -->
        <div class="flex flex-col space-y-1 w-full">
          <div 
            v-for="(dataset, datasetIndex) in data.datasets" 
            :key="datasetIndex"
            class="w-full rounded-t transition-all duration-500 hover:opacity-80"
            :style="{
              height: `${(dataset.data[index] / Math.max(...dataset.data)) * 100}%`,
              backgroundColor: dataset.backgroundColor,
              minHeight: '2px'
            }"
          ></div>
        </div>
        <div class="text-xs text-gray-600 mt-2 text-center">
          {{ label }}
        </div>
        <div class="text-xs font-semibold text-gray-800 mt-1">
          {{ data.datasets[0].data[index] + data.datasets[1].data[index] }}
        </div>
      </div>
    </div>
    <div class="mt-4 flex items-center justify-center space-x-4">
      <div 
        v-for="(dataset, index) in data.datasets" 
        :key="index"
        class="flex items-center space-x-2"
      >
        <div 
          class="w-4 h-4 rounded"
          :style="{ backgroundColor: dataset.backgroundColor }"
        ></div>
        <span class="text-sm text-gray-600">{{ dataset.label }}</span>
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
