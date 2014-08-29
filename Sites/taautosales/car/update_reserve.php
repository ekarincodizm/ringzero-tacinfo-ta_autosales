<?php
include_once("../include/config.php");
include_once("../include/function.php");

if(!CheckAuth()){
    header("Refresh: 0; url=../index.php");
    exit();
}

$page_title = "เปลี่ยนแปลงการจอง";
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

<div id="search" style="width:100%; margin-left:auto;margin-right:auto;" >
			<fieldset><legend><b>ค้นหาข้อมูล</b></legend>
			<form id="search" action="" method="post" > 
				<select name="ddl_condition" id="ddl_condition" onChange="javascript:select_condition();">
					<option value="all" style="background-Color:#FFFCCC">แสดงทั้งหมด</option>
					<option value="reserve_status">สถานะการจอง</option>
					<option value="res_id">เลขที่จอง,ชื่อผู้จอง</option>
				</select>
				
				<span id="span_reserve_status" style="display:none">
					<select select name="ddl_res_status" id="ddl_res_status" >
							<option value="not">เลือกสถานะการจอง</option>
							<option value="2">จอง</option>
							<option value="3">จองซ้อนคันรอเปลี่ยนคัน</option>
							<!--<option value="1">ขาย</option>
							<option value="0">ยกเลิกการจอง</option> -->
					</select>
				</span>
				
				<span id="span_res_id" style="display:none">
					<input type="text" name="txt_res_id" id="txt_res_id" size="60" >
				</span>
				
				<input type="button" name="btn_search" id="btn_search" value="   ค้นหา " onclick="validate();"/>
			</form>
			</fieldset>
		</div>
		<br>
		<div id="div_show_result" style="margin-top:10px"></div>

<div id="divshow" style="margin-top:10px; display:none"></div>

      </div>
   <div class="roundedcornr_bottom"><div></div></div>
</div>

<script type="text/javascript">
$(document).ready(function(){ 

	//แสดงรายการจอง
	$('#div_show_result').empty();
	$('#div_show_result').load('list_reserve_menu_up.php?option=change_reserve');
	
	// ค้นหาเลขที่การจอง
    $("#txt_res_id").autocomplete({
        source: "update_reserve_api.php?cmd=autocomplete",
        minLength:1
    });

});

	// แสดงเงื่อนไขข้อมูล
	$("#ddl_condition").change(function(){
		if( $("#ddl_condition").val() == 'reserve_status'){
			$('#span_reserve_status').show();
			$('#span_res_id').hide();
			$("#txt_res_id").val('');
		}else if($("#ddl_condition").val() == 'res_id'){
			$('#span_res_id').show();
			$('#span_reserve_status').hide();
		}else{
			$('#span_res_id').hide();
			$('#span_reserve_status').hide();
			$("#txt_res_id").val('');
		}
	});

	function validate(){
	var chk = 0;
	var errorMessage = "";
	
	if($('#ddl_condition').val() == 'reserve_status'){
		if($("#ddl_res_status").val() == 'not'){
			errorMessage +="กรุณาเลือกสถานะการจอง";
			chk++;
		}
	}else if($('#ddl_condition').val() == 'res_id'){
		if($("#txt_res_id").val() == ""){
			errorMessage +="กรุณาระบุเลขที่การจองหรือชื่อผู้จอง";
			chk++;
		}
	}else{}
	
	if(chk>0){
		alert(errorMessage);
		return false;
	}else{
		search();
	}
}

	// ค้นหาข้อมูล
	function search(){
		var str_keyword; 
		if( $("#ddl_condition").val() == 'reserve_status' ){
			str_keyword = $('#ddl_res_status').val();
		}else if( $("#ddl_condition").val() == 'res_id' ){
			var split_keyword = $("#txt_res_id").val().split("#");
			str_keyword = split_keyword[0];
		}else{
			str_keyword = "";
		}
		
		$('#div_show_result').empty();
		$.post('list_reserve_menu_up.php',{
			condition: $("#ddl_condition").val(),
			keyword: str_keyword,
			option: 'change_reserve'
		},
		function(data){
			$('#div_show_result').html(data);
		},'html');
	}
	
	// แสดง Modal Form เปลี่ยนแปลงการจอง
	function show_dialog(res_id){
		$('body').append('<div id="divdialogedit"></div>');
		$('#divdialogedit').load('reserv_car_edit_api.php?cmd=edit_resv_all&cmd2=t&id='+res_id);
		$('#divdialogedit').dialog({
			title: 'เปลี่ยนแปลงการจอง : '+res_id,
			resizable: false,
			modal: true,  
			width: 820,
			height: 520,
			close: function(ev, ui){
				$('#divdialogedit').remove();
			}
		});
	}
</script>

</body>
</html>