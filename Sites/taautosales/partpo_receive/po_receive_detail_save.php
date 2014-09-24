<?php
	include_once("../include/config.php");
	include_once("../include/function.php");
	
	//Load Initial HTTP Post Variables
	$id_user = $_SESSION["ss_iduser"];
	$type = pg_escape_string($_POST["type"]);
	$parts_pocode = pg_escape_string($_POST['parts_pocode']);
	$app_sentpartdate = date("m-d-Y", strtotime(pg_escape_string($_POST['app_sentpartdate'])));
	$credit_terms = pg_escape_string($_POST['credit_terms']);
	$esm_paydate = date("m-d-Y", strtotime(pg_escape_string($_POST['esm_paydate'])));
	$vat_status = pg_escape_string($_POST['vat_status']);
	$inv_no = pg_escape_string($_POST["inv_no"]);
	$receipt_no = pg_escape_string($_POST["receipt_no"]);
	
	$parts_received_details_array = json_decode(stripcslashes(pg_escape_string($_POST['parts_received_details_array'])));
	
	$dsubtotal = pg_escape_string($_POST['dsubtotal']);
	$pcdiscount = pg_escape_string($_POST['pcdiscount']);
	$discount= pg_escape_string($_POST['discount']);
	$vsubtotal = pg_escape_string($_POST['vsubtotal']);
	$pcvat = pg_escape_string($_POST['pcvat']);
	$vat = pg_escape_string($_POST['vat']);
	$nettotal = pg_escape_string($_POST['nettotal']);
	$recv_remark = pg_escape_string($_POST['recv_remark']);
	
	
	// ######### For Test checking the Variables #########
	
	// pg_query("ROLLBACK");
	// $data["test"] = $id_user;
	// $data["success"] = false;
	// $data["message"] = "";
	// echo json_encode($data);
	// exit;
	
	// ###################################################
	
	if($type == 1){
		$type_X = "N";
		$kny = 2;
	}
	elseif($type == 2){
		$type_X = "U";
		$kny = 3;
	}
	
	// parts_rcvcode
	// "RCPNXB-YYMMDDNNN"
	// "RCPN" follow by "XB" then follow by "-YYMMDDNNN"
	// X = "N" AS PO New (type = 1) or = "U" AS PO Old (type = 2)
	// B = Field: Office_id ---> Table: fuser --> Primary Key is Field : id_user --> Get from HTTP Get : "ss_iduser"
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
	
	
	pg_query("BEGIN WORK");
	$status = 0;
	$txt_error = array();
	
	
	$nowDate = nowDate();
	$nowDateTime = nowDateTime();	
	$generate_id_StrQuery = "
		select gen_parts_no(
			'".$app_sentpartdate."', 
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
	$partsReceived_strQuery = "
	INSERT INTO \"PartsReceived\"(
		parts_rcvcode,			parts_pocode, 
		rcv_date,				inv_no, 
		receipt_no,				due_date, 
		user_id,				user_timestamp, 
		appr_status,			rcv_dsubtotal, 
		rcv_pcdiscount,			rcv_discount, 
		rcv_vsubtotal,			rcv_pcvat, 
		rcv_vat,				rcv_nettotal, 
		recv_remark
	)
	VALUES (
		'{$gen_parts_no}',		'{$parts_pocode}',
		'{$app_sentpartdate}',	'{$inv_no}',
		'{$receipt_no}',		'{$esm_paydate}',
		'{$id_user}',			'{$nowDate}',
		'1',					'{$dsubtotal}',
		'{$pcdiscount}',		'{$discount}',
		'{$vsubtotal}',			'{$pcvat}',
		'{$vat}',				'{$nettotal}',
		'{$recv_remark}'
	);
	";
	
	if(!$result=@pg_query($partsReceived_strQuery)){
        $txt_error[] = "INSERT PartsReceived ไม่สำเร็จ $strQuery_PurchaseOrderPart";
        $status++;
    }
	
    //Query PartsReceivedDetails
    foreach($parts_received_details_array as $key => $value){
    	
		$idno = $value->idno;
		$parts_code = $value->parts_code;
		$rcv_quantity = $value->rcv_quantity;
		$unit = $value->unit;
		$costperunit = $value->costperunit;
		$total = $value->total;
		$wh_id = $value->wh_id;
		$locate_id = $value->locate_id;
		
		
		// insert PartsReceivedDetails
		$partsReceivedDetails_strQuery = "
			INSERT INTO \"PartsReceivedDetails\"
			(
				parts_rcvcode, 
				idno, 
				parts_code, 
				rcv_quantity, 
				unit, 
				costperunit, 
            	total, 
            	wh_id, 
            	locate_id
			)
			VALUES
			(
				'$gen_parts_no',
				'$idno',
				'$parts_code',
				'$rcv_quantity',
				'$unit',
				'$costperunit',
				'$total',
				'$wh_id',
				'$locate_id'
			)
		";
		
		if(!$result=@pg_query($partsReceivedDetails_strQuery)){
	        $txt_error[] = "INSERT PartsReceivedDetails ไม่สำเร็จ $partsReceivedDetails_strQuery";
	        $status++;
	    }
		
		//Check That there are parts_code in PartsStock or not
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
			/*
			$partsStock_strQuery = "
				UPDATE \"PartsStock\"
				SET 
					stock_lot = stock_lot + 1, 
					parts_rcvcode = '{$gen_parts_no}', 
					rcv_date = '{$app_sentpartdate}', 
					rcv_quantity = '{$rcv_quantity}', 
					costperunit = '{$costperunit}', 
					stock_remain = NULL,
					wh_id = '{$wh_id}', 
					locate_id = '{$locate_id}', 
					stock_status = '1'
				WHERE 
					parts_code = '{$parts_code}'
				RETURNING stock_id;
			";
			*/
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
					'{$app_sentpartdate}',
					'{$rcv_quantity}',
					'{$costperunit}',
					'{$rcv_quantity}',
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
					'{$app_sentpartdate}',
					'{$rcv_quantity}',
					'{$costperunit}',
					'{$rcv_quantity}',
					'{$wh_id}',
					'{$locate_id}',
					'1'
				)
				RETURNING stock_id;
			";
		}
		
		$partsStock_query = @pg_query($partsStock_strQuery);
		// $partsStock_numrows = pg_num_rows($partsStock_query);
		
		if($partsStock_result = @pg_fetch_array($partsStock_query)) {
			
			$i = 0; //For running number
			
			// Check That, Type PO is 1 or not, if yes, will insert PartsStockDetails each item.
			if($type == 1){
				
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
								note
							)
							VALUES (
								'{$codeid}', 
								'".$partsStock_result["stock_id"]."',
								'1',
								'{$wh_id}',
								'{$locate_id}',
								'{$recv_remark}'
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
		else{
	        $txt_error[] = "INSERT PartsStock_strQuery ไม่สำเร็จ $partsStock_strQuery";
	        $status++;
		}
	}
	// End Query PurchaseOrderPartsDetails
	
	
	
	// ########## Check ว่า Quantity เท่ากับ rcv_quantity หรือไม่ ##########
	$purchaseOrderPart_strQuery = "
		SELECT
			\"parts_pocode\",\"date\",\"type\",\"copypo_id\",
			\"credit_terms\",\"app_sentpartdate\",\"esm_paydate\",\"vender_id\",
			\"subtotal\",\"pcdiscount\",\"discount\",\"bfv_total\",
			\"pcvat\",\"vat\",\"nettotal\",\"status\",\"paid\"
		FROM
			\"PurchaseOrderPart\"
		WHERE 
			\"parts_pocode\" = '{$parts_pocode}'; 
	";
	$purchaseOrderPart_query = pg_query($purchaseOrderPart_strQuery);
	$purchaseOrderPart_numrow = pg_num_rows($purchaseOrderPart_query);
	while ($purchaseOrderPart_result = pg_fetch_array($purchaseOrderPart_query)) {
		
		$purchaseOrderPartsDetails_strQuery = "
			SELECT
				\"idno\",
				\"parts_code\",
				\"quantity\",
				\"unit\",
				\"costperunit\",
				\"total\"
			FROM
				\"PurchaseOrderPartsDetails\"
			WHERE 
				\"parts_pocode\" = '{$parts_pocode}'; 
		";
		$purchaseOrderPartsDetails_query = pg_query($purchaseOrderPartsDetails_strQuery);
		$purchaseOrderPartsDetails_numrows = pg_num_rows($purchaseOrderPartsDetails_query);
		$rcv_quantity_count_numrows = 0;
		while ($purchaseOrderPartsDetails_result = pg_fetch_array($purchaseOrderPartsDetails_query)) {
			
			// ### Get the Used Quantity ###
			$rcv_quantity_count = 0;
	    	$received_quantity_strQuery = "
				select 
					parts_code,
					SUM(rcv_quantity) AS rcv_quantity_count
				from 
					\"PartsReceivedDetails\" 
				where 
					parts_rcvcode IN 
					(
						select parts_rcvcode 
						from \"PartsReceived\" 
						where parts_pocode = '".$purchaseOrderPart_result["parts_pocode"]."'
					) 
					AND
					parts_code = '".$purchaseOrderPartsDetails_result['parts_code']."'
				group by parts_code ;
			";
			$received_quantity_query = pg_query($received_quantity_strQuery);
			while($received_quantity_result = pg_fetch_array($received_quantity_query)){
				$rcv_quantity_count = $received_quantity_result["rcv_quantity_count"];
			}
			
			// ### Check if there are no Used Quantity ###
			if(($purchaseOrderPartsDetails_result["quantity"] - $rcv_quantity_count) == 0 ){
				$rcv_quantity_count_numrows++;
			}
		}
	}
	if($purchaseOrderPartsDetails_numrows == $rcv_quantity_count_numrows){
		$purchaseOrderParts_update_status_query = "
			UPDATE \"PurchaseOrderPart\"
			SET 
				status = '3'
			WHERE
				parts_pocode = '".$parts_pocode."' 
			;
		";
		if(!$result=@pg_query($purchaseOrderParts_update_status_query)){
	        $txt_error[] = "INSERT purchaseOrderParts_update_status_query ไม่สำเร็จ $purchaseOrderParts_update_status_query";
	        $status++;
	    }
	}
	// ########## END - Check ว่า Quantity เท่ากับ rcv_quantity หรือไม่ ถ้าเท่าให้ Update status = 3 ##########
	
	$ApproveParts_forReceive_strQuery = "
		INSERT INTO 
			\"PartsApproved\"
		(
			code, user_id, user_note, user_timestamp, appr_id, appr_note, 
			appr_timestamp
		)
		VALUES
		(
			'{$gen_parts_no}',
			'{$id_user}',
			'{$recv_remark}',
			'{$nowDateTime}',
			'{$id_user}',
			'{$recv_remark}',
			'{$nowDateTime}'
		)
	";
	if(!$result=@pg_query($ApproveParts_forReceive_strQuery)){
        $txt_error[] = "INSERT ApproveParts_forReceive_strQuery ไม่สำเร็จ {$ApproveParts_forReceive_strQuery}";
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
	
	echo json_encode($data);
?>