import axios from 'axios';

export const API_BASE = (import.meta.env.VITE_API_URL || 'http://127.0.0.1:8000').replace(/\/$/, '');

export function getAuthToken() {
  return (
    localStorage.getItem('token_admin') ||
    localStorage.getItem('token_teacher') ||
    localStorage.getItem('token_nguoi_dung') ||
    localStorage.getItem('token_khach_hang') ||
    ''
  );
}

export const http = axios.create({
  baseURL: `${API_BASE}/api`,
});

http.interceptors.request.use((config) => {
  const token = getAuthToken();
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

/** Public requests (login, footer, etc.) — no Bearer header */
export const publicHttp = axios.create({
  baseURL: `${API_BASE}/api`,
});

export function storageUrl(path) {
  if (!path) return '';
  if (/^https?:\/\//i.test(path)) return path;
  return `${API_BASE}/storage/${String(path).replace(/^\/+/, '')}`;
}
