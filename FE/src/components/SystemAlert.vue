<template>
  <div v-if="showModal && alert.is_active" class="modal d-block" tabindex="-1" role="dialog" style="background: rgba(0,0,0,0.45); z-index: 1500;">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content border-0 shadow-lg">
        <div class="modal-header bg-warning-subtle">
          <h5 class="modal-title text-danger fw-bold mb-0">
            <i class="fa-solid fa-triangle-exclamation me-2"></i>
            Thông báo hệ thống
          </h5>
          <button type="button" class="btn-close" aria-label="Close" @click="showModal = false"></button>
        </div>
        <div class="modal-body">
          <p class="mb-0 text-dark">{{ alert.message }}</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" @click="showModal = false">Đã hiểu</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  data() {
    return {
      showModal: false,
      alert: {
        is_active: false,
        message: "",
      },
    };
  },
  mounted() {
    axios
      .get("http://127.0.0.1:8000/api/cau-hinh/thong-bao")
      .then((res) => {
        const data = res.data?.data || res.data || {};
        this.alert = {
          is_active: !!data.is_active,
          message: data.message || "",
        };
        if (this.alert.is_active && this.alert.message) {
          this.showModal = true;
        }
      })
      .catch(() => {});
  },
};
</script>
