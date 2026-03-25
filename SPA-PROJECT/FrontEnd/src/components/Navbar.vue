<template>
  <header class="navbar">
    <div class="navbar-left">
      <h1>Sistema de Recepción</h1>
    </div>

    <div class="navbar-right">
      <button class="icon-button">🔔</button>

      <div class="user">👤 Rodrigo</div>
    </div>
  </header>
</template>

<script>
export default {
  name: "Navbar",
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
  color: #2c3e50;
}

.navbar-right {
  display: flex;
  align-items: center;
  gap: 20px;
}

.icon-button {
  background: none;
  border: none;
  font-size: 18px;
  cursor: pointer;
}

.user {
  font-weight: bold;
}
</style>
