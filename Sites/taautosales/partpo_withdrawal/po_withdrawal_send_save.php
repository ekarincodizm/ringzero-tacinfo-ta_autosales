<?php
	// ini_set('max_execution_time', 300);
	include_once("../include/config.php");
	include_once("../include/function.php");
	include_once("po_withdrawal_webservice.php");
	
	//Load Initial HTTP Post Variables
	$id_user = $_SESSION["ss_iduser"];
	
	$withdrawal_code = pg_escape_string($_POST["withdrawal_code"]);
	$withdrawal_type = pg_escape_string($_POST["withdrawal_type"]);
	
	$send_type = pg_escape_string($_POST["send_type"]);
	$send_user_id = pg_escape_string($_POST["send_user_id"]);
	$send_withdraw_user_id = pg_escape_string($_POST["send_withdraw_user_id"]);
	$send_date = date("Y-m-d", strtotime(pg_escape_string($_POST['send_date'])));
	$send_usedate = date("Y-m-d", strtotime(pg_escape_string($_POST["send_usedate"])));
	
	$project_id = pg_escape_string($_POST["project_id"]);
	$project_quantity = pg_escape_string($_POST["project_quantity"]);
	
	$send_details_array = json_decode(stripcslashes(pg_escape_string($_POST["send_details_array"])));
	$send_note = pg_escape_string($_POST["send_note"]);
	
	
	pg_query("BEGIN WORK");
	$status = 0;
	$txt_error = array();
	
	//Inport Class Functions
	$function = new Withdrawal_send_save();
	
	
	// ######################################### Check Concurrency First ##################################################
	
	$withdrawalParts_check_strQuery = "
		SELECT code
		FROM \"WithdrawalParts\"
		WHERE 
			code = '".$withdrawal_code."' 
			AND
			status = 5
		;
	";
	$withdrawalParts_check_query = pg_query($withdrawalParts_check_strQuery);
	if($withdrawalParts_check_result = pg_fetch_array($withdrawalParts_check_query)){
		
		$txt_error[] = "กรุณาตรวจสอบข้อมูล";
		$status++;
	}
	else{
		
		
		// ######### For Test checking the Variables #########
		
		// echo $partsStockDetails_strQuery;
		// pg_query("ROLLBACK");
		// exit;
		
		// ###################################################
		
		if($send_type == 1){
			$type_X = "SPF";
			$kny = 7;
		}
		elseif($send_type == 2){
			$type_X = "SPJ";
			$kny = 8;
		}
		elseif($send_type == 3){
			$type_X = "SPW";
			$kny = 9;
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
		
		$nowdate = nowDate();
		$nowDateTime = nowDateTime();
		
		$generate_id_StrQuery = "
			select gen_parts_no(
				'".$send_date."', 
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
		
		//Query PartsReceived
		$sendParts_strQuery = "
		INSERT INTO \"SendParts\"(
			send_code, 
			withdrawal_code,
			type, 
			user_id, 
			send_user_id, 
			date, 
			usedate, 
			status, 
			note,
			send_date
		)
		VALUES (
			'{$gen_parts_no}',
			'{$withdrawal_code}',
			'{$send_type}',
			'{$send_user_id}',
			'{$send_withdraw_user_id}',
			'{$send_date}',
			'{$send_usedate}',
			1,
			'{$send_note}',
			'{$nowDateTime}'
		);
		";
		
		if(!$result=@pg_query($sendParts_strQuery)){
	        $txt_error[] = "INSERT sendParts_strQuery ไม่สำเร็จ $sendParts_strQuery";
	        $status++;
	    }
		
		
		$count_send_details_array = count($send_details_array);
		$status_quantity_equal = 0;
		$project_subtotal = 0.0;
		
	    // ################################## Query Send Details #####################################
	    foreach($send_details_array as $value){
	    	
			$idno = $value->idno;
			$parts_code = $value->parts_code;
			$quantity = $value->quantity;
			$send_quantity = $value->send_quantity; //จำนวน ที่ส่งออก
			
			
			// insert PartsReceivedDetails
			$sendPartsDetails_strQuery = "
				INSERT INTO \"SendPartsDetails\"
				(
					send_code,
					idno, 
					parts_code, 
					send_quantity
				)
				VALUES
				(
					'{$gen_parts_no}',
					'{$idno}',
					'{$parts_code}',
					'{$send_quantity}'
				)
			";
			
			if(!$result=@pg_query($sendPartsDetails_strQuery)){
		        $txt_error[] = "INSERT sendPartsDetails_strQuery ไม่สำเร็จ $sendPartsDetails_strQuery";
		        $status++;
		    }
			
			
			// ไป ลบ จำนวน Parts ใน Stocks 
			// Select ค่า PartsStock กับ PartsStockDetails
			// แล้วไป if else ว่า จะไป ลบจำนวน PartsStock หรือ PartsStockDetails
			
			// ถ้า parts_code อยู่ใน PartsStock
			$temp_sent_quantity = $send_quantity;
			$partsStock_strQuery = "
				SELECT 
					stock_id, parts_code, stock_remain
				FROM 
					\"PartsStock\"
				WHERE 
					parts_code = '".$parts_code."' 
					AND
					stock_status = 1 
					AND 
					stock_remain > 0
				ORDER BY rcv_date ;
			";
			
			$partsStock_query = @pg_query($partsStock_strQuery);
			while ($partsStock_result = @pg_fetch_array($partsStock_query)) {
				
				$temp_partsStock = $partsStock_result["stock_remain"];
				
				if($temp_sent_quantity <= $partsStock_result["stock_remain"]){
					$parts_strQuery = "
						UPDATE 
							\"PartsStock\"
						SET 
							stock_remain = stock_remain - ".$temp_sent_quantity."
						WHERE 
							parts_code = '".$parts_code."' 
							AND
							stock_id = '".$partsStock_result["stock_id"]."' ;
					";
					$temp_sent_quantity = $temp_sent_quantity - $temp_sent_quantity;
					
				}
				else{
					$parts_strQuery = "
						UPDATE 
							\"PartsStock\"
						SET 
							stock_remain = stock_remain - stock_remain
						WHERE 
							parts_code = '".$parts_code."' 
							AND
							stock_id = '".$partsStock_result["stock_id"]."' ;
					";
					$temp_sent_quantity = $temp_sent_quantity - $temp_partsStock;
					// $txt_error[] = "send_quantity > partsStock_result[\"stock_remain\"] ; ".$send_quantity." > ".$partsStock_result["stock_remain"];
			        // $status++;
			        
				}
				
				if(!$result=@pg_query($parts_strQuery)){
			        $txt_error[] = "UPDATE parts_strQuery ไม่สำเร็จ $parts_strQuery";
			        $status++;
			    }
				
				if($temp_sent_quantity <= 0){
					break;
				}
				
				/*
				$partsStock2_strQuery = "
					SELECT 
						parts_code, stock_remain
					FROM 
						\"PartsStock\"
					WHERE 
						stock_id = '".$partsStock_result["stock_id"]."'
					ORDER BY rcv_date ;
				";
				$partsStock2_query = @pg_query($partsStock2_strQuery);
				while ($partsStock2_result = @pg_fetch_array($partsStock2_query)) {
					
					echo "stock_remain = ".$partsStock2_result["stock_remain"];
					pg_query("ROLLBACK");
					exit;
				}
				*/
			}
			
			// ถ้า parts_code อยู่ใน PartsStockDetails
			$partsStockDetails_strQuery = "
				SELECT 
					codeid, stock_id, status, wh_id, locate_id, note
				FROM 
					\"PartsStockDetails\"
				WHERE 
					codeid = '".$parts_code."' 
					AND
					status = 1 ;
			";
			$partsStockDetails_query = @pg_query($partsStockDetails_strQuery);
			while ($partsStockDetails_result = @pg_fetch_array($partsStockDetails_query)) {
				
				//ถ้า parts_code อยู่ใน PartsStockDetails
				$partsStockDetails_update_strQuery = "
					UPDATE \"PartsStockDetails\"
						SET status = 2
					WHERE 
						codeid = '".$parts_code."' ;
				";
				if(!$result=@pg_query($partsStockDetails_update_strQuery)){
			        $txt_error[] = "UPDATE partsStockDetails_update_strQuery 123 ไม่สำเร็จ $partsStockDetails_update_strQuery";
			        $status++;
			    }
				
			}
			
			
		}
		// ########################################## End Query Send Details #########################################
		
		/*
		// ###### Insert Approve กลับ ######
		$ApproveParts_forWithdrawal_strQuery = "
			UPDATE
				\"PartsApproved\"
			SET
				appr_id = '".$id_user."', 
				appr_note = '".$send_note."', 
				appr_timestamp = '".$nowDateTime."'
			WHERE
				code = '".$withdrawal_code."' ;
		";
		
		if(!$result = @pg_query($ApproveParts_forWithdrawal_strQuery)){
	        $txt_error[] = "UPDATE ApproveParts_forWithdrawal_strQuery ไม่สำเร็จ ".$ApproveParts_forWithdrawal_strQuery;
	        $status++;
	    }
		*/
		
		
		// ไปเพิ่ม Comment บน Table: PartsStock ที่เก็บของใน Stock 
		// ให้เตือนว่า ถ้า Status = 2 จะแปลว่า สินค้าชิ้นนั้น ขายไปแล้ว หลังจากที่ กด SendParts สำเร็จแล้ว
		$withdrawalPartsDetails_strQuery = "
			SELECT
				withdrawal_id, 
				withdrawal_code, 
				idno, 
				parts_code, 
				withdrawal_quantity
			FROM 
				\"WithdrawalPartsDetails\"
			WHERE 
				\"withdrawal_code\" = '".$withdrawal_code."' ;
		";
		$withdrawalPartsDetails_query = @pg_query($withdrawalPartsDetails_strQuery);
		$withdrawalPartsDetails_numrow = @pg_num_rows($withdrawalPartsDetails_query);
		while ($withdrawalPartsDetails_result = @pg_fetch_array($withdrawalPartsDetails_query)) {
			
			// Calculate How many Quantity left after already withdrawal the Parts
			$max_send_quantity = 0;
			$view_withdrawal_quantity_strQuery = "
				SELECT
					parts_code,
					SUM(send_quantity) as send_quantity
				FROM 
					v_parts_withdrawal_quantity
				where 
					\"withdrawal_code\" = '".$withdrawal_code."'
					AND
					parts_code = '".$withdrawalPartsDetails_result["parts_code"]."'
				group by parts_code ;
			";
			$view_withdrawal_quantity_query = pg_query($view_withdrawal_quantity_strQuery);
			while ($view_withdrawal_quantity_result = pg_fetch_array($view_withdrawal_quantity_query)) {
				$max_send_quantity = $view_withdrawal_quantity_result["send_quantity"];
			}
			$max_send_quantity = $withdrawalPartsDetails_result["withdrawal_quantity"] - $max_send_quantity;
			
			//Check ว่า Quantity = withdrawal_quantity หรือไม่
			//Check สำหรับ ถ้าเกิดว่า Quantity = withdrawal_quantityเท่ากันทุกค่า แสดงว่า Withdrawal อันนี้ เบิกหมด ทั้งใบเบิก
			//เพราะฉะนั้น ต้อง Set Status ใบเบิกนั้นว่า เบิกหมดแล้ว ; Status = 5
			if($max_send_quantity == 0){
				$status_quantity_equal++;
			}
		}
	
		if($status_quantity_equal == $count_send_details_array){
			$withdrawalParts_setStatus_strQuery = "
				UPDATE
					\"WithdrawalParts\"
				SET
					status = 5
				WHERE
					code = '{$withdrawal_code}' 
			";
			if(!$result=@pg_query($withdrawalParts_setStatus_strQuery)){
		        $txt_error[] = "UPDATE withdrawalParts_setStatus_strQuery ไม่สำเร็จ {$withdrawalParts_setStatus_strQuery}";
		        $status++;
		    }
			
		}
		// End ไปเพิ่ม Comment บน Table: PartsStock ที่เก็บของใน Stock 
		// ให้เตือนว่า ถ้า Status = 2 จะแปลว่า สินค้าชิ้นนั้น ขายไปแล้ว หลังจากที่ กด SendParts สำเร็จแล้ว
		
		
		
		// ################ For PurchaseOrderPartsDetails #####################
		// ##### if(type == 2) #####
		if($withdrawal_type == 2){
			
			
			// ############## For purchaseOrderParts PartsApproved #################
			$purchaseOrderParts_PartsApproved_strQuery = "
				INSERT INTO \"PartsApproved\"(
					code, 
					user_id, 
					user_note, 
					user_timestamp, 
					appr_id, appr_note, 
					appr_timestamp
				)
				VALUES (
					'".$gen_parts_no."',  
					'000', 
					'ระบบอัตโนมัติ', 
					'{$nowDateTime}', 
					'000', 
					'ระบบอัตโนมัติ', 
					'{$nowDateTime}'
				);
			";
			if(!$result=@pg_query($purchaseOrderParts_PartsApproved_strQuery)){
		        $txt_error[] = "INSERT PartsApproved ไม่สำเร็จ $purchaseOrderParts_PartsApproved_strQuery";
		        $status++;
		    }
			
									// // Test
									// $purchaseOrderPartsDetails_strQuery = "
										// SELECT *
										// FROM \"PartsApproved\"
										// WHERE
											// code = '".$gen_parts_no."'
										// ;
									// ";
									// $purchaseOrderPartsDetails_query = pg_query($purchaseOrderPartsDetails_strQuery);
									// $purchaseOrderPartsDetails_result = pg_fetch_all($purchaseOrderPartsDetails_query);
									// var_dump($purchaseOrderPartsDetails_result);
									// pg_query("ROLLBACK");
									// exit;
									// // END Test
			
			
			// ################## END For PartsApproved #####################
			
			
			
			$new_product_id = $function->get_Projects($project_id, "product_id");
			
			$costperunit = floatval(
				$function->get_parts(
					$new_product_id, "priceperunit"
				)
			);
			$project_total = $costperunit * floatval($project_quantity);
			
			$purchaseOrderPartsDetails_strQuery = "
				INSERT INTO \"PurchaseOrderPartsDetails\"(
					parts_pocode, 
					idno, 
					parts_code, 
					quantity, 
					unit, 
					costperunit, 
					total
				)
				VALUES (
					'".$gen_parts_no."', 
					'".$idno."', 
					'".$new_product_id."', 
					'".$project_quantity."', 
					'".$function->get_parts($parts_code, "unitid")."', 
					'".$costperunit."', 
					'".$project_total."'
				);
			
			";
			
			// echo $purchaseOrderPartsDetails_strQuery;
			// pg_query("ROLLBACK");
			// exit;
			
			if(!$result=@pg_query($purchaseOrderPartsDetails_strQuery)){
		        $txt_error[] = "INSERT purchaseOrderPartsDetails_strQuery ไม่สำเร็จ $purchaseOrderPartsDetails_strQuery";
		        $status++;
		    }
			$project_subtotal += $project_total;
			
									// // Test
									// $purchaseOrderPartsDetails_strQuery = "
										// SELECT auto_id, parts_pocode, idno, parts_code, quantity, unit, costperunit, total
										// FROM \"PurchaseOrderPartsDetails\"
										// WHERE
											// parts_pocode = '".$gen_parts_no."'
										// ;
									// ";
									// $purchaseOrderPartsDetails_query = pg_query($purchaseOrderPartsDetails_strQuery);
									// $purchaseOrderPartsDetails_result = pg_fetch_all($purchaseOrderPartsDetails_query);
									// var_dump($purchaseOrderPartsDetails_result);
									// pg_query("ROLLBACK");
									// exit;
									// // END Test
			
			
			// ############## END For PurchaseOrderPartsDetails ###################
			
			
			// ############################## For Insert to --> PurchaseOrderPart ########################################
			
			$purchaseOrderPart_strQuery = "
				INSERT INTO
					\"PurchaseOrderPart\"
					(
						parts_pocode,
						date, 
						type, 
						credit_terms, 
						app_sentpartdate, 
						esm_paydate, 
						vender_id, 
						vat_status, 
						subtotal, 
						pcdiscount, 
						discount, 
						bfv_total, 
						pcvat, 
						vat, 
						nettotal, 
						status, 
						paid
					)
					VALUES
					(
						'".$gen_parts_no."',
						'".$nowDateTime."',
						1,
						0,
						'".$nowDateTime."',
						'".$nowDateTime."',
						1,
						0,
						'".$project_subtotal."',
						0,
						0,
						'".$project_subtotal."',
						0,
						0,
						'".$project_subtotal."',
						2,
						1
					);
			";
			if(!$purchaseOrderPart_result = @pg_query($purchaseOrderPart_strQuery)){
		        $txt_error[] = "INSERT PurchaseOrderPart ไม่สำเร็จ $purchaseOrderPart_strQuery";
		        $status++;
		    }
			// ############################## END For Insert to --> PurchaseOrderPart ######################################
			
		}
		// ##### END if(type == 2) #####
		// ################ END For PurchaseOrderPartsDetails #####################
		
	}
	// ######################################### Check Concurrency First ##################################################
	
	
	// Check Is Query or Not?
	if($status == 0){
        // pg_query("ROLLBACK");
        pg_query("COMMIT");
        $data['success'] = true;
        $data['sendParts_code'] = $gen_parts_no;
    }else{
        pg_query("ROLLBACK");
        $data['success'] = false;
        $data['message'] = "ไม่สามารถบันทึกได้! $txt_error[0]";
    }
	
	echo json_encode($data);
?>