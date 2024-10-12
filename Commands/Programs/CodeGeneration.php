<?php

namespace Commands\Programs;

use Commands\AbstractCommand;
use Commands\Argument;

class CodeGeneration extends AbstractCommand
{
    // 使用するコマンド名を設定します
    protected static ?string $alias = 'code-gen';
    protected static bool $requiredCommandValue = true;

    // 引数を割り当てます
    public static function getArguments(): array
    {
        return [
            (new Argument('name'))->description('Name of the file that is to be generated.')->required(false),
        ];
    }

    // コマンドを実行する
    public function execute(): int
    {
        $codeGenType = $this->getCommandValue();
        $this->log('Generating code for.......' . $codeGenType);

        // マイグレーションファイルの作成関数を実行
        if ($codeGenType === 'migration') {
            $migrationName = $this->getArgumentValue('name');
            $this->generateMigrationFile($migrationName);
        }

        return 0;
    }

    // マイグレーションファイルを作成関数
    private function generateMigrationFile(string $migrationName): void
    {
        // ファイル名を作成
        $filename = sprintf(
            '%s_%s_%s.php',
            date('Y-m-d'),
            time(),
            $migrationName
        );
        // マイグレーションファイルの中身を作成
        $migrationContent = $this->getMigrationContent($migrationName);

        // 移行ファイルを保存するパスを指定します
        $path = sprintf("%s/../../Database/Migrations/%s", __DIR__,$filename);

        // ファイル作成
        file_put_contents($path, $migrationContent);
        $this->log("Migration file {$filename} has been generated!");
    }

    // マイグレーションファイルの中身を作成
    private function getMigrationContent(string $migrationName): string
    {
        $className = $this->pascalCase($migrationName);

        return <<<MIGRATION
        <?php

        namespace Database\Migrations;

        use Database\SchemaMigration;

        class {$className} implements SchemaMigration
        {
            public function up(): array
            {
                // マイグレーションロジックをここに追加してください
                return [];
            }

            public function down(): array
            {
                // ロールバックロジックを追加してください
                return [];
            }
        }
        MIGRATION;
    }

    private function pascalCase(string $string): string{
        return str_replace(' ', '', ucwords(str_replace('_', ' ', $string)));
    }
}