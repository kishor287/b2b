<?php

// Define the root path constant
if(!defined('ROOT_PATH')){
    define('ROOT_PATH', realpath(dirname(__FILE__) . '/../'));
}

// Include Composer autoloader
require_once(ROOT_PATH . '/vendor/autoload.php');
