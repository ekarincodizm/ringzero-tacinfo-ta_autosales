<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

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
	<style>
    	.ui-autocomplete { height: 400px; overflow-y: scroll; overflow-x: hidden;}
    </style>
</head>
<body>

<div class="roundedcornr_box" style="width:900px">
   <div class="roundedcornr_top"><div></div></div>
      <div class="roundedcornr_content">

<?php
include_once ("../include/header_popup.php");
include_once ("barcode_service.php");

$class = new Model_barcode();

?>

<div style="text-align:left;">&nbsp;&nbsp;</div>

<div>
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
					<input type="text" name="barcode_start" id="barcode_start" class="" value="" style="width:300px;" disabled="disabled" />
				</td>
			</tr>
			<tr>
				<td style="text-align:right;">
					รหัสสินค้าที่พิมพ์ถึง รหัส
				</td>
				<td>
					<input type="text" name="barcode_end" id="barcode_end" class="" value="" style="width:300px;" disabled="disabled" />
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
		if(_p_type == ""){
			$("input[name=barcode_start]").val("");
			$("input[name=barcode_end]").val("");
			
			$("input[name=barcode_start]").prop("disabled", true);
			$("input[name=barcode_end]").prop("disabled", true);
		}
		else if(_p_type == 0){
			$("input[name=barcode_start]").prop("disabled", false);
			$("input[name=barcode_end]").prop("disabled", false);
			
			$("input[name=barcode_start]").val("");
			$("input[name=barcode_end]").val("");
			
			$("input[name=barcode_start]").removeClass("parts_code_detail");
			$("input[name=barcode_end]").removeClass("parts_code_detail");
			
			$("input[name=barcode_start]").addClass("parts_code_type0");
			$("input[name=barcode_end]").addClass("parts_code_type0");
		}
		else if(_p_type == 1){
			$("input[name=barcode_start]").prop("disabled", false);
			$("input[name=barcode_end]").prop("disabled", false);
			
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


	$parts_type0 = $class->get_parts_type0();
	$numrows_parts_type0 = $parts_type0["numrow"];
	foreach ($parts_type0["result"] as $res_parts) {
		
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
	$parts = $class->get_parts();
	$numrows_parts = $parts["numrow"];
	
	foreach ($parts["result"] as $res_parts) {
		
		$dt['value'] = $res_parts['code'];
		$dt['label'] = $res_parts["code"]." # ".$res_parts["name"]." # ".$res_parts["details"];
		$dt['type'] = $res_parts["type"];
		$parts_matches[] = $dt;
		
		$stock_remain = "";
		
		// ## Check Stock_remain ##
		if($res_parts["type"] == 0 || $res_parts["type"] == 1){
			
			$stock_remain = $class->get_v_parts_stock__count_per_parts_code($res_parts["code"]);
			
		}
		elseif($res_parts["type"] == 3){
			$stock_remain = 1;
		}
		
		if($stock_remain == "" || $stock_remain == NULL){
			$stock_remain = 0;
		}
		// ## End Check Stock_remain ##
		
		
		// ## Check Quantity ที่ ได้กดเบิกไป แล้วค้างอยู่ใน Queue ##
		$sum_withdrawal_quantity = $class->get_v_parts_withdrawal_quantity3($res_parts["code"]);
		
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
	foreach ($class->get_view_withdrawal_quantity() as $view_withdrawal_quantity_result) {
		
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
	    	
			if(item.type=='1'){
			    return $('<li class="ui-menu-item disabled" style="margin-top:5px; margin-bottom: 5px; margin-left: 5px; color: #999; "></li>').data("item.autocomplete", item).append('<span>'+item.label+' # '+String__stock_remain+'</span>').appendTo(ul);
			}
			else{
			    return $("<li></li>").data("item.autocomplete", item).append("<a>" + item.label + "</a>").appendTo(ul);
			}
		};
	});
	// ################################## End Load All Parts Detail ##########################################
	
	
	$('#btnSave').click(function() {
		var chk = 0;
		var msg = "ผิดพลาด! \n";
		var p_Type = $('#p_Type').val();
		var barcode_start = $('#barcode_start').val();
		var barcode_end = $('#barcode_end').val();

		if (p_Type == "") {
			msg += "กรุณาเลือก รหัสย่อย \n";
			chk++;
		}
		if (barcode_start == "") {
			msg += "กรุณาระบุ รหัสสินค้าที่พิมพ์จาก รหัส \n";
			chk++;
		}
		if (barcode_end == "") {
			msg += "กรุณาระบุ รหัสสินค้าที่พิมพ์ถึง รหัส \n";
			chk++;
		}
		
		
		if (chk > 0) {
			alert(msg);
			return false;
		} else {
			
			if(!confirm('คุณต้องการที่จะยืนยันการพิมพ์บาร์โค้ด หรือไม่')){
				return false;
			}
			
			popU('barcode_pdf.php?p_Type='+p_Type+'&barcode_start='+barcode_start+'&barcode_end='+barcode_end,'','toolbar=no,menubar=no,resizable=no,scrollbars=yes,status=no,location=no,width=750,height=300');
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