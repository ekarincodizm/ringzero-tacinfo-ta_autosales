<?php
include_once("../include/config.php");
include_once("../include/function.php");
include_once("parts_returnstock_webservice.php");

$function = pg_escape_string($_REQUEST["_function"]);
$withdrawal_code = pg_escape_string($_REQUEST["code"]);

if($function == "search_by_stock_code"){
	
	$parts_code = pg_escape_string($_GET["term"]);
	$type = pg_escape_string($_GET["_type"]);
	
	$sendParts = new SendParts();
	
	$return = $sendParts->search_by_stock_code($parts_code);
	echo json_encode($return);
}
elseif($function == "get_parts_detail"){
	
	$parts_code = pg_escape_string($_POST["_parts_code"]);
	
	$sendParts = new SendParts();
	
	$return = $sendParts->get_parts_detail($parts_code);
	echo json_encode($return);
}

?>