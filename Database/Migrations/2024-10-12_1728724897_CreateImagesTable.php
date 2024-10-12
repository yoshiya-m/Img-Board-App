<?php

namespace Database\Migrations;

use Database\SchemaMigration;

class CreateImagesTable implements SchemaMigration
{
    public function up(): array
    {
        // マイグレーションロジックをここに追加してください
        return [
            "CREATE TABLE images (
                post_id INT PRIMARY KEY AUTO_INCREMENT,
                filepath VARCHAR(50) NOT NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
            )"
        ];
    }

    public function down(): array
    {
        // ロールバックロジックを追加してください
        return [
            "DROP TABLE images"
        ];
    }
}