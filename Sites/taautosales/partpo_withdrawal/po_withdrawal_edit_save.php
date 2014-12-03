<?php
	include_once("../include/config.php");
	include_once("../include/function.php");
	
	include_once("po_withdrawal_webservice.php");
	
	//Load Initial HTTP Post Variables
	$id_user = $_SESSION["ss_iduser"];
	
	$withdrawal_code = pg_escape_string($_POST["withdrawal_code"]);
	
	$withdrawal_type = pg_escape_string($_POST["withdrawal_type"]);
	$withdrawal_user_id = pg_escape_string($_POST["withdrawal_user_id"]);
	$withdrawal_withdraw_user_id = pg_escape_string($_POST["withdrawal_withdraw_user_id"]);
	$withdrawal_date = date("Y-m-d", strtotime(pg_escape_string($_POST['withdrawal_date'])));
	$withdrawal_usedate = date("Y-m-d", strtotime(pg_escape_string($_POST["withdrawal_usedate"])));
	
	$project_id = pg_escape_string($_POST["project_id"]);
	$project_quantity = pg_escape_string($_POST["project_quantity"]);
	
	$withdrawal_details_array = json_decode(stripcslashes(pg_escape_string($_POST["withdrawal_details_array"])));
	$withdrawal_note = pg_escape_string($_POST["withdrawal_note"]);
	
	pg_query("BEGIN WORK");
	$status = 0;
	$txt_error = array();
	
	// ######### For Test checking the Variables #########
	
	// pg_query("ROLLBACK");
	// $data["test"] = $id_user;
	// $data["success"] = false;
	// $data["message"] = "";
	// echo json_encode($data);
	// exit;
	
	// ###################################################
	
	$nowdate = nowDate();
	$nowDateTime = nowDateTime();
	
	$withdrawalParts = new WithdrawalParts();
	
	// Run Process Create Withdrawal
	$data = $withdrawalParts->update_withdrawalParts(
		$id_user,
		$withdrawal_code,
		$withdrawal_type,
		$withdrawal_user_id,
		$withdrawal_withdraw_user_id,
		$withdrawal_date,
		$withdrawal_usedate,
		$project_id,
		$project_quantity,
		$withdrawal_details_array,
		$withdrawal_note
	);
	
	echo json_encode($data);
?>