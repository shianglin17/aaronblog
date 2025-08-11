<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\AuthController;
use Illuminate\Validation\ValidationException;

class UserRegisterCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:register {--name=} {--email=} {--password=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Register a new user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Aaron Blog - 用戶註冊');
        $this->info('');

        // 取得用戶輸入，支援參數或互動式
        $name = $this->option('name') ?: $this->ask('請輸入用戶名稱');
        $email = $this->option('email') ?: $this->ask('請輸入 Email');
        $password = $this->option('password') ?: $this->secret('請輸入密碼 (至少6個字元)');

        // 確認輸入
        $this->info('');
        $this->table(['欄位', '值'], [
            ['姓名', $name],
            ['Email', $email],
            ['密碼', str_repeat('*', strlen($password))]
        ]);

        if (!$this->confirm('確認要建立此用戶嗎？', true)) {
            $this->info('取消註冊');
            return 0;
        }

        // 執行註冊
        $controller = new AuthController();
        try {
            $result = $controller->registerFromCli($name, $email, $password);
            $user = $result['user'];
            
            $this->info('');
            $this->info("✅ 註冊成功！");
            $this->table(['資訊', '值'], [
                ['用戶 ID', $user->id],
                ['姓名', $user->name],
                ['Email', $user->email],
                ['建立時間', $user->created_at->format('Y-m-d H:i:s')]
            ]);
            
            return 0;
        } catch (ValidationException $e) {
            $this->error('❌ 驗證失敗：');
            foreach ($e->errors() as $field => $messages) {
                foreach ($messages as $msg) {
                    $this->error("  • {$field}: {$msg}");
                }
            }
            return 1;
        } catch (\Exception $e) {
            $this->error('❌ 註冊失敗：' . $e->getMessage());
            return 1;
        }
    }
}
