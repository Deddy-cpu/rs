<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Register" />

        <form @submit.prevent="submit" class="space-y-5">
            <!-- Judul -->
            <h2 class="text-center text-2xl font-bold text-gray-800">Register</h2>

            <!-- Name -->
            <div>
                <InputLabel for="name" value="Name" class="text-gray-700" />
                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                />
                <InputError class="mt-2 text-red-500" :message="form.errors.name" />
            </div>

            <!-- Email -->
            <div>
                <InputLabel for="email" value="Email" class="text-gray-700" />
                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200"
                    v-model="form.email"
                    required
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
                    autocomplete="new-password"
                />
                <InputError class="mt-2 text-red-500" :message="form.errors.password" />
            </div>

            <!-- Confirm Password -->
            <div>
                <InputLabel for="password_confirmation" value="Confirm Password" class="text-gray-700" />
                <TextInput
                    id="password_confirmation"
                    type="password"
                    class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200"
                    v-model="form.password_confirmation"
                    required
                    autocomplete="new-password"
                />
                <InputError class="mt-2 text-red-500" :message="form.errors.password_confirmation" />
            </div>

            <!-- Tombol -->
            <PrimaryButton
                class="w-full py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold shadow-md flex justify-center items-center transform hover:-translate-y-0.5 transition"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
            >
                REGISTER
            </PrimaryButton>

            <!-- Login link -->
            <p class="text-center text-sm text-gray-600 mt-4">
                Sudah punya akun?
                <Link href="/login" class="text-indigo-600 hover:underline font-medium">
                    Login
                </Link>
            </p>
        </form>
    </GuestLayout>
</template>
