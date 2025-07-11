# 編碼規範

## 概述

本專案遵循 Laravel 官方與團隊自訂規範，確保程式碼一致性、可讀性與可維護性。所有規範均以專案現有程式碼為準。

## 命名規範
- 類別：PascalCase
- 方法、變數：camelCase
- 常數：全大寫底線
- 檔案：小寫、連字符（-）
- 資料表：小寫、底線、複數
- 欄位：小寫、底線

## 檔案與目錄結構
- 控制器、服務、模型、資源、Repository、Cache、Transformer、Request、Migration、Seeder、Factory 皆有對應目錄
- 目錄與檔案命名均依規範

## 程式碼風格
- 4 空格縮排，不用 Tab
- 行長度不超過 120 字元
- 大括號獨立一行
- 類別、方法、區塊間適當空行

## 註解規範
- 使用 PHPDoc 格式
- 類別、方法、複雜邏輯必須有註解
- 單行註解用 //，多行用 /** */

## 型別宣告
- 方法參數、回傳值、屬性皆明確宣告型別

## 錯誤處理
- 使用自定義例外類別
- 適當記錄錯誤日誌
- 不向使用者顯示詳細錯誤訊息

## 安全性
- 所有輸入皆用 Laravel 驗證
- 資料庫操作用 Eloquent/Query Builder，避免原生 SQL
- 密碼用 Hash 儲存
- 使用 $fillable 或 $guarded 防止大量賦值

## 效能
- 避免 N+1 問題，使用 eager loading
- 適當使用快取，快取鍵有命名空間

## 測試
- 所有新功能需有測試，覆蓋率 80% 以上
- 測試命名以 test 開頭，描述功能

## 工具
- PHP CS Fixer、PHPStan、Laravel Pint

## Git 提交規範
- 格式：<type>(<scope>): <subject>
- type: feat, fix, docs, style, refactor, test, chore

## 最佳實踐
- 一致性、可讀性、型別安全、測試覆蓋、註解、效能、安全、工具化 