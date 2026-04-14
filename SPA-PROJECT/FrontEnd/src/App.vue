<template>
  <div id="app-content">
    <router-view v-slot="{ Component }">
      <transition name="page-fade" mode="out-in">
        <component :is="$route.name === 'Login' ? 'div' : 'DashboardLayout'">
          <component :is="Component" />
        </component>
      </transition>
    </router-view>
  </div>
</template>

<script>
import DashboardLayout from "./layouts/DashboardLayout.vue";

export default {
  components: {
    DashboardLayout,
  },
};
</script>

<style>
body {
  margin: 0;
  font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
}

/* Estilos de la transición */
.page-fade-enter-active,
.page-fade-leave-active {
  transition: opacity 0.3s ease;
}

.page-fade-enter-from,
.page-fade-leave-to {
  opacity: 0;
}
</style>
