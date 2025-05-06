/**
 * 日期相關工具函數
 */

/**
 * 格式化日期為本地化字符串
 * @param dateString - ISO格式的日期字符串
 * @returns 格式化後的日期字符串 (YYYY 年 MM 月 DD 日)
 */
export function formatDate(dateString: string): string {
  const date = new Date(dateString);
  return `${date.getFullYear()} 年 ${date.getMonth() + 1} 月 ${date.getDate()} 日`;
}

/**
 * 格式化日期時間為本地化字符串
 * @param dateString - ISO格式的日期字符串
 * @returns 格式化後的日期時間字符串 (YYYY 年 MM 月 DD 日 HH:MM)
 */
export function formatDateTime(dateString: string): string {
  const date = new Date(dateString);
  const hours = date.getHours().toString().padStart(2, '0');
  const minutes = date.getMinutes().toString().padStart(2, '0');
  
  return `${date.getFullYear()} 年 ${date.getMonth() + 1} 月 ${date.getDate()} 日 ${hours}:${minutes}`;
} 