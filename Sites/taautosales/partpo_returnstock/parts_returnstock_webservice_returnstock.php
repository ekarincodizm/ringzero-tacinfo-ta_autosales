<?php
include_once("parts_returnstock_webservice_returnstock_save.php");

/**
 * 
 */
class SendParts extends SendPartsSave {
	
	function __construct($argument = '') {
		
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
					\"PartsStock\".stock_id, 
					\"PartsStock\".parts_code, 
					\"PartsStock\".stock_remain AS quantity, 
					\"PartsStock\".stock_status AS status
				FROM
					\"PartsStock\"
				INNER JOIN
					parts
					ON
						parts.code = \"PartsStock\".parts_code
				WHERE
					parts.type = 0
					AND
						\"PartsStock\".stock_status = 2
					AND
						\"PartsStock\".parts_code LIKE '%".$parts_code."%'
				ORDER BY \"PartsStock\".parts_code
			)
			UNION
			(
				SELECT 
					\"PartsStock\".stock_id,
					\"PartsStockDetails\".codeid AS parts_code, 
					'1' AS quantity, 
					\"PartsStockDetails\".status AS status
				FROM 
					\"PartsStock\"
				LEFT JOIN 
					\"PartsStockDetails\"
				ON
					\"PartsStock\".stock_id::text = \"PartsStockDetails\".stock_id
				WHERE
					\"PartsStockDetails\".status = 2
					AND
						\"PartsStockDetails\".codeid LIKE '%".$parts_code."%'
				ORDER BY \"PartsStockDetails\".codeid
			)
			ORDER BY parts_code;
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
	
	
	public function search_by_stock_code_old($parts_code = '', $_type = '')
	{
		if($_type == 1){
			$type1 = 1;
			$type2 = 1;
		}
		elseif($_type == 2){
			$type1 = 3;
			$type2 = 2;
		}
		$strQuery = "
			(
				SELECT 
					\"SendPartsDetails\".parts_code,
					\"SendParts\".type, 
					\"SendParts\".status,
					\"SendPartsDetails\".send_quantity AS quantity
				FROM \"SendParts\"
				LEFT JOIN
					\"SendPartsDetails\"
					ON
						\"SendParts\".send_code = \"SendPartsDetails\".send_code
				WHERE
					\"SendPartsDetails\".parts_code LIKE '%".$parts_code."%'
					AND
						\"SendParts\".type = '".$type1."'
					AND
						\"SendParts\".status = 1
			)
			except
			(
				SELECT 
					\"ReturnPartsDetails\".parts_code,
					\"ReturnParts\".type, 
					\"ReturnParts\".status,
					\"ReturnPartsDetails\".return_quantity AS quantity
				FROM \"ReturnParts\"
				LEFT JOIN
					\"ReturnPartsDetails\"
					ON 
						\"ReturnPartsDetails\".return_code = \"ReturnParts\".return_code
				WHERE
					\"ReturnPartsDetails\".parts_code LIKE '%".$parts_code."%'
					AND
						\"ReturnParts\".type = '".$type1."'
					AND
						\"ReturnParts\".status = 1
			)
			ORDER BY parts_code;
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
	
	public function get_parts_detail($part_code)
	{
		$stock = $this->_get_details($part_code);
		
		$result_array = array(
			"name" => $stock['name'],
			"detail" => $stock['details'],
			"type" => $stock['type'],
			"barcode" => $stock['barcode'],
			
			"label" => $part_code." # ".$stock['barcode']." # ".$stock['name']." # ".$stock['details'],
	    	"value" => $part_code
		);
		
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
					type,
					barcode
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
					\"PartsStockDetails\".codeid = '".$parts_code."'
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
}


?>