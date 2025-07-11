# 設計模式

## 概述

本專案採用多種設計模式提升可維護性、可擴展性與可測試性，所有模式均有實際應用於程式碼。

## 核心設計模式

### 1. Repository Pattern
- 將資料存取邏輯集中於 Repository，與業務邏輯分離。
- 例如：ArticleRepository、CategoryRepository、TagRepository

### 2. Service Layer Pattern
- 將業務邏輯封裝於 Service，控制器僅負責請求處理。
- 例如：ArticleService、CategoryService、TagService

### 3. Dependency Injection
- 透過建構子注入依賴，提升可測試性與鬆耦合。
- 例如：Controller 注入 Service，Service 注入 Repository

### 4. Factory Pattern
- 使用 Factory 產生測試資料與模型實例。
- 例如：ArticleFactory、CategoryFactory、TagFactory

### 5. Observer Pattern
- 監聽模型事件，自動處理快取、日誌等副作用。
- 例如：ArticleObserver、CategoryObserver、TagObserver

### 6. Resource Pattern
- 使用 Resource 轉換 API 輸出格式。
- 例如：ArticleResource、CategoryResource、TagResource

### 7. Middleware Pattern
- 使用 Middleware 處理請求前後的共用邏輯。
- 例如：API 驗證、速率限制

### 8. MVC 與分層架構
- 採用 Model-View-Controller 與分層架構，職責明確。
- 目錄結構：Models、Http/Controllers、Services、Repositories、Resources

## 設計原則
- 遵循 SOLID、DRY 原則
- 依賴抽象、單一職責、開放封閉、介面分離、依賴反轉

## 總結
- 這些設計模式與原則確保專案高內聚、低耦合、易於維護與擴展。 