#!/bin/bash

# Aaron Blog 版本發布腳本
# 使用方式: ./scripts/release.sh [版本號] [發布說明]
# 範例: ./scripts/release.sh v1.0.1 "修復文章搜尋功能的 bug"

set -e

# 檢查參數
if [ $# -lt 2 ]; then
    echo "使用方式: $0 <版本號> <發布說明>"
    echo "範例: $0 v1.0.1 '修復文章搜尋功能的 bug'"
    exit 1
fi

VERSION=$1
RELEASE_NOTES=$2
DOCKER_IMAGE="aaronlei17/aaronblog-app"

echo "🚀 開始發布版本: $VERSION"
echo "📝 發布說明: $RELEASE_NOTES"

# 檢查是否有未提交的變更
if [ -n "$(git status --porcelain)" ]; then
    echo "❌ 錯誤: 有未提交的變更，請先提交所有變更"
    git status --short
    exit 1
fi

# 檢查是否在 main 分支
CURRENT_BRANCH=$(git branch --show-current)
if [ "$CURRENT_BRANCH" != "main" ]; then
    echo "❌ 錯誤: 請在 main 分支上發布版本，目前在 $CURRENT_BRANCH 分支"
    exit 1
fi

# 拉取最新代碼
echo "📥 拉取最新代碼..."
git pull origin main

# 檢查版本號格式
if [[ ! $VERSION =~ ^v[0-9]+\.[0-9]+\.[0-9]+$ ]]; then
    echo "❌ 錯誤: 版本號格式不正確，應該是 vX.Y.Z 格式（例如 v1.0.1）"
    exit 1
fi

# 檢查版本號是否已存在
if git tag -l | grep -q "^$VERSION$"; then
    echo "❌ 錯誤: 版本 $VERSION 已存在"
    exit 1
fi

# 建立 Git 標籤
echo "🏷️  建立 Git 標籤..."
git tag -a "$VERSION" -m "$RELEASE_NOTES"

# 推送標籤到遠端
echo "📤 推送標籤到 GitHub..."
git push origin "$VERSION"

# 建立 Docker 映像
echo "🐳 建立 Docker 映像..."
docker build -t "$DOCKER_IMAGE:$VERSION" .
docker build -t "$DOCKER_IMAGE:latest" .

# 推送 Docker 映像
echo "📤 推送 Docker 映像到 Docker Hub..."
docker push "$DOCKER_IMAGE:$VERSION"
docker push "$DOCKER_IMAGE:latest"

# 更新 docker-compose.gcp.yml 中的映像版本
echo "📝 更新 docker-compose.gcp.yml..."
sed -i.bak "s|image: $DOCKER_IMAGE:.*|image: $DOCKER_IMAGE:$VERSION|g" docker-compose.gcp.yml
rm docker-compose.gcp.yml.bak

# 提交版本更新
git add docker-compose.gcp.yml
git commit -m "chore: 更新生產環境映像版本到 $VERSION"
git push origin main

echo "✅ 版本 $VERSION 發布完成！"
echo ""
echo "📋 發布摘要:"
echo "   Git 標籤: $VERSION"
echo "   Docker 映像: $DOCKER_IMAGE:$VERSION"
echo "   發布說明: $RELEASE_NOTES"
echo ""
echo "🚀 部署指令:"
echo "   cd /path/to/production"
echo "   git pull origin main"
echo "   docker-compose -f docker-compose.gcp.yml pull"
echo "   docker-compose -f docker-compose.gcp.yml up -d" 