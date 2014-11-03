<?php
include_once ("../include/config.php");
include_once ("../include/function.php");
$page_title = "เพิ่มรายการอะไหล่/อุปกรณ์";
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
  <div>
    <table width="860" border="0" cellpadding="2">
	  <tr>
	  	<td width="100" style="text-align:right;">ประเภทกลุ่มอะไหล่</td>
	  	<td width="300">
			<select name="p_code_type" id="p_code_type" style="width:304px;" >
				<option value="">โปรดเลือก ประเภทกลุ่มอะไหล่</option>
<?php
				$partsCodeType_strQuery = "
					SELECT 
						\"PartsCodeID\",
						\"PartsCodeType\",
						\"PartsCodeDetail\"
					FROM 
						\"partsCodeType\" 
					ORDER BY \"PartsCodeID\"
				";
	    		$partsCodeType_query = pg_query($partsCodeType_strQuery);
				while($partsCodeType_result = pg_fetch_array($partsCodeType_query)){ //Query Unit Names for show
?>
					<option value="<?php echo $partsCodeType_result["PartsCodeID"]; ?>">[<?php echo $partsCodeType_result["PartsCodeType"]; ?>] <?php echo $partsCodeType_result["PartsCodeDetail"]; ?></option>
<?php
				}
?>
			</select>
	  	</td>
	  	<td width="100" style="text-align: right; ">
	  		ประเภทรหัสบาร์โค้ด
	  	</td>
	  	<td width="300">
	  		<label style="margin-left: 15px; margin-right: 15px;">
	  			<input type="radio" name="has_barcode" id="has_barcode_yes" value="yes" disabled="disabled" >มีรหัสบาร์โค้ด
	  		</label>
	  		<label>
	  			<input type="radio" name="has_barcode" id="has_barcode_no" value="no" checked="checked" disabled="disabled">ไม่มีรหัสบาร์โค้ด
	  		</label>
	  	</td>
	  </tr>
	  
		
	  <tr class="barcode_type">
	  </tr>
	  
	  <tr>
	    <td style="text-align:right;">ชื่อสินค้า</td>
	    <td ><input type="text" name="p_name" id="p_name" style="width:300px;"  /></td>
	    <td>
	    	
	    </td>
	  </tr>
	  <tr>
	    <td style="text-align:right;">รายละเอียดสินค้า</td>
	    <td >
	    	<input type="text" name="p_detail" id="p_detail" style="width:300px;" />
	    </td>
	  </tr>
	  <tr>
	    <td style="text-align:right;">ราคาขายของสินค้า</td>
	    <td ><input type="text" name="p_priceperunit" id="p_priceperunit" style="width:300px;" maxlength="15" /></td>
	  </tr>
	  <tr>
	    <td style="text-align:right;">หน่วย</td>
	    <td >
	    	<select name="p_unitid" id="p_unitid" style="width:150px;">
	    		<option value="">กรุณาระบุหน่วย</option>
	<?php
				$sqlStr = "
					SELECT * 
					FROM \"parts_unit\" 
					ORDER BY unitname
				";
	    		$qry_table = pg_query($sqlStr);
				while($res = pg_fetch_array($qry_table)){ //Query Unit Names for show
					$table_id = $res['unitid'];
					$table_name = $res['unitname'];
	?>
					<option value="<?php echo "$table_id"; ?>"><?php echo "$table_name"; ?></option>
	<?php
	}
	?>
	    	</select>
	    	
	    	<input type="button" value="เพิ่มหน่วย" onclick="javascript:popU('add_product_unit.php?page=add','','toolbar=no,menubar=no,resizable=no,scrollbars=yes,status=no,location=no,width=750,height=300')" style="cursor:pointer;" />
	    	<!-- Show Add Unit Button -- with Also Run popU For MAKE A POPUP Web (Add Unit Products 's Page) -->
	    	
	    	<input type="button" name="updatelist_p_unitid" id="updatelist_p_unitid" value="click" onclick="refreshListBox()" style="visibility: hidden; " />
	    	<!-- For Using when Unit Name already Added, and this buttons will be clicked After Added the Unit Name -->
	    	
	    </td>
	  </tr>
	  <tr>
	    <td style="text-align:right;">คิดค่าบริการ</td>
	    <td >
	    	<select name="p_svcharge" id="p_svcharge" style="width:150px;">
				<option value="1" selected="selected">คิดค่าบริการ</option>
				<option value="0">ไม่คิดค่าบริการ</option>
	    	</select>
	    </td>
	  </tr>
	  <tr>
	    <td style="text-align:right;">ประเภท</td>
	    <td >
	    	<select name="p_Type" id="p_Type" style="width:150px;" disabled="disabled">
				<option value="0">ไม่แยกรหัสย่อย</option>
				<option value="1" selected="selected">แยกรหัสย่อย</option>
	    	</select>
	    </td>
	  </tr>
	  
	  <tr>
	    <td>&nbsp;</td>
	    <td><input type="submit" name="btnSave" id="btnSave" value="บันทึก" /></td>
	  </tr>
  
    </table>
    
  </div>
</div>
  

</div>
   <div class="roundedcornr_bottom"><div></div></div>
</div>

<script type="text/javascript">
	var parts_code = new Array();
<?php
	$sqlStr_parts_code = "
		SELECT code
		FROM parts;
	"; //Query Parts' Products to check that There are some Parts Name that Already Added on the System
	$query_parts_code = pg_query($sqlStr_parts_code);
	while($res_parts_code = pg_fetch_array($query_parts_code)){
?>
		parts_code.push("<?php echo $res_parts_code["code"]; ?>");<?php
}
?>
	// For Test parts_code can be added From the Query
	// console.log(parts_code);
	
	
	$(function() {
		$("#p_priceperunit").live("keydown", function(e){
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
	});
	
	//For Check ว่่า มีรหัสบาร์โค้ด หรือไม่
	$("input[name=has_barcode]").change(function(){
		var has_barcode = $(this).val();
		console.log(has_barcode);
		if(has_barcode == "yes"){
			
			var str_barcode_dom = ""
				+"		<td style=\"text-align: right; \">"
				+"			รหัสบาร์โค้ด"
				+"		</td>"
				+"		<td>"
				+"			<input type=\"text\" name=\"barcode\" value=\"\" style=\"width:300px;\" />"
				+"		</td>"
			;
			$(".barcode_type").html(str_barcode_dom);
		}
		else if(has_barcode == "no"){
			$(".barcode_type").html("");
		}
	});
	
	
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
			$.post('parts_project_add_product_save.php', {
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
				//If Success, Will be recorded
				if (data.success) {

					//For Test
					console.log("id = " + data.test);
					console.log("data.message = " + data.message);
					alert(data.message);
					
					//ยัดตัวแปรไปหน้าหลัก 
					window.opener.document.getElementById("project_add_exist_parts").value = data.gen_part_code_id;
					window.close();
				}
				
				//If Failed, Will not be recorded
				else {
					alert(data.message);
					console.log(data.message);
				}
			}, 'json');
		}
	});
	
	function comeback_project_add(id){
		$('body').append('<div id="divdialog_new_print" ></div>');
		$('#divdialog_new_print').load('parts_project_add_project_box1.php');
		$('#divdialog_new_print').dialog({
			title: 'พิมพ์รายงาน : '+id,
			resizable: false,
			modal: true,
			width: 300,
			height: 200,
			close: function(ev, ui){
				$('#divdialog_new_print').remove();
			}
		});
	}
	
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