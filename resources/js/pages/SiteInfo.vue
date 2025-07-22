<template>
  <div class="site-info-container">
    <div class="site-info-content">
      
      <div class="tech-stack">
        <h2 class="section-title">技術棧</h2>
        <div class="tech-grid">
          <div class="tech-item">
            <div class="tech-icon">🎨</div>
            <div class="tech-info">
              <h3>Frontend</h3>
              <p>Vue.js 3, TypeScript, Naive UI</p>
            </div>
          </div>
          <div class="tech-item">
            <div class="tech-icon">⚡</div>
            <div class="tech-info">
              <h3>Backend</h3>
              <p>Laravel, MySQL, Redis</p>
            </div>
          </div>
          <div class="tech-item">
            <div class="tech-icon">🛠️</div>
            <div class="tech-info">
              <h3>Build Tools</h3>
              <p>Vite, Composer, npm</p>
            </div>
          </div>
        </div>
      </div>

      <div class="version-info">
        <h2 class="section-title">版本資訊</h2>
        <div class="version-card">
          <div class="version-header">
            <span class="version-number">v1.0.0</span>
            <span class="version-status">Current</span>
          </div>
          <p class="version-date">發布於 2024年12月</p>
          <div class="feature-list">
            <div class="feature-item">📝 文章管理系統</div>
            <div class="feature-item">🔍 全文搜索功能</div>
            <div class="feature-item">🏷️ 標籤與分類系統</div>
            <div class="feature-item">📱 響應式設計</div>
            <div class="feature-item">🎨 現代化UI界面</div>
            <div class="feature-item">🔐 後台管理系統</div>
          </div>
        </div>
      </div>

      <div class="roadmap">
        <h2 class="section-title">開發計劃</h2>
        <div class="roadmap-list">
          <div class="roadmap-item">
            <div class="roadmap-version">v1.1.0</div>
            <div class="roadmap-features">
              <span>文章評論系統</span>
              <span>RSS訂閱功能</span>
              <span>SEO優化</span>
              <span>夜間模式</span>
            </div>
          </div>
          
          <div class="roadmap-item">
            <div class="roadmap-version">v2.0.0</div>
            <div class="roadmap-features">
              <span>多語言支持</span>
              <span>進階搜索</span>
              <span>文章推薦系統</span>
              <span>用戶管理</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
// 版本資訊可以從 package.json 或 API 獲取
// 這裡先使用靜態資料
</script>

<style scoped>
.site-info-container {
  max-width: 800px;
  margin: 0 auto;
  padding: 40px 20px;
}

.site-info-content {
  display: flex;
  flex-direction: column;
  gap: 32px;
}

.section-title {
  font-size: 1.5rem;
  font-weight: 600;
  color: var(--text-color);
  margin: 0 0 24px 0;
  padding-bottom: 8px;
  border-bottom: 2px solid var(--border-color);
}

/* 技術棧 */
.tech-stack {
  background: var(--card-background);
  border-radius: var(--card-border-radius);
  padding: 24px;
  box-shadow: var(--card-shadow);
  border: var(--card-border);
}

.tech-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 16px;
}

.tech-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 16px;
  background: var(--hero-bg-start);
  border-radius: 8px;
  border: 1px solid var(--border-color);
}

.tech-icon {
  font-size: 1.5rem;
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: var(--card-background);
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.tech-info h3 {
  font-size: 0.95rem;
  font-weight: 600;
  color: var(--text-color);
  margin: 0 0 2px 0;
}

.tech-info p {
  font-size: 0.85rem;
  color: var(--text-secondary);
  margin: 0;
}

/* 版本資訊 */
.version-info {
  background: var(--card-background);
  border-radius: var(--card-border-radius);
  padding: 24px;
  box-shadow: var(--card-shadow);
  border: var(--card-border);
}

.version-card {
  padding: 16px;
  background: var(--hero-bg-start);
  border-radius: 8px;
  border: 1px solid var(--border-color);
}

.version-header {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 8px;
}

.version-number {
  font-size: 1.2rem;
  font-weight: 600;
  color: var(--primary-color);
}

.version-status {
  padding: 4px 8px;
  background: var(--primary-color);
  color: white;
  border-radius: 8px;
  font-size: 0.7rem;
  font-weight: 600;
}

.version-date {
  font-size: 0.9rem;
  color: var(--text-secondary);
  margin: 0 0 16px 0;
}

.feature-list {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
  gap: 8px;
}

.feature-item {
  font-size: 0.9rem;
  color: var(--text-color);
  padding: 4px 0;
}

/* 開發計劃 */
.roadmap {
  background: var(--card-background);
  border-radius: var(--card-border-radius);
  padding: 24px;
  box-shadow: var(--card-shadow);
  border: var(--card-border);
}

.roadmap-list {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.roadmap-item {
  padding: 16px;
  background: var(--hero-bg-start);
  border-radius: 8px;
  border: 1px solid var(--border-color);
}

.roadmap-version {
  font-size: 1rem;
  font-weight: 600;
  color: var(--primary-color);
  margin-bottom: 8px;
}

.roadmap-features {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}

.roadmap-features span {
  font-size: 0.85rem;
  color: var(--text-secondary);
  background: var(--tag-background);
  padding: 4px 8px;
  border-radius: 12px;
}

/* 響應式設計 */
@media (max-width: 768px) {
  .site-info-container {
    padding: 20px 16px;
  }
  
  .tech-stack,
  .version-info,
  .roadmap {
    padding: 20px;
  }
  
  .tech-grid {
    grid-template-columns: 1fr;
  }
  
  .feature-list {
    grid-template-columns: 1fr;
  }
  
  .roadmap-features {
    flex-direction: column;
    gap: 4px;
  }
}
</style>