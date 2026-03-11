import { createRouter, createWebHistory } from "vue-router";

import Dashboard from "../pages/Dashboard.vue";
import Visitors from "../pages/Visitors.vue";

const routes = [
  {
    path: "/",
    component: Dashboard,
  },
  {
    path: "/visitors",
    component: Visitors,
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;
