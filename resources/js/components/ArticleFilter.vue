<template>
  <div class="article-filter">
    <div class="filter-row">
      <n-select
        v-model:value="selectedCategory"
        :options="categoryOptions"
        placeholder="請選擇分類"
        clearable
        :disabled="categories.length === 0"
        class="filter-select"
      />
      <n-select
        v-model:value="selectedTags"
        :options="tagOptions"
        placeholder="請選擇標籤"
        multiple
        clearable
        :disabled="tags.length === 0"
        class="filter-select"
      />
      <div class="filter-actions">
        <n-button type="primary" size="small" @click="emitFilters">
          <template #icon>
            <n-icon><SearchOutline /></n-icon>
          </template>
          搜尋
        </n-button>
        <n-button size="small" @click="clearAllFilters">
          <template #icon>
            <n-icon><CloseOutline /></n-icon>
          </template>
          清除
        </n-button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, defineEmits, defineProps, watch } from 'vue';
import { SearchOutline, CloseOutline } from '@vicons/ionicons5';

interface Category {
  id: number;
  name: string;
  slug: string;
}

interface Tag {
  id: number;
  name: string;
  slug: string;
}

const props = defineProps<{
  categories: Category[];
  tags: Tag[];
}>();

const emit = defineEmits(['update:filters']);

const selectedCategory = ref<string | null>(null);
const selectedTags = ref<string[]>([]);

const categoryOptions = computed(() =>
  props.categories.map((cat) => ({
    label: cat.name,
    value: cat.slug
  }))
);

const tagOptions = computed(() =>
  props.tags.map((tag) => ({
    label: tag.name,
    value: tag.slug
  }))
);

// 清除所有篩選
const clearAllFilters = () => {
  selectedCategory.value = null;
  selectedTags.value = [];
  emitFilters();
};

// 觸發篩選變更事件
const emitFilters = () => {
  emit('update:filters', {
    category: selectedCategory.value || undefined,
    tags: selectedTags.value.length > 0 ? selectedTags.value : undefined
  });
};

// 若外部資料有變動，重置選擇（避免資料不同步）
watch(
  () => [props.categories, props.tags],
  () => {
    selectedCategory.value = null;
    selectedTags.value = [];
  }
);
</script>

<style scoped>
.article-filter {
  padding: 12px 16px 8px 16px;
  background-color: var(--background-color-light, #f5f5f5);
  border-radius: 8px;
  margin-bottom: 24px;
}

.filter-row {
  display: flex;
  align-items: center;
  gap: 16px;
  flex-wrap: wrap;
}

.filter-select {
  flex: 1 1 0;
  min-width: 120px;
  max-width: 320px;
}

.filter-actions {
  display: flex;
  gap: 8px;
  margin-left: auto;
}

@media (max-width: 600px) {
  .filter-row {
    flex-direction: column;
    align-items: stretch;
    gap: 10px;
  }
  .filter-actions {
    margin-left: 0;
    justify-content: flex-start;
  }
}
</style> 