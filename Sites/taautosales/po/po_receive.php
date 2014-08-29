<?php
include_once("../include/config.php");
include_once("../include/function.php");
if(!CheckAuth()){
    header("Refresh: 0; url=../index.php");
    exit();
}
$page_title = "รับสินค้าตาม Po";
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
include_once("../include/header_popup.php");
?>
<div id="dev_edit">

	<div id="search" style="width:100%; margin-left:auto;margin-right:auto;" >
		<fieldset><legend><b>ค้นหาข้อมูล</b></legend>
			<form id="search" action="" method="post" > 
				
				<select name="ddl_condition" id="ddl_condition" onChange="javascript:select_condition();">
					<option value="all" style="background-Color:#FFFCCC">แสดงทั้งหมด</option>
					<option value="po_type">ประเภทใบสั่งซื้อ</option>
					<option value="po_id">เลขที่ใบสั่งซื้อ</option>
					<option value="po_date">วันที่สั่งซื้อ</option>
					<option value="vender">ผู้ขาย</option>
				</select>
				
				<span id="span_po_type" style="display:none">
					<select select name="combo_potype" id="combo_potype" >
							<option value="">เลือกประเภท</option>
							<?php
								$qru_potype = pg_query("select * from \"POType\" order by po_type_name");
								while($res = pg_fetch_array($qru_potype)){
								
									$po_type_id = $res['po_type_id'];
									$po_type_name = $res['po_type_name'];
									
									echo " <option value=\"$po_type_id\" ";if($combo_potype == $po_type_id ){ echo "selected"; } echo "> $po_type_name </option>";
								}
							?>
					</select>
					

					
				</span>
				
				<span id="span_po_id" style="display:none">
					<input type="text" name="txtpo_id" id="txtpo_id" value="<?php echo $txtpo_id; ?>"/>
				</span>
				
				<span id="span_po_date" style="display:none">
					<input type="text" name="datefrom" id="datefrom" value="<?php echo $datefrom; ?>" /> ถึง <input type="text" name="dateto" id="dateto" value="<?php echo $dateto; ?>"/>
				</span>
				
				<span id="span_vender" style="display:none">
					<input type="text" name="txtvender" id="txtvender" size="50" value="<?php echo $txtvender; ?>" />
				</span>
				
				<input type="button" name="btn_search" id="btn_search" value="   ค้นหา " onclick="validate();"/>
				
			</form>
		</fieldset>
	</div>
	<br>
	<div id="list_receive"></div>

<div>	
<td>
<font color="red" > ***ประเภทเอกสารที่ออกจากระบบ***</font>
<br>
<font color="red" > 1.กรณี รับรถใหม่ (NW) ไม่มีเอกสารออกจากระบบ </font>
<br>
<font color="red" > 2.กรณี รับรถมือสอง (RT) รถคืน แบบ</font><font color="blue" >   มี</font><font color="red"> เงินคืนให้ลูกค้า ได้ เอกสาร  หนังสือสละสิทธิ์ </font>
<br>
<font color="red" > 3.กรณี รับรถมือสอง (RT) รถคืน แบบ</font><font color="blue" > ไม่มี</font><font color="red"> เงินคืนให้ลูกค้า ได้ เอกสาร  หนังสือบอกเลิกสัญญา</font>
<br>
<font color="red" > 4.กรณี รับรถมือสอง (US) รถยึด  ได้ เอกสาร  หนังสือสัญญาซื้อขายรถยนต์</font>
<br>
<font color="red" > 5.กรณี รับรถมือสอง (SC) รถยึด  ได้ เอกสาร  หนังสือบอกเลิกสัญญา</font>
<br>
<font color="red" > 6.กรณี โอนรถยึดมาฝากขาย (SE) ได้ เอกสาร  หนังสือบอกเลิกสัญญา</font><font color="blue" >   จากเมนูโอนรถยึดมาฝากขาย</font>
</td>
</tr>
</div>	
	
	
	
</div>

      </div>
   <div class="roundedcornr_bottom"><div></div></div>
</div>

<script>
$(document).ready(function(){
	
	$('#list_receive').empty();
	$('#list_receive').load('po_list_receive.php');
	/*$('#list_receive').load('po_list_receive.php',{
		condition: $('#ddl_condition').val(),
		keyword: keyword
	});*/
	
	$("#datefrom,#dateto").datepicker({
        showOn: 'button',
        buttonImage: '../images/calendar.gif',
        buttonImageOnly: true,
        changeMonth: true,
        changeYear: true,
        dateFormat: 'yy-mm-dd'
	});
	
	
	
});


//========== ซ่อนหรือแสดงเงื่อนไขสำหรับการค้นหา ==========//
    function select_condition(){
		if($('#ddl_condition').val() == "all"){
			$('#span_po_type').hide();
			$('#span_po_id').hide();
			$('#span_po_date').hide();
			$('#span_vender').hide();
			$("#combo_potype").val('');
			$('#txtpo_id').val('');
			$('#datefrom').val('');
			$('#dateto').val('');
			$('#txtvender').val('');
		}else if($('#ddl_condition').val() == "po_type"){
			$('#span_po_type').show('fast');
			$('#span_po_id').hide();
			$('#span_po_date').hide();
			$('#span_vender').hide();
			$('#txtpo_id').val('');
			$('#datefrom').val('');
			$('#dateto').val('');
			$('#txtvender').val('');
		}else if($('#ddl_condition').val() == "po_id"){
			$('#span_po_type').hide();
			$('#span_po_id').show('fast');
			$('#span_po_date').hide();
			$('#span_vender').hide();
			$("#combo_potype").val('');
			$('#datefrom').val('');
			$('#dateto').val('');
			$('#txtvender').val('');
		}else if($('#ddl_condition').val() == "po_date"){
			$('#span_po_type').hide();
			$('#span_po_id').hide();
			$('#span_po_date').show('fast');
			$('#span_vender').hide();
			$("#combo_potype").val('');
			$('#txtpo_id').val('');
			$('#txtvender').val('');
		}else if($('#ddl_condition').val() == "vender"){
			$('#span_po_type').hide();
			$('#span_po_id').hide();
			$('#span_po_date').hide()
			$('#span_vender').show('fast');
			$("#combo_potype").val('');
			$('#txtpo_id').val('');
			$('#datefrom').val('');
			$('#dateto').val('');
		}
	}
	
	function validate(){
	var chk = 0;
	var errorMessage = "";
	
	if($('#ddl_condition').val() == "po_type"){
		if($("#combo_potype").val() == ""){
			errorMessage +="กรุณาเลือกประเภทรถ\n";
			chk++;
		}
	}else if($('#ddl_condition').val() == "po_id"){
		if($("#txtpo_id").val() == ""){
			errorMessage +="กรุณาระบุเลขที่ใบสั่งซื้อ\n";
			chk++;
		}
	}else if($('#ddl_condition').val() == "po_date"){
		if($("#datefrom").val() == ""){
			errorMessage +="กรุณาระบุวันที่เริ่มต้น\n";
			chk++;
		}
		if($("#dateto").val() == ""){
			errorMessage +="กรุณาระบุวันที่สิ้นสุด\n";
			chk++;
		}
		if($("#datefrom").val() > $("#dateto").val() ){
			errorMessage +="วันที่เริ่มต้นต้องน้อยกว่าวันที่สินสุด\n";
			chk++;
		}
	}else if($('#ddl_condition').val() == "vender"){
		if($("#txtvender").val() == ""){
			errorMessage +="กรุณาระบุผู้ขาย\n";
			chk++;
		}
	}
	
	if(chk>0){
		alert(errorMessage);
		return false;
	}else{
		search_data();
	}
}

function search_data(){
	var keyword = "";
	var vender = $("#txtvender").val();
	var arr_vender = vender.split("#");
	
	if($("#ddl_condition").val() == "po_type"){
		keyword = $("#combo_potype").val();
	}else if($("#ddl_condition").val() == "po_id"){
		keyword = $("#txtpo_id").val();
	}else if($("#ddl_condition").val() == "po_date"){
		keyword = $("#datefrom").val()+" "+$("#dateto").val();
	}else if($("#ddl_condition").val() == "vender"){
		keyword = arr_vender[0];
	}
		
	$('#list_receive').empty();
	$('#list_receive').load('po_list_receive.php',{
		condition: $('#ddl_condition').val(),
		keyword: keyword
	});
}
</script>

</body>
</html>