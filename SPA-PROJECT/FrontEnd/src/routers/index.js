import { createRouter, createWebHistory } from "vue-router";

import Calendar from "../pages/Calendar.vue";
import Clients from "../pages/Clients.vue";
import Users from "../pages/Users.vue";

const routes = [
  {
    path: "/",
    component: Calendar,
  },
  {
    path: "/calendar",
    component: Calendar,
  },
  {
    path: "/clients",
    component: Clients,
  },
  {
    path: "/users",
    component: Users,
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;
