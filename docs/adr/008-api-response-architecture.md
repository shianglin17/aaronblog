# ADR-008: API 回應與異常處理架構演進

## Status
Accepted - 2025年

## Context
原始架構使用 ResponseMaker 處理 API 回應，但隨著專案發展需要更簡潔統一的錯誤處理機制。個人部落格 API 需要一致的回應格式和清晰的異常處理流程。

**相關 Commit**: 重構系列 commit - "錯誤處理架構重構"

## Decision
採用 **ApiResponse + ApiException + 全域異常處理器** 的現代化架構。

**架構演進**:
- v1: ResponseMaker → 功能分散，不夠統一
- v2: Service Exception → 改善但回應格式不一致  
- v3: ApiResponse 架構 → 統一簡潔的最終方案

**選擇理由**:
- **統一回應**: ApiResponse 類別統一所有 API 回應格式
- **簡潔異常**: ApiException 基類 + 具體異常類別
- **自動處理**: 全域異常處理器自動轉換異常為 API 回應
- **類型安全**: 明確的異常類型對應明確的 HTTP 狀態碼

## Consequences

### ✅ 正面影響
- **程式碼簡化**: Controller 只需調用 Service，無需手動處理錯誤
- **格式統一**: 所有 API 回應格式完全一致
- **異常明確**: 具體異常類別提供清晰的錯誤資訊
- **維護容易**: 新增錯誤類型只需新增異常類別

### ❌ 負面影響
- **學習成本**: 開發者需要了解新的異常處理流程
- **調試變化**: 需要適應全域異常處理器的調試方式

### 📊 量化效果
- Controller 代碼減少: 每個方法減少 5-10 行錯誤處理
- 回應格式一致性: 100% 統一
- 異常類型: 覆蓋所有常見錯誤場景 (404, 409, 422, 403)

## Implementation

### 核心組件
```php
// 統一回應格式
ApiResponse::ok($data)
ApiResponse::paginated($paginator, $transformer)
ApiResponse::notFound()
ApiResponse::conflict($message)

// 異常處理
class ApiException extends Exception
class ResourceConflictException extends ApiException

// 全域處理器
match($e) {
    ModelNotFoundException => ApiResponse::notFound(),
    ApiException => $e->toResponse(),
    ValidationException => ApiResponse::validationError($e->errors())
}
```

## 相關決策
- [ADR-005](005-frontend-backend-separation.md) - 前後台分離的 API 規範基礎