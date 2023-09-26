<?php

namespace Panel\Server\Classes\Controller;

use PDO;
use PDOException;

class BaseController
{

    private const HOST = 'localhost';
    private const USER = 'innerxcrm_internal';
    private const DB = 'innerxcrm_team_panel';
    private const PASS = 'innerxcrm_internal@77';

    protected $connection;

    public function __construct() {
        
    try {
        $dsn = "mysql:host=" . self::HOST . ";dbname=" . self::DB;
        $this->connection = new PDO($dsn, self::USER, self::PASS);
    } catch (PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}

} 