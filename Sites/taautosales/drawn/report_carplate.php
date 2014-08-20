<?php
include_once("../include/config.php");
include_once("../include/function.php");

if(!CheckAuth()){
    header("Refresh: 0; url=../index.php");
    exit();
}

$page_title = "รายงานป้ายเหล็ก";
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
		<div>
			<select name="ddl_condition" id="ddl_condition" onChange="javascript:select_condition();">
				<option value="all" style="background-Color:#FFFCCC">แสดงทั้งหมด</option>
				<option value="carplate">ค้นหาเลขป้ายแดง</option>

			</select>
			<span id="span_carplate" style="display:none">
				<input type="text" name="txt_name" id="txt_name" style="width:200px" onkeyup="javascript:CheckNaN()">
			</span>
			<input type="button" name="search" id="search" value="ค้นหา" />
		</div>
		
		<div id="list_carplate" style="margin-top:10px"></div>

      </div>
   <div class="roundedcornr_bottom"><div></div></div>
</div>

<script>
$(document).ready(function(){
	 $('#list_carplate').load('list_report_carplate.php');
});
function CheckNaN(){
    if( $('#txt_name').val() == '' ){
        $('#divshow').empty();
        $('#divshow').hide('fast');
    }
}
$("#txt_name").autocomplete({
    source: "autocomplete.php?cmd=car_plate",
    minLength:1
});
$("#search").click(function(){
	var chk = 0;
	var errorMessage = "";
	
	if($('#ddl_condition').val() == "carplate"){
		if($("#txt_name").val() == ""){
			errorMessage +="กรุณาระบุเลขป้ายแดง\n";
			chk++;
		}
	}
	
	if(chk>0){
		alert(errorMessage);
		return false;
	}else{
		$('#list_carplate').load('list_report_carplate.php?condition='+$('#ddl_condition').val()+'&car_plate='+$('#txt_name').val());
	}
});
function select_condition(){
		if($('#ddl_condition').val() == "all"){
			$('#span_carplate').hide();
			$('#txt_name').val('');
		}else if($('#ddl_condition').val() == "carplate"){
			$('#span_carplate').show('fast');
		}
}
</script>

</body>
</html>