<?php
// ###################### Function ###########################

include_once ("po_withdrawal_webservice_withdrawal.php");
include_once ("po_withdrawal_webservice_project.php");



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
class PartStock {
	
	function __construct($argument = '') {
		
	}
	
	
	/*
	 * ค้าหารหัสสินค้า partsStockBroken
	 * 
	 * */
	public function search_by_stockBroken_code($parts_code = '')
	{
		$strQuery = "
			(
				SELECT 
					\"PartsStockBroken\".parts_code AS code,
					parts.name,
					parts.details,
					parts.type,
					barcode
				FROM
					\"parts\"
				JOIN
					\"PartsStockBroken\" 
				ON 
					\"PartsStockBroken\".parts_code = parts.code
				WHERE
					\"PartsStockBroken\".parts_code LIKE '%".$parts_code."%'
			)
			UNION
			(
				SELECT 
					\"PartsStockBrokenDetails\".codeid AS code,
					parts.name,
					parts.details,
					'3' AS type,
					barcode
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
				WHERE
					\"PartsStockBrokenDetails\".codeid LIKE '%".$parts_code."%'
			)
			ORDER BY code;
		";
		
		$query = pg_query($strQuery);
		$numrow = pg_num_rows($query);
		
		if($numrow > 0){
			return pg_fetch_all($query);
		}
		else{
			return null;
		}
		
	}
	
	
	/*
	 * ค้าหารหัสสินค้า parts
	 * 
	 * */
	public function search_by_stock_code($parts_code = '')
	{
		$strQuery = "
			(
				SELECT 
					code,
					name,
					details,
					type,
					barcode
				FROM
					\"parts\"
				WHERE 
					code LIKE '%".$parts_code."%'
					OR
					barcode LIKE '%".$parts_code."%'
			)
			UNION
			(
				SELECT 
					\"PartsStockDetails\".codeid AS code,
					parts.name,
					parts.details,
					'3' AS type,
					barcode
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
					\"PartsStockDetails\".codeid LIKE '%".$parts_code."%'
			)
			ORDER BY code;
		";
		$query = pg_query($strQuery);
		$numrow = pg_num_rows($query);
		
		if($numrow > 0){
			return pg_fetch_all($query);
		}
		else{
			return null;
		}
		
	}
	
	
	
	/*
	 * Return 
	 * ชื่อสินค้า
	 * รายละเอียดสินค้า  
	 * จำนวนสินค้าคงคลัง
	 * จำนวนสินค้าสูงสุดที่เบิกได้ 
	*/
	
	public function get_stock_detail_and_aval($part_code)
	{
		$result_array = array();
		$stock = $this->_get_details($part_code);
		
		$result_array['code'] = $stock['code'];
		$result_array['name'] = $stock['name'];
		$result_array['detail'] = $stock['details'];
		
		$stock_remain = $this->_get_stock_remain($part_code);
		$result_array['stock_remain'] = intval($stock_remain["count"]);
		
		$sum_withdrawal = $this->_get_sum_withdrawal($part_code);
		$result_array['sum_withdrawal'] = intval($sum_withdrawal["withdrawal_quantity"]);
		
		// echo $result_array['sum_withdrawal'];
		// pg_query("ROLLBACK");
		// exit;
		
		if($result_array['stock_remain'] == null){
			$result_array['stock_remain'] = 0;
		}
		
		$result_array["stock_aval"] = intval($result_array['stock_remain']) - intval($result_array['sum_withdrawal']);
		
		return $result_array;
	}
	
	public function get_stock_broken_detail_and_aval($part_code){
		$result_array = array();
		$stock = $this->_get_details($part_code);
		
		$result_array['code'] = $stock['code'];
		$result_array['name'] = $stock['name'];
		$result_array['detail'] = $stock['details'];
		
		$stock_remain = $this->_get_stock_broken_remain($part_code);
		$result_array['stock_remain'] = intval($stock_remain["count"]);
		
		$sum_withdrawal = $this->_get_sum_withdrawal($part_code);
		$result_array['sum_withdrawal'] = intval($sum_withdrawal["withdrawal_quantity"]);
		
		// echo $result_array['sum_withdrawal'];
		// pg_query("ROLLBACK");
		// exit;
		
		if($result_array['stock_remain'] == null){
			$result_array['stock_remain'] = 0;
		}
		
		$result_array["stock_aval"] = intval($result_array['stock_remain']) - intval($result_array['sum_withdrawal']);
		
		return $result_array;
	}
	
	// อ่าน Detail ของ Parts และ Parts ที่มีรหัสแยกย่อย
	private function _get_details($parts_code)
	{
		$strQuery = "
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
					OR
					barcode = '".$parts_code."'
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
		$query = pg_query($strQuery);
		$numrow = pg_num_rows($query);
		
		if($numrow == 1){
			return pg_fetch_array($query);
		}
		else{
			return null;
		}
	}
	
	
	//SQL For If parts_code is in the PartsStockDetails or Not
	private function _has_multiple($part_code){
		
		$strQuery = "
			select count(*) as count from \"PartsStockDetails\" where codeid = '".$part_code."';
		";
		$query = pg_query($strQuery);
		$numrow = pg_num_rows($query);
	
		$res =	pg_fetch_array($query);
		if($res['count']==0){
			return false;
		}else{
			return true;
		}
		
	}
	
	
	// จำนวนสินค้าในคลัง
	private function _get_stock_remain($part_code)
	{
		//SQL For If parts_code is in the PartsStockDetails or Not
		
		if($this->_has_multiple($part_code)){
			
			$strQuery = "
				SELECT 
					codeid,
					count(*) AS count
				FROM
					\"PartsStockDetails\"
				WHERE 
					codeid = '".$part_code."'
					AND
					status = 1
				GROUP BY codeid
			";
			$query = pg_query($strQuery);
			$numrow = pg_num_rows($query);
			
			return pg_fetch_array($query);
			
		}
		else{
			
			$strQuery = "
				SELECT 
					parts_code, 
					sum(stock_remain) AS count
				FROM \"PartsStock\"
				where 
					parts_code = '".$part_code."'
				group by parts_code
			  ;
			";
			$query = pg_query($strQuery);
			$numrow = pg_num_rows($query);
			
			
			if($numrow == 1){
				return pg_fetch_array($query);
			}
			else{
				return null;
			}
		}
	}
	
	// จำนวนสินค้าในคลัง
	private function _get_stock_broken_remain($part_code)
	{
		//SQL For If parts_code is in the PartsStockDetails or Not
		
		if($this->_has_multiple($part_code)){
			
			$strQuery = "
				SELECT 
					codeid,
					count(*) AS count
				FROM
					\"PartsStockBrokenDetails\"
				WHERE 
					codeid = '".$part_code."'
					AND
					status = 1
				GROUP BY codeid
			";
			$query = pg_query($strQuery);
			$numrow = pg_num_rows($query);
			
			return pg_fetch_array($query);
			
		}
		else{
			
			$strQuery = "
				SELECT 
					parts_code, 
					sum(stock_remain) AS count
				FROM \"PartsStockBroken\"
				where 
					parts_code = '".$part_code."'
				group by parts_code
			  ;
			";
			$query = pg_query($strQuery);
			$numrow = pg_num_rows($query);
			
			
			if($numrow == 1){
				return pg_fetch_array($query);
			}
			else{
				return null;
			}
		}
	}
	
	
	// //Check รหัส parts code ว่า มีอยู่ใน Withdrawal หรือเปล่า
	// private function _Is_Exist_partscode_in_withdrawal($part_code){
// 		
		// $strQuery = "
			// SELECT
				// count(*) AS count
			// FROM
				// \"WithdrawalPartsDetails\"
			// LEFT JOIN
// 				
			// WHERE 
				// parts_code = '".$part_code."'
		// ";
		// $query = pg_query($strQuery);
		// $numrow = pg_num_rows($query);
// 	
		// $res =	pg_fetch_array($query);
		// if($res['count']==0){
			// return false;
		// }else{
			// return true;
		// }
// 		
	// }
	
	private function _get_sum_withdrawal ($part_code)
	{
		// if($this->_Is_Exist_partscode_in_withdrawal($part_code)){
			
			$strQuery = "
				SELECT 
					\"WithdrawalPartsDetails\".parts_code,
					sum(withdrawal_quantity) AS withdrawal_quantity
				FROM 
					\"WithdrawalParts\"
				LEFT JOIN 
					\"WithdrawalPartsDetails\"
				ON 
					\"WithdrawalPartsDetails\".withdrawal_code = \"WithdrawalParts\".code
				WHERE 
					\"WithdrawalParts\".status IN (1,2,3)
					AND 
						\"WithdrawalPartsDetails\".parts_code = '".$part_code."'
					AND
						\"WithdrawalPartsDetails\".status = 1
				group by 
					\"WithdrawalPartsDetails\".parts_code
				;
			";
			$query = pg_query($strQuery);
			$numrow = pg_num_rows($query);
			
			// echo $strQuery;
			// pg_query("ROLLBACK");
			// exit;
			
			if($numrow > 0){
				return pg_fetch_array($query);
			}
			else{
				return array(
					"parts_code" => $part_code,
					"withdrawal_quantity" => 0
				);
			}
			
			
		// }
		// else{
// 			
		// }
		
	}
	
}






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
 * 
 */
class Withdrawal_edit_body {
	
	private $withdrawal_code = '';
	
	function __construct($withdrawal_code = '') {
		$this->withdrawal_code = $withdrawal_code;
	}
	
	function get_approve()
	{
		$appr_strQuery = "
			SELECT 
				user_note
			FROM 
				\"PartsApproved\"
			WHERE
				code = '".$this->withdrawal_code."' ;
		";
		$appr_query = @pg_query($appr_strQuery);
		$appr_user_note = @pg_fetch_result($appr_query, 0);
		return $appr_user_note;
	}
	
	function get_withdrawalParts(){
		$withdrawalParts_strQuery = "
			SELECT 
				code, 
				type, 
				user_id, 
				withdraw_user_id, 
				date, 
				usedate, 
				status,
				project_id,
				project_quantity
			FROM 
				\"WithdrawalParts\"
			WHERE
				status = 1 
				AND
				code = '{$this->withdrawal_code}';
		";
		$withdrawalParts_query = @pg_query($withdrawalParts_strQuery);
		$withdrawalParts_numrow = @pg_num_rows($withdrawalParts_query);
		$withdrawalParts_result = pg_fetch_all($withdrawalParts_query);
		return $withdrawalParts_result;
	}
	
	function get_fuser($user_id = '')
	{
		$fuser_strQuery = "
			SELECT 
				fullname
			FROM 
				fuser
			WHERE
				id_user = '{$user_id}' ;
		";
		$fuser_query = @pg_query($fuser_strQuery);
		$fuser_result = @pg_fetch_all($fuser_query);
		return $fuser_result;
	}
	
	function get_fuser_list()
	{
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
	
	function get_project()
	{
		$project_strQuery = "
			SELECT 
				project_id, name
			FROM 
				\"Projects\"
			WHERE
				cancel = FALSE;
		";
		$project_query = @pg_query($project_strQuery);
		$project_result = @pg_fetch_array($project_query);
		return $project_result;
	}
	
	
	// Count amount of PartsStocks
	function call_parts($parts_code, $return){
		// $withdrawalParts_code = pg_escape_string($_GET["code"]);
		$withdrawalParts_code = $this->withdrawal_code;
		
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
			elseif($res_parts["type"] == 3){
				$stock_remain = 1;
			}
			
			if($stock_remain == "" || $stock_remain == NULL){
				$stock_remain = 0;
			}
			// ## End Check Stock_remain ##
			
			
			// ## Check Quantity ที่ ได้กดเบิกไป แล้วค้างอยู่ใน Queue ##
			/*
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
			*/
			
			
			// ## Check Quantity ที่ ได้กดเบิกไป แล้วค้างอยู่ใน Queue ##
			///*
			$sum_withdrawal_quantity_strQuery = "
				SELECT 
					sum(withdrawal_quantity) AS withdrawal_quantity
				FROM 
					\"WithdrawalParts\"
				LEFT JOIN 
					\"WithdrawalPartsDetails\"
				ON 
					\"WithdrawalPartsDetails\".withdrawal_code = \"WithdrawalParts\".code
				WHERE 
					\"WithdrawalParts\".status IN (1,2,3)
					AND 
						\"WithdrawalPartsDetails\".parts_code = '".$parts_code."'
				group by 
					\"WithdrawalPartsDetails\".parts_code
				;
			";
			$sum_withdrawal_quantity_query = pg_query($sum_withdrawal_quantity_strQuery);
			$sum_withdrawal_quantity = @pg_fetch_result($sum_withdrawal_quantity_query, 0);
			//*/
			
			//ถ้า sum_withdrawal_quantity แล้ว ไม่มีค่า ให้ Set เป็นค่า 0 แทนค่า Null
			if($sum_withdrawal_quantity == ""){
				$sum_withdrawal_quantity = 0;
			}
			
			
			$stock_remain_with_withdrawal_value = $stock_remain - $sum_withdrawal_quantity;
			// ## End Check Quantity ที่ ได้กดเบิกไป แล้วค้างอยู่ใน Queue ##
			
			// echo $sum_withdrawal_quantity;
			// exit;
			
			
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
			elseif($return == "type"){
				return $res_parts["type"];
			}
			elseif($return == "get_parts_stock_remain_and_sum"){
				$json = array(
					"stock_remain" => $stock_remain,
					"sum_withdrawal_quantity" => $sum_withdrawal_quantity
				);
				return json_encode($json);
			}
			elseif($return == "edit_body__get_parts_details"){
				
				
				// Calculate How many Quantity left after already withdrawal the Parts
				// จำนวนที่ได้กดเบิกออกไปจางคลังแล้ว ==> เอาค่านี้ ไปรวมกับ จำนวนที่เบิกได้ ถึงจะสามารถ นับได้ว่า เราเบิกได้สูงสุด หลังจากที่นับ จากของที่เบิกไปแล้ว
				$max_send_quantity = 0;
				$view_withdrawal_quantity_strQuery = "
					SELECT
						parts_code,
						SUM(send_quantity) as send_quantity
					FROM 
						v_parts_withdrawal_quantity
					WHERE
						parts_code = '".$parts_code."' 
					group by parts_code
					;
				";
				$view_withdrawal_quantity_query = pg_query($view_withdrawal_quantity_strQuery);
				while ($view_withdrawal_quantity_result = pg_fetch_array($view_withdrawal_quantity_query)) {
					
					$total_send_quantity = $view_withdrawal_quantity_result["send_quantity"];
					
				}
				
				// $total_send_quantity = 0;
				
				$stock_remain_with_withdrawal_value = $stock_remain - $sum_withdrawal_quantity + $total_send_quantity;
				
				
				
				
				$return = array(
					"name" => $res_parts['name'],
					"details" => $res_parts['details'],
					"stock_remain" => $stock_remain,
					"sum_withdrawal_quantity" => $sum_withdrawal_quantity,
					"total_send_quantity" => $total_send_quantity,
					"stock_remain_with_withdrawal_value" => $stock_remain_with_withdrawal_value
				);
				
				return json_encode($return);
			}
			// ##### End return value #####
		}
	}
	
	function get_parts_autocomplete(
		$parts_code = ''
	){
			
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
					WHERE 
						code LIKE '%".$parts_code."%'
					ORDER BY code
					LIMIT 100
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
					WHERE
						codeid LIKE '%".$parts_code."%'
					ORDER BY codeid
					LIMIT 100
				)
				ORDER BY code;
			";
			$qry_parts=@pg_query($strQuery_parts);
			$numrows_parts = pg_num_rows($qry_parts);
			// $parts_data = array();
			while($res_parts=@pg_fetch_array($qry_parts)){
				// $parts_data[] = $res_parts;
				$dt['value'] = $res_parts['code'];
				$dt['label'] = $res_parts["code"]." # ".$res_parts["barcode"]." # ".$res_parts["name"]." # ".$res_parts["details"];
				$dt['type'] = $res_parts["type"];
				
				$dt['code'] = $res_parts['code'];
				$dt['name'] = $res_parts["name"];
				$dt['details'] = $res_parts["details"];
				
				
				$stock_remain = "";
				
				// ## Check Stock_remain ##
				if($res_parts["type"] == 0 || $res_parts["type"] == 1){
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
				}
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
						code <> '".$this->withdrawal_code."'
					GROUP BY parts_code ;
				";
				$v_parts_withdrawal_quantity3_query = @pg_query($v_parts_withdrawal_quantity3_strQuery);
				$sum_withdrawal_quantity = @pg_fetch_result($v_parts_withdrawal_quantity3_query, 0);
				
				//ถ้า sum_withdrawal_quantity แล้ว ไม่มีค่า ให้ Set เป็นค่า 0 แทนค่า Null
				if($sum_withdrawal_quantity == ""){
					$sum_withdrawal_quantity = 0;
				}
				
				$stock_remain_with_withdrawal_value = $stock_remain - $sum_withdrawal_quantity;
				// ## End Check Quantity ที่ ได้กดเบิกไป แล้วค้างอยู่ใน Queue ##
				
				$dt['stock_remain'] = $stock_remain;
				$dt['withdrawal_quantity'] = $stock_remain_with_withdrawal_value;
				
				
				$data_parts[] = $dt;
			}
			if($numrows_parts == 0){
		        $data_parts[] = "ไม่พบข้อมูล";
		    }
			
			
			
			return json_encode($data_parts);
	}
	
	function get_Parts_Details($parts_code){
		
	}
	
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