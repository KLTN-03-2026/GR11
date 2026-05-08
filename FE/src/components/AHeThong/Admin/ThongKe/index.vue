<template>
  <div class="container-fluid py-4" style="background-color: #f8f9fa; min-height: 100vh;">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h4 class="fw-bold mb-1" style="color: #2b3445;">Báo Cáo Hệ Thống Chi Tiết</h4>
        <p class="text-muted mb-0" style="font-size: 0.9rem;">
          Phân tích hiệu suất API, sức khỏe hệ thống, incidents và quản lý user
        </p>
      </div>

      <div class="d-flex gap-2">
        <button class="btn btn-outline-secondary rounded-3 px-3 shadow-sm bg-white" @click="quayLai">
          <i class="fa-solid fa-arrow-left me-1"></i> Quay lại
        </button>
        <button class="btn btn-success rounded-3 px-4 shadow-sm" @click="xuatFileExcel" :disabled="isExporting">
          <i class="fa-solid fa-file-excel me-1" v-if="!isExporting"></i>
          <span class="spinner-border spinner-border-sm me-1" v-else></span>
          {{ isExporting ? 'Đang xuất...' : 'Xuất Excel' }}
        </button>
      </div>
    </div>

    <div class="card border-0 shadow-sm rounded-3 mb-4">
      <div class="card-body p-3">
        <h6 class="fw-bold mb-3 text-dark"><i class="fa-solid fa-filter me-2 text-primary"></i>Bộ Lọc Báo Cáo</h6>
        <div class="row g-3 align-items-end">
          
          <div class="col-12 col-md-3">
            <label class="form-label small text-muted fw-semibold mb-1">Từ ngày</label>
            <input type="date" class="form-control shadow-none" v-model="bo_loc.startDate">
          </div>
          
          <div class="col-12 col-md-3">
            <label class="form-label small text-muted fw-semibold mb-1">Đến ngày</label>
            <input type="date" class="form-control shadow-none" v-model="bo_loc.endDate">
          </div>
          
          <div class="col-12 col-md-3">
            <label class="form-label small text-muted fw-semibold mb-1">Service/Component</label>
            <select class="form-select shadow-none" v-model="bo_loc.service" style="cursor: pointer;">
              <option value="">-- Tất Cả Services --</option>
              <option value="API">API Server</option>
              <option value="Database">Database</option>
              <option value="Cache">Cache/Redis</option>
              <option value="Auth">Authentication</option>
              <option value="Queue">Message Queue</option>
              <option value="Storage">Storage</option>
            </select>
          </div>
          
          <div class="col-12 col-md-3 d-flex gap-2">
            <button class="btn btn-primary flex-grow-1 shadow-sm" @click="apDungBoLoc" :disabled="isLoading">
              <i class="fa-solid fa-magnifying-glass me-1" v-if="!isLoading"></i>
              <span class="spinner-border spinner-border-sm me-1" v-else></span>
              {{ isLoading ? 'Đang lọc...' : 'Phân Tích' }}
            </button>
            <button class="btn btn-light border" title="Đặt lại" @click="datLaiBoLoc" :disabled="isLoading">
              <i class="fa-solid fa-rotate-right"></i>
            </button>
          </div>

        </div>
      </div>
    </div>

    <div class="row g-3 mb-4">
      <div class="col-lg-3 col-md-6">
        <div class="card border-0 shadow-sm rounded-3 h-100" style="border-left: 4px solid #0d6efd !important;">
          <div class="card-body p-3">
            <small class="text-muted fw-semibold">Total API Requests</small>
            <h3 class="fw-bold text-dark mt-1 mb-0">{{ tong_quan_bao_cao.totalRequests.toLocaleString() }} <small class="text-muted fs-6">requests</small></h3>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6">
        <div class="card border-0 shadow-sm rounded-3 h-100" style="border-left: 4px solid #198754 !important;">
          <div class="card-body p-3">
            <small class="text-muted fw-semibold">API Success Rate</small>
            <h3 class="fw-bold text-success mt-1 mb-0">{{ tong_quan_bao_cao.successRate }}%</h3>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6">
        <div class="card border-0 shadow-sm rounded-3 h-100" style="border-left: 4px solid #ffc107 !important;">
          <div class="card-body p-3">
            <small class="text-muted fw-semibold">Errors (24h)</small>
            <h3 class="fw-bold text-warning mt-1 mb-0">{{ tong_quan_bao_cao.errorCount }} <small class="text-muted fs-6">errors</small></h3>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6">
        <div class="card border-0 shadow-sm rounded-3 h-100" style="border-left: 4px solid #6f42c1 !important;">
          <div class="card-body p-3">
            <small class="text-muted fw-semibold">Avg Response Time</small>
            <h3 class="fw-bold mt-1 mb-0" style="color: #6f42c1;">{{ tong_quan_bao_cao.avgResponseTime }} <small class="text-muted fs-6">ms</small></h3>
          </div>
        </div>
      </div>
    </div>

    <div class="card border-0 shadow-sm rounded-3 mb-4">
      <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-server text-primary me-2"></i>Service Health Report</h6>
          <span class="badge bg-light text-dark border"><i class="fa-regular fa-calendar me-1"></i> Theo thời gian lọc</span>
        </div>
        
        <div class="table-responsive">
          <table class="table table-hover align-middle border text-center mb-0">
            <thead class="table-light text-muted small">
              <tr>
                <th class="text-start ps-3 py-3">Service Name</th>
                <th class="py-3">Status</th>
                <th class="py-3">Uptime</th>
                <th class="py-3">Requests/Day</th>
                <th class="py-3">Avg Response (ms)</th>
                <th class="py-3">Error Rate</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="service in service_health" :key="service.id" style="cursor: pointer;">
                <td class="text-start ps-3 fw-semibold text-dark">{{ service.name }}</td>
                <td>
                  <span class="badge" :class="service.status === 'online' ? 'bg-success' : 'bg-danger'">
                    {{ service.status === 'online' ? '✅ Online' : '❌ Offline' }}
                  </span>
                </td>
                <td class="fw-bold">{{ service.uptime }}%</td>
                <td>{{ service.requestsPerDay.toLocaleString() }}</td>
                <td><strong :class="service.avgResponse < 200 ? 'text-success' : service.avgResponse < 500 ? 'text-warning' : 'text-danger'">{{ service.avgResponse }}ms</strong></td>
                <td><span class="badge" :class="service.errorRate < 1 ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning'">{{ service.errorRate }}%</span></td>
              </tr>
              <tr v-if="service_health.length === 0">
                <td colspan="6" class="py-4 text-muted">Không có dữ liệu trong khoảng thời gian này.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="card border-0 shadow-sm rounded-3 mb-4">
      <div class="card-body p-4">
        <h6 class="fw-bold mb-4 text-dark"><i class="fa-solid fa-triangle-exclamation text-danger me-2"></i>Incidents & Errors Report</h6>
        
        <div class="row g-4">
          <div class="col-lg-5 border-end-lg pe-lg-4">
            <div v-for="(incident, index) in incident_summary" :key="index" class="mb-4">
              <div class="d-flex justify-content-between mb-1">
                <span class="fw-semibold text-dark">{{ incident.type }}</span>
                <span class="fw-bold" :class="incident.severity === 'critical' ? 'text-danger' : incident.severity === 'warning' ? 'text-warning' : 'text-info'">{{ incident.count }} events</span>
              </div>
              <div class="progress" style="height: 8px;">
                <div class="progress-bar" :style="{ width: incident.percent + '%', backgroundColor: incident.severity === 'critical' ? '#ef4444' : incident.severity === 'warning' ? '#f59e0b' : '#3b82f6' }"></div>
              </div>
              <small class="text-muted d-block mt-1">{{ incident.description }}</small>
            </div>
          </div>

          <div class="col-lg-7 ps-lg-4">
            <small class="fw-bold text-muted text-uppercase mb-2 d-block">Top Errors (24h)</small>
            <div class="table-responsive">
              <table class="table table-sm table-hover table-bordered align-middle text-center mb-0">
                <thead class="table-light text-muted">
                  <tr>
                    <th class="text-start ps-2 py-2">Error Code</th>
                    <th class="py-2">Service</th>
                    <th class="py-2">Count</th>
                    <th class="py-2">Last Occurrence</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="error in top_errors" :key="error.id">
                    <td class="text-start ps-2 fw-semibold text-dark">{{ error.code }}</td>
                    <td>{{ error.service }}</td>
                    <td><span class="badge bg-danger">{{ error.count }}</span></td>
                    <td><small class="text-muted">{{ error.lastOccurrence }}</small></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
// import { useRouter } from 'vue-router'; // Bỏ comment nếu dùng Vue Router
// import axios from 'axios';

// const router = useRouter();

// Trạng thái loading
const isLoading = ref(false);
const isExporting = ref(false);

// 1. Dữ liệu bộ lọc (Filters)
const bo_loc = ref({
  startDate: '',
  endDate: '',
  service: ''
});

// 2. Thống kê tổng quan
const tong_quan_bao_cao = ref({
  totalRequests: 2543891,
  successRate: 99.2,
  errorCount: 45,
  avgResponseTime: 145
});

// 3. Service Health Status
const service_health = ref([
  { id: 1, name: 'API Server', status: 'online', uptime: 99.8, requestsPerDay: 245000, avgResponse: 145, errorRate: 0.2 },
  { id: 2, name: 'Database (MySQL)', status: 'online', uptime: 99.9, requestsPerDay: 180000, avgResponse: 230, errorRate: 0.1 },
  { id: 3, name: 'Redis Cache', status: 'online', uptime: 99.7, requestsPerDay: 520000, avgResponse: 5, errorRate: 0.3 },
  { id: 4, name: 'Authentication', status: 'online', uptime: 99.95, requestsPerDay: 85000, avgResponse: 180, errorRate: 0.05 },
  { id: 5, name: 'Message Queue', status: 'online', uptime: 99.6, requestsPerDay: 150000, avgResponse: 250, errorRate: 0.4 }
]);

// 4. Incident Summary
const incident_summary = ref([
  { type: 'Database Errors (500)', severity: 'critical', count: 23, percent: 45, description: 'Query timeout và connection errors' },
  { type: 'API Rate Limit (429)', severity: 'warning', count: 18, percent: 35, description: 'Vượt quota giới hạn' },
  { type: 'Service Warnings', severity: 'info', count: 10, percent: 20, description: 'Response time cao, cảnh báo hệ thống' }
]);

// 5. Top Errors (24 hours)
const top_errors = ref([
  { id: 1, code: '500', service: 'API Server', count: 23, lastOccurrence: '5 phút trước' },
  { id: 2, code: '429', service: 'API Server', count: 18, lastOccurrence: '15 phút trước' },
  { id: 3, code: '503', service: 'Database', count: 12, lastOccurrence: '1 giờ trước' },
  { id: 4, code: '502', service: 'Gateway', count: 8, lastOccurrence: '2 giờ trước' },
  { id: 5, code: '504', service: 'Cache', count: 5, lastOccurrence: '3 giờ trước' }
]);

// Lifecycle hook
onMounted(() => {
  const today = new Date();
  const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
  
  // Format YYYY-MM-DD
  bo_loc.value.startDate = firstDay.toISOString().split('T')[0];
  bo_loc.value.endDate = today.toISOString().split('T')[0];
});

// Methods
const apDungBoLoc = async () => {
  isLoading.value = true;
  
  // Giả lập delay gọi API 1 giây
  setTimeout(() => {
    console.log('Đang gọi API với query:', bo_loc.value);
    
    // Giả lập làm mới data
    tong_quan_bao_cao.value.totalRequests = Math.floor(Math.random() * (3000000 - 2000000) + 2000000);
    tong_quan_bao_cao.value.errorCount = Math.floor(Math.random() * 100);
    
    isLoading.value = false;
  }, 1000);
};

const datLaiBoLoc = () => {
  bo_loc.value.service = '';
  apDungBoLoc();
};

const xuatFileExcel = () => {
  if (!bo_loc.value.startDate || !bo_loc.value.endDate) {
    alert("Vui lòng chọn khoảng thời gian hợp lệ.");
    return;
  }
  
  isExporting.value = true;
  
  // Giả lập quá trình tải file
  setTimeout(() => {
    isExporting.value = false;
    console.log(`Đã xuất file: System_Report_${bo_loc.value.startDate}_den_${bo_loc.value.endDate}.xlsx`);
  }, 1500);
};

const quayLai = () => {
  // router.push('/admin/dashboard');
  console.log('Quay lại trang trước');
};
</script>

<style scoped>
/* Chỉnh nhẹ lại viền cho cột responsive */
@media (min-width: 992px) {
  .border-end-lg {
    border-right: 1px solid #dee2e6;
  }
}
</style>