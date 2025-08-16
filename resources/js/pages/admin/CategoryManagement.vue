<template>
  <n-message-provider>
      <div class="page-header">
        <h1>分類管理</h1>
        
        <!-- 篩選器和操作按鈕 -->
        <FilterBar
          v-model:search="searchQuery"
          @search="handleSearch"
        >
          <template #suffix>
            <n-button type="primary" @click="openCreateForm">新增分類</n-button>
          </template>
        </FilterBar>
        
        <!-- 數據表格 -->
        <DataTable
          :columns="CATEGORY_COLUMNS"
          :data="categories"
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
        :rules="CATEGORY_FORM_RULES"
        :submit-text="isEdit ? '保存' : '新增'"
        @submit="handleFormSubmit"
      >
        <n-form-item label="名稱" path="name">
          <n-input v-model:value="formModel.name" placeholder="請輸入分類名稱" />
        </n-form-item>
        
        <n-form-item label="Slug" path="slug">
          <n-input v-model:value="formModel.slug" placeholder="請輸入Slug" />
        </n-form-item>
        
        <n-form-item label="描述" path="description">
          <n-input v-model:value="formModel.description" type="textarea" placeholder="請輸入分類描述" />
        </n-form-item>
      </FormModal>
      
      <!-- 增強版刪除確認對話框 -->
      <ConfirmDialog
        v-model:show="showDeleteConfirm"
        title="確認刪除分類"
        message="此操作將永久刪除選中的分類，且無法復原。"
        item-type="分類"
        :item-info="deletingCategory"
        confirm-text="確認刪除"
        confirm-type="error"
        delete-mode="strict"
        @confirm="handleDelete"
        @cancel="cancelDelete"
      />
  </n-message-provider>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useMessage } from 'naive-ui';
import DataTable from '../../components/admin/DataTable.vue';
import FilterBar from '../../components/admin/FilterBar.vue';
import FormModal from '../../components/admin/FormModal.vue';
import ConfirmDialog from '../../components/admin/ConfirmDialog.vue';
import { CATEGORY_COLUMNS, CATEGORY_FORM_RULES } from '../../constants';
import { useCategories, useCategoryForm, useCategoryDelete } from '../../composables/useCategory';

// 消息提示
const message = useMessage();

// 搜索值
const searchQuery = ref('');

// 使用分類列表管理邏輯
const {
  categories,
  loading,
  pagination,
  fetchCategories,
  handleSearch,
  handlePageChange,
  handlePageSizeChange,
  handleSorterChange
} = useCategories(message);

// 使用分類表單邏輯
const {
  formModel,
  showForm,
  isEdit,
  formTitle,
  openCreateForm,
  openEditForm,
  handleFormSubmit
} = useCategoryForm(message, fetchCategories);

// 使用分類刪除邏輯
const {
  showDeleteConfirm,
  deletingCategory,
  openDeleteConfirm,
  cancelDelete,
  handleDelete
} = useCategoryDelete(message, fetchCategories);

// 初始化
onMounted(() => {
  fetchCategories();
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