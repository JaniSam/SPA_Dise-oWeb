<template>
  <div class="calendar-page">
    <div class="calendar-card">
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
                    <label>Documento del Cliente</label>
                    <div class="search-input-group">
                      <input
                        v-model="searchDoc"
                        type="text"
                        placeholder="Ej: 4545888"
                        @keyup.enter="searchClientByDoc"
                      />
                      <button
                        type="button"
                        class="btn-search"
                        @click="searchClientByDoc"
                      >
                        🔍
                      </button>
                    </div>
                    <p v-if="searchError" class="error-msg">
                      {{ searchError }}
                    </p>
                  </div>

                  <div class="form-group">
                    <label>Nombre del Cliente</label>
                    <input
                      v-model="form.title"
                      type="text"
                      readonly
                      class="readonly-input"
                      placeholder="Nombre del paciente..."
                    />
                  </div>
                </div>

                <div class="form-section highlight-specialist">
                  <label class="section-label">Asignación de Especialista</label>
                  <div class="form-group">
                    <select
                      v-model="form.specialistId"
                      @change="validateAppointment"
                      required
                      class="specialist-select"
                    >
                      <option value="" disabled>
                        Seleccionar Especialista...
                      </option>
                      <option
                        v-for="spec in specialists"
                        :key="spec.id"
                        :value="spec.id"
                      >
                        👤 {{ spec.name }} ({{ spec.area }})
                      </option>
                    </select>
                  </div>
                </div>

                <div class="form-section highlight-services">
                  <label class="section-label">Servicios Disponibles</label>
                  <div class="services-selection-grid">
                    <div
                      v-for="service in availableServices"
                      :key="service.id"
                      :class="[
                        'service-chip',
                        { active: isServiceSelected(service.id) },
                      ]"
                      @click="toggleService(service)"
                    >
                      <span class="chip-icon">{{
                        isServiceSelected(service.id) ? "✅" : "➕"
                      }}</span>
                      {{ service.nombre }} </div>
                  </div>
                </div>

                <div class="form-section">
                  <label class="section-label">Horario de la Cita</label>
                  <div class="form-row">
                    <div class="form-group">
                      <label>📅 Hora Inicio</label>
                      <input
                        v-model="form.start"
                        type="datetime-local"
                        required
                        @change="validateAppointment"
                      />
                    </div>
                    <div class="form-group">
                      <label>🏁 Hora Fin</label>
                      <input
                        v-model="form.end"
                        type="datetime-local"
                        required
                        @change="validateAppointment"
                      />
                    </div>
                  </div>
                </div>

                <div class="form-section">
                  <div class="form-group">
                    <label>Notas Adicionales</label>
                    <textarea
                      v-model="form.obs"
                      rows="2"
                      placeholder="Observaciones clínicas..."
                    ></textarea>
                  </div>
                </div>

                <div class="form-actions">
                  <button
                    v-if="isEditing"
                    type="button"
                    class="btn-delete"
                    @click="deleteReservation"
                  >
                    Eliminar Cita
                  </button>
                  <div class="spacer"></div>
                  <button
                    type="button"
                    class="btn-secondary"
                    @click="closeModal"
                  >
                    Cancelar
                  </button>
                  <button
                    type="submit"
                    class="btn-primary"
                    :disabled="
                      !form.title || !!availabilityError || !form.specialistId
                    "
                  >
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
import axios from "axios";
import {
  Dialog,
  DialogPanel,
  DialogTitle,
  TransitionRoot,
  TransitionChild,
} from "@headlessui/vue";

const isModalOpen = ref(false);
const isEditing = ref(false);
const currentId = ref(null);
const events = ref([]);
const availableServices = ref([]); // Se llenará desde la API
const searchDoc = ref("");
const searchError = ref("");
const availabilityError = ref(null);

const specialists = ref([
  { id: 1, name: "Rodrigo Estigarribia", area: "Fisioterapia", workDays: [1, 2, 3, 4, 5], start: "08:00", end: "18:00" },
  { id: 2, name: "Dra. Ana García", area: "Masajes", workDays: [1, 3, 5], start: "09:00", end: "14:00" },
]);

const form = reactive({
  title: "",
  start: "",
  end: "",
  obs: "",
  clientId: null,
  specialistId: "",
  price: 0,
  selectedServiceIds: [],
});

onMounted(async () => {
  // Carga inicial de servicios desde la base de datos
  await fetchServicios();
  // Carga de reservas existentes
  await fetchReservas();
});

const fetchServicios = async () => {
  try {
    const response = await axios.get("http://localhost:8000/api/servicios");
    availableServices.value = response.data;
  } catch (error) {
    console.error("Error cargando servicios:", error);
  }
};

const fetchReservas = async () => {
  try {
    const response = await axios.get("http://localhost:8000/api/reservas");
    events.value = response.data.map(res => ({
      id: res.id.toString(),
      title: res.usuario?.nombre || "Cita",
      start: `${res.fecha}T${res.hora_inicio}`,
      end: `${res.fecha}T${res.hora_fin}`,
      backgroundColor: '#38a89d',
      extendedProps: {
        price: res.monto,
        specialistId: res.especialista_id,
        obs: res.notas
      }
    }));
  } catch (error) {
    console.error("Error cargando reservas:", error);
  }
};

const isServiceSelected = (id) => form.selectedServiceIds.includes(id);

const toggleService = (service) => {
  const index = form.selectedServiceIds.indexOf(service.id);
  if (index > -1) {
    form.selectedServiceIds.splice(index, 1);
    form.price -= service.precio || 0;
  } else {
    form.selectedServiceIds.push(service.id);
    form.price += service.precio || 0;
  }
};

const searchClientByDoc = async () => {
  if (!searchDoc.value) return;
  try {
    const response = await axios.get(`http://localhost:8000/api/usuarios/buscar/${searchDoc.value}`);
    if (response.data) {
      form.title = response.data.nombre; 
      form.clientId = response.data.id;
      searchError.value = "";
    } else {
      form.title = "";
      form.clientId = null;
      searchError.value = "⚠️ Cliente no registrado.";
    }
  } catch (error) {
    searchError.value = "⚠️ Error al conectar con el servidor.";
  }
};

const validateAppointment = () => {
  availabilityError.value = null;
  const spec = specialists.value.find((s) => s.id === form.specialistId);
  if (!spec || !form.start || !form.end) return;

  const dStart = new Date(form.start);
  const dEnd = new Date(form.end);
  const timeStr = (d) => d.toTimeString().slice(0, 5);

  if (!spec.workDays.includes(dStart.getDay())) {
    availabilityError.value = `${spec.name} no trabaja este día.`;
    return;
  }
  if (timeStr(dStart) < spec.start || timeStr(dEnd) > spec.end) {
    availabilityError.value = `Fuera de horario (${spec.start} - ${spec.end}).`;
    return;
  }
  
  const hasConflict = events.value.some((e) => {
    if (isEditing.value && e.id === currentId.value) return false;
    const sameSpec = e.extendedProps.specialistId === spec.id;
    const overlaps = dStart.getTime() < new Date(e.end).getTime() && dEnd.getTime() > new Date(e.start).getTime();
    return sameSpec && overlaps;
  });

  if (hasConflict) availabilityError.value = "Especialista ocupado en este horario.";
};

const calendarOptions = reactive({
  plugins: [timeGridPlugin, interactionPlugin],
  initialView: "timeGridWeek",
  locale: esLocale,
  allDaySlot: false,
  selectable: true,
  editable: true,
  events: events,
  headerToolbar: {
    left: "prev,next today",
    center: "title",
    right: "timeGridWeek,timeGridDay",
  },
  select: (info) => {
    isEditing.value = false;
    searchDoc.value = "";
    availabilityError.value = null;
    Object.assign(form, {
      title: "",
      start: info.startStr.slice(0, 16),
      end: info.endStr.slice(0, 16),
      price: 0,
      specialistId: "",
      selectedServiceIds: [],
      obs: "",
    });
    isModalOpen.value = true;
  },
  eventClick: (info) => {
    isEditing.value = true;
    currentId.value = info.event.id;
    const p = info.event.extendedProps;
    Object.assign(form, {
      title: info.event.title,
      start: info.event.startStr.slice(0, 16),
      end: info.event.endStr ? info.event.endStr.slice(0, 16) : info.event.startStr.slice(0, 16),
      price: p.price || 0,
      specialistId: p.specialistId || "",
      obs: p.obs,
    });
    isModalOpen.value = true;
  },
});

const saveReservation = async () => {
  validateAppointment();
  if (availabilityError.value) return;

  const dataToSend = {
    usuario_id: form.clientId,
    especialista_id: form.specialistId,
    fecha: form.start.split('T')[0],
    hora_inicio: form.start.split('T')[1],
    hora_fin: form.end.split('T')[1],
    monto: form.price,
    notas: form.obs,
    estado: 'confirmada'
  };

  try {
    const response = await axios.post("http://localhost:8000/api/reservas", dataToSend);
    if (response.data) {
      await fetchReservas(); // Recargamos para ver cambios
      isModalOpen.value = false;
      alert("✅ Reserva guardada");
    }
  } catch (error) {
    alert("❌ Error al guardar");
  }
};

const deleteReservation = async () => {
  if (confirm("¿Eliminar esta cita?")) {
    try {
      await axios.delete(`http://localhost:8000/api/reservas/${currentId.value}`);
      await fetchReservas();
      isModalOpen.value = false;
    } catch (error) {
      alert("Error al eliminar");
    }
  }
};

const closeModal = () => { isModalOpen.value = false; };
</script>

<style scoped>
/* Los estilos se mantienen igual para no romper la estética */
.calendar-page { padding: 30px; background: #f9f7f2; min-height: 100vh; }
.calendar-card { background: white; padding: 20px; border-radius: 20px; border: 1px solid #e5e0d8; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
.modal-fixed-container { position: fixed; inset: 0; display: flex; align-items: center; justify-content: center; z-index: 101; background: rgba(0,0,0,0.3); backdrop-filter: blur(4px); padding: 20px; }
.modal-panel { background: white; padding: 2rem; border-radius: 30px; width: 100%; max-width: 550px; max-height: 90vh; overflow-y: auto; }
.modal-title { font-size: 1.5rem; font-weight: 700; text-align: center; margin-bottom: 1.5rem; }
.form-section { margin-bottom: 1.2rem; display: flex; flex-direction: column; gap: 0.8rem; }
.section-label { font-size: 0.75rem; font-weight: 800; text-transform: uppercase; color: #38a89d; }
.highlight-services { background: #f0f7f6; padding: 1.2rem; border-radius: 18px; border: 1px solid #d1e7e4; }
.highlight-specialist { background: #fdf2f8; padding: 1.2rem; border-radius: 18px; border: 1px solid #fce7f3; }
.services-selection-grid { display: flex; flex-wrap: wrap; gap: 8px; }
.service-chip { padding: 8px 16px; border-radius: 50px; background: white; border: 1px solid #e2e8f0; font-size: 0.8rem; cursor: pointer; transition: 0.2s; }
.service-chip.active { background: #38a89d; color: white; border-color: #38a89d; }
.form-actions { display: flex; align-items: center; gap: 12px; margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #edf2f7; }
.btn-primary { background: #38a89d; color: white; border: none; padding: 12px 24px; border-radius: 50px; font-weight: 600; cursor: pointer; }
.btn-search { background: #38a89d; color: white; border: none; padding: 0 12px; border-radius: 10px; cursor: pointer; }
.search-input-group { display: flex; gap: 8px; }
.readonly-input { background: #f7fafc; border-style: dashed; }
input, select, textarea { width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 12px; }
.availability-alert { background: #fff1f2; color: #e11d48; padding: 12px; border-radius: 12px; margin-bottom: 20px; text-align: center; }
.spacer { flex: 1; }
.btn-delete { color: #e53e3e; background: none; border: none; cursor: pointer; font-weight: 600; }
</style>