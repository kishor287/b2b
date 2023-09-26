<?php

namespace Panel\Server\Classes\Config;



require_once('Classes/Controller/BaseController.php');
require_once('Classes/Config/Builder.php');

use Exception;
use Panel\Server\Classes\Controller\BaseController;
use Panel\Server\Classes\Config\Builder;

class DatabaseSeeder extends BaseController
{

    private array $data;
    private $table;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * array $data 
     * in array key should be the table column name
     */
    public function data(array $data)
    {
        return $this->data = $data;
    }

    public function table(string $table)
    {
        return $this->table = $table;
    }
    // Add more seed methods for other tables here

    public function run()
    {
        $data = $this->data;
        if (!is_array($data)) {
            throw new Exception('data type should be array');
        }
        if ($this->table == null) {
            throw new Exception('pleae use table() to set a table to run seeder');
        }
        try {
            $db = new Builder();
            $db->table($this->table)->insert($data);
            return true;
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}