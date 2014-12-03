<?php
	include_once("../include/config.php");
	include_once("../include/function.php");
	
	//Load Initial HTTP Post Variables
	$id_user = $_SESSION["ss_iduser"];
	$withdrawal_code= pg_escape_string($_POST["withdrawal_code"]);
	$set_status = pg_escape_string($_POST["set_status"]);
	$appr_note = pg_escape_string($_POST["appr_note"]);
	
	pg_query("BEGIN WORK");
	$status = 0;
	$txt_error = array();
	
	$nowDateTime = nowDateTime();
	
	// ######## Select that there are NO WithdrawalParts insert before (Test concurrency Pass) ########
	$test_concurrency_strQuery = "
		SELECT *
		FROM
			\"WithdrawalParts\"
		WHERE 
			code = '".$withdrawal_code."'
			AND
			status = ".$set_status."
		;
	";
	$test_concurrency_query = @pg_query($test_concurrency_strQuery);
	$test_concurrency_numrow = @pg_num_rows($test_concurrency_query); 
	if($test_concurrency_numrow == 0){
		
		//Query PartsReceived
		$withdrawalParts_strQuery = "
			UPDATE 
				\"WithdrawalParts\"
			SET 
				status = ".$set_status."
			WHERE code = '".$withdrawal_code."';
		";
		
		if(!$result=@pg_query($withdrawalParts_strQuery)){
	        $txt_error[] = "INSERT withdrawalParts ไม่สำเร็จ $withdrawalParts_strQuery";
	        $status++;
	    }
		
		// ###### Insert Approve กลับ ######
		$ApproveParts_forWithdrawal_strQuery = "
			UPDATE
				\"PartsApproved\"
			SET
				appr_id = '".$id_user."', 
				appr_note = '".$appr_note."', 
				appr_timestamp = '".$nowDateTime."'
			WHERE
				code = '".$withdrawal_code."' ;
		";
		
		if(!$result = @pg_query($ApproveParts_forWithdrawal_strQuery)){
	        $txt_error[] = "UPDATE ApproveParts_forWithdrawal_strQuery ไม่สำเร็จ ".$ApproveParts_forWithdrawal_strQuery;
	        $status++;
	    }
	}
	
	// ######## Select that there are WithdrawalParts insert before (Test concurrency False) ########
	else{
		$txt_error[] = "ไม่สามารถทำรายการได้ เลขที่ขอเบิก : ".$withdrawal_code." มีการอนุมัติการเบิกสินค้าออกสต๊อกแล้ว";
        $status++;
	}
	
	
	// Check Is Query or Not?
	if($status == 0){
        // pg_query("ROLLBACK");
        pg_query("COMMIT");
        $data['success'] = true;
        $data['message'] = pass;
    }else{
        pg_query("ROLLBACK");
        $data['success'] = false;
        $data['message'] = "ไม่สามารถบันทึกได้! $txt_error[0]";
    }
	
	echo json_encode($data);
?>