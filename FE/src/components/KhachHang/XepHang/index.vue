<template>
  <div class="page-full bg-light min-vh-100">
    <div class="content-wrapper py-5">
      <div v-if="loading" class="text-center py-5 text-muted">Dang tai...</div>

      <template v-else>
        <div class="row justify-content-center align-items-end g-4 mb-5">
          <div
            v-for="item in topThree"
            :key="item.id"
            :class="[item.rank === 1 ? 'col-lg-4 col-md-5 col-12' : 'col-lg-3 col-md-4 col-12']"
          >
            <div
              class="card border-0 rounded-5 text-center p-4 top-card h-100 position-relative overflow-hidden"
              :class="item.cardClass"
            >
              <div class="top-bg-circle" :class="item.bgClass"></div>

              <div v-if="item.rank === 1" class="winner-crown">👑</div>

              <div class="position-relative mx-auto mb-3 mt-2">
                <div class="avatar-ring" :class="item.ringClass">
                  <img
                    :src="item.avatar"
                    class="rounded-circle avatar-image"
                    :class="item.rank === 1 ? 'avatar-large' : 'avatar-small'"
                    alt=""
                  />
                </div>

                <div class="top-rank-badge" :class="item.badgeClass">
                  {{ item.rank }}
                </div>
              </div>

              <h4 class="fw-bold mb-1" :class="item.rank === 1 ? 'fs-3' : 'fs-5'">
                {{ item.name }}
              </h4>

              <div class="small text-muted mb-2">{{ item.score }} diem</div>

              <div class="small fw-semibold mb-3 top-label">
                {{ item.label }}
              </div>

              <div class="top-progress">
                <div
                  class="top-progress-bar"
                  :class="item.progressClass"
                  :style="{ width: item.progress + '%' }"
                ></div>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-5 shadow-sm p-4 p-lg-5 leaderboard-box">
          <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
            <div>
              <h3 class="fw-bold mb-1 title-text">Bang Xep Hang</h3>
              <p class="text-muted mb-0">Xep theo diem tich luy</p>
            </div>
          </div>

          <div class="ranking-scroll-area" ref="rankingContainer" @scroll="handleScroll">
            <div class="ranking-item" v-for="(item, index) in rankingList" :key="item.id">
              <div class="d-flex align-items-center gap-3 flex-grow-1">
                <div class="rank-number">{{ index + 4 }}</div>

                <img :src="item.avatar" class="rounded-circle shadow-sm user-avatar" alt="" />

                <div>
                  <h6 class="fw-bold mb-1 title-text">{{ item.name }}</h6>

                  <small class="text-muted d-block mb-2">
                    {{ item.streak }}
                  </small>

                  <div class="progress rounded-pill custom-progress">
                    <div class="progress-bar bg-warning" :style="{ width: item.progress + '%' }"></div>
                  </div>
                </div>
              </div>

              <div class="text-end right-info">
                <h6 class="fw-bold mb-1 score-text">{{ item.score }} diem</h6>
              </div>
            </div>
          </div>
        </div>
      </template>
    </div>
  </div>
</template>

<script>
import { API_BASE } from '../../../api/http.js';
import axios from "axios";

export default {
  name: "LeaderboardPage",

  data() {
    return {
      loading: true,
      rawLeaderboard: [],
      showStickyRank: false,
      topThree: [],
      rankingList: [],
    };
  },

  mounted() {
    this.loadLeaderboard();
    this.$nextTick(() => {
      this.handleScroll();
      window.addEventListener("resize", this.handleScroll);
    });
  },

  beforeUnmount() {
    window.removeEventListener("resize", this.handleScroll);
  },

  methods: {
    loadLeaderboard() {
      this.loading = true;
      axios
        .get(`${API_BASE}/api/leaderboard?type=points&limit=20`)
        .then((res) => {
          if (res.data.status) {
            this.rawLeaderboard = Array.isArray(res.data.data) ? res.data.data : [];
            this.buildLeaderboardView();
          } else {
            this.rawLeaderboard = [];
            this.buildLeaderboardView();
          }
        })
        .catch(() => {
          this.rawLeaderboard = [];
          this.buildLeaderboardView();
        })
        .finally(() => {
          this.loading = false;
        });
    },
    buildLeaderboardView() {
      const rows = this.rawLeaderboard;
      const maxPoint = rows.length > 0 ? Math.max(...rows.map((x) => Number(x.diem_tich_luy || 0))) : 1;
      const safeMax = maxPoint > 0 ? maxPoint : 1;
      const getProgress = (p) => Math.max(8, Math.round((Number(p || 0) / safeMax) * 100));
      const avatarFallback = "/Client/images/user.jpg";

      const topMap = [
        {
          rank: 2,
          cardClass: "second-place shadow-sm",
          bgClass: "second-bg",
          ringClass: "silver-ring",
          badgeClass: "silver-badge",
          progressClass: "second-progress",
          orderIndex: 1,
        },
        {
          rank: 1,
          cardClass: "first-place shadow",
          bgClass: "first-bg",
          ringClass: "gold-ring",
          badgeClass: "gold-badge",
          progressClass: "first-progress",
          orderIndex: 0,
        },
        {
          rank: 3,
          cardClass: "third-place shadow-sm",
          bgClass: "third-bg",
          ringClass: "bronze-ring",
          badgeClass: "bronze-badge",
          progressClass: "third-progress",
          orderIndex: 2,
        },
      ];

      this.topThree = topMap
        .map((cfg, idx) => {
          const row = rows[cfg.orderIndex];
          if (!row) {
            return {
              id: `top-empty-${idx}`,
              rank: cfg.rank,
              name: "---",
              avatar: avatarFallback,
              score: 0,
              label: "Chua co du lieu",
              progress: 8,
              cardClass: cfg.cardClass,
              bgClass: cfg.bgClass,
              ringClass: cfg.ringClass,
              badgeClass: cfg.badgeClass,
              progressClass: cfg.progressClass,
            };
          }
          const streak = Number(row.streak_hien_tai || 0);
          return {
            id: row.nguoi_dung_id || `top-${idx}`,
            rank: cfg.rank,
            name: row.ho_ten || "Nguoi dung",
            avatar: avatarFallback,
            score: Number(row.diem_tich_luy || 0),
            label: `🔥 ${streak} ngay lien tiep`,
            progress: getProgress(row.diem_tich_luy),
            cardClass: cfg.cardClass,
            bgClass: cfg.bgClass,
            ringClass: cfg.ringClass,
            badgeClass: cfg.badgeClass,
            progressClass: cfg.progressClass,
          };
        })
        .filter(Boolean);

      this.rankingList = rows.slice(3).map((row, idx) => {
        const streak = Number(row.streak_hien_tai || 0);
        return {
          id: row.nguoi_dung_id || `rank-${idx}`,
          name: row.ho_ten || "Nguoi dung",
          avatar: avatarFallback,
          score: Number(row.diem_tich_luy || 0),
          streak: `🔥 ${streak} ngay lien tiep`,
          progress: getProgress(row.diem_tich_luy),
        };
      });
    },
    handleScroll() {
      const container = this.$refs.rankingContainer;
      if (!container) return;
      this.showStickyRank = container.scrollTop > 250;
    },
  },
};
</script>

<style scoped>
.title-text {
  color: #0d3b66;
}

.score-text {
  color: #ff6b35;
}

.top-label {
  color: #ff9f1c;
}

.leaderboard-box {
  border: 1px solid #f2f2f2;
}

.top-card {
  background: #fff;
  transition: all 0.35s ease;
}

.top-card:hover {
  transform: translateY(-10px);
}

.first-place {
  border: 2px solid #ffe29a;
}

.second-place {
  border: 2px solid #e3e7ee;
}

.third-place {
  border: 2px solid #f0d5c3;
}

.top-bg-circle {
  position: absolute;
  top: -50px;
  right: -50px;
  width: 140px;
  height: 140px;
  border-radius: 50%;
  opacity: 0.25;
}

.first-bg {
  background: #ffd54f;
}

.second-bg {
  background: #cdd5df;
}

.third-bg {
  background: #e0ab88;
}

.avatar-ring {
  width: 110px;
  height: 110px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: auto;
}

.gold-ring {
  background: linear-gradient(135deg, #ffd54f, #fff3bf);
}

.silver-ring {
  background: linear-gradient(135deg, #bcc5d0, #eef2f7);
}

.bronze-ring {
  background: linear-gradient(135deg, #c77d4e, #f4d3be);
}

.avatar-image {
  object-fit: cover;
  border: 4px solid #fff;
}

.avatar-large {
  width: 100px;
  height: 100px;
}

.avatar-small {
  width: 88px;
  height: 88px;
}

.top-rank-badge {
  position: absolute;
  bottom: -10px;
  right: -2px;
  width: 52px;
  height: 52px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 18px;
  font-weight: 700;
  box-shadow: 0 8px 18px rgba(0, 0, 0, 0.18);
  border: 4px solid #fff;
}

.gold-badge {
  background: linear-gradient(135deg, #ffb300, #ffd54f);
  color: #5a3d00;
}

.silver-badge {
  background: linear-gradient(135deg, #7d8796, #bcc5d0);
  color: white;
}

.bronze-badge {
  background: linear-gradient(135deg, #b76b3d, #d89b73);
  color: white;
}

.winner-crown {
  position: absolute;
  top: 12px;
  right: 18px;
  font-size: 28px;
}

.top-progress {
  width: 100%;
  height: 10px;
  background: #f2f4f7;
  border-radius: 999px;
  overflow: hidden;
}

.top-progress-bar {
  height: 100%;
  border-radius: 999px;
}

.first-progress {
  background: linear-gradient(90deg, #ffb300, #ffd54f);
}

.second-progress {
  background: linear-gradient(90deg, #8a94a5, #c6ced8);
}

.third-progress {
  background: linear-gradient(90deg, #b76b3d, #d89b73);
}

.ranking-scroll-area {
  max-height: 600px;
  overflow-y: auto;
  padding-right: 8px;
  position: relative;
}

.ranking-scroll-area::-webkit-scrollbar {
  width: 8px;
}

.ranking-scroll-area::-webkit-scrollbar-thumb {
  background: #ffb08d;
  border-radius: 999px;
}

.ranking-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 16px;
  padding: 18px;
  border-bottom: 1px solid #f3f3f3;
  border-radius: 22px;
  transition: all 0.3s ease;
}

.ranking-item:hover {
  background: #fffaf7;
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
}

.rank-number {
  width: 48px;
  height: 48px;
  border-radius: 16px;
  background: #fff1eb;
  color: #ff6b35;
  font-weight: bold;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.user-avatar {
  width: 65px;
  height: 65px;
  object-fit: cover;
}

.custom-progress {
  width: 160px;
  height: 8px;
  background: #f1f3f6;
}

@media (max-width: 768px) {
  .ranking-item {
    flex-direction: column;
    align-items: flex-start;
  }

  .right-info {
    width: 100%;
    text-align: left !important;
  }

  .custom-progress {
    width: 100%;
    min-width: 120px;
  }

  .avatar-ring {
    width: 90px;
    height: 90px;
  }

  .avatar-large,
  .avatar-small {
    width: 74px;
    height: 74px;
  }
}
.content-wrapper { width: 92%; max-width: 1650px; margin: 0 auto; } .page-full { width: 100vw; margin-left: calc(-50vw + 50%); }
</style>