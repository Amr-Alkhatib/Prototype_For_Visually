import client from './client';

// Authentication APIs
export const authAPI = {
  register: async (name, email, password, passwordConfirmation, tumId = null) => {
    return client.post('/auth/register', {
      name,
      email,
      password,
      password_confirmation: passwordConfirmation,
      tum_id: tumId,
    });
  },

  login: async (email, password) => {
    return client.post('/auth/login', {
      email,
      password,
    });
  },

  logout: async () => {
    return client.post('/auth/logout');
  },

  getCurrentUser: async () => {
    return client.get('/auth/me');
  },

  verifyTumOnline: async (tumId, password) => {
    return client.post('/auth/verify-tum', {
      tum_id: tumId,
      password,
    });
  },
};

// Simulation APIs
export const simulationAPI = {
  getAll: async (page = 1, perPage = 15, sortBy = 'created_at', order = 'desc') => {
    return client.get('/simulations', {
      params: {
        page,
        per_page: perPage,
        sort_by: sortBy,
        order,
      },
    });
  },

  create: async (simulationData) => {
    return client.post('/simulations', simulationData);
  },

  get: async (id) => {
    return client.get(`/simulations/${id}`);
  },

  update: async (id, simulationData) => {
    return client.put(`/simulations/${id}`, simulationData);
  },

  delete: async (id) => {
    return client.delete(`/simulations/${id}`);
  },

  bulkDelete: async (ids) => {
    return client.post('/simulations/bulk-delete', {
      ids,
    });
  },
};

export default { authAPI, simulationAPI };
