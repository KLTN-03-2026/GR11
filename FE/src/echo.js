import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

// Expose Pusher globally (required by laravel-echo)
window.Pusher = Pusher;

let echo = null;

// Only initialize Echo if we have the required Pusher configuration
const hasPusherConfig = import.meta.env.VITE_PUSHER_KEY && import.meta.env.VITE_PUSHER_CLUSTER;

if (hasPusherConfig) {
  try {
    echo = new Echo({
      broadcaster: 'pusher',
      key: import.meta.env.VITE_PUSHER_KEY,
      cluster: import.meta.env.VITE_PUSHER_CLUSTER,
      forceTLS: (import.meta.env.VITE_PUSHER_TLS === 'true') || true,
      authEndpoint: (import.meta.env.VITE_API_URL || 'http://127.0.0.1:8000').replace(/\/$/, '') + '/api/broadcasting/auth',
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
    });

    // expose globally for legacy components that check window.Echo
    window.Echo = echo;
  } catch (error) {
    console.error('Failed to initialize Laravel Echo:', error);
    // Create a dummy echo object so code doesn't break
    window.Echo = {
      channel: () => ({ on: () => {}, listen: () => {} }),
      private: () => ({ on: () => {}, listen: () => {} }),
      presence: () => ({ on: () => {}, listen: () => {} }),
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
  };
  echo = window.Echo;
}

export default echo;
