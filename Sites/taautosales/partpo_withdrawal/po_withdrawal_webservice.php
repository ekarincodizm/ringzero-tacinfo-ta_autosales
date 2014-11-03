<?php
// ###################### Function ###########################

function get_projectName($project_id){
	$project_strQuery = "
		SELECT 
			project_id, name
		FROM 
			\"Projects\"
		WHERE
			cancel = FALSE
			AND
			project_id = '".$project_id."' ;
	";
	$project_query = @pg_query($project_strQuery);
	while ($project_result = @pg_fetch_array($project_query)) {
		return $project_result["name"]; 
	}
}

// #################### End Function #########################


/**
 * 
 */
class Withdrawal_new_body {
	
	function __construct($argument = '') {
		
	}
	
	function get_fuser(){
		$fuser_strQuery = "
			SELECT 
				fullname, id_user
			FROM fuser
			ORDER BY fullname;
		";
		$fuser_query = @pg_query($fuser_strQuery);
		$fuser_result = @pg_fetch_all($fuser_query);
		return $fuser_result;
	}
	
	// ########################### Generate Parts ##############################
	
	function get_parts_details(){
		$strQuery_parts = "
			(
				SELECT 
					code,
					name,
					details,
					type,
					barcode
				FROM
					\"parts\"
			)
			UNION
			(
				SELECT 
					\"PartsStockDetails\".codeid AS code,
					parts.name,
					parts.details,
					'3' AS type,
					\"PartsStockDetails\".codeid AS barcode
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
			)
			ORDER BY code;
		";
		$qry_parts=@pg_query($strQuery_parts);
		$numrows_parts = pg_num_rows($qry_parts);
		$res_parts=@pg_fetch_all($qry_parts);
		return 
			array(
				"numrow" => $numrows_parts,
				"result" => $res_parts
			)
		;
	}
	
	function get_v_parts_stock__count_per_parts_code(){
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
		return $stock_remain;
	}
	
	function get_v_parts_withdrawal_quantity3_strQuery(){
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
		return $sum_withdrawal_quantity;
	}
	
	function get_view_withdrawal_quantity(){
		$view_withdrawal_quantity_strQuery = "
			SELECT
				parts_code,
				SUM(send_quantity) as send_quantity
			FROM 
				v_parts_withdrawal_quantity
			group by parts_code ;
		";
		$view_withdrawal_quantity_query = pg_query($view_withdrawal_quantity_strQuery);
		$view_withdrawal_quantity_result = pg_fetch_all($view_withdrawal_quantity_query);
		return $view_withdrawal_quantity_result;
	}
	
	// ######################### END Generate Parts ############################
	
	
	// ########################### Generate PartsBroken ##############################
	
	function get_broken_parts_details(){
		$strQuery_parts = "
				SELECT 
					\"PartsStockBrokenDetails\".codeid AS code,
					parts.name,
					parts.details,
					'3' AS type,
					\"PartsStockBrokenDetails\".codeid AS barcode
				FROM
					\"parts\"
				JOIN
					\"PartsStockBroken\" 
				ON 
					\"PartsStockBroken\".parts_code = parts.code
					
				LEFT JOIN 
					\"PartsStockBrokenDetails\"
				ON 
					 \"PartsStockBrokenDetails\".stock_broken_id::text = \"PartsStockBroken\".stock_broken_id::text
			ORDER BY code;
		";
		$qry_parts=@pg_query($strQuery_parts);
		$numrows_parts = pg_num_rows($qry_parts);
		$res_parts=@pg_fetch_all($qry_parts);
		return 
			array(
				"numrow" => $numrows_parts,
				"result" => $res_parts
			)
		;
	}
	
	function get_broken_v_parts_stock__count_per_parts_code(){
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
		return $stock_remain;
	}
	
	function get_broken_v_parts_withdrawal_quantity3_strQuery(){
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
		return $sum_withdrawal_quantity;
	}
	
	function get_broken_view_withdrawal_quantity(){
		$view_withdrawal_quantity_strQuery = "
			SELECT
				parts_code,
				SUM(send_quantity) as send_quantity
			FROM 
				v_parts_withdrawal_quantity
			group by parts_code ;
		";
		$view_withdrawal_quantity_query = pg_query($view_withdrawal_quantity_strQuery);
		$view_withdrawal_quantity_result = pg_fetch_all($view_withdrawal_quantity_query);
		return $view_withdrawal_quantity_result;
	}
	
	// ######################### END Generate PartsBroken ############################
	
	
}





/**
 *	file = po_withdrawal_send_save.php
 */ 
class Withdrawal_send_save{
	
	function __construct($argument) {
		
	}
	
	
	function get_parts($parts_code, $return){
		
		$parts_strQuery = "
			SELECT *
			FROM
				parts
			WHERE
				code = '".$parts_code."'
		";
		$parts_query = @pg_query($parts_strQuery);
		while($parts_result = @pg_fetch_result($parts_query, $return)){
			return $parts_result[$return];
		}
		
	}
	
	function get_Projects($project_id, $return){
		
		$projects_strQuery = "
			SELECT 
				project_id, name, cancel, product_id
			FROM 
				\"Projects\"
			WHERE
				project_id = '".$project_id."' ;
		";
		$project_query = @pg_query($projects_strQuery);
		while ($project_return = @pg_fetch_array($project_query)) {
			return $project_return[$return];
		}
	}
	
}




?>