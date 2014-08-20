<?php
/*include_once("../include/config.php");
include_once("../include/function.php");
if(!CheckAuth()){
    header("Refresh: 0; url=../index.php");
    exit();
}*/
$page_title = "พิมพ์สำเนาใบเสร็จรับเงินชั่วคราว";
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
					<option value="receipt_no">เลขที่ใบเสร็จรับเงินชั่วคราว</option>
					<option value="receipt_date">วันที่ใบเสร็จ</option>
				</select> 
				<span id = "span_receipt_no" style="display:none">
					<input type="text" name="txt_receipt_no" id="txt_receipt_no" size="25" >
				</span>
				<span id="span_receipt_date" style="display:none">
					<input type="text" name="txt_start_date" id="txt_start_date" size="20" value="<?php echo $nowdate; ?>">&nbsp;&nbsp;<label>ถึง</label>
					<input type="text" name="txt_end_date" id="txt_end_date" size="20" value="<?php echo $nowdate; ?>">
				</span>&nbsp;
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

	$('#txt_start_date,#txt_end_date').datepicker({
        showOn: 'button',
        buttonImage: '../images/calendar.gif',
        buttonImageOnly: true,
        changeMonth: true,
        changeYear: true,
        dateFormat: 'yy-mm-dd'
	});
	
	//แสดงรายการใบเสร็จรับเงินชั่วคราว
	$('#div_show_result').empty();
	// ไม่แสดง อัตโนมัติ ให้กดค้นหา
	//$('#div_show_result').load('list_receipt_for_reprint.php');
});
	// แสดงเงื่อนไขข้อมูล
	$("#ddl_condition").change(function(){
		if( $("#ddl_condition").val() == 'receipt_no'){
			$('#span_receipt_no').show();
			$('#span_receipt_date').hide();
			$("#txt_start_date").val('');
			$("#txt_end_date").val('');
		}else if($("#ddl_condition").val() == 'receipt_date'){
			$('#span_receipt_no').hide();
			$('#span_receipt_date').show();
			$("#txt_receipt_no").val('');
		}else{
			$('#span_receipt_no').hide();
			$('#span_receipt_date').hide();
			$("#txt_receipt_no").val('');
			$("#txt_start_date").val('');
			$("#txt_end_date").val('');
		}
	});

	function validate(){
		var chk = 0;
		var errorMessage = "";
		if($('#ddl_condition').val() == 'receipt_no'){
			if( $("#txt_receipt_no").val() == "" ){
				errorMessage +="กรุณาระบุ เลขที่ใบเสร็จ";
				chk++;
			}
		}else if($('#ddl_condition').val() == 'receipt_date'){
			if( $("#txt_start_date").val() == "" ){
				errorMessage +="กรุณาระบุ วันที่เริ่มต้น";
				chk++;
			}else if( $("#txt_end_date").val() == "" ){
				errorMessage +="กรุณาระบุ วันที่สิ้นสุด";
				chk++;
			}else{}
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
		if( $("#ddl_condition").val() == 'receipt_no' ){
			str_keyword = $("#txt_receipt_no").val();
		}else if( $("#ddl_condition").val() == 'receipt_date' ){
			str_keyword = $("#txt_start_date").val()+","+$("#txt_end_date").val();
		}else{
			str_keyword = "";
		}
		
		$('#div_show_result').empty();
		$.post('list_receipt_for_reprint.php',{
			condition: $("#ddl_condition").val(),
			keyword: str_keyword
		},
		function(data){
			$('#div_show_result').html(data);
		},'html');
	}
	
	// แสดง Modal Form เหตุผลของการ reprint
	function show_dialog(receipt_no,res_id){
		$('body').append('<div id="div_dialog"></div>');
		$('#div_dialog').load('receipt_reprint_reason.php?receipt_no='+receipt_no+'&res_id='+res_id);
		$('#div_dialog').dialog({
			title: 'เหตุผลในการขอพิมพ์สำเนาใบเสร็จรับเงินชั่วคราว: '+receipt_no,
			resizable: false,
			modal: true,  
			width: 450,
			height: 250,
			close: function(ev, ui){
				$('#div_dialog').remove();
			}
		});
	}
	
</script>

</body>
</html>