<?
session_start();
$url = explode('/', $_SERVER['REQUEST_URI']);
$page = explode("?", $url[2], 2);
 
if(isset($_SESSION['uId']) || isset($_COOKIE['uId'])){
   require_once('routes.php');
   include('view/main/head.html');
   include('view/'.$path);
   include('view/main/foot.html');
   exit();
}

if($page[0]=='register'){
include('view/register.html');    
}else{
include('view/login.html'); 
}