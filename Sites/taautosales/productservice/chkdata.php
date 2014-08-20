<?php
include_once("../include/config.php");

$txtalpha = trim($_POST['txtalpha']);


$sql = pg_query("SELECT alphas from \"Venders\" where \"alphas\" = '$txtalpha' ");						 
$row = pg_num_rows($sql);

	if($row>0){
		echo "f";
	}else{
		echo "t";
	}
?>