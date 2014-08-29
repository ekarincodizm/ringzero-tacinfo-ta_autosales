<?php
	include_once("../include/config.php");
	include_once("../include/function.php");
	
	$parts_pocode = pg_escape_string($_POST["parts_pocode"]);
	$status = -1;
	
	$purchaseOrderPart_sqlQuery = "
		SELECT 
			\"subtotal\",
			\"pcdiscount\",
			\"discount\",
			\"bfv_total\",
			\"pcvat\",
			\"vat\",
			\"nettotal\"
		FROM 
			\"PurchaseOrderPart\"
		WHERE
			\"parts_pocode\" = '{$parts_pocode}'
	";
	$purchaseOrderPart_query = @pg_query($purchaseOrderPart_sqlQuery);
	while($purchaseOrderPart_result = @pg_fetch_array($purchaseOrderPart_query)){
		$data["purchaseOrderPart_subtotal"] = $purchaseOrderPart_result["subtotal"];
		$data["purchaseOrderPart_pcdiscount"] = $purchaseOrderPart_result["pcdiscount"];
		$data["purchaseOrderPart_discount"] = $purchaseOrderPart_result["discount"];
		$data["purchaseOrderPart_bfv_total"] = $purchaseOrderPart_result["bfv_total"];
		$data["purchaseOrderPart_pcvat"] = $purchaseOrderPart_result["pcvat"];
		$data["purchaseOrderPart_vat"] = $purchaseOrderPart_result["vat"];
		$data["purchaseOrderPart_nettotal"] = $purchaseOrderPart_result["nettotal"];
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
        $data['message'] = "ไม่สามารถอ่านได้! $purchaseOrderPart_sqlQuery";
    }
	
	echo json_encode($data);
?>