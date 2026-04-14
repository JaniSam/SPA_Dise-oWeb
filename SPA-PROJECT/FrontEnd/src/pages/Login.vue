<template>
  <div class="login-spa-container">
    <div class="login-spa-card">
      <div class="spa-brand">
        <span class="icon-leaf">🌿</span>
        <h1>SPA</h1>
        <p>Sistema de Recepción</p>
      </div>

      <form @submit.prevent="handleLogin">
        <div class="input-spa-group">
          <label for="username">Usuario</label>
          <input
            type="text"
            id="username"
            v-model="username"
            placeholder="Usuario"
            required
          />
        </div>

        <div class="input-spa-group">
          <label for="password">Contraseña</label>
          <input
            type="password"
            id="password"
            v-model="password"
            placeholder="••••••••"
            required
          />
        </div>

        <transition name="fade">
          <p v-if="errorMessage" class="error-text">{{ errorMessage }}</p>
        </transition>

        <button type="submit" class="btn-spa-login">Ingresar</button>
      </form>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      username: "",
      password: "",
      errorMessage: "",
    };
  },
  methods: {
    handleLogin() {
      // Simulación de validación
      if (
        this.username === "admin" ||
        (this.username === "rodrigo" && this.password === "1234")
      ) {
        localStorage.setItem("user-authenticated", "true");
        localStorage.setItem("user-name", this.username);
        // Asignamos un rol simple basado en el nombre de usuario
        const role =
          this.username.toLowerCase() === "admin" ? "admin" : "recepcionista";
        localStorage.setItem("user-role", role);
        this.$router.push("/dashboard");
      } else {
        this.errorMessage = "Credenciales incorrectas. Intente de nuevo.";
        // Limpiar mensaje después de 3 segundos
        setTimeout(() => {
          this.errorMessage = "";
        }, 3000);
      }
    },
  },
};
</script>

<style scoped>
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
.spa-brand h1 {
  font-size: 2.2rem;
  font-weight: 300;
  color: #3e7b8c;
  letter-spacing: 2px;
  margin: 0;
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
}
.btn-spa-login:hover {
  background-color: #2c5864;
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
