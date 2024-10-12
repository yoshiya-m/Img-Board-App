<?php

namespace Database\Seeds;

use Database\AbstractSeeder;
use Faker\Factory;
use Carbon\Carbon;

class CarsSeeder extends AbstractSeeder
{
    protected ?string $tableName = 'cars';
    protected array $tableColumns = [
        [
            'data_type' => 'string',
            'column_name' => 'make'
        ],
        [
            'data_type' => 'string',
            'column_name' => 'model'
        ],
        [
            'data_type' => 'int',
            'column_name' => 'year'
        ],
        [
            'data_type' => 'string',
            'column_name' => 'color'
        ],
        [
            'data_type' => 'float',
            'column_name' => 'price'
        ],
        [
            'data_type' => 'float',
            'column_name' => 'mileage'
        ],
        [
            'data_type' => 'string',
            'column_name' => 'transmission'
        ],
        [
            'data_type' => 'string',
            'column_name' => 'engine'
        ],
        [
            'data_type' => 'string',
            'column_name' => 'status'
        ],
        [
            'data_type' => 'Carbon\Carbon',
            'column_name' => 'created_at'
        ],
        [
            'data_type' => 'Carbon\Carbon',
            'column_name' => 'updated_at'
        ],
    ];

    public function createRowData(): array
    {
        $faker = Factory::create();
        $DATA_QUANTITY = 1;
        $dataArr = [];
        for ($i = 0; $i < $DATA_QUANTITY; $i++) {
            $data = [
                $faker->word(),
                $faker->word(),
                (int)$faker->year(),
                $faker->word(),
                $faker->randomFloat(),
                $faker->randomFloat(),
                $faker->word(),
                $faker->word(),
                substr($faker->word(), 0, 10),
                Carbon::now(),
                Carbon::createFromTimestamp(strtotime(sprintf("%s %s", $faker->date(), $faker->time())))
            ];
            array_push($dataArr, $data);
        }
        return $dataArr;
    }
}
