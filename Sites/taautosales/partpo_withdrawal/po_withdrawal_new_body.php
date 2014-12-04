<?php
// ini_set('display_startup_errors',1);
// ini_set('display_errors',1);
// error_reporting(-1);

// error_reporting(E_ALL);
// ini_set('display_errors', 1);
$function = new Withdrawal_new_body();

$withdrawalParts = new WithdrawalParts();

?>
<br />
	<div style="width: 50%; float:left; ">
		
		<div>
			<!-- PO type -->
			<div style="width: 40%; float: left; text-align: right; margin-right: 2%; margin-top: 0.4%;">
				<strong>จุดประสงค์ :</strong>
			</div>
			<div style="width: 58%; float: left;">
				<select name="withdrawal_type" id="withdrawal_type">
					<option value="" >เลือกจุดประสงค์</option>
					<option value="1" >เบิกขายซ่อม</option>
					<option value="2" >เบิกประกอบชิ้นงาน</option>
					<option value="3" >เบิกของเสีย</option>
				</select>
			</div>
			<div style="clear: both;"></div>
		</div>
		<div>
			<div style="width: 40%; float: left; text-align: right; margin-right: 2%; margin-top: 0.4%;">
				<b>เจ้าหน้าที่ผู้ทำรายการ :</b>
			</div>
			<div style="width: 58%; float: left;">
				<input type="hidden" name="withdrawal_user_id" id="withdrawal_user_id" value="<?php echo $_SESSION["ss_iduser"]; ?>" disabled="disabled" />
<?php
				foreach ($function->get_fuser() as $fuser_result) {
					if($fuser_result["id_user"] == $_SESSION["ss_iduser"]){
						echo $fuser_result["fullname"];
					}
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
				<select name="withdrawal_withdraw_user_id" id="withdrawal_withdraw_user_id">
<?php
					foreach ($function->get_fuser() as $fuser_result) {
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
				<b>วันที่เบิก :</b>
			</div>
			<div style="width: 58%; float: left;">
				<input type="text" name="withdrawal_date" id="withdrawal_date" class="datepicker" value="" />
			</div>
			<div style="clear: both;"></div>
		</div>
		
		<!-- <div>
			<div style="width: 40%; float: left; text-align: right; margin-right: 2%; margin-top: 0.4%;">
				<b>เลขที่ :</b>
			</div>
			<div style="width: 58%; float: left;">
				<input type="text" name="" id="" class="" value="" />
			</div>
			<div style="clear: both;"></div>
		</div> -->
		
		<div>
			<div style="width: 40%; float: left; text-align: right; margin-right: 2%; margin-top: 0.4%;">
				<b>วันที่ต้องการใช้งาน :</b>
			</div>
			<div style="width: 58%; float: left;">
				<input type="text" name="withdrawal_usedate" id="withdrawal_usedate" class="datepicker" value="" />
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
			<b>รายการเบิกสินค้า</b><br />
			<input type="button" name="btn_add" id="btn_add" value="+ เพิ่ม" disabled="disabled"><input type="button" name="btn_del" id="btn_del" value="- ลบ" disabled="disabled">
		</div>
		
		<div style="float:right; margin-top:10px; width:85%">
			<table cellpadding="5" cellspacing="0" border="0" width="100%" bgcolor="#F0F0F0">
				<tr style="font-weight:bold; text-align:left; " bgcolor="#D0D0D0">
					<td width="5%">ลำดับที่</td>
					<td width="15%">รหัสสินค้า</td>
					<td width="20%">ชื่อสินค้า</td>
					<td width="30%">รายละเอียดสินค้า</td>
					<td width="15%">จำนวนที่เบิกได้สูงสุด(จำนวนสินค้าในคลัง)</td>
					<td width="15%">จำนวนที่เบิก</td>
				</tr>
				<tr bgcolor="#FFFFFF">
					<td>
						1.
					</td>
					<td>
						<input type="text" id="parts_code1" name="parts_code1" class="parts_code" data-code_id="1" disabled="disabled" />
					</td>
					
					<td>
						<span id="parts_name1" class="parts_name"></span>
						<input type="hidden" name="parts_name1" class="parts_name" value="" />
					</td>
					<td>
						<span id="parts_detail1" class="parts_detail"></span>
					</td>
					<td align="center">
						<span id="quantity1" class="quantity" data-quantity_id="1"></span>
						<input type="hidden" name="quantity1" class="quantity" data-quantity_id="1" />
					</td>
					<td>
						<input type="text" name="quantity_withdrawal1" id="quantity_withdrawal1" class="quantity_withdrawal" data-quantity_withdrawal="1" disabled="disabled" style="width:40px; text-align:right" />
					</td>
				</tr>
			</table>
			
			<div id="TextBoxesGroup"></div>
			
			<div class="linedotted"></div>
			<div style="clear:both"></div>
			
			<!-- ############## footer ############## -->
			
			<div style="margin-top:10px" align="center">
				<b>หมายเหตุ:</b><br />
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
				<input type="button" name="" id="btnSubmit" value="สร้างใบเบิก" />
			</div>
		</div>
	
	</div>

<script>
	//######################## Initial Calculate Variables ############################
	var projectDetailCount = new Array();
	var projectDetail = new Array();
	
	//counter = Count how many rows
	var counter = 1;
	
	// ######################## Initial => projectDetail ########################
<?php

	$projectDetail_strQuery = "
		SELECT 
			project_id,
			material_id, 
			use_unit
		FROM 
			\"ProjectDetails\"
		WHERE
			cancel = FALSE 
	;";
	$projectDetail_query = @pg_query($projectDetail_strQuery);

	while ($projectDetail_result = @pg_fetch_array($projectDetail_query)) {
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
	
	
	$("#withdrawal_type").live("change", function(){
		var withdrawal_type_value = $(this).val();
		if(withdrawal_type_value == ""){
			$(".withdrawal_type_2").html("");
			
			// ### Disable the buttons ###
			$("#btn_add").prop("disabled", true);
			$("#btn_del").prop("disabled", true);
			$("#parts_code1").prop("disabled", true);
			$("#parts_code1").val("");
			$("#parts_name1").html("");
			$(".parts_name[name=parts_name1]").val("");
			$("#parts_detail1").html("");
			$("#quantity1").html("");
			$(".quantity[name=quantity1]").val("");
			$("#quantity_withdrawal1").val("");
			$("#quantity_withdrawal1").prop("disabled", true);
			counter = 1;
			$("#TextBoxesGroup").html("");
		}
		else if(withdrawal_type_value == 1){
			$(".withdrawal_type_2").html("");
			
			// ### Enable the buttons ###
			$("#btn_add").prop("disabled", false);
			$("#btn_del").prop("disabled", false);
			$("#parts_code1").prop("disabled", false);
			$("#parts_code1").val("");
			$("#parts_name1").html("");
			$(".parts_name[name=parts_name1]").val("");
			$("#parts_detail1").html("");
			$("#quantity1").html("");
			$(".quantity[name=quantity1]").val("");
			$("#quantity_withdrawal1").val("");
			$("#quantity_withdrawal1").prop("disabled", true);
			counter = 1;
			$("#TextBoxesGroup").html("");
		}
		else if(withdrawal_type_value == 2){
			
			var string = ''
			+'	<div style="width: 40%; float: left; text-align: right; margin-right: 2%; margin-top: 0.4%;">'
			+'		<b> ใช้ทำโปรเจค :</b>'
			+'	</div>'
			+'	<div style="width: 58%; float: left;">'
			+'		<select name="project_id" class="project_id">'
			+'			<option value="">'
			+'				เลือกโปรเจค'			
			+'			</option>';
<?php
			$project_strQuery = "
				SELECT 
					project_id, name
	  			FROM 
	  				\"Projects\"
	  			WHERE
	  				cancel = FALSE;
			";
			$project_query = @pg_query($project_strQuery);
			while ($project_result = @pg_fetch_array($project_query)) {
?>
				string = string
				+'			<option value="<?php echo $project_result["project_id"]; ?>">'
				+'				<?php echo $project_result["name"]; ?>'			
				+'			</option>';
<?php
			}
?>
			string = string
			+'		</select>'
			+'	</div>'
			+'	<div style="width: 40%; float: left; text-align: right; margin-right: 2%; margin-top: 0.4%;">'
			+'		<b> ต้องการผลิตเป็นสินค้าจำนวน :</b>'
			+'	</div>'
			+'	<div style="width: 58%; float: left;">'
			+'		<input type="text" name="project_quantity" class="project_quantity" value="" disabled="disabled" />'
			+'	</div>'
			;
			
			$(".withdrawal_type_2").html(string);
			
			
			
			
			// ### Disable the buttons ###
			$("#btn_add").prop("disabled", true);
			$("#btn_del").prop("disabled", true);
			
			$("#parts_code1").prop("disabled", true);
			$("#parts_code1").val("");
			$("#parts_name1").html("");
			$(".parts_name[name=parts_name1]").val("");
			$("#parts_detail1").html("");
			$("#quantity1").html("");
			$(".quantity[name=quantity1]").val("");
			$("#quantity_withdrawal1").val("");
			$("#quantity_withdrawal1").prop("disabled", true);
			counter = 1;
			$("#TextBoxesGroup").html("");
		}
		else if(withdrawal_type_value == 3){
			
			$(".withdrawal_type_2").html("");
			
			// ### Enable the buttons ###
			$("#btn_add").prop("disabled", false);
			$("#btn_del").prop("disabled", false);
			$("#parts_code1").prop("disabled", false);
			$("#parts_code1").val("");
			$("#parts_name1").html("");
			$(".parts_name[name=parts_name1]").val("");
			$("#parts_detail1").html("");
			$("#quantity1").html("");
			$(".quantity[name=quantity1]").val("");
			$("#quantity_withdrawal1").val("");
			$("#quantity_withdrawal1").prop("disabled", true);
			$("#TextBoxesGroup").html("");
			counter = 1;
			$("#TextBoxesGroup").html("");
		}
	});
	
	
	$(".project_id").live("change", function(){
		var project_id = $(this).val();
		
		if(project_id == ""){
			$(".project_quantity").val("");
			$(".project_quantity").prop("disabled", true);
		}
		else{
			$(".project_quantity").prop("disabled", false);
			
			// แสดงค่า ในช่อง -> ต้องการผลิตเป็นสินค้าจำนวน
			$(".project_quantity").val("1");
			
			get_project_detail(project_id, 1);
		}
	});
	
	$(".project_quantity").live("keyup", function(){
		var project_id = $(".project_id").val();
		var project_quantity = $(this).val();
		
		if(project_quantity == ""){
			project_quantity = 0;
		}
		
		get_project_detail(project_id, project_quantity);
	});
	
	function get_project_detail(project_id, project_quantity){
		$.post(
			"po_withdrawal_requesturl.php",
			{
				_function: "get_project_detail",
				_project_id: project_id
			},
			function(data){
				
				console.log(data);
				
				// Count how many of parts do they have in that project
				
				// for(var i = 0; i < data.length; i++){
					// product_id = data[i].product_id;
					// console.log(product_id);
				// }
				
				// ### Clear List of Parts Data (Body Table)
				counter = 1;
				$("#TextBoxesGroup").html("");
				$(".parts_code#parts_code1").val("");
				$(".parts_name#parts_name1").val("");
				$(".parts_name[name=parts_name1]").html("");
				$(".parts_detail#parts_detail1").val("");
				$(".quantity#quantity1").html("");
				$(".quantity[name=quantity1]").val("");
				$(".quantity_withdrawal#quantity_withdrawal1").val("");
				$(".quantity_withdrawal#quantity_withdrawal1").prop("disabled", true);
				
				console.log("counter = "+counter);
				
				var check_quantity_withdrawal_status = 0;
				
				for(var i = 0; i < data.length; i++){
					
					if(i < data.length - 1){
						$("#btn_add").click();
					}
					
					//Get Parts_code Detail
					var parts = $.ajax({
						type: "POST",
						url: "po_withdrawal_requesturl.php",
						data: {
							_function: "get_stock_detail_by_code",
							_parts_code: data[i].parts_code
						},
						dataType: "json",
						async:false
					});
					
					parts = $.parseJSON(parts.responseText);
					
					console.log(parts);
					
					// ใส่ค่า Parts Code
					$(".parts_code#parts_code"+(i+1)).val(data[i].parts_code);
					
					// Enable Text Fields
					$("#parts_code"+(i+1)).prop("disabled", false);
					$(".quantity_withdrawal#quantity_withdrawal"+(i+1)).prop("disabled", false);
					
					// เพิ่มค่า Name, Detail
					$(".parts_name#parts_name"+(i+1)).html(parts.name);
					$(".parts_name[name=parts_name"+(i+1)+"]").val(parts.name);
					
					$(".parts_detail#parts_detail"+(i+1)).html(parts.detail);
					
					// // ใส่ค่า quantity_withdrawal
					$(".quantity#quantity"+(i+1)).text(parts.stock_aval+" ("+parts.stock_remain+") ");
					$(".quantity[name=quantity"+(i+1)+"]").val(parts.stock_aval+" ("+parts.stock_remain+") ");
					
					
					// Disable Text Fields
					$("#parts_code"+(i+1)).prop("disabled", true);
					$(".quantity_withdrawal#quantity_withdrawal"+(i+1)).prop("disabled", true);
					
					if(!_parts_details__check_quantity_withdrawal(parts.stock_aval, parseInt(data[i].use_unit), project_quantity)){
						check_quantity_withdrawal_status++;
					}
				}
				if(check_quantity_withdrawal_status > 0){
					alert("ท่านได้เบิกสินค้า เกินกว่า ที่เบิกได้สูงสุด");
					$(".project_quantity").val("0");
				}
				
				
			},
			"json"
		);
	}
	
	function _parts_details__check_quantity_withdrawal(stock_aval, use_unit, project_quantity){
		
		//เปลี่ยนแปลงค่า ใน Withdrawal_quantity
		// Check ค่าก่อน
		var status_proeject_quantity = 0;
		for(var i = 0; i < counter; i++){
			
			if(stock_aval < parseInt(use_unit) * parseInt(project_quantity)){
				status_proeject_quantity++;
			}
		}
		
		//เปลี่ยนแปลงค่า ใน Withdrawal_quantity
		if(status_proeject_quantity > 0){
			for(var i = 0; i < counter; i++){
				$(".quantity_withdrawal#quantity_withdrawal"+(i+1)).val("0");
			}
			return false;
		}
		else{
			for(var i = 0; i < counter; i++){
				$(".quantity_withdrawal#quantity_withdrawal"+(i+1)).val(parseInt(use_unit) * parseInt(project_quantity));
			}
			return true;
		}
	}
	
	$(".project_quantity").live("keydown", function(e){
		// Allow: backspace, delete, tab, escape, enter and .
		if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
		     // Allow: Ctrl+A
		    (e.keyCode == 65 && e.ctrlKey === true) || 
		     // Allow: home, end, left, right
		    (e.keyCode >= 35 && e.keyCode <= 39)) {
		         // let it happen, don't do anything
		         return;
		}
		// Ensure that it is a number and stop the keypress
		if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
		    e.preventDefault();
		}
	});
	
	$(".quantity_withdrawal").live("keydown", function(e){
		// Allow: backspace, delete, tab, escape, enter and .
		if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
		     // Allow: Ctrl+A
		    (e.keyCode == 65 && e.ctrlKey === true) || 
		     // Allow: home, end, left, right
		    (e.keyCode >= 35 && e.keyCode <= 39)) {
		         // let it happen, don't do anything
		         return;
		}
		// Ensure that it is a number and stop the keypress
		if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
		    e.preventDefault();
		}
	});
	
	//On Key Enter For close Autocomplete
	$(".quantity_withdrawal").live("keydown", function(event) {
		var code_id = $(this).data("quantity_withdrawal");
		if(event.keyCode == 13){
			$(".parts_code#parts_code"+(code_id+1)).focus();
	    }
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
	
	
	$(".parts_code").live("focus", function() {
		var data_code_id = $(this).data("code_id");
		var url_request = "";
		var withdrawal_type_data = $("#withdrawal_type").val(); 
		
		if(withdrawal_type_data == 1 || withdrawal_type_data == 2){
			url_request = "po_withdrawal_requesturl.php?_function=search_by_stock_code";
		}
		else if(withdrawal_type_data == 3){
			url_request = "po_withdrawal_requesturl.php?_function=search_by_stockBroken_code";
		}
		
		$(this).autocomplete({
			source: url_request,
			delay: 500,
			minLength:1,
			select: function(event, ui) {
				if(withdrawal_type_data == 1 || withdrawal_type_data == 2){
					$.post(
						"po_withdrawal_requesturl.php",
						{
							_function : "get_stock_detail_by_code",
							_parts_code : ui.item.code
						},
						function(data){
							console.log(data);
							$("#parts_name"+data_code_id).text(data.name);
							$(".parts_name[name=parts_name"+data_code_id+"]").val(data.name);
							$("#parts_detail"+data_code_id).text(data.detail);
							$(".quantity#quantity"+data_code_id).text(data.stock_aval+" ("+data.stock_remain+")");
						},
						'json'
					);
				}
				else if(withdrawal_type_data == 3){
					$.post(
						"po_withdrawal_requesturl.php",
						{
							_function : "get_stock_broken_detail_by_code",
							_parts_code : ui.item.code
						},
						function(data){
							console.log(data);
							$("#parts_name"+data_code_id).text(data.name);
							$(".parts_name[name=parts_name"+data_code_id+"]").val(data.name);
							$("#parts_detail"+data_code_id).text(data.detail);
							$(".quantity#quantity"+data_code_id).text(data.stock_aval+" ("+data.stock_remain+")");
						},
						'json'
					);
				}
			},
			response: function (event, ui) {
				
			}
	    }).data("autocomplete")._renderItem = function(ul, item) {
	    	item.label = item.code+" # "+item.barcode+" # "+item.name+" # "+item.details;
	    	item.value = item.code;
	 		if(item.type=='1'){
				// return $('<li class="ui-menu-item disabled" style="margin-top:5px; margin-bottom: 5px; margin-left: 5px; color: #999; "></li>').data("item.autocomplete", item).append('<span>'+item.name+' # '+item.name+'</span>').appendTo(ul);
				
				return $('<li class="ui-menu-item disabled" style="margin-top:5px; margin-bottom: 5px; margin-left: 5px; color: #999; "></li>').data("item.autocomplete", item).append('<span>'+item.label+'</span>').appendTo(ul);
			}
			else{
			    return $("<li></li>").data("item.autocomplete", item).append("<a>" + item.label + "</a>").appendTo(ul);
			}
		};
	});
	
	
	//On Key (Parts_code) Enter For close Autocomplete, and Key on this Quantity
	$(".parts_code").live("keydown", function(event) {
		var code_id = $(this).data("code_id");
		if(event.keyCode == 13){
			
			var withdrawal_type_data = $("#withdrawal_type").val();
			
			if(withdrawal_type_data == 1 || withdrawal_type_data == 2){
				var _parts = $.ajax({
					type: "POST",
					url: "po_withdrawal_requesturl.php",
					data: {
						_function: "get_stock_detail_by_code",
						_parts_code: $(this).val()
					},
					dataType: "json",
					async:false
				});
			}
			else if(withdrawal_type_data == 3){
				var _parts = $.ajax({
					type: "POST",
					url: "po_withdrawal_requesturl.php",
					data: {
						_function: "get_stock_broken_detail_by_code",
						_parts_code: $(this).val()
					},
					dataType: "json",
					async:false
				});
			}
			
			data = $.parseJSON(_parts.responseText);
			$(this).val(data.code);
			$("#parts_name"+code_id).text(data.name);
			$(".parts_name[name=parts_name"+code_id+"]").val(data.name);
			$("#parts_detail"+code_id).text(data.detail);
			$(".quantity#quantity"+code_id).text(data.stock_aval+" ("+data.stock_remain+")");
			
			$(this).autocomplete( "close" );
			$(".quantity_withdrawal#quantity_withdrawal"+code_id).prop("disabled", false);
			$(".quantity_withdrawal#quantity_withdrawal"+code_id).focus();
	    }
	});
	
	
	//On Key (quantity) Enter For close Autocomplete, and Key on this costperunit
	$(".quantity_withdrawal").live("keydown", function(event) {
		var code_id = $(this).data("quantity_withdrawal");
		if(event.keyCode == 13){
			$(".parts_code#parts_code"+(code_id+1)).focus();
	    }
	});
	
	
	$(".parts_code").live("blur", function(){
		var this_id = $(this).data("code_id");
		var parts_code = $(".parts_code#parts_code"+this_id).val();
		
		if(parts_code != ""){
			$(".quantity#quantity"+this_id).prop("disabled", false);
			$(".quantity_withdrawal#quantity_withdrawal"+this_id).prop("disabled", false);
		}
		else{
			$(".quantity#quantity"+this_id).prop("disabled", true);
			$(".quantity_withdrawal#quantity_withdrawal"+this_id).prop("disabled", true);
		}
	});
	
	
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
		+ '	<td width="15%" align="center">'
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
	
	//################### Submit ####################
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
		else if(_withdrawal_type == 2){
			var _withdrawal_project_id = $('.project_id').val();
			var _withdrawal_project_quantity = $(".project_quantity").val();
			
			console.log(_withdrawal_project_id);
			console.log(_withdrawal_project_quantity);
			
			if(_withdrawal_project_id == ""){
				alert('กรุณากรอก ใช้ทำโปรเจค');
				chk++;
				return false;
			}
			if(_withdrawal_project_quantity == "" || _withdrawal_project_quantity == 0){
				alert('กรุณากรอก ต้องการผลิตเป็นสินค้าจำนวน');
				chk++;
				return false;
			}
		}
		else if(_withdrawal_type == 1 || _withdrawal_type == 3){
			var _withdrawal_project_id = null;
			var _withdrawal_project_quantity = null;
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
					withdrawal_project_id : _withdrawal_project_id,
					withdrawal_project_quantity : _withdrawal_project_quantity,
					
					withdrawal_details_array: JSON.stringify(arradd),
					
					withdrawal_note : _withdrawal_note,
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
	
	// ############# END Submit #############
	
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