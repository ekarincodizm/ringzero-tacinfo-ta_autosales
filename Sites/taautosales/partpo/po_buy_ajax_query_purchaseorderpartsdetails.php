<?php
	include_once("../include/config.php");
	include_once("../include/function.php");
	
	$parts_pocode = pg_escape_string($_POST["parts_pocode"]);
	$status = -1;
	
	$data["purchaseOrderPartsDetails_idno"] = "";
	$data["purchaseOrderPartsDetails_parts_code"] = "";
	$data["purchaseOrderPartsDetails_quantity"] = "";
	$data["purchaseOrderPartsDetails_unit"] = "";
	$data["purchaseOrderPartsDetails_costperunit"] = "";
	$data["purchaseOrderPartsDetails_total"] ="";
	
	$purchaseOrderPartsDetails_sqlQuery = "
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
			\"parts_pocode\" = '{$parts_pocode}'
		ORDER BY \"auto_id\" ASC
	";
	$purchaseOrderPartsDetails_query = @pg_query($purchaseOrderPartsDetails_sqlQuery);
	$purchaseOrderPartsDetails_numrows = pg_num_rows($purchaseOrderPartsDetails_query);
	while($purchaseOrderPartsDetails_result = @pg_fetch_array($purchaseOrderPartsDetails_query)){
		$counter = $purchaseOrderPartsDetails_result["idno"];
		$data["purchaseOrderPartsDetails_idno"] .= $counter;
		$data["purchaseOrderPartsDetails_parts_code"] .= $purchaseOrderPartsDetails_result["parts_code"];
		$data["purchaseOrderPartsDetails_quantity"] .= $purchaseOrderPartsDetails_result["quantity"];
		$data["purchaseOrderPartsDetails_unit"] .= $purchaseOrderPartsDetails_result["unit"];
		$data["purchaseOrderPartsDetails_costperunit"] .= $purchaseOrderPartsDetails_result["costperunit"];
		$data["purchaseOrderPartsDetails_total"] .= $purchaseOrderPartsDetails_result["total"];
		
		if($counter < $purchaseOrderPartsDetails_numrows){
			$data["purchaseOrderPartsDetails_idno"] .= "###";
			$data["purchaseOrderPartsDetails_parts_code"] .= "###";
			$data["purchaseOrderPartsDetails_quantity"] .= "###";
			$data["purchaseOrderPartsDetails_unit"] .= "###";
			$data["purchaseOrderPartsDetails_costperunit"] .= "###";
			$data["purchaseOrderPartsDetails_total"] .= "###";
		}
	}
	if($purchaseOrderPartsDetails_numrows == 0){
		$status = -1;
	}
	else{
		$status = 0;
	}
	
	//Check Is Query or Not?
	if($status == 0){
        //pg_query("ROLLBACK");
        pg_query("COMMIT");
        $data['success'] = true;
		$data['message'] = "";
    }else{
        pg_query("ROLLBACK");
        $data['success'] = false;
        $data['message'] = "ไม่สามารถอ่านได้! $purchaseOrderPartsDetails_sqlQuery";
    }
	
	echo json_encode($data);
?>