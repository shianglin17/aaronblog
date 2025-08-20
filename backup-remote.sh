#!/bin/bash

# AaronBlog 遠端備份腳本 - 最小可行版本
# 請先設定您的實際伺服器資訊

set -e

# =============================================================================
# 載入設定檔
# =============================================================================
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
CONFIG_FILE="$SCRIPT_DIR/backup-config.env"

if [ ! -f "$CONFIG_FILE" ]; then
    echo "❌ 找不到設定檔 $CONFIG_FILE"
    echo "請確認 backup-config.env 檔案存在"
    exit 1
fi

# 載入設定
source "$CONFIG_FILE"

# 設定變數
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="$LOCAL_BACKUP_PATH/$DATE"

# =============================================================================
# 腳本開始
# =============================================================================

# 檢查必要設定
if [ -z "$REMOTE_HOST" ] || [ -z "$REMOTE_USER" ]; then
    echo "❌ 設定檔中缺少必要的 REMOTE_HOST 或 REMOTE_USER"
    exit 1
fi

# 建立備份目錄
mkdir -p "$BACKUP_DIR"

echo "🚀 開始備份 AaronBlog - $DATE"
echo "📡 遠端: $REMOTE_USER@$REMOTE_HOST"
echo "💾 備份到: $BACKUP_DIR"
echo ""

# SSH 選項
SSH_OPTS="-o ConnectTimeout=10 -o StrictHostKeyChecking=yes"
if [ -f "$SSH_KEY_PATH" ]; then
    SSH_OPTS="$SSH_OPTS -i $SSH_KEY_PATH"
fi

# 測試連線
echo "🔌 測試 SSH 連線..."
if ! ssh $SSH_OPTS "$REMOTE_USER@$REMOTE_HOST" "echo 'SSH 連線成功'" 2>/dev/null; then
    echo "❌ SSH 連線失敗，請檢查："
    echo "   - 伺服器 IP/網域是否正確"
    echo "   - SSH 金鑰是否設定正確"
    echo "   - 防火牆是否開啟 SSH 連接埠"
    exit 1
fi
echo "✅ SSH 連線成功"
echo ""

# 1. 備份資料庫（從 Docker volume）
echo "📊 備份資料庫..."
if ssh $SSH_OPTS "$REMOTE_USER@$REMOTE_HOST" \
    "docker run --rm -v $DOCKER_VOLUME_NAME:/data alpine cat /data/database.sqlite" > "$BACKUP_DIR/database.sqlite"; then
    echo "✅ 資料庫備份完成"
else
    echo "❌ 資料庫備份失敗"
    exit 1
fi

# 2. 備份 .env
echo "📄 備份設定檔..."
if scp $SSH_OPTS "$REMOTE_USER@$REMOTE_HOST:$REMOTE_PROJECT_PATH/.env" "$BACKUP_DIR/env.backup"; then
    echo "✅ 設定檔備份完成"
else
    echo "❌ 設定檔備份失敗"
    exit 1
fi

# 3. 建立備份資訊
cat > "$BACKUP_DIR/backup-info.txt" << EOF
AaronBlog 備份資訊
==================
備份時間: $(date)
遠端伺服器: $REMOTE_HOST
資料庫大小: $(ls -lh "$BACKUP_DIR/database.sqlite" | awk '{print $5}')
設定檔: env.backup (明文)

還原指令:
--------
資料庫: cp $BACKUP_DIR/database.sqlite /path/to/project/database/
設定檔: cp $BACKUP_DIR/env.backup .env
EOF

echo ""
echo "🎉 備份完成！"
echo "📁 備份位置: $BACKUP_DIR"
echo "📋 詳細資訊: $BACKUP_DIR/backup-info.txt"
echo ""
echo "快速還原指令："
echo "  cp $BACKUP_DIR/database.sqlite /path/to/project/database/"
echo "  cp $BACKUP_DIR/env.backup .env"