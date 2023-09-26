<?
session_start();
define('APP_PATH', dirname(__FILE__) . '/');
if(!isset($_SESSION['uId']) && $_POST['_action']!='login'){
      $result = json_encode(array("status"=>0,"msg"=>"error","data"=>"Session Expired. Please login again."));
      echo $result; exit();
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


include('../var.php');
include('function.php');
 

$action=$_POST['_action'];
$payload=$_POST['payload'];

include($payload.'.php');

   