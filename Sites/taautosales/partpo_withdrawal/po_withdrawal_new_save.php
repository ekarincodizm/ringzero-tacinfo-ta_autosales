<?php
	include_once("../include/config.php");
	include_once("../include/function.php");
	
	//Load Initial HTTP Post Variables
	$id_user = $_SESSION["ss_iduser"];
	$withdrawal_type = pg_escape_string($_POST["withdrawal_type"]);
	$withdrawal_user_id = pg_escape_string($_POST["withdrawal_user_id"]);
	$withdrawal_withdraw_user_id = pg_escape_string($_POST["withdrawal_withdraw_user_id"]);
	$withdrawal_date = date("Y-m-d", strtotime(pg_escape_string($_POST['withdrawal_date'])));
	$withdrawal_usedate = date("Y-m-d", strtotime(pg_escape_string($_POST["withdrawal_usedate"])));
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
	
	if($withdrawal_type == 1){
		$type_X = "RLPF";
		$kny = 4;
	}
	elseif($withdrawal_type == 2){
		//Load Initial HTTP Post Variables
		$withdrawal_project_id = pg_escape_string($_POST["withdrawal_project_id"]);
		$withdrawal_project_quantity = pg_escape_string($_POST["withdrawal_project_quantity"]);
		
		$type_X = "RLPJ";
		$kny = 5;
	}
	elseif($withdrawal_type == 3){
		$type_X = "RLPW";
		$kny = 6;
	}
	
	pg_query("BEGIN WORK");
	$status = 0;
	$txt_error = array();
	
	// - เลขที่ใบเบิก 
	// - เบิกขายซ่อม : RLPF1-YYMMDDNNN 
	// - เบิกประกอบชิ้นงาน : RLPJ1-YYMMDDNNN
	// - เบิกของเสีย : RLPW1-YYMMDDNNN
	
	// YY = Year
	// MM = Month
	// DD = Day
	// NNN = Running Number
	
	// Find B
	$office_id = "";
	$B_StrQuery = "
		SELECT \"office_id\"
		FROM \"fuser\"
		WHERE \"id_user\" = '".$id_user."'
	";
	$B_query = @pg_query($B_StrQuery);
	while($B_result=@pg_fetch_array($B_query)){
		$office_id = $B_result["office_id"];
	}
	
	$nowdate = nowDate();
	$nowDateTime = nowDateTime();
	
	$generate_id_StrQuery = "
		select gen_parts_no(
			'".$withdrawal_date."', 
			'".$type_X."', 
			'".$office_id."', 
			'".$kny."'
		);
	";
	$generate_id = @pg_query($generate_id_StrQuery);
	$gen_parts_no = @pg_fetch_result($generate_id, 0);
	if(empty($gen_parts_no)){
	    $txt_error[] = "สร้าง gen_rec_no ไม่สำเร็จ $generate_id_StrQuery";
		$status++;
	}
	else{
		
	}
	
	//Query PartsReceived
	$withdrawalParts_strQuery = "
	INSERT INTO \"WithdrawalParts\"(
		code, 
		type, 
		user_id, 
		withdraw_user_id, 
		date, 
		usedate, 
		status
	";
	if($withdrawal_type == 2){
		$withdrawalParts_strQuery .= "
			,
			project_id,
			project_quantity
		";
	}
	$withdrawalParts_strQuery .= "	
	)
	VALUES (
		'{$gen_parts_no}',
		'{$withdrawal_type}',
		'{$withdrawal_user_id}',
		'{$withdrawal_withdraw_user_id}',
		'{$withdrawal_date}',
		'{$withdrawal_usedate}',
		1
	";
	if($withdrawal_type == 2){
		$withdrawalParts_strQuery .= "
				,
				{$withdrawal_project_id},
				{$withdrawal_project_quantity}
		";
	}
	$withdrawalParts_strQuery .= "
		);
	";
	if(!$result=@pg_query($withdrawalParts_strQuery)){
        $txt_error[] = "INSERT withdrawalParts_strQuery ไม่สำเร็จ $withdrawalParts_strQuery";
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
				withdrawal_quantity
			)
			VALUES
			(
				'{$gen_parts_no}',
				'".$idno."',
				'".$parts_code."',
				'".$quantity_withdrawal."'
			);
		";
		
		if(!$result=@pg_query($withdrawalPartsDetails_strQuery)){
	        $txt_error[] = "INSERT withdrawalPartsDetails ไม่สำเร็จ $withdrawalPartsDetails_strQuery";
	        $status++;
	    }
	}
	// End Query PurchaseOrderPartsDetails
	
	
	//Insert Approve
	$ApproveParts_forWithdrawal_strQuery = "
		INSERT INTO 
			\"PartsApproved\"
		(
			code, 
			user_id, 
			user_note, 
			user_timestamp, 
			appr_id, 
			appr_note, 
			appr_timestamp
		)
		VALUES
		(
			'{$gen_parts_no}',
			'{$id_user}',
			'{$withdrawal_note}',
			'{$nowDateTime}',
			NULL,
			NULL,
			NULL
		)
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
        $data['parts_pocode'] = $gen_parts_no;
    }else{
        pg_query("ROLLBACK");
        $data['success'] = false;
        $data['message'] = "ไม่สามารถบันทึกได้! $txt_error[0]";
    }
	$data['status'] = $status;
	
	echo json_encode($data);
?>