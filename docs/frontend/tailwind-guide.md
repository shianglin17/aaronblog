# Tailwind CSS 指南

## 基礎概念

### 1. 間距（Spacing）
間距系統是基於 0.25rem（4px）的倍數：

```html
<!-- 邊距 -->
m-{size}    <!-- margin -->
p-{size}    <!-- padding -->

<!-- 方向 -->
t  --> top（上）
r  --> right（右）
b  --> bottom（下）
l  --> left（左）
x  --> 左右
y  --> 上下

<!-- 常用尺寸 -->
0   --> 0px
0.5 --> 0.125rem (2px)
1   --> 0.25rem  (4px)
2   --> 0.5rem   (8px)
4   --> 1rem     (16px)
8   --> 2rem     (32px)
```

### 2. 佈局（Layout）

#### Flexbox
```html
<div class="
  flex              <!-- display: flex -->
  flex-row         <!-- 水平排列 -->
  flex-col         <!-- 垂直排列 -->
  justify-center   <!-- 主軸置中 -->
  items-center     <!-- 交叉軸置中 -->
  gap-4            <!-- 元素間距 -->
">
```

#### Grid
```html
<div class="
  grid                    <!-- display: grid -->
  grid-cols-3            <!-- 三欄 -->
  gap-4                  <!-- 格線間距 -->
  md:grid-cols-4         <!-- 中等螢幕時四欄 -->
">
```

### 3. 響應式設計（Responsive Design）
```html
<!-- 斷點 -->
sm:  <!-- 640px  -->
md:  <!-- 768px  -->
lg:  <!-- 1024px -->
xl:  <!-- 1280px -->
2xl: <!-- 1536px -->
```

### 4. 顏色系統
```html
<!-- 色階 -->
50  --> 最淺
100-900 --> 由淺到深

<!-- 範例 -->
bg-blue-500    <!-- 背景色 -->
text-gray-700  <!-- 文字顏色 -->
```

### 5. 常用組合

#### 卡片元件
```html
<div class="
  bg-white 
  rounded-lg 
  shadow-md 
  p-6 
  hover:shadow-lg 
  transition-shadow
">
```

#### 按鈕樣式
```html
<button class="
  px-4 
  py-2 
  bg-blue-500 
  text-white 
  rounded 
  hover:bg-blue-600 
  transition-colors
">
```

#### 響應式容器
```html
<div class="
  w-full
  max-w-4xl
  mx-auto
  px-4
  md:px-6
  lg:px-8
">
```

## 最佳實踐

### 1. 保持一致性
- 為常用的組件建立固定的類別組合
- 使用相同的間距和顏色系統

### 2. 避免過度使用
- 對於重複使用的樣式，考慮提取為組件
- 使用 @apply 指令組合常用類別

### 3. 響應式設計
- 從小螢幕開始設計（Mobile First）
- 使用斷點前綴來適應不同螢幕尺寸 