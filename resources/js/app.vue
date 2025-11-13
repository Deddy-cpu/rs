<script setup>
import { ref } from 'vue'
import { login } from './auth'

const email = ref('')
const password = ref('')

const handleLogin = async () => {
  const success = await login(email.value, password.value)
  if (success) alert('Login berhasil!')
}
</script>

<template>
  <div class="p-4">
    <h2>Login</h2>
    <input v-model="email" placeholder="Email" />
    <input v-model="password" type="password" placeholder="Password" />
    <button @click="handleLogin">Login</button>
  </div>
</template>
