<?php
	include_once("../include/config.php");
	include_once("../include/function.php");
	
	//Load Initial HTTP Post Variables
	$id_user = $_SESSION["ss_iduser"];
	
	$withdrawal_code = pg_escape_string($_POST["withdrawal_code"]);
	
	// $withdrawal_type = pg_escape_string($_POST["withdrawal_type"]);
	$withdrawal_user_id = pg_escape_string($_POST["withdrawal_user_id"]);
	$withdrawal_withdraw_user_id = pg_escape_string($_POST["withdrawal_withdraw_user_id"]);
	$withdrawal_date = date("Y-m-d", strtotime(pg_escape_string($_POST['withdrawal_date'])));
	$withdrawal_usedate = date("Y-m-d", strtotime(pg_escape_string($_POST["withdrawal_usedate"])));
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
	
	
	//Query record PartsReceived
	$withdrawalParts_strQuery = "
		UPDATE \"WithdrawalParts\"
		SET
			user_id='{$withdrawal_user_id}',
			withdraw_user_id='{$withdrawal_withdraw_user_id}',
			date='{$withdrawal_date}',
			usedate='{$withdrawal_usedate}',
			status=1
		WHERE 
			code='{$withdrawal_code}';
	";
	
	if(!$result=@pg_query($withdrawalParts_strQuery)){
        $txt_error[] = "INSERT withdrawalParts_strQuery ไม่สำเร็จ $withdrawalParts_strQuery";
        $status++;
    }
	
	// Delete Old Parts
	$withdrawalPartsDetails_strQuery = "
		UPDATE 
			\"WithdrawalPartsDetails\"
		SET 
			status = 0
		WHERE 
			withdrawal_code = '".$withdrawal_code."' ;
	";
	if(!$result=@pg_query($withdrawalPartsDetails_strQuery)){
        $txt_error[] = "INSERT withdrawalPartsDetails ไม่สำเร็จ $withdrawalPartsDetails_strQuery";
        $status++;
    }
	
    //Query PurchaseOrderPartsDetails
    foreach($withdrawal_details_array as $key => $value){
    	
		$idno = $value->idno;
		$parts_code = $value->parts_code;
		$quantity_withdrawal = $value->quantity_withdrawal;
		
		
		// insert PartsReceivedDetails
		$withdrawalPartsDetails_strQuery = "
			INSERT INTO \"WithdrawalPartsDetails\"
			(
				withdrawal_code, 
				idno, 
				parts_code, 
				withdrawal_quantity,
				status
			)
			VALUES
			(
				'$withdrawal_code',
				'$idno',
				'$parts_code',
				'$quantity_withdrawal',
				1
			)
		";
		
		if(!$result=@pg_query($withdrawalPartsDetails_strQuery)){
	        $txt_error[] = "INSERT withdrawalPartsDetails ไม่สำเร็จ $withdrawalPartsDetails_strQuery";
	        $status++;
	    }
	}
	// End Query PurchaseOrderPartsDetails
	
	
	//Insert Approve
	$ApproveParts_forWithdrawal_strQuery = "
		UPDATE 
			\"PartsApproved\"
		SET
			user_id = '{$id_user}',
			user_note = '{$withdrawal_note}',
			user_timestamp = '{$nowDateTime}'
		WHERE
			code = '{$withdrawal_code}'
	";
	if(!$result=@pg_query($ApproveParts_forWithdrawal_strQuery)){
        $txt_error[] = "INSERT ApproveParts_forWithdrawal_strQuery ไม่สำเร็จ {$ApproveParts_forWithdrawal_strQuery}";
        $status++;
    }
		
	
	// Check Is Query or Not?
	if($status == 0){
        // pg_query("ROLLBACK");
        pg_query("COMMIT");
        $data['success'] = true;
        $data['parts_pocode'] = $withdrawal_code;
    }else{
        pg_query("ROLLBACK");
        $data['success'] = false;
        $data['message'] = "ไม่สามารถบันทึกได้! $txt_error[0]";
    }
	
	echo json_encode($data);
?>