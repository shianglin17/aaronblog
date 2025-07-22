<template>
  <div class="about-container">
    <div class="about-content">
      <!-- Content Section -->
      <section class="about-main">
        <div class="content-card">
          <h2 class="section-title">
            <n-icon size="20" class="section-icon">
              <CodeOutline />
            </n-icon>
            關於我
          </h2>
          <div class="content-text">
            <p>
              我是 Aaron，一位熱愛技術的軟體開發者。專注於前端開發和全端技術，
              喜歡探索新技術並分享開發心得。
            </p>
            <p>
              這個部落格記錄了我在軟體開發路上的學習歷程、技術筆記，
              以及一些生活感悟。希望能透過分享，與更多開發者交流學習。
            </p>
          </div>
        </div>

        <div class="content-card">
          <h2 class="section-title">
            <n-icon size="20" class="section-icon">
              <BuildOutline />
            </n-icon>
            技術領域
          </h2>
          <div class="skills-grid">
            <div class="skill-item">
              <div class="skill-icon">🎨</div>
              <div class="skill-content">
                <h3>前端開發</h3>
                <p>Vue.js, React, TypeScript</p>
              </div>
            </div>
            <div class="skill-item">
              <div class="skill-icon">⚡</div>
              <div class="skill-content">
                <h3>後端開發</h3>
                <p>Laravel, Node.js, Python</p>
              </div>
            </div>
            <div class="skill-item">
              <div class="skill-icon">📱</div>
              <div class="skill-content">
                <h3>全端開發</h3>
                <p>系統架構, API 設計, 資料庫</p>
              </div>
            </div>
          </div>
        </div>

        <div class="content-card">
          <h2 class="section-title">
            <n-icon size="20" class="section-icon">
              <MailOutline />
            </n-icon>
            聯繫方式
          </h2>
          <div class="contact-grid">
            <a href="#" class="contact-item">
              <n-icon size="24" class="contact-icon">
                <LogoGithub />
              </n-icon>
              <span>GitHub</span>
            </a>
            <a href="#" class="contact-item">
              <n-icon size="24" class="contact-icon">
                <MailOutline />
              </n-icon>
              <span>Email</span>
            </a>
            <a href="#" class="contact-item">
              <n-icon size="24" class="contact-icon">
                <LogoLinkedin />
              </n-icon>
              <span>LinkedIn</span>
            </a>
          </div>
        </div>
      </section>
    </div>
  </div>
</template>

<script setup lang="ts">
import { 
  PersonOutline, 
  CodeOutline, 
  BuildOutline, 
  MailOutline,
  LogoGithub,
  LogoLinkedin
} from '@vicons/ionicons5';
</script>

<style scoped>
.about-container {
  max-width: 800px;
  margin: 0 auto;
  padding: 40px 20px;
}

.about-content {
  display: flex;
  flex-direction: column;
  gap: 40px;
}

/* Content Section */
.about-main {
  display: flex;
  flex-direction: column;
  gap: 32px;
}

.content-card {
  background: var(--card-background);
  border-radius: var(--card-border-radius);
  padding: var(--card-padding);
  box-shadow: var(--card-shadow);
  border: var(--card-border);
}

.section-title {
  display: flex;
  align-items: center;
  gap: 12px;
  font-size: 1.5rem;
  font-weight: 600;
  color: var(--text-color);
  margin-bottom: 24px;
}

.section-icon {
  color: var(--primary-color);
}

.content-text {
  line-height: 1.8;
  color: var(--text-color);
}

.content-text p {
  margin-bottom: 16px;
}

.content-text p:last-child {
  margin-bottom: 0;
}

/* Skills Grid */
.skills-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 20px;
}

.skill-item {
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 20px;
  background: var(--hero-bg-start);
  border-radius: 12px;
  border: 1px solid var(--border-color);
}

.skill-icon {
  font-size: 2rem;
  width: 48px;
  height: 48px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: var(--card-background);
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.skill-content h3 {
  font-size: 1.1rem;
  font-weight: 600;
  color: var(--text-color);
  margin: 0 0 4px 0;
}

.skill-content p {
  font-size: 0.9rem;
  color: var(--text-secondary);
  margin: 0;
}

/* Contact Grid */
.contact-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
  gap: 16px;
}

.contact-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 16px;
  background: var(--hero-bg-start);
  border-radius: 12px;
  border: 1px solid var(--border-color);
  text-decoration: none;
  color: var(--text-color);
  transition: var(--card-transition);
}

.contact-item:hover {
  background: var(--nav-link-hover-background);
  border-color: var(--primary-color);
  transform: translateY(-2px);
}

.contact-icon {
  color: var(--primary-color);
}

/* 響應式設計 */
@media (max-width: 768px) {
  .about-container {
    padding: 20px 16px;
  }
  
  .skills-grid {
    grid-template-columns: 1fr;
  }
  
  .skill-item {
    padding: 16px;
  }
  
  .contact-grid {
    grid-template-columns: 1fr;
  }
}
</style>