<?php
include_once("../include/config.php");
include_once("../include/function.php");

if(!CheckAuth()){
    header("Refresh: 0; url=../index.php");
    exit();
}

$page_title = "บันทึกรถ เข้า-ออก";
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
<div id="tab_search"> 
	<fieldset><legend><b>ค้นหาข้อมูล</b></legend>
		<select name="ddl_condition" id="ddl_condition" onChange="javascript:select_condition();">
					<option value="all" style="background-Color:#FFFCCC">แสดงทั้งหมด</option>
					<option value="car_type">ประเภทรถ</option>
					<option value="car_idno">ทะเบียนรถในสต๊อก</option>
					<option value="license_plate">ทะเบียนรถ</option>
		</select>
				
		<span id="span_cars_type" style="display:none">
					<select select name="combo_type" id="combo_type" >
							<option value="">เลือกประเภทรถ</option>
							<?php
								$qru_potype = pg_query("select * from \"CarType\" order by car_type_name");
								while($res = pg_fetch_array($qru_potype)){
								
									$car_type_id = $res['car_type_id'];
									$car_type_name = $res['car_type_name'];
									
									echo " <option value=\"$car_type_id\"> $car_type_name </option>";
								}
							?>
					</select>
		</span>
		
		<span id="span_carid" style="display:none">
					<input type="text" name="txtcaridno" id="txtcaridno" />
		</span>
		
		<span id="span_plate" style="display:none">
					<input type="text" name="txtplate" id="txtplate" />
		</span>
		
		<input type="button" name="search" id="search" value="ค้นหา" onclick="validate();">
	</fieldset>
</div>

<div id="dev_show_content"></div>

      </div>
   <div class="roundedcornr_bottom"><div></div></div>
</div>

<script>
$(document).ready(function(){
    $('#dev_show_content').load('movement_car_api.php?cmd=content');
});
function ShowDetail(id){
    $('body').append('<div id="divdialogadd"></div>');
    $('#divdialogadd').load('movement_car_api.php?cmd=divshow&id='+id);
    $('#divdialogadd').dialog({
        title: 'แสดงรายละเอียดการเคลื่อนไหว : '+id,
        resizable: false,
        modal: true,  
        width: 600,
        height: 350,
        close: function(ev, ui){
            $('#divdialogadd').remove();
        }
    });
}
function select_condition(){
		if($('#ddl_condition').val() == "all"){
			$('#span_cars_type').hide();
			$('#span_carid').hide();
			$('#span_plate').hide();
			$('#combo_type').val('');
			$('#txtcaridno').val('');
			$('#txtplate').val('');
		}else if($('#ddl_condition').val() == "car_type"){
			$('#span_cars_type').show('fast');
			$('#span_carid').hide();
			$('#span_plate').hide();
			$('#txtcaridno').val('');
			$('#txtplate').val('');
		}else if($('#ddl_condition').val() == "car_idno"){
			$('#span_cars_type').hide();
			$('#span_carid').show('fast');
			$('#span_plate').hide();
			$('#combo_type').val('');
			$('#txtplate').val('');
		}else if($('#ddl_condition').val() == "license_plate"){
			$('#span_cars_type').hide();
			$('#span_carid').hide();
			$('#span_plate').show('fast');
			$('#combo_type').val('');
			$('#txtcaridno').val('');
		}
}
function validate(){
	var chk = 0;
	var errorMessage = "";
	
	if($('#ddl_condition').val() == "car_type"){
		if($("#combo_type").val() == ""){
			errorMessage +="กรุณาเลือกประเภทรถ\n";
			chk++;
		}
	}else if($('#ddl_condition').val() == "car_idno"){
		if($("#txtcaridno").val() == ""){
			errorMessage +="กรุณาระบุทะเบียนรถในสต๊อก\n";
			chk++;
		}
	}else if($('#ddl_condition').val() == "license_plate"){
		if($("#txtplate").val() == ""){
			errorMessage +="กรุณาระบุทะเบียนรถ\n";
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
	var vender = $("#txtcaridno").val();
	
	if($("#ddl_condition").val() == "car_type"){
		keyword = $("#combo_type").val();
	}else if($("#ddl_condition").val() == "car_idno"){
		keyword = $("#txtcaridno").val();
	}else if($("#ddl_condition").val() == "license_plate"){
		keyword = $("#txtplate").val();
	}
	
	$('#dev_show_content').load('movement_car_api.php?cmd=content&condition='+$("#ddl_condition").val()+'&keyword='+keyword);
}
$("#txtcaridno").autocomplete({
        source: "car_autocomplete.php?cmd=caridno",
        minLength:1
});
$("#txtplate").autocomplete({
        source: "car_autocomplete.php?cmd=plate",
        minLength:1
});
</script>

</body>
</html>