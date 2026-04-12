<template>
  <div class="sidebar">
    <h2>Recepción</h2>

    <router-link to="/dashboard" active-class="active-link"
      >Dashboard</router-link
    >
    <router-link to="/calendar" active-class="active-link"
      >Calendario</router-link
    >
    <router-link to="/clients" active-class="active-link">Clientes</router-link>

    <router-link
      v-if="userRole === 'admin'"
      to="/users"
      active-class="active-link"
    >
      Usuarios
    </router-link>

    <router-link to="/services" active-class="active-link"
      >Servicios</router-link
    >
  </div>
</template>

<script>
export default {
  name: "Sidebar",
  data() {
    return {
      userRole: "recepcionista",
    };
  },
  mounted() {
    // REGLA DE ORO: Sincronizamos el rol para mostrar/ocultar "Usuarios"
    const savedRole = localStorage.getItem("user-role");
    if (savedRole) {
      this.userRole = savedRole;
    }
  },
};
</script>

<style scoped>
/* He añadido scoped para que no afecte a otros links de la app */
.sidebar {
  width: 200px;
  background: #2c3e50;
  color: white;
  height: 100vh;
  padding: 20px;
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.sidebar a {
  color: white;
  text-decoration: none;
  padding: 10px;
  border-radius: 8px;
  transition: background 0.3s;
}

.sidebar a:hover {
  background: rgba(255, 255, 255, 0.1);
}

/* Estilo para saber en qué página estás */
.active-link {
  background: #38a89d !important; /* El color verde de tu SPA */
  font-weight: bold;
}
</style>
