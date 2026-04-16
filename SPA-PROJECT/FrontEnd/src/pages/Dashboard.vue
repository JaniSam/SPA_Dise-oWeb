<template>
  <div class="dashboard-container">
    <div v-if="isLoading" class="loading-overlay">Actualizando panel...</div>
    <header class="dashboard-header">
      <h1>Panel de Control</h1>
      <p class="date-display">{{ currentDate }}</p>
    </header>

    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon">📅</div>
        <div class="stat-info">
          <h3>Citas Hoy</h3>
          <p class="stat-number">{{ todayEvents.length }}</p>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon">⏳</div>
        <div class="stat-info">
          <h3>Pendientes</h3>
          <p class="stat-number">{{ pendingToday }}</p>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon">👥</div>
        <div class="stat-info">
          <h3>Total Clientes</h3>
          <p class="stat-number">{{ totalClients }}</p>
        </div>
      </div>
    </div>

    <div class="dashboard-content">
      <div class="table-card">
        <div class="table-header">
          <h2>Próximos Pacientes</h2>
          <router-link to="/calendar" class="btn-view-all"
            >Ver Calendario</router-link
          >
        </div>

        <div v-if="upcomingEvents.length > 0" class="custom-table-container">
          <table class="custom-table">
            <thead>
              <tr>
                <th>Hora</th>
                <th>Paciente</th>
                <th>Estado</th>
                <th>Contacto/Obs.</th>
                <th>Acción</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="event in upcomingEvents" :key="event.id">
                <td class="time-cell">{{ formatTime(event.start) }}</td>
                <td class="name-cell">{{ event.title }}</td>
                <td>
                  <span :class="['status-badge', event.extendedProps.status]">
                    {{ formatStatus(event.extendedProps.status) }}
                  </span>
                </td>
                <td class="obs-cell">
                  {{ event.extendedProps.obs || "Sin datos" }}
                </td>
                <td>
                  <button @click="openClinicalNote(event)" class="btn-attend">
                    {{
                      event.extendedProps.clinicalNote ? "📝 Ver" : "🏥 Atender"
                    }}
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div v-else class="empty-state">
          <p>No hay más citas programadas para hoy.</p>
        </div>
      </div>
    </div>

    <TransitionRoot appear :show="isNoteModalOpen" as="template">
      <Dialog as="div" @close="closeModal" class="modal-root">
        <div class="modal-fixed-container">
          <TransitionChild
            as="template"
            enter="duration-300 ease-out"
            enter-from="opacity-0 scale-95"
            enter-to="opacity-100 scale-100"
            leave="duration-200 ease-in"
            leave-from="opacity-100 scale-100"
            leave-to="opacity-0 scale-95"
          >
            <DialogPanel class="modal-panel">
              <DialogTitle class="modal-title">Ficha de Atención</DialogTitle>

              <div class="patient-banner">
                <strong>Paciente:</strong> {{ selectedEvent?.title }}
              </div>

              <div class="form-section">
                <label class="section-label">Evolución Clínica</label>
                <textarea
                  v-model="clinicalForm.note"
                  rows="4"
                  placeholder="Escribe el progreso del paciente..."
                ></textarea>
              </div>

              <div class="form-section highlight-supplies">
                <label class="section-label">Insumos Utilizados</label>
                <div class="supplies-grid">
                  <div
                    v-for="item in supplies"
                    :key="item.id"
                    class="supply-item"
                  >
                    <span>{{ item.name }}</span>
                    <div class="counter">
                      <button
                        type="button"
                        @click="decrementSupply(item.id)"
                        :disabled="!clinicalForm.usedSupplies[item.id]"
                      >
                        -
                      </button>
                      <input
                        type="number"
                        v-model.number="clinicalForm.usedSupplies[item.id]"
                        readonly
                      />
                      <button type="button" @click="incrementSupply(item.id)">
                        +
                      </button>
                    </div>
                  </div>
                </div>
              </div>

              <div class="form-actions">
                <button @click="closeModal" class="btn-secondary">
                  Cancelar
                </button>
                <button @click="saveClinicalData" class="btn-primary">
                  Guardar Atención
                </button>
              </div>
            </DialogPanel>
          </TransitionChild>
        </div>
      </Dialog>
    </TransitionRoot>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import { apiReservas } from "../services/ApiReserva"; // Importamos tu servicio
import {
  Dialog, DialogPanel, DialogTitle, TransitionRoot, TransitionChild,
} from "@headlessui/vue";

const allEvents = ref([]);
const totalClients = ref(0);
const isLoading = ref(true);
const now = new Date();

// Lógica de Atención
const isNoteModalOpen = ref(false);
const selectedEvent = ref(null);
const supplies = ref([
  { id: 1, name: "Aceite Base" },
  { id: 2, name: "Guantes (par)" },
  { id: 3, name: "Crema Hidratante" },
  { id: 4, name: "Toallas" },
]);
const clinicalForm = ref({ note: "", usedSupplies: {} });

onMounted(async () => {
  await cargarDatosDesdeAPI();
});

const cargarDatosDesdeAPI = async () => {
  isLoading.value = true;
  try {
    // 1. Traemos las reservas reales de la BD
    const resData = await apiReservas.getReservas();
    
    // 2. Mapeamos al formato que ya usa tu Dashboard
    allEvents.value = resData.map(r => ({
      id: r.id.toString(),
      title: `${r.cliente.nombre} ${r.cliente.apellido}`,
      start: `${r.fecha}T${r.hora_inicio}`,
      end: `${r.fecha}T${r.hora_fin}`,
      extendedProps: { 
        status: r.estado.toLowerCase(), // Lo pasamos a minúsculas para tus clases CSS
        obs: r.obs,
        cliente_id: r.cliente_id,
        servicio_nombre: r.servicio.nombre
      }
    }));

    // 3. Traemos el total de clientes desde el mismo endpoint de formularios
    const formData = await apiReservas.getFormData();
    totalClients.value = formData.clientes.length;

    // Inicializar contadores de insumos
    supplies.value.forEach((s) => {
      if (!clinicalForm.value.usedSupplies[s.id])
        clinicalForm.value.usedSupplies[s.id] = 0;
    });
  } catch (error) {
    console.error("Error cargando datos del dashboard:", error);
  } finally {
    isLoading.value = false;
  }
};

// --- EL RESTO DE TUS COMPUTED Y MÉTODOS SE MANTIENEN IGUAL ---
const todayEvents = computed(() => {
  return allEvents.value.filter((event) => {
    const eventDate = new Date(event.start);
    return eventDate.toDateString() === now.toDateString();
  });
});

const upcomingEvents = computed(() => {
  return [...todayEvents.value].sort((a, b) => new Date(a.start) - new Date(b.start));
});

const pendingToday = computed(() => {
  // Ahora comparamos con 'pendiente' en minúsculas por el mapeo anterior
  return todayEvents.value.filter((e) => e.extendedProps.status === "pendiente").length;
});

const currentDate = computed(() => {
  return now.toLocaleDateString("es-ES", {
    weekday: "long", year: "numeric", month: "long", day: "numeric",
  });
});

// Métodos de Modal y Formateo
const incrementSupply = (id) => clinicalForm.value.usedSupplies[id]++;
const decrementSupply = (id) => {
  if (clinicalForm.value.usedSupplies[id] > 0) clinicalForm.value.usedSupplies[id]--;
};

const openClinicalNote = (event) => {
  selectedEvent.value = event;
  clinicalForm.value.note = event.extendedProps.clinicalNote || "";
  isNoteModalOpen.value = true;
};

const closeModal = () => { isNoteModalOpen.value = false; };

const formatTime = (dateStr) => {
  return new Date(dateStr).toLocaleTimeString("es-ES", { hour: "2-digit", minute: "2-digit" });
};

const formatStatus = (status) => {
  const labels = { pendiente: "Pendiente", confirmada: "Confirmado", cancelada: "Cancelado", completada: "Completado" };
  return labels[status] || status;
};

// Para guardar la atención, podrías usar el updateReserva de tu API
const saveClinicalData = async () => {
  try {
    const payload = {
       estado: 'CONFIRMADA', // O 'COMPLETADA'
       obs: clinicalForm.value.note 
    };
    await apiReservas.updateReserva(selectedEvent.value.id, payload);
    await cargarDatosDesdeAPI(); // Recargar todo
    closeModal();
  } catch (e) {
    alert("Error al actualizar la cita");
  }
};
</script>

<style scoped>
/* ESTILOS ORIGINALES MANTENIDOS */
.dashboard-container {
  padding: 40px;
  background-color: #f9f7f2;
  min-height: 100vh;
}
.dashboard-header {
  margin-bottom: 30px;
}
.dashboard-header h1 {
  font-weight: 300;
  color: #2c3e50;
  font-size: 2rem;
}
.date-display {
  color: #8daa91;
  text-transform: capitalize;
  font-size: 0.9rem;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 20px;
  margin-bottom: 40px;
}
.stat-card {
  background: white;
  padding: 25px;
  border-radius: 20px;
  display: flex;
  align-items: center;
  gap: 20px;
  border: 1px solid #e5e0d8;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);
}
.stat-icon {
  font-size: 2.5rem;
  background: #fdfdfb;
  padding: 10px;
  border-radius: 15px;
}
.stat-info h3 {
  font-size: 0.8rem;
  text-transform: uppercase;
  color: #a3a3a3;
  letter-spacing: 1px;
  margin: 0;
}
.stat-number {
  font-size: 1.8rem;
  font-weight: 600;
  color: #2c3e50;
  margin: 0;
}

.table-card {
  background: white;
  border-radius: 20px;
  padding: 30px;
  border: 1px solid #e5e0d8;
}
.table-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 25px;
}
.btn-view-all {
  color: #38a89d;
  text-decoration: none;
  font-size: 0.9rem;
  font-weight: 500;
}

.custom-table {
  width: 100%;
  border-collapse: collapse;
}
.custom-table th {
  text-align: left;
  padding: 15px;
  color: #a3a3a3;
  font-size: 0.75rem;
  text-transform: uppercase;
  border-bottom: 1px solid #f0ebe3;
}
.custom-table td {
  padding: 15px;
  border-bottom: 1px solid #f9f7f2;
  color: #4a4a4a;
}

.time-cell {
  font-weight: 600;
  color: #38a89d;
}
.name-cell {
  font-weight: 500;
}
.obs-cell {
  font-size: 0.85rem;
  color: #7f8c8d;
}

/* NUEVO BOTÓN ATENDER */
.btn-attend {
  background: #f0fdfa;
  color: #38a89d;
  border: 1px solid #ccfbf1;
  padding: 6px 12px;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: 0.2s;
}
.btn-attend:hover {
  background: #38a89d;
  color: white;
}

/* STATUS BADGES ORIGINALES */
.status-badge {
  padding: 5px 12px;
  border-radius: 50px;
  font-size: 0.75rem;
  font-weight: 500;
}
.status-badge.pending {
  background: #fff7ed;
  color: #c2410c;
}
.status-badge.confirmed {
  background: #f0fdf4;
  color: #15803d;
}
.status-badge.cancelled {
  background: #fef2f2;
  color: #b91c1c;
}

/* MODAL STYLES (MATCHING YOUR APP'S VIBE) */
.modal-fixed-container {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.3);
  backdrop-filter: blur(2px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 100;
  padding: 20px;
}
.modal-panel {
  background: white;
  padding: 30px;
  border-radius: 25px;
  width: 100%;
  max-width: 500px;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
}
.modal-title {
  font-size: 1.4rem;
  font-weight: 600;
  color: #2c3e50;
  margin-bottom: 20px;
  text-align: center;
}
.patient-banner {
  background: #f9f7f2;
  padding: 12px;
  border-radius: 12px;
  margin-bottom: 20px;
  color: #4a4a4a;
  border-left: 4px solid #38a89d;
}
.section-label {
  font-size: 0.75rem;
  font-weight: 700;
  text-transform: uppercase;
  color: #a3a3a3;
  margin-bottom: 8px;
  display: block;
}
.highlight-supplies {
  background: #f0fdfa;
  padding: 20px;
  border-radius: 15px;
  margin-top: 20px;
}

.supplies-grid {
  display: grid;
  gap: 12px;
}
.supply-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 0.9rem;
}
.counter {
  display: flex;
  align-items: center;
  gap: 10px;
  background: white;
  padding: 4px 8px;
  border-radius: 8px;
  border: 1px solid #e2e8f0;
}
.counter button {
  width: 24px;
  height: 24px;
  border: none;
  background: #38a89d;
  color: white;
  border-radius: 4px;
  cursor: pointer;
}
.counter input {
  width: 30px;
  text-align: center;
  border: none;
  font-weight: bold;
  background: transparent;
}

.form-actions {
  display: flex;
  gap: 10px;
  margin-top: 30px;
  justify-content: flex-end;
}
.btn-primary {
  background: #38a89d;
  color: white;
  border: none;
  padding: 12px 25px;
  border-radius: 12px;
  font-weight: 600;
  cursor: pointer;
}
.btn-secondary {
  background: #f1f5f9;
  color: #475569;
  border: none;
  padding: 12px 20px;
  border-radius: 12px;
  cursor: pointer;
}

textarea {
  width: 100%;
  padding: 12px;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  resize: none;
  outline: none;
  transition: 0.2s;
}
textarea:focus {
  border-color: #38a89d;
}

.empty-state {
  text-align: center;
  padding: 40px;
  color: #a3a3a3;
  font-style: italic;
}
</style>