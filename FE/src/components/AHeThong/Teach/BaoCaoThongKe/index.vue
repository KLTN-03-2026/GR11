<template>
  <motion.div
    class="bao-cao-thong-ke"
    :initial="{ opacity: 0, y: 12 }"
    :animate="{ opacity: 1, y: 0 }"
    :transition="{ duration: 0.35 }"
  >
    <header class="page-header">
      <h1>Báo cáo thống kê lớp</h1>
      <p class="subtitle">Báo cáo Premium — dữ liệu từ snapshot AI</p>
    </header>

    <motion.div v-if="premiumRequired" class="alert alert-warning" :initial="{ opacity: 0 }" :animate="{ opacity: 1 }">
      <p>{{ errorMessage }}</p>
      <router-link to="/teacher/profile" class="btn-link">Nâng cấp Premium</router-link>
    </motion.div>

    <motion.div v-else-if="loading" class="loading">Đang tải báo cáo...</motion.div>

    <template v-else-if="report">
      <section class="summary-grid">
        <div class="card">
          <span class="label">Học viên</span>
          <strong>{{ classOverview.student_count ?? 0 }}</strong>
        </div>
        <motion.div class="card" :whileHover="{ scale: 1.02 }">
          <span class="label">Điểm TB 7 ngày</span>
          <strong>{{ classOverview.avg_score_7d ?? 0 }}/100</strong>
        </motion.div>
        <motion.div class="card" :whileHover="{ scale: 1.02 }">
          <span class="label">Tin chưa đọc</span>
          <strong>{{ classOverview.unread_messages ?? 0 }}</strong>
        </motion.div>
        <motion.div class="card" :whileHover="{ scale: 1.02 }">
          <span class="label">Lộ trình đã gán</span>
          <strong>{{ pathCount }}</strong>
        </motion.div>
      </section>

      <section v-if="weeklyScores.length" class="chart-section">
        <h2>Điểm trung bình theo tuần (lớp)</h2>
        <motion.div class="bar-chart" :initial="{ opacity: 0 }" :animate="{ opacity: 1 }">
          <motion.div
            v-for="(bar, idx) in weeklyScores"
            :key="bar.week"
            class="bar-row"
            :initial="{ width: 0 }"
            :animate="{ width: '100%' }"
            :transition="{ delay: idx * 0.05 }"
          >
            <span class="bar-label">{{ bar.week }}</span>
            <motion.div class="bar-track">
              <motion.div
                class="bar-fill"
                :initial="{ width: 0 }"
                :animate="{ width: barWidth(bar.avg_score) }"
                :transition="{ duration: 0.5, delay: idx * 0.08 }"
              />
            </motion.div>
            <span class="bar-value">{{ bar.avg_score }}</span>
          </motion.div>
        </motion.div>
      </section>

      <section v-if="unreadStudents.length" class="list-section">
        <h2>Học viên có tin chưa đọc</h2>
        <ul>
          <li v-for="s in unreadStudents" :key="s.hoc_vien_id">
            {{ s.student_name }} — {{ s.unread_count }} tin
          </li>
        </ul>
      </section>

      <div class="actions">
        <button type="button" class="btn primary" :disabled="loading" @click="loadReport(true)">
          Làm mới báo cáo
        </button>
        <button
          v-if="report.snapshot_id"
          type="button"
          class="btn secondary"
          @click="exportFile('csv')"
        >
          Tải CSV
        </button>
        <button
          v-if="report.snapshot_id"
          type="button"
          class="btn secondary"
          @click="exportFile('pdf')"
        >
          Tải PDF
        </button>
      </motion.div>
    </template>

    <motion.div v-else-if="errorMessage" class="alert alert-error">{{ errorMessage }}</motion.div>
  </motion.div>
</template>

<script>
import axios from "axios";
import { motion } from "motion-v";

export default {
  name: "BaoCaoThongKe",
  components: { motion },
  data() {
    return {
      apiBase: (import.meta.env.VITE_API_URL || "http://127.0.0.1:8000").replace(/\/$/, ""),
      loading: false,
      premiumRequired: false,
      errorMessage: "",
      report: null,
    };
  },
  computed: {
    payload() {
      return (this.report && this.report.payload) || {};
    },
    classOverview() {
      return this.payload.class_overview || {};
    },
    pathCount() {
      return ((this.payload.path_assignments || []) && this.payload.path_assignments.length) || 0;
    },
    unreadStudents() {
      return this.payload.students_with_unread || [];
    },
    weeklyScores() {
      const scores = this.payload.weekly_scores;
      if (Array.isArray(scores) && scores.length) return scores;
      const avg = Number(this.classOverview.avg_score_7d || 0);
      return [{ week: "7 ngày", avg_score: avg }];
    },
  },
  mounted() {
    this.loadReport(false);
  },
  methods: {
    authHeaders() {
      const token = localStorage.getItem("token_teacher") || localStorage.getItem("token");
      return token ? { Authorization: `Bearer ${token}` } : {};
    },
    barWidth(score) {
      const pct = Math.min(100, Math.max(0, (Number(score) / 100) * 100));
      return `${pct}%`;
    },
    async loadReport(regenerate) {
      this.loading = true;
      this.errorMessage = "";
      this.premiumRequired = false;
      try {
        const url = regenerate
          ? `${this.apiBase}/api/ai-reports/regenerate`
          : `${this.apiBase}/api/ai-reports/latest`;
        const res = regenerate
          ? await axios.post(url, {}, { headers: this.authHeaders() })
          : await axios.get(url, { headers: this.authHeaders() });
        if (res.data && res.data.status) {
          this.report = res.data.data;
        } else {
          this.errorMessage = (res.data && res.data.message) || "Không tải được báo cáo.";
        }
      } catch (err) {
        const status = err.response && err.response.status;
        const body = err.response && err.response.data;
        if (status === 403 && body && body.premium_required) {
          this.premiumRequired = true;
          this.errorMessage = body.message || "Cần gói Premium Giáo viên.";
        } else {
          this.errorMessage = (body && body.message) || "Không tải được báo cáo.";
        }
      } finally {
        this.loading = false;
      }
    },
    async exportFile(type) {
      if (!this.report || !this.report.snapshot_id) return;
      const path =
        type === "pdf"
          ? `/api/ai-reports/${this.report.snapshot_id}/export-pdf`
          : `/api/ai-reports/${this.report.snapshot_id}/export-csv`;
      try {
        const res = await axios.get(`${this.apiBase}${path}`, {
          headers: this.authHeaders(),
        });
        const downloadUrl = res.data && res.data.download_url;
        if (downloadUrl) {
          window.open(downloadUrl, "_blank");
        }
      } catch (err) {
        const body = err.response && err.response.data;
        this.errorMessage = (body && body.message) || "Không tải được file báo cáo.";
      }
    },
  },
};
</script>

<style scoped>
.bao-cao-thong-ke {
  padding: 1.5rem;
  max-width: 960px;
  margin: 0 auto;
}
.page-header h1 {
  margin: 0 0 0.25rem;
  font-size: 1.5rem;
}
.subtitle {
  color: #64748b;
  margin: 0 0 1.5rem;
}
.summary-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
  gap: 1rem;
  margin-bottom: 1.5rem;
}
.card {
  background: #fff;
  border-radius: 12px;
  padding: 1rem;
  box-shadow: 0 1px 3px rgba(15, 23, 42, 0.08);
}
.card .label {
  display: block;
  font-size: 0.85rem;
  color: #64748b;
}
.card strong {
  font-size: 1.4rem;
  color: #0f172a;
}
.chart-section,
.list-section {
  background: #fff;
  border-radius: 12px;
  padding: 1.25rem;
  margin-bottom: 1rem;
  box-shadow: 0 1px 3px rgba(15, 23, 42, 0.08);
}
.bar-row {
  display: grid;
  grid-template-columns: 100px 1fr 48px;
  align-items: center;
  gap: 0.5rem;
  margin-bottom: 0.5rem;
}
.bar-track {
  height: 10px;
  background: #e2e8f0;
  border-radius: 6px;
  overflow: hidden;
}
.bar-fill {
  height: 100%;
  background: linear-gradient(90deg, #3b82f6, #6366f1);
  border-radius: 6px;
}
.actions {
  display: flex;
  gap: 0.75rem;
  flex-wrap: wrap;
}
.btn {
  border: none;
  border-radius: 8px;
  padding: 0.6rem 1rem;
  cursor: pointer;
  font-weight: 600;
}
.btn.primary {
  background: #2563eb;
  color: #fff;
}
.btn.secondary {
  background: #e2e8f0;
  color: #0f172a;
}
.alert {
  padding: 1rem;
  border-radius: 8px;
}
.alert-warning {
  background: #fef3c7;
  color: #92400e;
}
.alert-error {
  background: #fee2e2;
  color: #991b1b;
}
.loading {
  color: #64748b;
}
.btn-link {
  color: #2563eb;
  font-weight: 600;
}
</style>
