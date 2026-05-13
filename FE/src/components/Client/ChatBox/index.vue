<template>
  <div :class="floating ? 'position-fixed bottom-0 end-0 m-4' : 'chatbox-page-shell'" :style="floating ? 'z-index: 9999;' : ''">

    <!-- CHAT LIST VIEW (Hình 1) -->
    <transition name="chat-window">
      <div v-if="isOpen && !selectedChat" :class="['card shadow-lg border-0 mb-3', floating ? 'chat-container chat-list' : 'chat-page-window chat-list']"
        :style="{ width: floating ? (listWidth + 'px') : '100%', height: floating ? (listHeight + 'px') : 'calc(100vh - 150px)', borderRadius: '1.25rem', overflow: 'hidden', backgroundColor: '#fff', transformOrigin: 'bottom right', cursor: floating && isResizingList ? 'nwse-resize' : 'default' }"
        @mouseup="stopResizeList" @mousemove="handleResizeList">

        <div class="card-header d-flex justify-content-between align-items-center py-3 border-0 chat-list-header">
          <h6 class="mb-0 fw-bold chat-title" style="flex: 1;">Đoạn chat</h6>
          <button
            class="btn btn-sm rounded-circle d-flex align-items-center justify-content-center chat-header-icon"
            style="width: 32px; height: 32px;"
          >
            <i class="fa-solid fa-ellipsis"></i>
          </button>
          <button
            v-if="isTeacherMode && floating"
            @click="goToFullChatPage"
            class="btn btn-sm rounded-circle d-flex align-items-center justify-content-center chat-header-icon"
            style="width: 32px; height: 32px; flex-shrink: 0;"
            title="Mở trang chat giáo viên"
          >
            <i class="fa-solid fa-up-right-from-square"></i>
          </button>
          <button v-if="floating" @click="toggleChat"
            class="btn btn-sm rounded-circle d-flex align-items-center justify-content-center chat-header-icon"
            style="width: 32px; height: 32px; flex-shrink: 0;">
            <i class="fa-solid fa-xmark"></i>
          </button>
        </div>

        <!-- Search Bar -->
        <div class="p-3 border-bottom">
          <div class="input-group">
            <span class="input-group-text bg-light border-0" style="width: 40px;">
              <i class="fa fa-search text-muted"></i>
            </span>
            <input type="text" v-model="searchQuery" class="form-control border-0 bg-light"
              placeholder="Tìm kiếm trên Messenger" style="border-radius: 50px;" />
          </div>
        </div>

        <!-- Tabs -->
        <div class="d-flex gap-2 px-3 py-3 border-bottom chat-tabs-wrap" style="overflow-x: auto;">
          <button @click="activeTab = 'all'" :class="{ 'active': activeTab === 'all' }"
            class="btn btn-sm rounded-pill chat-tab-btn" style="white-space: nowrap;">
            Tất cả
          </button>
          <button @click="activeTab = 'unread'" :class="{ 'active': activeTab === 'unread' }"
            class="btn btn-sm rounded-pill chat-tab-btn" style="white-space: nowrap;">
            Chưa đọc
          </button>
        </div>

        <!-- Chat List Items -->
        <div class="overflow-auto" style="height: calc(100% - 200px); scroll-behavior: smooth;">
          <div v-if="filteredChats.length === 0" class="text-center py-5 text-muted">
            <p>Không có cuộc trò chuyện</p>
          </div>

          <div v-for="chat in filteredChats" :key="chat.id" @click="selectChat(chat)"
            class="d-flex align-items-center p-3 border-bottom cursor-pointer chat-item-hover messenger-row"
            style="cursor: pointer; transition: background-color 0.2s;">
            <img v-if="chat.avatar" :src="chat.avatar" class="rounded-circle me-3 messenger-list-avatar"
              style="width: 50px; height: 50px; object-fit: cover;" />
            <div
              v-else
              class="rounded-circle me-3 d-flex align-items-center justify-content-center messenger-list-avatar"
              :class="{ 'brand-avatar-wrap': chat.type === 'ai' }"
              style="width: 50px; height: 50px; background-color: #f0f0f0;"
            >
              <i v-if="chat.type === 'ai'" :class="[branding.logo_icon, 'brand-avatar-glyph']"></i>
              <i v-else class="fa fa-user text-muted"></i>
            </div>

            <div class="flex-grow-1" style="min-width: 0;">
              <div class="d-flex justify-content-between align-items-start mb-1">
                <h6 class="mb-0 fw-bold messenger-row-name" style="max-width: 80%; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                  {{ chat.name }}
                </h6>
                <small v-if="chat.type === 'teacher'" class="text-muted flex-shrink-0 ms-2">{{ formatTime(chat.lastMessageTime) }}</small>
              </div>
              <p class="mb-0 text-muted small" style="max-width: 90%; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                {{ truncateText(chat.lastMessage, 56) }}
              </p>
            </div>

            <div v-if="chat.unread > 0" class="ms-2">
              <span class="badge bg-danger rounded-pill">{{ chat.unread }}</span>
            </div>
          </div>
        </div>

        <!-- Resize Handle for List -->
        <div v-if="floating" class="resize-handle" @mousedown="startResizeList" title="Kéo để phóng to/thu nhỏ"></div>
      </div>
    </transition>

    <!-- CHAT DETAIL VIEW (Hình 2) -->
    <transition name="chat-window">
      <div v-if="isOpen && selectedChat" :class="['card shadow-lg border-0 mb-3', floating ? 'chat-container chat-detail' : 'chat-page-window chat-detail']"
        :style="{ width: floating ? (chatWidth + 'px') : '100%', height: floating ? (chatHeight + 'px') : 'calc(100vh - 150px)', borderRadius: '1.25rem', overflow: 'hidden', backgroundColor: '#fff', transformOrigin: 'bottom right', cursor: floating && isResizing ? 'nwse-resize' : 'default' }"
        @mouseup="stopResize" @mousemove="handleResize">

        <div class="card-header d-flex justify-content-between align-items-center py-3 border-0 chat-detail-header"
          style="gap: 0.5rem;">
          <button @click="selectedChat = null"
            class="btn btn-sm rounded-circle d-flex align-items-center justify-content-center chat-header-icon"
            style="width: 30px; height: 30px; flex-shrink: 0;">
            <i class="fa fa-chevron-left"></i>
          </button>
          <div class="d-flex align-items-center flex-grow-1" style="min-width: 0;">
            <img v-if="selectedChat.avatar" :src="selectedChat.avatar" class="rounded-circle me-2"
              style="width: 40px; height: 40px; object-fit: cover;" alt="" />
            <div
              v-else
              class="rounded-circle me-2 d-flex align-items-center justify-content-center"
              :class="{ 'brand-avatar-wrap': selectedChat.type === 'ai' }"
              style="width: 40px; height: 40px; background-color: #e9ecef;"
            >
              <i v-if="selectedChat.type === 'ai'" :class="[branding.logo_icon, 'brand-avatar-glyph']"></i>
              <i v-else class="fa fa-user text-secondary"></i>
            </div>
            <div style="min-width: 0;">
              <h6 class="mb-0 fw-bold text-dark" style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                {{ selectedChat.name }}
              </h6>
              <small v-if="selectedChat.type === 'ai'" class="text-muted">Trợ lý học tập AI</small>
            </div>
          </div>
          <div class="d-flex align-items-center gap-2 flex-shrink-0">
            <button class="btn btn-sm rounded-circle d-flex align-items-center justify-content-center chat-header-icon"
              style="width: 30px; height: 30px;">
              <i class="fa fa-info-circle text-primary"></i>
            </button>
          </div>
        </div>

        <!-- Messages Body -->
        <div class="card-body overflow-auto d-flex flex-column gap-3 p-3" ref="chatBody"
          style="background-color: #fffaf9; scroll-behavior: smooth; flex: 1;">

          <div v-for="(msg, index) in messages" :key="index" class="d-flex message-pop"
            :class="msg.role === 'user' ? 'justify-content-end' : 'justify-content-start'">

            <div v-if="msg.role === 'teacher'" class="d-flex flex-column align-items-start">
              <div class="d-flex align-items-end gap-2">
              <img v-if="selectedChat?.avatar" :src="selectedChat.avatar" class="message-peer-avatar" alt="avatar giáo viên" />
              <div v-else class="message-peer-avatar message-peer-avatar-fallback">
                <i class="fa fa-user"></i>
              </div>
              <div class="px-3 py-2 shadow-sm text-dark peer-message-bubble"
                :class="{ 'clickable-message': !!msg.action_url }"
                @click="msg.action_url ? navigateToAction(msg.action_url) : null"
                style="font-size: 0.95rem; line-height: 1.4; border-radius: 18px 18px 18px 0; background-color: #ffffff; border: 1px solid #ffe0d9;">
                {{ msg.text }}
                <div v-if="msg.action_url" class="mt-1">
                  <a href="#" class="action-link-emphasis" @click.stop.prevent="navigateToAction(msg.action_url)">
                    Click vào đây
                  </a>
                </div>
              </div>
            </div>
              <small v-if="msg.time" class="message-time">{{ msg.time }}</small>
            </div>

            <div v-else-if="msg.role === 'ai'" class="d-flex flex-column align-items-start">
              <div class="px-3 py-2 shadow-sm text-dark peer-message-bubble"
                style="font-size: 0.95rem; line-height: 1.4; border-radius: 18px 18px 18px 0; background-color: #ffffff; border: 1px solid #ffe0d9;">
                {{ msg.text }}

                <a v-if="msg.action_url" href="#" @click.prevent="navigateToAction(msg.action_url)"
                  class="d-inline-block mt-2 px-2 py-1 bg-light text-primary rounded text-decoration-none fw-medium"
                  style="font-size: 0.85rem; border: 1px solid #d4e4ff;">
                  👉 {{ msg.action_label }}
                </a>
              </div>
              <small v-if="msg.time" class="message-time">{{ msg.time }}</small>
            </div>

            <div v-else class="d-flex flex-column align-items-end user-message-wrap">
              <div class="px-3 py-2 shadow-sm text-white user-message-bubble"
                :class="{ 'clickable-message': !!msg.action_url }"
                @click="msg.action_url ? navigateToAction(msg.action_url) : null"
                style="font-size: 0.95rem; line-height: 1.4; border-radius: 18px 18px 0 18px; background: linear-gradient(135deg, #ff8c6b, #fe5d37);">
                {{ msg.text }}
                <div v-if="msg.action_url" class="mt-1">
                  <a href="#" class="action-link-emphasis action-link-emphasis-on-primary" @click.stop.prevent="navigateToAction(msg.action_url)">
                    Click vào đây
                  </a>
                </div>
              </div>
              <small v-if="selectedChat?.type === 'teacher'" class="message-status mt-1">
                <i :class="getMessageStatusIcon(msg.status)"></i>
                {{ getMessageStatusText(msg.status) }}
              </small>
              <small v-if="msg.time" class="message-time message-time--self mt-1">{{ msg.time }}</small>
            </div>

          </div>

          <div v-if="isTyping && selectedChat?.type !== 'teacher'" class="d-flex justify-content-start typing-indicator">
            <div class="px-3 py-2 shadow-sm fst-italic text-muted small"
              style="border-radius: 18px 18px 18px 0; background-color: #ffffff; border: 1px solid #ffe0d9;">
              Đang gõ<span class="dots">...</span>
            </div>
          </div>
        </div>

        <!-- Message Input -->
        <div class="card-footer p-3 bg-white border-top">
          <div
            v-if="selectedChat?.type === 'ai' && !isTyping && showAiSuggestions"
            class="ai-suggest-wrap mb-3"
          >
            <small class="text-muted d-block mb-2 fw-semibold">Gợi ý nhanh cho bạn:</small>
            <div class="d-flex flex-wrap gap-2">
              <button
                v-for="(q, idx) in goiYCauHoiAi"
                :key="`${q}-${idx}`"
                type="button"
                class="btn btn-sm ai-suggest-chip"
                @click="hoiCauGoiY(q)"
              >
                {{ q }}
              </button>
            </div>
          </div>
          <div class="input-group align-items-center">

            <input ref="messageInputField" type="text" v-model="userInput" @keyup.enter="sendMessage()" :disabled="isTyping"
              class="form-control rounded-pill me-2 shadow-sm" placeholder="Hỏi..."
              style="border-color: #e0e0e0; padding: 10px 15px;" />

            <button @click="toggleRecording" :disabled="isTyping || !speechSupported"
              class="btn btn-outline-secondary rounded-circle d-flex align-items-center justify-content-center shadow-sm hover-scale"
              :class="{ 'recording-pulse text-danger border-danger': isRecording }" style="width: 48px; height: 48px;">
              <i class="fa-solid fa-microphone fa-lg ml-2"></i>
            </button>

            <button @click="sendMessage()" :disabled="!userInput.trim() || isTyping"
              class="btn rounded-circle d-flex align-items-center justify-content-center shadow-sm ms-2"
              style="width: 48px; height: 48px; background: #fe5d37; border: none; opacity: 1;">
              <i class="fa-solid fa-paper-plane text-white"></i>
            </button>
          </div>

          <transition name="fade">
            <small v-if="isRecording" class="text-danger d-block mt-2 text-center fw-medium">
              Đang nghe... nói xong hệ thống sẽ tự gửi
            </small>
          </transition>
        </div>

        <!-- Resize Handle -->
        <div v-if="floating" class="resize-handle" @mousedown="startResize" title="Kéo để phóng to/thu nhỏ"></div>
      </div>
    </transition>

    <!-- FAB Button -->
    <div v-if="floating" class="d-flex justify-content-end fab-wrap">
      <button @click="toggleChat"
        class="btn shadow-lg rounded-circle d-flex align-items-center justify-content-center fab-button hover-scale"
        style="width: 60px; height: 60px; background: #fe5d37; border: none;">

        <i v-if="isOpen" class="fa-solid fa-xmark text-white fs-2 ml-2"></i>

        <svg v-else xmlns="http://www.w3.org/2000/svg" width="34" height="34" viewBox="0 0 24 24" fill="none"
          stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <path
            d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z">
          </path>
          <circle cx="8" cy="12" r="1.5" fill="white" stroke="none"></circle>
          <circle cx="12" cy="12" r="1.5" fill="white" stroke="none"></circle>
          <circle cx="16" cy="12" r="1.5" fill="white" stroke="none"></circle>
        </svg>

      </button>
      <span v-if="totalTeacherUnread > 0" class="fab-unread-badge">
        {{ totalTeacherUnread > 99 ? '99+' : totalTeacherUnread }}
      </span>
    </div>

  </div>
</template>

<script>
import axios from 'axios';

const AI_CHAT_ID = 'echokids-ai';

export default {
  props: {
    mode: {
      type: String,
      default: 'student',
    },
    floating: {
      type: Boolean,
      default: true,
    },
  },
  data() {
    return {
      isOpen: this.floating ? false : true,
      selectedChat: null,
      searchQuery: '',
      activeTab: 'all',
      isRecording: false,
      isTyping: false,
      hasLoadedHistory: false,
      aiSessionId: null,
      userInput: '',
      messages: [],
      chatWidth: 380,
      chatHeight: 520,
      listWidth: 400,
      listHeight: 600,
      isResizing: false,
      isResizingList: false,
      startX: 0,
      startY: 0,
      startWidth: 0,
      startHeight: 0,
      teacherChats: [],
      teacherMessagePollInterval: null,
      teacherChatsPollInterval: null,
      subscribedSessionIds: new Set(),
      speechRecognition: null,
      speechSupported: false,
      activeLessonContext: null,
      activeLessonTeacherChat: null,
      goiYCauHoiAi: [
        'Gợi ý cho mình một bài học phù hợp hôm nay',
        'Mình nên luyện phát âm âm nào trước?',
        'Hãy đưa ra 3 mẹo luyện nói rõ ràng hơn',
        'Tóm tắt lộ trình luyện tập trong 7 ngày',
      ],
      showAiSuggestions: true,
      branding: {
        logo_icon: "fa fa-book-reader",
      },
    };
  },
  created() {
    this.messages = [{ role: 'ai', text: 'Xin chào! Mình là chatbox EchoKids. Mình có thể hỗ trợ học tập cho bạn.' }];
  },
  async mounted() {
    const savedWidth = localStorage.getItem('chat_width');
    const savedHeight = localStorage.getItem('chat_height');
    if (savedWidth) this.chatWidth = parseInt(savedWidth, 10);
    if (savedHeight) this.chatHeight = parseInt(savedHeight, 10);

    const savedListWidth = localStorage.getItem('list_width');
    const savedListHeight = localStorage.getItem('list_height');
    if (savedListWidth) this.listWidth = parseInt(savedListWidth, 10);
    if (savedListHeight) this.listHeight = parseInt(savedListHeight, 10);

    document.addEventListener('mouseup', this.stopResize);
    document.addEventListener('mousemove', this.handleResize);
    document.addEventListener('mouseup', this.stopResizeList);
    document.addEventListener('mousemove', this.handleResizeList);
    window.addEventListener('active-lesson-chat-updated', this.syncActiveLessonContext);

    this.syncActiveLessonContext();
    await this.loadTeacherChats();
    this.startTeacherChatsPolling();
    this.loadBranding();
    this.initSpeechRecognition();
  },
  beforeUnmount() {
    document.removeEventListener('mouseup', this.stopResize);
    document.removeEventListener('mousemove', this.handleResize);
    document.removeEventListener('mouseup', this.stopResizeList);
    document.removeEventListener('mousemove', this.handleResizeList);
    window.removeEventListener('active-lesson-chat-updated', this.syncActiveLessonContext);
    this.stopTeacherMessagePolling();
    this.stopTeacherChatsPolling();
    this.unsubscribeAllSessionChannels();
    this.stopSpeechRecognition();
  },
  watch: {
    isOpen(val) {
      if (val && this.selectedChat) {
        this.ensureChatHistoryLoaded();
        this.scrollToBottom();
      }
    },
    selectedChat(val) {
      this.stopSpeechRecognition();
      this.stopTeacherMessagePolling();
      if (val) {
        this.messages = [];
        this.userInput = '';
        this.isTyping = false;
        this.hasLoadedHistory = false;
        this.showAiSuggestions = true;
        this.ensureChatHistoryLoaded();
        if (val.type === 'teacher') {
          this.startTeacherMessagePolling();
        }
        this.focusMessageInput();
      }
    },
    $route() {
      this.syncActiveLessonContext();
    },
  },
  computed: {
    isTeacherMode() {
      return this.mode === 'teacher';
    },
    totalTeacherUnread() {
      return this.teacherChats.reduce((sum, chat) => sum + Number(chat?.unread || 0), 0);
    },
    aiChatItem() {
      return {
        id: AI_CHAT_ID,
        type: 'ai',
        name: 'EchoKids',
        avatar: null,
        lastMessage: 'Trợ lý học tập AI',
        lastMessageTime: new Date(),
        unread: 0,
        status: 'Trợ lý học tập',
      };
    },
    chatList() {
      if (this.isTeacherMode) {
        return this.teacherChats;
      }
      const list = [this.aiChatItem];
      if (this.activeLessonTeacherChat) {
        const hasRealChatSameTeacher = this.teacherChats.some(
          (c) => Number(c.teacherId) === Number(this.activeLessonTeacherChat.teacherId)
        );
        if (!hasRealChatSameTeacher) {
          list.push(this.activeLessonTeacherChat);
        }
      }
      return list.concat(this.teacherChats);
    },
    filteredChats() {
      let filtered = this.chatList;
      if (this.activeTab === 'unread') {
        filtered = filtered.filter((chat) => (chat.unread || 0) > 0);
      }
      if (this.searchQuery.trim()) {
        const query = this.searchQuery.toLowerCase();
        filtered = filtered.filter((chat) =>
          String(chat.name || '').toLowerCase().includes(query) ||
          String(chat.lastMessage || '').toLowerCase().includes(query)
        );
      }
      return filtered;
    },
  },
  methods: {
    formatTime(date) {
      if (!date) return '';
      if (!(date instanceof Date)) date = new Date(date);
      if (Number.isNaN(date.getTime())) return '';
      const diff = new Date() - date;
      const minutes = Math.floor(diff / 60000);
      const hours = Math.floor(minutes / 60);
      const days = Math.floor(hours / 24);
      if (minutes < 1) return 'Vừa xong';
      if (minutes < 60) return `${minutes}p`;
      if (hours < 24) return `${hours}h`;
      if (days === 1) return 'Hôm qua';
      if (days < 7) return `${days}d`;
      return date.toLocaleDateString('vi-VN');
    },
    formatMessageTime(value) {
      if (!value) return '';
      if (typeof value === 'string' && /^\d{1,2}:\d{2}$/.test(value.trim())) {
        return value.trim();
      }
      const date = value instanceof Date ? value : new Date(value);
      if (Number.isNaN(date.getTime())) return '';
      return date.toLocaleTimeString('vi-VN', {
        hour: '2-digit',
        minute: '2-digit',
      });
    },
    truncateText(text, max = 40) {
      const value = String(text || '').trim();
      return value.length > max ? `${value.slice(0, max)}...` : value;
    },
    getSessionStorageKey() {
      return this.isTeacherMode ? 'chat_session_id_teacher' : 'chat_session_id_student';
    },
    getAuthToken() {
      if (this.isTeacherMode) {
        return localStorage.getItem('token_teacher') || '';
      }
      return localStorage.getItem('token_nguoi_dung') || localStorage.getItem('token_khach_hang') || '';
    },
    getAuthHeader() {
      const token = this.getAuthToken();
      return token ? { Authorization: 'Bearer ' + token } : {};
    },
    syncActiveLessonContext() {
      if (this.isTeacherMode) {
        this.activeLessonContext = null;
        this.activeLessonTeacherChat = null;
        return;
      }
      try {
        const raw = localStorage.getItem('active_lesson_chat_context');
        this.activeLessonContext = raw ? JSON.parse(raw) : null;
      } catch (e) {
        this.activeLessonContext = null;
      }
      if (this.activeLessonContext?.teacher_id) {
        this.activeLessonTeacherChat = {
          id: `teacher-active-${this.activeLessonContext.teacher_id}`,
          type: 'teacher',
          isTemp: true,
          teacherId: Number(this.activeLessonContext.teacher_id),
          lessonId: Number(this.activeLessonContext.lesson_id || 0),
          lessonTitle: this.activeLessonContext.lesson_title || '',
          sessionId: null,
          name: this.activeLessonContext.teacher_name || 'Giáo viên',
          avatar: null,
          lastMessage: this.activeLessonContext.lesson_title ? `Bài: ${this.activeLessonContext.lesson_title}` : 'Nhắn tin với giáo viên',
          lastMessageTime: new Date(),
          unread: 0,
          status: 'Giáo viên bài học hiện tại',
        };
      } else {
        this.activeLessonTeacherChat = null;
      }
    },
    async loadBranding() {
      try {
        const res = await axios.get('http://127.0.0.1:8000/api/admin/cau-hinh/chung/data');
        if (res?.data?.status && res?.data?.data?.logo_icon) {
          this.branding.logo_icon = res.data.data.logo_icon;
        }
      } catch (e) {
        // keep default icon
      }
    },
    async loadTeacherChats() {
      const token = this.getAuthToken();
      if (!token) {
        this.teacherChats = [];
        return;
      }
      try {
        const endpoint = this.isTeacherMode
          ? 'http://127.0.0.1:8000/api/teacher/chat/sessions'
          : 'http://127.0.0.1:8000/api/student/chat/sessions';
        const res = await axios.get(endpoint, {
          headers: this.getAuthHeader(),
        });
        const rows = Array.isArray(res?.data) ? res.data : [];
        this.teacherChats = rows.map((row) => this.isTeacherMode
          ? {
              id: `session-${row.id}`,
              type: 'teacher',
              teacherId: null,
              sessionId: Number(row.id || 0),
              lessonId: 0,
              lessonTitle: row.lesson || '',
              name: row.studentName || 'Học viên',
              avatar: row.studentAvatar ? this.resolveAvatarUrl(row.studentAvatar) : null,
              lastMessage: row.lastMessage || '',
              lastMessageTime: row.timestamp ? new Date(row.timestamp) : null,
              unread: Number(row.unreadCount || 0),
              status: 'Đã có lịch sử chat',
            }
          : {
              id: `teacher-${row.teacher_id}`,
              type: 'teacher',
              teacherId: Number(row.teacher_id),
              sessionId: Number(row.session_id || 0),
              lessonId: Number(row.lesson_id || 0),
              lessonTitle: row.lesson_title || '',
              name: row.teacher_name || 'Giáo viên',
              avatar: row.teacher_avatar ? this.resolveAvatarUrl(row.teacher_avatar) : null,
              lastMessage: row.last_message || '',
              lastMessageTime: row.last_message_time ? new Date(row.last_message_time) : null,
              unread: Number(row.unread_count || 0),
              status: 'Đã có lịch sử chat',
            });
        this.teacherChats.forEach((chat) => {
          if (chat?.sessionId) {
            this.subscribeToTeacherSession(chat.sessionId);
          }
        });
      } catch (e) {
        console.error('Không tải được danh sách chat giáo viên', e);
      }
    },
    subscribeToTeacherSession(sessionId) {
      if (!window.Echo || !sessionId || this.subscribedSessionIds.has(sessionId)) {
        return;
      }
      const channelName = `chat-session.${sessionId}`;
      const listenEvent = this.isTeacherMode ? '.StudentSentMessage' : '.TeacherSentMessage';
      window.Echo.private(channelName).listen(listenEvent, (e) => {
        const incomingText = e?.message?.content || '';
        const incomingTime = e?.message?.created_at ? new Date(e.message.created_at) : new Date();
        const chat = this.teacherChats.find((c) => Number(c.sessionId) === Number(sessionId));
        if (!chat) {
          this.loadTeacherChats();
          return;
        }

        chat.lastMessage = incomingText || chat.lastMessage;
        chat.lastMessageTime = incomingTime;

        if (this.selectedChat?.type === 'teacher' && Number(this.selectedChat.sessionId) === Number(sessionId)) {
          this.loadTeacherChatHistory(this.selectedChat);
        } else {
          chat.unread = Number(chat.unread || 0) + 1;
          this.teacherChats = [chat, ...this.teacherChats.filter((c) => c.id !== chat.id)];
        }
      });
      this.subscribedSessionIds.add(sessionId);
    },
    unsubscribeAllSessionChannels() {
      if (!window.Echo) {
        return;
      }
      this.subscribedSessionIds.forEach((sessionId) => {
        window.Echo.leave(`private-chat-session.${sessionId}`);
      });
      this.subscribedSessionIds.clear();
    },
    resolveAvatarUrl(path) {
      if (!path) return null;
      if (String(path).startsWith('http://') || String(path).startsWith('https://')) return path;
      return `http://127.0.0.1:8000/storage/${String(path).replace(/^\/+/, '')}`;
    },
    selectChat(chat) {
      const merged = { ...chat };
      if (
        !this.isTeacherMode &&
        merged.type === 'teacher' &&
        this.activeLessonContext &&
        Number(this.activeLessonContext.teacher_id) === Number(merged.teacherId)
      ) {
        merged.lessonId = Number(this.activeLessonContext.lesson_id || merged.lessonId || 0);
        merged.lessonTitle = this.activeLessonContext.lesson_title || merged.lessonTitle || '';
      }
      merged.unread = 0;
      const target = this.teacherChats.find((c) => c.id === merged.id);
      if (target) {
        target.unread = 0;
      }
      this.selectedChat = merged;
    },
    async ensureAiSessionReady() {
      if (this.aiSessionId) return this.aiSessionId;
      const cached = Number(localStorage.getItem(this.getSessionStorageKey()) || 0);
      const payload = cached > 0 ? { session_id: cached } : {};
      const res = await axios.post('http://127.0.0.1:8000/api/chat/system/session', payload, {
        headers: this.getAuthHeader(),
      });
      const sessionId = Number(res?.data?.session_id || 0);
      if (sessionId > 0) {
        this.aiSessionId = sessionId;
        localStorage.setItem(this.getSessionStorageKey(), String(sessionId));
      }
      return this.aiSessionId;
    },
    async ensureTeacherSession(chat) {
      if (this.isTeacherMode) {
        return chat.sessionId || null;
      }
      if (chat.sessionId) return chat.sessionId;
      if (!chat.lessonId || chat.lessonId <= 0 || !chat.teacherId) {
        throw new Error('Thiếu lesson/teacher để mở chat giáo viên');
      }
      const res = await axios.post(
        'http://127.0.0.1:8000/api/student/chat/session',
        { lesson_id: chat.lessonId, teacher_id: chat.teacherId },
        { headers: this.getAuthHeader() }
      );
      const sid = Number(res?.data?.session_id || 0);
      chat.sessionId = sid > 0 ? sid : null;
      if (chat.sessionId) {
        this.subscribeToTeacherSession(chat.sessionId);
      }
      return chat.sessionId;
    },
    toggleChat() {
      this.isOpen = !this.isOpen;
      if (!this.isOpen) this.selectedChat = null;
    },
    goToFullChatPage() {
      if (this.isTeacherMode) {
        this.$router.push('/teacher/chat-box');
      } else {
        this.$router.push('/chat-box');
      }
    },
    initSpeechRecognition() {
      const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
      if (!SpeechRecognition) {
        this.speechSupported = false;
        return;
      }
      this.speechSupported = true;
      const recognition = new SpeechRecognition();
      recognition.lang = 'vi-VN';
      recognition.continuous = false;
      recognition.interimResults = true;
      let finalTranscript = '';

      recognition.onstart = () => {
        this.isRecording = true;
      };
      recognition.onresult = (event) => {
        let interimTranscript = '';
        for (let i = event.resultIndex; i < event.results.length; i += 1) {
          const transcript = String(event.results[i][0]?.transcript || '');
          if (event.results[i].isFinal) {
            finalTranscript += transcript + ' ';
          } else {
            interimTranscript += transcript;
          }
        }
        const merged = `${finalTranscript}${interimTranscript}`.trim();
        if (merged) {
          this.userInput = merged;
        }
      };
      recognition.onerror = () => {
        this.isRecording = false;
      };
      recognition.onend = async () => {
        this.isRecording = false;
        const text = finalTranscript.trim();
        finalTranscript = '';
        if (text) {
          this.userInput = text;
          await this.sendMessage('voice_text');
        } else {
          this.focusMessageInput();
        }
      };
      this.speechRecognition = recognition;
    },
    stopSpeechRecognition() {
      if (!this.speechRecognition) return;
      try {
        this.speechRecognition.stop();
      } catch (e) {
        // ignore stop errors
      }
      this.isRecording = false;
    },
    toggleRecording() {
      if (this.isTeacherMode) {
        return;
      }
      if (!this.speechSupported) {
        this.$toast?.error('Trình duyệt chưa hỗ trợ ghi âm giọng nói.');
        return;
      }
      if (!this.selectedChat) {
        this.$toast?.info('Bạn hãy chọn cuộc trò chuyện trước khi ghi âm.');
        return;
      }
      if (this.isTyping) return;
      if (this.isRecording) {
        this.stopSpeechRecognition();
        return;
      }
      try {
        this.speechRecognition.start();
      } catch (e) {
        this.$toast?.error('Không thể bắt đầu ghi âm, bạn thử lại nhé.');
      }
    },
    parseApiErrorMessage(err, fallbackText) {
      return err?.response?.data?.message || err?.response?.data?.error || fallbackText;
    },
    startResize(e) {
      this.isResizing = true;
      this.startX = e.clientX;
      this.startY = e.clientY;
      this.startWidth = this.chatWidth;
      this.startHeight = this.chatHeight;
      document.body.classList.add('is-resizing-global');
      e.preventDefault();
    },
    handleResize(e) {
      if (!this.isResizing) return;
      const deltaX = e.clientX - this.startX;
      const deltaY = e.clientY - this.startY;
      const minWidth = 300;
      const minHeight = 350;
      const maxWidth = window.innerWidth - 32;
      const maxHeight = window.innerHeight - 32;
      this.chatWidth = Math.max(minWidth, Math.min(this.startWidth - deltaX, maxWidth));
      this.chatHeight = Math.max(minHeight, Math.min(this.startHeight - deltaY, maxHeight));
    },
    stopResize() {
      if (!this.isResizing) return;
      this.isResizing = false;
      document.body.classList.remove('is-resizing-global');
      localStorage.setItem('chat_width', String(this.chatWidth));
      localStorage.setItem('chat_height', String(this.chatHeight));
    },
    startResizeList(e) {
      this.isResizingList = true;
      this.startX = e.clientX;
      this.startY = e.clientY;
      this.startWidth = this.listWidth;
      this.startHeight = this.listHeight;
      document.body.classList.add('is-resizing-global');
      e.preventDefault();
    },
    handleResizeList(e) {
      if (!this.isResizingList) return;
      const deltaX = e.clientX - this.startX;
      const deltaY = e.clientY - this.startY;
      const minWidth = 300;
      const minHeight = 350;
      const maxWidth = window.innerWidth - 32;
      const maxHeight = window.innerHeight - 32;
      this.listWidth = Math.max(minWidth, Math.min(this.startWidth - deltaX, maxWidth));
      this.listHeight = Math.max(minHeight, Math.min(this.startHeight - deltaY, maxHeight));
    },
    stopResizeList() {
      if (!this.isResizingList) return;
      this.isResizingList = false;
      document.body.classList.remove('is-resizing-global');
      localStorage.setItem('list_width', String(this.listWidth));
      localStorage.setItem('list_height', String(this.listHeight));
    },
    scrollToBottom() {
      this.$nextTick(() => {
        const chatBody = this.$refs.chatBody;
        if (chatBody) chatBody.scrollTop = chatBody.scrollHeight;
      });
    },
    focusMessageInput() {
      this.$nextTick(() => {
        const input = this.$refs.messageInputField;
        if (input && typeof input.focus === 'function') {
          input.focus();
        }
      });
    },
    navigateToAction(url) {
      if (!url) return;
      this.$router.push(url);
    },
    hoiCauGoiY(question) {
      if (!question || this.isTyping) return;
      this.userInput = question;
      this.sendMessage('suggestion');
    },
    async ensureChatHistoryLoaded() {
      if (this.hasLoadedHistory || !this.selectedChat) return;
      if (this.selectedChat.type === 'teacher') {
        await this.loadTeacherChatHistory(this.selectedChat);
      } else {
        await this.loadAiHistory();
      }
      this.hasLoadedHistory = true;
      this.scrollToBottom();
    },
    async loadAiHistory() {
      const token = this.getAuthToken();
      if (!token) {
        this.messages = [{ role: 'ai', text: 'Xin chào! Mình là chatbox EchoKids. Mình có thể hỗ trợ học tập cho bạn.' }];
        return;
      }
      try {
        const sessionId = await this.ensureAiSessionReady();
        if (!sessionId) return;
        const res = await axios.get('http://127.0.0.1:8000/api/chat/system/history', {
          params: { session_id: sessionId },
          headers: this.getAuthHeader(),
        });
        const list = Array.isArray(res?.data?.data) ? res.data.data : [];
        this.messages = list.length
          ? list.map((item) => ({
              role: item.role === 'user' ? 'user' : 'ai',
              text: item.text || '',
              action_url: item.action_url || null,
              action_label: item.action_label || null,
              time: this.formatMessageTime(item.created_at || item.time),
            }))
          : [{ role: 'ai', text: 'Xin chào! Mình là chatbox EchoKids. Mình có thể hỗ trợ học tập cho bạn.' }];
        
        // Hide suggestions if user already has a history of chatting
        if (list.length > 1) {
          this.showAiSuggestions = false;
        }
      } catch (err) {
        console.error('Lỗi tải lịch sử chat AI', err);
        this.messages = [{ role: 'ai', text: 'Xin chào! Mình là chatbox EchoKids. Mình có thể hỗ trợ học tập cho bạn.' }];
      }
    },
    async loadTeacherChatHistory(chat) {
      try {
        const sessionId = await this.ensureTeacherSession(chat);
        if (!sessionId) {
          this.messages = [{ role: 'teacher', text: 'Bạn có thể bắt đầu nhắn tin với giáo viên của bài học này.' }];
          return;
        }
        const endpoint = this.isTeacherMode
          ? `http://127.0.0.1:8000/api/teacher/chat/session/${sessionId}/messages`
          : `http://127.0.0.1:8000/api/student/chat/session/${sessionId}/messages`;
        const res = await axios.get(endpoint, {
          headers: this.getAuthHeader(),
        });
        const list = Array.isArray(res?.data?.messages) ? res.data.messages : [];
        this.messages = list.map((item) => ({
          id: item.id,
          role: this.isTeacherMode
            ? (item.sender === 'Giáo viên' ? 'user' : 'teacher')
            : (item.sender === 'Giáo viên' ? 'teacher' : 'user'),
          text: item.text || '',
          action_url: item.action_url || null,
          action_label: item.action_label || null,
          status: item.status || null,
          time: this.formatMessageTime(item.time || item.created_at),
        }));
        if (!this.messages.length) {
          this.messages = [{ role: 'teacher', text: 'Bạn có thể bắt đầu nhắn tin với giáo viên của bài học này.' }];
        }
        if (chat?.type === 'teacher') {
          chat.unread = 0;
          const target = this.teacherChats.find((c) => c.id === chat.id);
          if (target) {
            target.unread = 0;
          }
        }
      } catch (err) {
        console.error('Lỗi tải lịch sử chat giáo viên', err);
        this.messages = [{ role: 'teacher', text: 'Chưa tải được lịch sử chat giáo viên, bạn thử lại nhé.' }];
      }
    },
    async sendMessage(inputType = 'text') {
      const text = this.userInput.trim();
      if (!text || this.isTyping || !this.selectedChat) return;
      const token = this.getAuthToken();
      if (!token) {
        this.messages.push({ role: 'ai', text: 'Bạn cần đăng nhập để dùng chatbox nhé.' });
        this.$router.push('/dang-nhap');
        return;
      }

      this.messages.push({
        id: 'temp-' + Date.now(),
        role: 'user',
        text,
        status: 'sent',
        time: this.formatMessageTime(new Date()),
      });
      this.userInput = '';
      const isAiChat = this.selectedChat.type !== 'teacher';
      this.isTyping = isAiChat;
      if (isAiChat) {
        this.showAiSuggestions = false;
      }
      this.scrollToBottom();

      try {
        if (this.selectedChat.type === 'teacher') {
          const sessionId = await this.ensureTeacherSession(this.selectedChat);
          const sendEndpoint = this.isTeacherMode
            ? `http://127.0.0.1:8000/api/teacher/chat/session/${sessionId}/send`
            : `http://127.0.0.1:8000/api/student/chat/session/${sessionId}/send`;
          const res = await axios.post(
            sendEndpoint,
            { message: text },
            { headers: this.getAuthHeader() }
          );
          const data = res?.data || {};
          const newestMessage = this.messages[this.messages.length - 1];
          if (newestMessage && newestMessage.role === 'user') {
            newestMessage.id = data.id || newestMessage.id;
            newestMessage.status = data.status || 'sent';
            newestMessage.time = this.formatMessageTime(data.time || data.created_at || new Date());
          }
          const target = this.isTeacherMode
            ? this.teacherChats.find((c) => c.sessionId === this.selectedChat.sessionId)
            : this.teacherChats.find((c) => c.teacherId === this.selectedChat.teacherId);
          if (!target) {
            this.teacherChats.unshift({
              ...this.selectedChat,
              id: this.isTeacherMode ? `session-${this.selectedChat.sessionId}` : `teacher-${this.selectedChat.teacherId}`,
              isTemp: false,
            });
          }
          this.selectedChat.lastMessage = data.text || text;
          this.selectedChat.lastMessageTime = new Date();
          await Promise.all([this.loadTeacherChats(), this.loadTeacherChatHistory(this.selectedChat)]);
        } else {
          const sessionId = await this.ensureAiSessionReady();
          const res = await axios.post(
            'http://127.0.0.1:8000/api/chat/system',
            { session_id: sessionId, message: text, input_type: inputType },
            { headers: this.getAuthHeader() }
          );
          const data = res?.data || {};
          if (data.session_id) {
            this.aiSessionId = Number(data.session_id);
            localStorage.setItem(this.getSessionStorageKey(), String(this.aiSessionId));
          }
          this.messages.push({
            role: 'ai',
            text: data.message || 'EchoKids chưa nghe rõ, bạn thử nhập lại nhé.',
            action_url: data.action_url || null,
            action_label: data.action_label || null,
            time: this.formatMessageTime(data.created_at || data.time || new Date()),
          });
        }
      } catch (err) {
        console.error('Lỗi gửi tin nhắn', err);
        this.messages.push({
          role: this.selectedChat.type === 'teacher' ? 'teacher' : 'ai',
          text: this.parseApiErrorMessage(err, 'Hiện chưa gửi được tin nhắn, bạn thử lại sau nhé.'),
        });
      } finally {
        this.isTyping = false;
        this.scrollToBottom();
        this.focusMessageInput();
      }
    },
    startTeacherMessagePolling() {
      this.stopTeacherMessagePolling();
      this.teacherMessagePollInterval = setInterval(() => {
        if (this.selectedChat?.type === 'teacher') {
          this.loadTeacherChatHistory(this.selectedChat);
        }
      }, 5000);
    },
    startTeacherChatsPolling() {
      this.stopTeacherChatsPolling();
      this.teacherChatsPollInterval = setInterval(() => {
        this.loadTeacherChats();
      }, 10000);
    },
    stopTeacherMessagePolling() {
      if (this.teacherMessagePollInterval) {
        clearInterval(this.teacherMessagePollInterval);
        this.teacherMessagePollInterval = null;
      }
    },
    stopTeacherChatsPolling() {
      if (this.teacherChatsPollInterval) {
        clearInterval(this.teacherChatsPollInterval);
        this.teacherChatsPollInterval = null;
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
  },
};
</script>

<style scoped>
/* Thêm class này vào file CSS của bạn */
.circled-icon-container {
  display: inline-flex;
  /* Cho phép đặt kích thước và căn giữa */
  align-items: center;
  /* Căn giữa theo chiều dọc */
  justify-content: center;
  /* Căn giữa theo chiều ngang */
  width: 40px;
  /* Đặt chiều rộng cố định */
  height: 40px;
  /* Đặt chiều cao bằng chiều rộng để tạo hình vuông */
  border: 2px solid white;
  /* Viền màu trắng */
  border-radius: 50%;
  /* Viền tròn hoàn hảo trên hình vuông */
  position: relative;
  top: -2px;
  /* Giữ positioning ban đầu của bạn */
}

body.is-resizing-global {
  user-select: none !important;
  -webkit-user-select: none !important;
  cursor: nwse-resize !important;
}

.hover-scale {
  transition: transform 0.2s, box-shadow 0.2s;
}

.hover-scale:hover {
  transform: scale(1.1);
  box-shadow: 0 4px 15px rgba(254, 93, 55, 0.3);
}

.recording-pulse {
  animation: pulse-red 1.5s infinite;
}

@keyframes pulse-red {
  0% {
    box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7);
  }

  70% {
    box-shadow: 0 0 0 10px rgba(220, 53, 69, 0);
  }

  100% {
    box-shadow: 0 0 0 0 rgba(220, 53, 69, 0);
  }
}

.message-pop {
  animation: popIn 0.4s forwards;
}

@keyframes popIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }

  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.typing-indicator {
  animation: breathe 2s infinite;
}

@keyframes breathe {

  0%,
  100% {
    opacity: 0.6;
  }

  50% {
    opacity: 1;
  }
}

.fab-button {
  animation: bounceIn 0.5s;
}

.fab-wrap {
  position: relative;
}

.fab-unread-badge {
  position: absolute;
  top: -6px;
  right: -6px;
  min-width: 22px;
  height: 22px;
  border-radius: 999px;
  background: #dc2626;
  color: #fff;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 0 6px;
  font-size: 11px;
  font-weight: 700;
  line-height: 1;
  border: 2px solid #fff;
}

@keyframes bounceIn {
  0% {
    transform: scale(0);
  }

  100% {
    transform: scale(1);
  }
}

.chat-window-enter-active {
  transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.chat-window-leave-active {
  transition: all 0.3s;
}

.chat-window-enter-from,
.chat-window-leave-to {
  opacity: 0;
  transform: scale(0.5) translateY(50px);
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

.chat-container {
  position: fixed;
  bottom: 85px;
  right: 20px;
  z-index: 999;
}

.chatbox-page-shell {
  width: 100%;
  max-width: 1220px;
  margin: 0 auto;
  padding: 16px;
}

.chat-page-window {
  position: relative;
}

.chat-list {
  display: flex;
  flex-direction: column;
}

.chat-detail {
  display: flex;
  flex-direction: column;
}

.chat-item-hover {
  cursor: pointer;
  transition: background-color 0.2s ease;
}

.chat-item-hover:hover {
  background-color: #fff5f2;
}

.chat-list-header,
.chat-detail-header {
  background: linear-gradient(135deg, #fffaf8 0%, #fff2ee 100%);
  border-bottom: 1px solid #ffd8cc !important;
}

.chat-title {
  color: #111827;
  font-size: 1.35rem;
}

.brand-avatar-wrap {
  padding: 0 !important;
  overflow: hidden;
  border: 2px solid #fff3ef;
  background: linear-gradient(135deg, #ff6b35, #ff8c42) !important;
  box-shadow: 0 6px 14px rgba(255, 107, 53, 0.24);
  display: inline-flex !important;
  align-items: center;
  justify-content: center;
}

.brand-avatar-glyph {
  color: #fff;
  font-size: 1rem;
  line-height: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0;
  width: 100%;
  height: 100%;
}

.chat-header-icon {
  background-color: #f1f3f5;
  color: #374151;
  border: none;
}

.chat-tabs-wrap {
  background: #ffffff;
}

.chat-tab-btn {
  background: transparent;
  border: 1px solid transparent;
  color: #111827;
  font-weight: 600;
}

.chat-tab-btn.active {
  background-color: #e7f3ff;
  color: #0b5ed7;
}

.messenger-row {
  background: #ffffff;
  min-width: 0;
}

.messenger-list-avatar {
  flex-shrink: 0;
}

.messenger-row-name {
  color: #101828;
}

.message-status {
  color: #6c757d;
  font-size: 0.75rem;
  display: inline-flex;
  align-items: center;
  gap: 0.3rem;
}

.message-time {
  color: #94a3b8;
  font-size: 0.72rem;
  margin-top: 4px;
  padding-left: 36px;
}

.message-time--self {
  padding-left: 0;
}

.message-peer-avatar {
  width: 28px;
  height: 28px;
  border-radius: 50%;
  object-fit: cover;
  flex-shrink: 0;
}

.message-peer-avatar-fallback {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  background: #e5e7eb;
  color: #6b7280;
}

.peer-message-bubble {
  max-width: 82%;
  min-width: 120px;
  white-space: pre-wrap;
  word-break: break-word;
  border-color: #ffd9cf !important;
  background: #fffefe !important;
}

.user-message-wrap {
  max-width: 82%;
}

.user-message-bubble {
  min-width: 160px;
  max-width: 100%;
  white-space: pre-wrap;
  word-break: break-word;
  background: linear-gradient(135deg, #ff8a65, #ff5d37) !important;
}
.clickable-message {
  cursor: pointer;
  transition: transform 0.15s ease, box-shadow 0.15s ease, border-color 0.15s ease;
}
.clickable-message:hover {
  transform: translateY(-1px);
  border-color: #f59e0b !important;
  box-shadow: 0 6px 18px rgba(245, 158, 11, 0.2) !important;
}
.action-link-emphasis {
  color: #dc2626;
  font-weight: 700;
  text-decoration: underline;
}
.action-link-emphasis:hover {
  color: #b91c1c;
}
.action-link-emphasis-on-primary {
  color: #fee2e2;
}
.action-link-emphasis-on-primary:hover {
  color: #fecaca;
}

.ai-suggest-wrap {
  border: 1px solid #ffe1d7;
  border-radius: 12px;
  padding: 10px;
  background: linear-gradient(180deg, #fffaf8, #fff);
}

.ai-suggest-chip {
  border-radius: 999px;
  border: 1px solid #ffd1c3;
  background: #fff;
  color: #c2410c;
  font-weight: 600;
  font-size: 0.78rem;
}

.ai-suggest-chip:hover {
  background: #ffefe9;
  border-color: #ffb9a5;
}

.resize-handle {
  position: absolute;
  top: 0;
  left: 0;
  width: 24px;
  height: 24px;
  cursor: nwse-resize;
  background: linear-gradient(135deg, #fe5d37 50%, transparent 50%);
  opacity: 0.6;
  transition: opacity 0.2s;
  border-radius: 1.25rem 0 0 0;
  z-index: 10;
}

.resize-handle:hover {
  opacity: 1;
}
</style>