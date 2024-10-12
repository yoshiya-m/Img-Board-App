<?php

namespace Database\Seeds;

use Database\AbstractSeeder;
use Database\DataAccess\Implementations\PostDAOImpl;
use Faker\Factory;
use Helpers\DatabaseHelper;

class PostsSeeder extends AbstractSeeder
{
    protected ?string $tableName = 'posts';
    protected array $tableColumns = [
        [
            'data_type' => 'int',
            'column_name' => 'reply_to_id'
        ],
        [
            'data_type' => 'string',
            'column_name' => 'subject'
        ],
        [
            'data_type' => 'string',
            'column_name' => 'content'
        ],
    ];

    public function createRowData(): array
    {

        $faker = Factory::create();
        $DATA_QUANTITY = 1000;
        $dataArr = [];
        for ($i = 0; $i < $DATA_QUANTITY; $i++) {

            // DBから検索
            $postDAO = new PostDAOImpl;
            $replyToId  = $postDAO->getRandom()?->getPostId();
            

            $data = [
                $replyToId,
                $faker->word(),
                $faker->paragraph()
            ];
            array_push($dataArr, $data);
        }
        return $dataArr;
    }
}
