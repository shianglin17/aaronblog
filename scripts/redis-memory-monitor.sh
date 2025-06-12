#!/bin/bash

# Redis 記憶體監控腳本
# 用途：快速檢查 Redis 記憶體使用情況

echo "======================================"
echo "Aaron Blog - Redis 記憶體監控"
echo "======================================"
echo ""

# 快速查看系統記憶體
echo "【系統記憶體】"
free -h | head -2
echo ""

# 檢查 Redis 記憶體使用詳細資訊
echo "【Redis 記憶體詳細資訊】"
if docker ps --format '{{.Names}}' | grep -q "aaronblog-redis"; then
    echo "Redis 容器狀態：運行中"
    echo ""
    
    # Redis 記憶體使用統計
    echo "Redis 記憶體統計："
    docker exec aaronblog-redis redis-cli info memory | grep -E "(used_memory_human|used_memory_peak_human|maxmemory_human|mem_fragmentation_ratio)"
    echo ""
    
    # Redis 鍵值數量
    echo "Redis 鍵值統計："
    docker exec aaronblog-redis redis-cli info keyspace
    echo ""
    
    # Redis 命中率
    echo "Redis 命中率統計："
    docker exec aaronblog-redis redis-cli info stats | grep -E "(keyspace_hits|keyspace_misses)"
    
else
    echo "Redis 容器未運行"
fi

echo ""
echo "【Redis 使用建議】"
if docker ps --format '{{.Names}}' | grep -q "aaronblog-redis"; then
    REDIS_MEM=$(docker exec aaronblog-redis redis-cli info memory | grep "used_memory:" | cut -d: -f2 | tr -d '\r')
    REDIS_MEM_MB=$((REDIS_MEM / 1024 / 1024))
    
    if [ $REDIS_MEM_MB -gt 80 ]; then
        echo "⚠️  Redis 記憶體使用量：${REDIS_MEM_MB}MB (建議不超過 100MB)"
    else
        echo "✅ Redis 記憶體使用量：${REDIS_MEM_MB}MB (正常)"
    fi
else
    echo "💡 Redis 容器未運行"
fi

echo ""
echo "監控完成 - $(date)" 