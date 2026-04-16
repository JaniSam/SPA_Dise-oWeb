<template>
  <div class="calendar-page">
    <div class="calendar-card">
      <div v-if="isLoading" class="loading-overlay">Cargando citas...</div>
      <FullCalendar ref="fullCalendar" :options="calendarOptions" />
    </div>

    <TransitionRoot appear :show="isModalOpen" as="template">
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
              <DialogTitle class="modal-title">
                {{ isEditing ? "Detalles de la Cita" : "Nueva Reserva" }}
              </DialogTitle>

              <div v-if="availabilityError" class="availability-alert">
                ⚠️ {{ availabilityError }}
              </div>

              <form @submit.prevent="saveReservation" class="spa-form">
                
                <div class="form-section">
                  <div v-if="!isEditing" class="form-group">
                    <label>Documento del Cliente (Cédula)</label>
                    <div class="search-input-group">
                      <input
                        v-model="searchDoc"
                        type="text"
                        placeholder="Ej: 1254567"
                        @keyup.enter="searchClientByDoc"
                      />
                      <button type="button" class="btn-search" @click="searchClientByDoc">🔍</button>
                    </div>
                    <p v-if="searchError" class="error-msg">{{ searchError }}</p>
                  </div>

                  <div class="form-group">
                    <label>Nombre del Cliente</label>
                    <input
                      v-model="clientNameDisplay"
                      type="text"
                      readonly
                      class="readonly-input"
                      placeholder="Busca un cliente por documento..."
                    />
                  </div>
                </div>

                <div class="form-section highlight-specialist">
                  <label class="section-label">Especialista</label>
                  <div class="form-group">
                    <select v-model="form.terapeuta_id" required class="specialist-select">
                      <option value="" disabled>Seleccionar Especialista...</option>
                      <option v-for="t in terapeutas" :key="t.id" :value="t.id">
                        👤 {{ t.nombre }} {{ t.apellido }}
                      </option>
                    </select>
                  </div>
                </div>

                <div class="form-section highlight-services">
                  <label class="section-label">Servicio</label>
                  <div class="services-selection-grid">
                    <div
                      v-for="s in servicios"
                      :key="s.id"
                      :class="['service-chip', { active: form.servicio_id === s.id }]"
                      @click="selectService(s)"
                    >
                      <span>{{ form.servicio_id === s.id ? "✅" : "➕" }}</span>
                      {{ s.nombre }}
                    </div>
                  </div>
                </div>

                <div class="form-section">
                  <label class="section-label">Horario de la Cita</label>
                  <div class="form-row">
                    <div class="form-group">
                      <label>📅 Fecha y Hora Inicio</label>
                      <input v-model="form.start_dt" type="datetime-local" required @change="syncTimes" />
                    </div>
                    <div class="form-group">
                      <label>🏁 Hora Fin</label>
                      <input v-model="form.end_dt" type="datetime-local" required />
                    </div>
                  </div>
                </div>

                <div class="form-section">
                  <div class="form-group">
                    <label>Estado de la Cita</label>
                    <select v-model="form.estado" :class="form.estado.toLowerCase()" class="status-select">
                      <option value="PENDIENTE">⏳ Pendiente</option>
                      <option value="CONFIRMADA">✅ Confirmada</option>
                      <option value="CANCELADA">❌ Cancelada</option>
                      <option value="COMPLETADA">✔️ Completada</option>
                    </select>
                  </div>
                </div>

                <div class="form-actions">
                  <button v-if="isEditing" type="button" class="btn-delete" @click="deleteReservation">
                    Eliminar Cita
                  </button>
                  <div class="spacer"></div>
                  <button type="button" class="btn-secondary" @click="closeModal">Cancelar</button>
                  <button type="submit" class="btn-primary" :disabled="!form.cliente_id || !form.servicio_id">
                    {{ isEditing ? "Actualizar" : "Guardar Reserva" }}
                  </button>
                </div>
              </form>
            </DialogPanel>
          </TransitionChild>
        </div>
      </Dialog>
    </TransitionRoot>
  </div>
</template>

<script setup>
import { reactive, ref, onMounted } from "vue";
import FullCalendar from "@fullcalendar/vue3";
import timeGridPlugin from "@fullcalendar/timegrid";
import interactionPlugin from "@fullcalendar/interaction";
import esLocale from "@fullcalendar/core/locales/es";
import { apiReservas } from "../services/ApiReserva";
import {
  Dialog, DialogPanel, DialogTitle, TransitionRoot, TransitionChild,
} from "@headlessui/vue";

// Estados de Datos
const events = ref([]);
const clientes = ref([]);
const terapeutas = ref([]);
const servicios = ref([]);
const isLoading = ref(false);

// Estados de UI
const isModalOpen = ref(false);
const isEditing = ref(false);
const currentId = ref(null);
const searchDoc = ref("");
const searchError = ref("");
const clientNameDisplay = ref("");
const availabilityError = ref(null);

const form = reactive({
  cliente_id: "",
  terapeuta_id: "",
  servicio_id: "",
  start_dt: "", // Formato para datetime-local
  end_dt: "",
  estado: "PENDIENTE",
  obs: ""
});

onMounted(async () => {
  await loadAllData();
});

const loadAllData = async () => {
  isLoading.value = true;
  try {
    const [resData, formData] = await Promise.all([
      apiReservas.getReservas(),
      apiReservas.getFormData()
    ]);
    
    // Mapear reservas al formato de FullCalendar
    events.value = resData.map(r => ({
      id: r.id.toString(),
      title: `${r.cliente.nombre} - ${r.servicio.nombre}`,
      start: `${r.fecha}T${r.hora_inicio}`,
      end: `${r.fecha}T${r.hora_fin}`,
      backgroundColor: getStatusColor(r.estado),
      extendedProps: { ...r }
    }));

    clientes.value = formData.clientes;
    terapeutas.value = formData.terapeutas;
    servicios.value = formData.servicios;
  } catch (e) {
    console.error("Error al cargar datos:", e);
  } finally {
    isLoading.value = false;
  }
};

const getStatusColor = (status) => {
  const colors = { PENDIENTE: '#F59E0B', CONFIRMADA: '#10B981', CANCELADA: '#EF4444', COMPLETADA: '#3B82F6' };
  return colors[status] || '#6B7280';
};

const searchClientByDoc = () => {
  const client = clientes.value.find(c => c.cedula === searchDoc.value);
  if (client) {
    form.cliente_id = client.id;
    clientNameDisplay.value = `${client.nombre} ${client.apellido}`;
    searchError.value = "";
  } else {
    searchError.value = "⚠️ No encontrado.";
    form.cliente_id = "";
    clientNameDisplay.value = "";
  }
};

const selectService = (service) => {
  form.servicio_id = service.id;
  if (form.start_dt) {
    const start = new Date(form.start_dt);
    const end = new Date(start.getTime() + service.duracion_minutos * 60000);
    form.end_dt = end.toISOString().slice(0, 16);
  }
};

const syncTimes = () => {
  if (form.servicio_id && form.start_dt) {
    const service = servicios.value.find(s => s.id === form.servicio_id);
    if (service) selectService(service);
  }
};

const calendarOptions = reactive({
  plugins: [timeGridPlugin, interactionPlugin],
  initialView: "timeGridWeek",
  locale: esLocale,
  allDaySlot: false,
  selectable: true,
  editable: true,
  events: events,
  headerToolbar: { left: "prev,next today", center: "title", right: "timeGridWeek,timeGridDay" },
  
  select: (info) => {
    isEditing.value = false;
    searchDoc.value = "";
    clientNameDisplay.value = "";
    Object.assign(form, {
      cliente_id: "", terapeuta_id: "", servicio_id: "",
      start_dt: info.startStr.slice(0, 16),
      end_dt: info.endStr.slice(0, 16),
      estado: "PENDIENTE"
    });
    isModalOpen.value = true;
  },

  eventClick: (info) => {
    isEditing.value = true;
    const data = info.event.extendedProps;
    currentId.value = info.event.id;
    clientNameDisplay.value = `${data.cliente.nombre} ${data.cliente.apellido}`;
    Object.assign(form, {
      cliente_id: data.cliente_id,
      terapeuta_id: data.terapeuta_id,
      servicio_id: data.servicio_id,
      start_dt: info.event.startStr.slice(0, 16),
      end_dt: info.event.endStr.slice(0, 16),
      estado: data.estado
    });
    isModalOpen.value = true;
  }
});

const saveReservation = async () => {
  // Preparar datos para Laravel (separar fecha de hora)
  const startObj = new Date(form.start_dt);
  const endObj = new Date(form.end_dt);
  
  const payload = {
    cliente_id: form.cliente_id,
    terapeuta_id: form.terapeuta_id,
    servicio_id: form.servicio_id,
    fecha: startObj.toISOString().split('T')[0],
    hora_inicio: startObj.toTimeString().slice(0, 5),
    hora_fin: endObj.toTimeString().slice(0, 5),
    estado: form.estado
  };

  try {
    if (isEditing.value) {
      await apiReservas.updateReserva(currentId.value, payload);
    } else {
      await apiReservas.createReserva(payload);
    }
    await loadAllData(); // Recargar calendario
    closeModal();
  } catch (e) {
    alert("Error al guardar la reserva");
  }
};

const deleteReservation = async () => {
  if (!confirm("¿Eliminar cita?")) return;
  try {
    await apiReservas.deleteReserva(currentId.value);
    await loadAllData();
    closeModal();
  } catch (e) {
    alert("Error al eliminar");
  }
};

const closeModal = () => { isModalOpen.value = false; };
</script>

<style scoped>
/* --- BASE Y CONTENEDOR --- */
.calendar-page {
  padding: 30px;
  background: #f9f7f2;
  min-height: 100vh;
  font-family: "Inter", sans-serif;
}

.calendar-card {
  background: white;
  padding: 20px;
  border-radius: 20px;
  border: 1px solid #e5e0d8;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
  position: relative; /* Para el loading-overlay */
}

.loading-overlay {
  position: absolute;
  top: 15px;
  right: 15px;
  background: #38a89d;
  color: white;
  padding: 6px 16px;
  border-radius: 50px;
  font-size: 0.75rem;
  font-weight: 700;
  z-index: 10;
  box-shadow: 0 4px 12px rgba(56, 168, 157, 0.3);
}

/* --- MODAL Y ESTRUCTURA --- */
.modal-root {
  position: relative;
  z-index: 1000;
}

.modal-fixed-container {
  position: fixed;
  inset: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1100; /* Asegura que esté sobre el calendario */
  background: rgba(26, 32, 44, 0.4); /* Un gris más profundo */
  backdrop-filter: blur(4px);
  padding: 20px;
}

.modal-panel {
  background: white;
  padding: 2rem;
  border-radius: 30px;
  width: 100%;
  max-width: 550px;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
  max-height: 90vh;
  overflow-y: auto;
}

.modal-title {
  font-size: 1.5rem;
  font-weight: 700;
  color: #1a202c;
  text-align: center;
  margin-bottom: 1.5rem;
}

/* --- SECCIONES DEL FORMULARIO --- */
.form-section {
  margin-bottom: 1.2rem;
  display: flex;
  flex-direction: column;
  gap: 0.8rem;
}

.section-label {
  font-size: 0.75rem;
  font-weight: 800;
  text-transform: uppercase;
  color: #38a89d;
  letter-spacing: 0.5px;
  margin-bottom: 5px;
}

.highlight-specialist {
  background: #fdf2f8;
  padding: 1.2rem;
  border-radius: 18px;
  border: 1px solid #fce7f3;
}

.highlight-services {
  background: #f0f7f6;
  padding: 1.2rem;
  border-radius: 18px;
  border: 1px solid #d1e7e4;
}

/* --- GRUPOS DE INPUTS --- */
.form-group label {
  font-size: 0.7rem;
  font-weight: 600;
  text-transform: uppercase;
  color: #718096;
  margin-bottom: 6px;
  display: block;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
  align-items: flex-start;
}

input, select, textarea {
  width: 100%;
  padding: 12px;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  font-size: 0.9rem;
  transition: border-color 0.2s;
}

input:focus, select:focus {
  outline: none;
  border-color: #38a89d;
}

.readonly-input {
  background: #f7fafc;
  border-style: dashed;
  color: #4a5568;
}

/* --- BUSCADOR Y CHIPS --- */
.search-input-group {
  display: flex;
  gap: 8px;
}

.btn-search {
  background: #38a89d;
  color: white;
  border: none;
  padding: 0 15px;
  border-radius: 10px;
  cursor: pointer;
  transition: transform 0.1s;
}

.btn-search:active { transform: scale(0.95); }

.services-selection-grid {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}

.service-chip {
  padding: 8px 16px;
  border-radius: 50px;
  background: white;
  border: 1px solid #e2e8f0;
  font-size: 0.8rem;
  cursor: pointer;
  transition: all 0.2s;
}

.service-chip.active {
  background: #38a89d;
  color: white;
  border-color: #38a89d;
  box-shadow: 0 4px 10px rgba(56, 168, 157, 0.2);
}

/* --- ESTADOS Y ALERTAS --- */
.availability-alert {
  background: #fff1f2;
  color: #e11d48;
  padding: 12px;
  border-radius: 12px;
  margin-bottom: 20px;
  font-size: 0.85rem;
  font-weight: 700;
  text-align: center;
  border: 1px solid #ffe4e6;
}

.error-msg {
  color: #e11d48;
  font-size: 0.75rem;
  font-weight: 600;
}

/* Colores dinámicos para el select de estado */
.status-select.pendiente { color: #d97706; font-weight: bold; }
.status-select.confirmada { color: #059669; font-weight: bold; }
.status-select.cancelada { color: #dc2626; font-weight: bold; }
.status-select.completada { color: #2563eb; font-weight: bold; }

/* --- BOTONES DE ACCIÓN --- */
.form-actions {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-top: 1.5rem;
  padding-top: 1.5rem;
  border-top: 1px solid #edf2f7;
}

.spacer { flex: 1; }

.btn-primary {
  background: #38a89d;
  color: white;
  border: none;
  padding: 12px 24px;
  border-radius: 50px;
  font-weight: 600;
  cursor: pointer;
  transition: opacity 0.2s;
}

.btn-primary:disabled {
  background: #cbd5e1;
  cursor: not-allowed;
}

.btn-secondary {
  background: #edf2f7;
  color: #4a5568;
  border: none;
  padding: 12px 20px;
  border-radius: 50px;
  cursor: pointer;
}

.btn-delete {
  color: #e53e3e;
  background: none;
  border: none;
  cursor: pointer;
  font-size: 0.85rem;
  font-weight: 600;
}

/* Estilo para que el calendario se vea bien en esta página */
:deep(.fc) {
  --fc-button-bg-color: #38a89d;
  --fc-button-border-color: #38a89d;
  --fc-button-hover-bg-color: #2c8a81;
  --fc-event-resizer-thickness: 10px;
}
</style>