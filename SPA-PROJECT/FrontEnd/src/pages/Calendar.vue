<template>
  <div class="calendar-page">
    <div class="calendar-card">
      <FullCalendar ref="fullCalendar" :options="calendarOptions" />
    </div>

    <TransitionRoot appear :show="isModalOpen" as="template">
      <Dialog as="div" @close="closeModal" class="modal-root">
        <TransitionChild
          as="template"
          enter="duration-300 ease-out"
          enter-from="opacity-0"
          enter-to="opacity-100"
          leave="duration-200 ease-in"
          leave-from="opacity-100"
          leave-to="opacity-0"
        >
          <div class="modal-overlay" />
        </TransitionChild>

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
                {{ isEditing ? "Modificar Cita" : "Nueva Reserva" }}
              </DialogTitle>

              <form @submit.prevent="saveReservation" class="spa-form">
                <div class="form-group">
                  <label>Nombre del Cliente / Asunto</label>
                  <input
                    v-model="form.title"
                    type="text"
                    placeholder="Ej: Masaje de Piedras Volcánicas"
                    required
                  />
                </div>

                <div class="form-row">
                  <div class="form-group">
                    <label>Hora de Inicio</label>
                    <input
                      v-model="form.start"
                      type="datetime-local"
                      required
                    />
                  </div>
                  <div class="form-group">
                    <label>Hora de Fin</label>
                    <input v-model="form.end" type="datetime-local" required />
                  </div>
                </div>

                <div class="form-group">
                  <label>Estado de la Reserva</label>
                  <select v-model="form.status" class="status-select">
                    <option value="pending">⏳ Pendiente de Confirmar</option>
                    <option value="confirmed">✅ Cita Confirmada</option>
                    <option value="cancelled">❌ Cancelada</option>
                  </select>
                </div>

                <div class="form-group">
                  <label>Observaciones de Salud / Notas</label>
                  <textarea
                    v-model="form.obs"
                    placeholder="Detalles importantes sobre el cliente..."
                  ></textarea>
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
                    Cerrar
                  </button>
                  <button type="submit" class="btn-primary">
                    {{ isEditing ? "Actualizar Datos" : "Agendar Ahora" }}
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
import { reactive, ref } from "vue";
import FullCalendar from "@fullcalendar/vue3";
import timeGridPlugin from "@fullcalendar/timegrid";
import interactionPlugin from "@fullcalendar/interaction";
import esLocale from "@fullcalendar/core/locales/es";
import {
  Dialog,
  DialogPanel,
  DialogTitle,
  TransitionRoot,
  TransitionChild,
} from "@headlessui/vue";

// --- REACTIVIDAD ---
const isModalOpen = ref(false);
const isEditing = ref(false);
const currentId = ref(null);
const events = ref([]); // El array de eventos es reactivo

const form = reactive({
  title: "",
  start: "",
  end: "",
  status: "pending",
  obs: "",
});

// --- CONFIGURACIÓN DEL CALENDARIO ---
const calendarOptions = reactive({
  plugins: [timeGridPlugin, interactionPlugin],
  initialView: "timeGridWeek",
  locale: esLocale,
  slotDuration: "00:30:00",
  slotMinTime: "08:00:00",
  slotMaxTime: "21:00:00",
  allDaySlot: false,
  selectable: true,
  editable: true,
  nowIndicator: true,
  headerToolbar: {
    left: "prev,next today",
    center: "title",
    right: "timeGridWeek,timeGridDay",
  },
  events: events,

  // Crear nueva reserva al arrastrar
  select: (info) => {
    isEditing.value = false;
    form.title = "";
    form.start = info.startStr.slice(0, 16);
    form.end = info.endStr.slice(0, 16);
    form.status = "pending";
    form.obs = "";
    isModalOpen.value = true;
  },

  // Editar al hacer clic
  eventClick: (info) => {
    isEditing.value = true;
    currentId.value = info.event.id;
    form.title = info.event.title;
    form.start = info.event.startStr.slice(0, 16);
    form.end = info.event.endStr.slice(0, 16);
    form.status = info.event.extendedProps.status;
    form.obs = info.event.extendedProps.obs;
    isModalOpen.value = true;
  },
});

// --- LÓGICA DE GUARDADO ---
const saveReservation = () => {
  const statusConfig = {
    pending: { bg: "#FEF3C7", border: "#F59E0B", text: "#92400E" },
    confirmed: { bg: "#D1FAE5", border: "#10B981", text: "#065F46" },
    cancelled: { bg: "#FEE2E2", border: "#EF4444", text: "#991B1B" },
  };

  const eventData = {
    id: isEditing.value ? currentId.value : Date.now().toString(),
    title: form.title,
    start: form.start,
    end: form.end,
    backgroundColor: statusConfig[form.status].bg,
    borderColor: statusConfig[form.status].border,
    textColor: statusConfig[form.status].text,
    extendedProps: { status: form.status, obs: form.obs },
    className: `status-border-${form.status}`,
  };

  if (isEditing.value) {
    const idx = events.value.findIndex((e) => e.id === currentId.value);
    if (idx !== -1) events.value[idx] = eventData;
  } else {
    events.value.push(eventData);
  }
  closeModal();
};

const deleteReservation = () => {
  events.value = events.value.filter((e) => e.id !== currentId.value);
  closeModal();
};

const closeModal = () => {
  isModalOpen.value = false;
};
</script>

<style scoped>
/* RECONSTRUCCIÓN DE LA VISTA PREMIUM */
.calendar-page {
  padding: 40px;
  background-color: #f9f7f2;
  min-height: 100vh;
  font-family: "Inter", sans-serif;
}
.calendar-card {
  background: white;
  padding: 30px;
  border-radius: 20px;
  box-shadow: 0 10px 30px rgba(141, 170, 145, 0.1);
  border: 1px solid #e5e0d8;
}

/* Cabeceras de días (Color Teal de la imagen) */
:deep(.fc-col-header-cell) {
  background-color: #38a89d !important;
  color: white !important;
  font-weight: 500;
  padding: 12px 0 !important;
  border: none !important;
}
:deep(.fc-toolbar-title) {
  color: #4a4a4a;
  font-weight: 300;
  font-size: 1.6rem !important;
}

/* Botones Superiores (Estilo Arena/Dorado) */
:deep(.fc-button) {
  background-color: transparent !important;
  border: 1px solid #d4a373 !important;
  color: #d4a373 !important;
  border-radius: 50px !important;
  transition: 0.3s;
}
:deep(.fc-button-active) {
  background-color: #d4a373 !important;
  color: white !important;
}

/* Estilo de Bloques de Tiempo */
:deep(.fc-event) {
  border: none !important;
  border-radius: 0 8px 8px 0 !important;
  margin: 1px !important;
  padding: 2px 8px !important;
}
:deep(.status-border-pending) {
  border-left: 6px solid #f59e0b !important;
}
:deep(.status-border-confirmed) {
  border-left: 6px solid #10b981 !important;
}
:deep(.status-border-cancelled) {
  border-left: 6px solid #ef4444 !important;
  text-decoration: line-through;
  opacity: 0.7;
}

/* MODAL PREMIUM RECONSTRUIDO */
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.2);
  backdrop-filter: blur(4px);
  z-index: 100;
}
.modal-fixed-container {
  position: fixed;
  inset: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 101;
  padding: 1rem;
}
.modal-panel {
  background: white;
  padding: 2.5rem;
  border-radius: 24px;
  width: 100%;
  max-width: 500px;
  box-shadow: 0 25px 50px rgba(0, 0, 0, 0.1);
}
.modal-title {
  font-size: 1.6rem;
  color: #4a4a4a;
  margin-bottom: 2rem;
  font-weight: 300;
  text-align: center;
}

/* Formulario */
.spa-form {
  display: flex;
  flex-direction: column;
  gap: 1.2rem;
}
.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
}
.form-group label {
  display: block;
  font-size: 0.75rem;
  text-transform: uppercase;
  color: #a3a3a3;
  margin-bottom: 5px;
  letter-spacing: 0.05rem;
}
input,
select,
textarea {
  width: 100%;
  padding: 12px;
  border: 1px solid #e5e0d8;
  border-radius: 12px;
  background: #fdfdfb;
}
textarea {
  min-height: 80px;
  resize: none;
}

.form-actions {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-top: 1.5rem;
}
.spacer {
  flex: 1;
}
.btn-primary {
  background: #8daa91;
  color: white;
  padding: 12px 24px;
  border-radius: 50px;
  border: none;
  cursor: pointer;
  font-weight: 500;
}
.btn-secondary {
  background: #f3f4f6;
  color: #6b7280;
  padding: 12px 24px;
  border-radius: 50px;
  border: none;
  cursor: pointer;
}
.btn-delete {
  background: transparent;
  color: #ef4444;
  padding: 10px;
  border: none;
  cursor: pointer;
  font-size: 0.9rem;
}
</style>
