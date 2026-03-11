<template>
  <div class="layout">
    <Sidebar />

    <div class="content">
      <router-view />
    </div>
  </div>
</template>

<script>
import Sidebar from "../components/Sidebar.vue";

export default {
  components: {
    Sidebar,
  },
};
</script>

<style>
.layout {
  display: flex;
}

.content {
  padding: 20px;
  width: 100%;
}
</style>
