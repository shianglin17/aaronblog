<template>
  <aside class="profile-sidebar">
    <div class="profile-card">
      <!-- 個人頭像和基本資訊 -->
      <div class="profile-header">
        <div class="avatar-container">
          <img 
            :src="profileData.avatar" 
            :alt="`${profileData.name} 的頭像`" 
            class="profile-avatar"
          />
          <div class="avatar-badge">
            <n-icon size="16"><CodeOutline /></n-icon>
          </div>
        </div>
        <h1 class="profile-name">{{ profileData.name }}</h1>
        <p class="profile-title">{{ profileData.title }}</p>
      </div>

      <!-- 個人簡介 -->
      <div class="profile-bio">
        <p>{{ profileData.bio }}</p>
      </div>

      <!-- 技能標籤 -->
      <div class="profile-skills">
        <h3 class="skills-title">技術專長</h3>
        <div class="skills-tags">
          <span 
            v-for="skill in profileData.skills" 
            :key="skill" 
            class="skill-tag"
          >
            {{ skill }}
          </span>
        </div>
      </div>

      <!-- 統計資訊 -->
      <div class="profile-stats">
        <div class="stat-item">
          <span class="stat-number">{{ stats.totalArticles }}</span>
          <span class="stat-label">文章</span>
        </div>
        <div class="stat-divider"></div>
        <div class="stat-item">
          <span class="stat-number">{{ stats.totalCategories }}</span>
          <span class="stat-label">分類</span>
        </div>
        <div class="stat-divider"></div>
        <div class="stat-item">
          <span class="stat-number">{{ stats.totalTags }}</span>
          <span class="stat-label">標籤</span>
        </div>
      </div>
    </div>
  </aside>
</template>

<script setup lang="ts">
import { CodeOutline } from '@vicons/ionicons5';

// 定義 props 介面
interface ProfileData {
  name: string;
  title: string;
  bio: string;
  avatar: string;
  skills: string[];
}

interface ProfileStats {
  totalArticles: number;
  totalCategories: number;
  totalTags: number;
}

// Props
withDefaults(defineProps<{
  profileData?: ProfileData;
  stats: ProfileStats;
}>(), {
  profileData: () => ({
    name: 'Aaron Lei',
    title: '全端工程師',
    bio: '專注於軟體開發和技術分享，熱愛探索新技術並將經驗記錄下來。歡迎與我交流討論！',
    avatar: 'https://picsum.photos/120/120?random=aaron',
    skills: ['Laravel', 'MySQL', 'Vue.js', 'TypeScript', 'Docker']
  })
});
</script>

<style scoped>
/* 左側個人檔案 */
.profile-sidebar {
  position: -webkit-sticky;
  position: sticky;
  top: calc(80px + 2rem); /* 導航高度 + 間距 */
  align-self: flex-start;
  max-height: calc(100vh - 120px); /* 防止過高內容 */
  overflow-y: auto;
}

.profile-card {
  background: var(--surface-elevated);
  border-radius: 16px;
  padding: 28px;
  box-shadow: var(--shadow-lg);
  border: 1px solid var(--border-color);
  position: relative;
  overflow: hidden;
  transition: var(--transition-normal);
}

.profile-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 24px;
  right: 24px;
  height: 2px;
  background: var(--brand-primary);
  border-radius: 1px;
  opacity: 0.3;
}

/* 個人資料頭部 */
.profile-header {
  text-align: center;
  margin-bottom: 24px;
}

.avatar-container {
  position: relative;
  display: inline-block;
  margin-bottom: 16px;
}

.profile-avatar {
  width: 120px;
  height: 120px;
  border-radius: 50%;
  object-fit: cover;
  border: 4px solid white;
  box-shadow: var(--shadow-lg);
  transition: var(--transition-normal);
}

.profile-avatar:hover {
  transform: scale(1.05);
  box-shadow: var(--shadow-xl);
}

.avatar-badge {
  position: absolute;
  bottom: 8px;
  right: 8px;
  width: 36px;
  height: 36px;
  background: var(--brand-gradient);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  border: 3px solid white;
  box-shadow: var(--shadow-md);
  transition: var(--transition-normal);
}

.avatar-badge:hover {
  transform: scale(1.1);
}

.profile-name {
  font-size: 1.5rem;
  font-weight: 600;
  color: var(--text-color);
  margin-bottom: 4px;
}

.profile-title {
  font-size: 1rem;
  background: var(--brand-gradient);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  font-weight: 600;
}

/* 個人簡介 */
.profile-bio {
  margin-bottom: 24px;
  padding: 16px 0;
  border-bottom: 1px solid var(--border-color);
}

.profile-bio p {
  font-size: 0.9rem;
  line-height: 1.6;
  color: var(--text-secondary);
  margin: 0;
}

/* 技能標籤 */
.profile-skills {
  margin-bottom: 24px;
}

.skills-title {
  font-size: 0.85rem;
  font-weight: 600;
  color: var(--text-color);
  margin-bottom: 12px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.skills-tags {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}

.skill-tag {
  background: var(--brand-light);
  color: var(--brand-primary);
  padding: 6px 14px;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 600;
  border: 1px solid rgba(139, 69, 19, 0.2);
  transition: var(--transition-normal);
  cursor: pointer;
}

.skill-tag:hover {
  background: var(--brand-gradient);
  color: white;
  border-color: transparent;
  transform: translateY(-1px);
  box-shadow: var(--shadow-sm);
}

/* 統計資訊 */
.profile-stats {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 16px 0;
  border-top: 1px solid var(--border-color);
}

.stat-item {
  text-align: center;
  flex: 1;
}

.stat-number {
  display: block;
  font-size: 1.5rem;
  font-weight: 800;
  background: var(--brand-gradient);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  line-height: 1;
  margin-bottom: 4px;
}

.stat-label {
  font-size: 0.75rem;
  color: var(--text-secondary);
  text-transform: uppercase;
  letter-spacing: 0.5px;
  font-weight: 500;
}

.stat-divider {
  width: 1px;
  height: 24px;
  background: var(--border-color);
  margin: 0 16px;
}

/* 響應式設計 */
@media (max-width: 1024px) {
  .profile-card {
    padding: 20px;
  }
  
  .profile-avatar {
    width: 100px;
    height: 100px;
  }
}

@media (max-width: 768px) {
  .profile-sidebar {
    position: static;
    top: auto;
  }
  
  .profile-card {
    margin-bottom: 24px;
  }
  
  .profile-avatar {
    width: 80px;
    height: 80px;
  }
  
  .profile-name {
    font-size: 1.25rem;
  }
  
  .stat-number {
    font-size: 1.1rem;
  }
}
</style>