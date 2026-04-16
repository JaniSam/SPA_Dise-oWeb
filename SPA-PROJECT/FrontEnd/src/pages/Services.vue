<template>
  <div class="services-page">
    <header class="header">
      <h1>Gestión de Servicios</h1>
      <button @click="openCreateModal" class="btn-add">+ Nuevo Servicio</button>
    </header>

    <div class="table-card">
      <table class="custom-table">
        <thead>
          <tr>
            <th>Servicio</th>
            <th>Descripción</th>
            <th>Duración</th>
            <th>Precio Sugerido</th>
            <th>Estado</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="isLoading">
            <td colspan="6" style="text-align:center; color:#a3a3a3;">Cargando...</td>
          </tr>
          <tr v-else-if="services.length === 0">
            <td colspan="6" style="text-align:center; color:#a3a3a3;">No hay servicios registrados.</td>
          </tr>
          <tr v-for="service in services" :key="service.id">
            <td class="service-name">{{ service.nombre }}</td>
            <td class="service-desc">{{ service.descripcion || '—' }}</td>
            <td>{{ service.duracion_minutos }} min</td>
            <td class="service-price">{{ formatMoney(service.precio) }}</td>
            <td>
              <span :class="service.activo ? 'badge-active' : 'badge-inactive'">
                {{ service.activo ? 'Activo' : 'Inactivo' }}
              </span>
            </td>
            <td class="actions-cell">
              <button @click="openEditModal(service)" class="btn-edit-row">Editar</button>
              <button @click="deleteService(service.id)" class="btn-delete-row">Eliminar</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- MODAL CREAR / EDITAR -->
    <div v-if="isModalOpen" class="modal-overlay">
      <div class="modal-panel">
        <h2>{{ isEditing ? 'Editar Servicio' : 'Añadir Servicio' }}</h2>
        <div class="spa-form">
          <div class="form-group">
            <label>Nombre del Servicio *</label>
            <input
              v-model="formData.nombre"
              type="text"
              placeholder="Ej: Masaje Relajante"
            />
          </div>
          <div class="form-group">
            <label>Descripción</label>
            <textarea
              v-model="formData.descripcion"
              placeholder="Descripción opcional del servicio"
              rows="3"
            ></textarea>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label>Duración (minutos) *</label>
              <input
                v-model.number="formData.duracion_minutos"
                type="number"
                min="11"
                placeholder="Mínimo 11 min"
              />
            </div>
            <div class="form-group">
              <label>Precio (Gs.) *</label>
              <input
                v-model.number="formData.precio"
                type="number"
                min="0"
                placeholder="0"
              />
            </div>
          </div>
          <div class="form-group form-group-check">
            <label class="checkbox-label">
              <input type="checkbox" v-model="formData.activo" />
              Servicio activo
            </label>
          </div>
          <div class="form-actions">
            <button @click="closeModal" class="btn-secondary">Cancelar</button>
            <button @click="saveService" class="btn-primary">
              {{ isEditing ? 'Actualizar' : 'Guardar' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { apiServicios } from "../services/ApiServices";

const services = ref([]);
const isModalOpen = ref(false);
const isLoading = ref(false);
const isEditing = ref(false);
const editingId = ref(null);

const emptyForm = () => ({
  nombre: "",
  descripcion: "",
  duracion_minutos: 30,
  precio: 0,
  activo: true,
});

const formData = ref(emptyForm());

onMounted(async () => {
  await fetchServices();
});

const fetchServices = async () => {
  isLoading.value = true;
  try {
    const data = await apiServicios.getServicios();
    services.value = data;
  } catch (error) {
    console.error("Error al cargar servicios:", error);
  } finally {
    isLoading.value = false;
  }
};

const openCreateModal = () => {
  isEditing.value = false;
  editingId.value = null;
  formData.value = emptyForm();
  isModalOpen.value = true;
};

const openEditModal = (service) => {
  isEditing.value = true;
  editingId.value = service.id;
  formData.value = {
    nombre: service.nombre,
    descripcion: service.descripcion || "",
    duracion_minutos: service.duracion_minutos,
    precio: service.precio,
    activo: service.activo,
  };
  isModalOpen.value = true;
};

const closeModal = () => {
  isModalOpen.value = false;
  formData.value = emptyForm();
  isEditing.value = false;
  editingId.value = null;
};

const saveService = async () => {
  if (!formData.value.nombre || !formData.value.duracion_minutos) {
    alert("Por favor completá el nombre y la duración.");
    return;
  }
  if (formData.value.duracion_minutos < 11) {
    alert("La duración mínima es de 11 minutos.");
    return;
  }

  try {
    if (isEditing.value) {
      const updated = await apiServicios.updateServicio(editingId.value, formData.value);
      const idx = services.value.findIndex((s) => s.id === editingId.value);
      if (idx !== -1) services.value[idx] = updated;
    } else {
      const created = await apiServicios.createServicio(formData.value);
      services.value.push(created);
    }
    closeModal();
  } catch (error) {
    alert(isEditing.value ? "No se pudo actualizar el servicio." : "No se pudo guardar el servicio.");
  }
};

const deleteService = async (id) => {
  if (confirm("¿Estás seguro de eliminar este servicio?")) {
    try {
      await apiServicios.deleteServicio(id);
      services.value = services.value.filter((s) => s.id !== id);
    } catch (error) {
      alert("Error al eliminar el servicio.");
    }
  }
};

const formatMoney = (val) =>
  new Intl.NumberFormat("es-PY", {
    style: "currency",
    currency: "PYG",
    minimumFractionDigits: 0,
  }).format(val);
</script>

<style scoped>
.services-page {
  padding: 40px;
  background: #f9f7f2;
  min-height: 100vh;
}
.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 30px;
}
.header h1 {
  font-size: 1.6rem;
  color: #2c3e50;
}
.btn-add {
  background: #38a89d;
  color: white;
  border: none;
  padding: 12px 25px;
  border-radius: 50px;
  cursor: pointer;
  font-weight: 600;
}
.btn-add:hover {
  background: #2e9288;
}

/* Tabla */
.table-card {
  background: white;
  border-radius: 20px;
  padding: 25px;
  border: 1px solid #e5e0d8;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.04);
  overflow-x: auto;
}
.custom-table {
  width: 100%;
  border-collapse: collapse;
}
.custom-table th {
  text-align: left;
  padding: 12px 15px;
  color: #a3a3a3;
  font-size: 0.78rem;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  border-bottom: 1px solid #eee;
}
.custom-table td {
  padding: 14px 15px;
  border-bottom: 1px solid #f9f7f2;
  font-size: 0.9rem;
  color: #444;
}
.custom-table tr:last-child td {
  border-bottom: none;
}
.service-name {
  font-weight: 600;
  color: #2c3e50;
}
.service-desc {
  color: #888;
  font-size: 0.85rem;
  max-width: 200px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.service-price {
  color: #38a89d;
  font-weight: bold;
}
.actions-cell {
  display: flex;
  gap: 8px;
}
.btn-edit-row {
  color: #38a89d;
  background: none;
  border: 1px solid #38a89d;
  border-radius: 20px;
  padding: 4px 12px;
  cursor: pointer;
  font-size: 0.8rem;
}
.btn-edit-row:hover {
  background: #38a89d;
  color: white;
}
.btn-delete-row {
  color: #ef4444;
  background: none;
  border: 1px solid #ef4444;
  border-radius: 20px;
  padding: 4px 12px;
  cursor: pointer;
  font-size: 0.8rem;
}
.btn-delete-row:hover {
  background: #ef4444;
  color: white;
}

/* Badges */
.badge-active {
  background: #d1fae5;
  color: #065f46;
  padding: 3px 10px;
  border-radius: 20px;
  font-size: 0.78rem;
  font-weight: 600;
}
.badge-inactive {
  background: #fee2e2;
  color: #991b1b;
  padding: 3px 10px;
  border-radius: 20px;
  font-size: 0.78rem;
  font-weight: 600;
}

/* Modal */
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.4);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}
.modal-panel {
  background: white;
  padding: 35px;
  border-radius: 20px;
  width: 460px;
  max-width: 95vw;
  box-shadow: 0 20px 60px rgba(0,0,0,0.15);
}
.modal-panel h2 {
  color: #2c3e50;
  margin-bottom: 5px;
}
.spa-form {
  display: flex;
  flex-direction: column;
  gap: 15px;
  margin-top: 20px;
}
.form-row {
  display: flex;
  gap: 15px;
}
.form-row .form-group {
  flex: 1;
}
.form-group label {
  display: block;
  font-size: 0.75rem;
  color: #a3a3a3;
  margin-bottom: 5px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.04em;
}
input[type="text"],
input[type="number"],
textarea {
  width: 100%;
  padding: 10px 14px;
  border: 1px solid #ddd;
  border-radius: 10px;
  font-size: 0.9rem;
  box-sizing: border-box;
  transition: border-color 0.2s;
}
input:focus,
textarea:focus {
  outline: none;
  border-color: #38a89d;
}
textarea {
  resize: vertical;
}
.form-group-check {
  margin-top: 2px;
}
.checkbox-label {
  display: flex !important;
  align-items: center;
  gap: 8px;
  cursor: pointer;
  font-size: 0.9rem !important;
  text-transform: none !important;
  letter-spacing: 0 !important;
  color: #555 !important;
}
.checkbox-label input[type="checkbox"] {
  width: 16px;
  height: 16px;
  accent-color: #38a89d;
}
.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  margin-top: 5px;
}
.btn-primary {
  background: #38a89d;
  color: white;
  border: none;
  padding: 10px 24px;
  border-radius: 50px;
  cursor: pointer;
  font-weight: 600;
}
.btn-primary:hover {
  background: #2e9288;
}
.btn-secondary {
  background: #eee;
  border: none;
  padding: 10px 20px;
  border-radius: 50px;
  cursor: pointer;
  color: #555;
}
.btn-secondary:hover {
  background: #e0e0e0;
}
</style>