<template>
  <div class="clients-page">
    <header class="page-header">
      <div class="header-content">
        <h1>Clientes</h1>
        <p class="subtitle">Gestión de base de datos de pacientes</p>
      </div>
      <button class="btn-primary" @click="openCreateModal">
        <span class="plus-icon">+</span> Nuevo Cliente
      </button>
    </header>
    <div class="table-container">
      <table>
        <thead>
          <tr>
            <th>Nombre</th>
            <th>Apellido</th>
            <th>Email</th>
            <th>Teléfono</th>
            <th>Estado</th>
            <th class="actions-header">Acciones</th>
          </tr>
        </thead>

        <tbody>
          <tr v-for="client in clients" :key="client.id">
            <td class="client-name">{{ client.nombre }}</td>
            <td>{{ client.apellido }}</td>
            <td>{{ client.email }}</td>
            <td>{{ client.telefono }}</td>
            <td>
              <span :class="['status-badge', client.activo ? 'active' : 'inactive']">
                {{ client.activo ? 'Activo' : 'Inactivo' }}
              </span>
            </td>
            <td class="actions-cell">
              <button class="btn-edit" @click="openEditModal(client)" title="Editar">✎</button>
              <button class="btn-delete" @click="deleteClient(client.id)" title="Eliminar">✕</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-if="showModal" class="modal-overlay" @click.self="closeModal">
      <div class="modal">
        <div class="modal-header">
          <h2>{{ isEditing ? "Editar Cliente" : "Nuevo Cliente" }}</h2>
          <div class="header-line"></div>
        </div>

        <form @submit.prevent="saveClient" class="spa-form">
          <div class="form-row">
            <div class="input-group">
              <label>Nombre</label>
              <input type="text" v-model="form.nombre" required />
            </div>
            <div class="input-group">
              <label>Apellido</label>
              <input type="text" v-model="form.apellido" />
            </div>
          </div>

          <div class="input-group">
            <label>Correo Electrónico</label>
            <input type="email" v-model="form.email" required />
          </div>

          <div class="form-row">
            <div class="input-group">
              <label>Teléfono</label>
              <input type="text" v-model="form.telefono" />
            </div>
            <div class="input-group">
              <label>Estado</label>
              <select v-model="form.activo" class="spa-select">
                <option :value="true">Activo</option>
                <option :value="false">Inactivo</option>
              </select>
            </div>
          </div>

          <div v-if="!isEditing" class="input-group">
            <label>Password</label>
            <input type="password" v-model="form.password" required />
          </div>

          <div class="modal-actions">
            <button type="button" class="btn-cancel" @click="closeModal">Cancelar</button>
            <button type="submit" class="btn-save">{{ isEditing ? "Actualizar" : "Guardar" }}</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      clients: [
        // Ejemplo de cómo se verían los datos de tu tabla
        { id: 1, nombre: "Juan", apellido: "Pérez", email: "juan@mail.com", telefono: "0981123", activo: true, rol_id: 4 }
      ],
      showModal: false,
      isEditing: false,
      // Estructura idéntica a tu tabla 'usuarios'
      form: { 
        id: null, 
        nombre: "", 
        apellido: "", 
        email: "", 
        telefono: "", 
        password: "",
        activo: true,
        rol_id: 4 // Siempre 4 por ser Clientes
      },
    };
  },
  methods: {
    openCreateModal() {
      this.isEditing = false;
      this.form = { id: null, nombre: "", apellido: "", email: "", telefono: "", password: "", activo: true, rol_id: 4 };
      this.showModal = true;
    },
    openEditModal(client) {
      this.isEditing = true;
      this.form = { ...client };
      this.showModal = true;
    },
    saveClient() {
      if (this.isEditing) {
        const index = this.clients.findIndex((c) => c.id === this.form.id);
        this.clients[index] = { ...this.form };
      } else {
        this.form.id = Date.now(); // Simulación de ID serial
        this.clients.push({ ...this.form });
      }
      this.closeModal();
    },
    deleteClient(id) {
      this.clients = this.clients.filter((c) => c.id !== id);
    },
    closeModal() {
      this.showModal = false;
    },
    saveClient() {
    if (this.isEditing) {
      const index = this.clients.findIndex((c) => c.id === this.form.id);
      if (index !== -1) {

        this.clients.splice(index, 1, { ...this.form });
      }
      } else {
        this.form.id = Date.now(); 
        this.clients.push({ ...this.form });
      }
  this.closeModal();
}
  },
};


<style scoped>
/* Estructura y Fondo */
.clients-page {
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

/* Tabla Estilo Profesional */
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

.client-name {
  font-weight: 600;
  color: #4a4a4a;
}

.doc-badge {
  background: #f0ebe3;
  padding: 4px 10px;
  border-radius: 6px;
  font-size: 0.8rem;
  color: #7a7a7a;
}

/* Botones de Acciones */
.actions-cell {
  display: flex;
  gap: 8px;
}

.btn-primary {
  background: #8daa91;
  color: white;
  border: none;
  padding: 12px 24px;
  border-radius: 50px;
  cursor: pointer;
  font-weight: 500;
  transition: all 0.3s ease;
}

.btn-edit,
.btn-delete {
  width: 35px;
  height: 35px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  border: none;
  cursor: pointer;
  transition: 0.2s;
}

.btn-edit {
  background: #fef3c7;
  color: #d4a373;
}
.btn-delete {
  background: #fee2e2;
  color: #ef4444;
}

/* Modal Premium */
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
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
}

.modal-header {
  text-align: center;
  margin-bottom: 25px;
}
.modal-header h2 {
  font-weight: 300;
  color: #4a4a4a;
  margin-bottom: 10px;
}
.header-line {
  height: 3px;
  width: 40px;
  background: #8daa91;
  margin: 0 auto;
  border-radius: 10px;
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

input {
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
  padding: 12px 20px;
  border-radius: 50px;
  cursor: pointer;
}
.btn-cancel {
  background: #eee;
  color: #777;
  border: none;
  padding: 12px 20px;
  border-radius: 50px;
  cursor: pointer;
}
</style>
