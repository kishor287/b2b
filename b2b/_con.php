<?php

require('var.php');
$con = new PDO("mysql:host=$db_host;dbname=$db_dbname", $db_username, $db_password);
$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
