# Makefile for AaronBlog - Laravel + Vue 專案
# 提供本地開發和 GCP 部署的常用指令

.PHONY: help dev-up dev-down gcp-up gcp-down build dev-artisan gcp-artisan install clean status health

# 預設指令 - 顯示說明
help:
	@echo "AaronBlog 專案指令說明："
	@echo ""
	@echo "本地開發環境（MySQL）："
	@echo "  dev-up        - 啟動本地開發環境"
	@echo "  dev-down      - 停止本地開發環境"
	@echo "  dev-logs      - 查看本地環境日誌"
	@echo "  dev-shell     - 進入本地環境 app 容器"
	@echo "  dev-artisan   - 執行本地 artisan 指令 (用法: make dev-artisan CMD='migrate')"
	@echo "  dev-setup     - 本地環境初始設置（建構→啟動→遷移→填充）"
	@echo ""
	@echo "GCP 部署環境（SQLite）："
	@echo "  gcp-up        - 啟動 GCP 環境"
	@echo "  gcp-down      - 停止 GCP 環境"
	@echo "  gcp-logs      - 查看 GCP 環境日誌"
	@echo "  gcp-shell     - 進入 GCP 環境 app 容器"
	@echo "  gcp-artisan   - 執行 GCP artisan 指令"
	@echo "  gcp-setup-env - 設定 GCP 環境變數檔案"
	@echo "  gcp-deploy    - GCP 完整部署流程（建構→啟動→遷移→快取）"
	@echo ""
	@echo "通用指令："
	@echo "  build         - 重新建構映像檔"
	@echo "  install       - 安裝前端依賴"
	@echo "  clean         - 清理未使用的 Docker 資源"
	@echo "  status        - 查看容器狀態"
	@echo "  health        - 檢查服務健康狀態"
	@echo ""

# === 本地開發環境（MySQL）===
dev-up:
	@echo "🚀 啟動本地開發環境..."
	docker-compose up -d
	@echo "✅ 本地開發環境已啟動 - http://localhost:8080"

dev-down:
	@echo "🛑 停止本地開發環境..."
	docker-compose down

dev-logs:
	@echo "📋 查看本地環境日誌..."
	docker-compose logs -f

dev-shell:
	@echo "🐚 進入本地環境 app 容器..."
	docker exec -it aaronblog-app bash

dev-artisan:
	@echo "⚡ 執行本地 artisan 指令: $(CMD)"
	docker exec aaronblog-app php artisan $(CMD)

# === GCP 部署環境（SQLite）===
gcp-up:
	@echo "🌐 啟動 GCP 環境..."
	docker-compose -f docker-compose.gcp.yml up -d
	@echo "✅ GCP 環境已啟動"

gcp-down:
	@echo "🛑 停止 GCP 環境..."
	docker-compose -f docker-compose.gcp.yml down

gcp-logs:
	@echo "📋 查看 GCP 環境日誌..."
	docker-compose -f docker-compose.gcp.yml logs -f

gcp-shell:
	@echo "🐚 進入 GCP 環境 app 容器..."
	docker exec -it aaronblog-app bash

gcp-artisan:
	@echo "⚡ 執行 GCP artisan 指令: $(CMD)"
	docker exec aaronblog-app php artisan $(CMD)

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

status:
	@echo "📊 檢查容器狀態..."
	docker ps | grep aaronblog

health:
	@echo "🏥 檢查服務健康狀態..."
	docker ps --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}" | grep aaronblog

# === 便利指令組合 ===
dev-setup: build dev-up
	@echo "🎯 本地環境初始設置..."
	@echo "⏳ 等待服務啟動..."
	sleep 10
	$(MAKE) dev-artisan CMD="migrate"
	$(MAKE) dev-artisan CMD="db:seed"
	@echo "✅ 本地開發環境設置完成！"

gcp-setup-env:
	@echo "🔧 設定 GCP 環境變數..."
	@if [ ! -f .env.gcp ]; then \
		echo "📋 複製環境變數範本..."; \
		cp .env.gcp.example .env.gcp; \
		echo "⚠️  請編輯 .env.gcp 設定 APP_KEY 等重要參數"; \
		echo "💡 執行: php artisan key:generate --show 產生 APP_KEY"; \
	else \
		echo "✅ .env.gcp 已存在"; \
	fi

gcp-deploy: gcp-setup-env build gcp-up
	@echo "🌐 GCP 環境部署..."
	@echo "⏳ 等待服務啟動..."
	sleep 15
	$(MAKE) gcp-artisan CMD="key:generate --force"
	$(MAKE) gcp-artisan CMD="migrate --force"
	$(MAKE) gcp-artisan CMD="config:cache"
	$(MAKE) gcp-artisan CMD="route:cache"
	$(MAKE) gcp-artisan CMD="view:cache"
	$(MAKE) gcp-artisan CMD="storage:link"
	@echo "✅ GCP 環境部署完成！" 