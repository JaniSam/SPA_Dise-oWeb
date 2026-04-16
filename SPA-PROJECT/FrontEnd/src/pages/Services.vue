<template>
  <div class="services-page">
    <header class="header">
      <h1>Gestión de Servicios</h1>
      <button @click="openModal" class="btn-add">+ Nuevo Servicio</button>
    </header>

    <div class="table-card">
      <table class="custom-table">
        <thead>
          <tr>
            <th>Servicio</th>
            <th>Precio Sugerido</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="service in services" :key="service.id">
            <td>{{ service.nombre }}</td>
            <td>{{ formatMoney(service.precio) }}</td>
            <td>
              <button @click="deleteService(service.id)" class="btn-delete-row">
                Eliminar
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-if="isModalOpen" class="modal-overlay">
      <div class="modal-panel">
        <h2>Añadir Servicio</h2>
        <div class="spa-form">
          <div class="form-group">
            <label>Nombre del Servicio</label>
            <input
              v-model="newService.servicio"
              type="text"
              placeholder="Ej: Consulta Médica"
            />
          </div>
          <div class="form-group">
            <label>Precio (Gs.)</label>
            <input v-model.number="newService.precio" type="number" />
          </div>
          <div class="form-actions">
            <button @click="closeModal" class="btn-secondary">Cancelar</button>
            <button @click="saveService" class="btn-primary">Guardar</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
// Importamos el servicio que creamos
import { apiServicios } from "../services/ApiServices"; 

const services = ref([]);
const isModalOpen = ref(false);
const isLoading = ref(false); // Opcional: para mostrar un spinner
const newService = ref({ 
  nombre: "", 
  precio: 0, 
  duracion_minutos: 30, // Valor por defecto o añade un input en el modal
  descripcion: "" 
});
// 1. Cargar datos desde la API al montar el componente
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

const openModal = () => (isModalOpen.value = true);
const closeModal = () => {
  isModalOpen.value = false;
  newService.value = { servicio: "", precio: 0 };
};

// 2. Guardar servicio en la Base de Datos
const saveService = async () => {
  if (newService.value.servicio) {
    try {
      const created = await apiServicios.createServicio(newService.value);
      // Agregamos el resultado a la lista reactiva
      services.value.push(created);
      closeModal();
    } catch (error) {
      alert("No se pudo guardar el servicio");
    }
  }
};

// 3. Eliminar servicio de la Base de Datos
const deleteService = async (id) => {
  if (confirm("¿Estás seguro de eliminar este servicio?")) {
    try {
      await apiServicios.deleteServicio(id);
      // Filtramos la lista local para que desaparezca visualmente
      services.value = services.value.filter((s) => s.id !== id);
    } catch (error) {
      alert("Error al eliminar el servicio");
    }
  }
};

const formatMoney = (val) =>
  new Intl.NumberFormat("es-PY", { 
    style: "currency", 
    currency: "PYG",
    minimumFractionDigits: 0 
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
.btn-add {
  background: #38a89d;
  color: white;
  border: none;
  padding: 12px 25px;
  border-radius: 50px;
  cursor: pointer;
  font-weight: 600;
}
.table-card {
  background: white;
  border-radius: 20px;
  padding: 25px;
  border: 1px solid #e5e0d8;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);
}
.custom-table {
  width: 100%;
  border-collapse: collapse;
}
.custom-table th {
  text-align: left;
  padding: 15px;
  color: #a3a3a3;
  font-size: 0.8rem;
  border-bottom: 1px solid #eee;
}
.custom-table td {
  padding: 15px;
  border-bottom: 1px solid #f9f7f2;
}
.service-name {
  font-weight: 600;
  color: #2c3e50;
}
.service-price {
  color: #38a89d;
  font-weight: bold;
}
.btn-delete-row {
  color: #ef4444;
  background: none;
  border: none;
  cursor: pointer;
  font-size: 0.85rem;
}

/* Modal Simple Styles */
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
  padding: 30px;
  border-radius: 20px;
  width: 400px;
}
.spa-form {
  display: flex;
  flex-direction: column;
  gap: 15px;
  margin-top: 20px;
}
.form-group label {
  display: block;
  font-size: 0.75rem;
  color: #a3a3a3;
  margin-bottom: 5px;
}
input {
  width: 100%;
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 10px;
}
.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  margin-top: 10px;
}
.btn-primary {
  background: #8daa91;
  color: white;
  border: none;
  padding: 10px 20px;
  border-radius: 50px;
  cursor: pointer;
}
.btn-secondary {
  background: #eee;
  border: none;
  padding: 10px 20px;
  border-radius: 50px;
  cursor: pointer;
}
</style>
