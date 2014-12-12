<?php

/**
 * New Withdrawal Save Process
 */
class WithdrawalParts {
	
	function __construct($parameter = '') {
		
	}
	
	/*
	 * สร้างใบเบิก
	 * 
	 * Initial Variables : 
	 * 
	 * $withdrawal_details_array คือ สินค้า ทุกๆ Records
	 * 
	 * */
	function create_withdrawalParts(
		$id_user = '',
		$withdrawal_type = '',
		$withdrawal_user_id = '',
		$withdrawal_withdraw_user_id = '',
		$withdrawal_date = '',
		$withdrawal_usedate = '',
		$withdrawal_project_id = '',
		$withdrawal_project_quantity = '',
		$withdrawal_details_array = '',
		$withdrawal_note = ''
	)
	{
		pg_query("BEGIN WORK");
		$status = 0;
		$txt_error = array();
		
		$nowdate = nowDate();
		$nowDateTime = nowDateTime();
		
		$gen_parts_no = $this->_create_withdrawal__gen_parts_no(
			$nowdate, 
			$nowDateTime,
			$withdrawal_type,
			$id_user,
			$withdrawal_date
		);
		if($gen_parts_no["success"] == false){
			$txt_error[] = $gen_parts_no["txt_error"];
			$status++;
		}
		
		$insert_PartsReceived = $this->_create_withdrawal__insert_WithdrawalParts(
			$gen_parts_no["result"],
			$withdrawal_type,
			$withdrawal_user_id,
			$withdrawal_withdraw_user_id,
			$withdrawal_date,
			$withdrawal_usedate,
			$withdrawal_project_id,
			$withdrawal_project_quantity
		);
		
		if($insert_PartsReceived["success"] == FALSE){
			$txt_error[] = $insert_PartsReceived["txt_error"];
			$status++;
		}
		
		$purchaseOrderPartsDetails = $this->_create_withdrawal__insert_WithdrawalPartsDetails(
			$withdrawal_details_array,
			$gen_parts_no["result"],
			$withdrawal_type
		);
		
		if($purchaseOrderPartsDetails["success"] == FALSE){
			$txt_error[] = $purchaseOrderPartsDetails["txt_error"];
			$status++;
		}
		
		// echo $status;
		// pg_query("ROLLBACK");
		// exit;
		
		$approveParts_forWithdrawal = $this->_create_withdrawal__approveParts_forWithdrawal(
			$gen_parts_no["result"],
			$id_user,
			$withdrawal_note,
			$nowDateTime
		);
		
		if($approveParts_forWithdrawal["success"] == FALSE){
			$txt_error[] = $approveParts_forWithdrawal["txt_error"];
			$status++;
		}
		
		$success_query = $this->_withdrawal__success_query(
			$status,
			$gen_parts_no["result"],
			$txt_error[0]
		);
		
		return $success_query;
	}

	private function _create_withdrawal__gen_parts_no (
		$nowdate = '', 
		$nowDateTime = '',
		$withdrawal_type = '',
		$id_user = '',
		$withdrawal_date = ''
	)
	{
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
			return array(
				"success" => false,
				"result" => "",
				"txt_error" => "สร้าง gen_rec_no ไม่สำเร็จ $generate_id_StrQuery"
			);
		}
		else{
			return array(
				"success" => true,
				"result" => $gen_parts_no
			); 
		}
		
	}
	
	private function _create_withdrawal__insert_WithdrawalParts (
		$gen_parts_no = '',
		$withdrawal_type = '',
		$withdrawal_user_id = '',
		$withdrawal_withdraw_user_id = '',
		$withdrawal_date = '',
		$withdrawal_usedate = '',
		$withdrawal_project_id = '',
		$withdrawal_project_quantity = ''
	) 
	{
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
			
			return array(
				"success" => FALSE,
				"result" => "",
				"txt_error" => "INSERT withdrawalParts_strQuery ไม่สำเร็จ $withdrawalParts_strQuery"
			);
			
	    }
		else{
			return array(
				"success" => TRUE,
				"result" => "",
				"txt_error" => ""
			);
		}
	}
	
	
	//Query PurchaseOrderPartsDetails
	private function _create_withdrawal__insert_WithdrawalPartsDetails(
		$withdrawal_details_array = '',
		$gen_parts_no = '',
		$withdrawal_type = ''
	){
		$status = 0;
		
	    foreach($withdrawal_details_array as $key => $value){
	    	
			$idno = $value->idno;
			$parts_code = $value->parts_code;
			$quantity_withdrawal = $value->quantity_withdrawal;
			
			
			// ############## Check Is_Exist_partscode_in_partsStock ? ################
			if($this->_Is_Exist_partscode_in_partsStock($parts_code, $withdrawal_type, $quantity_withdrawal) == TRUE){
				
				
				
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
			        $txt_error .= "INSERT withdrawalPartsDetails ไม่สำเร็จ $withdrawalPartsDetails_strQuery \n";
			        $status++;
		   	 	}
			}
			else{
				$txt_error .= "INSERT withdrawalPartsDetails ไม่สำเร็จ : กรุณาตรวจสอบจำนวนสินค้า\n";
				$status++;
			}
		}
		// End Query PurchaseOrderPartsDetails
		
		if($status != 0){
			return array(
				"success" => FALSE,
				"result" => "",
				"txt_error" => $txt_error
			);
		}
		else{
			return array(
				"success" => TRUE,
				"result" => "",
				"txt_error" => ""
			);
		}
	}

	private function _Is_Exist_partscode_in_partsStock(
		$parts_code = '',
		$withdrawal_type = '',
		$quantity_withdrawal = ''
	)
	{
		$partStock = new PartStock();
		
		if($withdrawal_type == 1 || $withdrawal_type == 2){
			$return = $partStock->get_stock_detail_and_aval($parts_code);
		}
		else if($withdrawal_type == 3){
			$return = $partStock->get_stock_broken_detail_and_aval($parts_code);
		}
		
		if(intval($return["stock_aval"]) > 0 && intval($return["stock_aval"]) >= intval($quantity_withdrawal)){
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	
	private function _create_withdrawal___select_PartsStock_and_PartsStockDetails($parts_code = '')
	{
		$strQuery = "
			SELECT 
				\"PartsStock\".stock_id, 
				\"PartsStock\".parts_code, 
				\"PartsStockDetails\".codeid,
				\"PartsStock\".stock_lot, 
				\"PartsStock\".stock_remain, 
				stock_status, 
				parts.type
			FROM 
				\"PartsStock\"
			JOIN 
				parts
				ON parts.code = \"PartsStock\".parts_code
			LEFT JOIN 
				\"PartsStockDetails\"
				ON \"PartsStockDetails\".stock_id::text = \"PartsStock\".stock_id::text
			WHERE
				parts_code = '".$parts_code."'
				OR
					\"PartsStockDetails\".codeid = '".$parts_code."'
			;
		";
		
	}
	
	private function _create_withdrawal__approveParts_forWithdrawal(
		$gen_parts_no = '',
		$id_user = '',
		$withdrawal_note = '',
		$nowDateTime = ''
	){
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
			
			return array(
				"success" => FALSE,
				"result" => "",
				"txt_error" => "INSERT ApproveParts_forWithdrawal_strQuery ไม่สำเร็จ {$ApproveParts_forWithdrawal_strQuery}"
			);
	    }
		else{
			return array(
				"success" => TRUE,
				"result" => "",
				"txt_error" => ""
			);
		}
	}
	
	function update_withdrawalParts(
		$id_user = '',
		$withdrawal_code = '',
		$withdrawal_type = '',
		$withdrawal_user_id = '',
		$withdrawal_withdraw_user_id = '',
		$withdrawal_date = '',
		$withdrawal_usedate = '',
		$withdrawal_project_id = '',
		$withdrawal_project_quantity = '',
		$withdrawal_details_array = '',
		$withdrawal_note = ''
	)
	{
		pg_query("BEGIN WORK");
		$status = 0;
		$txt_error = array();
		
		$nowdate = nowDate();
		$nowDateTime = nowDateTime();
		
		$update_WithdrawalParts = $this->_update_withdrawal__update_WithdrawalParts(
			$withdrawal_code,
			$withdrawal_type,
			$withdrawal_user_id,
			$withdrawal_withdraw_user_id,
			$withdrawal_date,
			$withdrawal_usedate,
			$withdrawal_project_quantity
		);
		
		if($update_WithdrawalParts["success"] == FALSE){
			$txt_error[] = $update_WithdrawalParts["txt_error"];
			$status++;
		}
		
		$delete_old_withdrawalPartsDetails = $this->_Delete_old_WithdrawalPartsDetails(
			$withdrawal_code
		);
		if($delete_old_withdrawalPartsDetails["success"] == FALSE){
			$txt_error[] = $delete_old_withdrawalPartsDetails["txt_error"];
			$status++;
		}
		
		// // echo $status;
		// var_dump($delete_old_withdrawalPartsDetails);
		// pg_query("ROLLBACK");
		// exit;
		
		
		$insert_withdrawalPartsDetails = $this->_update_withdrawal__insert_WithdrawalPartsDetails(
			$withdrawal_details_array,
			$withdrawal_code,
			$withdrawal_type
		);
		
		if($insert_withdrawalPartsDetails["success"] == FALSE){
			$txt_error[] = $insert_withdrawalPartsDetails["txt_error"];
			$status++;
		}
		
		$approveParts_forWithdrawal = $this->_update_withdrawal__approveParts_forWithdrawal(
			$withdrawal_code,
			$id_user,
			$withdrawal_note,
			$nowDateTime
		);
		
		if($approveParts_forWithdrawal["success"] == FALSE){
			$txt_error[] = $approveParts_forWithdrawal["txt_error"];
			$status++;
		}
		
		$success_query = $this->_withdrawal__success_query(
			$status,
			$withdrawal_code,
			$txt_error[0]
		);
		
		// // echo $status;
		// var_dump($success_query);
		// pg_query("ROLLBACK");
		// exit;
		
		return $success_query;
	}
	
	private function _update_withdrawal__update_WithdrawalParts (
		$withdrawal_code = '',
		$withdrawal_type = '',
		$withdrawal_user_id = '',
		$withdrawal_withdraw_user_id = '',
		$withdrawal_date = '',
		$withdrawal_usedate = '',
		$withdrawal_project_quantity = ''
	) 
	{
		$withdrawalParts_strQuery = "
			UPDATE \"WithdrawalParts\"
			SET
				user_id='{$withdrawal_user_id}',
				withdraw_user_id='{$withdrawal_withdraw_user_id}',
				date='{$withdrawal_date}',
				usedate='{$withdrawal_usedate}',
				status=1
		";
		
		if($withdrawal_type == 2){
			
			$withdrawalParts_strQuery .= "
				,
				project_quantity = {$withdrawal_project_quantity}
			";
		}
				
		$withdrawalParts_strQuery .= "
			WHERE 
				code='{$withdrawal_code}';
		";
		
		if(!$result=@pg_query($withdrawalParts_strQuery)){
			
			return array(
				"success" => FALSE,
				"result" => "",
				"txt_error" => "UPDATE withdrawalParts_strQuery ไม่สำเร็จ $withdrawalParts_strQuery"
			);
			
	    }
		else{
			return array(
				"success" => TRUE,
				"result" => "",
				"txt_error" => ""
			);
		}
	}
	
	private function _Delete_old_WithdrawalParts(
		$withdrawal_code = ''
	)
	{
		$withdrawalParts_strQuery = "
			UPDATE \"WithdrawalParts\"
			SET 
				status = 0
			WHERE 
				withdrawal_code = '".$withdrawal_code."' ;
			;
		";
		if(!$result=@pg_query($withdrawalParts_strQuery)){
			
	    	return array(
				"success" => FALSE,
				"result" => "",
				"txt_error" => "DELETE withdrawalParts_strQuery ไม่สำเร็จ $withdrawalParts_strQuery"
			);
			
	    }
		else{
			return array(
				"success" => TRUE,
				"result" => "",
				"txt_error" => ""
			);
		}
	}
	
	private function _Delete_old_WithdrawalPartsDetails (
		$withdrawal_code = ''
	) 
	{
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
			
	    	return array(
				"success" => FALSE,
				"result" => "",
				"txt_error" => "DELETE withdrawalPartsDetail_strQuery ไม่สำเร็จ $withdrawalPartsDetails_strQuery"
			);
			
	    }
		else{
			
			// $sql = "select *
				// from \"WithdrawalPartsDetails\"
				// where withdrawal_code = '".$withdrawal_code."' ;
			// ";
			// $resulttt = pg_fetch_all(pg_query($sql));
			// var_dump($resulttt);
			// pg_query("ROLLBACK");
			// exit;
			
			return array(
				"success" => TRUE,
				"result" => "",
				"txt_error" => ""
			);
		}
	}
	
	private function _Delete_revert_to_PartsStock_and_PartsStockDetail(
		
	){
		
	}
	
	//Query PurchaseOrderPartsDetails
	private function _update_withdrawal__insert_WithdrawalPartsDetails(
		$withdrawal_details_array = '',
		$withdrawal_code = '',
		$withdrawal_type = ''
	){
		$status = 0;
		
		
	    foreach($withdrawal_details_array as $key => $value){
	    	
			$idno = $value->idno;
			$parts_code = $value->parts_code;
			$quantity_withdrawal = $value->quantity_withdrawal;
			
			
			// echo $this->_Is_Exist_partscode_in_partsStock($parts_code);
			// pg_query("ROLLBACK");
			// exit;
			
			
			// ############## Check Is_Exist_partscode_in_partsStock ? ################
			if($this->_Is_Exist_partscode_in_partsStock($parts_code, $withdrawal_type, $quantity_withdrawal) == TRUE){
				
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
					);
				";
				
				if(!$result=@pg_query($withdrawalPartsDetails_strQuery)){
			        $txt_error .= "INSERT withdrawalPartsDetails ไม่สำเร็จ $withdrawalPartsDetails_strQuery \n";
			        $status++;
		   	 	}
			}
			else{
				$txt_error .= "INSERT withdrawalPartsDetails ไม่สำเร็จ : กรุณาตรวจสอบจำนวนสินค้า \n";
				$status++;
			}
			
			// echo $this->_Is_Exist_partscode_in_partsStock($parts_code);
			// var_dump($txt_error);
			// pg_query("ROLLBACK");
			// exit;
		}
		// End Query PurchaseOrderPartsDetails
		
		if($status != 0){
			return array(
				"success" => FALSE,
				"result" => "",
				"txt_error" => $txt_error
			);
		}
		else{
			return array(
				"success" => TRUE,
				"result" => "",
				"txt_error" => ""
			);
		}
	}
	
	
	private function _update_withdrawal__approveParts_forWithdrawal(
		$withdrawal_code = '',
		$id_user = '',
		$withdrawal_note = '',
		$nowDateTime = ''
	){
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
			
			return array(
				"success" => FALSE,
				"result" => "",
				"txt_error" => "INSERT ApproveParts_forWithdrawal_strQuery ไม่สำเร็จ {$ApproveParts_forWithdrawal_strQuery}"
			);
	    }
		else{
			return array(
				"success" => TRUE,
				"result" => "",
				"txt_error" => ""
			);
		}
	}
	
	function delete_withdrawalParts(
		$id_user = '',
		$withdrawal_code = ''
	)
	{
		pg_query("BEGIN WORK");
		$status = 0;
		$txt_error = array();
		
		
		$delete_old_withdrawalParts = $this->_Delete_old_WithdrawalParts(
			$withdrawal_code
		);
		if($delete_old_withdrawalParts["success"] == FALSE){
			$txt_error[] = $delete_old_withdrawalParts["txt_error"];
			$status++;
		}
		
		
		$delete_old_withdrawalPartsDetails = $this->_Delete_old_WithdrawalPartsDetails(
			$withdrawal_code
		);
		if($delete_old_withdrawalPartsDetails["success"] == FALSE){
			$txt_error[] = $delete_old_withdrawalPartsDetails["txt_error"];
			$status++;
		}
	}
	
	private function _withdrawal__success_query (
		$status = '',
		$gen_parts_no = '',
		$txt_error = ''
	)
	{
		// Check Is Query or Not?
		if($status == 0){
			// pg_query("ROLLBACK");
			pg_query("COMMIT");
			$data['success'] = true;
			$data['parts_pocode'] = $gen_parts_no;
			$data['message'] = "";
	    }else{
			pg_query("ROLLBACK");
			$data['success'] = false;
			$data['message'] = "ไม่สามารถบันทึกได้! ".$txt_error;
		}
		$data['status'] = $status;
		return $data;
	}
}
?>