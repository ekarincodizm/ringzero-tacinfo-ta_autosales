<?php
include_once("../include/config.php");
include_once("../include/function.php");

/**
 * 
 */
class Model_barcode {
	
	function __construct($argument = '') {
		
	}
	
	function get_parts_type0() {
		$strQuery_parts_type0 = "
			SELECT 
				code,
				name,
				details,
				type
			FROM
				\"parts\"
			WHERE
				type = 0
			ORDER by code;
		";
		$qry_parts_type0=@pg_query($strQuery_parts_type0);
		$numrows_parts_type0 = pg_num_rows($qry_parts_type0);
		$res_parts=@pg_fetch_all($qry_parts_type0);
			
		return
			array(
				"numrow" => $numrows_parts_type0,
				"result" => $res_parts,
			)
		;
	}
	
	function get_parts(){
		
		$strQuery_parts = "
			(
				SELECT 
					code,
					name,
					details,
					type
				FROM
					\"parts\"
				WHERE
					type = 1
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
				WHERE
					\"PartsStockDetails\".codeid <> ''
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
	
	function get_v_parts_stock__count_per_parts_code($parts_code=''){
		// ## Check Stock_remain ##
		
		$v_parts_stock__count_per_parts_code_strQuery = "
			SELECT 
				stock_remain
			FROM 
				v_parts_stock__count_per_parts_code
			WHERE
				parts_code = '".$parts_code."'
		";
		$v_parts_stock__count_per_parts_code_query = @pg_query($v_parts_stock__count_per_parts_code_strQuery);
		$stock_remain = @pg_fetch_result($v_parts_stock__count_per_parts_code_query, 0);
		return $stock_remain;
	}
	
	function get_v_parts_withdrawal_quantity3($parts_code=''){
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
				parts_code = '".$parts_code."'
			GROUP BY parts_code ;
		";
		// AND
			// code <> '".$withdrawalParts_code."'
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
	
}


?>