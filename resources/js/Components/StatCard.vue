<template>
  <div class="bg-white rounded-xl shadow-lg p-6 border-l-4" :class="borderColor">
    <div class="flex items-center justify-between">
      <div>
        <p class="text-sm font-medium text-gray-600">{{ title }}</p>
        <p class="text-3xl font-bold text-gray-900">{{ value }}</p>
        <p class="text-sm text-gray-500 mt-1">{{ subtitle }}</p>
      </div>
      <div class="p-3 rounded-full" :class="iconBgColor">
        <span class="text-2xl">{{ icon }}</span>
      </div>
    </div>
    <div v-if="trend" class="mt-4 flex items-center">
      <span :class="trendColor" class="text-sm font-medium">
        {{ trend > 0 ? '↗' : '↘' }} {{ Math.abs(trend) }}%
      </span>
      <span class="text-sm text-gray-500 ml-2">dari bulan lalu</span>
    </div>
  </div>
</template>

<script setup lang="ts">
interface Props {
  title: string
  value: string | number
  subtitle?: string
  icon: string
  borderColor: string
  iconBgColor: string
  trend?: number
  trendColor?: string
}

const props = withDefaults(defineProps<Props>(), {
  subtitle: '',
  trend: 0,
  trendColor: 'text-green-600'
})
</script>
