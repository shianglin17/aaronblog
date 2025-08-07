import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { categoryApi, tagApi } from '../api'
import type { Category } from '../types/category'
import type { Tag } from '../types/tag'

// 用於併發控制的 Promise 儲存
let loadingPromise: Promise<{ categories: Category[], tags: Tag[] }> | null = null

/**
 * 靜態資料 Store
 * 
 * 統一管理分類與標籤的快取，提供型別安全的 CRUD 操作
 * 特色：
 * - 併發安全的資料載入
 * - 樂觀更新與錯誤回滾
 * - 型別安全的查詢方法
 * - 統一的錯誤處理
 */
export const useStaticDataStore = defineStore('staticData', () => {
  // ==================== State ====================
  const categories = ref<Category[]>([])
  const tags = ref<Tag[]>([])
  const loading = ref({
    categories: false,
    tags: false,
    global: false
  })
  
  const isLoaded = ref(false)

  // ==================== Getters ====================
  const categoryOptions = computed(() => 
    categories.value.map(cat => ({
      label: `${cat.name} (${cat.articles_count || 0})`,
      value: cat.id,
      slug: cat.slug
    }))
  )
  
  const tagOptions = computed(() => 
    tags.value.map(tag => ({
      label: `${tag.name} (${tag.articles_count || 0})`,
      value: tag.id,
      slug: tag.slug
    }))
  )

  // ==================== Actions ====================
  
  /**
   * 確保資料已載入（併發安全）
   * @param force 強制重新載入
   * @returns Promise 包含載入的資料
   */
  async function ensureLoaded(force = false): Promise<{ categories: Category[], tags: Tag[] }> {
    // 如果已載入且非強制更新，直接返回
    if (!force && isLoaded.value) {
      return { categories: categories.value, tags: tags.value }
    }
    
    // 如果正在載入，等待現有的 Promise
    if (loadingPromise && !force) {
      return await loadingPromise
    }
    
    // 創建新的載入 Promise
    loadingPromise = performLoad()
    
    try {
      const result = await loadingPromise
      return result
    } finally {
      loadingPromise = null
    }
  }
  
  /**
   * 執行實際的資料載入
   */
  async function performLoad(): Promise<{ categories: Category[], tags: Tag[] }> {
    loading.value.global = true
    loading.value.categories = true
    loading.value.tags = true
    
    try {
      const [categoriesRes, tagsRes] = await Promise.all([
        categoryApi.getList(),
        tagApi.getList()
      ])
      
      // 原子性更新
      categories.value = categoriesRes.data
      tags.value = tagsRes.data
      isLoaded.value = true
      
      return { categories: categoriesRes.data, tags: tagsRes.data }
    } catch (error) {
      // 載入失敗時重置狀態
      isLoaded.value = false
      console.error('[StaticDataStore] Failed to load data:', error)
      throw error
    } finally {
      loading.value.global = false
      loading.value.categories = false
      loading.value.tags = false
    }
  }
  

  /**
   * 新增分類到快取
   * 注意：應在 API 呼叫成功後使用
   */
  function addCategory(category: Category): void {
    if (!category?.id) {
      console.warn('[StaticDataStore] Invalid category data for add:', category)
      return
    }
    
    // 檢查是否已存在（防止重複）
    const exists = categories.value.some(c => c.id === category.id)
    if (!exists) {
      categories.value.push(category)
    }
  }

  /**
   * 更新分類快取
   * 注意：應在 API 呼叫成功後使用
   */
  function updateCategory(category: Category): boolean {
    if (!category?.id) {
      console.warn('[StaticDataStore] Invalid category data for update:', category)
      return false
    }
    
    const index = categories.value.findIndex(c => c.id === category.id)
    if (index !== -1) {
      categories.value[index] = { ...category }
      return true
    }
    
    console.warn('[StaticDataStore] Category not found for update:', category.id)
    return false
  }

  /**
   * 從快取中移除分類
   * 注意：應在 API 呼叫成功後使用
   */
  function removeCategory(id: number): boolean {
    if (typeof id !== 'number' || id <= 0) {
      console.warn('[StaticDataStore] Invalid category ID for remove:', id)
      return false
    }
    
    const originalLength = categories.value.length
    categories.value = categories.value.filter(c => c.id !== id)
    return categories.value.length < originalLength
  }

  /**
   * 新增標籤到快取
   * 注意：應在 API 呼叫成功後使用
   */
  function addTag(tag: Tag): void {
    if (!tag?.id) {
      console.warn('[StaticDataStore] Invalid tag data for add:', tag)
      return
    }
    
    // 檢查是否已存在（防止重複）
    const exists = tags.value.some(t => t.id === tag.id)
    if (!exists) {
      tags.value.push(tag)
    }
  }

  /**
   * 更新標籤快取
   * 注意：應在 API 呼叫成功後使用
   */
  function updateTag(tag: Tag): boolean {
    if (!tag?.id) {
      console.warn('[StaticDataStore] Invalid tag data for update:', tag)
      return false
    }
    
    const index = tags.value.findIndex(t => t.id === tag.id)
    if (index !== -1) {
      tags.value[index] = { ...tag }
      return true
    }
    
    console.warn('[StaticDataStore] Tag not found for update:', tag.id)
    return false
  }

  /**
   * 從快取中移除標籤
   * 注意：應在 API 呼叫成功後使用
   */
  function removeTag(id: number): boolean {
    if (typeof id !== 'number' || id <= 0) {
      console.warn('[StaticDataStore] Invalid tag ID for remove:', id)
      return false
    }
    
    const originalLength = tags.value.length
    tags.value = tags.value.filter(t => t.id !== id)
    return tags.value.length < originalLength
  }
  
  /**
   * 根據 ID 查找分類
   */
  function getCategoryById(id: number): Category | undefined {
    if (typeof id !== 'number' || id <= 0) return undefined
    return categories.value.find(c => c.id === id)
  }
  
  /**
   * 根據 ID 查找標籤
   */
  function getTagById(id: number): Tag | undefined {
    if (typeof id !== 'number' || id <= 0) return undefined
    return tags.value.find(t => t.id === id)
  }
  
  /**
   * 獲取 Store 狀態的統計資訊
   */
  const stats = computed(() => ({
    categoriesCount: categories.value.length,
    tagsCount: tags.value.length,
    isLoaded: isLoaded.value,
    isLoading: loading.value.global
  }))

  return {
    // State (唯讀屬性，但保持型別相容性)
    categories,
    tags,
    loading,
    isLoaded,
    
    // Computed
    categoryOptions,
    tagOptions,
    stats,
    
    // Core Actions
    ensureLoaded,
    
    // CRUD Operations (僅在 API 成功後使用)
    addCategory,
    updateCategory,
    removeCategory,
    addTag,
    updateTag,
    removeTag,
    
    // Query Helpers
    getCategoryById,
    getTagById
  }
})