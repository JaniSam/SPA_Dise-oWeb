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
            <th>Nombre</th> <th>Apellido</th> <th>Email</th>
            <th>Teléfono</th>
            <th>Documento</th>
            <th class="actions-header">Acciones</th>
          </tr>
        </thead>

        <tbody>
          <tr v-for="client in clients" :key="client.id">
            <td class="client-name">{{ client.name }}</td> <td>{{ client.lastname }}</td> <td>{{ client.email }}</td>
            <td>{{ client.phone }}</td>
            <td>
              <span class="doc-badge">{{ client.document }}</span>
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
              <label>Nombres</label>
              <input type="text" placeholder="Ej: Juan" v-model="form.name" required />
            </div>
            <div class="input-group">
              <label>Apellidos</label>
              <input type="text" placeholder="Ej: Pérez" v-model="form.lastname" required />
            </div>
          </div>

          <div class="input-group">
            <label>Correo Electrónico</label>
            <input type="email" placeholder="email@ejemplo.com" v-model="form.email" />
          </div>

          <div class="form-row">
            <div class="input-group">
              <label>Teléfono</label>
              <input type="text" placeholder="0981..." v-model="form.phone" />
            </div>
            <div class="input-group">
              <label>Documento</label>
              <input type="text" placeholder="C.I. / RUC" v-model="form.document" />
            </div>
          </div>

          <div class="modal-actions">
            <button type="button" class="btn-cancel" @click="closeModal">Cancelar</button>
            <button type="submit" class="btn-save">
              {{ isEditing ? "Actualizar" : "Guardar Cliente" }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import ClienteService from '../services/ApiClients';

export default {
  data() {
    return {
      clients: [],
      showModal: false,
      isEditing: false,
      form: { id: null, name: "", lastname: "", email: "", phone: "", document: "" },
    };
  },
  methods: {
    async getClients() {
      try {
        this.clients = await ClienteService.getAll();
      } catch (error) {
        console.error("Error al cargar clientes:", error);
      }
    },
    async saveClient() {
      try {
        if (this.isEditing) {
          await ClienteService.update(this.form.id, this.form);
          alert("¡Cliente actualizado!");
        } else {
          await ClienteService.create(this.form);
          alert("¡Cliente guardado!");
        }
        this.closeModal();
        this.getClients();
      } catch (error) {
        alert("Error al procesar la solicitud.");
      }
    },
    async deleteClient(id) {
      if (!confirm("¿Eliminar cliente?")) return;
      try {
        await ClienteService.delete(id);
        this.getClients();
      } catch (error) {
        console.error(error);
      }
    },
    openCreateModal() {
      this.isEditing = false;
      this.form = { id: null, name: "", lastname: "", email: "", phone: "", document: "" };
      this.showModal = true;
    },
    openEditModal(client) {
      this.isEditing = true;
      this.form = { ...client };
      this.showModal = true;
    },
    closeModal() {
      this.showModal = false;
    }
  },
  mounted() {
    this.getClients();
  }
};
</script>

<style scoped>
/* Estilos sin cambios, manteniendo tu diseño original */
.clients-page { padding: 40px; background-color: #f9f7f2; min-height: 100vh; font-family: "Inter", sans-serif; }
.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
.page-header h1 { font-size: 1.8rem; font-weight: 300; color: #4a4a4a; margin: 0; }
.subtitle { color: #8daa91; margin-top: 5px; font-size: 0.9rem; }
.table-container { background: white; border-radius: 16px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04); border: 1px solid #e5e0d8; overflow: hidden; }
table { width: 100%; border-collapse: collapse; }
th { background: #fdfdfb; padding: 16px; text-align: left; font-size: 0.75rem; text-transform: uppercase; color: #a3a3a3; border-bottom: 1px solid #f0ebe3; }
td { padding: 16px; border-bottom: 1px solid #f9f7f2; color: #555; font-size: 0.95rem; }
.client-name { font-weight: 600; color: #4a4a4a; }
.doc-badge { background: #f0ebe3; padding: 4px 10px; border-radius: 6px; font-size: 0.8rem; color: #7a7a7a; }
.actions-cell { display: flex; gap: 8px; }
.btn-primary { background: #8daa91; color: white; border: none; padding: 12px 24px; border-radius: 50px; cursor: pointer; font-weight: 500; }
.btn-edit { background: #fef3c7; color: #d4a373; border: none; width: 35px; height: 35px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; }
.btn-delete { background: #fee2e2; color: #ef4444; border: none; width: 35px; height: 35px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; }
.modal-overlay { position: fixed; inset: 0; background: rgba(0, 0, 0, 0.3); backdrop-filter: blur(4px); display: flex; justify-content: center; align-items: center; z-index: 100; }
.modal { background: white; padding: 35px; border-radius: 24px; width: 420px; box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15); }
.modal-header { text-align: center; margin-bottom: 25px; }
.header-line { height: 3px; width: 40px; background: #8daa91; margin: 0 auto; border-radius: 10px; }
.spa-form { display: flex; flex-direction: column; gap: 15px; }
.input-group label { display: block; font-size: 0.7rem; color: #a3a3a3; text-transform: uppercase; margin-bottom: 5px; }
input { width: 100%; padding: 12px; border: 1px solid #e5e0d8; border-radius: 12px; background: #fdfdfb; box-sizing: border-box; }
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
.modal-actions { display: flex; justify-content: flex-end; gap: 12px; margin-top: 20px; }
.btn-save { background: #8daa91; color: white; border: none; padding: 12px 20px; border-radius: 50px; cursor: pointer; }
.btn-cancel { background: #eee; color: #777; border: none; padding: 12px 20px; border-radius: 50px; cursor: pointer; }
</style>