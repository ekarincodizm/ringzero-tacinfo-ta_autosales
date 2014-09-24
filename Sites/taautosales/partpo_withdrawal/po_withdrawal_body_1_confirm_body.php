<!-- header -->
<br />
<?php
$withdrawalParts_code = pg_escape_string($_GET["code"]);

$appr_strQuery = "
	SELECT 
		user_note
	FROM 
		\"PartsApproved\"
	WHERE
		code = '".$withdrawalParts_code."' ;
";
$appr_query = @pg_query($appr_strQuery);
$appr_user_note = @pg_fetch_result($appr_query, 0);

$withdrawalParts_strQuery = "
	SELECT 
		code, 
		type, 
		user_id, 
		withdraw_user_id, 
		date, 
		usedate, 
		status
	FROM 
		\"WithdrawalParts\"
	WHERE
		status = 1 
		AND
		code = '{$withdrawalParts_code}';
";
$withdrawalParts_query = @pg_query($withdrawalParts_strQuery);
$withdrawalParts_numrow = @pg_num_rows($withdrawalParts_query);
while ($withdrawalParts_result = pg_fetch_array($withdrawalParts_query)) {
?>
	<div style="width: 50%; float:left; ">
		
		<div>
			<!-- PO type -->
			<div style="width: 40%; float: left; text-align: right; margin-right: 2%; margin-top: 0.4%;">
				<strong>จุดประสงค์ :</strong>
			</div>
			<div style="width: 58%; float: left;">
<?php
				if($withdrawalParts_result["type"] == 1){
					?>เบิกขายซ่อม<?php
				}
				if($withdrawalParts_result["type"] == 2){
					?>เบิกประกอบชิ้นงาน<?php
				}
				if($withdrawalParts_result["type"] == 3){
					?>เบิกของเสีย<?php
				}
				
?>
			</div>
			<div style="clear: both;"></div>
		</div>
		<div>
			<div style="width: 40%; float: left; text-align: right; margin-right: 2%; margin-top: 0.4%;">
				<b>เจ้าหน้าที่ผู้ทำรายการ :</b>
			</div>
			<div style="width: 58%; float: left;">
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
			<div style="width: 40%; float: left; text-align: right; margin-right: 2%; margin-top: 0.4%;">
				<b>เจ้าหน้าที่ผู้ขอเบิก :</b>
			</div>
			<div style="width: 58%; float: left;">
<?php
					$fuser_strQuery = "
						SELECT 
							fullname
						FROM fuser
						WHERE
							id_user = '{$withdrawalParts_result["withdraw_user_id"]}' ;
					";
					$fuser_query = @pg_query($fuser_strQuery);
					while ($fuser_result = @pg_fetch_array($fuser_query)) {
						echo $fuser_result["fullname"];
					}
?>
			</div>
			<div style="clear: both;"></div>
		</div>
	</div>
	<div style="width: 50%; float:left; ">
		
		<div>
			<div style="width: 40%; float: left; text-align: right; margin-right: 2%; margin-top: 0.4%;">
				<b>วันที่เบิก :</b>
			</div>
			<div style="width: 58%; float: left;">
				<?php echo date("d-m-Y", strtotime($withdrawalParts_result["date"])); ?>
			</div>
			<div style="clear: both;"></div>
		</div>
		
		<div>
			<div style="width: 40%; float: left; text-align: right; margin-right: 2%; margin-top: 0.4%;">
				<b>วันที่ต้องการใช้งาน :</b>
			</div>
			<div style="width: 58%; float: left;">
				<?php echo date("d-m-Y", strtotime($withdrawalParts_result["usedate"])); ?>
			</div>
			<div style="clear: both;"></div>
		</div>
		
	</div>
	<div style="clear: both;"></div>
	
	
	<div style="font-size:12px">
		<!-- ##################### Middle ####################### -->
		
		<div style="float:right; margin-top:10px; width:100%">
			<table cellpadding="5" cellspacing="0" border="0" width="100%" bgcolor="#F0F0F0">
				<tr style="font-weight:bold; text-align:left; " bgcolor="#D0D0D0">
					<td width="5%">ลำดับที่</td>
					<td width="15%">รหัสสินค้า</td>
					<td width="20%">ชื่อสินค้า</td>
					<td width="30%">รายละเอียดสินค้า</td>
					<td width="15%">จำนวนที่เบิกได้สูงสุด(จำนวนสินค้าในคลัง)</td>
					<td width="15%">จำนวนที่เบิก</td>
				</tr>
<?php
				function call_parts($parts_code, $return){
					$withdrawalParts_code = pg_escape_string($_GET["code"]);
					// $parts_strQuery = "
						// SELECT 
							// parts_code,
							// codeid,
							// name,
							// details,
							// stock_remain
						// FROM
							// \"v_parts_stock_detail__type_union\"
						// WHERE
							// parts_code = '{$parts_code}';
					// ";
					// $parts_query=@pg_query($parts_strQuery);
					// while($parts_return = @pg_fetch_array($parts_query)){
						// echo $parts_return[$return];
					// }
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
				
				// echo $strQuery_parts;
				
				
				$withdrawalPartsDetails_strQuery = "
					SELECT
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
							parts_code = '".$withdrawalPartsDetails_result["parts_code"]."'
						group by parts_code ;
					";
					$view_withdrawal_quantity_query = pg_query($view_withdrawal_quantity_strQuery);
					while ($view_withdrawal_quantity_result = pg_fetch_array($view_withdrawal_quantity_query)) {
						$max_send_quantity = $view_withdrawal_quantity_result["send_quantity"];
					}
					
					
					// อันนี้คือ Stock_remain ที่รวมกับของ withdrawal_quantity เก่าที่ได้รวมไป
					$stock_remain_with_withdrawal = call_parts($withdrawalPartsDetails_result["parts_code"], "withdrawal_quantity") + $max_send_quantity;
?>
						<tr bgcolor="#FFFFFF">
							<td>
								<?php echo $withdrawalPartsDetails_result["idno"]; ?>.
							</td>
							<td>
								<?php echo $withdrawalPartsDetails_result["parts_code"]; ?>
								<input type="hidden" name="parts_code<?php echo $withdrawalPartsDetails_result["idno"]; ?>" class="parts_code" data-code_id="<?php echo $withdrawalPartsDetails_result["idno"]; ?>" value="<?php echo $withdrawalPartsDetails_result["parts_code"]; ?>" />
							</td>
							
							<td>
								<?php echo call_parts($withdrawalPartsDetails_result["parts_code"], "name"); ?>
							</td>
							<td>
								<?php echo call_parts($withdrawalPartsDetails_result["parts_code"], "details"); ?>
							</td>
							<td align="center">
								<?php echo $stock_remain_with_withdrawal." (".(call_parts($withdrawalPartsDetails_result["parts_code"], "stock_remain")).")"; ?>
								<input type="hidden" name="quantity<?php echo $withdrawalPartsDetails_result["idno"]; ?>" class="quantity" data-quantity_id="<?php echo $withdrawalPartsDetails_result["idno"]; ?>" value="<?php echo call_parts($withdrawalPartsDetails_result["parts_code"], "withdrawal_quantity"); ?>" />
							</td>
							<td align="center">
								<?php echo $withdrawalPartsDetails_result["withdrawal_quantity"]; ?>
								<input type="hidden" name="quantity_withdrawal<?php echo $withdrawalPartsDetails_result["idno"]; ?>" class="quantity_withdrawal" data-quantity_withdrawal="<?php echo $withdrawalPartsDetails_result["idno"]; ?>" value="<?php echo $withdrawalPartsDetails_result["withdrawal_quantity"]; ?>" />
							</td>
						</tr>
<?php
				}
?>
			</table>
			
			<div class="linedotted"></div>
			<div style="clear:both"></div>
			
			<!-- ############## footer ############## -->
			
			<div style="margin-top:10px" align="center">
				<b>หมายเหตุ ของผู้เบิก:</b><br />
			</div>
			<div style="margin-top:10px" align="center">
				<?php echo $appr_user_note; ?>
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
				<input type="button" name="" class="btn_confirm" value="ยึนยันการเบิก" data-withdrawal_code="<?php echo $withdrawalParts_code; ?>" />
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
	
	
	// ตรวจสอบว่า Quantity Withdrawal เกิน Stock หรือไม่ ถ้าเกิน ก็ไม่ให้เบิก ระบบจะลดจำนวน ให้เท่ากับจำนวนทั้งหมด 
	$(".quantity_withdrawal").live("change", function(){
		var quantity_withdrawal_value = $(this).val();
		var this_id = $(this).data("quantity_withdrawal");
		var quantity = $(".quantity[name=quantity" + this_id + "]").val();
		
		console.log("quantity_withdrawal_value = "+quantity_withdrawal_value);
		console.log("this_id = "+this_id)
		console.log("quantity = "+quantity);
		
		if(parseInt(quantity_withdrawal_value) > parseInt(quantity)){
			alert("คุณกรอกจำนวนสินค้าเกินสต๊อก");
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
	console.log(counter);

	function numberWithCommas(x) {
	    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
	}
	
	
	$(".btn_confirm").click(function(){
		var _withdrawal_code = $(this).data("withdrawal_code");
		var chk = 0, chk_quantity = 0;
		var i = 0;
		
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
			if(confirm('คุณต้องการที่จะยืนยันการอนุมัติการเบิกหรือไม่') == false){
				return false;
			}
			else{
				$.post(
					'po_withdrawal_body_save.php',
					{
						withdrawal_code : _withdrawal_code,
						set_status: 2,
					},
					function(data){
						if(data.success){
							console.log("data.success = " + data.success);
							console.log("data.message = " + data.message);
							ShowSuccess();
							//location.reload();
						}else{
							alert(data.message);
							console.log("data.success = " + data.success);
							console.log("data.message = " + data.message);
						}
					},
					'json'
				);
			}
		}
	});
	
	// ##########################
	
	function ShowPrint(id){
		$('body').append('<div id="divdialogprint"></div>');
		$('#divdialogprint').html("<br/><div style=\"text-align:center\">บันทึกเรียบร้อยแล้ว<br /><br /><input type=\"button\" name=\"btnPrint\" id=\"btnPrint\" value=\"กลับสู่หน้าหลัก\" onclick=\"javascript:location.reload();\"></div>");
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