<template>
  <div class="layout">
    <Sidebar />

    <div class="main">
      <Navbar />

      <div class="content">
        <slot />
      </div>
    </div>
  </div>
</template>

<script>
import Sidebar from "../components/Sidebar.vue";
import Navbar from "../components/Navbar.vue";

export default {
  components: {
    Sidebar,
    Navbar,
  },
};
</script>

<style>
/* Se mantienen tus estilos originales intactos */
.layout {
  display: flex;
}

.main {
  width: 100%;
}

.content {
  padding: 20px;
}
</style>
