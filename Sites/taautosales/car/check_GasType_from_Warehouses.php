<?php
include_once("../include/config.php");

$wh_id = pg_escape_string($_POST["wh_id"]);

// หากลุ่มคลัง
$qry_wh_group = pg_query("select \"wh_group\" from \"Warehouses\" where \"wh_id\" = '$wh_id' ");
$wh_group = pg_fetch_result($qry_wh_group,0);

echo $wh_group;
?>