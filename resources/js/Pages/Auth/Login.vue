<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Login" />
          <form @submit.prevent="submit" class="space-y-5">
    <!-- Judul -->
    <h2 class="text-center text-2xl font-bold text-gray-800">Login</h2>

    <!-- Email -->
    <div>
        <InputLabel for="email" value="Email" class="text-gray-700" />
        <TextInput
            id="email"
            type="email"
            class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200"
            v-model="form.email"
            required
            autofocus
            autocomplete="username"
        />
        <InputError class="mt-2 text-red-500" :message="form.errors.email" />
    </div>

    <!-- Password -->
    <div>
        <InputLabel for="password" value="Password" class="text-gray-700" />
        <TextInput
            id="password"
            type="password"
            class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200"
            v-model="form.password"
            required
            autocomplete="current-password"
        />
        <InputError class="mt-2 text-red-500" :message="form.errors.password" />
    </div>

    <!-- Remember + Forgot -->
    <div class="flex items-center justify-between text-sm">
        <label class="flex items-center">
            <Checkbox name="remember" v-model:checked="form.remember" />
            <span class="ms-2 text-gray-600">Ingat saya</span>
        </label>

        <Link
            v-if="canResetPassword"
            :href="route('password.request')"
            class="text-indigo-600 hover:text-indigo-800 font-medium"
        >
            Lupa password?
        </Link>
    </div>

    <!-- Tombol -->
<PrimaryButton
    class="w-full py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 
           text-white font-semibold shadow-md transform hover:-translate-y-0.5 
           transition flex justify-center items-center"
    :class="{ 'opacity-25': form.processing }"
    :disabled="form.processing"
>
    LOGIN
</PrimaryButton>



    <!-- Register -->
    <p class="text-center text-sm text-gray-600 mt-4">
        Belum punya akun?
        <Link href="/register" class="text-indigo-600 hover:underline font-medium">
            Register
        </Link>
    </p>
</form>

    </GuestLayout>
</template>
