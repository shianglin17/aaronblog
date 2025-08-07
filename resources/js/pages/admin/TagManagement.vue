<template>
  <n-message-provider>
    <AdminLayout>
      <div class="page-header">
        <h1>標籤管理</h1>
        
        <!-- 篩選器和操作按鈕 -->
        <FilterBar
          v-model:search="searchQuery"
          @search="handleSearch"
        >
          <template #suffix>
            <n-button type="primary" @click="openCreateForm">新增標籤</n-button>
          </template>
        </FilterBar>
        
        <!-- 數據表格 -->
        <DataTable
          :columns="TAG_COLUMNS"
          :data="tags"
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
        :rules="TAG_FORM_RULES"
        :submit-text="isEdit ? '保存' : '新增'"
        @submit="handleFormSubmit"
      >
        <n-form-item label="名稱" path="name">
          <n-input v-model:value="formModel.name" placeholder="請輸入標籤名稱" />
        </n-form-item>
        
        <n-form-item label="Slug" path="slug">
          <n-input v-model:value="formModel.slug" placeholder="請輸入Slug" />
        </n-form-item>
      </FormModal>
      
      <!-- 增強版刪除確認對話框 -->
      <ConfirmDialog
        v-model:show="showDeleteConfirm"
        title="確認刪除標籤"
        message="此操作將永久刪除選中的標籤，且無法復原。"
        item-type="標籤"
        :item-info="deletingTag"
        confirm-text="確認刪除"
        confirm-type="error"
        delete-mode="loose"
        @confirm="handleDelete"
        @cancel="cancelDelete"
      />
    </AdminLayout>
  </n-message-provider>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useMessage } from 'naive-ui';
import AdminLayout from '../../components/admin/AdminLayout.vue';
import DataTable from '../../components/admin/DataTable.vue';
import FilterBar from '../../components/admin/FilterBar.vue';
import FormModal from '../../components/admin/FormModal.vue';
import ConfirmDialog from '../../components/admin/ConfirmDialog.vue';
import { TAG_COLUMNS, TAG_FORM_RULES } from '../../constants';
import { useTags, useTagForm, useTagDelete } from '../../composables/useTag';

// 消息提示
const message = useMessage();

// 搜索值
const searchQuery = ref('');

// 使用標籤列表管理邏輯
const {
  tags,
  loading,
  pagination,
  fetchTags,
  handleSearch,
  handlePageChange,
  handlePageSizeChange,
  handleSorterChange
} = useTags(message);

// 使用標籤表單邏輯
const {
  formModel,
  showForm,
  isEdit,
  formTitle,
  openCreateForm,
  openEditForm,
  handleFormSubmit
} = useTagForm(message, fetchTags);

// 使用標籤刪除邏輯
const {
  showDeleteConfirm,
  deletingTag,
  openDeleteConfirm,
  cancelDelete,
  handleDelete
} = useTagDelete(message, fetchTags);

// 初始化
onMounted(() => {
  fetchTags();
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