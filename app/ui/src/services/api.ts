import axios from 'axios';

const API_BASE_URL = import.meta.env.VITE_API_URL || 'http://localhost:8000/api';

class ApiService {
  private api: ReturnType<typeof axios.create>;

  constructor() {
    this.api = axios.create({
      baseURL: API_BASE_URL,
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
    });

    // Request interceptor to add JWT token
    this.api.interceptors.request.use(
      (config) => {
        const token = localStorage.getItem('auth_token');
        if (token) {
          config.headers.Authorization = `Bearer ${token}`;
        }
        return config;
      },
      (error) => {
        return Promise.reject(error);
      }
    );

    // Response interceptor to handle errors
    this.api.interceptors.response.use(
      (response) => response,
      (error: any) => {
        if (error.response?.status === 401) {
          // Unauthorized - clear token and redirect to login
          localStorage.removeItem('auth_token');
          localStorage.removeItem('user');
          window.location.href = '/login';
        }
        return Promise.reject(error);
      }
    );
  }

  // Auth endpoints
  async login(username: string, password: string) {
    const response = await this.api.post('/auth/login', { username, password });
    if (response.data.success && response.data.token) {
      localStorage.setItem('auth_token', response.data.token);
      localStorage.setItem('user', JSON.stringify(response.data.user));
    }
    return response.data;
  }

  async logout() {
    const response = await this.api.post('/auth/logout');
    localStorage.removeItem('auth_token');
    localStorage.removeItem('user');
    return response.data;
  }

  async getMe() {
    const response = await this.api.get('/auth/me');
    return response.data;
  }

  // SMS Inbox endpoints
  async getInbox(params?: { page?: number; per_page?: number; is_read?: boolean; search?: string }) {
    try {
      const response = await this.api.get('/sms/inbox', { params });
      return response.data;
    } catch (error: any) {
      console.error('getInbox error:', error);
      // Return error structure instead of throwing
      return {
        success: false,
        error: error.response?.data?.message || error.message || 'Failed to load inbox',
        data: [],
        pagination: null
      };
    }
  }

  async syncInbox() {
    const response = await this.api.post('/sms/inbox/sync');
    return response.data;
  }

  async markAsRead(id: number) {
    const response = await this.api.put(`/sms/inbox/${id}/read`);
    return response.data;
  }

  async markAllAsRead() {
    const response = await this.api.put('/sms/inbox/read-all');
    return response.data;
  }

  async getMessage(id: number) {
    const response = await this.api.get(`/sms/inbox/${id}`);
    return response.data;
  }

  // SMS Outbox endpoints
  async getOutbox(params?: { page?: number; per_page?: number; status?: string }) {
    const response = await this.api.get('/sms/outbox', { params });
    return response.data;
  }

  async sendSms(phoneNumber: string, message: string, line?: number) {
    const response = await this.api.post('/sms/send', {
      phone_number: phoneNumber,
      message,
      line,
    });
    return response.data;
  }

  async getOutboxMessage(id: number) {
    const response = await this.api.get(`/sms/outbox/${id}`);
    return response.data;
  }

  // SMS Blast endpoints
  async getBatches(params?: { page?: number; per_page?: number }) {
    const response = await this.api.get('/sms/blast', { params });
    return response.data;
  }

  async createBlast(name: string, message: string, phoneNumbers: string[], line?: number) {
    const response = await this.api.post('/sms/blast', {
      name,
      message,
      phone_numbers: phoneNumbers,
      line,
    });
    return response.data;
  }

  async uploadCsvBlast(name: string, message: string, csvFile: File, line?: number) {
    const formData = new FormData();
    formData.append('name', name);
    formData.append('message', message);
    formData.append('csv_file', csvFile);
    if (line) {
      formData.append('line', line.toString());
    }

    const response = await this.api.post('/sms/blast/upload-csv', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });
    return response.data;
  }

  async getBatch(id: number) {
    const response = await this.api.get(`/sms/blast/${id}`);
    return response.data;
  }

  async getBatchProgress(id: number) {
    const response = await this.api.get(`/sms/blast/${id}/progress`);
    return response.data;
  }
}

export default new ApiService();
