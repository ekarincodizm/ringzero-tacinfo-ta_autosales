<?php
include_once("../include/config.php");
include_once("../include/function.php");
/**
 * 
 */
class SendPartsSave {
	
	private $id_user = "";
	private $return_type = "";
	private $return_user_id = "";
	private $return_return_user_id = "";
	private $return_date = "";
	private $return_details_array = "";
	private $return_note = "";
	
	private $gen_parts_no = "";
	private $nowdate = "";
	private $nowdateTime = "";
	
	private $status = 0;
	private $txt_error = "";
	
	
	function __construct(){
		
	}
	
	public function InitialVariables(
		$id_user = "",
		$return_type = "",
		$return_user_id = "",
		$return_return_user_id = "",
		$return_date = "",
		$return_details_array = "",
		$return_note = "",
		$stock_lot = ""
	){
		$this->id_user = $_SESSION["ss_iduser"];
		$this->return_type = pg_escape_string($_POST["return_type"]);
		$this->return_user_id = pg_escape_string($_POST["return_user_id"]);
		$this->return_return_user_id = pg_escape_string($_POST["return_return_user_id"]);
		$this->return_date = date("Y-m-d", strtotime(pg_escape_string($_POST['return_date'])));
		$this->return_details_array = json_decode(stripcslashes(pg_escape_string($_POST["return_details_array"])));
		$this->return_note = pg_escape_string($_POST["return_note"]);
	}
	
	public function ReturnParts()
	{
		pg_query("BEGIN WORK");
		$this->status = 0;
		$this->txt_error = array();
		
		$this->nowdate = nowDate();
		$this->nowdateTime = nowDateTime();
		
		$concurrency = $this->Check_concurrency();
		
		if($concurrency){
			$this->gen_parts_no = $this->_gen_parts_no();
			
			// var_dump($this->gen_parts_no);
			// pg_query("ROLLBACK");
			// exit;
			
			if(!(isset($this->gen_parts_no))){
				$this->status++;
			}
			
			if($this->return_type == 1){ //คืนของเข้าสต๊อก
				$this->Return_type_1_Normal();
			}
			elseif($this->return_type == 2){
				$this->Return_type_2_Broken();
			}
			
			$this->_insert_approve();
		}
		else{
			$this->txt_error[] = "Concurrency ไม่สามารถบันทึกได้ มีรหัสที่ได้ทำการเรียบร้อยแล้ว";
			$this->status++;
		}
		
		return $this->_check_is_status();
	}
	
	private function Check_concurrency(){
		$success_false = 0;
		if($this->return_type == 1){
			
			foreach ($this->return_details_array as $value) {
				$idno = $value->idno;
				$parts_type = $value->parts_type;
				$parts_code = $value->parts_code;
				$stock_id = $value->stock_id;
				
				// ### ใช่วิธี นั่งนับเอาว่า ตอนคืน ได้คืน เกินกว่าที่มีอยู่ใน Stock หรือเปล่า ###
				if($parts_type == 0){
					$temp_quantity_return = $quantity_return;
					
					foreach ($this->_select_PartsStock($parts_code) as $get_PartsStock_result) {
						$quantity_sent = $get_PartsStock_result["rcv_quantity"] - $get_PartsStock_result["stock_remain"];
						
						if($temp_quantity_return >= $quantity_sent){
							$temp_quantity_return = $temp_quantity_return - $quantity_sent;
						}
						else{
							break;
						}
					}
					if($temp_quantity_return > 0){
						$success_false++;
						// return FALSE;
					}
					else{
						// return TRUE;
					}
				}
				elseif($parts_type == 3){
					$parts_code_detail = $parts_code;
					$parts_code = substr($parts_code_detail, 0, 7);
					
					$select_PartsStockDetails = $this->_select_PartsStockDetails(TRUE, $parts_code_detail, $stock_id, "0");
					
					// var_dump($select_PartsStockDetails[0]["count"]);
					// var_dump($select_PartsStockDetails);
					// pg_query("ROLLBACK");
					// exit;
					
					if(!$select_PartsStockDetails){
						
					}
					else if($select_PartsStockDetails[0]["count"] > 0){
						$success_false++;
						
					}
				}
			}

			if($success_false > 0){
				return FALSE;
			}
			else{
				return TRUE;
			}
		}
		elseif($this->return_type == 2){
			foreach ($this->return_details_array as $value) {
				$idno = $value->idno;
				$parts_type = $value->parts_type;
				$parts_code = $value->parts_code;
				$stock_id = $value->stock_id;
				
				// ### ใช่วิธี นั่งนับเอาว่า ตอนคืน ได้คืน เกินกว่าที่มีอยู่ใน Stock หรือเปล่า ###
				if($parts_type == 0){
					$temp_quantity_return = $quantity_return;
					
					foreach ($this->_select_PartsStockBroken($parts_code) as $get_PartsStock_result) {
						$quantity_sent = $get_PartsStock_result["rcv_quantity"] - $get_PartsStock_result["stock_remain"];
						
						if($temp_quantity_return >= $quantity_sent){
							$temp_quantity_return = $temp_quantity_return - $quantity_sent;
						}
						else{
							break;
						}
					}
					if($temp_quantity_return > 0){
						// return FALSE;
						$success_false++;
					}
					else{
						// return TRUE;
					}
				}
				elseif($parts_type == 3){
					$parts_code_detail = $parts_code;
					$parts_code = substr($parts_code_detail, 0, 7);
					
					$select_PartsStockBrokenDetails = $this->_select_PartsStockBrokenDetails(TRUE, $parts_code_detail, $stock_id, "0");
					
					if(!$select_PartsStockBrokenDetails){
						
					}
					else if($select_PartsStockBrokenDetails[0]["count"] > 0){
						$success_false++;
						
					}
				}
			}
			
			if($success_false > 0){
				return FALSE;
			}
			else{
				return TRUE;
			}
		}
	}
	
	private function _select_PartsStockBrokenDetails(
		$is_count = FALSE,
		$parts_code_detail = '',
		$stock_id = '',
		$status = ''
	)
	{
		if($is_count == TRUE){
			$PartsStockDetails_strQuery = "
				SELECT 
					codeid,
					count(*) AS count
			";
		}
		else{
			$PartsStockDetails_strQuery = "
				SELECT *
			";
		}
		$PartsStockDetails_strQuery .= "
			FROM
				\"PartsStockBrokenDetails\"
			WHERE
				codeid = '".$parts_code_detail."'
		";
		if($stock_id != ""){
			$PartsStockDetails_strQuery .= "
				AND
				stock_broken_id = '".$stock_id."'
			";
		}
		if($status != ""){
			$PartsStockDetails_strQuery .= "
				AND
				status = '".$status."'
			";
		}
		$PartsStockDetails_strQuery .= "
			GROUP BY
				codeid
			ORDER BY 
				codeid
		";
		
		$PartsStockDetails_query = pg_query($PartsStockDetails_strQuery);
		return pg_fetch_all($PartsStockDetails_query); 
	}
	
	private function _select_PartsStockBroken(
		$parts_code = '',
		$stock_id = ''
	)
	{
		$PartsStockBroken_strQuery = "
			SELECT *
			FROM
				\"PartsStockBroken\"
			WHERE
				parts_code = '".$parts_code."'
		";
		if($stock_id != ""){
			$PartsStockBroken_strQuery = "
				AND
				stock_broken_id = '".$stock_id."'
			";
		}
		$PartsStockBroken_strQuery = "
				AND
				stock_status = '1'
			ORDER BY 
				parts_code
		";
		$PartsStockBroken_query = pg_query($PartsStockBroken_strQuery);
		return pg_fetch_all($PartsStockBroken_query); 
	}
	
	private function _select_PartsStock(
		$parts_code = '',
		$stock_id = ''
	)
	{
		$PartsStock_strQuery = "
			SELECT *
			FROM
				\"PartsStock\"
			WHERE
				parts_code = '".$parts_code."'
		";
		if($stock_id != ""){
			$PartsStock_strQuery = "
				AND
				stock_id = '".$stock_id."'
			";
		}
		$PartsStock_strQuery = "
				AND
				stock_status = '1'
			ORDER BY 
				parts_code
		";
		$PartsStock_query = pg_query($PartsStock_strQuery);
		return pg_fetch_all($PartsStock_query); 
	}
	
	private function _select_PartsStockDetails(
		$is_count = FALSE,
		$parts_code_detail = '',
		$stock_id = '',
		$status = ''
	)
	{
		if($is_count == TRUE){
			$PartsStockDetails_strQuery = "
				SELECT
					codeid, 
					count(*) AS count
			";
		}
		else{
			$PartsStockDetails_strQuery = "
				SELECT *
			";
		}
		$PartsStockDetails_strQuery .= "
			FROM
				\"PartsStockDetails\"
			WHERE
				codeid = '".$parts_code_detail."'
		";
		if($stock_id != ""){
			$PartsStockDetails_strQuery .= "
				AND
				stock_id = '".$stock_id."'
			";
		}
		if($status != ""){
			$PartsStockDetails_strQuery .= "
				AND
				status = '".$status."'
			";
		}
		$PartsStockDetails_strQuery .= "
			GROUP BY 
				codeid
			ORDER BY 
				codeid
		";
		
		$PartsStockDetails_query = pg_query($PartsStockDetails_strQuery);
		
		// $result = pg_fetch_result($PartsStockDetails_query,0);
		
		// var_dump($PartsStockDetails_strQuery);
					// pg_query("ROLLBACK");
					// exit;
		
		return pg_fetch_all($PartsStockDetails_query); 
	}
	
	private function Return_type_1_Normal()
	{
		$this->_insert_ReturnParts();
		
		$this->Return_type_1_Normal_details_array();
	}
	
	private function Return_type_1_Normal_details_array()
	{
		foreach ($this->return_details_array as $value) {
			$idno = $value->idno;
			$parts_type = $value->parts_type;
			$parts_code = $value->parts_code;
			$stock_id = $value->stock_id;
			$quantity_return = $value->quantity_return;
			$wh_id = $value->wh_id;
			$locate_id = $value->locate_id;
			
			$this->__insert_ReturnPartsDetails(
				$idno,
				$parts_type,
				$parts_code,
				$quantity_return,
				$wh_id,
				$locate_id
			);
			
			// ############## Check the parts_type First (is เป็น (รหัสแยกย่อย == 1)) #################
			
			// ################## ไม่มีรหัสแยกย่อย ######################
			if($parts_type == 0){
				$this->Return_type_1_Normal_details_array_parts_type_0(
					$idno,
					$parts_type,
					$parts_code,
					$stock_id,
					$quantity_return,
					$wh_id,
					$locate_id
				);
			}
			
			// ################## มีรหัสแยกย่อย ########################
			elseif($parts_type == 3){
				$this->Return_type_1_Normal_details_array_parts_type_1(
					$idno,
					$parts_type,
					$parts_code,
					$stock_id,
					$quantity_return,
					$wh_id,
					$locate_id
				);
			}
			
		}
	}
	// ######################### END คืนของเข้าสต๊อก #########################
	
	
	// ไม่มีรหัสแยกย่อย
	private function Return_type_1_Normal_details_array_parts_type_0(
		$idno,
		$parts_type,
		$parts_code,
		$stock_id,
		$quantity_return,
		$wh_id,
		$locate_id
	)
	{
		/*
		$this->___return_type_1_Normal__parts_type_0__remove_old_PartsStock(
			$idno,
			$parts_type,
			$parts_code,
			$stock_id,
			$quantity_return,
			$wh_id,
			$locate_id
		);
		*/
		
		$this->___return_type_1_Normal__parts_type_0__insert_PartsStock__RETURNING_stock_id(
			$idno,
			$parts_type,
			$parts_code,
			$quantity_return,
			$wh_id,
			$locate_id
		);
		
		$this->___return_type_1_Normal__parts_type_0__reduce_old_PartsStock(
			$idno,
			$parts_type,
			$parts_code,
			$quantity_return,
			$wh_id,
			$locate_id
		);
		
	}
	
	// มีรหัสแยกย่อย
	private function Return_type_1_Normal_details_array_parts_type_1(
		$idno,
		$parts_type,
		$parts_code,
		$stock_id,
		$quantity_return,
		$wh_id,
		$locate_id
	)
	{
		$parts_code_detail = $parts_code;
		$parts_code = substr($parts_code_detail, 0, 7);
		
		$this->___return_type_1_Normal__parts_type_1__insert_partsStock__RETURNING_stock_id(
			$idno,
			$parts_type,
			$parts_code,
			$quantity_return,
			$wh_id,
			$locate_id,
			$parts_code_detail
		);
		
		$this->___return_type_1_Normal__parts_type_1__reduce_old_PartsStockDetails(
			$idno,
			$parts_type,
			$parts_code,
			$stock_id,
			$quantity_return,
			$wh_id,
			$locate_id,
			$parts_code_detail
		);
		
		$this->___return_type_1_Normal__parts_type_1__check_reduce_old_PartsStock(
			$idno,
			$parts_type,
			$parts_code,
			$stock_id,
			$quantity_return,
			$wh_id,
			$locate_id,
			$parts_code_detail
		);
	}
	
	private function Return_type_2_Broken()
	{
		$this->_insert_BrokenParts();
		
		$this->Return_type_2_Broken_details_array();
	}
	
	private function Return_type_2_Broken_details_array()
	{
		foreach ($this->return_details_array as $value) {
			$idno = $value->idno;
			$parts_type = $value->parts_type;
			$parts_code = $value->parts_code;
			$stock_id = $value->stock_id;
			$quantity_return = $value->quantity_return;
			$wh_id = $value->wh_id;
			$locate_id = $value->locate_id;
			
			
			$this->__insert_BrokenPartsDetails(
				$idno,
				$parts_code,
				$quantity_return
			);
			
			// ############## Check the parts_type First (is เป็น (รหัสแยกย่อย == 1)) #################
			
			// ################## ไม่มีรหัสแยกย่อย ######################
			if($parts_type == 0){
				$this->Return_type_2_Broken_details_array_parts_type_0(
					$idno,
					$parts_type,
					$parts_code,
					$quantity_return,
					$wh_id,
					$locate_id
				);
			}
			
			// ############################### มีรหัสแยกย่อย ###################################
			elseif($parts_type == 3){
				$this->Return_type_2_Broken_details_array_parts_type_1(
					$idno,
					$parts_type,
					$parts_code,
					$stock_id,
					$quantity_return,
					$wh_id,
					$locate_id
				);
			}
		}
	}
	
	private function Return_type_2_Broken_details_array_parts_type_0(
		$idno,
		$parts_type,
		$parts_code,
		$quantity_return,
		$wh_id,
		$locate_id
	)
	{
		$this->___return_type_2_Broken__parts_type_0__insert_PartsStockBroken__RETURNING_stock_broken_id(
			$idno,
			$parts_type,
			$parts_code,
			$quantity_return,
			$wh_id,
			$locate_id
		);
		
		$this->___return_type_2_Broken__parts_type_0__reduce_old_PartsStockBroken(
			$idno,
			$parts_type,
			$parts_code,
			$quantity_return,
			$wh_id,
			$locate_id
		);
		
		/*
		$this->___return_type_2_Broken__parts_type_0__check_reduce_SendPartsDetails(
			$idno,
			$parts_type,
			$parts_code,
			$quantity_return,
			$wh_id,
			$locate_id
		);
		*/
	}
	
	private function Return_type_2_Broken_details_array_parts_type_1(
		$idno,
		$parts_type,
		$parts_code,
		$stock_id,
		$quantity_return,
		$wh_id,
		$locate_id
	)
	{
		$parts_code_detail = $parts_code;
		$parts_code = substr($parts_code_detail, 0, 7);
		
		$this->___return_type_2_Broken__parts_type_1__insert_PartsStockBroken__RETURNING_stock_broken_id(
			$idno,
			$parts_type,
			$parts_code,
			$quantity_return,
			$wh_id,
			$locate_id,
			$parts_code_detail
		);
		
		$this->___return_type_2_Broken__parts_type_1__reduce_old_PartsStockBrokenDetails(
			$idno,
			$parts_type,
			$parts_code,
			$stock_id,
			$quantity_return,
			$wh_id,
			$locate_id,
			$parts_code_detail
		);
		
		// echo $this->status;
		// var_dump($this->txt_error);
		// pg_query("ROLLBACK");
		// exit; 
		
		$this->___return_type_2_Broken__parts_type_1__check_reduce_old_PartsStockBroken(
			$idno,
			$parts_type,
			$parts_code,
			$stock_id,
			$quantity_return,
			$wh_id,
			$locate_id,
			$parts_code_detail
		);
		
	}
	
	/*
	 * การสาร้าง รหัส Return
	 * */
	private function _gen_parts_no()
	{
		// ### เริ่มการสาร้าง รหัส Return ###
		if($this->return_type == 1){
			$type_X = "RTST";
			$kny = 11;
		}
		elseif($this->return_type == 2){
			$type_X = "RTBK";
			$kny = 12;
		}
		
		// - เลขที่ใบเบิก 
		// - คืนขายซ่อม : RTST-YYMMDDNNN 
		// - คืนของเสีย : RTBK-YYMMDDNNN
		
		// YY = Year
		// MM = Month
		// DD = Day
		// NNN = Running Number
		
		// Find B
		$office_id = "";
		$B_StrQuery = "
			SELECT \"office_id\"
			FROM \"fuser\"
			WHERE \"id_user\" = '".$this->id_user."'
		";
		$B_query = pg_query($B_StrQuery);
		while($B_result=@pg_fetch_array($B_query)){
			$office_id = $B_result["office_id"];
		}
		
		//Call Store Function
		$generate_id_StrQuery = "
			select gen_parts_no(
				'".$this->return_date."', 
				'".$type_X."', 
				'".$office_id."', 
				'".$kny."'
			);
		";
		$generate_id = @pg_query($generate_id_StrQuery);
		$gen_parts_no = @pg_fetch_result($generate_id, 0);
		
		if(empty($gen_parts_no)){
			$this->txt_error[] = "สร้าง gen_rec_no ไม่สำเร็จ $generate_id_StrQuery";
			return null;
		}
		else{
			return $gen_parts_no;
		}
		// ### จบการสาร้าง รหัส Return ###
	}
	
	private function _insert_ReturnParts()
	{
		$returnParts_strQuery = "
			INSERT INTO \"ReturnParts\"(
				return_code, 
				type, 
				user_id, 
				return_user_id, 
				date, 
				status, 
				note, 
				return_date
			)
			VALUES (
				'".$this->gen_parts_no."', 
				".$this->return_type.", 
				'".$this->return_user_id."', 
				'".$this->return_return_user_id."', 
				'".$this->return_date."', 
				1, 
				'".$this->return_note."', 
				'".$this->nowdateTime."'
			);
		";
		
		if(!$result=@pg_query($returnParts_strQuery)){
			$this->txt_error[] = "INSERT ReturnParts ไม่สำเร็จ $returnParts_strQuery";
			$this->status++;
	    }
	}
	
	private function __insert_ReturnPartsDetails(
		$idno,
		$parts_type,
		$parts_code,
		$quantity_return,
		$wh_id,
		$locate_id
	)
	{
		$returnPartsDetails_strQuery = "
			INSERT INTO \"ReturnPartsDetails\"
			(
				return_code, 
				idno, 
				parts_code, 
				return_quantity
			)
			VALUES
			(
				'{$this->gen_parts_no}',
				'".$idno."',
				'".$parts_code."',
				'".$quantity_return."'
			);
		";
		
		if(!$result=@pg_query($returnPartsDetails_strQuery)){
			$this->txt_error[] = "INSERT returnPartsDetails ไม่สำเร็จ";
			$this->status++;
	    }
	}
	
	
	private function ___return_type_1_Normal__parts_type_0__remove_old_PartsStock(
		$idno = "",
		$parts_type = '',
		$parts_code = '',
		$stock_id = '',
		$quantity_return = '',
		$wh_id = '',
		$locate_id = ''
	)
	{
		if($parts_type == 0){
			/*
			$sqlQuery = "
				UPDATE \"PartsStock\"
				SET 
					stock_lot=?, parts_rcvcode=?, rcv_date=?, 
					rcv_quantity=?, costperunit=?, stock_remain=?, wh_id=?, locate_id=?, stock_status=?
				WHERE
					 stock_id = ?
					 AND
					 parts_code = ?
					 
				;
			";
			*/
		}
		elseif($parts_type == 1){
			$delete_PartsStockDetails_sqlQuery = "
				UPDATE \"PartsStockDetails\"
				SET 
					status = 0
				WHERE 
					codeid = '".$parts_code."'
					AND
						stock_id = '".$stock_id."'
				;
			";
			if(!$delete_PartsStockDetails_query = pg_query($delete_PartsStockDetails_sqlQuery)){
				$this->txt_error[] = "DELETE old PartsStockDetails ไม่สำเร็จ ".$delete_PartsStockDetails_sqlQuery;
				$this->status++;
			}
		}
		
		
		
	}
	
	
	private function ___return_type_1_Normal__parts_type_0__insert_PartsStock__RETURNING_stock_id(
		$idno = '',
		$parts_type = '',
		$parts_code = '',
		$quantity_return = '',
		$wh_id = '',
		$locate_id = ''
	)
	{
		// ######### INSERT partsStock ##########
		$partsStock_check_strQuery = "
			SELECT 
				parts_code,
				MAX(stock_lot) AS stock_lot
			FROM 
				\"PartsStock\"
			WHERE 
				parts_code = '".$parts_code."' 
			group by parts_code ;
		";
		$partsStock_check_query = pg_query($partsStock_check_strQuery);
		if($partsStock_check_result = pg_fetch_array($partsStock_check_query)){
			$partsStock_strQuery = "
				INSERT INTO \"PartsStock\"
				(
					parts_code, 
					stock_lot, 
					parts_rcvcode, 
					rcv_date, 
					rcv_quantity, 
					costperunit, 
					stock_remain, 
					wh_id, 
					locate_id, 
					stock_status
				)
				VALUES
				(
					'{$parts_code}',
					".($partsStock_check_result["stock_lot"] + 1).",
					'{$this->gen_parts_no}',
					'{$this->return_date}',
					'{$quantity_return}',
					null,
					'{$quantity_return}',
					'{$wh_id}',
					'{$locate_id}',
					'1'
				)
				RETURNING stock_id;
			";
		}
		else{
			
			//insert the PartsStock when there are no parts_code in PartsStock
			$partsStock_strQuery = "
				INSERT INTO \"PartsStock\"
				(
					parts_code, 
					stock_lot, 
					parts_rcvcode, 
					rcv_date, 
					rcv_quantity, 
					costperunit, 
					stock_remain, 
					wh_id, 
					locate_id, 
					stock_status
				)
				VALUES
				(
					'{$parts_code}',
					'1',
					'{$this->gen_parts_no}',
					'{$this->return_date}',
					'{$quantity_return}',
					null,
					'{$quantity_return}',
					'{$wh_id}',
					'{$locate_id}',
					'1'
				)
				RETURNING stock_id;
			";
		}
		$partsStock_query = @pg_query($partsStock_strQuery);
		
		if(!$partsStock_result = @pg_fetch_array($partsStock_query)) {
			$i = 0; //For running number
			
			// Check That, Type PO is 1 or not, if yes, will insert PartsStockDetails each item.
			
			$this->txt_error[] = "INSERT PartsStock ไม่สำเร็จ $partsStock_strQuery";
			$this->status++;
		}
		// ######### END INSERT partsStock ##########
		
	}
	
	private function ___return_type_1_Normal__parts_type_0__reduce_old_PartsStock(
		$idno,
		$parts_type,
		$parts_code,
		$quantity_return,
		$wh_id,
		$locate_id
	)
	{
		// ##################### ทำการลดค่าใน SendPartsDetails (ลดจำนวนที่จะส่ง) ######################
		
		// ### Read How many Send_Quantity for that parts_code ###
		/*
		$get_SendPartsDetails_strQuery = "
			SELECT 
				send_details_id, 
				send_code,
				parts_code, 
				send_quantity
			FROM 
				\"SendPartsDetails\"
			WHERE
				parts_code = '".$parts_code."'
			ORDER BY 
				send_code
			;
		";
		*/
		
		$temp_quantity_return = $quantity_return;
		
		$get_SendPartsDetails_strQuery = "
			SELECT 
				stock_id, parts_code, stock_lot, parts_rcvcode, rcv_date, rcv_quantity, costperunit, stock_remain, wh_id, locate_id, stock_status
			FROM 
				\"PartsStock\"
			WHERE
				parts_code = '".$parts_code."'
			ORDER BY 
				parts_code
			;
		";
		$get_SendPartsDetails_query = @pg_query($get_SendPartsDetails_strQuery);
		while ($get_SendPartsDetails_result = @pg_fetch_array($get_SendPartsDetails_query)) {
			
			$quantity_sent = $get_SendPartsDetails_result["rcv_quantity"] - $get_SendPartsDetails_result["stock_remain"];
			
			if($temp_quantity_return >= $quantity_sent){
				$set_SendPartsDetails_strQuery = "
					UPDATE 
						\"PartsStock\"
					SET 
						stock_remain = rcv_quantity
						AND
						stock_status = 0
					WHERE 
						stock_id = '".$get_SendPartsDetails_result["stock_id"]."'
					;
				";
				$temp_quantity_return = $temp_quantity_return - $quantity_sent;
				if(!$result=@pg_query($set_SendPartsDetails_strQuery)){
			        $this->txt_error[] = "UPDATE reduce PartsStock ไม่สำเร็จ $set_SendPartsDetails_strQuery";
			        $this->status++;
			    }
			}
			else{
				$set_SendPartsDetails_strQuery = "
					UPDATE 
						\"PartsStock\"
					SET 
						stock_remain = ".($get_SendPartsDetails_result["stock_remain"] + $temp_quantity_return)."
					WHERE 
						stock_id = '".$get_SendPartsDetails_result["stock_id"]."'
					;
				";
				if(!$result=@pg_query($set_SendPartsDetails_strQuery)){
			        $this->txt_error[] = "UPDATE reduce PartsStock ไม่สำเร็จ $set_SendPartsDetails_strQuery";
			        $this->status++;
			    }
				$temp_quantity_return = 0;
				// $send_code = $get_SendPartsDetails_query["send_code"];
				
				break;
			}
		}
		
		// return $send_code;
		
		// ################### END ทำการลดค่าใน SendPartsDetails (ลดจำนวนที่จะส่ง) ####################
	}
	
	
	private function ___return_type_1_Normal__parts_type_1__insert_partsStock__RETURNING_stock_id(
		$idno,
		$parts_type,
		$parts_code,
		$quantity_return,
		$wh_id,
		$locate_id,
		$parts_code_detail
	)
	{
		// ######### INSERT partsStock ##########
		$partsStock_check_strQuery = "
			SELECT 
				parts_code,
				MAX(stock_lot) AS stock_lot
			FROM 
				\"PartsStock\"
			WHERE 
				parts_code = '".$parts_code."' 
			group by parts_code ;
		";
		$partsStock_check_query = pg_query($partsStock_check_strQuery);
		if($partsStock_check_result = pg_fetch_array($partsStock_check_query)){
			$partsStock_strQuery = "
				INSERT INTO \"PartsStock\"
				(
					parts_code, 
					stock_lot, 
					parts_rcvcode, 
					rcv_date, 
					rcv_quantity, 
					costperunit, 
					stock_remain, 
					wh_id, 
					locate_id, 
					stock_status
				)
				VALUES
				(
					'{$parts_code}',
					".($partsStock_check_result["stock_lot"] + 1).",
					'{$this->gen_parts_no}',
					'{$this->return_date}',
					'{$quantity_return}',
					null,
					'{$quantity_return}',
					'{$wh_id}',
					'{$locate_id}',
					'1'
				)
				RETURNING stock_id;
			";
		}
		else{
			
			//insert the PartsStock when there are no parts_code in PartsStock
			$partsStock_strQuery = "
				INSERT INTO \"PartsStock\"
				(
					parts_code, 
					stock_lot, 
					parts_rcvcode, 
					rcv_date, 
					rcv_quantity, 
					costperunit, 
					stock_remain, 
					wh_id, 
					locate_id, 
					stock_status
				)
				VALUES
				(
					'{$parts_code}',
					'1',
					'{$this->gen_parts_no}',
					'{$this->return_date}',
					'{$quantity_return}',
					null,
					'{$quantity_return}',
					'{$wh_id}',
					'{$locate_id}',
					'1'
				)
				RETURNING stock_id;
			";
		}
		$partsStock_query = @pg_query($partsStock_strQuery);
		
		if($partsStock_result = @pg_fetch_array($partsStock_query)) {
			
			$i = 0; //For running number
			
			$PartsStockDetails_strQuery = "
				INSERT INTO \"PartsStockDetails\"(
					codeid, 
					stock_id, 
					status, 
					wh_id, 
					locate_id
				)
				VALUES (
					'{$parts_code_detail}', 
					'".$partsStock_result["stock_id"]."',
					'1',
					'',
					''
				);
			";
			
			if(!$result=@pg_query($PartsStockDetails_strQuery)){
		        $this->txt_error[] = "INSERT PartsStockDetails_strQuery ไม่สำเร็จ $PartsStockDetails_strQuery";
		        $this->status++;
		    }
			
			
			
			// ############ For Test ###########
			/*
			$partsStock2_strQuery = "
				SELECT stock_id, parts_code, stock_lot, parts_rcvcode, rcv_date, rcv_quantity, 
				costperunit, stock_remain, wh_id, locate_id, stock_status
				FROM \"PartsStock\"
				where 
				stock_id = '".$partsStock_result["stock_id"]."'
				;
			";
			$partsStock2_query = pg_query($partsStock2_strQuery);
			$partsStock2_result = pg_fetch_all($partsStock2_query);
			var_dump($partsStock2_result);
			// pg_query("ROLLBACK");
			// exit;
			*/
			// ######## End For Test ########## 
			
			// ############ For Test ###########
			/*
			$partsStockDetails2_strQuery = "
				SELECT *
				FROM \"PartsStockDetails\"
				where 
				codeid = '".$parts_code_detail."'
				AND
				stock_id = '".$partsStock_result["stock_id"]."'
				;
			";
			$partsStockDetails2_query = pg_query($partsStockDetails2_strQuery);
			$partsStockDetails2_result = pg_fetch_all($partsStockDetails2_query);
			var_dump($partsStockDetails2_result);
			pg_query("ROLLBACK");
			exit;
			*/
			// ######## End For Test ########## 
			
		}
		else{
			$this->txt_error[] = "INSERT partsStock_strQuery ไม่สำเร็จ $partsStock_strQuery";
	        $this->status++;
		}
		
		// ######### END INSERT partsStock ##########
	}
	
	private function ___return_type_1_Normal__parts_type_1__reduce_old_PartsStockDetails(
		$idno,
		$parts_type,
		$parts_code,
		$stock_id,
		$quantity_return,
		$wh_id,
		$locate_id,
		$parts_code_detail
	)
	{
		// ##################### ทำการลดค่าใน PartsStockDetails (ลดจำนวนที่จะส่ง) ######################
		$reduce_old_PartsStockDetails_strQuery = "
			UPDATE
				\"PartsStockDetails\"
			SET
				status = 0
			WHERE
				codeid = '".$parts_code_detail."'
				AND
				stock_id = '".$stock_id."'
			;
		";
		if(!$reduce_old_PartsStockDetails_query = pg_query($reduce_old_PartsStockDetails_strQuery)){
	        $this->txt_error[] = "UPDATE remove old PartsStockDetails ไม่สำเร็จ $reduce_old_PartsStockDetails_strQuery";
	        $this->status++;
	    }
		
		
		/*
		$check_old_PartsStockDetails_strQuery = "
			SELECT
				*
			FROM
				\"PartsStockDetails\"
			WHERE
				codeid = '".$parts_code_detail."'
				AND
				stock_id = '".$stock_id."'
		";
		$check_old_PartsStockDetails_query = pg_query($check_old_PartsStockDetails_strQuery);
		$check_old_PartsStockDetails_result = pg_fetch_all($check_old_PartsStockDetails_query);
		var_dump($check_old_PartsStockDetails_result);
		pg_query("ROLLBACK");
		exit;
		*/
		// ################### END ทำการลดค่าใน PartsStockDetails (ลดจำนวนที่จะส่ง) ####################
	}
	
	private function ___return_type_1_Normal__parts_type_1__check_reduce_old_PartsStock(
		$idno,
		$parts_type,
		$parts_code,
		$stock_id,
		$quantity_return,
		$wh_id,
		$locate_id,
		$parts_code_detail
	)
	{
		// ##### For Test #####
		/*
		$check_old_PartsStockDetails_strQuery = "
			SELECT
				*
			FROM
				\"PartsStock\"
			WHERE
				stock_id = '".$stock_id."'
		";
		$check_old_PartsStockDetails_query = pg_query($check_old_PartsStockDetails_strQuery);
		$check_old_PartsStockDetails_result = pg_fetch_all($check_old_PartsStockDetails_query);
		var_dump($check_old_PartsStockDetails_result);
		pg_query("ROLLBACK");
		exit;
		*/
		// ##### END For Test #####
		
		$partsStockDetails_strQuery = "
			SELECT
				COUNT(codeid) AS count
			FROM
				\"PartsStockDetails\"
			WHERE 
				status = 2
				AND
				stock_id = '".$stock_id."'
			GROUP BY 
				stock_id;
		";
		$partsStockDetails_query = pg_query($partsStockDetails_strQuery);
		$partsStockDetails_result = pg_fetch_result($partsStockDetails_query, 0);
		if($partsStockDetails_result == 0){
			$delete_partsStock_strQuery = "
				UPDATE 
					\"PartsStock\"
				SET 
					stock_status = 0
				WHERE
					stock_id = '".$stock_id."'
				;
			";
			if(!$delete_partsStock_query = pg_query($delete_partsStock_strQuery)){
				$this->txt_error[] = "UPDATE remove old PartsStock ไม่สำเร็จ $delete_partsStock_strQuery";
	        	$this->status++;
			}
		}
		
		// ##### For Test #####
		/*
		$check_old_PartsStockDetails_strQuery = "
			SELECT
				*
			FROM
				\"PartsStock\"
			WHERE
				stock_id = '".$stock_id."'
		";
		$check_old_PartsStockDetails_query = pg_query($check_old_PartsStockDetails_strQuery);
		$check_old_PartsStockDetails_result = pg_fetch_all($check_old_PartsStockDetails_query);
		var_dump($check_old_PartsStockDetails_result);
		pg_query("ROLLBACK");
		exit;
		*/
		// ##### END For Test #####
	}
	
	private function _insert_BrokenParts()
	{
		$brokenParts_strQuery = "
			INSERT INTO \"BrokenParts\"(
				broken_code, 
				type, 
				user_id, 
				broken_user_id, 
				date, 
				status, 
				note, 
				broken_date
			)
			VALUES (
				'".$this->gen_parts_no."', 
				".$this->return_type.", 
				'".$this->return_user_id."', 
				'".$this->return_return_user_id."', 
				'".$this->return_date."', 
				1, 
				'".$this->return_note."', 
				'".$this->nowdateTime."'
			);
		";
		
		if(!$result=@pg_query($brokenParts_strQuery)){
	        $this->txt_error[] = "INSERT BrokenParts ไม่สำเร็จ $brokenParts_strQuery";
	        $this->status++;
	    }
	}
	
	private function __insert_BrokenPartsDetails(
		$idno,
		$parts_code,
		$quantity_return
	)
	{
		$returnPartsDetails_strQuery = "
			INSERT INTO \"BrokenPartsDetails\"
			(
				broken_code, 
				idno, 
				parts_code, 
				broken_quantity
			)
			VALUES
			(
				'{$this->gen_parts_no}',
				'".$idno."',
				'".$parts_code."',
				'".$quantity_return."'
			);
		";
		
		if(!$result=@pg_query($returnPartsDetails_strQuery)){
	        $this->txt_error[] = "INSERT returnPartsDetails ไม่สำเร็จ $returnPartsDetails_strQuery";
	        $this->status++;
	    }
	}
	
	private function ___return_type_2_Broken__parts_type_0__insert_PartsStockBroken__RETURNING_stock_broken_id(
		$idno,
		$parts_type,
		$parts_code,
		$quantity_return,
		$wh_id,
		$locate_id
	)
	{
		// ######### INSERT PartsStockBroken ##########
		$partsStock_check_strQuery = "
			SELECT 
				parts_code,
				MAX(stock_lot) AS stock_lot
			FROM 
				\"PartsStockBroken\"
			WHERE 
				parts_code = '".$parts_code."' 
			group by parts_code ;
		";
		$partsStock_check_query = pg_query($partsStock_check_strQuery);
		
		if($partsStock_check_result = pg_fetch_array($partsStock_check_query)){
			
			$partsStock_strQuery = "
				INSERT INTO \"PartsStockBroken\"
				(
					parts_code, 
					stock_lot, 
					parts_rcvcode, 
					rcv_date, 
					rcv_quantity, 
					costperunit, 
					stock_remain, 
					wh_id, 
					locate_id, 
					stock_status
				)
				VALUES
				(
					'{$parts_code}',
					".($partsStock_check_result["stock_lot"] + 1).",
					'{$this->gen_parts_no}',
					'{$this->return_date}',
					'{$quantity_return}',
					null,
					'{$quantity_return}',
					'{$wh_id}',
					'{$locate_id}',
					'1'
				)
				RETURNING stock_broken_id;
			";
		}
		else{
			//insert the PartsStock when there are no parts_code in PartsStock
			$partsStock_strQuery = "
				INSERT INTO \"PartsStockBroken\"
				(
					parts_code, 
					stock_lot, 
					parts_rcvcode, 
					rcv_date, 
					rcv_quantity, 
					costperunit, 
					stock_remain, 
					wh_id, 
					locate_id, 
					stock_status
				)
				VALUES
				(
					'{$parts_code}',
					'1',
					'{$this->gen_parts_no}',
					'{$this->return_date}',
					'{$quantity_return}',
					null,
					'{$quantity_return}',
					'{$wh_id}',
					'{$locate_id}',
					'1'
				)
				RETURNING stock_broken_id;
			";
		}
		
		$partsStock_query = @pg_query($partsStock_strQuery);
		
		if($partsStock_result = @pg_fetch_array($partsStock_query)) {
			$i = 0; //For running number
			
			// Check That, Type PO is 1 or not, if yes, will insert PartsStockDetails each item.
		}
		// ######### END INSERT PartsStockBroken ##########
	}
	
	private function ___return_type_2_Broken__parts_type_0__reduce_old_PartsStockBroken(
		$idno,
		$parts_type,
		$parts_code,
		$quantity_return,
		$wh_id,
		$locate_id
	)
	{
		// ##################### ทำการลดค่าใน SendPartsDetails (ลดจำนวนที่จะส่ง) ######################
		
		// ### Read How many Send_Quantity for that parts_code ###
		/*
		$get_SendPartsDetails_strQuery = "
			SELECT 
				send_details_id, 
				send_code,
				parts_code, 
				send_quantity
			FROM 
				\"SendPartsDetails\"
			WHERE
				parts_code = '".$parts_code."'
			ORDER BY 
				send_code
			;
		";
		*/
		
		$temp_quantity_return = $quantity_return;
		
		$get_SendPartsDetails_strQuery = "
			SELECT stock_broken_id, parts_code, stock_lot, parts_rcvcode, rcv_date, rcv_quantity, costperunit, stock_remain, wh_id, locate_id, stock_status
			FROM
				\"PartsStockBroken\"
			WHERE
				parts_code = '".$parts_code."'
			ORDER BY 
				send_code
			;
		";
		$get_SendPartsDetails_query = @pg_query($get_SendPartsDetails_strQuery);
		while ($get_SendPartsDetails_result = @pg_fetch_array($get_SendPartsDetails_query)) {
			
			$quantity_sent = $get_SendPartsDetails_result["rcv_quantity"] - $get_SendPartsDetails_result["stock_remain"];
			
			if($temp_quantity_return >= $quantity_sent){
				$set_SendPartsDetails_strQuery = "
					UPDATE 
						\"PartsStockBroken\"
					SET 
						stock_remain = rcv_quantity
						AND
						stock_status = 0
					WHERE 
						stock_broken_id = '".$get_SendPartsDetails_result["stock_broken_id"]."'
					;
				";
				$temp_quantity_return = $temp_quantity_return - $get_SendPartsDetails_result["send_quantity"];
				if(!$result=@pg_query($set_SendPartsDetails_strQuery)){
					$this->txt_error[] = "UPDATE reduce PartsStockBroken ไม่สำเร็จ $set_SendPartsDetails_strQuery";
					$this->status++;
				}
			}
			else{
				$set_SendPartsDetails_strQuery = "
					UPDATE 
						\"PartsStockBroken\"
					SET 
						stock_remain = ".($get_SendPartsDetails_result["stock_remain"] + $temp_quantity_return)."
					WHERE 
						stock_broken_id = '".$get_SendPartsDetails_result["stock_broken_id"]."'
					;
				";
				if(!$result=@pg_query($set_SendPartsDetails_strQuery)){
			        $this->txt_error[] = "UPDATE reduce PartsStockBroken ไม่สำเร็จ $set_SendPartsDetails_strQuery";
			        $this->status++;
			    }
				
				$temp_quantity_return = 0;
				// $send_code = $get_SendPartsDetails_query["send_code"];
				
				break;
			}
			
		}
		// ################### END ทำการลดค่าใน SendPartsDetails (ลดจำนวนที่จะส่ง) ####################
	}
	
	
	/*
	private function ___return_type_2_Broken__parts_type_0__check_reduce_SendPartsDetails(
		$idno,
		$parts_type,
		$parts_code,
		$quantity_return,
		$wh_id,
		$locate_id
	)
	{
		// ##################### Check About ทำการลดค่าใน SendPartsDetails (ลดจำนวนที่จะส่ง) ######################
		$get_check_sendParts_strQuery = "
			SELECT 
				--send_code,
				SUM(send_quantity) AS send_quantity
			FROM 
				\"SendPartsDetails\"
			WHERE
				parts_code = '".$parts_code."'
				AND
				send_code = '".$send_code."'
			GROUP BY
				send_code
			;
		";
		$get_check_sendParts_query = @pg_query($get_check_sendParts_strQuery);
		while ($get_check_sendParts_result = @pg_fetch_array($get_check_sendParts_query)) {
			
			if($get_check_sendParts_result["send_quantity"] == 0){
				// ##################### ทำการลดค่าใน SendParts (ลดจำนวนที่จะส่ง) ######################
				$sendParts_strQuery = "
					UPDATE 
						\"SendParts\"
					SET 
						status = 0
					WHERE 
						send_code = '".$send_code."'
					;
				";
				if(!$result=@pg_query($sendParts_strQuery)){
			        $this->txt_error[] = "UPDATE remove SendParts ไม่สำเร็จ $sendParts_strQuery";
			        $this->status++;
			    }
				// ################### END ทำการลดค่าใน SendParts (ลดจำนวนที่จะส่ง) ####################
			}
			
		}
		if(!$get_check_sendParts_query){
	        $this->txt_error[] = "UPDATE remove SendPartsDetails ไม่สำเร็จ $sendParts_strQuery";
	        $this->status++;
		}
		// ################### END Check About ทำการลดค่าใน SendPartsDetails (ลดจำนวนที่จะส่ง) ####################
	}
	*/
	
	private function ___return_type_2_Broken__parts_type_1__insert_PartsStockBroken__RETURNING_stock_broken_id(
		$idno,
		$parts_type,
		$parts_code,
		$quantity_return,
		$wh_id,
		$locate_id,
		$parts_code_detail
	)
	{
		// ######### INSERT PartsStockBroken ##########
		$partsStock_check_strQuery = "
			SELECT 
				parts_code,
				MAX(stock_lot) AS stock_lot
			FROM 
				\"PartsStockBroken\"
			WHERE 
				parts_code = '".$parts_code."' 
			group by parts_code ;
		";
		
		$partsStock_check_query = pg_query($partsStock_check_strQuery);
		
		if($partsStock_check_result = pg_fetch_array($partsStock_check_query)){
			$partsStock_strQuery = "
				INSERT INTO \"PartsStockBroken\"
				(
					parts_code, 
					stock_lot, 
					parts_rcvcode, 
					rcv_date, 
					rcv_quantity, 
					costperunit, 
					stock_remain, 
					wh_id, 
					locate_id, 
					stock_status
				)
				VALUES
				(
					'{$parts_code}',
					".($partsStock_check_result["stock_lot"] + 1).",
					'{$this->gen_parts_no}',
					'{$this->return_date}',
					'{$quantity_return}',
					null,
					'{$quantity_return}',
					'{$wh_id}',
					'{$locate_id}',
					'1'
				)
				RETURNING stock_broken_id;
			";
		}
		else{
			//insert the PartsStock when there are no parts_code in PartsStock
			$partsStock_strQuery = "
				INSERT INTO \"PartsStockBroken\"
				(
					parts_code, 
					stock_lot, 
					parts_rcvcode, 
					rcv_date, 
					rcv_quantity, 
					costperunit, 
					stock_remain, 
					wh_id, 
					locate_id, 
					stock_status
				)
				VALUES
				(
					'{$parts_code}',
					'1',
					'{$this->gen_parts_no}',
					'{$this->return_date}',
					'{$quantity_return}',
					null,
					'{$quantity_return}',
					'".$wh_id."',
					'".$locate_id."',
					'1'
				)
				RETURNING stock_broken_id;
			";
		}
		
		if(!$partsStock_query = @pg_query($partsStock_strQuery)){
			$this->txt_error[] = "INSERT PartsStockBroken ไม่สำเร็จ $partsStock_strQuery";
	        $this->status++;
		}
		
		if($partsStock_result = @pg_fetch_array($partsStock_query)) {
			$i = 0; //For running number
			
			// Check That, Type PO is 1 or not, if yes, will insert PartsStockDetails each item.
				// insert PartsStockDetails (each row of item)
					
					$PartsStockDetails_strQuery = "
						INSERT INTO \"PartsStockBrokenDetails\"(
							codeid, 
							stock_broken_id, 
							status, 
							wh_id, 
							locate_id 
						)
						VALUES (
							'{$parts_code_detail}', 
							'".$partsStock_result["stock_broken_id"]."',
							'1',
							'{$wh_id}',
							'{$locate_id}'
						);
					";
					
					if(!$result=@pg_query($PartsStockDetails_strQuery)){
				        $this->txt_error[] = "INSERT PartsStockDetails_strQuery ไม่สำเร็จ $PartsStockDetails_strQuery";
				        $this->status++;
				    }
				// END insert PartsStockDetails (each row of item)
			//END Check that parts_code is parts.type = 1 or not, if yes, Query Insert PartStockDetails
			
			
			// ####### For Test Added PartsStockBroken #######
			/*
			$check_partsStock_strQuery = "
				SELECT *
				FROM
					\"PartsStockBroken\"
				WHERE
					stock_broken_id = '".$partsStock_result["stock_broken_id"]."';
			";
			$check_partsStock_query = pg_query($check_partsStock_strQuery);
			$check_partsStock_result = pg_fetch_all($check_partsStock_query);
			var_dump($check_partsStock_result);
			pg_query("ROLLBACK");
			exit;
			*/
			// ##### END For Test Added PartsStockBroken #####
			
		}
		// ######### END INSERT partsStockBroken ##########
	}
	
	private function ___return_type_2_Broken__parts_type_1__reduce_old_PartsStockBrokenDetails(
		$idno,
		$parts_type,
		$parts_code,
		$stock_id,
		$quantity_return,
		$wh_id,
		$locate_id,
		$parts_code_detail
	){
		// ##################### ทำการลดค่าใน SendPartsDetails (ลดจำนวนที่จะส่ง) ######################
		
		
		$reduce_old_PartsStockBrokenDetails_strQuery = "
			UPDATE
				\"PartsStockBrokenDetails\"
			SET
				status = 1
			WHERE
				codeid = '".$parts_code_detail."'
				AND
				stock_broken_id = '".$stock_id."'
			;
		";
		// $reduce_old_PartsStockBrokenDetails_query = pg_query($reduce_old_PartsStockBrokenDetails_strQuery);
		
		if(!$reduce_old_PartsStockBrokenDetails_query = pg_query($reduce_old_PartsStockBrokenDetails_strQuery)){
	        $this->txt_error[] = "UPDATE remove old PartsStockDetails ไม่สำเร็จ $reduce_old_PartsStockBrokenDetails_strQuery";
	        $this->status++;
	    }
		
		// var_dump($reduce_old_PartsStockBrokenDetails_query);
		// pg_query("ROLLBACK");
		// exit;
		
		
		/*
		$sendParts_strQuery = "
			UPDATE 
				\"SendPartsDetails\"
			SET 
				send_quantity = 0
			WHERE 
				parts_code = '".$parts_code_detail."'
			RETURNING
				send_code
			;
		";
		$sendParts_query = pg_query($sendParts_strQuery);
		while ($sendParts_result = pg_fetch_array($sendParts_query)) {
			
			// ##################### ทำการลดค่าใน SendParts (ลดจำนวนที่จะส่ง) ######################
			$sendParts_strQuery = "
				UPDATE 
					\"SendParts\"
				SET 
					status = 0
				WHERE 
					send_code = '".$sendParts_result["send_code"]."';
			";
			if(!$result=@pg_query($sendParts_strQuery)){
		        $this->txt_error[] = "UPDATE remove SendParts ไม่สำเร็จ $sendParts_strQuery";
		        $this->status++;
		    }
			// ################### END ทำการลดค่าใน SendParts (ลดจำนวนที่จะส่ง) ####################
			
		}
		if(!$sendParts_query){
	        $this->txt_error[] = "UPDATE remove SendPartsDetails ไม่สำเร็จ $sendParts_strQuery";
	        $this->status++;
		}
		*/
		// ################### END ทำการลดค่าใน SendPartsDetails (ลดจำนวนที่จะส่ง) ####################
	}
	
	private function ___return_type_2_Broken__parts_type_1__check_reduce_old_PartsStockBroken(
		$idno,
		$parts_type,
		$parts_code,
		$stock_id,
		$quantity_return,
		$wh_id,
		$locate_id,
		$parts_code_detail
	)
	{
		$partsStockBrokenDetails_strQuery = "
			SELECT
				COUNT(codeid) AS count
			FROM
				\"PartsStockBrokenDetails\"
			WHERE 
				status = 2
				AND
				stock_broken_id = '".$stock_id."'
			GROUP BY 
				stock_broken_id;
		";
		$partsStockBrokenDetails_query = pg_query($partsStockBrokenDetails_strQuery);
		$partsStockBrokenDetails_result = pg_fetch_result($partsStockBrokenDetails_query, 0);
		
		// var_dump($partsStockBrokenDetails_strQuery);
		// pg_query("ROLLBACK");
		// exit;
		
		if($partsStockBrokenDetails_result == 0 || $partsStockBrokenDetails_result == null){
			$delete_partsStockBroken_strQuery = "
				UPDATE 
					\"PartsStockBroken\"
				SET 
					stock_status = 0
				WHERE
					stock_broken_id = '".$stock_id."'
				;
			";
			if(!$delete_partsStockBroken_query = pg_query($delete_partsStockBroken_strQuery)){
				$this->txt_error[] = "UPDATE remove old PartsStock ไม่สำเร็จ $delete_partsStockBroken_strQuery";
	        	$this->status++;
			}
		}
	}
	
	
	private function _insert_approve()
	{
		//Insert Approve
		$ApproveParts_forReturn_strQuery = "
			INSERT INTO 
				\"PartsApproved\"
			(
				code, 
				user_id, 
				user_note, 
				user_timestamp, 
				appr_id, 
				appr_note, 
				appr_timestamp
			)
			VALUES
			(
				'{$this->gen_parts_no}',
				'{$this->return_user_id}',
				'{$this->return_note}',
				'{$this->nowdateTime}',
				'000',
				'มีการอนุมัติโดยระบบอัตโนมัติ',
				'{$this->nowdateTime}'
			)
		";
		if(!$result2=@pg_query($ApproveParts_forReturn_strQuery)){
	        $this->txt_error[] = "INSERT PartsApproved ไม่สำเร็จ {$ApproveParts_forReturn_strQuery}";
	        $this->status++;
	    }
	}
	
	private function _check_is_status(){
		// Check Is Query or Not?
		if($this->status == 0){
	        // pg_query("ROLLBACK");
	        pg_query("COMMIT");
	        $data['success'] = true;
	        $data['parts_pocode'] = $this->gen_parts_no;
			$data['message'] = "";
	    }else{
	        pg_query("ROLLBACK");
	        $data['success'] = false;
	        $data['message'] = "ไม่สามารถบันทึกได้! ".($this->txt_error[0]);
	    }
		$data['status'] = $this->status;
		
		return $data;
	}
}


?>