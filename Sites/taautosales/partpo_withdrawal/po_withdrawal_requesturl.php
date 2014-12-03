<?php
include_once("../include/config.php");
include_once("../include/function.php");
include_once("po_withdrawal_webservice.php");

$function = pg_escape_string($_REQUEST["_function"]);
$withdrawal_code = pg_escape_string($_REQUEST["code"]);

if($function == "get_parts_stock_remain_and_sum"){
	
	$parts_code = pg_escape_string($_POST["_parts_code"]);
	$return = pg_escape_string($_POST["_return"]);
	
	$class = new Withdrawal_edit_body($withdrawal_code);
	
	$return = $class->call_parts(
		$parts_code, 
		$return
	);
	
	echo $return;
}
elseif($function == "edit_body__get_partscode_autocomplete"){
	
	$parts_code = pg_escape_string($_GET["term"]);
	
	$class = new Withdrawal_edit_body($withdrawal_code);
	$return = $class->get_parts_autocomplete(
		$parts_code
	);
	echo $return;
}
elseif ($function == "edit_body__get_parts_details") {
	
	$parts_code = pg_escape_string($_POST["_parts_code"]);
	$return = pg_escape_string($_POST["_return"]);
	
	$class = new Withdrawal_edit_body($withdrawal_code);
	
	$return = $class->call_parts(
		$parts_code, 
		$return
	);
	
	echo $return;
	
}

elseif($function == "search_by_stock_code"){
	
	$parts_code = pg_escape_string($_GET["term"]);
	
	$partStock = new PartStock();
	
	$return = $partStock->search_by_stock_code($parts_code);
	echo json_encode($return);
}

elseif($function == "search_by_stockBroken_code"){
	
	$parts_code = pg_escape_string($_GET["term"]);
	
	$partStock = new PartStock();
	
	$return = $partStock->search_by_stockBroken_code($parts_code);
	echo json_encode($return);
}

elseif($function == "get_stock_detail_by_code"){
	
	$parts_code = pg_escape_string($_POST["_parts_code"]);
	
	$partStock = new PartStock();
	
	$return = $partStock->get_stock_detail_and_aval($parts_code);
	echo json_encode($return);
}
elseif($function == "get_project_detail"){
	
	$project_id = pg_escape_string($_POST["_project_id"]);
	
	$project = new Project();
	
	echo json_encode($project->get_project_detail($project_id));
}

?>