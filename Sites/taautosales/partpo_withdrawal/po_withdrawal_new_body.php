<?php
// ini_set('display_startup_errors',1);
// ini_set('display_errors',1);
// error_reporting(-1);

$function = new Withdrawal_new_body();
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
			<input type="button" name="btn_add" id="btn_add" value="+ เพิ่ม"><input type="button" name="btn_del" id="btn_del" value="- ลบ">
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
<?php
	//##################### PHP Function ##########################
	
	/*
	function max_send_quantity($withdrawalParts_code, $parts_code){
		$view_withdrawal_quantity_strQuery = "
			SELECT
				parts_code,
				SUM(send_quantity) as send_quantity
			FROM 
				v_parts_withdrawal_quantity
			where 
				\"withdrawal_code\" = '".$withdrawalParts_code."'
				AND
				parts_code = '".$parts_code."'
			group by parts_code ;
		";
		$view_withdrawal_quantity_query = pg_query($view_withdrawal_quantity_strQuery);
		while ($view_withdrawal_quantity_result = pg_fetch_array($view_withdrawal_quantity_query)) {
			$max_send_quantity = $view_withdrawal_quantity_result["send_quantity"];
		}
		$max_send_quantity = $withdrawalPartsDetails_result["withdrawal_quantity"] - $max_send_quantity;
		return $max_send_quantity;
	}
	*/
?>
<script>
	//######################## Initial Calculate Variables ############################
	var projectDetailCount = new Array();
	var projectDetail = new Array();
	
	//counter = Count how many rows
	var counter = 1;
	
	// ######################## Initial => projectDetail ########################
<?php
	$projectDetailCount_strQuery = "
		SELECT 
			project_id,
			count(project_id) AS count
		FROM 
			\"ProjectDetails\"
		WHERE
			cancel = FALSE
		group by project_id; 
	;";
	$projectDetailCount_query = @pg_query($projectDetailCount_strQuery);
	while($projectDetailCount_result = @pg_fetch_array($projectDetailCount_query)){
?>
		projectDetailCount.push([
			"<?php echo $projectDetailCount_result["project_id"]; ?>",
			"<?php echo $projectDetailCount_result["count"]; ?>"
		]);
<?php
	}
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
	
	// ########################### Generate Parts ##############################
	
	var parts = new Array();
<?php
	$parts_detail = $function->get_parts_details();
	$numrows_parts = $parts_detail["numrow"];
	
	foreach ($parts_detail["result"] as $res_parts) {
		
		// $parts_data[] = $res_parts;
		$dt['value'] = $res_parts['code'];
		$dt['label'] = $res_parts["code"]." # ".$res_parts["barcode"]." # ".$res_parts["name"]." # ".$res_parts["details"];
		$dt['type'] = $res_parts["type"];
		$parts_matches[] = $dt;
		
		$stock_remain = "";
		
		// ## Check Stock_remain ##
		if($res_parts["type"] == 0 || $res_parts["type"] == 1){
			$stock_remain = $function->get_v_parts_stock__count_per_parts_code();
		}
		elseif($res_parts["type"] == 3){
			$stock_remain = 1;
		}
		
		if($stock_remain == "" || $stock_remain == NULL){
			$stock_remain = 0;
		}
		// ## End Check Stock_remain ##
		
		// ## Check Quantity ที่ ได้กดเบิกไป แล้วค้างอยู่ใน Queue ##
		$sum_withdrawal_quantity = $function->get_v_parts_withdrawal_quantity3_strQuery();
		
		//ถ้า sum_withdrawal_quantity แล้ว ไม่มีค่า ให้ Set เป็นค่า 0 แทนค่า Null
		if($sum_withdrawal_quantity == ""){
			$sum_withdrawal_quantity = 0;
		}
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
	
	// Calculate How many Quantity left after already withdrawal the Parts
	var total_send_quantity_array = new Array();
<?php
	// Calculate How many Quantity left after already withdrawal the Parts
	// จำนวนที่ได้กดเบิกออกไปจางคลังแล้ว ==> เอาค่านี้ ไปรวมกับ จำนวนที่เบิกได้ ถึงจะสามารถ นับได้ว่า เราเบิกได้สูงสุด หลังจากที่นับ จากของที่เบิกไปแล้ว
	$max_send_quantity = 0;
	foreach ($function->get_view_withdrawal_quantity() as $view_withdrawal_quantity_result) {
		
		$max_send_quantity = $view_withdrawal_quantity_result["send_quantity"];
?>
		total_send_quantity_array.push([
			"<?php echo $view_withdrawal_quantity_result["parts_code"]; ?>",
			"<?php echo $view_withdrawal_quantity_result["send_quantity"]; ?>"
		]);
<?php
	}
	// ######################### END Generate Parts ############################
?>
</script>
<script>
	// ########################### Generate PartsBroken ##############################
	
	var broken_parts = new Array();
<?php
	$parts_detail = $function->get_broken_parts_details();
	$numrows_parts = $parts_detail["numrow"];
	
	?>
	console.log(<?php echo json_encode($parts_detail); ?>);
	<?php
	
	foreach ($parts_detail["result"] as $res_parts) {
		
		// $parts_data[] = $res_parts;
		$dt['value'] = $res_parts['code'];
		$dt['label'] = $res_parts["code"]." # ".$res_parts["barcode"]." # ".$res_parts["name"]." # ".$res_parts["details"];
		$dt['type'] = $res_parts["type"];
		$broken_parts_matches[] = $dt;
		
		$stock_remain = "";
		
		// ## Check Stock_remain ##
		if($res_parts["type"] == 0 || $res_parts["type"] == 1){
			$stock_remain = $function->get_broken_v_parts_stock__count_per_parts_code();
		}
		elseif($res_parts["type"] == 3){
			$stock_remain = 1;
		}
		
		if($stock_remain == "" || $stock_remain == NULL){
			$stock_remain = 0;
		}
		// ## End Check Stock_remain ##
		
		// ## Check Quantity ที่ ได้กดเบิกไป แล้วค้างอยู่ใน Queue ##
		$sum_withdrawal_quantity = $function->get_broken_v_parts_withdrawal_quantity3_strQuery();
		
		//ถ้า sum_withdrawal_quantity แล้ว ไม่มีค่า ให้ Set เป็นค่า 0 แทนค่า Null
		if($sum_withdrawal_quantity == ""){
			$sum_withdrawal_quantity = 0;
		}
		// ## End Check Quantity ที่ ได้กดเบิกไป แล้วค้างอยู่ใน Queue ##
?>
			broken_parts.push([
				"<?php echo $res_parts['code']; ?>", 
				"<?php echo $res_parts['name']; ?>", 
				"<?php echo $res_parts['details']; ?>",
				"<?php echo $stock_remain; ?>",
				"<?php echo $sum_withdrawal_quantity; ?>",
			]);
<?php
	}
	if($numrows_parts == 0){
        $broken_parts_matches[] = "ไม่พบข้อมูล";
    }
	
	// $parts_matches = array_slice($parts_matches, 0, 100);
?>
	
	// Calculate How many Quantity left after already withdrawal the Parts
	var total_send_quantity_array = new Array();
<?php
	// Calculate How many Quantity left after already withdrawal the Parts
	// จำนวนที่ได้กดเบิกออกไปจางคลังแล้ว ==> เอาค่านี้ ไปรวมกับ จำนวนที่เบิกได้ ถึงจะสามารถ นับได้ว่า เราเบิกได้สูงสุด หลังจากที่นับ จากของที่เบิกไปแล้ว
	$max_send_quantity = 0;
	foreach ($function->get_view_withdrawal_quantity() as $view_withdrawal_quantity_result) {
		
		$max_send_quantity = $view_withdrawal_quantity_result["send_quantity"];
?>
		total_send_quantity_array.push([
			"<?php echo $view_withdrawal_quantity_result["parts_code"]; ?>",
			"<?php echo $view_withdrawal_quantity_result["send_quantity"]; ?>"
		]);
<?php
	}
	// ######################### END Generate PartsBroken ############################
?>

	
	$("#withdrawal_type").live("change", function(){
		var withdrawal_type_value = $(this).val();
		if(withdrawal_type_value == ""){
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
			$("#parts_code1").prop("disabled", true);
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
			+'		<input type="text" name="project_quantity" class="project_quantity" value="" />'
			+'	</div>'
			;
			
			$(".withdrawal_type_2").html(string);
			
			
			// ### Disable the buttons ###
			$("#btn_add").prop("disabled", true);
			$("#btn_del").prop("disabled", true);
			$("#parts_code1").prop("disabled", true);
			counter = 1;
			$("#TextBoxesGroup").html("");
			$("#parts_code1").prop("disabled", false);
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
			$("#TextBoxesGroup").html("");
			counter = 1;
			$("#TextBoxesGroup").html("");
			$("#parts_code1").prop("disabled", false);
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
			$("#parts_code1").prop("disabled", false);
		}
	});
	

	$(".project_id").live("change", function(){
		var project_id = $(this).val();
		var temp_project_detail = new Array();
		
		// แสดงค่า ในช่อง -> ต้องการผลิตเป็นสินค้าจำนวน
		$(".project_quantity").val("1");
		
		// Count how many of parts do they have in that project
		var this_count_project = 0;
		for(var i = 0; i < projectDetailCount.length; i++){
			if($.inArray(project_id , projectDetailCount[i]) == 0){
				this_count_project = projectDetailCount[i][1];
			}
		}
		
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
		
		
		// find the real parts for that project
		for(var i = 0; i < projectDetail.length; i++){
			
			// if project_id is in Table : projectDetail
			if($.inArray(project_id , projectDetail[i]) == 0){
				$("#btn_add").click();
				
				temp_project_detail.push([
					projectDetail[i][0],
					projectDetail[i][1],
					projectDetail[i][2],
				]);
			}
		}
		$("#btn_del").click();
		
		// console.log(temp_project_detail);
		
		for(var i = 0; i < counter; i++){
			// console.log(i);
			// if(i == 0){
				// $(".parts_code#parts_code1").val(temp_project_detail[i][1]);
				// // $(".parts_name#parts_name1").val("");
				// // $(".parts_name[name=parts_name1]").html("");
				// // $(".parts_detail#parts_detail1").val("");
				// // $(".quantity#quantity1").html("");
				// // $(".quantity[name=quantity1]").val("");
				// $(".quantity_withdrawal#quantity_withdrawal1").prop("disabled", false);
				// $(".quantity_withdrawal#quantity_withdrawal1").val(temp_project_detail[i][2]);
			// }
			// else{
				
				// ใส่ค่า Parts Code
				$(".parts_code#parts_code"+(i+1)).val(temp_project_detail[i][1]);
				
				// Enable Text Fields
				$("#parts_code"+(i+1)).prop("disabled", false);
				$(".quantity_withdrawal#quantity_withdrawal"+(i+1)).prop("disabled", false);
				
				// เพิ่มค่า Name, Detail
				$(".parts_code#parts_code"+(i+1)).focus();
				$(".parts_code#parts_code"+(i+1)).blur();
				
				// ใส่ค่า quantity_withdrawal
				$(".quantity_withdrawal#quantity_withdrawal"+(i+1)).val(temp_project_detail[i][2]);
				
				// Disable Text Fields
				$("#parts_code"+(i+1)).prop("disabled", true);
				$(".quantity_withdrawal#quantity_withdrawal"+(i+1)).prop("disabled", true);
			// }
		}
		
		//เปลี่ยนแปลงค่า ใน Withdrawal_quantity
		// Check ค่าก่อน
		var _project_quantity = 1;
		var status_proeject_quantity = 0;
		for(var i = 0; i < counter; i++){
			
			console.log($(".quantity[name=quantity"+(i+1)+"]").val());
			// console.log(((temp_project_detail[i][2])*_project_quantity));
			
			// quantity
			if($(".quantity[name=quantity"+(i+1)+"]").val() < ((temp_project_detail[i][2])*_project_quantity) ){
				status_proeject_quantity++;
			}
		}
		
		//เปลี่ยนแปลงค่า ใน Withdrawal_quantity
		if(status_proeject_quantity > 0){
			alert("จำนวนที่เบิกได้สูงสุด เกินกว่า ที่มีอยู่");
			for(var i = 0; i < counter; i++){
				$(".project_quantity").val("0");
				$(".quantity_withdrawal#quantity_withdrawal"+(i+1)).val( ((temp_project_detail[i][2])*0) );
			}
		}
		else{
			for(var i = 0; i < counter; i++){
				$(".quantity_withdrawal#quantity_withdrawal"+(i+1)).val( ((temp_project_detail[i][2])*_project_quantity) );
			}
		}
		
	});
	
	$(".project_quantity").live("keyup", function(){
		var _project_quantity = $(this).val();
		var project_id = $(".project_id").val();
		var temp_project_detail = new Array();
		
		//เก็บค่า Project ล่าสุด จาก Project id นั้น
		// find the real parts for that project
		for(var i = 0; i < projectDetail.length; i++){
			
			// if project_id is in Table : projectDetail
			if($.inArray(project_id , projectDetail[i]) == 0){
				temp_project_detail.push([
					projectDetail[i][0],
					projectDetail[i][1],
					projectDetail[i][2],
				]);
			}
		}
		
		//เปลี่ยนแปลงค่า ใน Withdrawal_quantity
		// Check ค่าก่อน
		var status_proeject_quantity = 0;
		for(var i = 0; i < counter; i++){
			
			console.log($(".quantity[name=quantity"+(i+1)+"]").val());
			// console.log(((temp_project_detail[i][2])*_project_quantity));
			
			// quantity
			if($(".quantity[name=quantity"+(i+1)+"]").val() < ((temp_project_detail[i][2])*_project_quantity) ){
				status_proeject_quantity++;
			}
		}
		
		//เปลี่ยนแปลงค่า ใน Withdrawal_quantity
		if(status_proeject_quantity > 0){
			
			alert("จำนวนที่เบิกได้สูงสุด เกินกว่า ที่มีอยู่");
			for(var i = 0; i < counter; i++){
				$(".project_quantity").val("0");
				$(".quantity_withdrawal#quantity_withdrawal"+(i+1)).val( ((temp_project_detail[i][2])*0) );
			}
		}
		else{
			for(var i = 0; i < counter; i++){
				$(".quantity_withdrawal#quantity_withdrawal"+(i+1)).val( ((temp_project_detail[i][2])*_project_quantity) );
			}
		}
		
	});
	
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
	
	$(".parts_code").live("blur", function(){
		// console.log("parts_code = " + $(this).val());
		var this_id = $(this).data("code_id");
		// console.log("this_id = " + this_id);
		var parts_code = $(".parts_code#parts_code"+this_id).val();
		
		var i = 0;
		var parts_name_value = "";
		var parts_detail_value = "";
		var stock_remain_value = 0;
		var sum_withdrawal_quantity_value = 0;
		
		if($("#withdrawal_type").val() == 1 || $("#withdrawal_type").val() == 2){
			for(i=0; i<parts.length; i++){
				// ### คำสั่ง $.inArray(ค่าที่จะหา, array นั้น) --> มันจะได้ Index ของ ค่าที่จะหา ถ้าไม่มี จะได้ค่า -1 (index เริ่มนับจาก 0 เป็นตัวแรก) ###
				if($.inArray($(this).val() , parts[i]) == 0){
					parts_name_value = parts[i][1];
					parts_detail_value = parts[i][2];
					stock_remain_value = parts[i][3];
					sum_withdrawal_quantity_value = parts[i][4];
					break;
				}
			}
		}
		else if($("#withdrawal_type").val() == 3){
			for(i=0; i<broken_parts.length; i++){
				// ### คำสั่ง $.inArray(ค่าที่จะหา, array นั้น) --> มันจะได้ Index ของ ค่าที่จะหา ถ้าไม่มี จะได้ค่า -1 (index เริ่มนับจาก 0 เป็นตัวแรก) ###
				if($.inArray($(this).val() , broken_parts[i]) == 0){
					parts_name_value = broken_parts[i][1];
					parts_detail_value = broken_parts[i][2];
					stock_remain_value = broken_parts[i][3];
					sum_withdrawal_quantity_value = broken_parts[i][4];
					break;
				}
			}
		}
		
		
		// Calculate How many Quantity left after already withdrawal the Parts
		// จำนวนที่ได้กดเบิกออกไปจางคลังแล้ว ==> เอาค่านี้ ไปรวมกับ จำนวนที่เบิกได้ ถึงจะสามารถ นับได้ว่า เราเบิกได้สูงสุด หลังจากที่นับ จากของที่เบิกไปแล้ว
		var total_send_quantity = 0;
		
		for(i=0; i<total_send_quantity_array.length; i++){
			// ### คำสั่ง $.inArray(ค่าที่จะหา, array นั้น) --> มันจะได้ Index ของ ค่าที่จะหา ถ้าไม่มี จะได้ค่า -1 (index เริ่มนับจาก 0 เป็นตัวแรก) ###
			if($.inArray($(this).val() , total_send_quantity_array[i]) == 0){
				total_send_quantity = total_send_quantity_array[i][1];
				break;
			}
		}
		
		
		var stock_remain_with_withdrawal_value = parseInt(stock_remain_value) - parseInt(sum_withdrawal_quantity_value) + parseInt(total_send_quantity);
		
		console.log("parts_name = " + parts_name_value);
		console.log("parts_detail_value = " + parts_detail_value);
		console.log("stock_remain_value = " + stock_remain_value);
		console.log("sum_withdrawal_quantity_value = " + sum_withdrawal_quantity_value);
		
		
		// check ถ้า มี ของอยู่ใน Stock ถึงจะทำการ Show Parts อันนั้น
		if(stock_remain_with_withdrawal_value > 0 && parts_code != "" ){
			
			$(".parts_name#parts_name"+this_id).html(parts_name_value);
			$(".parts_name[name=parts_name"+this_id+"]").val(parts_name_value);
			$(".parts_detail#parts_detail"+this_id).html(parts_detail_value);
			$(".quantity#quantity"+this_id).html(stock_remain_with_withdrawal_value+" ("+stock_remain_value+")");
			$(".quantity[name=quantity"+this_id+"]").val(stock_remain_with_withdrawal_value);
			$(".quantity_withdrawal#quantity_withdrawal"+this_id).val(stock_remain_with_withdrawal_value);
			
			$(".quantity#quantity"+this_id).prop("disabled", false);
			$(".quantity_withdrawal#quantity_withdrawal"+this_id).prop("disabled", false);
		}
		// check ถ้า ไม่มี ของอยู่ใน Stock ให้ Clear ค่า Parts code ออก
		else{
			$(this).val("");
			
			$(".parts_name#parts_name"+this_id).html("");
			$(".parts_name[name=parts_name"+this_id+"]").val("");
			$(".parts_detail#parts_detail"+this_id).html("");
			$(".quantity#quantity"+this_id).html("");
			$(".quantity[name=quantity"+this_id+"]").val("");
			
			$(".quantity#quantity"+this_id).prop("disabled", "disabled");
			$(".quantity_withdrawal#quantity_withdrawal"+this_id).prop("disabled", "disabled");
			console.log("do here");
		}
		
		// // Move curser to the next Parts_code
		// if(this_id < counter){
			// // $(".parts_code#parts_code"+(this_id)).blur();
			// $(".parts_code#parts_code"+(this_id+1)).focus();
		// }
		
	});
	
	var parts_code_autocomplete = <?php echo json_encode($parts_matches); ?>;
	var broken_parts_code_autocomplete = <?php echo json_encode($broken_parts_matches); ?>;
	// console.log(parts_code_autocomplete);
	// console.log(broken_parts_code_autocomplete);
	
	//On Key Enter For close Autocomplete
	$(".parts_code").live("keydown", function(event) {
		var code_id = $(this).data("code_id");
		if(event.keyCode == 13){
			$(this).autocomplete( "close" );
			$(".quantity_withdrawal#quantity_withdrawal"+code_id).prop("disabled", false);
			$(".quantity_withdrawal#quantity_withdrawal"+code_id).focus();
			
	    }
	});
	
	//On Key Enter For close Autocomplete
	$(".quantity_withdrawal").live("keydown", function(event) {
		var code_id = $(this).data("quantity_withdrawal");
		if(event.keyCode == 13){
			$(".parts_code#parts_code"+(code_id+1)).focus();
	    }
	});
	
	$(".parts_code").live("focus", function() {
		
		var autocomplete = "";
		if($("#withdrawal_type").val() == 1 || $("#withdrawal_type").val() == 2){
			autocomplete = parts_code_autocomplete;
		}
		else if($("#withdrawal_type").val() == 3){
			autocomplete = broken_parts_code_autocomplete;
		}
		
		$(this).autocomplete({
	        // source: "po_withdrawal_new_webservice.php",
	        source: autocomplete,
	        minLength:1,
	        select: function(event, ui) {
				if(ui.item.value == 'ไม่พบข้อมูลเก่า'){
					
				}else{
				   
				}
	        }
	    }).data("autocomplete")._renderItem = function(ul, item) {
	    	
	    	// #### สำหรับ นับจำนวน สินค้าคงเหลือในคลัง ####
	    	var i = 0;
			var parts_name_value = "";
			var parts_detail_value = "";
			var stock_remain_value = 0;
			var sum_withdrawal_quantity_value = 0;
			for(i=0; i<parts.length; i++){
				// ### คำสั่ง $.inArray(ค่าที่จะหา, array นั้น) --> มันจะได้ Index ของ ค่าที่จะหา ถ้าไม่มี จะได้ค่า -1 (index เริ่มนับจาก 0 เป็นตัวแรก) ###
				if($.inArray( item.value , parts[i]) == 0){
					parts_name_value = parts[i][1];
					parts_detail_value = parts[i][2];
					stock_remain_value = parts[i][3];
					sum_withdrawal_quantity_value = parts[i][4];
					break;
				}
			}
			
			// Calculate How many Quantity left after already withdrawal the Parts
			// จำนวนที่ได้กดเบิกออกไปจางคลังแล้ว ==> เอาค่านี้ ไปรวมกับ จำนวนที่เบิกได้ ถึงจะสามารถ นับได้ว่า เราเบิกได้สูงสุด หลังจากที่นับ จากของที่เบิกไปแล้ว
			var total_send_quantity = 0;
			
			for(i=0; i<total_send_quantity_array.length; i++){
				// ### คำสั่ง $.inArray(ค่าที่จะหา, array นั้น) --> มันจะได้ Index ของ ค่าที่จะหา ถ้าไม่มี จะได้ค่า -1 (index เริ่มนับจาก 0 เป็นตัวแรก) ###
				if($.inArray( item.value , total_send_quantity_array[i]) == 0){
					total_send_quantity = total_send_quantity_array[i][1];
					break;
				}
			}
			
			console.log("total_send_quantity = "+total_send_quantity);
			
			var stock_remain_with_withdrawal_value = parseInt(stock_remain_value) - parseInt(sum_withdrawal_quantity_value) + parseInt(total_send_quantity);
			var String__stock_remain = stock_remain_with_withdrawal_value+" ("+stock_remain_value+")";
	    	// #### End สำหรับ นับจำนวน สินค้าคงเหลือในคลัง ####
	    	
	    	// ### ถ้า  stock_remain_with_withdrawal_value หรือของที่ไม่มีอยู่ในคลัง หรือว่า ของหมด Stock ไปแล้ว => ให้ไม่ต้อง Show ใน Autocomplete ###
	    	if(stock_remain_with_withdrawal_value > 0){
	    	
				if(item.type=='1'){
				    return $('<li class="ui-menu-item disabled" style="margin-top:5px; margin-bottom: 5px; margin-left: 5px; color: #999; "></li>').data("item.autocomplete", item).append('<span>'+item.label+' # '+String__stock_remain+'</span>').appendTo(ul);
				}
				else{
				    return $("<li></li>").data("item.autocomplete", item).append("<a>" + item.label + "</a>").appendTo(ul);
				}
				
			}
			
		};
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