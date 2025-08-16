/**
 * 主題配置索引文件
 * 統一導出前台和後台主題配置
 */

import { frontendThemeOverrides, frontendCSSVariables } from './frontend'
import { adminThemeOverrides, adminCSSVariables } from './admin'

export { frontendThemeOverrides, frontendCSSVariables, adminThemeOverrides, adminCSSVariables }

// 主題類型定義
export type ThemeType = 'frontend' | 'admin'

// 主題選擇器
export function getThemeConfig(themeType: ThemeType) {
  switch (themeType) {
    case 'frontend':
      return {
        themeOverrides: frontendThemeOverrides,
        cssVariables: frontendCSSVariables
      }
    case 'admin':
      return {
        themeOverrides: adminThemeOverrides,
        cssVariables: adminCSSVariables
      }
    default:
      throw new Error(`Unknown theme type: ${themeType}`)
  }
}

// 根據路由判斷主題類型
export function getThemeTypeFromRoute(path: string): ThemeType {
  return path.startsWith('/admin') ? 'admin' : 'frontend'
}