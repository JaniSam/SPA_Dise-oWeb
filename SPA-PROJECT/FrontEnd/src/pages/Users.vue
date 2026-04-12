<template>
  <div class="users-page">
    <header class="page-header">
      <div class="header-content">
        <h1>Usuarios del Sistema</h1>
        <p class="subtitle">Gestión de accesos y roles del personal</p>
      </div>
      <button class="btn-primary" @click="openCreateModal">
        <span class="plus-icon">+</span> Nuevo Usuario
      </button>
    </header>

    <div class="table-container">
      <table>
        <thead>
          <tr>
            <th>Nombre</th>
            <th>Email</th>
            <th>Teléfono</th>
            <th>Rol</th>
            <th>Estado</th>
            <th class="actions-header">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="user in users" :key="user.id">
            <td class="user-name">{{ user.nombre }} {{ user.apellido }}</td>
            <td class="user-email">{{ user.email }}</td>
            <td>{{ user.telefono }}</td>
            <td>
              <span class="role-badge" :class="getRoleClass(user.rol_id)">
                {{ getRoleName(user.rol_id) }}
              </span>
            </td>
            <td>
              <span
                class="status-dot"
                :class="user.activo ? 'activo' : 'inactivo'"
              ></span>
              <span class="status-text">{{
                user.activo ? "activo" : "inactivo"
              }}</span>
            </td>
            <td class="actions-cell">
              <button
                class="btn-edit"
                @click="openEditModal(user)"
                title="Editar"
              >
                ✎
              </button>
              <button
                class="btn-delete"
                @click="openDeleteModal(user)"
                title="Eliminar"
              >
                ✕
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-if="showModal" class="modal-overlay" @click.self="closeModal">
      <div class="modal">
        <div class="modal-header">
          <h2>{{ isEditing ? "Editar Usuario" : "Nuevo Usuario" }}</h2>
          <div class="header-line"></div>
        </div>

        <form @submit.prevent="saveUser" class="spa-form">
          <div class="input-group">
            <label>Nombre</label>
            <input v-model="form.nombre" required />
          </div>
          <div class="input-group">
            <label>Apellido</label>
            <input v-model="form.apellido" />
          </div>
          <div class="input-group">
            <label>Email</label>
            <input type="email" v-model="form.email" required />
          </div>
          <div class="input-group">
            <label>Teléfono</label>
            <input v-model="form.telefono" />
          </div>
          <div class="input-group">
            <label>Password</label>
            <input type="password" v-model="form.password" required />
          </div>
          <div class="form-row">
            <div class="input-group">
              <label>Rol</label>
              <select v-model="form.rol_id">
                <option :value="1">Admin</option>
                <option :value="2">Recepcionista</option>
                <option :value="3">Terapeuta</option>
              </select>
            </div>
            <div class="input-group">
              <label>Estado</label>
              <select v-model="form.activo">
                <option :value="true">Activo</option>
                <option :value="false">Inactivo</option>
              </select>
            </div>
          </div>
          <div class="modal-actions">
            <button type="button" class="btn-cancel" @click="closeModal">
              Cancelar
            </button>
            <button type="submit" class="btn-save">
              {{ isEditing ? "Actualizar" : "Guardar Usuario" }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <div
      v-if="showConfirmModal"
      class="modal-overlay"
      @click.self="closeConfirmModal"
    >
      <div class="modal">
        <div class="modal-header">
          <h2>{{ confirmTitle }}</h2>
          <div class="header-line"></div>
        </div>
        <p style="text-align: center; margin: 20px 0">{{ confirmMessage }}</p>
        <div class="modal-actions">
          <button class="btn-cancel" @click="closeConfirmModal">
            Cancelar
          </button>
          <button
            :class="confirmType === 'delete' ? 'btn-delete' : 'btn-save'"
            @click="executeAction"
          >
            Confirmar
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      users: [],
      showModal: false,
      isEditing: false,
      showConfirmModal: false,
      confirmType: "",
      confirmTitle: "",
      confirmMessage: "",
      tempUser: null,
      form: {
        id: null,
        nombre: "",
        apellido: "",
        email: "",
        telefono: "",
        password: "",
        rol_id: 2,
        activo: true,
      },
    };
  },
  mounted() {
    // REGLA DE ORO: Bloqueo de seguridad si no es Admin
    const role = localStorage.getItem("user-role");
    if (role !== "admin") {
      alert("Acceso restringido a administradores.");
      this.$router.push("/calendar");
      return;
    }
    const saved = localStorage.getItem("spa-users");
    if (saved) this.users = JSON.parse(saved);
  },
  methods: {
    getRoleName(id) {
      const roles = { 1: "Admin", 2: "Recepcionista", 3: "Terapeuta" };
      return roles[id] || "Personal";
    },
    getRoleClass(id) {
      const classes = { 1: "admin", 2: "recepcionista", 3: "seguridad" };
      return classes[id] || "";
    },
    saveUser() {
      if (this.isEditing) {
        const index = this.users.findIndex((u) => u.id === this.form.id);
        this.users[index] = { ...this.form };
      } else {
        this.form.id = Date.now();
        this.users.push({ ...this.form });
      }
      localStorage.setItem("spa-users", JSON.stringify(this.users));
      this.closeModal();
    },
    openCreateModal() {
      this.isEditing = false;
      this.form = {
        id: null,
        nombre: "",
        apellido: "",
        email: "",
        telefono: "",
        password: "",
        rol_id: 2,
        activo: true,
      };
      this.showModal = true;
    },
    openEditModal(user) {
      this.isEditing = true;
      this.form = { ...user };
      this.showModal = true;
    },
    openDeleteModal(user) {
      this.tempUser = user;
      this.confirmType = "delete";
      this.confirmTitle = "Confirmar eliminación";
      this.confirmMessage = `¿Eliminar a ${user.nombre}?`;
      this.showConfirmModal = true;
    },
    executeAction() {
      if (this.confirmType === "delete") {
        this.users = this.users.filter((u) => u.id !== this.tempUser.id);
        localStorage.setItem("spa-users", JSON.stringify(this.users));
      }
      this.closeConfirmModal();
    },
    closeModal() {
      this.showModal = false;
    },
    closeConfirmModal() {
      this.showConfirmModal = false;
    },
  },
};
</script>

<style scoped>
.users-page {
  padding: 40px;
  background-color: #f9f7f2;
  min-height: 100vh;
  font-family: "Inter", sans-serif;
}
.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 30px;
}
.page-header h1 {
  font-size: 1.8rem;
  font-weight: 300;
  color: #4a4a4a;
  margin: 0;
}
.subtitle {
  color: #8daa91;
  margin-top: 5px;
  font-size: 0.9rem;
}
.table-container {
  background: white;
  border-radius: 16px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
  border: 1px solid #e5e0d8;
  overflow: hidden;
}
table {
  width: 100%;
  border-collapse: collapse;
}
th {
  background: #fdfdfb;
  padding: 16px;
  text-align: left;
  font-size: 0.75rem;
  text-transform: uppercase;
  letter-spacing: 0.05rem;
  color: #a3a3a3;
  border-bottom: 1px solid #f0ebe3;
}
td {
  padding: 16px;
  border-bottom: 1px solid #f9f7f2;
  color: #555;
  font-size: 0.95rem;
}
.user-name {
  font-weight: 600;
  color: #4a4a4a;
}
.role-badge {
  font-size: 0.75rem;
  padding: 4px 10px;
  border-radius: 50px;
  text-transform: capitalize;
}
.role-badge.admin {
  background: #e0e7ff;
  color: #4338ca;
}
.role-badge.recepcionista {
  background: #dcfce7;
  color: #15803d;
}
.status-dot {
  display: inline-block;
  width: 8px;
  height: 8px;
  border-radius: 50%;
  margin-right: 8px;
}
.status-dot.activo {
  background: #10b981;
}
.status-dot.inactivo {
  background: #ef4444;
}
.btn-primary {
  background: #8daa91;
  color: white;
  border: none;
  padding: 12px 24px;
  border-radius: 50px;
  cursor: pointer;
  font-weight: 500;
}
.actions-cell {
  display: flex;
  gap: 8px;
}
.btn-edit,
.btn-delete {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  border: none;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
}
.btn-edit {
  background: #fef3c7;
  color: #d4a373;
}
.btn-delete {
  background: #fee2e2;
  color: #ef4444;
}
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.3);
  backdrop-filter: blur(4px);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 100;
}
.modal {
  background: white;
  padding: 35px;
  border-radius: 24px;
  width: 420px;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
}
.modal-header {
  text-align: center;
  margin-bottom: 25px;
}
.header-line {
  height: 3px;
  width: 35px;
  background: #8daa91;
  margin: 8px auto;
  border-radius: 5px;
}
.spa-form {
  display: flex;
  flex-direction: column;
  gap: 15px;
}
.input-group label {
  display: block;
  font-size: 0.7rem;
  color: #a3a3a3;
  text-transform: uppercase;
  margin-bottom: 5px;
}
input,
select {
  width: 100%;
  padding: 12px;
  border: 1px solid #e5e0d8;
  border-radius: 12px;
  background: #fdfdfb;
  box-sizing: border-box;
}
.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 15px;
}
.modal-actions {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  margin-top: 20px;
}
.btn-save {
  background: #8daa91;
  color: white;
  border: none;
  padding: 12px 24px;
  border-radius: 50px;
  cursor: pointer;
}
.btn-cancel {
  background: transparent;
  color: #999;
  border: none;
  padding: 12px;
  cursor: pointer;
}
</style>
