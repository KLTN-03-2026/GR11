<template>
  <div class="chat-teacher">
    <!-- Sidebar: Danh sách các cuộc trò chuyện -->
    <div class="sidebar">
      <h3>Danh sách trò chuyện</h3>
      <div class="chat-search-wrap">
        <i class="fa fa-search"></i>
        <input
          v-model="searchQuery"
          type="text"
          class="chat-search-input"
          placeholder="Tìm theo tên hoặc tin nhắn..."
        >
      </div>
      <div
        v-for="session in filteredSessions"
        :key="session.id"
        @click="selectSession(session)"
        @mouseenter="hoveredSessionId = session.id"
        @mouseleave="onSessionMouseLeave(session.id)"
        :class="['session-item', { active: selectedSession && selectedSession.id === session.id }]"
      >
        <div class="session-top">
          <img
            v-if="session.studentAvatarUrl"
            :src="session.studentAvatarUrl"
            class="avatar-xs"
            alt="avatar học viên"
          />
          <div v-else class="avatar-xs avatar-fallback">
            <i class="fa fa-user"></i>
          </div>
          <div class="session-text">
            <p class="student-name">{{ session.studentName }}</p>
            <p class="lesson">{{ session.lesson }}</p>
            <p class="last-message">{{ session.lastMessage }}</p>
          </div>
          <span v-if="Number(session.unreadCount || 0) > 0" class="unread-badge">
            {{ Number(session.unreadCount) > 99 ? '99+' : Number(session.unreadCount) }}
          </span>
          <div class="session-actions">
            <button
              v-if="hoveredSessionId === session.id || openMenuSessionId === session.id"
              class="session-menu-btn"
              @click.stop="toggleSessionMenu(session.id)"
              title="Tùy chọn"
            >
              <i class="fa-solid fa-ellipsis"></i>
            </button>
            <div
              v-if="openMenuSessionId === session.id"
              class="session-menu-dropdown"
              @click.stop
            >
              <button
                class="session-menu-item danger"
                :disabled="deletingSessionId === session.id"
                @click.stop="deleteSession(session)"
              >
                {{ deletingSessionId === session.id ? 'Đang xóa...' : 'Xóa đoạn chat' }}
              </button>
            </div>
          </div>
        </div>
        <div class="session-meta">
          <span class="timestamp">{{ session.timestamp }}</span>
        </div>
      </div>
    </div>

    <!-- Chat Area: Nội dung chat chi tiết -->
    <div class="chat-area">
      <div v-if="selectedSession" class="chat-content">
        <div class="chat-header">
          <div class="chat-header-user">
            <img
              v-if="selectedSession.studentAvatarUrl"
              :src="selectedSession.studentAvatarUrl"
              class="chat-header-avatar"
              alt="avatar học viên"
            />
            <div v-else class="chat-header-avatar avatar-fallback">
              <i class="fa fa-user"></i>
            </div>
            <h3>{{ selectedSession.studentName }}</h3>
          </div>
        </div>
        <div class="messages" ref="messagesContainer">
          <div
            v-for="msg in selectedSession.messages"
            :key="msg.id"
            :class="['message', msg.sender === 'Giáo viên' ? 'teacher' : 'student']"
          >
            <div class="message-row">
              <img
                v-if="msg.sender !== 'Giáo viên' && selectedSession.studentAvatarUrl"
                :src="selectedSession.studentAvatarUrl"
                class="message-avatar"
                alt="avatar học viên"
              />
              <div v-else-if="msg.sender !== 'Giáo viên'" class="message-avatar avatar-fallback">
                <i class="fa fa-user"></i>
              </div>
              <div class="message-content">
                {{ msg.text }}
              </div>
            </div>
            <small v-if="msg.sender === 'Giáo viên'" class="message-status">
              <i :class="getMessageStatusIcon(msg.status)"></i>
              {{ getMessageStatusText(msg.status) }}
            </small>
            <span class="message-time">{{ msg.time }}</span>
          </div>
        </div>
        <div class="message-input">
          <input
            v-model="newMessage"
            @keyup.enter="sendMessage"
            placeholder="Nhập tin nhắn..."
            ref="messageInput"
            :disabled="sending"
          >
          <button @click="sendMessage" :disabled="sending">{{ sending ? 'Đang gửi...' : 'Gửi' }}</button>
        </div>
      </div>
      <div v-else class="no-selection">
        <p>Chọn một cuộc trò chuyện để bắt đầu.</p>
      </div>
    </div>

    <div v-if="deleteModal.visible" class="delete-modal-backdrop" @click="closeDeleteModal">
      <div class="delete-modal-card" @click.stop>
        <h5 class="mb-2">Xác nhận xóa</h5>
        <p class="mb-3">
          Bạn có chắc muốn xóa đoạn chat với
          <strong>{{ deleteModal.session?.studentName || 'học viên này' }}</strong>?
        </p>
        <div class="delete-modal-actions">
          <button class="btn-cancel" @click="closeDeleteModal" :disabled="deletingSessionId === deleteModal.session?.id">
            Hủy
          </button>
          <button class="btn-delete" @click="confirmDeleteSession" :disabled="deletingSessionId === deleteModal.session?.id">
            {{ deletingSessionId === deleteModal.session?.id ? 'Đang xóa...' : 'Xóa đoạn chat' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'ChatTeacher',
  data() {
    return {
      apiBase: 'http://127.0.0.1:8000',
      sessions: [],
      selectedSession: null,
      searchQuery: '',
      newMessage: '',
      sending: false,
      hoveredSessionId: null,
      openMenuSessionId: null,
      deletingSessionId: null,
      deleteModal: {
        visible: false,
        session: null,
      },
      subscribedSessionIds: new Set(),
      pollInterval: null,
      messagePollInterval: null,
    }
  },
  mounted() {
    this.loadSessions();
    // Poll for new sessions every 30 seconds
    this.pollInterval = setInterval(this.loadSessions, 30000);
    // subscribe to sessions after initial load will be handled in loadSessions
    document.addEventListener('click', this.closeSessionMenuOnOutsideClick);
  },
  beforeUnmount() {
    if (this.pollInterval) {
      clearInterval(this.pollInterval);
    }
    if (this.messagePollInterval) {
      clearInterval(this.messagePollInterval);
    }
    if (window.Echo) {
      this.subscribedSessionIds.forEach((sessionId) => {
        window.Echo.leave(`private-chat-session.${sessionId}`);
      });
    }
    document.removeEventListener('click', this.closeSessionMenuOnOutsideClick);
  },
  watch: {
    selectedSession(newSession, oldSession) {
      if (oldSession && this.messagePollInterval) {
        clearInterval(this.messagePollInterval);
      }
      if (newSession) {
        // Poll for new messages every 10 seconds
        this.messagePollInterval = setInterval(() => {
          this.loadMessages(newSession.id);
        }, 10000);
      }
    }
  },
  computed: {
    filteredSessions() {
      const keyword = String(this.searchQuery || '').trim().toLowerCase();
      if (!keyword) {
        return this.sessions;
      }
      return this.sessions.filter((session) => {
        const name = String(session?.studentName || '').toLowerCase();
        const message = String(session?.lastMessage || '').toLowerCase();
        return name.includes(keyword) || message.includes(keyword);
      });
    },
  },
  methods: {
    resolveAvatarUrl(path) {
      if (!path) return null;
      if (String(path).startsWith('http://') || String(path).startsWith('https://')) return path;
      return `${this.apiBase}/storage/${String(path).replace(/^\/+/, '')}`;
    },
    getAuthToken() {
      return localStorage.getItem('token_teacher') || '';
    },
    authHeaders() {
      const token = this.getAuthToken();
      return token ? { Authorization: 'Bearer ' + token } : {};
    },
    async loadSessions() {
      try {
        const response = await axios.get(`${this.apiBase}/api/teacher/chat/sessions`, {
          headers: this.authHeaders(),
        });
        const rows = Array.isArray(response.data) ? response.data : [];
        this.sessions = rows.map((item) => ({
          ...item,
          unreadCount: Number(item?.unreadCount || 0),
          studentAvatarUrl: this.resolveAvatarUrl(item.studentAvatar),
        }));
        // Subscribe to session channels for real-time updates
        if (window.Echo) {
          this.sessions.forEach(s => {
            if (s && s.id) this.subscribeToSession(s.id);
          });
        }
      } catch (error) {
        console.error('Error loading sessions:', error);
        if (error?.response?.status === 401) {
          this.$toast?.error('Phiên đăng nhập đã hết hạn, vui lòng đăng nhập lại.');
          this.$router.push('/dang-nhap');
        }
      }
    },
    async selectSession(session) {
      this.selectedSession = session;
      this.openMenuSessionId = null;
      await this.loadMessages(session.id);
      this.$nextTick(() => {
        this.scrollToBottom();
        this.focusMessageInput();
      });
    },
    toggleSessionMenu(sessionId) {
      this.openMenuSessionId = this.openMenuSessionId === sessionId ? null : sessionId;
    },
    onSessionMouseLeave(sessionId) {
      if (this.openMenuSessionId !== sessionId) {
        this.hoveredSessionId = null;
      }
    },
    closeSessionMenuOnOutsideClick() {
      this.openMenuSessionId = null;
    },
    deleteSession(session) {
      this.deleteModal = { visible: true, session };
      this.openMenuSessionId = null;
    },
    closeDeleteModal() {
      if (this.deletingSessionId) return;
      this.deleteModal = { visible: false, session: null };
    },
    async confirmDeleteSession() {
      const session = this.deleteModal.session;
      if (!session) return;
      this.deletingSessionId = session.id;
      try {
        await axios.delete(`${this.apiBase}/api/teacher/chat/session/${session.id}`, {
          headers: this.authHeaders(),
        });
        this.sessions = this.sessions.filter((s) => s.id !== session.id);
        if (this.selectedSession?.id === session.id) {
          this.selectedSession = null;
        }
        this.$toast?.success('Đã xóa đoạn chat.');
      } catch (error) {
        this.$toast?.error(error?.response?.data?.message || 'Không thể xóa đoạn chat.');
      } finally {
        this.deletingSessionId = null;
        this.openMenuSessionId = null;
        this.deleteModal = { visible: false, session: null };
      }
    },
    async loadMessages(sessionId) {
      try {
        const response = await axios.get(`${this.apiBase}/api/teacher/chat/session/${sessionId}/messages`, {
          headers: this.authHeaders(),
        });
        this.selectedSession.messages = response.data.messages;
        this.selectedSession.studentName = response.data.session.studentName;
        this.selectedSession.studentAvatarUrl = this.resolveAvatarUrl(response.data.session.studentAvatar);
        this.selectedSession.lesson = response.data.session.lesson;
        this.selectedSession.unreadCount = 0;

        const sessionIndex = this.sessions.findIndex((s) => s.id === sessionId);
        if (sessionIndex !== -1) {
          this.sessions[sessionIndex].unreadCount = 0;
        }
        // scroll after loading
        this.$nextTick(() => this.scrollToBottom());
      } catch (error) {
        console.error('Error loading messages:', error);
      }
    },

    subscribeToSession(sessionId) {
      try {
        if (!window.Echo) return;
        if (this.subscribedSessionIds.has(sessionId)) return;
        const channelName = `chat-session.${sessionId}`;
        const existing = window.Echo.private(channelName);
        // Avoid multiple listeners: try listening and rely on server
        existing.stopListening && existing.stopListening('.StudentSentMessage');
        existing.stopListening && existing.stopListening('StudentSentMessage');
        window.Echo.private(channelName).listen('.StudentSentMessage', (e) => {
          const incomingText = e?.message?.content || '';
          const incomingTime = e?.message?.created_at
            ? new Date(e.message.created_at).toLocaleTimeString('vi-VN', {
                hour: '2-digit',
                minute: '2-digit',
                hour12: false,
                timeZone: 'Asia/Ho_Chi_Minh',
              })
            : '';

          // If the message belongs to the currently opened session, reload messages
          if (this.selectedSession && this.selectedSession.id === sessionId) {
            this.loadMessages(sessionId);
          } else {
            const idx = this.sessions.findIndex((s) => s.id === sessionId);
            if (idx !== -1) {
              const current = this.sessions[idx];
              const nextUnread = Number(current.unreadCount || 0) + 1;
              this.sessions[idx] = {
                ...current,
                unreadCount: nextUnread,
                lastMessage: incomingText || current.lastMessage,
                timestamp: incomingTime || current.timestamp,
              };

              const [updated] = this.sessions.splice(idx, 1);
              this.sessions.unshift(updated);
            } else {
              this.loadSessions();
            }
          }
        });
        this.subscribedSessionIds.add(sessionId);
      } catch (err) {
        console.warn('Echo subscribe error', err);
      }
    },
    async sendMessage() {
      if (this.newMessage.trim() && !this.sending) {
        this.sending = true;
        try {
          const response = await axios.post(
            `${this.apiBase}/api/teacher/chat/session/${this.selectedSession.id}/send`,
            { message: this.newMessage },
            { headers: this.authHeaders() }
          );

          // Add the sent message to the list
          this.selectedSession.messages.push({
            ...response.data,
            status: response?.data?.status || 'sent',
          });
          this.newMessage = '';

          // Update last message in sessions list
          const sessionIndex = this.sessions.findIndex(s => s.id === this.selectedSession.id);
          if (sessionIndex !== -1) {
            this.sessions[sessionIndex].lastMessage = response.data.text;
            this.sessions[sessionIndex].timestamp = response.data.time;
          }

          this.scrollToBottom();
        } catch (error) {
          console.error('Error sending message:', error);
        } finally {
          this.sending = false;
          this.focusMessageInput();
        }
      }
    },
    focusMessageInput() {
      this.$nextTick(() => {
        const input = this.$refs.messageInput;
        if (input && typeof input.focus === 'function') {
          input.focus();
        }
      });
    },
    scrollToBottom() {
      if (this.$refs.messagesContainer) {
        this.$refs.messagesContainer.scrollTop = this.$refs.messagesContainer.scrollHeight;
      }
    },
    getMessageStatusText(status) {
      if (status === 'seen') return 'Đã xem';
      if (status === 'delivered') return 'Đã nhận';
      return 'Đã gửi';
    },
    getMessageStatusIcon(status) {
      if (status === 'seen') return 'fa-solid fa-circle-check';
      if (status === 'delivered') return 'fa-solid fa-check-double';
      return 'fa-solid fa-check';
    },
  }
}
</script>

<style scoped>
.chat-teacher {
  display: flex;
  height: calc(100vh - 72px);
  min-height: 0;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  background-color: #ffffff;
  overflow: hidden;
}

.sidebar {
  width: 350px;
  border-right: 1px solid #e0e0e0;
  padding: 20px;
  background-color: #ffffff;
  box-shadow: 2px 0 5px rgba(0,0,0,0.1);
  overflow-y: auto;
  flex-shrink: 0;
}

.sidebar h3 {
  margin-top: 0;
  color: #333;
  font-size: 18px;
  font-weight: 600;
  margin-bottom: 20px;
}

.chat-search-wrap {
  position: relative;
  margin-bottom: 12px;
}

.chat-search-wrap i {
  position: absolute;
  left: 12px;
  top: 50%;
  transform: translateY(-50%);
  color: #94a3b8;
  font-size: 13px;
}

.chat-search-input {
  width: 100%;
  border: 1px solid #e5e7eb;
  border-radius: 999px;
  background: #fff;
  padding: 10px 12px 10px 34px;
  font-size: 13px;
  color: #111827;
  outline: none;
}

.chat-search-input:focus {
  border-color: #93c5fd;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.12);
}

.session-item {
  padding: 10px 12px;
  border-bottom: 1px solid #eee;
  cursor: pointer;
  transition: all 0.2s ease;
  border-radius: 10px;
  margin-bottom: 6px;
  background-color: #fafafa;
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.session-top {
  display: flex;
  align-items: center;
  gap: 10px;
  position: relative;
}

.session-text {
  min-width: 0;
  flex: 1;
}

.session-actions {
  position: relative;
  margin-left: auto;
}

.unread-badge {
  min-width: 20px;
  height: 20px;
  padding: 0 6px;
  border-radius: 999px;
  background: #dc2626;
  color: #fff;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 11px;
  font-weight: 700;
  line-height: 1;
  margin-left: 6px;
}

.session-menu-btn {
  width: 28px;
  height: 28px;
  border-radius: 50%;
  border: 1px solid #d1d5db;
  background: #fff;
  color: #6b7280;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
}

.session-menu-dropdown {
  position: absolute;
  top: 34px;
  right: 0;
  z-index: 20;
  background: #fff;
  border: 1px solid #e5e7eb;
  border-radius: 10px;
  box-shadow: 0 8px 18px rgba(0, 0, 0, 0.12);
  min-width: 130px;
  padding: 6px;
}

.session-menu-item {
  width: 100%;
  text-align: left;
  border: none;
  background: transparent;
  padding: 8px 10px;
  border-radius: 8px;
  cursor: pointer;
  font-size: 13px;
}

.session-menu-item.danger {
  color: #dc2626;
}

.session-menu-item:hover:not(:disabled) {
  background: #fef2f2;
}

.delete-modal-backdrop {
  position: fixed;
  inset: 0;
  background: rgba(15, 23, 42, 0.45);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 3000;
}

.delete-modal-card {
  width: min(92vw, 420px);
  background: #fff;
  border-radius: 14px;
  padding: 16px;
  box-shadow: 0 16px 32px rgba(0, 0, 0, 0.2);
}

.delete-modal-actions {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
}

.btn-cancel,
.btn-delete {
  border: none;
  border-radius: 10px;
  padding: 8px 14px;
  font-weight: 600;
}

.btn-cancel {
  background: #e5e7eb;
  color: #111827;
}

.btn-delete {
  background: #dc2626;
  color: #fff;
}

.avatar-xs {
  width: 44px;
  height: 44px;
  border-radius: 50%;
  object-fit: cover;
  flex-shrink: 0;
}

.session-item:hover {
  background-color: #f0f8ff;
  transform: translateY(-2px);
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.session-item.active {
  background-color: #e3f2fd;
  border-left: 4px solid #2196f3;
  box-shadow: 0 2px 8px rgba(33,150,243,0.2);
}

.student-name {
  font-weight: bold;
  margin: 0;
  color: #111827;
  font-size: 15px;
  line-height: 1.2;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.lesson {
  display: none;
}

.last-message {
  margin: 2px 0 0 0;
  color: #6b7280;
  font-size: 14px;
  line-height: 1.2;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  max-width: 240px;
}

.timestamp {
  font-size: 12px;
  color: #999;
}

.session-meta {
  display: flex;
  justify-content: flex-end;
  width: 100%;
  margin-top: 0;
}

.chat-area {
  flex: 1;
  display: flex;
  flex-direction: column;
  padding: 0;
  background-color: #ffffff;
  min-height: 0;
  overflow: hidden;
}

.chat-content {
  display: flex;
  flex-direction: column;
  height: 100%;
  min-height: 0;
  border-radius: 0;
  box-shadow: none;
  overflow: hidden;
}

.chat-header {
  background-color: #2196f3;
  color: white;
  padding: 15px 20px;
  border-radius: 0;
}

.chat-header-user {
  display: flex;
  align-items: center;
  gap: 10px;
}

.chat-header-avatar {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  object-fit: cover;
  flex-shrink: 0;
}

.chat-header h3 {
  margin: 0;
  font-size: 18px;
  font-weight: 500;
}

.messages {
  flex: 1;
  overflow-y: auto;
  padding: 16px;
  background-color: #f5f6f8;
  min-height: 0;
  max-height: none;
}

.message {
  margin-bottom: 15px;
  display: flex;
  flex-direction: column;
  animation: fadeIn 0.3s ease-in;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

.message.teacher {
  align-items: flex-end;
}

.message.student {
  align-items: flex-start;
}

.message-content {
  width: fit-content;
  max-width: min(70%, 560px);
  min-width: 140px;
  padding: 12px 16px;
  border-radius: 18px;
  word-wrap: break-word;
  overflow-wrap: anywhere;
  word-break: break-word;
  white-space: pre-wrap;
  text-align: left;
  font-size: 14px;
  line-height: 1.4;
  position: relative;
}

.message-row {
  display: flex;
  align-items: flex-end;
  gap: 8px;
}

.message.teacher .message-row {
  justify-content: flex-end;
}

.message-avatar {
  width: 30px;
  height: 30px;
  border-radius: 50%;
  object-fit: cover;
  flex-shrink: 0;
}

.avatar-fallback {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  background: #e2e8f0;
  color: #64748b;
}

.message.teacher .message-content {
  background-color: #2196f3;
  color: white;
  border-bottom-right-radius: 4px;
}

.message.student .message-content {
  background-color: #e0e0e0;
  color: #333;
  border-bottom-left-radius: 4px;
}

.message-time {
  font-size: 11px;
  color: #999;
  margin-top: 5px;
  padding: 0 8px;
}

.message-status {
  color: #6b7280;
  font-size: 12px;
  margin-top: 6px;
  display: inline-flex;
  align-items: center;
  gap: 4px;
  padding: 0 8px;
}

.message-input {
  display: flex;
  gap: 10px;
  padding: 14px 16px;
  background-color: #ffffff;
  border-top: 1px solid #e0e0e0;
  border-radius: 0;
}

.message-input input {
  flex: 1;
  padding: 12px 16px;
  border: 2px solid #e0e0e0;
  border-radius: 25px;
  font-size: 14px;
  outline: none;
  transition: border-color 0.2s;
}

.message-input input:focus {
  border-color: #2196f3;
}

.message-input input:disabled {
  background-color: #f5f5f5;
  cursor: not-allowed;
}

.message-input button {
  padding: 12px 24px;
  background-color: #2196f3;
  color: white;
  border: none;
  border-radius: 25px;
  cursor: pointer;
  font-size: 14px;
  font-weight: 500;
  transition: background-color 0.2s;
}

.message-input button:hover:not(:disabled) {
  background-color: #1976d2;
}

.message-input button:disabled {
  background-color: #ccc;
  cursor: not-allowed;
}

.no-selection {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100%;
  color: #666;
  font-size: 18px;
  text-align: center;
}

/* Responsive */
@media (max-width: 768px) {
  .chat-teacher {
    flex-direction: column;
    height: calc(100vh - 70px);
    min-height: 0;
  }
  
  .sidebar {
    width: 100%;
    height: 200px;
    border-right: none;
    border-bottom: 1px solid #e0e0e0;
  }
  
  .chat-area {
    height: auto;
    padding: 0;
  }
  
  .messages {
    max-height: none;
  }

  .message-content {
    width: fit-content;
    min-width: 120px;
    max-width: 82%;
    padding: 10px 14px;
    font-size: 13px;
  }
}

@media (max-width: 480px) {
  .message-content {
    width: 86%;
    max-width: 86%;
  }
}
</style>