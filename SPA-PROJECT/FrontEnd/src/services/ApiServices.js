// src/services/apiServicios.js
const BASE_URL = "http://127.0.0.1:8000/api";

export const apiServicios = {
  // OBTENER TODOS LOS SERVICIOS
  async getServicios() {
    try {
      const res = await fetch(`${BASE_URL}/servicios`);
      if (!res.ok) throw new Error("Error al obtener servicios");
      return await res.json();
    } catch (error) {
      console.error(error);
      return [];
    }
  },

  // CREAR UN NUEVO SERVICIO
  async createServicio(data) {
    const res = await fetch(`${BASE_URL}/servicios`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "Accept": "application/json",
      },
      body: JSON.stringify(data),
    });
    return await res.json();
  },

  // OBTENER UN SERVICIO ESPECÍFICO
  async getServicio(id) {
    const res = await fetch(`${BASE_URL}/servicios/${id}`);
    if (!res.ok) throw new Error("Servicio no encontrado");
    return await res.json();
  },

  // ACTUALIZAR UN SERVICIO
  async updateServicio(id, data) {
    const res = await fetch(`${BASE_URL}/servicios/${id}`, {
      method: "PUT",
      headers: {
        "Content-Type": "application/json",
        "Accept": "application/json",
      },
      body: JSON.stringify(data),
    });
    return await res.json();
  },

  // ELIMINAR UN SERVICIO
  async deleteServicio(id) {
    const res = await fetch(`${BASE_URL}/servicios/${id}`, {
      method: "DELETE",
      headers: {
        "Accept": "application/json",
      },
    });
    
    // Si Laravel devuelve 204 No Content, no intentamos parsear JSON
    if (res.status === 204) return { success: true };
    return await res.json();
  },
};