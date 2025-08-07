import { ref } from 'vue'

/**
 * 分頁邏輯工具
 * 提供基本的分頁狀態管理和處理函數
 */
export function usePagination(initialPageSize = 10) {
  // 分頁狀態
  const currentPage = ref(1)
  const pageSize = ref(initialPageSize)
  
  /**
   * 處理頁碼變更
   */
  function handlePageChange(page: number) {
    currentPage.value = page
  }
  
  /**
   * 處理每頁數量變更
   */
  function handlePageSizeChange(size: number) {
    pageSize.value = size
    currentPage.value = 1 // 變更每頁數量時重置到第一頁
  }
  
  /**
   * 重置到第一頁（通常在搜尋或篩選時使用）
   */
  function resetToFirstPage() {
    currentPage.value = 1
  }
  
  /**
   * 計算分頁後的資料
   */
  function paginateData<T>(data: T[]): T[] {
    const start = (currentPage.value - 1) * pageSize.value
    const end = start + pageSize.value
    return data.slice(start, end)
  }
  
  /**
   * 生成分頁資訊物件
   */
  function createPaginationInfo(totalItems: number) {
    return {
      currentPage: currentPage.value,
      perPage: pageSize.value,
      totalItems
    }
  }
  
  return {
    // 狀態
    currentPage,
    pageSize,
    
    // 處理函數
    handlePageChange,
    handlePageSizeChange,
    resetToFirstPage,
    
    // 工具函數
    paginateData,
    createPaginationInfo
  }
}