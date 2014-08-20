<?php
include_once("../include/config.php");

$cmd = $_POST['cmd'];
$txtcarnum = trim(pg_escape_string($_POST['txtcarnum']));
$txtmarnum = trim(pg_escape_string($_POST["txtmarnum"]));

if($cmd == 'newcar'){
$sql = pg_query("SELECT car_id FROM \"Cars\" where \"car_num\" = '$txtcarnum' or \"mar_num\" = '$txtmarnum' ");						 
$row = pg_num_rows($sql);

	if($row>0){
		echo "f";
	}else{
		echo "t";
	}
}

if($cmd == 'usedcar'){
	$sql = pg_query("SELECT car_id FROM \"Cars\" where car_status <> 'S' and (\"car_num\" = '$txtcarnum' or \"mar_num\" = '$txtmarnum') ");						 
$row = pg_num_rows($sql);

	if($row>0){
		echo "f";
	}else{
		echo "t";
	}
}

?>