<template>
  <div class="sidebar">
    <h2>Recepción</h2>

    <router-link to="/">Dashboard</router-link>
    <router-link to="/visitors">Visitantes</router-link>
  </div>
</template>

<style>
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

a {
  color: white;
  text-decoration: none;
}
</style>
