# AaronBlog

這是我的第一個正式有推上線的 side project，因為想要寫一點文章所以開始製作部落格，作品請見 [AaronBlog](https://aaronlei.com/)。

這個部落格主要是讓我放一些自己寫的文章，同時我也想分享給其他人，之後會有留言功能，希望可以讓大家提出對我的指教。

未來我也會持續的更新這個部落格，包含程式碼、架構，希望可以透過自己有在使用的 app 來增進自己的實力，並且在 github 上面記錄。

## 技術選型

前端 : Vue 3, TypeScript
後端 : Laravel 12
前端畫面套件 : naive ui
後端認證 : Sanctum 
容器 : docker
伺服器 : GCP-VM

# 關於部署 :

## 後端修改後推上正式環境流程

1. 本地```docker buildx build --platform linux/amd64 -t aaronlei17/aaronblog-app:latest --push .```
2. GCP VM 直接 ```docker pull aaronlei17/aaronblog-app:latest```
3. GCP VM ```docker-compose -f docker-compose.gcp.yml down```
4. GCP VM ```docker-compose -f docker-compose.gcp.yml up -d```

## 前端修改後推上正式環境流程

1. 本地 npm run build 編譯前端資源
2. git push（要包含前端編譯後的 public/）
3. GCP VM ```git pull``` (docker-compose 已掛載 volumn) 

## 維護與清理

### Docker 資源清理

建議定期清理 Docker 資源以釋放磁碟空間和避免資源衝突：

```bash
# 清理所有未使用的 Docker 資源（映像、容器、網路、構建快取）
docker system prune -a -f
```

**建議清理時機：**
- 部署遇到問題時
- 磁碟空間不足時
- 定期維護（每週或每月）

**注意事項：**
- 此指令會刪除所有未使用的 Docker 映像
- 執行前請確保沒有其他重要容器在運行
- 清理後首次啟動可能需要重新下載映像

## 文檔

- [版本管理規範](docs/versioning.md)
- [API 文檔](docs/api/README.md)
- [系統架構](docs/architecture/architecture-overview.md)