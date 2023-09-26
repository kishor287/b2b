<?php


namespace server\Classes\Seeder;

require_once APP_PATH.'../vendor/autoload.php'; 
use Faker\Factory as Faker;
use Panel\Server\Classes\Config\Builder;
use Panel\Server\Classes\Config\DatabaseSeeder;

class SimInventorySeeder {

    private $table;
    private $faker;
    public function __construct($table){
        $this->table = $table;
        $this->faker = Faker::create();
    }
    public function makeData(int $count){
        $table = $this->table;
        for ($i=1; $i < $count; $i++) { 
            $userId = '12';
            $simNumber = $this->faker->randomNumber();
            $barCode = $this->faker->text();
            $country = $this->faker->country();
            $uploadedby = $this->faker->name();
            $data = [
                'user_id' => $userId,
                'sim_number' => $simNumber,
                'bar_code' => $barCode,
                'country' => $country,
                'uploaded_by' => $uploadedby,
            ];

            $dbSeeder = new DatabaseSeeder();
            $dbSeeder->data($data);
            $dbSeeder->table($table);
            $dbSeeder->run();
        }
    }
}