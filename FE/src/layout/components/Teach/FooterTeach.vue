<template>
  <div>
      <div>
      <footer class="iq-footer mt-5">
        <div class="container-fluid">
          <div class="row">
            <div class="col-lg-6">
              <ul class="list-inline mb-0">
                <li class="list-inline-item">
                  <a href="privacy-policy.html">Privacy Policy</a>
                </li>
                <li class="list-inline-item">
                  <a href="terms-of-service.html">Terms of Use</a>
                </li>
              </ul>
            </div>
            <div class="col-lg-6 text-right">
              Copyright 2026 <a href="#">{{ footer.site_name }}</a>.
            </div>
          </div>
        </div>
      </footer>
    </div>
  </div>
  </template>
  <script>
  import axios from "axios";
  
  export default {
    data() {
      return {
        footer: {
          site_name: "EchoKids",
        },
      };
    },
    mounted() {
      this.taiCauHinhFooter();
    },
    methods: {
      taiCauHinhFooter() {
        axios
          .get("http://127.0.0.1:8000/api/cau-hinh/footer/data")
          .then((res) => {
            if (res.data?.status && res.data?.data) {
              this.footer.site_name = res.data.data.site_name || this.footer.site_name;
            }
          })
          .catch(() => {});
      },
    },
  };
  </script>
  <style></style>
  