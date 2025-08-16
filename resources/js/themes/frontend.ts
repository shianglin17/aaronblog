/**
 * 前台主題配置
 * 溫暖棕色系設計，適合部落格閱讀體驗
 */

export const frontendThemeOverrides = {
  common: {
    primaryColor: '#8b4513',
    primaryColorHover: '#d2691e',
    primaryColorPressed: '#6b3410',
    borderRadius: '8px',
    fontWeight: '500',
    textColor1: '#2d1810',
    textColor2: '#5a4037',
    borderColor: '#efebe6'
  },
  Card: {
    borderRadius: '12px',
    color: '#ffffff'
  },
  Button: {
    textColor: '#5a4037',
    borderRadius: '8px',
    colorHover: '#fdf8f3'
  },
  Input: {
    borderRadius: '8px',
    borderHover: '#d2691e'
  }
}

export const frontendCSSVariables = `
/* 前台專用 CSS 變數 - 溫暖棕色系設計系統 */
:root[data-theme="frontend"] {
  /* 基礎色彩 */
  --background-color: #fefdfb;
  --text-color: #2d1810;
  --text-secondary: #5a4037;
  --text-tertiary: #8d6e63;
  --border-color: #efebe6;
  
  /* 品牌色彩系統 */
  --brand-primary: #8b4513;
  --brand-secondary: #d2691e;
  --brand-tertiary: #daa520;
  --brand-gradient: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-secondary) 60%, var(--brand-tertiary) 100%);
  --brand-light: #fdf8f3;
  
  /* 語義色彩 */
  --success-color: #38a169;
  --success-light: #f0fff4;
  --warning-color: #ed8936;
  --warning-light: #fffaf0;
  --error-color: #e53e3e;
  --error-light: #fed7d7;
  --info-color: #3182ce;
  --info-light: #ebf8ff;
  
  /* 表面層次系統 */
  --surface-color: #ffffff;
  --surface-elevated: #ffffff;
  --surface-hover: #fdf8f3;
  --surface-active: #f9f2ec;
  --surface-secondary: #faf7f2;
  --surface-tertiary: #f3ede5;
  
  /* 陰影系統 - 溫暖色調 */
  --shadow-xs: 0 1px 2px 0 rgba(139, 69, 19, 0.08);
  --shadow-sm: 0 1px 3px 0 rgba(139, 69, 19, 0.12), 0 1px 2px 0 rgba(139, 69, 19, 0.08);
  --shadow-md: 0 4px 6px -1px rgba(139, 69, 19, 0.12), 0 2px 4px -1px rgba(139, 69, 19, 0.08);
  --shadow-lg: 0 10px 15px -3px rgba(139, 69, 19, 0.12), 0 4px 6px -2px rgba(139, 69, 19, 0.06);
  --shadow-xl: 0 20px 25px -5px rgba(139, 69, 19, 0.12), 0 10px 10px -5px rgba(139, 69, 19, 0.06);
  
  /* 互動狀態 */
  --hover-opacity: 0.8;
  --active-scale: 0.95;
  --transition-fast: 0.15s ease-out;
  --transition-normal: 0.2s ease-out;
  --transition-slow: 0.3s ease-out;
  
  /* Hero 區域變數 */
  --hero-bg-start: #fdfcfb;
  --hero-bg-end: #f5f3f0;
  --hero-border-radius: 16px;
  --hero-padding: 64px 40px;
  --hero-margin-bottom: 48px;
  --hero-shadow: 0 8px 32px rgba(125, 110, 93, 0.1);
  
  /* 標題樣式變數 */
  --title-font-size: 2.5rem;
  --title-font-weight: 500;
  --title-margin-bottom: 16px;
  --title-letter-spacing: 0.5px;
  --title-color: var(--text-color);
  --title-text-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
  
  /* 描述文字變數 */
  --description-font-size: 1.125rem;
  --description-color: var(--text-secondary);
  --description-font-weight: 400;
  --description-letter-spacing: 0.3px;
  --description-max-width: 600px;
  --description-line-height: 1.7;
  
  /* 文章卡片變數 */
  --card-background: #ffffff;
  --card-border-radius: 12px;
  --card-padding: 24px;
  --card-gap: 24px;
  --card-shadow: 0 2px 16px rgba(125, 110, 93, 0.08);
  --card-shadow-hover: 0 8px 32px rgba(125, 110, 93, 0.15);
  --card-border: 1px solid rgba(125, 110, 93, 0.1);
  --card-transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  
  /* 標籤變數 */
  --tag-background: rgba(125, 110, 93, 0.08);
  --tag-color: var(--brand-primary);
  --tag-hover-background: var(--brand-primary);
  --tag-hover-color: #ffffff;
  --tag-border-radius: 16px;
  --tag-padding: 4px 12px;
  
  /* 搜尋器變數 */
  --search-background: linear-gradient(135deg, #ffffff 0%, #fafafa 100%);
  --search-border: 2px solid #e8e5e0;
  --search-border-radius: 16px;
  --search-shadow: 0 2px 8px rgba(0, 0, 0, 0.04), 0 1px 2px rgba(0, 0, 0, 0.08);
  --search-shadow-focus: 0 8px 32px rgba(125, 110, 93, 0.15), 0 2px 8px rgba(125, 110, 93, 0.1), 0 0 0 4px rgba(125, 110, 93, 0.05);
  --search-input-height: 48px;
  --search-padding: 6px 20px;
}
`