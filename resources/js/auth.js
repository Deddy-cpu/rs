// resources/js/auth.js
import axios from 'axios'

export async function login(email, password) {
  try {
    const response = await axios.post('http://localhost:8000/api/login', {
      email,
      password
    })
    localStorage.setItem('token', response.data.token)
    console.log('✅ Token disimpan:', response.data.token)
    return true
  } catch (err) {
    console.error('❌ Gagal login:', err.response?.data || err)
    return false
  }
}
