<template>
  <div class="container-fluid admin-dashboard">

    <div class="d-flex justify-content-between align-items-center mb-4 dashboard-header">
      <div>
        <h4 class="fw-bold mb-1 dashboard-title">Tổng quan Admin</h4>
        <p class="dashboard-subtitle mb-0">
          <template v-if="ho_ten_admin_hien_thi">
            Chào mừng quản trị viên
            <span class="fw-bold text-danger">{{ ho_ten_admin_hien_thi }}</span>
            đã quay trở lại!
          </template>
          <template v-else>
            Chào mừng bạn đã quay trở lại!
          </template>
        </p>
      </div>
      <div class="d-flex align-items-center gap-2">
        <button class="btn btn-sm btn-outline-danger btn-dashboard-action" @click="lamTuoiDuLieu">
          <i class="fa-solid fa-arrows-rotate me-1"></i>Làm tươi
        </button>
        <select class="form-select shadow-sm fw-semibold input-rounded" v-model="bo_loc_thoi_gian" @change="capNhatToanBoDuLieu" style="cursor: pointer; width: 180px;">
          <option value="tuan">Theo Tuần</option>
          <option value="thang">Theo Tháng</option>
          <option value="nam">Theo Năm</option>
        </select>
      </div>
    </div>

    

    <div v-show="activeTab === 'monitoring'">
      <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
          <div class="card dashboard-card dashboard-card--success border-0 shadow-sm rounded-3 h-100" :style="{ borderLeft: '5px solid ' + (apiHealth.status === 'healthy' ? '#10b981' : '#ef4444') }">
            <div class="card-body card-body-highlight p-3">
              <div class="d-flex justify-content-between align-items-start">
                <div>
                  <small class="text-muted fw-semibold">Trạng Thái API</small>
                  <h3 class="mb-0 fw-bold mt-1" :style="{ color: apiHealth.status === 'healthy' ? '#10b981' : '#ef4444' }">
                    {{ apiHealth.status === 'healthy' ? 'HOẠT ĐỘNG' : 'LỖI' }}
                  </h3>
                  <small :class="apiHealth.status === 'healthy' ? 'text-success fw-bold' : 'text-danger fw-bold'">
                    <i :class="apiHealth.status === 'healthy' ? 'fa-solid fa-check-circle me-1' : 'fa-solid fa-exclamation-circle me-1'"></i>
                    {{ apiHealth.uptime }}% uptime
                  </small>
                </div>
                <div class="p-2 rounded-3" :style="{ backgroundColor: apiHealth.status === 'healthy' ? '#d1fae5' : '#fee2e2' }">
                  <i :class="apiHealth.status === 'healthy' ? 'fa-solid fa-server text-success' : 'fa-solid fa-server text-danger'" style="font-size: 1.25rem;"></i>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-xl-3 col-md-6">
          <div class="card dashboard-card dashboard-card--info border-0 shadow-sm rounded-3 h-100" :style="{ borderLeft: '5px solid ' + (systemMetrics.errorCount > 5 ? '#ef4444' : '#3b82f6') }">
            <div class="card-body card-body-highlight p-3">
              <div class="d-flex justify-content-between align-items-start">
                <div>
                  <small class="text-muted fw-semibold ">Lỗi Hệ Thống (24h)</small>
                  <h3 class="mb-0 fw-bold mt-1 text-dark">{{ systemMetrics.errorCount }}</h3>
                  <small :class="systemMetrics.errorCount > 5 ? 'text-danger fw-bold' : 'text-muted'">
                    <i :class="systemMetrics.errorCount > 5 ? 'fa-solid fa-arrow-trend-up me-1' : 'fa-solid fa-arrow-trend-down me-1'"></i>
                    {{ systemMetrics.errorTrend }}
                  </small>
                </div>
                <div class="p-2 rounded-3" :style="{ backgroundColor: systemMetrics.errorCount > 5 ? '#fee2e2' : '#f3f4f6' }">
                  <i class="fa-solid fa-triangle-exclamation" :style="{ color: systemMetrics.errorCount > 5 ? '#ef4444' : '#9ca3af', fontSize: '1.25rem' }"></i>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-xl-3 col-md-6">
          <div class="card dashboard-card dashboard-card--blue border-0 shadow-sm rounded-3 h-100" :style="{ borderLeft: '5px solid ' + (systemMetrics.dbStatus === 'connected' ? '#3b82f6' : '#ef4444') }">
            <div class="card-body card-body-highlight p-3">
              <div class="d-flex justify-content-between align-items-start">
                <div>
                  <small class="text-muted fw-semibold">Người dùng</small>
                  <h3 class="mb-0 fw-bold mt-1" :style="{ color: systemMetrics.dbStatus === 'connected' ? '#3b82f6' : '#ef4444' }">
                    {{ systemMetrics.dbStatus === 'connected' ? 'Hoạt động' : 'NGẮT' }}
                  </h3>
                  <small class="text-muted fw-bold">
                   <div class="text-danger fw-bold"> <i class="fa-solid fa-database me-1"></i>{{ systemMetrics.dbConnections }} Hoạt động</div> 
                  </small>
                </div>
                <div class="p-2 rounded-3" :style="{ backgroundColor: systemMetrics.dbStatus === 'connected' ? '#dbeafe' : '#fee2e2' }">
                  <i class="fa-solid fa-database" :style="{ color: systemMetrics.dbStatus === 'connected' ? '#3b82f6' : '#ef4444', fontSize: '1.25rem' }"></i>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-xl-3 col-md-6">
          <div class="card dashboard-card dashboard-card--warning border-0 shadow-sm rounded-3 h-100" :style="{ borderLeft: '5px solid ' + (apiUsagePercentage > 80 ? '#ef4444' : '#10b981') }">
            <div class="card-body card-body-highlight p-3">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <small class="text-muted fw-semibold">Giới hạn sử dụng API</small>
                <i class="fa-solid fa-microchip" :style="{ color: apiUsagePercentage > 80 ? '#ef4444' : '#10b981' }"></i>
              </div>
              <div class="d-flex justify-content-between align-items-end mb-1">
                <h4 class="mb-0 fw-bold text-dark">{{ apiUsagePercentage }}%</h4>
                <small class="text-muted">{{ keyMetrics.api_used.toLocaleString() }}/{{ keyMetrics.api_limit.toLocaleString() }} req</small>
              </div>
              <div class="progress" style="height: 6px;">
                <div class="progress-bar" :style="{ width: apiUsagePercentage + '%', backgroundColor: apiUsagePercentage > 80 ? '#ef4444' : '#10b981' }"></div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="row g-3 mb-4">
        <div class="col-lg-8">
          <div class="card border-0 shadow-sm rounded-3 h-100 bg-white">
            <div class="card-body p-4">
              <div class="d-flex justify-content-between align-items-center mb-4">
                <h6 class="fw-bold mb-0"><i class="fa-solid fa-chart-line me-2 text-primary"></i>Hiệu Suất API (Request/giây)</h6>
                <small class="text-muted">Theo dõi tải hệ thống</small>
              </div>
              <div style="position: relative; height: 300px;">
                <canvas ref="apiPerformanceChart"></canvas>
              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-4">
          <div class="card border-0 shadow-sm rounded-3 h-100 bg-white">
            <div class="card-body p-4">
              <h6 class="fw-bold mb-4"><i class="fa-solid fa-comments me-2 text-warning"></i>Tình Trạng Chat Hỗ Trợ</h6>
              <div style="position: relative; height: 210px;" class="mb-3">
                <canvas ref="chatSupportChart"></canvas>
              </div>
              <div class="d-grid gap-2">
                <div class="d-flex justify-content-between align-items-center p-2 rounded-2" style="background-color: #fff7ed;">
                  <small class="fw-semibold text-dark"><i class="fa-solid fa-circle me-1" style="color:#f97316;"></i>Phiên chat đang mở</small>
                  <span class="badge bg-warning-subtle text-warning">{{ chatSupportStats.openSessions }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center p-2 rounded-2" style="background-color: #f0fdf4;">
                  <small class="fw-semibold text-dark"><i class="fa-solid fa-circle me-1" style="color:#22c55e;"></i>Tin chưa đọc của giáo viên</small>
                  <span class="badge bg-success-subtle text-success">{{ chatSupportStats.unreadByTeacher }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center p-2 rounded-2" style="background-color: #eff6ff;">
                  <small class="fw-semibold text-dark"><i class="fa-solid fa-circle me-1" style="color:#3b82f6;"></i>Tin chưa đọc của học viên</small>
                  <span class="badge bg-primary-subtle text-primary">{{ chatSupportStats.unreadByStudent }}</span>
                </div>
              </div>

              <small class="text-muted d-block text-center mt-4">
                <i class="fa-solid fa-clock me-1"></i>{{ systemMetrics.lastUpdatedText || 'Cập nhật vừa xong' }}
              </small>
            </div>
          </div>
        </div>
      </div>

      <div class="row g-3">
        <div class="col-lg-6">
          <div class="card border-0 shadow-sm rounded-3 h-100 bg-white">
            <div class="card-body p-4">
              <h6 class="fw-bold mb-4"><i class="fa-solid fa-database me-2 text-info"></i>Sức Khỏe Cơ Sở Dữ Liệu</h6>
              <div class="row g-2 mb-3">
                <div class="col-6">
                  <div class="p-3 rounded-2" style="background-color: #f3f4f6;">
                    <small class="text-muted d-block mb-1">Tối ưu hóa</small>
                    <h5 class="mb-0 fw-bold text-dark">{{ systemMetrics.dbOptimization }}%</h5>
                  </div>
                </div>
                <div class="col-6">
                  <div class="p-3 rounded-2" style="background-color: #f3f4f6;">
                    <small class="text-muted d-block mb-1">Slow Queries</small>
                    <h5 class="mb-0 fw-bold" :style="{ color: systemMetrics.slowQueries > 10 ? '#ef4444' : '#10b981' }">{{ systemMetrics.slowQueries }}</h5>
                  </div>
                </div>
              </div>
              <div class="row g-2">
                <div class="col-6">
                  <div class="p-3 rounded-2" style="background-color: #f3f4f6;">
                    <small class="text-muted d-block mb-1">Kích thước DB</small>
                    <h5 class="mb-0 fw-bold text-dark">{{ systemMetrics.dbSize }}</h5>
                  </div>
                </div>
                <div class="col-6">
                  <div class="p-3 rounded-2" style="background-color: #f3f4f6;">
                    <small class="text-muted d-block mb-1">Lần backup cuối</small>
                    <h5 class="mb-0 fw-bold text-dark">{{ systemMetrics.lastBackup }}</h5>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-6">
          <div class="card border-0 shadow-sm rounded-3 h-100 bg-white">
            <div class="card-body p-4">
              <div class="d-flex justify-content-between align-items-center mb-4">
                <h6 class="fw-bold mb-0"><i class="fa-solid fa-bell me-2 text-danger"></i>Cảnh Báo Hệ Thống</h6>
                <a href="/admin/error-logs" class="text-decoration-none text-primary" style="font-size: 0.85rem;">Xem chi tiết</a>
              </div>
              <div class="d-flex align-items-start mb-3" v-for="canh_bao in danh_sach_canh_bao" :key="canh_bao.id">
                <div class="p-2 rounded-circle me-3" :style="{ backgroundColor: canh_bao.type === 'error' ? '#fee2e2' : canh_bao.type === 'warning' ? '#fef3c7' : '#d1fae5' }">
                  <i :class="canh_bao.icon" :style="{ color: canh_bao.type === 'error' ? '#ef4444' : canh_bao.type === 'warning' ? '#f59e0b' : '#10b981', fontSize: '1rem' }"></i>
                </div>
                <div class="flex-grow-1 border-bottom pb-3">
                  <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="fw-bold" style="font-size: 0.95rem;">{{ canh_bao.tieu_de }}</span>
                    <small style="font-size: 0.75rem; color: #a0aec0;">{{ canh_bao.thoi_gian }}</small>
                  </div>
                  <p class="text-muted mb-0" style="font-size: 0.85rem;">{{ canh_bao.mo_ta }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div v-show="activeTab === 'reports'">
      <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body p-3">
          <div class="d-flex justify-content-between mb-3">
            <div>
              <h6 class="fw-bold mb-1">Bộ Lọc Báo Cáo</h6>
              <p class="text-muted mb-0" style="font-size: 0.9rem;">Lọc theo khoảng thời gian và service để lấy báo cáo hệ thống cụ thể.</p>
            </div>
            <button class="btn btn-success btn-sm" @click="xuatBaoCao" :disabled="isExporting">
              <i class="fa-solid fa-file-excel me-1" v-if="!isExporting"></i>
              <span class="spinner-border spinner-border-sm me-1" v-else></span>
              {{ isExporting ? 'Đang xuất...' : 'Xuất báo cáo' }}
            </button>
          </div>
          <div class="row g-3 align-items-end">
            <div class="col-12 col-md-3">
              <label class="form-label small text-muted fw-semibold mb-1">Từ ngày</label>
              <input type="date" class="form-control shadow-none" v-model="reportFilter.startDate">
            </div>
            <div class="col-12 col-md-3">
              <label class="form-label small text-muted fw-semibold mb-1">Đến ngày</label>
              <input type="date" class="form-control shadow-none" v-model="reportFilter.endDate">
            </div>
            <div class="col-12 col-md-3">
              <label class="form-label small text-muted fw-semibold mb-1">Service</label>
              <select class="form-select shadow-none" v-model="reportFilter.service">
                <option value="">-- Tất cả --</option>
                <option value="API">API Server</option>
                <option value="Database">Database</option>
                <option value="Cache">Cache/Redis</option>
                <option value="Auth">Authentication</option>
                <option value="Queue">Message Queue</option>
                <option value="Storage">Storage</option>
              </select>
            </div>
            <div class="col-12 col-md-3 d-flex gap-2">
              <button class="btn btn-primary flex-grow-1" @click="apDungBoLoc" :disabled="isLoading">
                <i class="fa-solid fa-magnifying-glass me-1" v-if="!isLoading"></i>
                <span class="spinner-border spinner-border-sm me-1" v-else></span>
                {{ isLoading ? 'Đang lọc...' : 'Áp dụng' }}
              </button>
              <button class="btn btn-light border" @click="datLaiBoLoc" :disabled="isLoading">
                <i class="fa-solid fa-rotate-right"></i>
              </button>
            </div>
          </div>
        </div>
      </div>

      <div class="row g-3 mb-4">
        <div class="col-lg-3 col-md-6" v-for="metric in reportSummaryCards" :key="metric.title">
          <div class="card border-0 shadow-sm rounded-3 h-100 bg-white">
            <div class="card-body p-3">
              <small class="text-muted fw-semibold">{{ metric.title }}</small>
              <h3 class="fw-bold text-dark mt-1 mb-0">{{ metric.value }}</h3>
              <small class="text-muted">{{ metric.subtitle }}</small>
            </div>
          </div>
        </div>
      </div>

      <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body p-4">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-server text-primary me-2"></i>Service Health Report</h6>
            <span class="badge bg-light text-dark border">{{ reportFilter.service || 'Tất cả service' }}</span>
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
                <tr v-for="service in filteredServiceHealth" :key="service.id">
                  <td class="text-start ps-3 fw-semibold text-dark">{{ service.name }}</td>
                  <td>
                    <span class="badge" :class="service.status === 'online' ? 'bg-success' : 'bg-danger'">
                      {{ service.status === 'online' ? 'Online' : 'Offline' }}
                    </span>
                  </td>
                  <td class="fw-bold">{{ service.uptime }}%</td>
                  <td>{{ service.requestsPerDay.toLocaleString() }}</td>
                  <td><strong :class="service.avgResponse < 200 ? 'text-success' : service.avgResponse < 500 ? 'text-warning' : 'text-danger'">{{ service.avgResponse }}ms</strong></td>
                  <td><span class="badge" :class="service.errorRate < 1 ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning'">{{ service.errorRate }}%</span></td>
                </tr>
                <tr v-if="filteredServiceHealth.length === 0">
                  <td colspan="6" class="py-4 text-muted">Không có service phù hợp.</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body p-4">
          <h6 class="fw-bold mb-4 text-dark"><i class="fa-solid fa-triangle-exclamation text-danger me-2"></i>Incident Summary</h6>
          <div class="row g-4">
            <div class="col-lg-5 border-end-lg pe-lg-4">
              <div v-for="incident in incidentSummary" :key="incident.type" class="mb-4">
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
                    <tr v-for="error in topErrors" :key="error.id">
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

  </div>
</template>

<script>
import axios from 'axios';
import Chart from 'chart.js/auto';

export default {
  name: 'AdminDashboard',
  data() {
    return {
      ho_ten_admin: '',
      apiBase: (import.meta.env.VITE_API_URL || 'http://127.0.0.1:8000').replace(/\/$/, ''),
      activeTab: 'monitoring',
      bo_loc_thoi_gian: 'thang',
      apiHealth: {
        status: 'healthy',
        uptime: 99.9
      },
      keyMetrics: {
        api_used: 35000,
        api_limit: 50000
      },
      systemMetrics: {
        errorCount: 3,
        errorTrend: '+2 từ hôm qua',
        dbStatus: 'connected',
        dbConnections: 24,
        activeUsers: 0,
        lockedUsers: 0,
        unreadNotices: 0,
        dbOptimization: 92,
        slowQueries: 5,
        dbSize: '8.5 GB',
        lastBackup: '2 giờ trước',
        lastUpdatedText: 'Cập nhật vừa xong'
      },
      duLieuApiPerformance: {
        tuan: { labels: ['T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'CN'], data: [450, 520, 480, 650, 580, 720, 890] },
        thang: { labels: ['Tuần 1', 'Tuần 2', 'Tuần 3', 'Tuần 4'], data: [4500, 5200, 4800, 6500] },
        nam: { labels: ['T1', 'T2', 'T3', 'T4', 'T5', 'T6'], data: [45000, 52000, 48000, 65000, 58000, 72000] }
      },
      danh_sach_canh_bao: [
        { id: 1, icon: 'fa-solid fa-server', type: 'warning', tieu_de: 'CPU cao', mo_ta: 'Mức sử dụng CPU ở 65%, khuyến nghị tối ưu.', thoi_gian: '5 phút trước' },
        { id: 2, icon: 'fa-solid fa-database', type: 'info', tieu_de: 'Backup thành công', mo_ta: 'Backup định kỳ hoàn thành lúc 02:00.', thoi_gian: '2 giờ trước' },
        { id: 3, icon: 'fa-solid fa-exclamation-triangle', type: 'error', tieu_de: 'Lỗi API endpoint', mo_ta: "Service '/api/voice' bị lỗi 3 lần, tự phục hồi.", thoi_gian: '30 phút trước' }
      ],
      reportFilter: {
        startDate: '',
        endDate: '',
        service: ''
      },
      isLoading: false,
      isExporting: false,
      chatSupportStats: {
        openSessions: 0,
        unreadByTeacher: 0,
        unreadByStudent: 0,
        totalPending: 0
      },
      reportSummaryCards: [
        { title: 'Total Requests', value: '2,543,891', subtitle: 'requests tháng này' },
        { title: 'Success Rate', value: '99.2%', subtitle: 'request thành công' },
        { title: 'Errors (24h)', value: '45', subtitle: 'sự kiện lỗi' },
        { title: 'Avg Response Time', value: '145ms', subtitle: 'thời gian phản hồi' }
      ],
      serviceHealth: [
        { id: 1, name: 'API Server', status: 'online', uptime: 99.8, requestsPerDay: 245000, avgResponse: 145, errorRate: 0.2 },
        { id: 2, name: 'Database (MySQL)', status: 'online', uptime: 99.9, requestsPerDay: 180000, avgResponse: 230, errorRate: 0.1 },
        { id: 3, name: 'Redis Cache', status: 'online', uptime: 99.7, requestsPerDay: 520000, avgResponse: 5, errorRate: 0.3 },
        { id: 4, name: 'Authentication', status: 'online', uptime: 99.95, requestsPerDay: 85000, avgResponse: 180, errorRate: 0.05 },
        { id: 5, name: 'Message Queue', status: 'online', uptime: 99.6, requestsPerDay: 150000, avgResponse: 250, errorRate: 0.4 }
      ],
      incidentSummary: [
        { type: 'Database Errors (500)', severity: 'critical', count: 23, percent: 45, description: 'Query timeout và connection errors' },
        { type: 'API Rate Limit (429)', severity: 'warning', count: 18, percent: 35, description: 'Vượt quota giới hạn' },
        { type: 'Service Warnings', severity: 'info', count: 10, percent: 20, description: 'Response time cao, cảnh báo hệ thống' }
      ],
      topErrors: [
        { id: 1, code: '500', service: 'API Server', count: 23, lastOccurrence: '5 phút trước' },
        { id: 2, code: '429', service: 'API Server', count: 18, lastOccurrence: '15 phút trước' },
        { id: 3, code: '503', service: 'Database', count: 12, lastOccurrence: '1 giờ trước' },
        { id: 4, code: '502', service: 'Gateway', count: 8, lastOccurrence: '2 giờ trước' },
        { id: 5, code: '504', service: 'Cache', count: 5, lastOccurrence: '3 giờ trước' }
      ]
    };

  },
  computed: {
    apiUsagePercentage() {
      return Math.round((this.keyMetrics.api_used / this.keyMetrics.api_limit) * 100);
    },
    filteredServiceHealth() {
      if (!this.reportFilter.service) {
        return this.serviceHealth;
      }
      return this.serviceHealth.filter(service => service.name.toLowerCase().includes(this.reportFilter.service.toLowerCase()));
    },
    ho_ten_admin_hien_thi() {
      return (this.ho_ten_admin || '').trim();
    },
  },
  watch: {
    '$route.query.tab': {
      immediate: true,
      handler(value) {
        this.dongBoTabTuQuery(value);
      }
    }
  },
  created() {
    this.chartInstances = {
      apiPerformance: null,
      chatSupport: null
    };
  },
  mounted() {
    this.ho_ten_admin = localStorage.getItem('ho_ten') || '';
    this.setReportDefaults();
    this.renderApiPerformanceChart();
    this.taiDuLieuTongQuan();
    this.apDungBoLoc();
  },
  beforeUnmount() {
    if (this.chartInstances.apiPerformance) this.chartInstances.apiPerformance.destroy();
    if (this.chartInstances.chatSupport) this.chartInstances.chatSupport.destroy();
  },
  methods: {
    authHeaders() {
      return {
        Authorization: 'Bearer ' + (localStorage.getItem('token_admin') || ''),
      };
    },
    xuLyLoiAxios(err, defaultMessage = 'Không thể kết nối máy chủ.') {
      if (err?.response?.status === 401) {
        this.$toast?.error('Phiên đăng nhập hết hạn, vui lòng đăng nhập lại.');
        this.$router.push('/dang-nhap');
        return;
      }
      this.$toast?.error(err?.response?.data?.message || defaultMessage);
    },
    dongBoTabTuQuery(tabQuery) {
      const allowedTabs = ['monitoring', 'reports'];
      this.activeTab = allowedTabs.includes(tabQuery) ? tabQuery : 'monitoring';
    },
    chuyenTab(tab) {
      this.activeTab = tab;
      this.$router.replace({
        path: '/admin/dashboard',
        query: tab === 'monitoring' ? {} : { tab }
      });
    },
    renderApiPerformanceChart() {
      const ctx = this.$refs.apiPerformanceChart.getContext('2d');
      const dl = this.duLieuApiPerformance[this.bo_loc_thoi_gian];
      this.chartInstances.apiPerformance = new Chart(ctx, {
        type: 'line',
        data: {
          labels: dl.labels,
          datasets: [{
            label: 'Request/giây',
            data: dl.data,
            borderColor: '#3b82f6',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            borderWidth: 3,
            tension: 0.4,
            fill: true,
            pointRadius: 5,
            pointBackgroundColor: '#3b82f6',
            pointBorderColor: '#fff',
            pointBorderWidth: 2
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: { position: 'bottom' }
          },
          scales: {
            y: {
              beginAtZero: true,
              grid: { borderDash: [5, 5] },
              title: { display: true, text: 'Request/giây' }
            }
          }
        }
      });
    },
    renderChatSupportChart() {
      const ctx = this.$refs.chatSupportChart?.getContext('2d');
      if (!ctx) return;
      if (this.chartInstances.chatSupport) this.chartInstances.chatSupport.destroy();

      this.chartInstances.chatSupport = new Chart(ctx, {
        type: 'doughnut',
        data: {
          labels: ['Phiên đang mở', 'Chưa đọc GV', 'Chưa đọc HV'],
          datasets: [{
            data: [
              this.chatSupportStats.openSessions,
              this.chatSupportStats.unreadByTeacher,
              this.chatSupportStats.unreadByStudent
            ],
            backgroundColor: ['#f97316', '#22c55e', '#3b82f6'],
            borderWidth: 0
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          cutout: '68%',
          plugins: {
            legend: { display: false }
          }
        }
      });
    },
    setReportDefaults() {
      const today = new Date();
      const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
      this.reportFilter.startDate = firstDay.toISOString().split('T')[0];
      this.reportFilter.endDate = today.toISOString().split('T')[0];
    },
    async taiDuLieuTongQuan() {
      try {
        const [realtimeRes, performanceRes] = await Promise.all([
          axios.get(this.apiBase + '/api/admin/dashboard/realtime', {
            headers: this.authHeaders(),
          }),
          axios.get(this.apiBase + '/api/admin/dashboard/performance', {
            params: { period: this.bo_loc_thoi_gian },
            headers: this.authHeaders(),
          }),
        ]);

        const realtimeData = realtimeRes?.data?.data || {};
        this.apiHealth = realtimeData.api_health || this.apiHealth;
        this.keyMetrics = realtimeData.key_metrics || this.keyMetrics;
        this.systemMetrics = {
          ...this.systemMetrics,
          ...(realtimeData.system_metrics || {}),
        };
        this.chatSupportStats = realtimeData.chat_support || this.chatSupportStats;
        this.danh_sach_canh_bao = realtimeData.alerts || this.danh_sach_canh_bao;
        this.renderChatSupportChart();

        if (performanceRes?.data?.status) {
          this.duLieuApiPerformance[this.bo_loc_thoi_gian] = performanceRes.data.data || { labels: [], data: [] };
          const chartData = this.duLieuApiPerformance[this.bo_loc_thoi_gian];
          if (this.chartInstances.apiPerformance) {
            this.chartInstances.apiPerformance.data.labels = chartData.labels || [];
            this.chartInstances.apiPerformance.data.datasets[0].data = chartData.data || [];
            this.chartInstances.apiPerformance.update();
          }
        }
      } catch (err) {
        this.xuLyLoiAxios(err, 'Không tải được dữ liệu dashboard.');
      }
    },
    capNhatToanBoDuLieu() {
      this.taiDuLieuTongQuan();
    },
    lamTuoiDuLieu() {
      this.taiDuLieuTongQuan();
      this.apDungBoLoc();
      this.$toast?.success('Đã làm tươi dữ liệu.');
    },
    async apDungBoLoc() {
      this.isLoading = true;
      try {
        const res = await axios.get(this.apiBase + '/api/admin/dashboard/reports', {
          params: {
            startDate: this.reportFilter.startDate,
            endDate: this.reportFilter.endDate,
            service: this.reportFilter.service,
          },
          headers: this.authHeaders(),
        });
        const data = res?.data?.data;
        if (!data) {
          this.$toast?.error('Không nhận được dữ liệu báo cáo hợp lệ.');
          return;
        }

        const summary = data.summary || {};
        this.reportSummaryCards = [
          { title: 'Total Requests', value: Number(summary.totalRequests || 0).toLocaleString(), subtitle: 'requests trong khoảng lọc' },
          { title: 'Success Rate', value: `${summary.successRate || 0}%`, subtitle: 'request thành công' },
          { title: 'Errors', value: Number(summary.errorCount || 0).toLocaleString(), subtitle: 'sự kiện lỗi' },
          { title: 'Avg Response Time', value: `${summary.avgResponseTime || 0}ms`, subtitle: 'thời gian phản hồi' }
        ];
        this.serviceHealth = data.serviceHealth || [];
        this.incidentSummary = data.incidentSummary || [];
        this.topErrors = data.topErrors || [];
      } catch (err) {
        this.xuLyLoiAxios(err, 'Không tải được báo cáo chi tiết.');
      } finally {
        this.isLoading = false;
      }
    },
    datLaiBoLoc() {
      this.reportFilter.service = '';
      this.setReportDefaults();
      this.apDungBoLoc();
    },
    async xuatBaoCao() {
      if (!this.reportFilter.startDate || !this.reportFilter.endDate) {
        this.$toast?.error('Vui lòng chọn khoảng thời gian hợp lệ.');
        return;
      }
      this.isExporting = true;
      try {
        const res = await axios.get(this.apiBase + '/api/admin/dashboard/export', {
          params: {
            startDate: this.reportFilter.startDate,
            endDate: this.reportFilter.endDate,
            service: this.reportFilter.service,
          },
          headers: this.authHeaders(),
          responseType: 'blob',
        });
        const blob = new Blob([res.data], { type: 'text/csv;charset=utf-8;' });
        const url = window.URL.createObjectURL(blob);
        const link = document.createElement('a');
        const disposition = res.headers?.['content-disposition'] || '';
        const matchedName = disposition.match(/filename="?(.*?)"?$/);
        const downloadName = matchedName?.[1] || `admin-report-${this.reportFilter.startDate}-to-${this.reportFilter.endDate}.csv`;
        link.href = url;
        link.setAttribute('download', downloadName);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        window.URL.revokeObjectURL(url);
        this.$toast?.success('Đã xuất báo cáo.');
      } catch (err) {
        this.xuLyLoiAxios(err, 'Không thể xuất báo cáo.');
      } finally {
        this.isExporting = false;
      }
    }
  }
};
</script>

<style scoped>
.admin-dashboard {
  background-color: #f6f8fc;
  font-family: system-ui, -apple-system, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol';
  color: #111827;
}

.dashboard-header {
  border-bottom: 1px solid rgba(148, 163, 184, 0.16);
}

.dashboard-title {
  color: #111827;
}

.dashboard-subtitle {
  color: #6b7280;
  font-size: 0.95rem;
}

.btn-dashboard-action {
  border-radius: 14px;
  padding: 0.55rem 1rem;
  font-weight: 600;
}

.btn-dashboard-action.btn-outline-danger {
  color: #b91c1c;
  border-color: #fb7185;
  background-color: #fff;
}

.btn-dashboard-action.btn-outline-danger:hover {
  background-color: #fef2f2;
  border-color: #f87171;
}

.input-rounded {
  border-radius: 14px !important;
}

.admin-dashboard .card {
  border: 1px solid rgba(15, 23, 42, 0.08);
  border-radius: 20px;
  box-shadow: 0 14px 40px rgba(15, 23, 42, 0.06);
}

.admin-dashboard .card .card-body {
  padding: 1.4rem;
}

.admin-dashboard .card.bg-white {
  background-color: #ffffff;
}

.admin-dashboard .text-muted {
  color: #6b7280 !important;
}

.admin-dashboard .table thead th {
  border-bottom: 1px solid #e5e7eb;
  text-transform: uppercase;
  letter-spacing: 0.02em;
  font-size: 0.82rem;
  color: #475569;
}

.admin-dashboard .table tbody tr:hover {
  background-color: #f8fafc;
}

.admin-dashboard .table td,
.admin-dashboard .table th {
  vertical-align: middle;
}

.admin-dashboard .status-badge,
.admin-dashboard .badge-pill {
  border-radius: 999px;
  padding: 0.4rem 0.85rem;
  font-weight: 700;
  letter-spacing: -0.01em;
}

.admin-dashboard .badge-pill {
  background-color: #f8fafc;
  color: #111827;
}

.admin-dashboard .progress {
  height: 8px;
  border-radius: 999px;
  background-color: #e5e7eb;
}

.admin-dashboard .progress-bar {
  border-radius: 999px;
}

.admin-dashboard .form-control,
.admin-dashboard .form-select {
  border-color: #e5e7eb;
  box-shadow: none;
  min-height: 42px;
}

.admin-dashboard .form-control:focus,
.admin-dashboard .form-select:focus {
  border-color: #3b82f6;
  box-shadow: 0 0 0 0.15rem rgba(59, 130, 246, 0.18);
}

.admin-dashboard .card-body h6,
.admin-dashboard .card-body h5,
.admin-dashboard .card-body h4,
.admin-dashboard .card-body h3 {
  color: #111827;
}

.dashboard-card--success .card-body {
  background: linear-gradient(180deg, #f0fdf4 0%, #ecfdf5 100%);
}

.dashboard-card--info .card-body {
  background: linear-gradient(180deg, #eff6ff 0%, #f8fbff 100%);
}

.dashboard-card--blue .card-body {
  background: linear-gradient(180deg, #eef2ff 0%, #f8fafc 100%);
}

.dashboard-card--warning .card-body {
  background: linear-gradient(180deg, #fffbeb 0%, #fef3c7 100%);
}

.dashboard-card--success .card-body,
.dashboard-card--info .card-body,
.dashboard-card--blue .card-body,
.dashboard-card--warning .card-body {
  border: 1px solid rgba(148, 163, 184, 0.16);
}

.admin-dashboard .card-body .text-dark {
  color: #111827 !important;
}

.admin-dashboard .card-body .text-primary {
  color: #2563eb !important;
}

.admin-dashboard .card-body .text-danger {
  color: #dc2626 !important;
}

.admin-dashboard .bg-success-subtle,
.admin-dashboard .bg-warning-subtle,
.admin-dashboard .bg-primary-subtle,
.admin-dashboard .bg-light {
  background-color: #f8fafc !important;
}

.admin-dashboard .shadow-sm {
  box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06) !important;
}

.dashboard-card {
  border-radius: 1.5rem;
  overflow: hidden;
}

.card-body-highlight {
  border-radius: 1.5rem;
  background: linear-gradient(180deg, rgba(255,255,255,0.96) 0%, rgba(248,250,252,0.98) 100%);
  border: 1px solid rgba(148, 163, 184, 0.16);
  box-shadow: inset 0 0 0 1px rgba(255,255,255,0.9);
}

.dashboard-card + .dashboard-card {
  margin-top: 0.75rem;
}

.admin-dashboard a.text-primary:hover {
  color: #1d4ed8 !important;
}
</style>
