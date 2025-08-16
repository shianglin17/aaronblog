<template>
  <n-message-provider>
      <div class="page-header">
        <h1>文章管理</h1>
        
        <!-- 篩選器和操作按鈕 -->
        <ArticleFilterBar
          v-model:search="searchQuery"
          v-model:status="params.status"
          v-model:category="categoryFilter"
          v-model:tags="tagFilters"
          :category-options="categoryOptions"
          :tag-options="tagOptions"
          @search="handleSearch"
          @status-change="handleStatusChange"
          @category-change="handleCategoryChange"
          @tags-change="handleTagsChange"
          @reset="resetFilters"
        >
          <template #suffix>
            <n-button type="primary" @click="openCreateForm">新增文章</n-button>
          </template>
        </ArticleFilterBar>
        
        <!-- 數據表格 -->
        <DataTable
          :columns="ARTICLE_COLUMNS"
          :data="articles"
          :loading="loading"
          :pagination="pagination"
          @page-change="handlePageChange"
          @page-size-change="handlePageSizeChange"
          @sorter-change="handleSorterChange"
          @edit="openEditForm"
          @delete="openDeleteConfirm"
        />
      </div>
      
      <!-- 表單模態窗 -->
      <FormModal
        v-model:show="showForm"
        v-model="formModel"
        :title="formTitle"
        :rules="ARTICLE_FORM_RULES"
        :submit-text="isEdit ? '保存' : '新增'"
        @submit="handleFormSubmit"
        width="700px"
      >
        <n-form-item label="標題" path="title">
          <n-input v-model:value="formModel.title" placeholder="請輸入文章標題" />
        </n-form-item>
        
        <n-form-item label="Slug" path="slug">
          <n-input v-model:value="formModel.slug" placeholder="請輸入Slug" />
        </n-form-item>
        
        <n-form-item label="描述" path="description">
          <n-input v-model:value="formModel.description" type="textarea" placeholder="請輸入文章描述" />
        </n-form-item>
        
        <n-form-item label="分類" path="category_id">
          <n-select
            v-model:value="formModel.category_id"
            :options="formCategoryOptions"
            placeholder="請選擇分類"
          />
        </n-form-item>
        
        <n-form-item label="標籤" path="tags">
          <n-select
            v-model:value="formModel.tags"
            :options="formTagOptions"
            placeholder="請選擇標籤"
            multiple
          />
        </n-form-item>
        
        <n-form-item label="狀態" path="status">
          <n-select
            v-model:value="formModel.status"
            :options="[
              { label: '已發布', value: 'published' },
              { label: '草稿', value: 'draft' }
            ]"
            placeholder="請選擇狀態"
          />
        </n-form-item>
        
        <n-form-item label="內容" path="content">
          <n-input v-model:value="formModel.content" type="textarea" placeholder="請輸入文章內容" rows="10" />
        </n-form-item>
      </FormModal>
      
      <!-- 刪除確認對話框 -->
      <n-modal v-model:show="showDeleteConfirm" preset="dialog" title="確認刪除" positive-text="確認" negative-text="取消" @positive-click="handleDelete" @negative-click="cancelDelete">
        <template #default>
          確定要刪除這篇文章嗎？
        </template>
      </n-modal>
  </n-message-provider>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useMessage } from 'naive-ui';
import DataTable from '../../components/admin/DataTable.vue';
import ArticleFilterBar from '../../components/admin/ArticleFilterBar.vue';
import FormModal from '../../components/admin/FormModal.vue';
import { ARTICLE_COLUMNS, ARTICLE_FORM_RULES } from '../../constants';
import { useAdminArticles, useAdminArticleForm, useAdminArticleDelete, useAdminOptions } from '../../composables/useAdminArticle';

// 消息提示
const message = useMessage();

// 使用管理後台文章列表管理邏輯
const {
  articles,
  loading,
  params,
  pagination,
  categoryOptions,
  tagOptions,
  searchQuery,
  categoryFilter,
  tagFilters,
  fetchArticles,
  loadFilterOptions,
  handleSearch,
  handleStatusChange,
  handleCategoryChange,
  handleTagsChange,
  handlePageChange,
  handlePageSizeChange,
  handleSorterChange,
  resetFilters
} = useAdminArticles(message);

// 使用管理後台文章表單邏輯
const {
  formModel,
  showForm,
  isEdit,
  formTitle,
  openCreateForm,
  openEditForm,
  handleFormSubmit
} = useAdminArticleForm(message, fetchArticles);

// 使用管理後台文章刪除邏輯
const {
  showDeleteConfirm,
  openDeleteConfirm,
  cancelDelete,
  handleDelete
} = useAdminArticleDelete(message, fetchArticles);

// 使用管理後台選項數據
const {
  categoryOptions: formCategoryOptions,
  tagOptions: formTagOptions,
  loadOptions
} = useAdminOptions(message);

// 初始化
onMounted(() => {
  // 同時載入文章和靜態資料（分類標籤）
  Promise.all([
    fetchArticles(),
    loadOptions()
  ]).catch(error => {
    console.error('Failed to initialize article management:', error);
  });
});
</script>

<style scoped>
.page-header {
  margin-bottom: 20px;
}

.page-header h1 {
  margin-top: 0;
  margin-bottom: 20px;
}
</style> 