<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
    id: {
        type: String,
        default: '',
    },
    class: {
        type: String,
        default: '',
    },
    placeholder: {
        type: String,
        default: '',
    },
    required: {
        type: Boolean,
        default: false,
    },
    autocomplete: {
        type: String,
        default: 'current-password',
    },
});

const model = defineModel({
    type: String,
    default: '',
});

const showPassword = ref(false);

const inputType = computed(() => showPassword.value ? 'text' : 'password');
const eyeIcon = computed(() => showPassword.value ? 'fas fa-eye-slash' : 'fas fa-eye');

const togglePasswordVisibility = () => {
    showPassword.value = !showPassword.value;
};
</script>

<template>
    <div class="relative">
        <input
            :id="id"
            :type="inputType"
            v-model="model"
            :class="[
                'rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 pr-10',
                props.class
            ]"
            :placeholder="placeholder"
            :required="required"
            :autocomplete="autocomplete"
        />
        <button
            type="button"
            @click="togglePasswordVisibility"
            class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 focus:outline-none focus:text-gray-600 transition-colors duration-200"
            :title="showPassword ? 'Hide password' : 'Show password'"
        >
            <i :class="[eyeIcon, 'text-sm']"></i>
        </button>
    </div>
</template>
