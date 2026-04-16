// src/services/ApiReservas.js
const BASE_URL = "http://127.0.0.1:8000/api";

export const apiReservas = {

  // GET /api/reservas
  async getReservas() {
    const res = await fetch(`${BASE_URL}/reservas`, {
      headers: { "Accept": "application/json" },
    });
    if (!res.ok) throw new Error("Error al obtener reservas");
    return res.json();
  },

  // GET /api/reservas/form-data — clientes, terapeutas y servicios para los selects
  async getFormData() {
    const res = await fetch(`${BASE_URL}/reservas/form-data`, {
      headers: { "Accept": "application/json" },
    });
    if (!res.ok) throw new Error("Error al obtener datos del formulario");
    return res.json();
  },

  // POST /api/reservas
  async createReserva(data) {
    const res = await fetch(`${BASE_URL}/reservas`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "Accept": "application/json",
      },
      body: JSON.stringify(data),
    });
    return res.json();
  },

  // PUT /api/reservas/{id}
  async updateReserva(id, data) {
    const res = await fetch(`${BASE_URL}/reservas/${id}`, {
      method: "PUT",
      headers: {
        "Content-Type": "application/json",
        "Accept": "application/json",
      },
      body: JSON.stringify(data),
    });
    return res.json();
  },

  // DELETE /api/reservas/{id}
  async deleteReserva(id) {
    const res = await fetch(`${BASE_URL}/reservas/${id}`, {
      method: "DELETE",
      headers: { "Accept": "application/json" },
    });
    if (res.status === 204) return { success: true };
    return res.json();
  },
};