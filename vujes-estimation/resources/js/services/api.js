const defaultHeaders = {
  'Content-Type': 'application/json',
  Accept: 'application/json',
};

function getXsrfToken() {
  const match = document.cookie.match(new RegExp('(^| )XSRF-TOKEN=([^;]+)'));
  return match ? decodeURIComponent(match[2]) : null;
}

async function request(url, originalMethod = 'GET', body = null) {
  const options = {
    method: originalMethod,
    credentials: 'include',
    headers: { ...defaultHeaders },
  };

  if (['POST', 'PUT', 'PATCH', 'DELETE'].includes(method.toUpperCase())) {
    const token = getXsrfToken();
    if (token) {
      options.headers['X-XSRF-TOKEN'] = token;
    }
  }

  if (body) {
    options.body = JSON.stringify(body);
  }

  const response = await fetch(url, options);

  if (!response.ok) {
    const errorData = await response.json().catch(() => ({}));
    throw errorData;
  }

  const contentType = response.headers.get('content-type');
  if (contentType && contentType.includes('application/json')) {
    return await response.json();
  }
  return true;
}

export default {
  getUsers: () => request('/api/users'),
  login: async (id) => {
    await request('/sanctum/csrf-cookie', 'GET');
    return request('/api/login', 'POST', { id });
  },
  logout: () => request('/api/logout', 'POST'),

  getClients: () => request('/api/clients'),
  saveClient: (data, id = null) =>
    request(id ? `/api/clients/${id}` : '/api/clients', id ? 'PUT' : 'POST', data),
  deleteClient: (id) => request(`/api/clients/${id}`, 'DELETE'),

  getProjects: () => request('/api/projects'),
  saveProject: (data, id = null) =>
    request(id ? `/api/projects/${id}` : '/api/projects', id ? 'PUT' : 'POST', data),
  deleteProject: (id) => request(`/api/projects/${id}`, 'DELETE'),

  getEstimations: () => request('/api/estimations'),
  saveEstimation: (data, id = null) =>
    request(id ? `/api/estimations/${id}` : '/api/estimations', id ? 'PUT' : 'POST', data),
  deleteEstimation: (id) => request(`/api/estimations/${id}`, 'DELETE'),
};
