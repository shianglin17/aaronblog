/**
 * 日期相關工具函數
 */

// 台北時區的偏移量為 UTC+8
const TAIPEI_TIMEZONE_OFFSET = 8 * 60 * 60 * 1000;

/**
 * 將日期轉換為台北時區 (UTC+8)
 * @param date - 日期物件
 * @returns 台北時區的日期物件
 */
function convertToTaipeiTime(date: Date): Date {
  // 創建一個新的日期物件，避免修改原始物件
  const utcTime = date.getTime() + (date.getTimezoneOffset() * 60 * 1000);
  return new Date(utcTime + TAIPEI_TIMEZONE_OFFSET);
}

/**
 * 格式化日期時間為台北時區的本地化字符串
 * @param dateString - ISO格式的日期字符串
 * @returns 格式化後的日期時間字符串 (YYYY 年 MM 月 DD 日 HH:MM)
 */
export function formatDateTime(dateString: string): string {
  const date = convertToTaipeiTime(new Date(dateString));
  const hours = date.getHours().toString().padStart(2, '0');
  const minutes = date.getMinutes().toString().padStart(2, '0');
  
  return `${date.getFullYear()} 年 ${date.getMonth() + 1} 月 ${date.getDate()} 日 ${hours}:${minutes}`;
}
