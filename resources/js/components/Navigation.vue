<template>
  <nav class="navigation" :class="{ 'is-scrolled': isScrolled }">
    <div class="nav-container">
      <!-- Logo/Brand -->
      <div class="nav-brand">
        <router-link to="/" class="brand-link">
          <span class="brand-text">Aaron's Blog</span>
        </router-link>
      </div>

      <!-- Desktop Navigation -->
      <div class="nav-menu" :class="{ 'is-active': isMobileMenuOpen }">
        <router-link
          v-for="item in navigationItems"
          :key="item.name"
          :to="item.path"
          class="nav-link"
          :class="{ 'is-active': $route.path === item.path }"
          @click="closeMobileMenu"
        >
          <n-icon :size="16" class="nav-icon">
            <component :is="item.icon" />
          </n-icon>
          <span class="nav-text">{{ item.name }}</span>
        </router-link>
      </div>

      <!-- Mobile Menu Toggle -->
      <button
        class="mobile-menu-toggle"
        @click="toggleMobileMenu"
        :class="{ 'is-active': isMobileMenuOpen }"
      >
        <span class="hamburger-line"></span>
        <span class="hamburger-line"></span>
        <span class="hamburger-line"></span>
      </button>
    </div>

    <!-- Mobile Menu Overlay -->
    <div v-if="isMobileMenuOpen" class="mobile-overlay" @click="closeMobileMenu"></div>
  </nav>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue';
import { 
  HomeOutline, 
  PersonOutline, 
  GitBranchOutline
} from '@vicons/ionicons5';

// 導航項目
const navigationItems = [
  {
    name: '文章首頁',
    path: '/',
    icon: HomeOutline
  },
  {
    name: '關於我',
    path: '/about',
    icon: PersonOutline
  },
  {
    name: '關於此網站',
    path: '/site-info',
    icon: GitBranchOutline
  }
];

// 狀態管理
const isScrolled = ref(false);
const isMobileMenuOpen = ref(false);

// 滾動處理
const handleScroll = () => {
  isScrolled.value = window.scrollY > 20;
};

// 手機選單控制
const toggleMobileMenu = () => {
  isMobileMenuOpen.value = !isMobileMenuOpen.value;
  // 防止背景滾動
  document.body.style.overflow = isMobileMenuOpen.value ? 'hidden' : '';
};

const closeMobileMenu = () => {
  isMobileMenuOpen.value = false;
  document.body.style.overflow = '';
};

// 生命週期
onMounted(() => {
  window.addEventListener('scroll', handleScroll);
  handleScroll(); // 初始檢查
});

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll);
  document.body.style.overflow = '';
});
</script>

<style scoped>
.navigation {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  z-index: 1000;
  background: var(--nav-background);
  backdrop-filter: var(--nav-backdrop-filter);
  border-bottom: var(--nav-border);
  transition: var(--nav-transition);
}

.navigation.is-scrolled {
  background: var(--nav-scrolled-background);
  box-shadow: var(--nav-scrolled-shadow);
}

.nav-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  height: var(--nav-height);
}

/* Brand */
.nav-brand {
  flex-shrink: 0;
}

.brand-link {
  display: flex;
  align-items: center;
  text-decoration: none;
  color: var(--nav-brand-color);
  font-weight: 600;
  font-size: 1.25rem;
  transition: var(--nav-transition);
}

.brand-link:hover {
  color: var(--primary-color);
}

.brand-text {
  letter-spacing: 0.5px;
}

/* Desktop Navigation */
.nav-menu {
  display: flex;
  align-items: center;
  gap: 20px;
}

.nav-link {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 8px 16px;
  border-radius: 8px;
  text-decoration: none;
  color: var(--nav-link-color);
  font-weight: 500;
  font-size: 0.95rem;
  transition: var(--nav-transition);
  position: relative;
}

.nav-link:hover {
  color: var(--primary-color);
  background: var(--nav-link-hover-background);
}

.nav-link.is-active {
  color: var(--primary-color);
  background: var(--nav-link-active-background);
}

.nav-icon {
  opacity: 0.8;
}

.nav-text {
  font-size: 0.9rem;
}

/* Mobile Menu Toggle */
.mobile-menu-toggle {
  display: none;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  width: 40px;
  height: 40px;
  background: transparent;
  border: none;
  cursor: pointer;
  padding: 0;
  gap: 4px;
}

.hamburger-line {
  width: 20px;
  height: 2px;
  background: var(--nav-link-color);
  transition: all 0.3s ease;
  transform-origin: center;
}

.mobile-menu-toggle.is-active .hamburger-line:nth-child(1) {
  transform: rotate(45deg) translate(3px, 3px);
}

.mobile-menu-toggle.is-active .hamburger-line:nth-child(2) {
  opacity: 0;
}

.mobile-menu-toggle.is-active .hamburger-line:nth-child(3) {
  transform: rotate(-45deg) translate(3px, -3px);
}

.mobile-overlay {
  display: none;
}

/* 響應式設計 */
@media (max-width: 768px) {
  .nav-container {
    padding: 0 16px;
  }
  
  .mobile-menu-toggle {
    display: flex;
  }
  
  .nav-menu {
    position: fixed;
    top: var(--nav-height);
    right: 0;
    width: 280px;
    height: calc(100vh - var(--nav-height));
    background: var(--nav-mobile-background);
    backdrop-filter: var(--nav-backdrop-filter);
    border-left: var(--nav-border);
    flex-direction: column;
    align-items: stretch;
    gap: 0;
    padding: 24px 0;
    transform: translateX(100%);
    transition: transform 0.3s ease;
  }
  
  .nav-menu.is-active {
    transform: translateX(0);
  }
  
  .nav-link {
    padding: 16px 24px;
    border-radius: 0;
    justify-content: flex-start;
    gap: 12px;
  }
  
  .nav-link.is-active {
    background: var(--nav-link-active-background);
    border-right: 3px solid var(--primary-color);
  }
  
  .mobile-overlay {
    display: block;
    position: fixed;
    top: var(--nav-height);
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(4px);
  }
}

@media (max-width: 480px) {
  .nav-container {
    padding: 0 12px;
  }
  
  .nav-menu {
    width: 100%;
    left: 0;
    border-left: none;
    border-top: var(--nav-border);
  }
}
</style>