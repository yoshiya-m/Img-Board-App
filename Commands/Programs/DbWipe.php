<?php

namespace Commands\Programs;

use Commands\AbstractCommand;
use Commands\Argument;
use Helpers\Settings;
use Database\MySQLWrapper;

class DbWipe extends AbstractCommand
{
    // 使用するコマンド名を設定
    protected static ?string $alias = 'db-wipe';

    // 引数を割り当て
    public static function getArguments(): array
    {
        return [
            (new Argument('backup'))->description('Make a backup.')->required(false)->allowAsShort(true),
        ];
    }
    // データベース消去を実行する
    public function execute(): int
    {
        // データベース消去の引数を代入
        $backup = $this->getArgumentValue('backup');
        if($backup === true){
            $this->backup();
        }
        $this->clearDatabase();

        return 0;
    }
    // バックアップの作成
    private function backup(): void {
        $username = $username??Settings::env('DATABASE_USER');
        $password = $password??Settings::env('DATABASE_USER_PASSWORD');
        $database = $database??Settings::env('DATABASE_NAME');
        $this->log("making a backup......");
        exec("mysqldump -u $username -p $database > backup.sql");
        $this->log("Successfully made a backup");
    }
    // データベース消去
    private function clearDatabase(): void {
        $mysqli = new MySQLWrapper();
        $username = $username??Settings::env('DATABASE_USER');
        $password = $password??Settings::env('DATABASE_USER_PASSWORD');
        $database = $database??Settings::env('DATABASE_NAME');
        $this->log("clearing database...");
        $mysqli->query("DROP DATABASE $database");
        $mysqli->query("CREATE DATABASE $database");
        $this->log("Successfully cleared database");
        
    }


}