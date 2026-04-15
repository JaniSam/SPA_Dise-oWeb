// src/services/api.js
const delay = (ms) => new Promise((res) => setTimeout(res, ms));
const BASE_URL = "http://127.0.0.1:8000/api";

export const api = {
  // OBTENER USUARIOS
  async getUsuarios() {
    const res = await fetch(`${BASE_URL}/usuarios`);
    return res.json();
  },

  // CREAR USUARIO
  async createUsuario(data) {
    const res = await fetch(`${BASE_URL}/usuarios`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "Accept": "application/json"
      },
      body: JSON.stringify(data)
    });

    return res.json();
  },

  // Listar Ususario
  async getUsuario(id) {
    const res = await fetch(`${BASE_URL}/usuarios/${id}`);
    return res.json();
  },

  // ACTUALIZAR Usuario
  async updateUsuario(id, data) {
    const res = await fetch(`${BASE_URL}/usuarios/${id}`, {
      method: "PUT",
      headers: {
        "Content-Type": "application/json",
        "Accept": "application/json"
      },
      body: JSON.stringify(data)
    });

    return res.json();
  },

  // ELIMINAR Usuario
  async deleteUsuario(id) {
    const res = await fetch(`${BASE_URL}/usuarios/${id}`, {
      method: "DELETE"
    });

    return res.json();
  },

  // Obtener Citas
  async getEvents() {
    await delay(500); // Simula latencia de red
    return JSON.parse(localStorage.getItem("spa-events") || "[]");
  },

  // Guardar o Actualizar Cita
  async saveEvent(eventData) {
    await delay(700);
    let events = JSON.parse(localStorage.getItem("spa-events") || "[]");
    const index = events.findIndex((e) => e.id === eventData.id);

    if (index !== -1) {
      events[index] = eventData;
    } else {
      events.push(eventData);
    }

    localStorage.setItem("spa-events", JSON.stringify(events));
    return { success: true };
  },

  // Eliminar Cita
  async deleteEvent(id) {
    await delay(400);
    let events = JSON.parse(localStorage.getItem("spa-events") || "[]");
    events = events.filter((e) => e.id !== id);
    localStorage.setItem("spa-events", JSON.stringify(events));
    return { success: true };
  },

  // Obtener Especialistas (En el futuro esto vendrá de tu DB Java)
  async getSpecialists() {
    await delay(300);
    return [
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
      {
        id: 3,
        name: "Carlos Pérez",
        area: "Estética",
        workDays: [2, 4, 6],
        start: "10:00",
        end: "20:00",
      },
    ];
  },
};
