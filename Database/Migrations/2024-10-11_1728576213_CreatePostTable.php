<?php

namespace Database\Migrations;

use Database\SchemaMigration;

class CreatePostTable implements SchemaMigration
{
    public function up(): array
    {
        return [
            "CREATE TABLE posts (
                post_id INT PRIMARY KEY AUTO_INCREMENT,
                reply_to_id INT,
                subject VARCHAR(255),
                content TEXT NOT NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )"
        ];
    }

    public function down(): array
    {
        // ロールバックロジックを追加してください
        return [
            "DROP TABLE posts"
        ];
    }
}