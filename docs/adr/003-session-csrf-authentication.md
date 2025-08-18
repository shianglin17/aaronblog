# ADR-003: Session Cookie + CSRF Token 認證機制

## Status
Accepted - 2025年8月17日

## Context

部落格系統需要實作安全的管理員認證機制。在選擇認證方案時，需要考慮安全性、簡單性和前端整合的便利性。

### 技術演進背景

**原有方案：Laravel Sanctum API Bearer Token**
- 使用 Personal Access Token 進行 API 認證
- 前端需要手動管理 Token 存儲和傳送
- 適合純 API 後端架構

**新方案：Laravel Sanctum Session Cookie + CSRF**
- 利用 Laravel 傳統 Session 認證機制
- 瀏覽器自動處理 Cookie 管理
- 內建 CSRF 保護機制

### 考慮的方案
1. **維持 API Bearer Token**: 保持現有的 Token 認證方式
2. **Session Cookie + CSRF**: 轉向 Session 為基礎的認證

### 專案需求
- **單體應用**: 非分散式架構，不需要跨服務認證
- **安全性優先**: 管理後台需要高安全性保護
- **開發效率**: 利用 Laravel Sanctum 現有功能
- **XSS 防護**: 前端 SPA 需要 CSRF 保護

**相關 Commit**: `91294be` - "fix(auth): 解決登入登出流程中的 CSRF token 錯誤"

## Decision

採用 **Laravel Sanctum 的 Session Cookie + CSRF Token** 認證機制，替代原有的 API Bearer Token 方案。

### 決策對比分析

**API Bearer Token 的限制**
- 前端需要手動管理 Token 存儲（localStorage/sessionStorage）
- Token 刷新機制複雜，容易出現認證中斷
- 缺乏內建的 CSRF 保護
- 需要額外處理 Token 過期的用戶體驗

**Session Cookie + CSRF 的優勢**
1. **用戶體驗改善**:
   - 瀏覽器自動處理 Cookie 傳送和存儲
   - 無需前端手動管理認證狀態
   - 自動處理 Session 過期和續期

2. **安全性增強**:
   - HttpOnly Cookie 防止 XSS 攻擊
   - CSRF Token 防止跨站請求偽造
   - Session 與 IP 綁定，增強安全性

3. **開發簡化**:
   - Laravel Sanctum 原生支援，配置簡單
   - 與 Laravel 傳統認證機制完美整合
   - 中介軟體自動處理認證檢查

## Consequences

### 正面影響 ✅

- **用戶體驗顯著改善**: 消除了前端 Token 管理的複雜性
- **安全性提升**: CSRF 保護 + HttpOnly Cookie 雙重防護
- **開發效率**: 減少認證相關的前端邏輯代碼
- **維護簡化**: 利用 Laravel 成熟的 Session 管理機制
- **除錯便利**: 瀏覽器開發工具可直接查看 Cookie 狀態

### 負面影響與挑戰 ❌

- **架構限制**: 從無狀態 API 轉為有狀態認證
- **CORS 配置**: 需要正確設定 Sanctum Stateful Domains
- **Cookie 設定**: 生產環境需要正確的 Secure/SameSite 配置
- **除錯複雜化**: CSRF Token 同步問題的排查相對困難

### 實際遇到的問題與解決

#### 問題: CSRF Token 不同步
```
登入 → 登出 → 再登入 時出現 CSRF token 無效
```

#### 根因分析
1. 登出時調用 `regenerateToken()` 重新產生 CSRF token
2. 前端 SPA 未及時更新新的 token
3. 下次請求使用舊 token 導致驗證失敗

#### 解決方案
```php
// AuthController.php - 登出方法優化
public function logout(Request $request)
{
    // 清除 session 資料
    $request->session()->invalidate();
    
    // 注意：不重新產生 CSRF token，避免前端 SPA 中的 token 不同步問題
    // 用戶已登出且 session 已失效，重新產生 CSRF token 的安全價值很低
    
    return ApiResponse::ok();
}
```

## Implementation

### 認證流程設計

```javascript
// 前端認證流程
1. 獲取 CSRF Cookie: GET /sanctum/csrf-cookie
2. 登入請求: POST /api/auth/login (攜帶 CSRF token)
3. Session Cookie 自動設定
4. 後續 API 請求自動攜帶 Session Cookie + CSRF token
```

### Laravel 配置

```php
// config/sanctum.php
'stateful' => explode(',', env('SANCTUM_STATEFUL_DOMAINS', sprintf(
    '%s%s',
    'localhost,localhost:3000,127.0.0.1,127.0.0.1:8000,::1',
    env('APP_URL') ? ','.parse_url(env('APP_URL'), PHP_URL_HOST) : ''
))),

// Session 配置
'same_site' => 'lax',
'secure' => env('SESSION_SECURE_COOKIE', false),
'http_only' => true,
```

### 前端整合

```typescript
// authApi.ts - 簡化後的認證邏輯
export const authApi = {
  async login(credentials: LoginData) {
    // Laravel session()->regenerate() 已處理 CSRF token
    // 無需額外刷新 token
    const response = await http.post('/auth/login', credentials);
    return response.data;
  },
  
  async logout() {
    await http.post('/auth/logout');
    // Session 已失效，CSRF token 不需要重新獲取
  }
};
```

## 安全考量

### CSRF 保護機制
- 所有 POST/PUT/DELETE 請求必須攜帶 CSRF token
- Token 綁定到特定 Session，無法跨 Session 重用
- 自動檢查 Referer 和 Origin 標頭

### Session 安全配置
- HttpOnly: 防止 JavaScript 存取 Cookie
- SameSite=Lax: 防護 CSRF 攻擊
- Secure: 生產環境強制 HTTPS

## 效能特性

```bash
# 認證開銷比較
JWT 驗證：     需要解密和驗證簽名 (~5ms)
Session 驗證：  直接查詢 Session 存儲 (~1ms)

# 儲存空間
JWT Token：    ~200-500 bytes (包含 payload)
Session ID：   ~40 bytes (僅 session identifier)
```

## 相關決策

- 與 [ADR-001 SQLite 資料庫](001-sqlite-production-database.md) 搭配，Session 存儲在檔案中
- 支持 [ADR-005 前後台分離](005-frontend-backend-separation.md) 的安全認證需求