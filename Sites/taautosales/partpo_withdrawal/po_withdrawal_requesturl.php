<?php
include_once("../include/config.php");
include_once("../include/function.php");
include_once("po_withdrawal_webservice.php");

$function = pg_escape_string($_POST["_function"]);

if($function == "get_parts_stock_remain_and_sum"){
	
	$withdrawal_code = pg_escape_string($_POST["code"]);
	$parts_code = pg_escape_string($_POST["_parts_code"]);
	$return = pg_escape_string($_POST["_return"]);
	
	$class = new Withdrawal_edit_body($withdrawal_code);
	
	$return = $class->call_parts($parts_code, $return);
	
	return $return;
}
?>