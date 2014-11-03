<?php 
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


/**
 * 
 */
class Return_stock_new {
	
	function __construct($argument = '') {
		
	}
	
	function get_fuser_fullname($ss_iduser = ''){
		$fuser_strQuery = "
			SELECT 
				fullname, id_user
			FROM fuser
			ORDER BY fullname;
		";
		$fuser_query = @pg_query($fuser_strQuery);
		while ($fuser_result = @pg_fetch_array($fuser_query)) {
			if($fuser_result["id_user"] == $ss_iduser){
				return $fuser_result["fullname"];
			}
		}
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
}

class Return_stock_body1 {
	
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

class Return_stock_body2 {
	
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

class Return_stock_view {
	
	private $return_type = '';
	private $withdrawalParts_code = '';
	
	function __construct(
		$return_type = '', 
		$withdrawalParts_code = ''
	) {
			$this->return_type = $return_type;
			$this->withdrawalParts_code = $withdrawalParts_code;
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
			);
	}
	
}
?>