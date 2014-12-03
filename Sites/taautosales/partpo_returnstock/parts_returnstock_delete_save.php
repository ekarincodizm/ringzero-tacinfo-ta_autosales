<?php
	include_once("../include/config.php");
	include_once("../include/function.php");
	
	$return_code = pg_escape_string($_POST["return_code"]);
	$type = pg_escape_string($_POST["type"]);
	
	pg_query("BEGIN WORK");
	$status = 0;
	$txt_error = array();
	
	// ################ For Return ################
	if($type == "return"){
		// ########## Check Concurrency First ##############
		$returnParts_check_strQuery = "
			SELECT 
				return_code, 
				type, 
				user_id, 
				return_user_id, 
				date, 
				status
			FROM 
				\"ReturnParts\"
			WHERE
				status = 1 ;
		";
		$returnParts_check_query = @pg_query($returnParts_check_strQuery);
		$returnParts_check_numrow = @pg_num_rows($returnParts_check_query);
		if($returnParts_check_numrow > 0){
			// ########### Delete RetureParts ############
			$returnParts_delete_strQuery = "
				UPDATE 
					\"ReturnParts\"
				SET 
					status = 0
				WHERE 
					return_code = '".$return_code."'
				;
			";
			if(!$result=@pg_query($returnParts_delete_strQuery)){
		        $txt_error[] = "Delete ReturnParts ไม่สำเร็จ \n $returnParts_delete_strQuery";
		        $status++;
		    }
			// ######### END Delete RetureParts ##########
			
			
			// ########### Delete PartsStock ############
			$partsStock_delete_strQuery = "
				UPDATE 
					\"PartsStock\"
				SET 
					stock_status = 0
				WHERE parts_rcvcode = '".$return_code."'
				RETURNING stock_id
				;
			";
			$partsStock_delete_query = pg_query($partsStock_delete_strQuery);
			while($partsStock_delete_result = pg_fetch_array($partsStock_delete_query)) {
				
				// ########### Delete PartsStockDetail ############
				$partsStockDetail_delete_strQuery = "
					UPDATE 
						\"PartsStockDetails\"
					SET 
						status = 0
					WHERE 
						stock_id = '".$partsStock_delete_result["stock_id"]."';
					;
				";
				if(!$partsStockDetail_delete_query = @pg_query($partsStockDetail_delete_strQuery)){
			        $txt_error[] = "Delete PartsStockDetails ไม่สำเร็จ \n $partsStockDetail_delete_strQuery";
			        $status++;
			    }
				// ######### END Delete PartsStockDetail ##########
			}
			if(!$partsStock_delete_query){
		        $txt_error[] = "Delete PartsStock ไม่สำเร็จ \n $partsStock_delete_strQuery";
		        $status++;
			}
			// ######### END Delete PartsStock ##########
			
		}
		else{
			$txt_error[] = "ไม่สามารถบันทึกได้ กรุณาตรวจสอบข้อมูล";
			$status++;
		}
		// ######### END Check Concurrency First ###########
	}
	// ############## END For Return ##############
	
	
	
	// ################ For broken ################
	elseif($type == "broken"){
		// ########## Check Concurrency First ##############
		$returnParts_check_strQuery = "
			SELECT 
				broken_code, 
				type, 
				user_id, 
				broken_user_id, 
				date, 
				status
			FROM 
				\"BrokenParts\"
			WHERE
				status = 1 ;
		";
		$returnParts_check_query = @pg_query($returnParts_check_strQuery);
		$returnParts_check_numrow = @pg_num_rows($returnParts_check_query);
		if($returnParts_check_numrow > 0){
			// ########### Delete BrokenParts ############
			$returnParts_delete_strQuery = "
				UPDATE 
					\"BrokenParts\"
				SET 
					status = 0
				WHERE 
					broken_code = '".$return_code."'
				;
			";
			if(!$result=@pg_query($returnParts_delete_strQuery)){
		        $txt_error[] = "Delete ReturnParts ไม่สำเร็จ \n $returnParts_delete_strQuery";
		        $status++;
		    }
			// ######### END Delete BrokenParts ##########
			
			
			// ########### Delete PartsStockBroken ############
			$partsStockBroken_delete_strQuery = "
				UPDATE 
					\"PartsStockBroken\"
				SET 
					stock_status = 0
				WHERE parts_rcvcode = '".$return_code."'
				RETURNING stock_broken_id
				;
			";
			$partsStockBroken_delete_query = pg_query($partsStockBroken_delete_strQuery);
			while($partsStockBroken_delete_result = pg_fetch_array($partsStockBroken_delete_query)) {
				// ########### Delete PartsStockBrokenDetails ############
				$partsStockDetail_delete_strQuery = "
					UPDATE 
						\"PartsStockBrokenDetails\"
					SET 
						status = 0
					WHERE 
						stock_broken_id = '".$partsStockBroken_delete_result["stock_broken_id"]."';
					;
				";
				if(!$partsStockDetail_delete_query = @pg_query($partsStockDetail_delete_strQuery)){
			        $txt_error[] = "Delete PartsStockBrokenDetails ไม่สำเร็จ \n $partsStockDetail_delete_strQuery";
			        $status++;
			    }
				// ######### END Delete PartsStockBrokenDetails ##########
			}
			if(!$partsStockBroken_delete_query){
		        $txt_error[] = "Delete PartsStock ไม่สำเร็จ \n $partsStock_delete_strQuery";
		        $status++;
			}
			// ######### END Delete PartsStockBroken ##########
			
		}
		else{
			$txt_error[] = "ไม่สามารถบันทึกได้ กรุณาตรวจสอบข้อมูล";
			$status++;
		}
		// ######### END Check Concurrency First ###########
	}
	// ############## END For broken ##############
	
	
	// Check Is Query or Not?
	if($status == 0){
        // pg_query("ROLLBACK");
        pg_query("COMMIT");
        $data['success'] = true;
        $data['parts_pocode'] = $gen_parts_no;
    }else{
        pg_query("ROLLBACK");
        $data['success'] = false;
        $data['message'] = "$txt_error[0]";
    }
	$data['status'] = $status;
	
	echo json_encode($data);
?>