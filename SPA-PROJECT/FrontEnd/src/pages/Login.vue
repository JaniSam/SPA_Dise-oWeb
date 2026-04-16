<template>
  <div class="login-spa-container">
    <div class="login-spa-card">
      <div class="spa-brand">
        <span class="icon-leaf">🌿</span>
        <h1>SPA</h1>
        <p>BIENESTAR & ARMONÍA</p>
      </div>

      <!-- Tabs -->
      <div class="tabs">
        <button :class="['tab', activeTab === 'login' ? 'active' : '']" @click="activeTab = 'login'">
          Ingresar
        </button>
        <button :class="['tab', activeTab === 'register' ? 'active' : '']" @click="activeTab = 'register'">
          Registrarse
        </button>
      </div>

      <!-- FORMULARIO LOGIN -->
      <form v-if="activeTab === 'login'" @submit.prevent="handleLogin">
        <div class="input-spa-group">
          <label>Correo Electrónico</label>
          <input v-model="email" type="email" placeholder="admin@ejemplo.com" required />
        </div>
        <div class="input-spa-group">
          <label>Contraseña</label>
          <input v-model="password" type="password" placeholder="••••••••" required />
        </div>
        <transition name="fade">
          <p v-if="error" class="error-text">{{ error }}</p>
        </transition>
        <button type="submit" class="btn-spa-login" :disabled="loading">
          {{ loading ? 'Iniciando sesión...' : 'INGRESAR' }}
        </button>
      </form>

      <!-- FORMULARIO REGISTRO -->
      <form v-if="activeTab === 'register'" @submit.prevent="handleRegister">
        <div class="form-row">
          <div class="input-spa-group">
            <label>Nombre</label>
            <input v-model="reg.nombre" type="text" placeholder="María" required />
          </div>
          <div class="input-spa-group">
            <label>Apellido</label>
            <input v-model="reg.apellido" type="text" placeholder="González" />
          </div>
        </div>
        <div class="input-spa-group">
          <label>Correo Electrónico</label>
          <input v-model="reg.email" type="email" placeholder="maria@ejemplo.com" required />
        </div>
        <div class="input-spa-group">
          <label>Teléfono</label>
          <input v-model="reg.telefono" type="text" placeholder="0981234567" />
        </div>
        <div class="input-spa-group">
          <label>Contraseña</label>
          <input v-model="reg.password" type="password" placeholder="••••••••" required />
        </div>
        
        <!-- DESPUÉS - dos transition separadas -->
        <transition name="fade">
          <p v-if="regError" class="error-text">{{ regError }}</p>
        </transition>
        <transition name="fade">
          <p v-if="regSuccess" class="success-text">{{ regSuccess }}</p>
        </transition>
        <button type="submit" class="btn-spa-login" :disabled="regLoading">
          {{ regLoading ? 'Registrando...' : 'CREAR CUENTA' }}
        </button>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from "vue";
import { useRouter } from "vue-router";
import { api } from "../services/ApiUser";

const router   = useRouter();
const activeTab = ref("login");

// --- LOGIN ---
const email    = ref("");
const password = ref("");
const error    = ref("");
const loading  = ref(false);

const handleLogin = async () => {
  loading.value = true;
  error.value = "";
  try {
    const data = await api.login({ email: email.value, password: password.value });
    if (data.token) {
      localStorage.setItem("token", data.token);
      localStorage.setItem("user", JSON.stringify(data.user));
      localStorage.setItem("user-authenticated", "true");
      localStorage.setItem("user-role", data.user.rol_id === 1 ? "admin" : "user");
      router.push("/dashboard");
    } else {
      error.value = data.error || "Correo o contraseña incorrectos";
    }
  } catch {
    error.value = "Error de conexión con el servidor";
  } finally {
    loading.value = false;
  }
};

// --- REGISTRO ---
const reg = reactive({
  nombre: "", apellido: "", email: "",
  telefono: "", password: "", rol_id: ""
});
const regError   = ref("");
const regSuccess = ref("");
const regLoading = ref(false);

const handleRegister = async () => {
  regLoading.value = true;
  regError.value   = "";
  regSuccess.value = "";
  try {
    const data = await api.register({ ...reg });
    if (data.token) {
      regSuccess.value = "¡Cuenta creada! Redirigiendo...";
      localStorage.setItem("token", data.token);
      localStorage.setItem("user", JSON.stringify(data.user));
      localStorage.setItem("user-authenticated", "true");
      localStorage.setItem("user-role", data.user.rol_id === 1 ? "admin" : "user");
      setTimeout(() => router.push("/dashboard"), 1200);
    } else {
      regError.value = data.fields
        ? Object.values(data.fields).flat().join(" | ")
        : data.error || "Error al registrar";
    }
  } catch {
    regError.value = "Error de conexión con el servidor";
  } finally {
    regLoading.value = false;
  }
};
</script>

<style scoped>
.login-spa-container {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  background: linear-gradient(135deg, #fdfbfb 0%, #ebedee 100%);
  color: #555;
}
.login-spa-card {
  background: white;
  padding: 2.5rem 2.5rem;
  border-radius: 12px;
  box-shadow: 0 10px 25px rgba(0,0,0,0.07);
  width: 400px;
  text-align: center;
}
.spa-brand .icon-leaf { font-size: 2rem; }
.spa-brand h1 {
  font-size: 2.2rem;
  font-weight: 300;
  color: #3e7b8c;
  letter-spacing: 2px;
  margin: 0;
}
.spa-brand p { color: #a3a3a3; font-size: 0.85rem; margin-bottom: 1rem; }

/* Tabs */
.tabs {
  display: flex;
  border-bottom: 2px solid #f0ebe3;
  margin-bottom: 1.5rem;
}
.tab {
  flex: 1;
  padding: 10px;
  background: none;
  border: none;
  cursor: pointer;
  font-size: 0.9rem;
  color: #a3a3a3;
  font-weight: 500;
  transition: color 0.2s;
}
.tab.active {
  color: #3e7b8c;
  border-bottom: 2px solid #3e7b8c;
  margin-bottom: -2px;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
}
.input-spa-group { margin-bottom: 1.2rem; text-align: left; }
label { display: block; font-size: 0.8rem; font-weight: 600; color: #3e7b8c; margin-bottom: 4px; }
input, .spa-select {
  width: 100%;
  padding: 11px 12px;
  border-radius: 8px;
  border: 1px solid #e0e0e0;
  background-color: #fafafa;
  box-sizing: border-box;
  font-size: 0.9rem;
}
input:focus, .spa-select:focus {
  outline: none;
  border-color: #a8dadc;
  box-shadow: 0 0 0 3px rgba(168,218,220,0.2);
}
.error-text   { color: #d9534f; font-size: 0.85rem; margin-bottom: 1rem; }
.success-text { color: #5cb85c; font-size: 0.85rem; margin-bottom: 1rem; }
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
.btn-spa-login:hover:not(:disabled) { background-color: #2c5864; }
.btn-spa-login:disabled { opacity: 0.6; cursor: not-allowed; }
.fade-enter-active, .fade-leave-active { transition: opacity 0.5s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>