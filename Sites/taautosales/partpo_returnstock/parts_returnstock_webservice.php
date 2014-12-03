<?php
include_once("parts_returnstock_webservice_returnstock.php");
 
/**
 * 
 */
class Return_stock_default {
	
	function __construct($argument = '') {
		
	}
	
	function get_fuser_fullname($id_user = ''){
		$fuser_strQuery = "
			SELECT 
				fullname
			FROM 
				fuser
			WHERE 
				id_user = '".$id_user."'
			ORDER BY fullname
			;
		";
		$fuser_query = @pg_query($fuser_strQuery);
		while ($fuser_result = @pg_fetch_array($fuser_query)) {
			return $fuser_result["fullname"];
		}
	}
	
	function get_fuser_list_fullname(){
		
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
	
}



/**
 * 
 */
class Return_stock_new extends Return_stock_default {
	
	function __construct($argument = '') {
		
	}
	
	function get_all_SendParts_javascript(){
		$sendParts_strQuery = "
			SELECT
				SendParts_temp.parts_code AS code,
				parts_temp.name,
				parts_temp.details,
				parts_temp.barcode,
				parts_temp.type
			FROM
			(
				SELECT 
					\"SendPartsDetails\".parts_code,
					SUM(\"SendPartsDetails\".send_quantity)
				FROM 
					\"SendParts\" 
				LEFT JOIN 
					\"SendPartsDetails\" 
					ON 
					\"SendParts\".send_code = \"SendPartsDetails\".send_code
				WHERE
					\"SendParts\".status = 1
					AND 
					\"SendParts\".type = '1'
				group by \"SendPartsDetails\".parts_code
			) 
			AS SendParts_temp
			
			LEFT JOIN
			(
				(
				SELECT
					code,
					name,
					details,
					barcode,
					type
				FROM 
					parts
				)
				UNION
				(
				SELECT
					\"PartsStockDetails\".codeid AS code,
					parts.name,
					parts.details,
					parts.barcode,
					parts.type
				  FROM 
					\"PartsStock\"
				left join
					\"PartsStockDetails\"
					on
					\"PartsStock\".stock_id::text = \"PartsStockDetails\".stock_id::text
				left join
					parts
					on
					\"PartsStock\".parts_code = parts.code
				)
				order by code
			) 
			AS parts_temp
				ON 
				SendParts_temp.parts_code = parts_temp.code
			;
		";
		$sendParts_query = @pg_query($sendParts_strQuery);
		while ($sendParts_result = @pg_fetch_array($sendParts_query)) {
			
			/*
			$dt['value'] = $sendParts_result["code"];
			$dt['label'] = 
				$sendParts_result["code"].
				" # ".
				$sendParts_result["barcode"].
				" # ".
				$sendParts_result["name"].
				" # ".
				$sendParts_result["details"];
			$return[] = $dt;
			*/
			$return[] = $sendParts_result;
		}
		return $return;
	}
	
	function get_warehouses(){
		$warehouses_strQuery = "
			SELECT 
				\"wh_id\",
				\"wh_name\"
			FROM
				\"Warehouses\"
			WHERE
				wh_group = 1
		";
		$warehouses_query = pg_query($warehouses_strQuery);
		return pg_fetch_all($warehouses_query);
	}
	
	function get_locate(){
		$locate_strQuery = "
			SELECT 
				\"locate_id\",
				\"locate_name\"
			FROM
				\"Locate\"
		";
		$locate_query = pg_query($locate_strQuery);
		return pg_fetch_all($locate_query);
	}
	
	
	function get_all_ReturnParts(){
		$returnParts_strQuery = "
		SELECT 
			return_code, send_code, type, user_id, return_user_id, date, 
			usedate, status, note, send_date
		FROM 
			\"ReturnParts\";
		";
		$returnParts_query = @pg_query($returnParts_strQuery);
		while ($returnParts_result = @pg_fetch_array($returnParts_query)) {
			$return[] = $returnParts_result;
			
			$dt['value'] = $returnParts_result["return_code"];
			$dt['label'] = $returnParts_result["return_code"];
		}
		return $return;
	}
	
	function get_projectDetailCount(){
		
		$projectDetailCount_strQuery = "
			SELECT 
				project_id,
				count(project_id) AS count
			FROM 
				\"ProjectDetails\"
			WHERE
				cancel = FALSE
			group by project_id; 
		;";
		$projectDetailCount_query = @pg_query($projectDetailCount_strQuery);
		$projectDetailCount_result = @pg_fetch_all($projectDetailCount_query);
		return $projectDetailCount_result;
	}
	
	function get_projectDetail(){
		
		$projectDetail_strQuery = "
			SELECT 
				project_id,
				material_id, 
				use_unit
			FROM 
				\"ProjectDetails\"
			WHERE
				cancel = FALSE 
		;";
		$projectDetail_query = @pg_query($projectDetail_strQuery);
		$projectDetail_result = @pg_fetch_all($projectDetail_query);
		return $projectDetail_result;
	}
	
}

class Return_stock_body1 extends Return_stock_default {
	
	function __construct($argument = '') {
		
	}
	
	function get_ReturnParts(){
		$withdrawalParts_strQuery = "
			SELECT 
				return_code, 
				type, 
				user_id, 
				return_user_id, 
				date, 
				status
			FROM 
				\"ReturnParts\"
			WHERE
				status = 1 ;
		";
		$withdrawalParts_query = @pg_query($withdrawalParts_strQuery);
		$withdrawalParts_numrow = @pg_num_rows($withdrawalParts_query);
		$withdrawalParts_result = pg_fetch_all($withdrawalParts_query);
		
		return 
			array(
				"result" => $withdrawalParts_result,
				"numrow" => $withdrawalParts_numrow
			);
	}
}

class Return_stock_body2 extends Return_stock_default {
	
	function __construct($argument = '') {
		
	}
	
	function get_BrokenParts(){
		$withdrawalParts_strQuery = "
			SELECT 
				broken_code, 
				type, 
				user_id, 
				broken_user_id, 
				date, 
				status
			FROM 
				\"BrokenParts\"
			WHERE
				status = 1 ;
		";
		$withdrawalParts_query = @pg_query($withdrawalParts_strQuery);
		$withdrawalParts_numrow = @pg_num_rows($withdrawalParts_query);
		$withdrawalParts_result = pg_fetch_all($withdrawalParts_query);
		return 
			array(
				"result" => $withdrawalParts_result,
				"numrow" => $withdrawalParts_numrow
			)
		;
	}
}

class Return_stock_view extends Return_stock_default {
	
	//$return_type = '';
	private $withdrawalParts_code = '';
	
	function __construct(
		$return_type = '', 
		$withdrawalParts_code = ''
	) {
			$this->return_type = $return_type;
			$this->withdrawalParts_code = $withdrawalParts_code;
	}
	
	function get_PartsApprove(){
		$appr_strQuery = "
			SELECT 
				user_note, appr_note
			FROM 
				\"PartsApproved\"
			WHERE
				code = '".$this->withdrawalParts_code."' ;
		";
		$appr_query = @pg_query($appr_strQuery);
		$appr_user_note = @pg_fetch_result($appr_query, 0);
		$appr_appr_note = @pg_fetch_result($appr_query, 1);
		return 
			array(
				"user_note" => $appr_user_note,
				"appr_note" => $appr_appr_note
			)
		;
	}
	
	function call_parts($parts_code, $return){
		
		$withdrawalParts_code = pg_escape_string($_GET["code"]);
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
					code = '".$parts_code."'
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
					codeid = '".$parts_code."'
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
			$stock_remain_with_withdrawal_value = $stock_remain - $sum_withdrawal_quantity;
			// ## End Check Quantity ที่ ได้กดเบิกไป แล้วค้างอยู่ใน Queue ##
			
			
			// ##### return value #####
			if($return == "code"){
				return $res_parts['code'];
			}
			
			elseif($return == "name"){
				return $res_parts['name'];
			}
			elseif($return == "details"){
				return $res_parts['details'];
			}
			elseif($return == "stock_remain"){
				return $stock_remain;
			}
			elseif($return == "withdrawal_quantity"){
				return $stock_remain_with_withdrawal_value;
			}
			// ##### End return value #####
		}
	}
	
	function get_ReturnParts(){
		if($this->return_type == "return"){
			$withdrawalParts_strQuery = "
				SELECT 
					return_code, 
					type, 
					user_id, 
					return_user_id, 
					date, 
					status
				FROM 
					\"ReturnParts\"
				WHERE
					status = 1
					AND
					return_code = '{$this->withdrawalParts_code}';
			";
		}
		elseif($this->return_type == "broken"){
			$withdrawalParts_strQuery = "
				SELECT 
					broken_code, 
					type, 
					user_id, 
					broken_user_id, 
					date, 
					status
				FROM 
					\"BrokenParts\"
				WHERE
					status = 1
					AND
					broken_code = '{$this->withdrawalParts_code}';
			";
		}
		
		$withdrawalParts_query = @pg_query($withdrawalParts_strQuery);
		$withdrawalParts_numrow = @pg_num_rows($withdrawalParts_query);
		$withdrawalParts_result = pg_fetch_all($withdrawalParts_query);
		return 
			array(
				"result" => $withdrawalParts_result,
				"numrow" => $withdrawalParts_numrow
			)
		;
	}
	
	function get_ReturnPartsDetail(){
		if($this->return_type == "return"){
			$withdrawalPartsDetails_strQuery = "
				SELECT
					idno, 
					parts_code, 
					return_quantity
				FROM 
					\"ReturnPartsDetails\"
				WHERE
					\"return_code\" = '".$this->withdrawalParts_code."' ;
			";
		}
		elseif($this->return_type == "broken"){
			$withdrawalPartsDetails_strQuery = "
				SELECT
					idno, 
					parts_code, 
					broken_quantity
				FROM 
					\"BrokenPartsDetails\"
				WHERE
					\"broken_code\" = '".$this->withdrawalParts_code."' ;
			";
		}
		
		$withdrawalPartsDetails_query = @pg_query($withdrawalPartsDetails_strQuery);
		$withdrawalPartsDetails_numrow = @pg_num_rows($withdrawalPartsDetails_query);
		$withdrawalPartsDetails_result = @pg_fetch_all($withdrawalPartsDetails_query);
		return 
			array(
				"numrow" => $withdrawalPartsDetails_numrow,
				"result" => $withdrawalPartsDetails_result
			)
		;
	}
}
?>