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
        $name = $this->option('name');
        $email = $this->option('email');
        $password = $this->option('password');

        if (!$email || !$password) {
            $this->error('Email and password are required');
            return;
        }

        $controller = new AuthController();
        try {
            $result = $controller->register($name, $email, $password);
            $user = $result['user'];
            $this->info("註冊成功！用戶ID：{$user->id}，Email：{$user->email}");
        } catch (ValidationException $e) {
            foreach ($e->errors() as $field => $messages) {
                foreach ($messages as $msg) {
                    $this->error("{$field}: {$msg}");
                }
            }
        } catch (\Exception $e) {
            $this->error('註冊失敗：' . $e->getMessage());
        }
    }
}
