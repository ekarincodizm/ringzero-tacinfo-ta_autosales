<?php
/**
 * 
 */
class Model_po_receive {
	
	function __construct($argument) {
		
	}

	function read_Parts_Unit($unitid){ //Return UnitName
		$parts_unit_strQuery = "
			SELECT 
				\"unitname\"
			FROM 
				\"parts_unit\"
			WHERE
				\"unitid\" = '".$unitid."'
		";
		$parts_unit_query = pg_query($parts_unit_strQuery);
		while($parts_unit_result = pg_fetch_array($parts_unit_query)){
			echo $parts_unit_result["unitname"];
		}
	}
	
	function received_quantity_check($parts_pocode){
		
		// Initial
		$isList = 0;
		
		// แสดง PO Details
		$purchaseOrderPartsDetails_strQuery = "
			SELECT 
				* 
			FROM 
				\"PurchaseOrderPartsDetails\"
			JOIN 
				\"parts\"
			ON
				\"parts\".code = \"PurchaseOrderPartsDetails\".parts_code
			WHERE 
				\"PurchaseOrderPartsDetails\".parts_pocode = '".$parts_pocode."'
			ORDER BY auto_id ASC 
		";
	    $purchaseOrderPartsDetails_query = pg_query($purchaseOrderPartsDetails_strQuery);
		$purchaseOrderPartsDetails_numrow = pg_num_rows($purchaseOrderPartsDetails_query); 
	    while($purchaseOrderPartsDetails_result = pg_fetch_array($purchaseOrderPartsDetails_query)){
	    	
			// Check Received Quantity Count
			$received_quantity_check_strQuery = "
				select 
					parts_code,
					SUM(rcv_quantity) AS rcv_quantity_count
				from 
					\"PartsReceivedDetails\" 
				where 
					parts_rcvcode IN 
					(
						select parts_rcvcode 
						from \"PartsReceived\" 
						where parts_pocode = '".$parts_pocode."'
					)
					AND
					parts_code = '".$purchaseOrderPartsDetails_result['parts_code']."'
				group by parts_code 
				;
			";
			$received_quantity_check_query = pg_query($received_quantity_check_strQuery);
			while($received_quantity_check_result = pg_fetch_array($received_quantity_check_query)){
				
				if($purchaseOrderPartsDetails_result['quantity'] - $received_quantity_check_result["rcv_quantity_count"] == 0){
					$isList++;
				}
			}
		}
		
		//For Check that There are numrow == the amount of 0 units from that Parts
		if($purchaseOrderPartsDetails_numrow == $isList){
			return FALSE;
		}
		else{
			return TRUE;
		}
	}
	
	function get_purchaseOrderPartsDetails($parts_pocode = ''){
		//แสดง PO Details
		$purchaseOrderPartsDetails_strQuery = "
			SELECT 
				* 
			FROM 
				\"PurchaseOrderPartsDetails\" 
			JOIN 
				\"parts\"
			ON
				\"parts\".code = \"PurchaseOrderPartsDetails\".parts_code
			WHERE 
				\"PurchaseOrderPartsDetails\".parts_pocode = '".$parts_pocode."'
			ORDER BY auto_id ASC 
		";
	    $purchaseOrderPartsDetails_query = pg_query($purchaseOrderPartsDetails_strQuery);
	    while($purchaseOrderPartsDetails_result = pg_fetch_array($purchaseOrderPartsDetails_query)){
	    	$return[] = $purchaseOrderPartsDetails_result;
		}
		return $return;
	}
	
	function get_purchaseOrderPart($display_type = '', $display_type2 = '', $po_id = ''){
		//แสดง PO
		$purchaseOrderPart_strQuery = "
			SELECT * 
			FROM 
				\"PurchaseOrderPart\"
		";
		
		// echo "display_type = ".$display_type."<br />";
		// echo "display_type2 = ".$display_type2."<br />";
		// echo "po_id = ".$po_id."<br />";
		
		if($display_type == "2"){
			// if($display_type2 == "2"){
				$purchaseOrderPart_strQuery .= "
					LEFT JOIN 
						\"PartsApproved\"
					ON
						\"PartsApproved\".code = \"PurchaseOrderPart\".parts_pocode
				";
			// }
		}
		
		$purchaseOrderPart_strQuery .= "
			WHERE 
				\"PurchaseOrderPart\".\"status\" = '2'
		";
		
		if($display_type == "2"){
			if($display_type2 == "2"){
				$purchaseOrderPart_strQuery .= "
					AND
					(
						\"PartsApproved\".user_id = '000'
						AND
						\"PartsApproved\".user_note = 'ระบบอัตโนมัติ'
						AND
						\"PartsApproved\".appr_id = '000'
						AND
						\"PartsApproved\".appr_note = 'ระบบอัตโนมัติ'
					)
				";
			}
			elseif($display_type2 == "1"){
				$purchaseOrderPart_strQuery .= "
					AND NOT
					(
						\"PartsApproved\".user_id = '000'
						AND
						\"PartsApproved\".user_note = 'ระบบอัตโนมัติ'
						AND
						\"PartsApproved\".appr_id = '000'
						AND
						\"PartsApproved\".appr_note = 'ระบบอัตโนมัติ'
					)
				";
			}
		}
		elseif($display_type == "3"){
			$purchaseOrderPart_strQuery .= "
				AND
				\"PurchaseOrderPart\".parts_pocode LIKE '%".$po_id."%'
			";
		}
		
		$purchaseOrderPart_strQuery .= "
			ORDER BY parts_pocode ASC ;
		";
		
		$purchaseOrderPart_query = pg_query($purchaseOrderPart_strQuery);
		$purchaseOrderPart_numrow = pg_num_rows($purchaseOrderPart_query);
		while($purchaseOrderPart_result = pg_fetch_array($purchaseOrderPart_query)){
			$return[] = $purchaseOrderPart_result;
		}
		if($purchaseOrderPart_numrow != 0){
			return $return;
		}
		else{
			return null;
		}
	}
	
	function get_received_quantity($parts_pocode, $parts_code){
		/*
		$received_strQuery = "
			SELECT 
				* 
			FROM 
				v_parts_received__quantity_2
			WHERE
				parts_pocode = ''
		";
		*/
		$received_quantity_strQuery = "
			select 
				parts_code,
				SUM(rcv_quantity) AS rcv_quantity_count
			from 
				\"PartsReceivedDetails\" 
			where 
				parts_rcvcode IN 
				(
					select parts_rcvcode 
					from \"PartsReceived\" 
					where parts_pocode = '".$parts_pocode."'
				) 
				AND
				parts_code = '".$parts_code."'
			group by parts_code ;
		";
		
		$received_quantity_query = pg_query($received_quantity_strQuery);
		$received_quantity_numrow = pg_num_rows($received_quantity_query);
		
		// if($received_quantity_numrow == 1){
			// $rcv_quantity_result = pg_fetch_result($received_quantity_query, 0, "rcv_quantity_count");
		// }
		$rcv_quantity_result = pg_fetch_all($received_quantity_query);
		
	 	return 
			array(
				"numrow" => $received_quantity_numrow,
				"rcv_quantity_count" => $rcv_quantity_result
			);
	}
	
}




function get_partsApproved(){
	$partsApproved_strQuery = "
		SELECT
			code, user_id, user_note, user_timestamp, appr_id, appr_note, 
			appr_timestamp
		FROM 
			\"PartsApproved\";
	";
	
}









/**
 * ############# PO_Receive_detail #############
 * 
 * ################### Model ###################
 */
class Model_po_receive_detail{
	
	private $parts_pocode = '';
	
	function __construct($parts_pocode) {
		$this->parts_pocode = $parts_pocode;
	}
	
	//Find purchaseOrderPart Parts_name, Parts_Detail
	function find_Parts_value($parts_code, $output){
		$purchaseOrderPart_strQuery = "
			SELECT
				\"code\",
				\"name\",
				\"details\"
			FROM
				\"parts\"
			WHERE 
				\"code\" = '{$parts_code}'; 
		";
		$purchaseOrderPart_query = pg_query($purchaseOrderPart_strQuery);
		while ($purchaseOrderPart_result = pg_fetch_array($purchaseOrderPart_query)) {
			echo $purchaseOrderPart_result[$output];
		}
	}
	
	function find_Parts_unit_value($unitid){
		$strQuery_parts_unit = "
			SELECT unitname
			FROM \"parts_unit\" 
			WHERE
				unitid = '{$unitid}'
		";
		$qry_parts_unit = @pg_query($strQuery_parts_unit);
		while($res_parts_unit = @pg_fetch_array($qry_parts_unit)){
			echo $res_parts_unit['unitname'];
		}
	}
	
	function get_purchaseOrderPart($parts_pocode = ''){
		$purchaseOrderPart_strQuery = "
			SELECT
				\"parts_pocode\",\"date\",\"type\",\"copypo_id\",
				\"credit_terms\",\"app_sentpartdate\",\"esm_paydate\",\"vender_id\",
				\"vat_status\",
				\"subtotal\",\"pcdiscount\",\"discount\",\"bfv_total\",
				\"pcvat\",\"vat\",\"nettotal\",\"status\",\"paid\"
			FROM
				\"PurchaseOrderPart\"
			WHERE 
				\"parts_pocode\" = '{$parts_pocode}'; 
		";
		$purchaseOrderPart_query = pg_query($purchaseOrderPart_strQuery);
		while ($purchaseOrderPart_result = pg_fetch_array($purchaseOrderPart_query)) {
			$return[] = $purchaseOrderPart_result;
		}
		return $return;
	}
	
	function get_all_purchaseOrderPart(){
		$strQuery_PurchaseOrderPart = "
			SELECT 
				\"parts_pocode\"
			FROM
				\"PurchaseOrderPart\"
		";
		$query_PurchaseOrderPart = @pg_query($strQuery_PurchaseOrderPart);
		$numrows_parts = pg_num_rows($query_PurchaseOrderPart);
		$result_PurchaseOrderPart = @pg_fetch_all($query_PurchaseOrderPart);
		return $result_PurchaseOrderPart;
	}
	
	function get_VVenders(){
		$qry = pg_query("SELECT * FROM \"VVenders\" WHERE type_ven = 'M' or type_ven='B' ORDER BY pre_name,cus_name ASC");
		$res = pg_fetch_all($qry);
		return $res;
	}
	
	function get_purchaseOrderPartsDetails(){
		$purchaseOrderPartsDetails_strQuery = "
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
				\"parts_pocode\" = '{$this->parts_pocode}'; 
		";
		$purchaseOrderPartsDetails_query = pg_query($purchaseOrderPartsDetails_strQuery);
		// $purchaseOrderPartsDetails_numrows = pg_num_rows($purchaseOrderPartsDetails_query);
		$purchaseOrderPartsDetails_numrows = 0;
		while ($result = @pg_fetch_array($purchaseOrderPartsDetails_query)) {
			
			// Get the Used Quantity
			$rcv_quantity_count = 0;
			
			$rcv_quantity_count = 
				$this->get_received_quantity(
					$this->parts_pocode,
					$result["parts_code"]
				)
			;
			
			$total_quantity_ = $result["quantity"] - $rcv_quantity_count;
			
			if($total_quantity_ > 0){
				$result["idno"] = ++$purchaseOrderPartsDetails_numrows;
				$purchaseOrderPartsDetails_result[] = $result;
				$total_quantity[] = $total_quantity_;
			}
		}
		
		return
			array(
				"numrow" => $purchaseOrderPartsDetails_numrows,
				"result" => $purchaseOrderPartsDetails_result,
				"total_quantity" => $total_quantity
			)
		;
	}
	
	function get_type_is_assembly(){
		$partsApproved_strQuery = "
			SELECT
				code, user_id, user_note, user_timestamp, appr_id, appr_note, 
				appr_timestamp
			FROM 
				\"PartsApproved\"
			WHERE
				code = '".$this->parts_pocode."' ;
		";
		$partsApproved_query = @pg_query($partsApproved_strQuery);
		if($partsApproved_result = @pg_fetch_array($partsApproved_query)) {
			
			if(
				$partsApproved_result["user_id"] == "000" &&
				$partsApproved_result["user_note"] == "ระบบอัตโนมัติ" &&
				$partsApproved_result["appr_id"] == "000" &&
				$partsApproved_result["appr_note"] == "ระบบอัตโนมัติ"
			){
				return TRUE;
			}
			else{
				return FALSE;
			}
		}
		
	}
	
	function get_Warehouses(){
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
		return $warehouses_result = pg_fetch_all($warehouses_query);
	}
	
	function get_Locate(){
		$locate_strQuery = "
			SELECT 
				\"locate_id\",
				\"locate_name\"
			FROM
				\"Locate\"
		";
		$locate_query = pg_query($locate_strQuery);
		return $locate_result = pg_fetch_all($locate_query);
	}
	
	function get_received_quantity(
		$parts_pocode = '',
		$parts_code = ''
	){
		$received_quantity_strQuery = "
			select 
				parts_code,
				SUM(rcv_quantity) AS rcv_quantity_count
			from 
				\"PartsReceivedDetails\" 
			where 
				parts_rcvcode IN 
				(
					select parts_rcvcode 
					from \"PartsReceived\" 
					where parts_pocode = '".$parts_pocode."'
				) 
				AND
				parts_code = '".$parts_code."'
			group by parts_code ;
		";
		$received_quantity_query = pg_query($received_quantity_strQuery);
		while($received_quantity_result = pg_fetch_array($received_quantity_query)){
			$rcv_quantity_count = $received_quantity_result["rcv_quantity_count"];
		}
		return $rcv_quantity_count;
	}
}

//########## END PO_Receive_detail #############

?>