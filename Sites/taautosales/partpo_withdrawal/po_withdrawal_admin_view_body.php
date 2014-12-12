<br />
<?php
// ###################### Function ###########################
include_once("po_withdrawal_webservice.php");
// #################### End Function #########################

$class = new Withdrawal_edit_body($withdrawalParts_code);

$partStock = new PartStock();


$status = pg_escape_string($_GET["status"]);
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
		status,
		project_id,
		project_quantity
	FROM 
		\"WithdrawalParts\"
	WHERE
		status = ".$status."
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
	<div class="withdrawal_type_2" style="width: 50%; float:left; ">
<?php
		if($withdrawalParts_result["type"] == 2){
?>
			<div style="width: 50%; float: left; text-align: right; margin-right: 2%; ">
				<b> ใช้ทำโปรเจค :</b>
			</div>
			<div style="width: 48%; float: left;">
				<?php echo get_projectName($withdrawalParts_result["project_id"]); ?>
			</div>
			<div style="clear: both; "></div>
			<div style="width: 50%; float: left; text-align: right; margin-right: 2%; ">
				<b> ต้องการผลิตเป็นสินค้าจำนวน :</b>
			</div>
			<div style="width: 48%; float: left;">
				<?php echo $withdrawalParts_result["project_quantity"]; ?>
			</div>
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
					<td width="20%">ชื่อสินค้า</td>
					<td width="30%">รายละเอียดสินค้า</td>
					<td width="15%">จำนวนที่เบิกได้สูงสุด(จำนวนที่เบิกได้ทั้งหมด)</td>
					<td width="15%">จำนวนที่เบิก</td>
				</tr>
<?php
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
					
					$getStockDetails = $partStock->get_stock_detail_and_aval($withdrawalPartsDetails_result["parts_code"]);
					
					$stock_remain_with_withdrawal = intval($class->call_parts($withdrawalPartsDetails_result["parts_code"], "withdrawal_quantity")) +  intval($max_send_quantity);
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
								<?php echo $class->call_parts($withdrawalPartsDetails_result["parts_code"], "name"); ?>
							</td>
							<td>
								<?php echo $class->call_parts($withdrawalPartsDetails_result["parts_code"], "details"); ?>
							</td>
							<td align="center">
								<?php // echo $stock_remain_with_withdrawal." (".(call_parts($withdrawalPartsDetails_result["parts_code"], "stock_remain")).")"; ?>
								<?php echo $getStockDetails["stock_aval"]." (".$getStockDetails["stock_remain"].")"; ?>
								<input type="hidden" name="quantity<?php echo $withdrawalPartsDetails_result["idno"]; ?>" class="quantity" data-quantity_id="<?php echo $withdrawalPartsDetails_result["idno"]; ?>" value="<?php echo $class->call_parts($withdrawalPartsDetails_result["parts_code"], "withdrawal_quantity"); ?>" />
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
				<b>หมายเหตุ ของผู้ขอเบิก:</b><br />
			</div>
			<div style="margin-top:10px" align="center">
				<?php echo $appr_user_note; ?>
				<br /><br />
			</div>
			<div style="width: 100%; ">
				<div style="float: left; width: 15%; ">
					หมายเหตุ ผู้อนุมัติ :
				</div>
				<div style="float: left; width: 85%; ">
					<textarea rows="5" cols="100" name="appr_note" id="appr_note"></textarea>
				</div>
			</div>
			<div align="center">
				<br /><br /><br /><br /><br /><br /><br />
				<input type="button" class="btn_confirm" value="อนุมัติ" data-withdrawal_code="<?php echo $withdrawalParts_code; ?>" />
				<input type="button" class="btn_cancel" value="ไม่อนุมัติ" data-withdrawal_code="<?php echo $withdrawalParts_code; ?>" />
			</div>
			
			<div class="linedotted"></div>
			<div style="clear:both"></div>
			
			<!-- <div style="margin-top:10px" align="center">
				<input type="button" name="" id="btnClose" class="" value="ปิด" />
			</div> -->
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

	function numberWithCommas(x) {
	    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
	}
	
	// $("#btnClose").click(function(){
		// $('#divdialogadd').remove();
	// });
	
	
	//########## Submit ###########
	
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
	
	
	
	$(".btn_confirm").click(function(){
		var _withdrawal_code = $(this).data("withdrawal_code");
		var _appr_note = $("#appr_note").val();
		
		var chk = 0, chk_quantity = 0, i = 0;
		
		if(_appr_note == ""){
			alert("กรุณา กรอก หมายเหตุ ผู้อนุมัติ");
			chk++;
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
			else{
				$.post(
					'po_withdrawal_admin_save.php',
					{
						withdrawal_code : _withdrawal_code,
						appr_note: _appr_note,
						set_status: 3,
					},
					function(data){
						if(data.success){
							console.log("data.success = " + data.success);
							console.log("data.message = " + data.message);
							ShowSuccess();
							// location.reload();
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
	
	$(".btn_cancel").click(function(){
		var _withdrawal_code = $(this).data("withdrawal_code");
		var _appr_note = $("#appr_note").val();
		
		var chk = 0;
		
		if(_appr_note == ""){
			alert("กรุณา กรอก หมายเหตุ ผู้อนุมัติ");
			chk++;
			return false;
		}
		if(chk == 0){
			if(!confirm('คุณต้องการที่จะยืนยันการยกเลิกการเบิกหรือไม่')){
				return false;
			}
			else{
				$.post(
					'po_withdrawal_admin_save.php',
					{
						withdrawal_code : _withdrawal_code,
						appr_note: _appr_note,
						set_status: 4,
					},
					function(data){
						if(data.success){
							console.log("data.success = " + data.success);
							console.log("data.message = " + data.message);
							ShowSuccess();
							// location.reload();
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
	
	function ShowSuccess(){
		$('body').append('<div id="divdialogprint"></div>');
		$('#divdialogprint').html("<br/><div style=\"text-align:center\">บันทึกเรียบร้อยแล้ว<br /><br /><input type=\"button\" name=\"btnPrint\" id=\"btnPrint\" value=\"ตกลง\" onclick=\"javascript:location.reload();\"></div>");
		$('#divdialogprint').dialog({
			title: 'บันทึกเรียบร้อยแล้ว',
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