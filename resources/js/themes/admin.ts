/**
 * 後台主題配置
 * 專業灰藍色系設計，適合管理系統操作體驗
 */

export const adminThemeOverrides = {
  common: {
    primaryColor: '#1890ff',
    primaryColorHover: '#40a9ff',
    primaryColorPressed: '#096dd9',
    borderRadius: '6px',
    fontWeight: '400',
    textColor1: '#262626',
    textColor2: '#595959',
    borderColor: '#d9d9d9'
  },
  Card: {
    borderRadius: '8px',
    color: '#ffffff'
  },
  Button: {
    textColor: '#262626',
    borderRadius: '6px',
    colorHover: '#f5f5f5'
  },
  Input: {
    borderRadius: '6px',
    borderHover: '#40a9ff'
  },
  DataTable: {
    borderRadius: '8px',
    borderColor: '#f0f0f0'
  },
  Dialog: {
    borderRadius: '8px'
  },
  Modal: {
    borderRadius: '8px'
  }
}

export const adminCSSVariables = `
/* 後台專用 CSS 變數 - 專業灰藍色系設計系統 */
:root[data-theme="admin"] {
  /* 基礎色彩 - 專業管理系統色調 */
  --background-color: #f0f2f5;
  --text-color: #262626;
  --text-secondary: #595959;
  --text-tertiary: #8c8c8c;
  --border-color: #d9d9d9;
  
  /* 品牌色彩系統 - 專業藍色系 */
  --brand-primary: #1890ff;
  --brand-secondary: #40a9ff;
  --brand-tertiary: #69c0ff;
  --brand-gradient: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-secondary) 60%, var(--brand-tertiary) 100%);
  --brand-light: #f0f8ff;
  
  /* 語義色彩 - 管理系統標準 */
  --success-color: #52c41a;
  --success-light: #f6ffed;
  --warning-color: #faad14;
  --warning-light: #fffbe6;
  --error-color: #ff4d4f;
  --error-light: #fff2f0;
  --info-color: #1890ff;
  --info-light: #e6f7ff;
  
  /* 表面層次系統 - 管理界面層級 */
  --surface-color: #ffffff;
  --surface-elevated: #ffffff;
  --surface-hover: #fafafa;
  --surface-active: #f5f5f5;
  --surface-secondary: #fafafa;
  --surface-tertiary: #f0f0f0;
  
  /* 陰影系統 - 管理系統標準陰影 */
  --shadow-xs: 0 1px 2px 0 rgba(0, 0, 0, 0.03);
  --shadow-sm: 0 1px 3px 0 rgba(0, 0, 0, 0.05), 0 1px 2px 0 rgba(0, 0, 0, 0.1);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
  --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
  
  /* 互動狀態 - 管理系統標準 */
  --hover-opacity: 0.85;
  --active-scale: 0.98;
  --transition-fast: 0.1s ease-out;
  --transition-normal: 0.15s ease-out;
  --transition-slow: 0.2s ease-out;
  
  /* 管理界面特定變數 */
  --admin-header-bg: #ffffff;
  --admin-header-height: 64px;
  --admin-header-shadow: 0 1px 4px rgba(0, 0, 0, 0.08);
  --admin-content-bg: #f0f2f5;
  --admin-content-padding: 24px;
  
  /* 表格變數 */
  --table-header-bg: #fafafa;
  --table-border-color: #f0f0f0;
  --table-hover-bg: #fafafa;
  --table-selected-bg: #e6f7ff;
  
  /* 表單變數 */
  --form-label-color: #262626;
  --form-input-bg: #ffffff;
  --form-input-border: #d9d9d9;
  --form-input-focus-border: #40a9ff;
  
  /* 卡片變數 - 管理系統風格 */
  --card-background: #ffffff;
  --card-border-radius: 8px;
  --card-padding: 24px;
  --card-margin: 16px;
  --card-shadow: 0 1px 3px rgba(0, 0, 0, 0.12), 0 1px 2px rgba(0, 0, 0, 0.24);
  --card-border: 1px solid #f0f0f0;
  
  /* 按鈕變數 */
  --button-border-radius: 6px;
  --button-primary-bg: var(--brand-primary);
  --button-primary-hover: var(--brand-secondary);
  --button-default-bg: #ffffff;
  --button-default-border: #d9d9d9;
  
  /* 導航變數 */
  --nav-active-bg: #e6f7ff;
  --nav-active-color: var(--brand-primary);
  --nav-hover-bg: #f5f5f5;
  
  /* 狀態標籤變數 */
  --status-published-bg: #f6ffed;
  --status-published-color: #52c41a;
  --status-draft-bg: #fff7e6;
  --status-draft-color: #faad14;
  --status-archived-bg: #f5f5f5;
  --status-archived-color: #8c8c8c;
  
  /* RWD 斷點變數 */
  --breakpoint-xs: 480px;   /* 超小螢幕：手機直式 */
  --breakpoint-sm: 576px;   /* 小螢幕：手機橫式 */
  --breakpoint-md: 768px;   /* 中等螢幕：平板直式 */
  --breakpoint-lg: 992px;   /* 大螢幕：平板橫式/小筆電 */
  --breakpoint-xl: 1200px;  /* 超大螢幕：桌機 */
  --breakpoint-xxl: 1600px; /* 極大螢幕：大桌機 */
  
  /* 移動端特定變數 */
  --mobile-header-height: 56px;
  --mobile-padding: 12px;
  --mobile-content-padding: 16px;
  --mobile-card-padding: 16px;
  --mobile-font-size-sm: 0.75rem;
  --mobile-font-size-base: 0.875rem;
  --mobile-font-size-lg: 1rem;
  
  /* 平板特定變數 */
  --tablet-header-height: 60px;
  --tablet-padding: 16px;
  --tablet-content-padding: 20px;
  --tablet-card-padding: 20px;
  
  /* 響應式表格變數 */
  --table-min-width: 800px;
  --table-mobile-min-width: 320px;
  --table-scroll-width: 100%;
}
`