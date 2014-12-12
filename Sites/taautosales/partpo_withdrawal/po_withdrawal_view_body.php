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
		user_note, appr_note
	FROM 
		\"PartsApproved\"
	WHERE
		code = '".$withdrawalParts_code."' ;
";
$appr_query = @pg_query($appr_strQuery);
$appr_user_note = @pg_fetch_result($appr_query, 0);
$appr_appr_note = @pg_fetch_result($appr_query, 1);

// $po_withdrawal_page

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
			<div style="width: 50%; float: left; text-align: right; margin-right: 2%; margin-top: 0.4%;">
				<strong>จุดประสงค์ :</strong>
			</div>
			<div style="width: 48%; float: left;">
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
			<div style="width: 50%; float: left; text-align: right; margin-right: 2%; margin-top: 0.4%;">
				<b>เจ้าหน้าที่ผู้ทำรายการ :</b>
			</div>
			<div style="width: 48%; float: left;">
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
			<div style="width: 50%; float: left; text-align: right; margin-right: 2%; margin-top: 0.4%;">
				<b>วันที่เบิก :</b>
			</div>
			<div style="width: 48%; float: left;">
				<?php echo date("d-m-Y", strtotime($withdrawalParts_result["date"])); ?>
			</div>
			<div style="clear: both;"></div>
		</div>
		
		<div>
			<div style="width: 50%; float: left; text-align: right; margin-right: 2%; margin-top: 0.4%;">
				<b>วันที่ต้องการใช้งาน :</b>
			</div>
			<div style="width: 48%; float: left;">
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
					<td width="15%">จำนวนที่เบิกได้สูงสุด(จำนวนสินค้าในคลัง)</td>
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
					
					// อันนี้คือ Stock_remain ที่รวมกับของ withdrawal_quantity เก่าที่ได้รวมไป
					$stock_remain_with_withdrawal = intval($class->call_parts($withdrawalPartsDetails_result["parts_code"], "withdrawal_quantity")) +  intval($max_send_quantity);
?>
					<tr bgcolor="#FFFFFF">
						<td>
							<?php echo $withdrawalPartsDetails_result["idno"]; ?>.
						</td>
						<td>
							<?php echo $withdrawalPartsDetails_result["parts_code"]; ?>
						</td>
						
						<td>
							<?php echo $class->call_parts($withdrawalPartsDetails_result["parts_code"], "name"); ?>
						</td>
						<td>
							<?php echo $class->call_parts($withdrawalPartsDetails_result["parts_code"], "details"); ?>
						</td>
						<td align="center">
							<?php // echo $stock_remain_with_withdrawal." (".(call_parts($withdrawalPartsDetails_result["parts_code"], "stock_remain")).")"; //." (".(call_parts($withdrawalPartsDetails_result["parts_code"], "withdrawal_quantity")).")"; ?>
							<?php echo $getStockDetails["stock_aval"]." (".$getStockDetails["stock_remain"].")"; ?>
						</td>
						<td align="center">
							<?php echo $withdrawalPartsDetails_result["withdrawal_quantity"]; ?>
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
<?php
			if($po_withdrawal_page == 3){
?>
				<div style="margin-top:10px" align="center">
					<b>หมายเหตุ ของผู้เบิก:</b><br />
				</div>
				<div style="margin-top:10px" align="center">
					<?php echo $appr_appr_note; ?>
				</div>
<?php
			}
?>
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

	$('#btn_add').click(function(){
	    counter++;
	    var newTextBoxDiv = $(document.createElement('div')).attr("id", 'TextBoxDiv' + counter);
	
	    table = 
	    '<table width="100%" cellpadding="5" cellspacing="0" border="0">'
	    + ' <tr>'
	    + ' <td width="5%">'
	    + '		'+counter+'.'
	    + '	</td>'
	    + ' <td width="15%">'
		+ ' 	<input type="text" id="parts_code' + counter + '" name="parts_code' + counter + '" class="parts_code" data-code_id="' + counter + '" />'
		+ ' </td>'
		+ '	<td width="20%">'
		+ '		<span id="parts_name' + counter + '" class="parts_name"></span>'
		+ '		<input type="hidden" name="parts_name' + counter + '" class="parts_name" value="" />'
		+ '	</td>'
		+ '	<td  width="30%">'
		+ '		<span id="parts_detail' + counter + '" class="parts_detail"></span>'
		+ '	</td>'
		+ '	<td width="15%" align="right">'
	    + ' 	<span id="quantity' + counter + '" class="quantity" data-quantity_id="' + counter + '"></span>'
	    + '		<input type="hidden" name="quantity' + counter + '" class="quantity" data-quantity_id="' + counter + '" />'
	    + '	</td>'
	    + '	<td width="15%">'
		+ '		<input type="text" name="quantity_withdrawal' + counter + '" id="quantity_withdrawal' + counter + '" class="quantity_withdrawal" data-quantity_withdrawal="'+ counter +'" disabled="disabled" style="width:40px; text-align:right" />'
		+ '	</td>'
		+ '</tr>'
		+ '</table>';
	
		newTextBoxDiv.html(table);
		newTextBoxDiv.appendTo("#TextBoxesGroup");
	});
	
	//############### Delete ###################
	$("#btn_del").click(function(){
		if(counter==1){
		return false;
		}
		$("#TextBoxDiv" + counter).remove();
		counter--;
	});
	
	function numberWithCommas(x) {
	    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
	}
	
	// $("#btnClose").click(function(){
		// $('#divdialogadd').remove();
	// });
	
	
	//########## Submit ###########
	$('#btnSubmit').click(function(){
		var chk = 0;
		
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
			if(_quantity_withdrawal == "" || _quantity_withdrawal == 0){
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
				quantity_withdrawal: _quantity_withdrawal, 
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
		
		if(chk == 0){
			if(!confirm('ต้องการทำการบันทึกหรือไม่?')){
				return false;
			}
		
			$.post(
				'po_withdrawal_new_save.php',
				{
					withdrawal_type: _withdrawal_type,
					withdrawal_user_id: _withdrawal_user_id,
					withdrawal_withdraw_user_id : _withdrawal_withdraw_user_id,
					withdrawal_date : _withdrawal_date,
					withdrawal_usedate : _withdrawal_usedate,
					
					withdrawal_details_array: JSON.stringify(arradd),
					
					withdrawal_note : _withdrawal_note,
				},
				function(data){
					if(data.success){
						ShowPrint(data.parts_pocode);
						console.log("data.success = " + data.success);
						console.log("data.message = " + data.message);
						console.log("data.test = " + data.test);
						//location.reload();
					}else{
						alert(data.message);
						console.log("data.success = " + data.success);
						console.log("data.message = " + data.message);
						console.log("data.test = " + data.test);
					}
				},'json'
			);
		}
		
	}); //End BtnClick
	
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