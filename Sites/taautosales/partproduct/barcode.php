<?php
include_once ("../include/config.php");
include_once ("../include/function.php");
$page_title = "พิมพ์บาร์โค้ดอะไหล่/อุปกรณ์";
//Title Bar Name
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="txt/html; charset=utf-8" />
    <title><?php echo $company_name; ?> - <?php echo $page_title; ?></title>
    <LINK href="../images/styles.css" type=text/css rel=stylesheet>

    <link type="text/css" href="../images/jqueryui/css/redmond/jquery-ui-1.8.16.custom.css" rel="stylesheet" />
    <script type="text/javascript" src="../images/jqueryui/js/jquery-1.6.2.min.js"></script>
    <script type="text/javascript" src="../images/jqueryui/js/jquery-ui-1.8.16.custom.min.js"></script>

</head>
<body>

<div class="roundedcornr_box" style="width:900px">
   <div class="roundedcornr_top"><div></div></div>
      <div class="roundedcornr_content">

<?php
include_once ("../include/header_popup.php");
?>

<div style="text-align:left;">&nbsp;&nbsp;</div>

<div>
	<div style="float:right; "><button style="width:75px;" onclick="window.location='product.php'">กลับ</button></div><br />
	<div>
		<table width="860" border="0" cellpadding="2">
			<tr>
				<td width="200" style="text-align:right;">ประเภท</td>
				<td width="600" >
					<select name="p_Type" id="p_Type" style="width:150px;">
						<option value="">โปรดเลือกรหัสย่อย</option>
						<option value="0">ไม่แยกรหัสย่อย</option>
						<option value="1">แยกรหัสย่อย</option>
					</select>
				</td>
			</tr>
			<tr>
				<td style="text-align:right;">
					รหัสสินค้าที่พิมพ์จาก รหัส
				</td>
				<td>
					<input type="text" name="barcode_start" class="" value="" style="width:300px;" />
				</td>
			</tr>
			<tr>
				<td style="text-align:right;">
					รหัสสินค้าที่พิมพ์ถึง รหัส
				</td>
				<td>
					<input type="text" name="barcode_end" class="" value="" style="width:300px;" />
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td><input type="submit" name="btnSave" id="btnSave" value="สั่งพิมพ์" /></td>
			</tr>
		</table>
    
	</div>
</div>
  

</div>
   <div class="roundedcornr_bottom"><div></div></div>
</div>

<script type="text/javascript">
	
	$("#p_Type").live("change", function(){
		var _p_type = $(this).val();
		if(_p_type == 0){
			$("input[name=barcode_start]").val("");
			$("input[name=barcode_end]").val("");
			
			$("input[name=barcode_start]").removeClass("parts_code_detail");
			$("input[name=barcode_end]").removeClass("parts_code_detail");
			
			$("input[name=barcode_start]").addClass("parts_code_type0");
			$("input[name=barcode_end]").addClass("parts_code_type0");
		}
		else if(_p_type == 1){
			$("input[name=barcode_start]").val("");
			$("input[name=barcode_end]").val("");
			
			$("input[name=barcode_start]").removeClass("parts_code_type0");
			$("input[name=barcode_end]").removeClass("parts_code_type0");
			
			$("input[name=barcode_start]").addClass("parts_code_detail");
			$("input[name=barcode_end]").addClass("parts_code_detail");
		}
	});
	
	// ###################################### Load All Parts ##############################################
	var parts_type0 = new Array();
<?php
	$strQuery_parts_type0 = "
		SELECT 
			code,
			name,
			details,
			type
		FROM
			\"parts\"
		WHERE
			type = 0;
	";
	$qry_parts_type0=@pg_query($strQuery_parts_type0);
	$numrows_parts_type0 = pg_num_rows($qry_parts_type0);
	// $parts_data = array();
	while($res_parts=@pg_fetch_array($qry_parts_type0)){
		
		// $parts_data[] = $res_parts;
		$dt['value'] = $res_parts['code'];
		$dt['label'] = $res_parts["code"]." # ".$res_parts["name"]." # ".$res_parts["details"];
		$dt['type'] = $res_parts["type"];
		$parts_type0_matches[] = $dt;
		
?>
		parts_type0.push([
			"<?php echo $res_parts['code']; ?>", 
			"<?php echo $res_parts['name']; ?>", 
			"<?php echo $res_parts['details']; ?>",
		]);
<?php
	}
	if($numrows_parts_type0 == 0){
        $parts_type0_matches[] = "ไม่พบข้อมูล";
    }
?>
	var parts_code_type0_autocomplete = <?php echo json_encode($parts_type0_matches); ?>;
	$(".parts_code_type0").live("focus", function() {
		$(this).autocomplete({
	        source: parts_code_type0_autocomplete,
	        minLength:1,
	        select: function(event, ui) {
				if(ui.item.value == 'ไม่พบข้อมูลเก่า'){
					
				}else{
				   
				}
	        }
	    });
	});
	// #################################### End Load All Parts ############################################
	
	
	
	// #################################### Load All Parts Detail ############################################
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
		$dt['type'] = $res_parts["type"];
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
?>
	// Calculate How many Quantity left after already withdrawal the Parts
	var total_send_quantity_array = new Array();
<?php
	// Calculate How many Quantity left after already withdrawal the Parts
	// จำนวนที่ได้กดเบิกออกไปจางคลังแล้ว ==> เอาค่านี้ ไปรวมกับ จำนวนที่เบิกได้ ถึงจะสามารถ นับได้ว่า เราเบิกได้สูงสุด หลังจากที่นับ จากของที่เบิกไปแล้ว
	$max_send_quantity = 0;
	$view_withdrawal_quantity_strQuery = "
		SELECT
			parts_code,
			SUM(send_quantity) as send_quantity
		FROM 
			v_parts_withdrawal_quantity
		group by parts_code ;
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
?>
	var parts_code_autocomplete = <?php echo json_encode($parts_matches); ?>;
	$(".parts_code_detail").live("focus", function() {
		$(this).autocomplete({
	        source: parts_code_autocomplete,
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
	// ################################## End Load All Parts Detail ##########################################
	
	
	$('#btnSave').click(function() {
		var chk = 0;
		var msg = "ผิดพลาด! \n";

		//var txttable = $('#cb_product').val();
		//alert(txttable);

		if ($('#p_code_type').val() == "") {
			msg += "กรุณาระบุ ประเภทกลุ่มอะไหล่ \n";
			chk++;
		}
		
		if ($("input[name=has_barcode]:checked").val() == "" || $("input[name=has_barcode]:checked").val() == null ) {
			msg += "กรุณาระบุ ประเภทรหัสบาร์โค้ด \n";
			chk++;
		}
		else if($("input[name=has_barcode]:checked").val() == "yes"){
			if($("input[name=barcode]").val() == ""){
				msg += "กรุณาระบุ รหัสบาร์โค้ด \n";
				chk++;
			}
		}
		
		if ($('#p_name').val() == "") {
			msg += "กรุณาระบุ ชื่อสินค้า \n";
			chk++;
		}
		if ($('#p_detail').val() == "") {
			msg += "กรุณาระบุ รายละเอียดสินค้า \n";
			chk++;
		}

		if ($('#p_priceperunit').val() == "") {
			msg += "กรุณาระบุ ราคาขายของสินค้า \n";
			chk++;
		}

		if ($('#p_unitid').val() == "") {
			msg += "กรุณาระบุ หน่วย \n";
			chk++;
		}
		if ($('#p_svcharge').val() == "") {
			msg += "กรุณาระบุ ค่าบริการ \n";
			chk++;
		}
		if ($('#p_Type').val() == "") {
			msg += "กรุณาระบุ ประเภท \n";
			chk++;
		}
		
		//For Valudate check that there are Parts Product that already Added
		/*
		for (var i = 0; i < parts_code.length; i++) {//For Valudate check that there are Parts Product that already Added
			var count_code = 0;
			if ($('#p_code').val() == parts_code[i]) {
				count_code++;
			}
			// console.log("count_code = "+count_code);
			if (count_code > 0) {
				msg += "กรุณาระบุ รหัสสินค้าใหม่ เนื่องจากรหัสสินค้าซ้ำกับของเก่า \n";
				chk++;
			}
		}
		*/

		if (chk > 0) {
			alert(msg);
			return false;
		} else {
			
			if(!confirm('คุณต้องการที่จะยืนยันการอนุมัติหรือไม่')){
				return false;
			} 
			
			if($("input[name=has_barcode]:checked").val() == "yes"){
				var _barcode = $("input[name=barcode]").val();
			}
			else if($("input[name=has_barcode]:checked").val() == "no"){
				var _barcode = "";
			}
			
			//Send AJAX Request: HTTP POST: For Record Parts 's Products
			$.post('save_product.php', {
				p_has_barcode: $("input[name=has_barcode]:checked").val(),
				p_barcode: _barcode,
				
				p_code_type : $("#p_code_type").val(),
				p_name : $('#p_name').val(),
				p_detail : $('#p_detail').val(),
				p_priceperunit : $('#p_priceperunit').val(),
				p_unitid : $('#p_unitid').val(),
				p_svcharge : $('#p_svcharge').val(),
				p_Type : $('#p_Type').val() //This is 2nd Parameter -- Send Post Variables
			}, function(data) {
				if (data.success) {//If Success, Will be recorded
					console.log("# data.success = success #");

					//For Test
					console.log("id = " + data.test);
					console.log("data.message = " + data.message);

					alert(data.message);
					
					// location.reload();
					location='product.php'
				} else {//If Failed, Will not be recorded
					console.log("# data.success = false #");
					alert(data.message);
					console.log(data.message);
				}
			}, 'json');
		}
	});

	function popU(U, N, T) {
		newWindow = window.open(U, N, T);
		if (!newWindow.opener)
			newWindow.opener = self;
	}

	function refreshListBox()// refresh ประเภทหน่วย
	{
		var dataAssetsList = $.ajax({// รับค่าจาก ajax เก็บไว้ที่ตัวแปร dataAssetsList
			url : "display_add_product_unit.php", // ไฟล์สำหรับการกำหนดเงื่อนไข
			//data:"list1="+$(this).val(), // ส่งตัวแปร GET ชื่อ list1
			async : false
		}).responseText;

		$("select#p_unitid").html(dataAssetsList);
		// นำค่า dataAssetsList มาแสดงใน listbox ที่ชื่อ assets..
		$("select#p_unitid")
	}
</script>

</body>
</html>