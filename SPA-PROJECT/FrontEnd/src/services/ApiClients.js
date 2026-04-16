import axios from 'axios';

// La URL ahora apunta a la ruta de clientes definida en api.php
const API_URL = 'http://localhost:8000/api/clientes';

export default {
    // 1. Obtener todos los clientes
    async getAll() {
        try {
            const response = await axios.get(API_URL);
            // Mapeamos los campos exactos de la migración de clientes
            return response.data.map(c => ({
                id: c.id,
                name: c.nombres,            // 'nombres' (Laravel) -> 'name' (Vue)
                lastname: c.apellidos,      // 'apellidos' (Laravel) -> 'lastname' (Vue)
                email: c.email,
                phone: c.telefono,
                document: c.numero_documento // 'numero_documento' (Laravel) -> 'document' (Vue)
            }));
        } catch (error) {
            console.error("Error al obtener clientes:", error);
            throw error;
        }
    },

    // 2. Crear un nuevo cliente
    async create(formData) {
        const clienteData = this._mapToBackend(formData);
        return await axios.post(API_URL, clienteData);
    },

    // 3. Actualizar cliente existente
    async update(id, formData) {
        const clienteData = this._mapToBackend(formData);
        return await axios.put(`${API_URL}/${id}`, clienteData);
    },

    // 4. Eliminar cliente
    async delete(id) {
        return await axios.delete(`${API_URL}/${id}`);
    },

    /**
     * FUNCIÓN PRIVADA: Transforma los datos de la vista 
     * al formato exacto de la tabla 'clientes'.
     */
    _mapToBackend(form) {
        return {
            nombres: form.name,             // 'nombres' en migración
            apellidos: form.lastname,       // 'apellidos' en migración
            email: form.email,
            telefono: form.phone,
            numero_documento: form.document // 'numero_documento' en migración
        };
    }
};