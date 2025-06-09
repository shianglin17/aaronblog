# AaronBlog

這是我的第一個正式有推上線的 side project，因為想要寫一點文章所以開始製作部落格，作品請見 [https:aaron](https://aaronlei.com/)

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
2. GCP VM 直接 ```docker push aaronlei17/aaronblog-app:latest```
3. GCP VM ```docker-compose.gcp.yml down```
4. GCP VM ```docker-compose.gcp.yml up -d```

## 前端修改後推上正式環境流程

1. 本地 npm run build 編譯前端資源
2. git push（要包含前端編譯後的 public/）
3. GCP VM ```git pull``` (docker-compose 已掛載 volumn) 