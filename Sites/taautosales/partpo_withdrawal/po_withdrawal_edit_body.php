<!-- header -->
<br />
<?php
$withdrawalParts_code = pg_escape_string($_GET["code"]);

$class = new Withdrawal_edit_body($withdrawalParts_code);

$appr_user_note = $class->get_approve();
$get_withdrawalParts = $class->get_withdrawalParts();

$partStock = new PartStock();

foreach ($get_withdrawalParts as $withdrawalParts_result) {
?>
	<div style="width: 50%; float:left; ">
		
		<div>
			<!-- PO type -->
			<div style="width: 40%; float: left; text-align: right; margin-right: 2%; margin-top: 0.4%;">
				<strong>จุดประสงค์ :</strong>
			</div>
			<div style="width: 58%; float: left;">
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
				foreach ($class->get_fuser($withdrawalParts_result["user_id"]) as $fuser_result) {
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
				<select name="withdrawal_withdraw_user_id" id="withdrawal_withdraw_user_id">
<?php
					foreach ($class->get_fuser_list() as $fuser_result) {
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
			<div style="width: 40%; float: left; text-align: right; margin-right: 2%; margin-top: 0.4%;">
				<b>วันที่เบิก :</b>
			</div>
			<div style="width: 58%; float: left;">
				<input type="text" name="withdrawal_date" id="withdrawal_date" class="datepicker" value="<?php echo date("d-m-Y", strtotime($withdrawalParts_result["date"])); ?>" />
			</div>
			<div style="clear: both;"></div>
		</div>
		
		<div>
			<div style="width: 40%; float: left; text-align: right; margin-right: 2%; margin-top: 0.4%;">
				<b>วันที่ต้องการใช้งาน :</b>
			</div>
			<div style="width: 58%; float: left;">
				<input type="text" name="withdrawal_usedate" id="withdrawal_usedate" class="datepicker" value="<?php echo date("d-m-Y", strtotime($withdrawalParts_result["usedate"])); ?>" />
			</div>
			<div style="clear: both;"></div>
		</div>
		
		<div class="withdrawal_type_2">
<?php
			if($withdrawalParts_result["type"] == 2){
?>
				<div style="width: 40%; float: left; text-align: right; margin-right: 2%; margin-top: 0.4%;">
					<b> ใช้ทำโปรเจค :</b>
				</div>
				<div style="width: 58%; float: left;">
					<select name="project_id" class="project_id" disabled="disabled">
						<option value="">
							เลือกโปรเจค	
						</option>
<?php
						foreach ($class->get_project() as $project_result) {
?>
							<option value="<?php echo $project_result["project_id"]; ?>" <?php
								if($withdrawalParts_result["project_id"] == $project_result["project_id"]){
									?>selected="selected"<?php
								}
							?>>
								<?php echo $project_result["name"]; ?>
							</option>
<?php
						}
?>
					</select>
				</div>
				<div style="width: 40%; float: left; text-align: right; margin-right: 2%; margin-top: 0.4%;">
					<b> ต้องการผลิตเป็นสินค้าจำนวน :</b>
				</div>
				<div style="width: 58%; float: left;">
					<input type="text" name="project_quantity" id="project_quantity" value="<?php echo $withdrawalParts_result["project_quantity"]; ?>" />
				</div>
<?php
			}
?>
		</div>
		
	</div>
	<div style="clear: both;"></div>
	
	
	<div style="font-size:12px">
		<!-- ##################### Middle ####################### -->
		
		<div style="float:left; margin-top:10px; width:15%">
			<b>รายการเบิกสินค้า</b><br />
			<input type="button" name="btn_add" id="btn_add" value="+ เพิ่ม" <?php 
				if($withdrawalParts_result["type"] == 2){
					?>disabled="disabled"<?php
				} 
			?>><input type="button" name="btn_del" id="btn_del" value="- ลบ" <?php 
				if($withdrawalParts_result["type"] == 2){
					?>disabled="disabled"<?php
				} 
			?>>
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
<?php
				// Count amount of PartsStocks
?>
				<script>
					// Calculate How many Quantity left after already withdrawal the Parts
					var total_send_quantity_array = new Array();
<?php
					// Calculate How many Quantity left after already withdrawal the Parts
					// จำนวนที่ได้กดเบิกออกไปจางคลังแล้ว ==> เอาค่านี้ ไปรวมกับ จำนวนที่เบิกได้ ถึงจะสามารถ นับได้ว่า เราเบิกได้สูงสุด หลังจากที่นับ จากของที่เบิกไปแล้ว
					/*
					$max_send_quantity = 0;
					$view_withdrawal_quantity_strQuery = "
						SELECT
							parts_code,
							SUM(send_quantity) as send_quantity
						FROM 
							v_parts_withdrawal_quantity
						group by parts_code 
						;
					";
					$view_withdrawal_quantity_query = pg_query($view_withdrawal_quantity_strQuery);
					while ($view_withdrawal_quantity_result = pg_fetch_array($view_withdrawal_quantity_query)) {
						$max_send_quantity = $view_withdrawal_quantity_result["send_quantity"];
?>
						total_send_quantity_array.push([
							"<?php echo $view_withdrawal_quantity_result["parts_code"]; ?>",
							"<?php echo $view_withdrawal_quantity_result["send_quantity"]; ?>"
						]);
<?php
					}
					*/
?>
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
					
					
					$getStockDetails = $partStock->get_stock_detail_and_aval($withdrawalPartsDetails_result["parts_code"]);
					
					if($withdrawalPartsDetails_result["idno"] == 1){
						
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
						// echo "$max_send_quantity".$max_send_quantity;
						
						
						// อันนี้คือ Stock_remain ที่รวมกับของ withdrawal_quantity เก่าที่ได้รวมไป
						$stock_remain_with_withdrawal = intval($class->call_parts($withdrawalPartsDetails_result["parts_code"], "withdrawal_quantity")) +  intval($max_send_quantity);
						
?>
						<tr bgcolor="#FFFFFF">
							<td>
								<?php echo $withdrawalPartsDetails_result["idno"]; ?>.
							</td>
							<td>
								<input type="text" id="parts_code1" name="parts_code1" class="parts_code" data-code_id="1" value="<?php echo $withdrawalPartsDetails_result["parts_code"]; ?>" <?php 
									if($withdrawalParts_result["type"] == 2){
										?>disabled="disabled"<?php
									} 
								?> />
							</td>
							
							<td>
								<span id="parts_name1" class="parts_name"><?php echo $class->call_parts($withdrawalPartsDetails_result["parts_code"], "name"); ?></span>
								<input type="hidden" name="parts_name1" class="parts_name" value="<?php echo $class->call_parts($withdrawalPartsDetails_result["parts_code"], "name"); ?>" />
							</td>
							<td>
								<span id="parts_detail1" class="parts_detail"><?php echo $class->call_parts($withdrawalPartsDetails_result["parts_code"], "details"); ?></span>
							</td>
							<td align="center">
								<span id="quantity1" class="quantity" data-quantity_id="1"><?php 
									// echo $stock_remain_with_withdrawal." (".($class->call_parts($withdrawalPartsDetails_result["parts_code"], "stock_remain")).")";
									echo $getStockDetails["stock_aval"]." (".$getStockDetails["stock_remain"].")";
								?></span>
								<input type="hidden" name="quantity1" class="quantity" data-quantity_id="1" value="<?php echo $stock_remain_with_withdrawal; ?>" />
							</td>
							<td>
								<input type="text" name="quantity_withdrawal1" id="quantity_withdrawal1" class="quantity_withdrawal" data-quantity_withdrawal="1" value="<?php echo $withdrawalPartsDetails_result["withdrawal_quantity"]; ?>" style="width:40px; text-align:right" <?php echo $withdrawalPartsDetails_result["parts_code"]; ?>" <?php 
									if($withdrawalParts_result["type"] == 2){
										?>disabled="disabled"<?php
									} 
								?> />
								<?php // echo $withdrawalPartsDetails_result["parts_code"]; ?>
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
											<input type="text" id="parts_code<?php echo $withdrawalPartsDetails_result["idno"]; ?>" name="parts_code<?php echo $withdrawalPartsDetails_result["idno"]; ?>" class="parts_code" data-code_id="<?php echo $withdrawalPartsDetails_result["idno"]; ?>" value="<?php echo $withdrawalPartsDetails_result["parts_code"]; ?>" <?php echo $withdrawalPartsDetails_result["parts_code"]; ?>" <?php 
												if($withdrawalParts_result["type"] == 2){
													?>disabled="disabled"<?php
												} 
											?> />
										</td>
										<td width="20%">
											<span id="parts_name<?php echo $withdrawalPartsDetails_result["idno"]; ?>" class="parts_name"><?php echo $class->call_parts($withdrawalPartsDetails_result["parts_code"], "name"); ?></span>
											<input type="hidden" name="parts_name<?php echo $withdrawalPartsDetails_result["idno"]; ?>" class="parts_name" value="<?php echo $class->call_parts($withdrawalPartsDetails_result["parts_code"], "name"); ?>" />
										</td>
										<td  width="30%">
											<span id="parts_detail<?php echo $withdrawalPartsDetails_result["idno"]; ?>" class="parts_detail"><?php echo $class->call_parts($withdrawalPartsDetails_result["parts_code"], "details"); ?></span>
										</td>
										<td width="15%" align="center">
								    		<span id="quantity<?php echo $withdrawalPartsDetails_result["idno"]; ?>" class="quantity" data-quantity_id="<?php echo $withdrawalPartsDetails_result["idno"]; ?>"><?php 
								    			// echo $stock_remain_with_withdrawal." (".($class->call_parts($withdrawalPartsDetails_result["parts_code"], "stock_remain")).")";
												echo $getStockDetails["stock_aval"]." (".$getStockDetails["stock_remain"].")";
								    		?></span>
											<input type="hidden" name="quantity<?php echo $withdrawalPartsDetails_result["idno"]; ?>" class="quantity" data-quantity_id="<?php echo $withdrawalPartsDetails_result["idno"]; ?>" value="<?php echo $stock_remain_with_withdrawal; ?>" />
								    		
								    	</td>
								    	<td width="15%">
											<input type="text" name="quantity_withdrawal<?php echo $withdrawalPartsDetails_result["idno"]; ?>" id="quantity_withdrawal<?php echo $withdrawalPartsDetails_result["idno"]; ?>" class="quantity_withdrawal" data-quantity_withdrawal="<?php echo $withdrawalPartsDetails_result["idno"]; ?>" value="<?php echo $withdrawalPartsDetails_result["withdrawal_quantity"]; ?>" style="width:40px; text-align:right"  <?php echo $withdrawalPartsDetails_result["parts_code"]; ?>" <?php 
												if($withdrawalParts_result["type"] == 2){
													?>disabled="disabled"<?php
												} 
											?>/>
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
				<b>หมายเหตุ:</b><br />
				<textarea name="withdrawal_note" id="withdrawal_note" cols="130" rows="3"><?php echo $appr_user_note; ?></textarea>
			</div>
			
			<div class="linedotted"></div>
			<div style="clear:both"></div>
			
			<div style="margin-top:10px" align="center">
				<input type="button" name="" id="btnSubmit" value="บันทึก" />
			</div>
		</div>
	</div>
<?php
}
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
	
	/*
	$(".project_id").live("change", function(){
		var project_id = $(this).val();
		var temp_project_detail = new Array();
		
		if(project_id == ""){
			$(".project_quantity").prop("disabled", true);
		}
		else{
			$(".project_quantity").prop("disabled", false);
		
			// แสดงค่า ในช่อง -> ต้องการผลิตเป็นสินค้าจำนวน
			$("#project_quantity").val("1");
			
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
					$("#project_quantity").val("0");
					$(".quantity_withdrawal#quantity_withdrawal"+(i+1)).val( ((temp_project_detail[i][2])*0) );
				}
			}
			else{
				for(var i = 0; i < counter; i++){
					$(".quantity_withdrawal#quantity_withdrawal"+(i+1)).val( ((temp_project_detail[i][2])*_project_quantity) );
				}
			}
		}
	});
	*/
	// ###### End Check Withdrawal Type ######
	$("#project_quantity").live("keyup", function(){
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
				$("#project_quantity").val("0");
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
	
	function parts_code_autocomplete_select(this_id, parts_code){
		// var this_id = $(this).data("code_id");
		// var parts_code = $(".parts_code#parts_code"+this_id).val();
		
		console.log("parts_code = "+parts_code);
		
		var i = 0;
		var parts_name_value = "";
		var parts_detail_value = "";
		var stock_remain_value = 0;
		var sum_withdrawal_quantity_value = 0;
		
				
		$.post(
			'po_withdrawal_requesturl.php',
			{
				_function: 'edit_body__get_parts_details',
				code : '<?php echo $withdrawalParts_code; ?>',
				_parts_code: parts_code,
				_return : 'edit_body__get_parts_details'
			},
			function(data){
				console.log("data"+data);
				console.log("parts_name [AJAX] = "+data.name);
				console.log("parts_detail [AJAX] = "+data.details);
				console.log("stock_remain [AJAX] = "+data.stock_remain);
				console.log("sum_withdrawal_quantity [AJAX] = "+data.sum_withdrawal_quantity);
				if(data.name != ""){
					parts_name_value = data.name;
				}
				if(data.details != ""){
					parts_detail_value = data.details;
				}
				if(data.stock_remain != ""){
					stock_remain_value = data.stock_remain;
				}
				if(data.sum_withdrawal_quantity != ""){
					sum_withdrawal_quantity_value = data.sum_withdrawal_quantity;
				}
				
				
				///*
				// Calculate How many Quantity left after already withdrawal the Parts
				// จำนวนที่ได้กดเบิกออกไปจางคลังแล้ว ==> เอาค่านี้ ไปรวมกับ จำนวนที่เบิกได้ ถึงจะสามารถ นับได้ว่า เราเบิกได้สูงสุด หลังจากที่นับ จากของที่เบิกไปแล้ว
				var total_send_quantity = 0;
				
				if(data.total_send_quantity != ""){
					total_send_quantity = data.total_send_quantity;
				}
				
				var stock_remain_with_withdrawal_value = 0;
				
				if(data.stock_remain_with_withdrawal_value != ""){
					stock_remain_with_withdrawal_value = data.stock_remain_with_withdrawal_value;
					
				}
				
				
				console.log("parts_name = " + parts_name_value);
				console.log("sum_withdrawal_quantity_value = "+sum_withdrawal_quantity_value);
				
				// check ถ้า มี ของอยู่ใน Stock ถึงจะทำการ Show Parts อันนั้น
				if(stock_remain_with_withdrawal_value > 0 && parts_code != "" ){
					
					console.log("pass");
				
					$(".parts_name#parts_name"+this_id).html(parts_name_value);
					$(".parts_name[name=parts_name"+this_id+"]").val(parts_name_value);
					$(".parts_detail#parts_detail"+this_id).html(parts_detail_value);
					$(".quantity#quantity"+this_id).html(stock_remain_with_withdrawal_value+" ("+stock_remain_value+")");
					$(".quantity[name=quantity"+this_id+"]").val(stock_remain_with_withdrawal_value);
					$(".quantity_withdrawal#quantity_withdrawal"+this_id).val(stock_remain_with_withdrawal_value);
					
					$(".quantity#quantity"+this_id).prop("disabled", false);
					$(".quantity_withdrawal#quantity_withdrawal"+this_id).prop("disabled", false);
				}
				
				else{
					
					console.log("Failed");
					
					$(this).val("");
					
					$(".parts_name#parts_name"+this_id).html("");
					$(".parts_name[name=parts_name"+this_id+"]").val("");
					$(".parts_detail#parts_detail"+this_id).html("");
					$(".quantity#quantity"+this_id).html("");
					$(".quantity[name=quantity"+this_id+"]").val("");
					$(".quantity_withdrawal#quantity_withdrawal"+this_id).val("");
					
					$(".quantity#quantity"+this_id).prop("disabled", "disabled");
					$(".quantity_withdrawal#quantity_withdrawal"+this_id).prop("disabled", "disabled");
				}
				
				//*/
				
			},
			'json'
		);
		
	}
	
	
	// ทำการเพิ่ม Item Parts_code สำหรับ Autocomplete 
	//var parts_code_autocomplete = <?php //echo json_encode($parts_matches); ?>//;
	
	//On Key Enter For close Autocomplete
	$(".parts_code").live("keydown", function(event) {
		var code_id = $(this).data("code_id");
		if(event.keyCode == 13){
			$(this).autocomplete( "close" );
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
	
	
	$(".parts_code").live("focus", function() {
		
		var this_id = $(this).data("code_id");
		var url_request = "";
		var withdrawal_type_data = $("#withdrawal_type").val(); 
		
		if(withdrawal_type_data == 1 || withdrawal_type_data == 2){
			url_request = "po_withdrawal_requesturl.php?_function=search_by_stock_code";
		}
		else if(withdrawal_type_data == 3){
			url_request = "po_withdrawal_requesturl.php?_function=search_by_stockBroken_code";
		}
		
		$(this).autocomplete({
	        source: "po_withdrawal_requesturl.php?_function=search_by_stock_code",
	        delay: 700,
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
	
	// ตรวจสอบว่า Quantity Withdrawal เกิน Stock หรือไม่ ถ้าเกิน ก็ไม่ให้เบิก ระบบจะลดจำนวน ให้เท่ากับจำนวนทั้งหมด 
	/*
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
	*/
	
	
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
		
		var _project_quantity = "";
		var _project_id = "";
		if(_withdrawal_type == 2){
			
			_project_id = $(".project_id").val();
			_project_quantity = $("#project_quantity").val();
			
			if(_project_id == ""){
				alert('กรุณากรอก ต้องการผลิตเป็นสินค้าจำนวน');
				chk++;
				return false;
			}
			if(_project_quantity == "" || _project_quantity == 0 || _project_quantity == "0"){
				alert('กรุณากรอก ต้องการผลิตเป็นสินค้าจำนวน');
				chk++;
				$("#project_quantity").focus();
				return false;
			}
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
			if(!confirm('ต้องการทำการบันทึก รหัส : <?php echo $withdrawalParts_code; ?> หรือไม่?')){
				return false;
			}
		
			$.post(
				'po_withdrawal_edit_save.php',
				{
					withdrawal_code: "<?php echo $withdrawalParts_code; ?>",
					
					withdrawal_type: _withdrawal_type,
					withdrawal_user_id: _withdrawal_user_id,
					withdrawal_withdraw_user_id : _withdrawal_withdraw_user_id,
					withdrawal_date : _withdrawal_date,
					withdrawal_usedate : _withdrawal_usedate,
					
					project_id : _project_id,
					project_quantity : _project_quantity,
					
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