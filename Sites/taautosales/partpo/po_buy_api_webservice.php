<?php
include_once("../include/config.php");
include_once("../include/function.php");
	
function get_PurchaseOrderPart(){
	$strQuery_PurchaseOrderPart = "
		SELECT 
			\"parts_pocode\"
		FROM
			\"PurchaseOrderPart\"
	";
	$query_PurchaseOrderPart = @pg_query($strQuery_PurchaseOrderPart);
	$numrows_parts = pg_num_rows($query_PurchaseOrderPart);
	while($result_PurchaseOrderPart = @pg_fetch_array($query_PurchaseOrderPart)){
		$dt["value"] = $result_PurchaseOrderPart["parts_pocode"];
		$dt["label"] = $result_PurchaseOrderPart["parts_pocode"];
		$parts_pocode_matches[] = $dt;
	}
	return 
		array(
			"numrow" => $numrows_parts,
			"result" => $parts_pocode_matches,
		)
	;
}

function get_view_VVenders(){
	$strQuery = "SELECT * FROM \"VVenders\" WHERE type_ven = 'M' or type_ven='B' ORDER BY pre_name,cus_name ASC";
	$qry = pg_query($strQuery);
	while( $res = pg_fetch_array($qry) ){
	    $dt["vender_id"] = $res['vender_id'];
	    $dt["pre_name"] = trim($res['pre_name']);
	    $dt["cus_name"] = trim($res['cus_name']);
	    $dt["surname"] = trim($res['surname']);
		$dt["branch_id"] = trim($res['branch_id']);
		$array[] = $dt;
	}
	return $array;
}

function get_parts(){
	$strQuery_parts = "
		SELECT 
			code, name, details, priceperunit, unitid, svcharge, type, item_count, barcode
		FROM 
			\"parts\" 
		ORDER BY code ASC
	";
	$qry_parts=@pg_query($strQuery_parts);
	$numrows_parts = pg_num_rows($qry_parts);
	while($res_parts=@pg_fetch_array($qry_parts)){
		$dt['code'] = $res_parts['code'];
		$dt['name'] = $res_parts['name'];
		$dt['unitid'] = $res_parts['unitid'];
		$dt['details'] = $res_parts['details'];
		$dt['value'] = $dt['code'];
		$dt['label'] = $dt["code"]." # ".$res_parts["barcode"]." # ".$dt['name']." # ".$dt["details"];
		$parts_matches[] = $dt;
	}
	return
		array(
			"result" => $parts_matches,
			"numrow" => $numrows_parts,
		)
	;
}

function get_parts_unit(){
	$strQuery_parts_unit = "
		SELECT *
		FROM \"parts_unit\" 
		ORDER BY unitname ASC
	";
	$parts_unit_data = array();
	$qry_parts_unit = @pg_query($strQuery_parts_unit);
	while($res_parts_unit = @pg_fetch_array($qry_parts_unit)){
		$parts_unit_data[] = $res_parts_unit;
	}
	return $parts_unit_data;
}

?>