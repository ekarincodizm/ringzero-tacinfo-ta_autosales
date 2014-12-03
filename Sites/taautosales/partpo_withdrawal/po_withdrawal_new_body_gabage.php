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

// ########################### Generate PartsBroken ##############################
var broken_parts = new Array();
<?php
	$partsBroken_detail = $function->get_broken_parts_details();
	$numrows_partsBroken = $partsBroken_detail["numrow"];
	
	?>
	console.log(<?php echo json_encode($partsBroken_detail); ?>);
	<?php
	
	foreach ($partsBroken_detail["result"] as $res_parts) {
		
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
	if($numrows_partsBroken == 0){
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
	/*
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
	*/
	

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
	
	
	