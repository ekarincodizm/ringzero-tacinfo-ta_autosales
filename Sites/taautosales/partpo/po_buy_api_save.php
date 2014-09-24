<?php
	include_once("../include/config.php");
	include_once("../include/function.php");
	
	//Load Initial HTTP Post Variables
	$id_user = $_POST['id_user'];
	$type = $_POST['type'];
	$date = date("m-d-Y", strtotime($_POST['date']));
	// $parts_pocode = $_POST['parts_pocode'];
	$copypo_id = $_POST['copypo_id'];
	$app_sentpartdate = date("m-d-Y", strtotime($_POST['app_sentpartdate']));
	$credit_terms = $_POST['credit_terms'];
	$esm_paydate = date("m-d-Y", strtotime($_POST['esm_paydate']));
	$vender_id = $_POST['vender_id'];
	$vat_status = $_POST['vat_status'];
	$purchase_order_parts_details_array = json_decode(stripcslashes($_POST['purchase_order_parts_details_array']));
	$dsubtotal = $_POST['dsubtotal'];
	$pcdiscount = $_POST['pcdiscount'];
	$discount= $_POST['discount'];
	$vsubtotal = $_POST['vsubtotal'];
	$pcvat = $_POST['pcvat'];
	$vat = $_POST['vat'];
	$nettotal = $_POST['nettotal'];
	$PartsApproved_appr_note = $_POST['PartsApproved_appr_note'];
	
	if($type == 1){
		$type_X = "N";
		$kny = 0;
	}
	elseif($type == 2){
		$type_X = "U";
		$kny = 1;
	}
	
	// parts_pocode
	// "POPXB-YYMMDDNNN"
	// "POP" follow by "XB" then follow by "-YYMMDDNNN"
	// X = "n" AS PO New (type = 1) or = "u" AS PO Old (type = 2)
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
	
	
	$generate_id_StrQuery = "
		select gen_parts_no(
			'".$date."', 
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
	
	// ######### For Test checking the Variables #########
	
	// $data["test"] = date("m-d-Y", strtotime($date));
	// $data["success"] = false;
	// $data["message"] = "";
	// echo json_encode($data);
	// exit;
	
	// ###################################################
	
	
	//Query PurchaseOrderPart
	$strQuery_PurchaseOrderPart = "
		INSERT INTO \"PurchaseOrderPart\" 
		(
			\"parts_pocode\",\"date\",\"type\",\"copypo_id\",
			\"credit_terms\",\"app_sentpartdate\",\"esm_paydate\",\"vender_id\",
			\"subtotal\",\"pcdiscount\",\"discount\",\"bfv_total\",
			\"pcvat\",\"vat\",\"nettotal\", \"vat_status\", \"status\",\"paid\"
		)
		VALUES 
    	(
	    	'$gen_parts_no','$date','$type','$copypo_id',
	    	'$credit_terms','$app_sentpartdate','$esm_paydate','$vender_id',
	    	'$dsubtotal','$pcdiscount','$discount','$vsubtotal',
	    	'$pcvat','$vat','$nettotal','$vat_status', '1','0'
		)
	";
	
	if(!$result=@pg_query($strQuery_PurchaseOrderPart)){
        $txt_error[] = "INSERT PurchaseOrderPart ไม่สำเร็จ $strQuery_PurchaseOrderPart";
        $status++;
    }
	
	
	// $data["test"] = $strQuery_PurchaseOrderPart;
	// $data["success"] = false;
	// $data["message"] = "";
	// echo json_encode($data);
	// exit;
	
    
    //Query PurchaseOrderPartsDetails
    foreach($purchase_order_parts_details_array as $key => $value){
    	
		$idno = $value->idno;
		$parts_code = $value->parts_code;
		$quantity = $value->quantity;
		$unit = $value->unit;
		$costperunit = $value->costperunit;
		$total = $value->total;
		
		if(empty($parts_code) or empty($quantity) or empty($unit) or empty($costperunit)){
            continue;
        }
		
		$strQeury_PurchaseOrderPartsDetails = "
			INSERT INTO \"PurchaseOrderPartsDetails\"
			(
				\"parts_pocode\", 
				\"idno\",
				\"parts_code\",
				\"quantity\",
				\"unit\",
				\"costperunit\",
				\"total\"
			)
			VALUES
			(
				'$gen_parts_no',
				'$idno',
				'$parts_code',
				'$quantity',
				'$unit',
				'$costperunit',
				'$total'
			)
		";
		
		if(!$result=@pg_query($strQeury_PurchaseOrderPartsDetails)){
	        $txt_error[] = "INSERT PurchaseOrderPartsDetails ไม่สำเร็จ $strQeury_PurchaseOrderPartsDetails";
	        $status++;
	    }
	}
	
	//Query PartsApproved
	$strQuery_PartsApproved = "
		INSERT INTO \"PartsApproved\" 
		(
			\"code\",
			\"user_id\",
			\"user_note\",
			\"user_timestamp\",
			\"appr_id\",
			\"appr_note\",
			\"appr_timestamp\"
		) 
		VALUES 
    	(
	    	'$gen_parts_no',
	    	'$id_user',
	    	'$PartsApproved_appr_note',
	    	CURRENT_TIMESTAMP,
	    	null,
	    	null,
	    	null
		)
	";
	
	if(!$result=@pg_query($strQuery_PartsApproved)){
        $txt_error[] = "INSERT PartsApproved ไม่สำเร็จ $strQuery_PartsApproved";
        $status++;
    }
    
	
	//Check Is Query or Not?
	if($status == 0){
        //pg_query("ROLLBACK");
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