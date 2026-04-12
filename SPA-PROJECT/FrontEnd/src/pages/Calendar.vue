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
                  <label class="section-label"
                    >Asignación de Especialista</label
                  >
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
                  <label class="section-label">Servicios Solicitados</label>
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
                      {{ service.name }}
                    </div>
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

                <div class="form-section highlight-payment">
                  <label class="section-label">Información de Cobro</label>
                  <div class="form-row payment-grid">
                    <div class="form-group">
                      <label>Monto Total</label>
                      <div class="price-input-wrapper">
                        <input
                          v-model.number="form.price"
                          type="number"
                          class="highlight-price"
                        />
                        <span class="currency-tag">Gs.</span>
                      </div>
                    </div>
                    <div class="form-group">
                      <label>Método de Pago</label>
                      <select
                        v-model="form.paymentMethod"
                        class="payment-select"
                      >
                        <option value="efectivo">💵 Efectivo</option>
                        <option value="transferencia">📱 Transferencia</option>
                        <option value="tarjeta">💳 Tarjeta</option>
                      </select>
                    </div>
                  </div>
                </div>

                <div class="form-section">
                  <div class="form-group">
                    <label>Estado de la Cita</label>
                    <select
                      v-model="form.status"
                      :class="form.status"
                      class="status-select"
                    >
                      <option value="pending">⏳ Pendiente</option>
                      <option value="confirmed">✅ Confirmada</option>
                      <option value="cancelled">❌ Cancelada</option>
                    </select>
                  </div>
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
const availableServices = ref([]);
const searchDoc = ref("");
const searchError = ref("");
const availabilityError = ref(null);

// LISTA DE ESPECIALISTAS CON SUS REGLAS DE DISPONIBILIDAD
const specialists = ref([
  {
    id: 1,
    name: "Rodrigo Estigarribia",
    area: "Fisioterapia",
    workDays: [1, 2, 3, 4, 5],
    start: "08:00",
    end: "18:00",
  },
  {
    id: 2,
    name: "Dra. Ana García",
    area: "Masajes",
    workDays: [1, 3, 5],
    start: "09:00",
    end: "14:00",
  },
]);

const form = reactive({
  title: "",
  start: "",
  end: "",
  status: "pending",
  obs: "",
  clientId: null,
  specialistId: "", // Nuevo
  price: 0,
  paymentMethod: "efectivo",
  selectedServiceIds: [],
});

onMounted(() => {
  events.value = JSON.parse(localStorage.getItem("spa-events") || "[]");
  availableServices.value = JSON.parse(
    localStorage.getItem("spa-services") || "[]",
  );
});

const isServiceSelected = (id) => form.selectedServiceIds.includes(id);

const toggleService = (service) => {
  const index = form.selectedServiceIds.indexOf(service.id);
  if (index > -1) {
    form.selectedServiceIds.splice(index, 1);
    form.price -= service.price;
  } else {
    form.selectedServiceIds.push(service.id);
    form.price += service.price;
  }
};

const searchClientByDoc = () => {
  const savedClients = JSON.parse(localStorage.getItem("spa-clients") || "[]");
  const client = savedClients.find(
    (c) => (c.document || c.ruc || "").toString() === searchDoc.value,
  );
  if (client) {
    form.title = client.name || client.nombre;
    form.clientId = client.id;
    searchError.value = "";
  } else {
    searchError.value = "⚠️ No encontrado.";
  }
};

// NUEVA FUNCIÓN: VALIDAR DISPONIBILIDAD Y HORARIOS
const validateAppointment = () => {
  availabilityError.value = null;
  const spec = specialists.value.find((s) => s.id === form.specialistId);
  if (!spec || !form.start || !form.end) return;

  const dStart = new Date(form.start);
  const dEnd = new Date(form.end);
  const timeStr = (d) => d.toTimeString().slice(0, 5);

  // 1. Validar Día Laboral
  if (!spec.workDays.includes(dStart.getDay())) {
    availabilityError.value = `${spec.name} no trabaja los ${dStart.toLocaleDateString("es-ES", { weekday: "long" })}s.`;
    return;
  }
  // 2. Validar Rango Horario
  if (timeStr(dStart) < spec.start || timeStr(dEnd) > spec.end) {
    availabilityError.value = `Horario fuera de rango para ${spec.name} (${spec.start} - ${spec.end}).`;
    return;
  }
  // 3. Validar Choques (mismo especialista)
  const hasConflict = events.value.some((e) => {
    if (isEditing.value && e.id === currentId.value) return false;
    const sameSpec = e.extendedProps.specialistId === spec.id;
    const overlaps =
      dStart.getTime() < new Date(e.end).getTime() &&
      dEnd.getTime() > new Date(e.start).getTime();
    return sameSpec && overlaps;
  });

  if (hasConflict)
    availabilityError.value =
      "El especialista ya tiene una cita asignada en este horario.";
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
      status: "pending",
      paymentMethod: "efectivo",
      obs: "",
    });
    isModalOpen.value = true;
  },
  eventClick: (info) => {
    isEditing.value = true;
    currentId.value = info.event.id;
    availabilityError.value = null;
    const p = info.event.extendedProps;
    Object.assign(form, {
      title: info.event.title,
      start: info.event.startStr.slice(0, 16),
      end: info.event.endStr
        ? info.event.endStr.slice(0, 16)
        : info.event.startStr.slice(0, 16),
      price: p.price || 0,
      specialistId: p.specialistId || "",
      selectedServiceIds: p.selectedServiceIds || [],
      status: p.status,
      paymentMethod: p.paymentMethod,
      obs: p.obs,
    });
    isModalOpen.value = true;
  },
});

const saveReservation = () => {
  validateAppointment();
  if (availabilityError.value) return;

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
    extendedProps: { ...form },
  };

  if (isEditing.value) {
    const idx = events.value.findIndex((e) => e.id === currentId.value);
    events.value[idx] = eventData;
  } else {
    events.value.push(eventData);
  }
  localStorage.setItem("spa-events", JSON.stringify(events.value));
  isModalOpen.value = false;
};

const deleteReservation = () => {
  events.value = events.value.filter((e) => e.id !== currentId.value);
  localStorage.setItem("spa-events", JSON.stringify(events.value));
  isModalOpen.value = false;
};

const closeModal = () => {
  isModalOpen.value = false;
};
</script>

<style scoped>
/* SE MANTIENEN TUS ESTILOS ORIGINALES */
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
}

/* NUEVOS ESTILOS PARA ESPECIALISTAS Y ALERTAS */
.highlight-specialist {
  background: #fdf2f8;
  padding: 1.2rem;
  border-radius: 18px;
  border: 1px solid #fce7f3;
}
.specialist-select {
  border-color: #ec4899 !important;
  font-weight: 600;
  color: #9d174d;
}

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

/* RESTO DE TUS ESTILOS ORIGINALES SIN TOCAR */
.modal-fixed-container {
  position: fixed;
  inset: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 101;
  background: rgba(0, 0, 0, 0.3);
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
.highlight-services {
  background: #f0f7f6;
  padding: 1.2rem;
  border-radius: 18px;
  border: 1px solid #d1e7e4;
}
.highlight-payment {
  background: #f8fafc;
  padding: 1.2rem;
  border-radius: 18px;
  border: 1px solid #e2e8f0;
}
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
  gap: 25px;
  align-items: flex-start;
}
input,
select,
textarea {
  width: 100%;
  padding: 12px;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  font-size: 0.9rem;
}
.readonly-input {
  background: #f7fafc;
  border-style: dashed;
}
.price-input-wrapper {
  position: relative;
  display: flex;
  align-items: center;
}
.highlight-price {
  font-weight: 800 !important;
  font-size: 1.1rem !important;
  padding-right: 45px !important;
  border-color: #38a89d !important;
}
.currency-tag {
  position: absolute;
  right: 12px;
  font-weight: 700;
  color: #94a3b8;
  font-size: 0.75rem;
  pointer-events: none;
}
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
  transition: 0.2s;
}
.service-chip.active {
  background: #38a89d;
  color: white;
  border-color: #38a89d;
}
.form-actions {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-top: 1.5rem;
  padding-top: 1.5rem;
  border-top: 1px solid #edf2f7;
}
.spacer {
  flex: 1;
}
.btn-primary {
  background: #38a89d;
  color: white;
  border: none;
  padding: 12px 24px;
  border-radius: 50px;
  font-weight: 600;
  cursor: pointer;
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
.status-select.pending {
  color: #d97706;
  font-weight: bold;
}
.status-select.confirmed {
  color: #059669;
  font-weight: bold;
}
.search-input-group {
  display: flex;
  gap: 8px;
}
.btn-search {
  background: #38a89d;
  color: white;
  border: none;
  padding: 0 12px;
  border-radius: 10px;
  cursor: pointer;
}
</style>
