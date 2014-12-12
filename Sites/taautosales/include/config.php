<?php
include_once("config_session.php");

$host = "172.16.2.251";
$port = 5432;
//$db = "devtaautosales";
$db = "devtaauto012";
// $db = "devtaauto016";
//$db = "dbtaauto001";  //ใช้งานจริง
$user = "dev";
$pass = "nextstep";
$company_name = "TA Autosales Co.,Ltd.";
$company_vat = 7;
$conn = "host=$host port=$port dbname=$db user=$user password=$pass";
$db_connect = pg_connect($conn) or die("CAN'T CONNECT !");
?>
