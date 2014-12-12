<?php
include_once("../include/config.php");
include_once("../include/function.php");

function get_Parts(){
	$parts_strQuery = "
		SELECT 
			code, name, details, barcode
		FROM 
			parts
		WHERE
			type = 1
		ORDER BY code;
	";
	$qry_list = pg_query($parts_strQuery);
	while( $res_list = pg_fetch_array($qry_list) ){
		$array[] = $res_list;
	}
	return $array;
}
function get_Parts_type_0(){
	$parts_strQuery = "
		SELECT 
			code, name, details, barcode
		FROM 
			parts
		WHERE
			type = 0
		ORDER BY code;
	";
	$qry_list = pg_query($parts_strQuery);
	while( $res_list = pg_fetch_array($qry_list) ){
		$array[] = $res_list;
	}
	return $array;
}
function get_Parts_with_field($code='', $field=''){
	$parts_strQuery = "
		SELECT 
			code, name, details
		FROM 
			parts
		WHERE
			type = 0
	";
	if($code != ''){
		$parts_strQuery .= "
			AND
				code = '".$code."'
		";
	}
	$parts_strQuery .= "	
		ORDER BY code;
	";
	$qry_list = pg_query($parts_strQuery);
	while( $res_list = pg_fetch_array($qry_list) ){
		return $res_list[$field];
	}
}

function get_Parts_autocomplete(){
	$strQuery_parts = "
		SELECT 
			code, name, details, barcode
			--, priceperunit, unitid, svcharge, type, item_count
		FROM 
			\"parts\" 
		ORDER BY code ASC ;
	";
	$qry_parts=@pg_query($strQuery_parts);
	$numrows_parts = pg_num_rows($qry_parts);
	$parts_data = array();
	while($res_parts=@pg_fetch_array($qry_parts)){
		$array[] = array(
			"value" => $res_parts['code'],
			"label" => $res_parts["code"]." # ".$res_parts["barcode"]." # ".$res_parts["name"]." # ".$res_parts["details"],
		);
	}
	return $array;
}

function get_Parts_autocomplete_type_1(){
	$strQuery_parts = "
		SELECT 
			code, name, details, barcode
			--, priceperunit, unitid, svcharge, type, item_count
		FROM 
			\"parts\" 
		WHERE
			type = 1
		ORDER BY code ASC ;
	";
	$qry_parts=@pg_query($strQuery_parts);
	$numrows_parts = pg_num_rows($qry_parts);
	$parts_data = array();
	while($res_parts=@pg_fetch_array($qry_parts)){
		$array[] = array(
			"value" => $res_parts['code'],
			"label" => $res_parts["code"]." # ".$res_parts["barcode"]." # ".$res_parts["name"]." # ".$res_parts["details"],
		);
	}
	return $array;
}
