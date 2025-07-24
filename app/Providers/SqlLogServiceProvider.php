<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Events\QueryExecuted;

class SqlLogServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // 只在開發環境或明確啟用時記錄 SQL
        if (config('app.env') === 'local' || env('ENABLE_SQL_LOG', false)) {
            $this->enableSqlLogging();
        }
    }

    /**
     * 啟用 SQL 查詢日誌記錄
     */
    private function enableSqlLogging(): void
    {
        DB::listen(function (QueryExecuted $event) {
            $sql = $event->sql;
            $bindings = $event->bindings;
            $time = $event->time;

            // 將綁定參數替換到 SQL 中，方便閱讀
            $sqlWithBindings = $this->replaceBindings($sql, $bindings);

            // 格式化日誌訊息
            $logMessage = $this->formatLogMessage($sqlWithBindings, $time);

            // 寫入日誌檔案
            Log::channel('sql')->info($logMessage);
        });
    }

    /**
     * 將綁定參數替換到 SQL 中
     */
    private function replaceBindings(string $sql, array $bindings): string
    {
        foreach ($bindings as $binding) {
            $value = is_numeric($binding) ? $binding : "'" . addslashes($binding) . "'";
            $sql = preg_replace('/\?/', $value, $sql, 1);
        }

        return $sql;
    }

    /**
     * 格式化日誌訊息
     */
    private function formatLogMessage(string $sql, float $time): string
    {
        $timestamp = now()->format('Y-m-d H:i:s');
        
        // 轉換毫秒為更易讀的格式
        if ($time < 1) {
            $executionTime = number_format($time * 1000, 2) . 'ms';
        } else {
            $executionTime = number_format($time, 3) . 's';
        }
        
        return "[{$timestamp}] ({$executionTime}) {$sql}";
    }

    /**
     * 查看 SQL 日誌的靜態方法
     */
    public static function showLog(int $lines = 10, bool $todayOnly = false): void
    {
        // 確定日誌檔案路徑
        if ($todayOnly) {
            $logFile = storage_path('logs/sql-' . date('Y-m-d') . '.log');
        } else {
            // 找到最新的 SQL 日誌檔案
            $logFiles = glob(storage_path('logs/sql-*.log'));
            if (empty($logFiles)) {
                echo "No SQL log files found.\n";
                return;
            }
            $logFile = end($logFiles); // 最新的檔案
        }

        if (!file_exists($logFile)) {
            echo "SQL log file not found: " . basename($logFile) . "\n";
            return;
        }

        echo "Showing last {$lines} lines from: " . basename($logFile) . "\n\n";

        // 讀取並顯示日誌內容
        $content = file_get_contents($logFile);
        $lines_array = explode("\n", $content);
        $lines_array = array_filter($lines_array); // 移除空行
        
        $last_lines = array_slice($lines_array, -$lines);
        
        foreach ($last_lines as $line) {
            // 解析日誌格式並美化輸出
            if (preg_match('/\[(.*?)\] local\.INFO: \[(.*?)\] \((.*?)\) (.*)/', $line, $matches)) {
                $timestamp = $matches[2];
                $executionTime = $matches[3];
                $sql = $matches[4];
                
                echo "\033[33m[{$timestamp}]\033[0m \033[32m({$executionTime})\033[0m\n";
                echo "\033[36m{$sql}\033[0m\n\n";
            } else {
                echo $line . "\n";
            }
        }
    }
}
