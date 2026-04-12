<template>
  <header class="navbar">
    <div class="navbar-left">
      <h1>Sistema de Recepción</h1>
    </div>

    <div class="navbar-right">
      <button class="icon-button">🔔</button>

      <div class="user-container">
        <div class="user">👤 {{ nombreUsuario }}</div>
        <button @click="handleLogout" class="logout-button">Salir</button>
      </div>
    </div>
  </header>
</template>

<script>
export default {
  name: "Navbar",
  data() {
    return {
      // Valor por defecto si algo falla
      nombreUsuario: "Usuario",
    };
  },
  mounted() {
    // Cuando el Navbar aparece, lee el nombre que guardamos en el Login
    const nombreEnStorage = localStorage.getItem("user-name");
    if (nombreEnStorage) {
      this.nombreUsuario = nombreEnStorage;
    }
  },
  methods: {
    handleLogout() {
      localStorage.removeItem("user-authenticated");
      // Limpiamos el nombre al salir para seguridad
      localStorage.removeItem("user-name");
      this.$router.push("/login");
    },
  },
};
</script>

<style scoped>
.navbar {
  height: 60px;
  background: white;
  border-bottom: 1px solid #ddd;
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0 20px;
}
.navbar-left h1 {
  font-size: 18px;
  color: #3e7b8c;
}
.navbar-right {
  display: flex;
  gap: 20px;
  align-items: center;
}
.user-info {
  display: flex;
  align-items: center;
  gap: 15px;
}
.user-name {
  font-weight: bold;
  color: #555;
}
.logout-btn {
  background: #fdf2f2;
  border: 1px solid #ffcfcf;
  color: #d9534f;
  padding: 5px 12px;
  border-radius: 6px;
  cursor: pointer;
  font-size: 13px;
}
.logout-btn:hover {
  background: #d9534f;
  color: white;
}
.icon-button {
  background: none;
  border: none;
  cursor: pointer;
  font-size: 18px;
}
</style>
