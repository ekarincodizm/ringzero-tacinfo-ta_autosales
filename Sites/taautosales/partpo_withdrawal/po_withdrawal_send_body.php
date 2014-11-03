<br />
<?php
// ###################### Function ###########################
include_once("po_withdrawal_webservice.php");
// #################### End Function #########################

$withdrawalParts_code = pg_escape_string($_GET["code"]);

$appr_strQuery = "
	SELECT 
		user_note, appr_note
	FROM 
		\"PartsApproved\"
	WHERE
		code = '".$withdrawalParts_code."' ;
";
$appr_query = @pg_query($appr_strQuery);
$appr_user_note = @pg_fetch_result($appr_query, 0);
$appr_appr_note = @pg_fetch_result($appr_query, 1);


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
		status = 3 
		AND
		code = '{$withdrawalParts_code}';
";
$withdrawalParts_query = @pg_query($withdrawalParts_strQuery);
$withdrawalParts_numrow = @pg_num_rows($withdrawalParts_query);
while ($withdrawalParts_result = pg_fetch_array($withdrawalParts_query)) {
	$withdrawalParts_type = $withdrawalParts_result["type"];
?>
	<div style="width: 50%; float:left; ">
		
		<div>
			<!-- PO type -->
			<div style="width: 50%; float: left; text-align: right; margin-right: 2%; margin-top: 0.4%;">
				<strong>จุดประสงค์ :</strong>
			</div>
			<div style="width: 48%; float: left;">
				<select name="withdrawal_type" id="withdrawal_type" disabled="disabled">
					<option value="" >เลือกจุดประสงค์</option>
					<option value="1" <?php 
						if($withdrawalParts_result["type"] == 1){
							?>selected="selected"<?php
						}
					?>>เบิกขายซ่อม</option>
					<option value="2" <?php 
						if($withdrawalParts_result["type"] == 2){
							?>selected="selected"<?php
						}
					?>>เบิกประกอบชิ้นงาน</option>
					<option value="3"  <?php 
						if($withdrawalParts_result["type"] == 3){
							?>selected="selected"<?php
						}
					?>>เบิกของเสีย</option>
				</select>
<?php
				if($withdrawalParts_result["type"] == 1){
					
				}
?>
			</div>
			<div style="clear: both;"></div>
		</div>
		<div>
			<div style="width: 50%; float: left; text-align: right; margin-right: 2%; margin-top: 0.4%;">
				<b>เจ้าหน้าที่ผู้ทำรายการ :</b>
			</div>
			<div style="width: 48%; float: left;">
				<input type="hidden" name="withdrawal_user_id" id="withdrawal_user_id" value="<?php echo $_SESSION["ss_iduser"]; ?>" disabled="disabled" />
<?php
				$fuser_strQuery = "
					SELECT 
						fullname
					FROM 
						fuser
					WHERE
						id_user = '{$withdrawalParts_result["user_id"]}' ;
				";
				$fuser_query = @pg_query($fuser_strQuery);
				while ($fuser_result = @pg_fetch_array($fuser_query)) {
					echo $fuser_result["fullname"];
				}
?>
			</div>
			<div style="clear: both;"></div>
		</div>
		<div>
			<div style="width: 50%; float: left; text-align: right; margin-right: 2%; margin-top: 0.4%;">
				<b>เจ้าหน้าที่ผู้ขอเบิก :</b>
			</div>
			<div style="width: 48%; float: left;">
				<select name="withdrawal_withdraw_user_id" id="withdrawal_withdraw_user_id" disabled="disabled">
<?php
					$fuser_strQuery = "
						SELECT 
							fullname, id_user
						FROM fuser
						ORDER BY fullname;
					";
					$fuser_query = @pg_query($fuser_strQuery);
					while ($fuser_result = @pg_fetch_array($fuser_query)) {
						?>
						<option value="<?php echo $fuser_result["id_user"]; ?>" <?php
							if($fuser_result["id_user"] == $withdrawalParts_result["withdraw_user_id"]){
								echo "selected=\"selected\"";
							}
						?>><?php echo $fuser_result["fullname"]; ?></option>
						<?php
					}
?>
				</select>
			</div>
			<div style="clear: both;"></div>
		</div>
	</div>
	<div style="width: 50%; float:left; ">
		
		<div>
			<div style="width: 50%; float: left; text-align: right; margin-right: 2%; margin-top: 0.4%;">
				<b>วันที่เบิก :</b>
			</div>
			<div style="width: 48%; float: left;">
				<input type="text" name="withdrawal_date" id="withdrawal_date" class="datepicker" disabled="disabled" value="<?php echo date("d-m-Y", strtotime($withdrawalParts_result["date"])); ?>" />
			</div>
			<div style="clear: both;"></div>
		</div>
		
		<div>
			<div style="width: 50%; float: left; text-align: right; margin-right: 2%; margin-top: 0.4%;">
				<b>วันที่ต้องการใช้งาน :</b>
			</div>
			<div style="width: 48%; float: left;">
				<input type="text" name="withdrawal_usedate" id="withdrawal_usedate" class="datepicker" disabled="disabled" value="<?php echo date("d-m-Y", strtotime($withdrawalParts_result["usedate"])); ?>" />
			</div>
			<div style="clear: both;"></div>
		</div>
		
	</div>
	<div class="withdrawal_type_2" style="width: 50%; float:left; ">
<?php
		if($withdrawalParts_result["type"] == 2){
?>
			<div style="width: 50%; float: left; text-align: right; margin-right: 2%; ">
				<b> ใช้ทำโปรเจค :</b>
			</div>
			<div style="width: 48%; float: left;"><?php
				$project_id = $withdrawalParts_result["project_id"];
				echo get_projectName($project_id); 
			?></div>
			<div style="clear: both; "></div>
			<div style="width: 50%; float: left; text-align: right; margin-right: 2%; ">
				<b> ต้องการผลิตเป็นสินค้าจำนวน :</b>
			</div>
			<div style="width: 48%; float: left;"><?php 
				$project_quantity = $withdrawalParts_result["project_quantity"];
				echo $project_quantity; 
			?></div>
<?php
		}
?>
	</div>
	
	<div style="clear: both;"></div>
	
	
	<div style="font-size:12px">
		<!-- ##################### Middle ####################### -->
		
		<div style="float:right; margin-top:10px; width:100%">
			<table cellpadding="5" cellspacing="0" border="0" width="100%" bgcolor="#F0F0F0">
				<tr style="font-weight:bold; text-align:left; " bgcolor="#D0D0D0">
					<td width="5%">ลำดับที่</td>
					<td width="15%">รหัสสินค้า</td>
					<td width="15%">ชื่อสินค้า</td>
					<td width="20%">รายละเอียดสินค้า</td>
					<td width="15%">จำนวนที่เบิกได้สูงสุด(จำนวนสินค้าในคลัง)</td>
					<td width="10%">จำนวนที่เบิก</td>
					<td width="20%">จำนวนที่จ่าย&sol;จำนวนที่เบิกได้สูงสุด</td>
				</tr>
<?php
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
?>
				<script>
					var parts = new Array();
<?php
					$strQuery_parts = "
						(
							SELECT 
								code,
								name,
								details,
								type
							FROM
								\"parts\"
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
						// ## End Check Quantity ที่ ได้กดเบิกไป แล้วค้างอยู่ใน Queue ##
?>
							parts.push([
								"<?php echo $res_parts['code']; ?>", 
								"<?php echo $res_parts['name']; ?>", 
								"<?php echo $res_parts['details']; ?>",
								"<?php echo $stock_remain; ?>",
								"<?php echo $sum_withdrawal_quantity; ?>",
							]);
<?php
					}
					if($numrows_parts == 0){
				        $parts_matches[] = "ไม่พบข้อมูล";
				    }
					// $parts_matches = array_slice($parts_matches, 0, 100);
?>
					// console.log("parts = "+parts);
				</script>
<?php
				$withdrawalPartsDetails_strQuery = "
					SELECT
						withdrawal_id, 
						withdrawal_code, 
						idno, 
						parts_code, 
						withdrawal_quantity
  					FROM 
  						\"WithdrawalPartsDetails\"
  					WHERE 
  						\"withdrawal_code\" = '".$withdrawalParts_code."' 
  						AND
  						status = 1 ;
				";
				$withdrawalPartsDetails_query = @pg_query($withdrawalPartsDetails_strQuery);
				$withdrawalPartsDetails_numrow = @pg_num_rows($withdrawalPartsDetails_query);
				while ($withdrawalPartsDetails_result = @pg_fetch_array($withdrawalPartsDetails_query)) {
					
					
					// Calculate How many Quantity left after already withdrawal the Parts
					$max_send_quantity = 0;
					$view_withdrawal_quantity_strQuery = "
						SELECT
							parts_code,
							SUM(send_quantity) as send_quantity
						FROM 
							v_parts_withdrawal_quantity
						where 
							\"withdrawal_code\" = '".$withdrawalParts_code."'
							AND
							parts_code = '".$withdrawalPartsDetails_result["parts_code"]."'
						group by parts_code ;
					";
					$view_withdrawal_quantity_query = pg_query($view_withdrawal_quantity_strQuery);
					while ($view_withdrawal_quantity_result = pg_fetch_array($view_withdrawal_quantity_query)) {
						$max_send_quantity = $view_withdrawal_quantity_result["send_quantity"];
					}
					$max_send_quantity = $withdrawalPartsDetails_result["withdrawal_quantity"] - $max_send_quantity;
					
					
					if($withdrawalPartsDetails_result["idno"] == 1){
						
						// อันนี้คือ Stock_remain ที่รวมกับของ withdrawal_quantity เก่าที่ได้รวมไป
						$stock_remain_with_withdrawal = call_parts($withdrawalPartsDetails_result["parts_code"], "withdrawal_quantity");
?>
						<tr bgcolor="#FFFFFF">
							<td>
								<?php echo $withdrawalPartsDetails_result["idno"]; ?>.
							</td>
							<td>
								<?php echo $withdrawalPartsDetails_result["parts_code"]; ?>
								<input type="hidden" name="parts_code1" id="parts_code1" class="parts_code1" data-code_id="<?php echo $withdrawalPartsDetails_result["parts_code"]; ?>" value="<?php echo $withdrawalPartsDetails_result["parts_code"]; ?>" />
							</td>
							
							<td>
								<span id="parts_name1" class="parts_name"><?php echo call_parts($withdrawalPartsDetails_result["parts_code"], "name"); ?></span>
								<input type="hidden" name="parts_name1" class="parts_name" value="<?php echo call_parts($withdrawalPartsDetails_result["parts_code"], "name"); ?>" />
							</td>
							<td>
								<span id="parts_detail1" class="parts_detail"><?php echo call_parts($withdrawalPartsDetails_result["parts_code"], "details"); ?></span>
							</td>
							<td align="center">
								<span id="quantity1" class="quantity" data-quantity_id="1"><?php echo $stock_remain_with_withdrawal." (".(call_parts($withdrawalPartsDetails_result["parts_code"], "stock_remain")).")"; ?></span>
								<input type="hidden" name="quantity1" class="quantity" data-quantity_id="1" value="<?php echo $stock_remain_with_withdrawal; ?>" />
							</td>
							<td>
								<?php echo $withdrawalPartsDetails_result["withdrawal_quantity"]; ?>
							</td>
							<td>
								<input type="<?php 
									if($withdrawalParts_result["type"] == 2){
										?>hidden<?php
									}
									else{
										?>text<?php
									}
								?>" name="quantity_withdrawal1" id="quantity_withdrawal1" class="quantity_withdrawal" data-quantity_withdrawal="1" data-Max_send_qty="<?php echo $max_send_quantity; ?>" value="<?php echo $max_send_quantity; ?>" style="width:40px; text-align:right" />
<?php
								if($withdrawalParts_result["type"] == 2){
									echo $max_send_quantity;
								}
?>
								&sol; <?php echo $max_send_quantity; ?>
							</td>
						</tr>
					</table>
<?php
					}
					else{
?>
						<div id="TextBoxesGroup">
							<div id="TextBoxDiv<?php echo $withdrawalPartsDetails_result["idno"]; ?>">
								<table width="100%" cellpadding="5" cellspacing="0" border="0">
							    	<tr>
							    		<td width="5%">
							    			<?php echo $withdrawalPartsDetails_result["idno"]; ?>.
								    	</td>
								    	<td width="15%">
								    		<?php echo $withdrawalPartsDetails_result["parts_code"]; ?>
											<input type="hidden" id="parts_code<?php echo $withdrawalPartsDetails_result["idno"]; ?>" name="parts_code<?php echo $withdrawalPartsDetails_result["idno"]; ?>" class="parts_code" data-code_id="<?php echo $withdrawalPartsDetails_result["idno"]; ?>" value="<?php echo $withdrawalPartsDetails_result["parts_code"]; ?>" />
										</td>
										<td width="15%">
											<span id="parts_name<?php echo $withdrawalPartsDetails_result["idno"]; ?>" class="parts_name"><?php echo call_parts($withdrawalPartsDetails_result["parts_code"], "name"); ?></span>
											<input type="hidden" name="parts_name<?php echo $withdrawalPartsDetails_result["idno"]; ?>" class="parts_name" value="<?php echo call_parts($withdrawalPartsDetails_result["parts_code"], "name"); ?>" />
										</td>
										<td  width="20%">
											<span id="parts_detail<?php echo $withdrawalPartsDetails_result["idno"]; ?>" class="parts_detail"><?php echo call_parts($withdrawalPartsDetails_result["parts_code"], "details"); ?></span>
										</td>
										<td width="15%" align="center">
								    	 	<span id="quantity<?php echo $withdrawalPartsDetails_result["idno"]; ?>" class="quantity" data-quantity_id="<?php echo $withdrawalPartsDetails_result["idno"]; ?>"><?php echo $stock_remain_with_withdrawal." (".(call_parts($withdrawalPartsDetails_result["parts_code"], "stock_remain")).")"; ?></span>
								    		<input type="hidden" name="quantity<?php echo $withdrawalPartsDetails_result["idno"]; ?>" class="quantity" data-quantity_id="<?php echo $withdrawalPartsDetails_result["idno"]; ?>" value="<?php echo $stock_remain_with_withdrawal; ?>" />
								    	</td>
										<td width="10%">
											<?php echo $withdrawalPartsDetails_result["withdrawal_quantity"]; ?>
										</td>
								    	<td width="20%">
											<input type="<?php 
												if($withdrawalParts_result["type"] == 2){
													?>hidden<?php
												}
												else{
													?>text<?php
												}
											?>" name="quantity_withdrawal<?php echo $withdrawalPartsDetails_result["idno"]; ?>" id="quantity_withdrawal<?php echo $withdrawalPartsDetails_result["idno"]; ?>" class="quantity_withdrawal" data-Max_send_qty="<?php echo $max_send_quantity; ?>" data-quantity_withdrawal="<?php echo $withdrawalPartsDetails_result["idno"]; ?>" value="<?php echo $max_send_quantity; ?>" style="width:40px; text-align:right" />
<?php
											if($withdrawalParts_result["type"] == 2){
												echo $max_send_quantity;
											}
?>
											&sol; <?php echo $max_send_quantity; ?>
										</td>
									</tr>
								</table>
							</div>
						</div>
<?php
					}
				}
				if($withdrawalPartsDetails_numrow == 1){
?>
					<div id="TextBoxesGroup"></div>
<?php
				}
?>
			<div class="linedotted"></div>
			<div style="clear:both"></div>
			
			<!-- ############## footer ############## -->
			
			<div style="margin-top:10px" align="center">
				<b>หมายเหตุ ของผู้เบิก:</b><br />
				<?php echo $appr_user_note; ?>
			</div>
			<br />
			
			<div style="margin-top:10px" align="center">
				<b>หมายเหตุ ของผู้อนุมัติ:</b><br />
				<?php echo $appr_appr_note; ?>
			</div>
			<br />
			
			<div style="margin-top:10px" align="center">
				<b>หมายเหตุ ใบจ่าย:</b><br />
				<textarea name="withdrawal_note" id="withdrawal_note" cols="130" rows="3"></textarea>
			</div>
			
			<!-- <div style="width: 100%; ">
				<div style="float: left; width: 10%; ">
					หมายเหตุ
				</div>
				<div style="float: left; width: 90%; ">
					<textarea></textarea>
				</div>
			</div> -->
			
			<div class="linedotted"></div>
			<div style="clear:both"></div>
			
			<div style="margin-top:10px" align="center">
				<input type="button" name="" id="btnSubmit" value="เบิกจ่าย" />
			</div>
		</div>
	</div>
<?php
}
?>
<script>
	//######################## Initial Calculate Variables ############################
	
	$(function() {
		$(".datepicker").datepicker({
			dateFormat: 'dd-mm-yy' ,
		});
	}); 
	
	$(".parts_code").live("blur", function(){
		// console.log("parts_code = " + $(this).val());
		var this_id = $(this).data("code_id");
		// console.log("this_id = " + this_id);
		
		var i = 0;
		var parts_name_value = "";
		var parts_detail_value = "";
		var stock_remain_value = 0;
		
		for(i=0; i<parts.length; i++){
			// ### คำสั่ง $.inArray(ค่าที่จะหา, array นั้น) --> มันจะได้ Index ของ ค่าที่จะหา ถ้าไม่มี จะได้ค่า -1 (index เริ่มนับจาก 0 เป็นตัวแรก) ###
			if($.inArray($(this).val() , parts[i]) == 0){
				parts_name_value = parts[i][1];
				parts_detail_value = parts[i][2];
				stock_remain_value = parts[i][3];
				break;
			}
		}
		console.log("parts_name = " + parts_name_value);
		$(".parts_name#parts_name"+this_id).html(parts_name_value);
		$(".parts_name[name=parts_name"+this_id+"]").val(parts_name_value);
		$(".parts_detail#parts_detail"+this_id).html(parts_detail_value);
		$(".quantity#quantity"+this_id).html(stock_remain_value);
		$(".quantity[name=quantity"+this_id+"]").val(stock_remain_value);
		$(".quantity_withdrawal#quantity_withdrawal"+this_id).val("");
		
		
		if(parts_name_value == ""){
			$(this).val("");
			// alert("ไม่มีรหัสนี้อยู่ในระบบ")
			$(".quantity#quantity"+this_id).prop("disabled", "disabled");
			$(".quantity_withdrawal#quantity_withdrawal"+this_id).prop("disabled", "disabled");
		}
		else{
			$(".quantity#quantity"+this_id).prop("disabled", false);
			$(".quantity_withdrawal#quantity_withdrawal"+this_id).prop("disabled", false);
		}
	});
	
	var parts_code_autocomplete = <?php echo json_encode($parts_matches); ?>;
	
	$(".parts_code").live("focus", function() {
		$(this).autocomplete({
	        source: parts_code_autocomplete,
	        minLength:1,
	        select: function(event, ui) {
				if(ui.item.value == 'ไม่พบข้อมูลเก่า'){
					
				}else{
				   
				}
	        }
	    });
	});
	
	$(".quantity_withdrawal").live("change", function(){
		var quantity_withdrawal_value = $(this).val();
		var this_id = $(this).data("quantity_withdrawal");
		//var quantity = $(".quantity[name=quantity" + this_id + "]").val();
		var quantity = $(this).data("Max_send_qty");
		
		var this_parts_code = $(".parts_code#parts_code" + this_id).val();
		
		console.log("quantity_withdrawal_value = "+quantity_withdrawal_value);
		console.log("this_id = "+this_id)
		console.log("quantity = "+quantity);
		
		if(parseInt(quantity_withdrawal_value) > parseInt(quantity)){
			alert("คุณกรอกจำนวนสินค้าที่ต้องการเบิก เกินกว่าจำนวนที่เบิกทั้งหมด ลบด้วย จำนวนที่ได้เบิกไปแล้ว ของรหัสสินค้า : " + this_parts_code);
			$(".quantity_withdrawal#quantity_withdrawal"+this_id).val(quantity);
		}
	});
	
	
	//counter = Count how many rows
	var counter = <?php
		if($withdrawalPartsDetails_numrow != 0 || $withdrawalPartsDetails_numrow != ""){
			echo $withdrawalPartsDetails_numrow;
		}
		else{
			echo 1;
		}
	?>;

	
	function numberWithCommas(x) {
	    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
	}
	
	//########## Submit ###########
	$('#btnSubmit').click(function(){
		var chk = 0, chk_quantity = 0, i = 0;
		
		//### Header ###
		var _withdrawal_type = $('#withdrawal_type').val();
		var _withdrawal_user_id = $('#withdrawal_user_id').val();
		var _withdrawal_withdraw_user_id = $('#withdrawal_withdraw_user_id').val();
		var _withdrawal_date = $('#withdrawal_date').val();
		var _withdrawal_usedate = $('#withdrawal_usedate').val();
		
		if(_withdrawal_type == ""){
			alert('กรุณาเลือก จุดประสงค์');
			chk++;
			return false;
		}
		if(_withdrawal_withdraw_user_id == ""){
			alert('กรุณากรอก เจ้าหน้าที่ผู้ขอเบิก');
			chk++;
			return false;
		}
		if(_withdrawal_date == ""){
			alert('กรุณากรอก วันที่เบิก');
			chk++;
			$('#withdrawal_date').focus();
			return false;
		}
		if(_withdrawal_usedate == ""){
			alert('กรุณากรอก วันที่ต้องการใช้งาน');
			chk++;
			$('#withdrawal_usedate').focus();
			return false;
		}
		
		
		//### Middle ###
		var arradd = new Array();
		for( i=1; i<=counter; i++ ){
			
			var _parts_code = $('#parts_code' + i).val();
			var _quantity_withdrawal = $('#quantity_withdrawal' + i).val();
			var _quantity = $(".quantity[name=quantity" + i +"]").val();
			
			if(_parts_code == ""){
				alert('กรุณาเลือก เลือกรหัสสินค้า (รายการที่ '+i+')');
				chk++;
				$('#parts_code' + i).focus();
				return false;
			}
			if(_quantity_withdrawal == ""){
				alert('กรุณากรอก จำนวนที่เบิก (รายการที่ '+i+')');
				chk++;
				$('#quantity_withdrawal' + i).focus();
				return false;
			}
			/*
			else if(_quantity_withdrawal > _quantity){
				alert('กรุณากรอก จำนวนที่เบิก ให้น้อยกว่าจำนวนที่มีอยู่ใน คลัง (รายการที่ '+i+')');
				chk++;
				$(".quantity#quantity" + i).focus();
				return false;
			}
			*/
			arradd[i-1] = {
				idno: i, 
				parts_code: _parts_code, 
				quantity : _quantity,
				send_quantity: _quantity_withdrawal, 
			};
		}
		
		//### Footer ###
		var _withdrawal_note = $('#withdrawal_note').val();
		
		if(_withdrawal_note == ""){
			alert('กรุณากรอก หมายเหตุบันทึกการอนุมัติ');
			chk++;
			$('#withdrawal_note').focus();
			return false;
		}
		
		// ## validate Quantity ##
		for(i=0; i<counter; i++){
			// var _parts_code = $(".parts_code[name=parts_code"+i+"]").val();
			var _quantity = $(".quantity[name=quantity"+i+"]").val();
			var _quantity_withdrawal = $(".quantity_withdrawal[name=quantity_withdrawal"+i+"]").val();
			if(parseInt(_quantity_withdrawal) > parseInt(_quantity)){
				chk++;
				chk_quantity++;
			}
		}
		if(chk_quantity > 0){
			alert("คุณกรอกจำนวนสินค้าเกินสต๊อก");
			return false;
		}
		console.log("chk_quantity = "+chk_quantity);
		// ## End validate Quantity ##
		
		
		if(chk == 0){
			if(!confirm('ต้องการทำการบันทึกหรือไม่?')){
				return false;
			}
		
			$.post(
				'po_withdrawal_send_save.php',
				{
					withdrawal_code: "<?php echo $withdrawalParts_code; ?>", 
					withdrawal_type: "<?php echo $withdrawalParts_type; ?>",
					
					send_type: _withdrawal_type,
					send_user_id: _withdrawal_user_id,
					send_withdraw_user_id : _withdrawal_withdraw_user_id,
					send_date : _withdrawal_date,
					send_usedate : _withdrawal_usedate,
					
					project_id : "<?php echo $project_id; ?>",
					project_quantity : "<?php echo $project_quantity; ?>",
					
					send_details_array: JSON.stringify(arradd),
					
					send_note : _withdrawal_note,
				},
				function(data){
					if(data.success){
						console.log("data.success = " + data.success);
						console.log("data.message = " + data.message);
						console.log("data.sendParts_code = " + data.sendParts_code);
						ShowPrint(data.sendParts_code);
						//location.reload();
					}else{
						console.log("data.success = " + data.success);
						console.log("data.message = " + data.message);
						console.log("data.test = " + data.test);
						alert(data.message);
					}
				},'json'
			);
		}
		
	}); //End BtnClick
	
	// ##########################
	
	function ShowPrint(id){
		$('body').append('<div id="divdialogprint"></div>');
		$str = 
		"<br/>"+
		"<div style=\"text-align:center\">"+
		"	บันทึกเรียบร้อยแล้ว<br /><br />"+
		"	<input type=\"button\" name=\"btnPrint\" id=\"btnPrint\" value=\"กลับสู่หน้าหลัก\" onclick=\"javascript:window.open('./po_withdrawal_mat_pdf.php?sendParts_code="+id+"') />"
		"</div>";
		
		$('#divdialogprint').html("<br/><div style=\"text-align:center\">บันทึกเรียบร้อยแล้ว<br /><br /><input type=\"button\" name=\"btnPrint\" id=\"btnPrint\" value=\"กลับสู่หน้าหลัก\" onclick=\"javascript:window.open('./po_withdrawal_mat_pdf.php?sendParts_code="+ id +"','po_id4343423','toolbar=no,menubar=no,resizable=yes,scrollbars=yes,status=no,location=no,width=800,height=600'); javascript:location.reload();\"></div>");
		$('#divdialogprint').dialog({
			title: 'บันทึกเรียบร้อยแล้ว : '+id,
			resizable: false,
			modal: true,
			width: 300,
			height: 150,
			close: function(ev, ui){
				for( i=1; i<=counter; i++){
					$('#combo_product'+ i).val("");
					$('#txt_unit'+ i).val("");
					$('#txt_cost'+ i).val("");
					$('#span_price'+ i).text("0");
					$('#txt_vat'+ i).val("");
					$('#span_sum'+ i).text("0");
				}
				$('#span_sum_all_price').text("0");
				$('#span_sum_all_vat').text("0");
				$('#span_sum_all_all').text("0");
				$('#divdialogprint').remove();
			}
		});
	}
	
</script>