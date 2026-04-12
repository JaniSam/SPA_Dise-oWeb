import { createRouter, createWebHistory } from "vue-router";
import Login from "../pages/Login.vue";
import Dashboard from "../pages/Dashboard.vue";
import Calendar from "../pages/Calendar.vue";
import Clients from "../pages/Clients.vue";
import Users from "../pages/Users.vue";
import Services from "../pages/Services.vue";

const routes = [
  {
    path: "/",
    // REGLA DE ORO: Usamos la misma llave que en el beforeEach
    redirect: () => {
      return localStorage.getItem("user-authenticated") === "true"
        ? "/dashboard"
        : "/login";
    },
  },
  { path: "/login", name: "Login", component: Login },
  {
    path: "/dashboard",
    name: "Dashboard",
    component: Dashboard,
    meta: { requiresAuth: true },
  },
  {
    path: "/calendar",
    name: "Calendar",
    component: Calendar,
    meta: { requiresAuth: true },
  },
  {
    path: "/clients",
    name: "Clients",
    component: Clients,
    meta: { requiresAuth: true },
  },
  {
    path: "/users",
    name: "Users",
    component: Users,
    meta: { requiresAuth: true },
  },
  {
    path: "/services",
    name: "Services",
    component: Services,
    meta: { requiresAuth: true },
  },
  { path: "/:pathMatch(.*)*", redirect: "/login" },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach((to, from, next) => {
  const isAuthenticated = localStorage.getItem("user-authenticated") === "true";
  const userRole = localStorage.getItem("user-role");

  if (to.meta.requiresAuth && !isAuthenticated) {
    next("/login");
  }
  // Evitamos que un usuario logueado entre al login de nuevo
  else if (to.path === "/login" && isAuthenticated) {
    next("/dashboard");
  }
  // Protección de ruta de usuarios
  else if (to.path === "/users" && userRole !== "admin") {
    next("/dashboard");
  } else {
    next();
  }
});

export default router;
