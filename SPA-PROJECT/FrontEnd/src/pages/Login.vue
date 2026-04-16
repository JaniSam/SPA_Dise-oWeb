<template>
  <div class="login-spa-container">
    <div class="login-spa-card">
      <div class="spa-brand">
        <span class="icon-leaf">🌿</span>
        <h1>SPA</h1>
        <p>BIENESTAR & ARMONÍA</p>
      </div>

      <form @submit.prevent="handleLogin">
        <div class="input-spa-group">
          <label>Correo Electrónico</label>
          <input 
            v-model="email" 
            type="email" 
            placeholder="admin@ejemplo.com" 
            required 
          />
        </div>

        <div class="input-spa-group">
          <label>Contraseña</label>
          <input 
            v-model="password" 
            type="password" 
            placeholder="••••••••" 
            required 
          />
        </div>

        <transition name="fade">
          <p v-if="error" class="error-text">{{ error }}</p>
        </transition>

        <button type="submit" class="btn-spa-login" :disabled="loading">
          {{ loading ? 'Iniciando sesión...' : 'INGRESAR' }}
        </button>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";
import { api } from "../services/ApiUser"; // Importación corregida

const email = ref("");
const password = ref("");
const error = ref("");
const loading = ref(false);
const router = useRouter();

const handleLogin = async () => {
  loading.value = true;
  error.value = "";
  
  try {
    // Usamos el método login de tu ApiUser.js
    const data = await api.login({
      email: email.value,
      password: password.value,
    });

    if (data.token) {
      localStorage.setItem("token", data.token)
      localStorage.setItem("user", JSON.stringify(data.user))
      
      // ← AGREGA ESTAS DOS LÍNEAS:
      localStorage.setItem("user-authenticated", "true")
      localStorage.setItem("user-role", data.user.rol_id === 1 ? "admin" : "user")
      
      router.push("/dashboard")
    } else {
      // Aquí capturamos el "Credenciales inválidas" que viste en Thunder Client
      error.value = data.error || "Correo o contraseña incorrectos";
    }
  } catch (err) {
    error.value = "Error de conexión con el servidor";
  } finally {
    loading.value = false;
  }
};
</script>

<style scoped>
/* Restaurado tu diseño SPA original */
.login-spa-container {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
  background: linear-gradient(135deg, #fdfbfb 0%, #ebedee 100%);
  color: #555;
}
.login-spa-card {
  background: white;
  padding: 3rem 2.5rem;
  border-radius: 12px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
  width: 360px;
  text-align: center;
}
.spa-brand .icon-leaf {
  font-size: 2rem;
}
.spa-brand h1 {
  font-size: 2.2rem;
  font-weight: 300;
  color: #3e7b8c;
  letter-spacing: 2px;
  margin: 0;
}
.spa-brand p {
  color: #a3a3a3;
  font-size: 0.85rem;
  margin-bottom: 2rem;
}
.input-spa-group {
  margin-bottom: 1.5rem;
  text-align: left;
}
label {
  display: block;
  font-size: 0.85rem;
  font-weight: 600;
  color: #3e7b8c;
  margin-bottom: 0.5rem;
}
input {
  width: 100%;
  padding: 12px;
  border-radius: 8px;
  border: 1px solid #e0e0e0;
  background-color: #fafafa;
  box-sizing: border-box;
  font-size: 0.9rem;
}
input:focus {
  outline: none;
  border-color: #a8dadc;
  box-shadow: 0 0 0 3px rgba(168, 218, 220, 0.2);
}
.error-text {
  color: #d9534f;
  font-size: 0.85rem;
  margin-bottom: 1rem;
}
.btn-spa-login {
  width: 100%;
  padding: 12px;
  background-color: #3e7b8c;
  border: none;
  border-radius: 8px;
  color: white;
  font-weight: 600;
  cursor: pointer;
  font-size: 0.95rem;
  transition: background-color 0.2s;
}
.btn-spa-login:hover:not(:disabled) {
  background-color: #2c5864;
}
.btn-spa-login:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.5s;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>