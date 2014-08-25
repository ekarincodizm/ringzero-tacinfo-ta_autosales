<?php
	include_once("../include/config.php");
	include_once("../include/function.php");
	
	//Load Initial HTTP Post Variables
	$UserID = $_SESSION["ss_iduser"];
	$parts_pocode = pg_escape_string($_POST["parts_pocode"]);
	$approve_status = pg_escape_string($_POST["approve_status"]);
	$appr_note = pg_escape_string($_POST["appr_note"]);
	$appr_timestamp = nowDateTime();
	
	
	pg_query("BEGIN WORK");
	$status = 0;
	$txt_error = array();
	
	
	$PartsApproved_StrQuery = "
		UPDATE
			\"PartsApproved\"
		SET
			\"appr_id\" = '{$UserID}',
			\"appr_note\" = '{$appr_note}',
			\"appr_timestamp\" = '{$appr_timestamp}'
		WHERE
			\"code\" = '{$parts_pocode}'
	";
	if(!$PartsApproved_query = @pg_query($PartsApproved_StrQuery)){
        $txt_error[] = "UPDATE PartsApproved ไม่สำเร็จ $PartsApproved_StrQuery";
        $status++;
    }
	
	
	$PurchaseOrderPart_StrQuery = "
		UPDATE
			\"PurchaseOrderPart\"
		SET
			\"status\" = '{$approve_status}'
		WHERE
			\"parts_pocode\" = '{$parts_pocode}'
	";
	if(!$PurchaseOrderPart_query = @pg_query($PurchaseOrderPart_StrQuery)){
        $txt_error[] = "UPDATE PurchaseOrderPart ไม่สำเร็จ $PurchaseOrderPart_StrQuery";
        $status++;
    }
	
	
		
	/*
	$PartsApproved_StrQuery = "
		SELECT 
			\"code\"
		FROM 
			\"PartsApproved\"
		WHERE 
			\"code\" = '".$parts_pocode."'
			AND
			\"appr_id\" = '".$UserID."'
	";
	$PartsApproved_query = @pg_query($PartsApproved_StrQuery);
	if($PartsApproved_result = @pg_fetch_array($PartsApproved_query)){
		
	}
	else{
		$PartsApproved_StrQuery_record = "
			INSERT INTO 
				\"PartsApproved\"
			(
				\"code\",
				\"appr_id\",
				\"appr_note\",
				\"appr_timestamp\"
			)
			VALUES
			(
				'{$UserID}',
				'{$appr_note}',
				'{$appr_timestamp}'
			)
				
				
			SET
				\"appr_note\" = '{$appr_note}',
				\"appr_id\" = '{$UserID}',
				\"appr_timestamp\" = '{$appr_timestamp}'
			WHERE
				\"code\" = '{$parts_pocode}'
		";
		if(!$PartsApproved_query = @pg_query($PartsApproved_StrQuery)){
	        $txt_error[] = "UPDATE PartsApproved ไม่สำเร็จ $PartsApproved_StrQuery_record";
	        $status++;
	    }
	}
	*/
	
	
	
	// ######### For Test checking the Variables #########
	
	// $data["test"] = date("m-d-Y", strtotime($date));
	// $data["success"] = false;
	// $data["message"] = "";
	// echo json_encode($data);
	// exit;
	
	// ###################################################
    
	
	//Check Is Query or Not?
	if($status == 0){
        //pg_query("ROLLBACK");
        pg_query("COMMIT");
        $data['success'] = true;
        $data['message'] = "บันทึกเรียบร้อย";
    }else{
        pg_query("ROLLBACK");
        $data['success'] = false;
        $data['message'] = "ไม่สามารถบันทึกได้! $txt_error[0]";
    }
	
	// $data["test"] = $B_query;
	
	echo json_encode($data);
?>