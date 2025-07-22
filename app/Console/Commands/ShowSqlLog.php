<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ShowSqlLog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sql:show {--lines=20 : Number of lines to show} {--today : Show only today\'s log}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show SQL query logs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $lines = $this->option('lines');
        $today = $this->option('today');

        // 確定日誌檔案路徑
        if ($today) {
            $logFile = storage_path('logs/sql-' . date('Y-m-d') . '.log');
        } else {
            // 找到最新的 SQL 日誌檔案
            $logFiles = glob(storage_path('logs/sql-*.log'));
            if (empty($logFiles)) {
                $this->error('No SQL log files found.');
                return 1;
            }
            $logFile = end($logFiles); // 最新的檔案
        }

        if (!File::exists($logFile)) {
            $this->error("SQL log file not found: {$logFile}");
            return 1;
        }

        $this->info("Showing last {$lines} lines from: " . basename($logFile));
        $this->line('');

        // 讀取並顯示日誌內容
        $content = File::get($logFile);
        $lines_array = explode("\n", $content);
        $lines_array = array_filter($lines_array); // 移除空行
        
        $last_lines = array_slice($lines_array, -$lines);
        
        foreach ($last_lines as $line) {
            // 解析日誌格式並美化輸出
            if (preg_match('/\[(.*?)\] local\.INFO: \[(.*?)\] \((.*?)\) (.*)/', $line, $matches)) {
                $timestamp = $matches[2];
                $executionTime = $matches[3];
                $sql = $matches[4];
                
                $this->line("<fg=yellow>[{$timestamp}]</> <fg=green>({$executionTime})</>");
                $this->line("<fg=cyan>{$sql}</>");
                $this->line('');
            } else {
                $this->line($line);
            }
        }

        return 0;
    }
}
