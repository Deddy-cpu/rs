<template>
  <div class="w-full">
    <!-- Tab Headers -->
    <div class="border-b border-gray-200 mb-6">
      <nav class="-mb-px flex space-x-8">
        <button
          v-for="tab in tabs"
          :key="tab.id"
          @click="setActiveTab(tab.id)"
          :class="[
            'py-2 px-1 border-b-2 font-medium text-sm transition-colors duration-200',
            activeTab === tab.id
              ? 'border-blue-500 text-blue-600'
              : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
          ]"
        >
          <div class="flex items-center space-x-2">
            <i :class="tab.icon" class="text-lg"></i>
            <span>{{ tab.name }}</span>
            <span v-if="tab.count !== undefined" class="bg-gray-100 text-gray-600 px-2 py-1 rounded-full text-xs">
              {{ tab.count }}
            </span>
          </div>
        </button>
      </nav>
    </div>

    <!-- Tab Content -->
    <div class="tab-content">
      <slot :activeTab="activeTab"></slot>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'

const props = defineProps({
  tabs: {
    type: Array,
    required: true
  },
  defaultTab: {
    type: String,
    default: null
  }
})

const emit = defineEmits(['tab-change'])

const activeTab = ref(props.defaultTab || props.tabs[0]?.id || '')

const setActiveTab = (tabId) => {
  activeTab.value = tabId
  emit('tab-change', tabId)
}

// Watch for external changes to activeTab
watch(() => props.defaultTab, (newTab) => {
  if (newTab) {
    activeTab.value = newTab
  }
})
</script>

<style scoped>
.tab-content {
  min-height: 200px;
}
</style>
