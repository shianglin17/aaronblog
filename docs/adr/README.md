# Architecture Decision Records (ADR)

本目錄包含 Aaron 部落格系統的架構決策記錄，基於實際的 Git commit 歷史分析而來。

## 什麼是 ADR？

架構決策記錄（Architecture Decision Records, ADR）是一種輕量級的文件格式，用於記錄重要的架構決策及其背景、動機和後果。

## ADR 清單

| ADR | 標題 | 狀態 | 日期 |
|-----|------|------|------|
| [001](001-sqlite-production-database.md) | 採用 SQLite 作為生產環境資料庫 | Accepted | 2025-06-23 |
| [002](002-redis-caching-strategy.md) | Redis 快取策略實作 | Accepted | 2025-06-13 |
| [003](003-session-csrf-authentication.md) | Session Cookie + CSRF Token 認證機制 | Accepted | 2025-08-17 |
| [004](004-swagger-api-documentation.md) | 採用 Swagger/OpenAPI 作為 API 文件 | Accepted | 2025-08-16 |
| [005](005-frontend-backend-separation.md) | 前後台邏輯完全分離 | Accepted | 2025-08-13 |
| [006](006-github-actions-cicd.md) | GitHub Actions CI/CD 自動化部署 | Accepted | 2025-07-05 |
| [007](007-remove-soft-delete.md) | 移除軟刪除機制 | Accepted | 2025-06-11 |
| [008](008-api-response-architecture.md) | API 回應與異常處理架構演進 | Accepted | 2025年 |
| [009](009-aws-ec2-deployment-migration.md) | AWS EC2 部署環境遷移 | Accepted | 2025-08-26 |

## 決策狀態說明

- **Proposed**: 提議中，尚未實作
- **Accepted**: 已接受並實作
- **Deprecated**: 已棄用
- **Superseded**: 被新決策取代

## ADR 編寫指南

每個 ADR 應包含以下結構：

1. **Status**: 決策狀態
2. **Context**: 背景與問題描述
3. **Decision**: 決策內容
4. **Consequences**: 影響與後果
5. **Implementation**: 實作細節（可選）

## 相關連結

- [技術決策與演進](../technical-insights.md) - 基於 Git 提交歷史的技術決策分析
- [系統架構概述](../architecture/system-overview.md) - 整體系統架構設計