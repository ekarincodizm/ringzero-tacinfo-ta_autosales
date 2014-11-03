<?php
	include_once("../include/config.php");
	include_once("../include/function.php");
	
	//Load Initial HTTP Post Variables
	$id_user = $_SESSION["ss_iduser"];
	$return_type = pg_escape_string($_POST["return_type"]);
	$return_user_id = pg_escape_string($_POST["return_user_id"]);
	$return_return_user_id = pg_escape_string($_POST["return_return_user_id"]);
	$return_date = date("Y-m-d", strtotime(pg_escape_string($_POST['return_date'])));
	$return_details_array = json_decode(stripcslashes(pg_escape_string($_POST["return_details_array"])));
	$return_note = pg_escape_string($_POST["return_note"]);
	
	
	// ######### For Test checking the Variables #########
	
	// pg_query("ROLLBACK");
	// $data["test"] = $id_user;
	// $data["success"] = false;
	// $data["message"] = "";
	// echo json_encode($data);
	// exit;
	
	// ###################################################
	
	if($return_type == 1){
		$type_X = "RTST";
		$kny = 11;
	}
	elseif($return_type == 2){
		$type_X = "RTBK";
		$kny = 12;
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
			'".$return_date."', 
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
	
	if($return_type == 1){ //คืนของเข้าสต๊อก
		
		$returnParts_strQuery = "
			INSERT INTO \"ReturnParts\"(
				return_code, 
				type, 
				user_id, 
				return_user_id, 
				date, 
				status, 
				note, 
				return_date
			)
			VALUES (
				'".$gen_parts_no."', 
				".$return_type.", 
				'".$return_user_id."', 
				'".$return_return_user_id."', 
				'".$return_date."', 
				1, 
				'".$return_note."', 
				'".$nowDateTime."'
			);
		";
		
		if(!$result=@pg_query($returnParts_strQuery)){
	        $txt_error[] = "INSERT ReturnParts ไม่สำเร็จ $returnParts_strQuery";
	        $status++;
	    }
		
		foreach ($return_details_array as $value) {
			$idno = $value->idno;
			$parts_type = $value->parts_type;
			$parts_code = $value->parts_code;
			$quantity_return = $value->quantity_return;
			$wh_id = $value->wh_id;
			$locate_id = $value->locate_id;
			
			
			$returnPartsDetails_strQuery = "
				INSERT INTO \"ReturnPartsDetails\"
				(
					return_code, 
					idno, 
					parts_code, 
					return_quantity
				)
				VALUES
				(
					'{$gen_parts_no}',
					'".$idno."',
					'".$parts_code."',
					'".$quantity_return."'
				);
			";
			
			if(!$result=@pg_query($returnPartsDetails_strQuery)){
		        $txt_error[] = "INSERT returnPartsDetails ไม่สำเร็จ $returnPartsDetails_strQuery";
		        $status++;
		    }
			
			
			// ######### INSERT partsStock ##########
			$partsStock_check_strQuery = "
				SELECT 
					parts_code,
					MAX(stock_lot) AS stock_lot
				FROM 
					\"PartsStock\"
				WHERE 
					parts_code = '".$parts_code."' 
				group by parts_code ;
			";
			$partsStock_check_query = pg_query($partsStock_check_strQuery);
			if($partsStock_check_result = pg_fetch_array($partsStock_check_query)){
				$partsStock_strQuery = "
					INSERT INTO \"PartsStock\"
					(
						parts_code, 
						stock_lot, 
						parts_rcvcode, 
						rcv_date, 
						rcv_quantity, 
						costperunit, 
						stock_remain, 
						wh_id, 
						locate_id, 
						stock_status
					)
					VALUES
					(
						'{$parts_code}',
						".($partsStock_check_result["stock_lot"] + 1).",
						'{$gen_parts_no}',
						'{$return_date}',
						'{$quantity_return}',
						null,
						'{$quantity_return}',
						'{$wh_id}',
						'{$locate_id}',
						'1'
					)
					RETURNING stock_id;
				";
			}
			else{
				
				//insert the PartsStock when there are no parts_code in PartsStock
				$partsStock_strQuery = "
					INSERT INTO \"PartsStock\"
					(
						parts_code, 
						stock_lot, 
						parts_rcvcode, 
						rcv_date, 
						rcv_quantity, 
						costperunit, 
						stock_remain, 
						wh_id, 
						locate_id, 
						stock_status
					)
					VALUES
					(
						'{$parts_code}',
						'1',
						'{$gen_parts_no}',
						'{$return_date}',
						'{$quantity_return}',
						null,
						'{$quantity_return}',
						'{$wh_id}',
						'{$locate_id}',
						'1'
					)
					RETURNING stock_id;
				";
			}
			$partsStock_query = @pg_query($partsStock_strQuery);
			
			// echo $partsStock_strQuery;
				// pg_query("ROLLBACK");
				// exit;
			
			if($partsStock_result = @pg_fetch_array($partsStock_query)) {
				$i = 0; //For running number
				
				// Check That, Type PO is 1 or not, if yes, will insert PartsStockDetails each item.
				if($parts_type == 1){
					
					// Check that parts_code is parts.type = 1 or not, if yes, Query Insert PartStockDetails
					$parts_check_type_strQuery = "
						SELECT
							parts.type
						FROM
							parts
						WHERE 
							parts.code = '{$parts_code}'
							AND
							parts.type = 1
					";
					$parts_check_type_query = @pg_query($parts_check_type_strQuery);
					if(@pg_fetch_result($parts_check_type_query, 0) == 1){
						
						// insert PartsStockDetails (each row of item)
						for($i = 0; $i < $rcv_quantity; $i++){
							$item_count_strQuery = "
								UPDATE
									\"parts\"
								SET
									\"item_count\" = \"item_count\" + 1
								WHERE
									code = '".$parts_code."'
								RETURNING \"item_count\" ;
							";
							$item_count_query = @pg_query($item_count_strQuery);
							$item_count = pg_fetch_result($item_count_query, 0);
							
							//Generate PartsStockDetails : codeid	
							$codeid = $parts_code.sprintf('%06d', $item_count);
							
							$PartsStockDetails_strQuery = "
								INSERT INTO \"PartsStockDetails\"(
									codeid, 
									stock_id, 
									status, 
									wh_id, 
									locate_id, 
								)
								VALUES (
									'{$codeid}', 
									'".$partsStock_result["stock_id"]."',
									'1',
									'',
									''
								);
							";
							
							if(!$result=@pg_query($PartsStockDetails_strQuery)){
						        $txt_error[] = "INSERT PartsStockDetails_strQuery ไม่สำเร็จ $PartsStockDetails_strQuery";
						        $status++;
						    }
						}
						// END insert PartsStockDetails (each row of item)
					}
					//END Check that parts_code is parts.type = 1 or not, if yes, Query Insert PartStockDetails
		// echo "#########################";
	// pg_query("ROLLBACK");
	// exit;
				}
			}
			
			
			// ######### END INSERT partsStock ##########
			
		}
	}
	// ######################### END คืนของเข้าสต๊อก #########################
	
	// ######################## คืนเป็นของเสีย ########################
	elseif($return_type == 2){
		
		$brokenParts_strQuery = "
			INSERT INTO \"BrokenParts\"(
				broken_code, 
				type, 
				user_id, 
				broken_user_id, 
				date, 
				status, 
				note, 
				broken_date
			)
			VALUES (
				'".$gen_parts_no."', 
				".$return_type.", 
				'".$return_user_id."', 
				'".$return_return_user_id."', 
				'".$return_date."', 
				1, 
				'".$return_note."', 
				'".$nowDateTime."'
			);
			
		";
		
		if(!$result=@pg_query($brokenParts_strQuery)){
	        $txt_error[] = "INSERT BrokenParts ไม่สำเร็จ $brokenParts_strQuery";
	        $status++;
	    }
		
		foreach ($return_details_array as $value) {
			$idno = $value->idno;
			$parts_type = $value->parts_type;
			$parts_code = $value->parts_code;
			$quantity_return = $value->quantity_return;
			$wh_id = $value->wh_id;
			$locate_id = $value->locate_id;
			
			
			$returnPartsDetails_strQuery = "
				INSERT INTO \"BrokenPartsDetails\"
				(
					broken_code, 
					idno, 
					parts_code, 
					broken_quantity
				)
				VALUES
				(
					'{$gen_parts_no}',
					'".$idno."',
					'".$parts_code."',
					'".$quantity_return."'
				);
			";
			
			if(!$result=@pg_query($returnPartsDetails_strQuery)){
		        $txt_error[] = "INSERT returnPartsDetails ไม่สำเร็จ $returnPartsDetails_strQuery";
		        $status++;
		    }
			
			
			// PartsStockBroken
			// ######### INSERT partsStock ##########
			$partsStock_check_strQuery = "
				SELECT 
					parts_code,
					MAX(stock_lot) AS stock_lot
				FROM 
					\"PartsStockBroken\"
				WHERE 
					parts_code = '".$parts_code."' 
				group by parts_code ;
			";
			$partsStock_check_query = pg_query($partsStock_check_strQuery);
			
			if($partsStock_check_result = pg_fetch_array($partsStock_check_query)){
				
				$partsStock_strQuery = "
					INSERT INTO \"PartsStockBroken\"
					(
						parts_code, 
						stock_lot, 
						parts_rcvcode, 
						rcv_date, 
						rcv_quantity, 
						costperunit, 
						stock_remain, 
						wh_id, 
						locate_id, 
						stock_status
					)
					VALUES
					(
						'{$parts_code}',
						".($partsStock_check_result["stock_lot"] + 1).",
						'{$gen_parts_no}',
						'{$return_date}',
						'{$quantity_return}',
						null,
						'{$quantity_return}',
						'{$wh_id}',
						'{$locate_id}',
						'1'
					)
					RETURNING stock_broken_id;
				";
			}
			else{
				//insert the PartsStock when there are no parts_code in PartsStock
				$partsStock_strQuery = "
					INSERT INTO \"PartsStockBroken\"
					(
						parts_code, 
						stock_lot, 
						parts_rcvcode, 
						rcv_date, 
						rcv_quantity, 
						costperunit, 
						stock_remain, 
						wh_id, 
						locate_id, 
						stock_status
					)
					VALUES
					(
						'{$parts_code}',
						'1',
						'{$gen_parts_no}',
						'{$return_date}',
						'{$quantity_return}',
						null,
						'{$quantity_return}',
						'{$wh_id}',
						'{$locate_id}',
						'1'
					)
					RETURNING stock_broken_id;
				";
			}
			
			$partsStock_query = @pg_query($partsStock_strQuery);
			
			if($partsStock_result = @pg_fetch_array($partsStock_query)) {
				$i = 0; //For running number
				
				// Check That, Type PO is 1 or not, if yes, will insert PartsStockDetails each item.
				if($parts_type == 1){
					
					// Check that parts_code is parts.type = 1 or not, if yes, Query Insert PartStockDetails
					$parts_check_type_strQuery = "
						SELECT
							parts.type
						FROM
							parts
						WHERE 
							parts.code = '{$parts_code}'
							AND
							parts.type = 1
					";
					$parts_check_type_query = @pg_query($parts_check_type_strQuery);
					if(@pg_fetch_result($parts_check_type_query, 0) == 1){
						
						// insert PartsStockDetails (each row of item)
						for($i = 0; $i < $rcv_quantity; $i++){
							$item_count_strQuery = "
								UPDATE
									\"parts\"
								SET
									\"item_count\" = \"item_count\" + 1
								WHERE
									code = '".$parts_code."'
								RETURNING \"item_count\" ;
							";
							$item_count_query = @pg_query($item_count_strQuery);
							$item_count = pg_fetch_result($item_count_query, 0);
							
							//Generate PartsStockDetails : codeid	
							$codeid = $parts_code.sprintf('%06d', $item_count);
							
							$PartsStockDetails_strQuery = "
								INSERT INTO \"PartsStockBrokenDetails\"(
									codeid, 
									stock_broken_id, 
									status, 
									wh_id, 
									locate_id, 
								)
								VALUES (
									'{$codeid}', 
									'".$partsStock_result["stock_id"]."',
									'1',
									'',
									''
								);
							";
							
							if(!$result=@pg_query($PartsStockDetails_strQuery)){
						        $txt_error[] = "INSERT PartsStockDetails_strQuery ไม่สำเร็จ $PartsStockDetails_strQuery";
						        $status++;
						    }
						}
						// END insert PartsStockDetails (each row of item)
					}
					//END Check that parts_code is parts.type = 1 or not, if yes, Query Insert PartStockDetails
				}
			}
		}
	}
	// ###################### END คืนเป็นของเสีย ##########################
	
	//Insert Approve
	$ApproveParts_forReturn_strQuery = "
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
			'{$return_user_id}',
			'{$return_note}',
			'{$nowDateTime}',
			'000',
			'มีการอนุมัติโดยระบบอัตโนมัติ',
			'{$nowDateTime}'
		)
	";
	if(!$result2=@pg_query($ApproveParts_forReturn_strQuery)){
        $txt_error[] = "INSERT PartsApproved ไม่สำเร็จ {$ApproveParts_forReturn_strQuery}";
        $status++;
    }
	
	
	// echo $ApproveParts_forReturn_strQuery;
	// echo $status;
	// pg_query("ROLLBACK");
	// exit;
		
	
	// Check Is Query or Not?
	if($status == 0){
        pg_query("ROLLBACK");
        // pg_query("COMMIT");
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