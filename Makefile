# Makefile for AaronBlog - Laravel + Vue 專案
# 提供開發和生產環境的常用指令

.PHONY: help dev-up dev-down prod-up prod-down build logs shell dev-artisan prod-artisan install clean

# 預設指令 - 顯示說明
help:
	@echo "AaronBlog 專案指令說明："
	@echo ""
	@echo "開發環境："
	@echo "  dev-up        - 啟動開發環境"
	@echo "  dev-down      - 停止開發環境"
	@echo "  dev-logs      - 查看開發環境日誌"
	@echo "  dev-shell     - 進入開發環境 app 容器"
	@echo "  dev-artisan   - 執行開發環境 artisan 指令 (用法: make dev-artisan CMD='migrate')"
	@echo ""
	@echo "生產環境："
	@echo "  prod-up       - 啟動生產環境"
	@echo "  prod-down     - 停止生產環境"
	@echo "  prod-logs     - 查看生產環境日誌"
	@echo "  prod-shell    - 進入生產環境 app 容器"
	@echo "  prod-artisan  - 執行生產環境 artisan 指令 (用法: make prod-artisan CMD='migrate')"
	@echo ""
	@echo "通用指令："
	@echo "  build         - 重新建構映像檔"
	@echo "  install       - 安裝前端依賴"
	@echo "  clean         - 清理未使用的 Docker 資源"
	@echo "  rebuild-prod  - 完全重建生產環境映像檔（解決前端樣式問題）"
	@echo ""

# === 開發環境 ===
dev-up:
	@echo "🚀 啟動開發環境..."
	docker-compose -f docker-compose.yml -f docker-compose.dev.yml up -d
	@echo "✅ 開發環境已啟動 - http://localhost:8080"

dev-down:
	@echo "🛑 停止開發環境..."
	docker-compose -f docker-compose.yml -f docker-compose.dev.yml down

dev-logs:
	@echo "📋 查看開發環境日誌..."
	docker-compose -f docker-compose.yml -f docker-compose.dev.yml logs -f

dev-shell:
	@echo "🐚 進入開發環境 app 容器..."
	docker exec -it aaronblog-dev-app bash

dev-artisan:
	@echo "⚡ 執行開發環境 artisan 指令: $(CMD)"
	docker exec aaronblog-dev-app php artisan $(CMD)

# === 生產環境 ===
prod-up:
	@echo "🚀 啟動生產環境..."
	docker-compose -f docker-compose.yml -f docker-compose.prod.yml up -d
	@echo "✅ 生產環境已啟動 - http://localhost"

prod-down:
	@echo "🛑 停止生產環境..."
	docker-compose -f docker-compose.yml -f docker-compose.prod.yml down

prod-logs:
	@echo "📋 查看生產環境日誌..."
	docker-compose -f docker-compose.yml -f docker-compose.prod.yml logs -f

prod-shell:
	@echo "🐚 進入生產環境 app 容器..."
	docker exec -it aaronblog-prod-app bash

prod-artisan:
	@echo "⚡ 執行生產環境 artisan 指令: $(CMD)"
	docker exec aaronblog-prod-app php artisan $(CMD)

# === 通用指令 ===
build:
	@echo "🔨 重新建構映像檔..."
	docker-compose build --no-cache

install:
	@echo "📦 安裝前端依賴..."
	npm install

clean:
	@echo "🧹 清理未使用的 Docker 資源..."
	docker system prune -f
	docker volume prune -f

rebuild-prod:
	@echo "🔄 重建生產環境（清理舊映像檔）..."
	docker-compose -f docker-compose.yml -f docker-compose.prod.yml down
	docker rmi aaronblog-app:latest || true
	docker-compose build --no-cache
	@echo "✅ 生產環境映像檔已重建"

# === 便利指令組合 ===
dev-setup: build dev-up
	@echo "🎯 開發環境初始設置..."
	@echo "⏳ 等待服務啟動..."
	sleep 10
	$(MAKE) dev-artisan CMD="migrate"
	$(MAKE) dev-artisan CMD="db:seed"
	@echo "✅ 開發環境設置完成！"

prod-deploy: build prod-up
	@echo "🎯 生產環境部署..."
	@echo "⏳ 等待服務啟動..."
	sleep 10
	$(MAKE) prod-artisan CMD="migrate --force"
	$(MAKE) prod-artisan CMD="config:cache"
	$(MAKE) prod-artisan CMD="route:cache"
	$(MAKE) prod-artisan CMD="view:cache"
	@echo "✅ 生產環境部署完成！" 