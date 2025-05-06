# Laravel 認證流程詳解

## 概述

本文檔詳細解釋 Laravel Sanctum 認證系統的內部工作原理，特別是用戶登入時的資料流程和 sessions 表的使用方式。

## 認證流程圖

```
┌─────────────┐     ┌─────────────┐     ┌───────────────┐     ┌───────────────┐
│  客戶端     │     │ AuthController│     │  Auth::attempt │     │   SessionGuard │
│  (前端)     │────▶│   login()    │────▶│     (驗證)     │────▶│      login()    │
└─────────────┘     └─────────────┘     └───────────────┘     └───────┬─────────┘
                                                                       │
                                                                       ▼
┌─────────────┐     ┌─────────────┐     ┌───────────────┐     ┌───────────────┐
│  客戶端     │     │   Response   │     │ Session中間件  │     │ DatabaseSession│
│  (token)    │◀────│ (user+token) │◀────│    儲存        │◀────│     Handler    │
└─────────────┘     └─────────────┘     └───────────────┘     └───────────────┘
```

## 登入流程詳解

### 1. 入口點 - AuthController@login

當使用者提交登入表單時，請求首先到達 `AuthController@login` 方法：

```php
public function login(LoginRequest $request)
{
    $request->authenticate();  // 驗證憑證
    
    $user = Auth::user();  // 獲取已登入的用戶
    $token = $user->createToken('api-token')->plainTextToken;  // 建立 Sanctum token
    
    return response()->json([
        'message' => '登入成功',
        'user' => $user,
        'token' => $token
    ]);
}
```

### 2. 驗證流程 - Auth::attempt()

在 `LoginRequest@authenticate` 中調用 `Auth::attempt()`：

```php
public function authenticate()
{
    if (! Auth::attempt($this->only('email', 'password'))) {
        throw ValidationException::withMessages([
            'email' => __('auth.failed'),
        ]);
    }
}
```

### 3. SessionGuard 的工作原理

`Auth::attempt()` 實際上是調用 `SessionGuard@attempt`：

```php
public function attempt(array $credentials = [], $remember = false)
{
    // 驗證用戶憑證
    if ($this->hasValidCredentials($user, $credentials)) {
        $this->login($user, $remember);  // 登入用戶
        return true;
    }
    return false;
}
```

### 4. session 更新機制

當用戶登入後，`SessionGuard@login` 會更新 session：

```php
public function login(AuthenticatableContract $user, $remember = false)
{
    $this->updateSession($user->getAuthIdentifier());  // 更新 session 中的用戶 ID
    
    // 處理 "記住我" 功能
    if ($remember) {
        $this->ensureRememberTokenIsSet($user);
        $this->queueRecallerCookie($user);
    }
    
    $this->fireLoginEvent($user, $remember);
    $this->setUser($user);
}
```

### 5. DatabaseSessionHandler 寫入 sessions 表

當 HTTP 請求結束時，Laravel 會透過 `DatabaseSessionHandler` 將 session 資料寫入 sessions 表：

```php
public function write($sessionId, $data): bool
{
    $payload = $this->getDefaultPayload($data);
    
    // 判斷是更新還是插入操作
    if ($this->exists) {
        $this->performUpdate($sessionId, $payload);
    } else {
        $this->performInsert($sessionId, $payload);
    }
    
    return $this->exists = true;
}
```

### 6. sessions 表數據自動關聯到用戶

session 資料會自動關聯到當前用戶：

```php
protected function getDefaultPayload($data)
{
    $payload = [
        'payload' => base64_encode($data),  // session 資料
        'last_activity' => $this->currentTime(),  // 最後活動時間
    ];
    
    // 添加用戶信息和請求信息
    return tap($payload, function (&$payload) {
        $this->addUserInformation($payload)  // 添加用戶 ID
            ->addRequestInformation($payload);  // 添加 IP 和 User Agent
    });
}
```

## sessions 表結構

sessions 表包含以下欄位：
- `id`: session ID，作為主鍵
- `user_id`: 與用戶 ID 的外鍵關聯
- `ip_address`: 用戶 IP 地址
- `user_agent`: 用戶瀏覽器和設備信息
- `payload`: 編碼後的 session 資料
- `last_activity`: 最後活動時間戳

## API Token 與 Session 的關係

在基於 API 的認證中：
1. 用戶登入時同時創建 Session 和 Sanctum API Token
2. Session 用於網頁認證
3. API Token 用於 API 認證

## 後續請求處理

1. 網頁請求：通過 Session Cookie 自動識別用戶
2. API 請求：通過 Bearer Token 進行認證

## 安全考量

1. Session 資料使用 base64 編碼存儲
2. Sessions 表中的用戶關聯使系統能追蹤用戶登入記錄
3. 可設定 Session 和 Token 過期時間提高安全性 