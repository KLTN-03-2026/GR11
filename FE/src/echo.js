import { API_BASE } from './api/http.js';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

// Expose Pusher globally (required by laravel-echo)
window.Pusher = Pusher;

let echo = null;

// Only initialize Echo if we have the required Pusher configuration
const hasPusherConfig = import.meta.env.VITE_PUSHER_KEY && import.meta.env.VITE_PUSHER_CLUSTER;

if (hasPusherConfig) {
  try {
    const isSelfHosted = !!import.meta.env.VITE_PUSHER_HOST;
    const useTLS = (import.meta.env.VITE_PUSHER_TLS === 'true');

    const echoOptions = {
      broadcaster: 'pusher',
      key: import.meta.env.VITE_PUSHER_KEY,
      cluster: import.meta.env.VITE_PUSHER_CLUSTER,
      forceTLS: useTLS,
      authEndpoint: (API_BASE).replace(/\/$/, '') + '/api/broadcasting/auth',
      auth: {
        headers: {
          Authorization: 'Bearer ' + (
            localStorage.getItem('token_teacher') ||
            localStorage.getItem('token_admin') ||
            localStorage.getItem('token_nguoi_dung') ||
            localStorage.getItem('token_khach_hang') ||
            ''
          )
        }
      }
    };

    // Nếu dùng Soketi / server self-hosted thì cần wsHost + wsPort
    if (isSelfHosted) {
      echoOptions.wsHost = import.meta.env.VITE_PUSHER_HOST;
      echoOptions.wsPort = parseInt(import.meta.env.VITE_PUSHER_PORT || '6001', 10);
      echoOptions.wssPort = parseInt(import.meta.env.VITE_PUSHER_PORT || '6001', 10);
      echoOptions.enabledTransports = ['ws', 'wss'];
      echoOptions.disableStats = true;
    }

    echo = new Echo(echoOptions);

    // expose globally for legacy components that check window.Echo
    window.Echo = echo;
  } catch (error) {
    console.error('Failed to initialize Laravel Echo:', error);
    // Create a dummy echo object so code doesn't break
    window.Echo = {
      channel: () => ({ on: () => {}, listen: () => {} }),
      private: () => ({ on: () => {}, listen: () => {} }),
      presence: () => ({ on: () => {}, listen: () => {} }),
      leave: () => {},
    };
    echo = window.Echo;
  }
} else {
  console.warn('Laravel Echo is not configured. Set VITE_PUSHER_KEY and VITE_PUSHER_CLUSTER environment variables.');
  // Create a dummy echo object so code doesn't break
  window.Echo = {
    channel: () => ({ on: () => {}, listen: () => {} }),
    private: () => ({ on: () => {}, listen: () => {} }),
    presence: () => ({ on: () => {}, listen: () => {} }),
    leave: () => {},
  };
  echo = window.Echo;
}

export default echo;
