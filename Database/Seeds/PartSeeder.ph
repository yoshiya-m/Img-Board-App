<?php

namespace Database\Seeds;

use Database\AbstractSeeder;
use Faker\Factory;
use Database\MySQLWrapper;

class PartSeeder extends AbstractSeeder
{
    protected ?string $tableName = 'part';
    protected array $tableColumns = [
        [
            'data_type' => 'int',
            'column_name' => 'carID'
        ],
        [
            'data_type' => 'string',
            'column_name' => 'name'
        ],
        [
            'data_type' => 'string',
            'column_name' => 'description'
        ],
        [
            'data_type' => 'float',
            'column_name' => 'price'
        ],
        [
            'data_type' => 'int',
            'column_name' => 'quantityInStock'
        ]
    ];

    public function createRowData(): array
    {
        $faker = Factory::create();
        // データ生成数
        $DATA_QUANTITY = 100000;
        // carsから最大のidを取得して、1から最大の間でnumberを生成する
        $mysqli = new MySQLWrapper();
        $result = $mysqli->query('
            SELECT id from cars 
        ');

        if ($result === false) throw new \Exception('could not execute query');
        else $rows = $result->fetch_all();
        $dataArr = [];
        
        for ($i = 0; $i < $DATA_QUANTITY; $i++) {
            $rowIndex = $faker->numberBetween(0, count($rows) -1);
            $data = [
                (int)$rows[$rowIndex][0],// carsから取り出す必要あり
                $faker->word(),
                $faker->sentence(),
                $faker->randomFloat(),
                $faker->randomNumber(),
            ];
            array_push($dataArr, $data);
        }
        return $dataArr;
    }
}
