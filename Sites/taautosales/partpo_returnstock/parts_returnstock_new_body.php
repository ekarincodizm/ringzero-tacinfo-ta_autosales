<br />
<?php
	$function = new Return_stock_new();
	$warehouses = $function->get_warehouses(); 
	$locate = $function->get_locate();
?>
<script>
	var warehouses_value = <?php echo json_encode($warehouses); ?>;
	var locate_value = <?php echo json_encode($locate); ?>;
</script>
	<div style="width: 50%; float:left; ">
		
		<div>
			<!-- PO type -->
			<div style="width: 40%; float: left; text-align: right; margin-right: 2%; margin-top: 0.4%;">
				<strong>จุดประสงค์ :</strong>
			</div>
			<div style="width: 58%; float: left;">
				<select name="return_type" id="return_type">
					<option value="" selected="selected" >เลือกจุดประสงค์</option>
					<option value="1" >คืนของเข้าสต๊อก</option>
					<option value="2" >คืนเป็นของเสีย</option>
				</select>
			</div>
			<div style="clear: both;"></div>
		</div>
		<div>
			<div style="width: 40%; float: left; text-align: right; margin-right: 2%; margin-top: 0.4%;">
				<b>เจ้าหน้าที่ผู้ทำรายการ :</b>
			</div>
			<div style="width: 58%; float: left;">
				<input type="hidden" name="return_user_id" id="return_user_id" value="<?php echo $_SESSION["ss_iduser"]; ?>" disabled="disabled" />
<?php
				echo $function->get_fuser_fullname($_SESSION["ss_iduser"]);
?>
			</div>
			<div style="clear: both;"></div>
		</div>
		<div>
			<div style="width: 40%; float: left; text-align: right; margin-right: 2%; margin-top: 0.4%;">
				<b>เจ้าหน้าที่ผู้ขอคืน :</b>
			</div>
			<div style="width: 58%; float: left;">
				<select name="return_return_user_id" id="return_return_user_id">
<?php
					foreach ($function->get_fuser_list_fullname() as $fuser_result) {
?>
						<option value="<?php echo $fuser_result["id_user"]; ?>" <?php
							if($fuser_result["id_user"] == $_SESSION["ss_iduser"]){
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
			<div style="width: 40%; float: left; text-align: right; margin-right: 2%; margin-top: 0.4%;">
				<b>วันที่คืน :</b>
			</div>
			<div style="width: 58%; float: left;">
				<input type="text" name="return_date" id="return_date" class="datepicker" value="" />
			</div>
			<div style="clear: both;"></div>
		</div>
		
		<div class="withdrawal_type_2">
			
		</div>
		
	</div>
	<div style="clear: both;"></div>
	
	
	<div style="font-size:12px">
		<!-- ##################### Middle ####################### -->
		
		<div style="float:left; margin-top:10px; width:15%">
			<b>รายการที่คืนสินค้า</b><br />
			<input type="button" name="btn_add" id="btn_add" value="+ เพิ่ม"><input type="button" name="btn_del" id="btn_del" value="- ลบ">
		</div>
		
		<div style="float:right; margin-top:10px; width:85%">
			<table cellpadding="5" cellspacing="0" border="0" width="100%" bgcolor="#F0F0F0">
				<tr style="font-weight:bold; text-align:left; " bgcolor="#D0D0D0">
					<td width="8%">ลำดับที่</td>
					<td width="10%">มีรหัสแยกย่อย</td>
					<td width="17%">รหัสสินค้า</td>
					<td width="20%">ชื่อสินค้า</td>
					<td width="25%">รายละเอียดสินค้า</td>
					<td width="5%">จำนวนที่รับ</td>
					
					<td width="10%">คลัง</td>
					<td width="5%">ชั้นวาง</td>
				</tr>
<tr bgcolor="#FFFFFF">
					<td>
						1.
					</td>
					<td>
						<!-- <select class="parts_type" name="parts_type1" id="parts_type1" data-id="1">
							<option value="-1">เลือก</option>
							<option value="1">มี</option>
							<option value="0">ไม่มี</option>
						</select> -->
						<span class="parts_type_label" name="parts_type_label1" id="parts_type_label1" data-id="1"></span>
						<input type="hidden" class="parts_type" name="parts_type1" id="parts_type1" data-id="1" value="" />
					</td>
					<td>
						<input type="text" id="parts_code1" name="parts_code1" class="parts_code" data-code_id="1" />
					</td>
					
					<td>
						<span id="parts_name1" class="parts_name"></span>
						<input type="hidden" name="parts_name1" class="parts_name" value="" />
					</td>
					<td>
						<span id="parts_detail1" class="parts_detail"></span>
					</td>
					<td>
						<input type="text" name="quantity_return1" id="quantity_return1" class="quantity_return" data-quantity_return="1" disabled="disabled" style="width:40px; text-align:right" />
					</td>
					
					
					<td>
						<select name="wh_id1" id="wh_id1>" class="wh_id">
							<option value="">โปรดเลือกคลัง</option>
<?php
							foreach ($warehouses as $warehouses_data) {
								?><option value="<?php echo $warehouses_data["wh_id"] ?>"><?php echo $warehouses_data["wh_name"] ?></option>
<?php
							}
?>
						</select>
					</td>
					<td>
						<select name="locate_id1" id="locate_id1" class="locate_id">
							<option value="">โปรดเลือกชั้นวาง</option>
<?php
							foreach ($locate as $locate_data) {
								?><option value="<?php echo $locate_data["locate_id"] ?>"><?php echo $locate_data["locate_name"] ?></option><?php
							}
?>
						</select>
					</td>
				</tr>
			</table>
			
			<div id="TextBoxesGroup"></div>
			
			<div class="linedotted"></div>
			<div style="clear:both"></div>
			
			<!-- ############## footer ############## -->
			
			<div style="margin-top:10px" align="center">
				<b>หมายเหตุ:</b><br />
				<textarea name="return_note" id="return_note" cols="130" rows="3"></textarea>
			</div>
			
			<div class="linedotted"></div>
			<div style="clear:both"></div>
			
			<div style="margin-top:10px" align="center">
				<input type="button" name="" id="btnSubmit" value="สร้างใบเบิก" />
			</div>
		</div>
	
	</div>
<script>
	var parts = new Array();
	
<?php
	foreach ($function->get_all_SendParts_javascript() as $result) {
		
		$dt['value'] = $result["code"];
		$dt['label'] = 
			$result["code"].
			" # ".
			$result["barcode"].
			" # ".
			$result["name"].
			" # ".
			$result["details"];
		$parts_matches[] = $dt;
?>
		parts.push([
			"<?php echo $result['code']; ?>", 
			"<?php echo $result['name']; ?>", 
			"<?php echo $result['details']; ?>",
			"<?php echo $result['type']; ?>",
		]);
<?php
	}
?>
	//######################## Initial Calculate Variables ############################
	var projectDetailCount = new Array();
	var projectDetail = new Array();
	
	//counter = Count how many rows
	var counter = 1;
	
	// ######################## Initial => projectDetail ########################
<?php
	foreach ($function->get_projectDetailCount() as $projectDetailCount_result) {
?>
		projectDetailCount.push([
			"<?php echo $projectDetailCount_result["project_id"]; ?>",
			"<?php echo $projectDetailCount_result["count"]; ?>"
		]);
<?php
	}
	
	foreach ($function->get_projectDetail() as $projectDetail_result) {
?>
		projectDetail.push([
			"<?php echo $projectDetail_result["project_id"]; ?>",
			"<?php echo $projectDetail_result["material_id"]; ?>",
			"<?php echo $projectDetail_result["use_unit"]; ?>"
		]);
<?php
	}
	// ###################### END Initial => projectDetail ######################
?>
	
	$(function() {
		$(".datepicker").datepicker({
			dateFormat: 'dd-mm-yy' ,
		});
	}); 
	
	/*
	$(".parts_type").live("change", function(){
		var this_id = $(this).data("id");
		var this_value = $(this).val();
		if(this_value == 1 || this_value == 0){
			$(".parts_code#parts_code"+this_id).prop("disabled", false);
			$(".quantity_return#quantity_return"+this_id).prop("disabled", false);
		}
		if(this_value == "-1"){
			$(".parts_code#parts_code"+this_id).prop("disabled", true);
			$(".quantity_return#quantity_return"+this_id).prop("disabled", true);
		}
		$(".parts_code#parts_code"+this_id).val("");
		$(".parts_name#parts_name"+this_id).html("");
		$(".parts_name[name=parts_name"+this_id+"]").val("");
		$(".parts_detail#parts_detail"+this_id).html("");
		$(".quantity_return#quantity_return"+this_id).val("");
	});
	*/
	
	$(".parts_code").live("blur", function(){
		var this_id = $(this).data("code_id");
		var parts_code = $(".parts_code#parts_code"+this_id).val();
		
		var i = 0;
		var parts_name_value = "";
		var parts_detail_value = "";
		var parts_type_value = 0;
		var parts_type_label = "";
		
		for(i=0; i<parts.length; i++){
			// ### คำสั่ง $.inArray(ค่าที่จะหา, array นั้น) --> มันจะได้ Index ของ ค่าที่จะหา ถ้าไม่มี จะได้ค่า -1 (index เริ่มนับจาก 0 เป็นตัวแรก) ###
			if($.inArray($(this).val() , parts[i]) == 0){
				parts_name_value = parts[i][1];
				parts_detail_value = parts[i][2];
				parts_type_value = parts[i][3];
				
				if(parts_type_value == 1){
					parts_type_label = "มี";
				}
				if(parts_type_value == 0){
					parts_type_label = "ไม่มี";
				}
				
				break;
			}
		}
		
		console.log("parts_name = " + parts_name_value);
		console.log("parts_detail_value = " + parts_detail_value);
		
		if(parts_code != "" && parts_name_value != ""){
		// check ถ้า มี ของอยู่ใน Stock ถึงจะทำการ Show Parts อันนั้น
			
			$(".parts_type#parts_type"+this_id).val(parts_type_value);
			$(".parts_type_label#parts_type_label"+this_id).html(parts_type_label);
			
			
			$(".parts_name#parts_name"+this_id).html(parts_name_value);
			$(".parts_name[name=parts_name"+this_id+"]").val(parts_name_value);
			$(".parts_detail#parts_detail"+this_id).html(parts_detail_value);
			
			$(".quantity_return#quantity_return"+this_id).prop("disabled", false);
		}
		else{
			$(".parts_type#parts_type"+this_id).val("");
			$(".parts_type_label#parts_type_label"+this_id).html("");
			
			$(".parts_code#parts_code"+this_id).val("");
			
			$(".parts_name#parts_name"+this_id).html("");
			$(".parts_name[name=parts_name"+this_id+"]").val("");
			$(".parts_detail#parts_detail"+this_id).html("");
			
			$(".quantity_return#quantity_return"+this_id).prop("disabled", true);
		}
		
		
		// // Move curser to the next Parts_code
		// if(this_id < counter){
			// // $(".parts_code#parts_code"+(this_id)).blur();
			// $(".parts_code#parts_code"+(this_id+1)).focus();
		// }
		
	});
	
	var parts_code_autocomplete = <?php echo json_encode($parts_matches); ?>;
	
	//On Key Enter For close Autocomplete
	$(".parts_code").live("keydown", function(event) {
		var code_id = $(this).data("code_id");
		if(event.keyCode == 13){
			$(this).autocomplete( "close" );
			$(".parts_code#parts_code"+(code_id+1)).focus();
	    }
	});
	
	$(".parts_code").live("focus", function() {
		$(this).autocomplete({
	        // source: "po_withdrawal_new_webservice.php",
	        source: parts_code_autocomplete,
	        minLength:1,
	        select: function(event, ui) {
				if(ui.item.value == 'ไม่พบข้อมูลเก่า'){
					
				}else{
				   
				}
	        }
	    });
	});
	
	// ตรวจสอบว่า Quantity Withdrawal เกิน Stock หรือไม่ ถ้าเกิน ก็ไม่ให้เบิก ระบบจะลดจำนวน ให้เท่ากับจำนวนทั้งหมด 
	$(".quantity_return").live("change", function(){
		var quantity_return_value = $(this).val();
		var this_id = $(this).data("quantity_return");
		var quantity = $(".quantity[name=quantity" + this_id + "]").val();
		
		console.log("quantity_return_value = "+quantity_return_value);
		console.log("this_id = "+this_id)
		console.log("quantity = "+quantity);
		
		if(parseInt(quantity_return_value) > parseInt(quantity)){
			alert("คุณกรอกจำนวนสินค้าเกินสต๊อก");
			$(".quantity_return#quantity_return"+this_id).val(quantity);
		}
	});
	
	$('#btn_add').click(function(){
	    counter++;
	    var newTextBoxDiv = $(document.createElement('div')).attr("id", 'TextBoxDiv' + counter);
		
	    table = 
	    '<table width="100%" cellpadding="5" cellspacing="0" border="0">'
	    + ' <tr>'
	    + ' 	<td width="8%">'
	    + '			'+counter+'.'
	    + '		</td>'
	    + ' 	<td width="10%">'
	    + '			<!-- <select class="parts_type" name="parts_type'+ counter +'" id="parts_type'+ counter +'" data-id="'+ counter +'">'
		+ '				<option value="-1">เลือก</option>'
		+ '				<option value="1">มี</option>'
		+ '				<option value="0">ไม่มี</option>'
		+ '			</select> -->'
		+ '			<span class="parts_type_label" name="parts_type_label'+ counter +'" id="parts_type_label'+ counter +'" data-id="'+ counter +'"></span>'
		+ '			<input type="hidden" class="parts_type" name="parts_type'+ counter +'" id="parts_type'+ counter +'" data-id="'+ counter +'" value="" />'
	    + '		</td>'
	    + ' 	<td width="17%">'
		+ '			<input type="text" id="parts_code' + counter + '" name="parts_code' + counter + '" class="parts_code" data-code_id="' + counter + '" />'
		+ '		</td>'
		+ '		<td width="20%">'
		+ '			<span id="parts_name' + counter + '" class="parts_name"></span>'
		+ '			<input type="hidden" name="parts_name' + counter + '" class="parts_name" value="" />'
		+ '		</td>'
		+ '		<td  width="25%">'
		+ '			<span id="parts_detail' + counter + '" class="parts_detail"></span>'
		+ '		</td>'
	    + '		<td width="5%">'
		+ '			<input type="text" name="quantity_return' + counter + '" id="quantity_return' + counter + '" class="quantity_return" data-quantity_return="'+ counter +'" disabled="disabled" style="width:40px; text-align:right" />'
		+ '		</td>'
		+ '	'
		+ '		<td width="10%">'
		+ '			<select name="wh_id' + counter + '" id="wh_id' + counter + '>" class="wh_id">'
		+ '				<option value="">โปรดเลือกคลัง</option>';
						for(var i = 0; i < warehouses_value.length; i++){
							table += ' <option value="' + warehouses_value[i]["wh_id"] + '" >' + warehouses_value[i]["wh_name"] + '</option>';
						}
		table = table
		+ '			</select>'
		+ '		</td>'
		+ '		<td width="5%">'
		+ '			<select name="locate_id' + counter + '" id="locate_id' + counter + '" class="locate_id">'
		+ '				<option value="">โปรดเลือกชั้นวาง</option>';
						for(var i = 0; i < locate_value.length; i++){
							table += ' <option value="' + locate_value[i]["locate_id"] + '" >' + locate_value[i]["locate_name"] + '</option>';
						}
		table = table
		+ '			</select>'
		+ '		</td>'
		+ '	</tr>'
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
	
	
	//########## Submit ###########
	$('#btnSubmit').click(function(){
		var chk = 0;
		
		//### Header ###
		var _return_type = $('#return_type').val();
		var _return_user_id = $('#return_user_id').val();
		var _return_return_user_id = $('#return_return_user_id').val();
		var _return_date = $('#return_date').val();
		
		if(_return_type == ""){
			alert('กรุณาเลือก จุดประสงค์');
			chk++;
			return false;
		}
		
		if(_return_return_user_id == ""){
			alert('กรุณากรอก เจ้าหน้าที่ผู้ขอคืน');
			chk++;
			return false;
		}
		if(_return_date == ""){
			alert('กรุณากรอก วันที่คืน');
			chk++;
			$('#return_date').focus();
			return false;
		}
		
		
		//### Middle ###
		var arradd = new Array();
		for( i=1; i<=counter; i++ ){
			
			var _parts_type = $(".parts_type#parts_type" + i).val();
			
			var _parts_code = $('#parts_code' + i).val();
			var _quantity_return = $('#quantity_return' + i).val();
			var _wh_id = $('.wh_id1#wh_id' + i).val();
			var _locate_id = $(".locate_id#locate_id" + i).val();
			
			if(_parts_code == ""){
				alert('กรุณาเลือก เลือกรหัสสินค้า (รายการที่ '+i+')');
				chk++;
				$('#parts_code' + i).focus();
				return false;
			}
			if(_quantity_return == "" || _quantity_return == 0){
				alert('กรุณากรอก จำนวนที่คืน (รายการที่ '+i+')');
				chk++;
				$('#quantity_return' + i).focus();
				return false;
			}
			/*
			else if(_quantity_return > _quantity){
				alert('กรุณากรอก จำนวนที่เบิก ให้น้อยกว่าจำนวนที่มีอยู่ใน คลัง (รายการที่ '+i+')');
				chk++;
				$(".quantity#quantity" + i).focus();
				return false;
			}
			*/
			arradd[i-1] = { 
				idno: i, 
				parts_type: _parts_type,
				parts_code: _parts_code, 
				quantity_return: _quantity_return, 
				wh_id : _wh_id,
				locate_id : _locate_id
			};
		}
		
		//### Footer ###
		var _return_note = $('#return_note').val();
		
		if(_return_note == ""){
			alert('กรุณากรอก หมายเหตุ');
			chk++;
			$('#return_note').focus();
			return false;
		}
		
		if(chk == 0){
			if(!confirm('ต้องการทำการบันทึกหรือไม่?')){
				return false;
			}
		
			$.post(
				'parts_returnstock_new_save.php',
				{
					return_type: _return_type,
					return_user_id: _return_user_id,
					return_return_user_id : _return_return_user_id,
					return_date : _return_date,
					
					return_details_array: JSON.stringify(arradd),
					
					return_note : _return_note,
				},
				function(data){
					if(data.success){
						ShowPrint(data.parts_pocode);
						console.log("data.success = " + data.success);
						console.log("data.status = " + data.status);
						console.log("data.message = " + data.message);
						//location.reload();
					}else{
						alert(data.message);
						console.log("data.success = " + data.success);
						console.log("data.status = " + data.status);
						console.log("data.message = " + data.message);
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