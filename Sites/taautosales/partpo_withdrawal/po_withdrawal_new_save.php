<?php
	include_once("../include/config.php");
	include_once("../include/function.php");
	
	include_once("po_withdrawal_webservice.php");
	
	//Load Initial HTTP Post Variables
	$id_user = $_SESSION["ss_iduser"];
	$withdrawal_type = pg_escape_string($_POST["withdrawal_type"]);
	$withdrawal_user_id = pg_escape_string($_POST["withdrawal_user_id"]);
	$withdrawal_withdraw_user_id = pg_escape_string($_POST["withdrawal_withdraw_user_id"]);
	$withdrawal_date = date("Y-m-d", strtotime(pg_escape_string($_POST['withdrawal_date'])));
	$withdrawal_usedate = date("Y-m-d", strtotime(pg_escape_string($_POST["withdrawal_usedate"])));
	
	$withdrawal_project_id = pg_escape_string($_POST["withdrawal_project_id"]);
	$withdrawal_project_quantity = pg_escape_string($_POST["withdrawal_project_quantity"]);
	
	$withdrawal_details_array = json_decode(stripcslashes(pg_escape_string($_POST["withdrawal_details_array"])));
	$withdrawal_note = pg_escape_string($_POST["withdrawal_note"]);
	
	// ######### For Test checking the Variables #########
	
	// pg_query("ROLLBACK");
	// $data["test"] = $id_user;
	// $data["success"] = false;
	// $data["message"] = "";
	// echo json_encode($data);
	// exit;
	
	// ###################################################
	
	$withdrawalParts = new WithdrawalParts();
	
	// Run Process Create Withdrawal
	$data = $withdrawalParts->create_withdrawalParts(
		$id_user,
		$withdrawal_type,
		$withdrawal_user_id,
		$withdrawal_withdraw_user_id,
		$withdrawal_date,
		$withdrawal_usedate,
		$withdrawal_project_id,
		$withdrawal_project_quantity,
		$withdrawal_details_array,
		$withdrawal_note
	);
	
	echo json_encode($data);
?>