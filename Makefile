# Makefile for AaronBlog - Laravel + Vue 專案
# 提供本地開發和 GCP 部署的常用指令

.PHONY: help docker-app docker-app-bash

# 預設指令 - 顯示說明
help:
	@echo "AaronBlog 專案指令說明："
	@echo ""
	@echo "docker 指令："
	@echo "  docker-app-bash - docker exec -it aaronblog-app bash"
	@echo "  docker-app - 跑 aaronblog-app 容器"
	@echo ""

docker-app:
	@echo "docker 跑 aaronblog-app 容器"
	docker exec aaronblog-app

docker-app-bash:
	@echo "進入 aaronblog-app 容器 bash"
	docker exec -it aaronblog-app bash