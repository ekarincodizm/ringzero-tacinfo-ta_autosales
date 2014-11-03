<?php
include_once("../include/config.php");
include_once("../include/function.php");

//Input
$key = strtolower(pg_escape_string($_GET['term']));

$strQuery_parts = "
	(
		SELECT 
			code,
			name,
			details,
			type
		FROM
			\"parts\"
		WHERE lower(code) LIKE '%".$key."%'
	)
	UNION
	(
		SELECT 
			\"PartsStockDetails\".codeid AS code,
			parts.name,
			parts.details,
			'3' AS type
		FROM
			\"parts\"
		JOIN
			\"PartsStock\" 
		ON 
			\"PartsStock\".parts_code = parts.code
			
		LEFT JOIN 
			\"PartsStockDetails\"
		ON 
			 \"PartsStockDetails\".stock_id::text = \"PartsStock\".stock_id::text
		WHERE lower(code) LIKE '%".$key."%'
	)
	ORDER BY code;
";
$qry_parts=@pg_query($strQuery_parts);
$numrows_parts = pg_num_rows($qry_parts);
// $parts_data = array();
while($res_parts=@pg_fetch_array($qry_parts)){
	// $parts_data[] = $res_parts;
	$dt['value'] = $res_parts['code'];
	$dt['label'] = $res_parts["code"]." # ".$res_parts["name"]." # ".$res_parts["details"];
	$dt['type'] = $res_parts["type"];
	$parts_matches[] = $dt;
	
	$stock_remain = "";
	
	// ## Check Stock_remain ##
	if($res_parts["type"] == 0 || $res_parts["type"] == 1){
		$v_parts_stock__count_per_parts_code_strQuery = "
			SELECT 
				stock_remain
			FROM 
				v_parts_stock__count_per_parts_code
			WHERE
				parts_code = '".$res_parts["code"]."'
		";
		$v_parts_stock__count_per_parts_code_query = @pg_query($v_parts_stock__count_per_parts_code_strQuery);
		$stock_remain = @pg_fetch_result($v_parts_stock__count_per_parts_code_query, 0);
	}
	// elseif($res_parts["type"] == 1){
		// $v_parts_stock__count_per_parts_code_strQuery = "
			// SELECT 
				// stock_status
			// FROM 
				// v_parts_stock_detail__count_per_parts_code
			// WHERE
				// parts_code = '".$res_parts["code"]."'
		// ";
		// $v_parts_stock__count_per_parts_code_query = @pg_query($v_parts_stock__count_per_parts_code_strQuery);
		// $stock_remain = @pg_fetch_result($v_parts_stock__count_per_parts_code_query, 0);
	// }
	elseif($res_parts["type"] == 3){
		$stock_remain = 1;
	}
	
	if($stock_remain == "" || $stock_remain == NULL){
		$stock_remain = 0;
	}
	// ## End Check Stock_remain ##
	
	
	// ## Check Quantity ที่ ได้กดเบิกไป แล้วค้างอยู่ใน Queue ##
	$v_parts_withdrawal_quantity3_strQuery = "
		SELECT 
			sum(withdrawal_quantity) AS sum_withdrawal_quantity
		FROM 
			v_parts_withdrawal_quantity3
		WHERE 
			withdrawal_status IN (1,2,3)
			AND 
			withdrawal_detail_status = 1
			AND
			parts_code = '".$res_parts["code"]."'
			AND
			code <> '".$withdrawalParts_code."'
		GROUP BY parts_code ;
	";
	$v_parts_withdrawal_quantity3_query = @pg_query($v_parts_withdrawal_quantity3_strQuery);
	$sum_withdrawal_quantity = @pg_fetch_result($v_parts_withdrawal_quantity3_query, 0);
	
	//ถ้า sum_withdrawal_quantity แล้ว ไม่มีค่า ให้ Set เป็นค่า 0 แทนค่า Null
	if($sum_withdrawal_quantity == ""){
		$sum_withdrawal_quantity = 0;
	}
	// ## End Check Quantity ที่ ได้กดเบิกไป แล้วค้างอยู่ใน Queue ##
}
if($numrows_parts == 0){
    $parts_matches[] = "ไม่พบข้อมูล";
}

// $parts_matches = array_slice($parts_matches, 0, 100);
?><?php
// Calculate How many Quantity left after already withdrawal the Parts
// var total_send_quantity_array = new Array();

// Calculate How many Quantity left after already withdrawal the Parts
// จำนวนที่ได้กดเบิกออกไปจางคลังแล้ว ==> เอาค่านี้ ไปรวมกับ จำนวนที่เบิกได้ ถึงจะสามารถ นับได้ว่า เราเบิกได้สูงสุด หลังจากที่นับ จากของที่เบิกไปแล้ว
$max_send_quantity = 0;
$view_withdrawal_quantity_strQuery = "
	SELECT
		parts_code,
		SUM(send_quantity) as send_quantity
	FROM 
		v_parts_withdrawal_quantity
	group by parts_code ;
";
$view_withdrawal_quantity_query = pg_query($view_withdrawal_quantity_strQuery);
while ($view_withdrawal_quantity_result = pg_fetch_array($view_withdrawal_quantity_query)) {
	$max_send_quantity = $view_withdrawal_quantity_result["send_quantity"];
}
echo json_encode($parts_matches);
?>
