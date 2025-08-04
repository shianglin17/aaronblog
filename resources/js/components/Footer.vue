<template>
  <footer class="footer">
    <div class="footer-content">
      <!-- 社交媒體連結 -->
      <div class="social-media">
        <div class="social-links">
          <a 
            v-for="social in socialMediaList"
            :key="social.icon"
            :href="social.url"
            :aria-label="social.label"
            target="_blank"
            rel="noopener noreferrer"
            class="social-link"
          >
            <n-icon :size="18" class="social-icon">
              <component :is="getSocialIcon(social.icon)" />
            </n-icon>
          </a>
        </div>
      </div>
      
      <!-- 版權資訊 -->
      <div class="copyright">
        <p>© {{ currentYear }} Aaron 的個人部落格</p>
        <p class="version">{{ currentVersion }}</p>
      </div>
    </div>
  </footer>
</template>

<script setup lang="ts">
import { computed, ref, onMounted } from 'vue';
import { 
  LogoInstagram, 
  LogoLinkedin,
  ChatboxEllipses
} from '@vicons/ionicons5';
import { SOCIAL_MEDIA_LIST } from '../constants/socialMedia';

/**
 * 計算當前年份
 */
const currentYear = computed(() => new Date().getFullYear());

/**
 * 當前版本
 */
const currentVersion = ref('v1.0.0');

/**
 * 社交媒體清單
 */
const socialMediaList = SOCIAL_MEDIA_LIST;

/**
 * 根據圖示名稱取得對應的圖示元件
 * @param iconName - 圖示名稱
 * @returns 圖示元件
 */
function getSocialIcon(iconName: string) {
  const iconMap = {
    instagram: LogoInstagram,
    linkedin: LogoLinkedin,
    threads: ChatboxEllipses // 使用聊天泡泡圖示代表 Threads
  };
  
  return iconMap[iconName as keyof typeof iconMap] || ChatboxEllipses;
}

/**
 * 獲取最新版本資訊
 */
const fetchLatestVersion = async () => {
  try {
    const response = await fetch('https://api.github.com/repos/shianglin17/aaronblog/tags', {
      headers: {
        'Accept': 'application/vnd.github.v3+json',
        'User-Agent': 'AaronBlog-Frontend'
      }
    });

    if (response.ok) {
      const tags = await response.json();
      if (tags && tags.length > 0) {
        const latestTag = tags[0];
        currentVersion.value = latestTag.name;
      }
    }
  } catch (error) {
    console.warn('無法獲取版本資訊:', error);
  }
};

onMounted(() => {
  fetchLatestVersion();
});
</script>

<style scoped>
.footer {
  background-color: var(--background-color);
  padding: 18px 20px 12px;
  margin-top: auto;
  position: relative;
}

/* 水平線左右留白 */
.footer::before {
  content: '';
  position: absolute;
  top: 0;
  left: 20px;
  right: 20px;
  height: 1px;
  background-color: var(--border-color);
}

.footer-content {
  max-width: 860px;
  margin: 0 auto;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 12px;
}

.social-media {
  text-align: center;
}

.social-links {
  display: flex;
  gap: 18px;
  justify-content: center;
  align-items: center;
}

.social-link {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 36px;
  height: 36px;
  border-radius: 50%;
  transition: all 0.3s ease;
  color: var(--text-secondary);
  text-decoration: none;
}

.social-link:hover {
  color: var(--primary-color);
  background-color: rgba(125, 110, 93, 0.08);
  transform: translateY(-1px);
}

.social-icon {
  transition: transform 0.3s ease;
}

.social-link:hover .social-icon {
  transform: scale(1.05);
}

.copyright {
  text-align: center;
  color: var(--text-secondary);
  line-height: 1.4;
}

.copyright p {
  font-size: 0.85rem;
  margin-bottom: 2px;
}

.copyright .version {
  font-size: 0.75rem;
  opacity: 0.8;
}

/* 響應式設計 */
@media (max-width: 768px) {
  .footer {
    padding: 16px 16px 10px;
  }
  
  .footer::before {
    left: 16px;
    right: 16px;
  }
  
  .footer-content {
    gap: 10px;
  }
  
  .social-links {
    gap: 16px;
  }
  
  .social-link {
    width: 32px;
    height: 32px;
  }
  
  .copyright p {
    font-size: 0.8rem;
  }
  
  .copyright .version {
    font-size: 0.7rem;
  }
}

@media (max-width: 480px) {
  .footer {
    padding: 14px 12px 8px;
  }
  
  .footer::before {
    left: 12px;
    right: 12px;
  }
  
  .social-links {
    gap: 14px;
  }
  
  .social-link {
    width: 28px;
    height: 28px;
  }
  
  .copyright .version {
    font-size: 0.65rem;
  }
}
</style> 