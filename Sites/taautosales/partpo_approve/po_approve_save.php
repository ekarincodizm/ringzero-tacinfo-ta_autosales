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
	
	//ต้อง Check ก่อนว่า Parts_pocode ตัวนั้น ได้ทำการ Update ไปก่อนหน้าหรือไม่
	$check_isApprove_strQuery = "
		SELECT 
			parts_pocode, date, type, copypo_id, credit_terms, app_sentpartdate, 
			esm_paydate, vender_id, vat_status, subtotal, pcdiscount, discount, 
			bfv_total, pcvat, vat, nettotal, status, paid
		FROM 
			\"PurchaseOrderPart\"
		WHERE	
			parts_pocode = '".$parts_pocode."'
			AND
			status = 1
		;
	";
	$check_isApprove_query = @pg_query($check_isApprove_strQuery);
	if($check_isApprove_result = @pg_fetch_array($check_isApprove_query)){
	//ถ้า Check แล้ว ไม่ concurrency
		
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
		
		
	}
	else{
	//ถ้า Check แล้ว concurrency
		$txt_error[] = "ผู้ใช้ก่อนหน้านี้ ได้ทำรายการนี้ไปเรียบร้อยแล้ว";
        $status++;
	}
	
	// ######### For Test checking the Variables #########
	
	// $data["test"] = date("m-d-Y", strtotime($date));
	// $data["success"] = false;
	// $data["message"] = "";
	// echo json_encode($data);
	// exit;
	
	// ###################################################
    
	
	//Check Is Query or Not?
	if($status == 0){
        // pg_query("ROLLBACK");
        pg_query("COMMIT");
        $data['success'] = true;
        $data['message'] = "บันทึกเรียบร้อย";
    }else{
        pg_query("ROLLBACK");
        $data['success'] = false;
        $data['message'] = "ไม่สามารถบันทึกได้! $txt_error[0]";
    }
	
	echo json_encode($data);
?>