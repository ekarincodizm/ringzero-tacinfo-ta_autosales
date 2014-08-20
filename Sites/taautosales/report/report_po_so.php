<?php
/* include_once("../include/config.php");
include_once("../include/function.php");
if(!CheckAuth()){
    header("Refresh: 0; url=../index.php");
    exit();
} */

$page_title = "รายงานแสดงประวัติการซื้อขายรถยนต์";
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

		<div style="margin-top:5px; margin-bottom:5px">
			<b>ค้นหาตาม: </b>
			<select name="ddl_condition" id="ddl_condition">
				<option value="not">กรุณาเลือก</option>
				<option value="0">ทั้งหมด</option>
				<option value="1">วันที่ซื้อ</option>
				<option value="2">วันที่ขาย</option>
				<option value="3">เลขที่สัญญาขาย</option>
			</select>

			<span id = "span_po_date" style="display:none">
				<label>วันที่เริ่มต้น</label><input type ="text" name="txt_po_start_date" id="txt_po_start_date" />	
				<label>วันที่สิ้นสุด</label><input type ="text" name="txt_po_end_date" id="txt_po_end_date" />	
			</span>
			<span id="span_so_date" style="display:none">
				<label>วันที่เริ่มต้น</label><input type ="text" name="txt_so_start_date" id="txt_so_start_date" />	
				<label>วันที่สิ้นสุด</label><input type ="text" name="txt_so_end_date" id="txt_so_end_date" />
			</span>
			<span id="span_idno" style="display:none">
				<input type ="text" name="txt_idno" id="txt_idno"/>
			</span>
			<input type="button" name="btn_search" id="btn_search" value="ค้นหา">

			<span id="span_where" style="display:none"></span>
		</div>
		<div id="div_show_data"></div>
	 </div>
   <div class="roundedcornr_bottom"><div></div></div>
</div>

<script type="text/javascript">

  $("#txt_po_start_date, #txt_po_end_date,#txt_so_start_date,#txt_so_end_date").datepicker({
        showOn: 'button',
        buttonImage: '../images/calendar.gif',
        buttonImageOnly: true,
        changeMonth: true,
        changeYear: true,
        dateFormat: 'yy-mm-dd'
    });
	
	$("#ddl_condition").change(function(){
		if( $("#ddl_condition").val() == 0 ){
			$('#span_po_date').hide('fast');
			$('#span_so_date').hide('fast');
			$('#span_idno').hide('fast');
		}else if( $("#ddl_condition").val() == 1){
			$('#span_po_date').show('fast');
			$('#span_so_date').hide('fast');
			$('#span_idno').hide('fast');
		}else if($("#ddl_condition").val() == 2){
			$('#span_so_date').show('fast');
			$('#span_po_date').hide('fast');
			$('#span_idno').hide('fast');
		}else if( $("#ddl_condition").val() == 3){
			$('#span_idno').show('fast');
			$('#span_so_date').hide('fast');
			$('#span_po_date').hide('fast');
		}else{}
	});

	
/*$(document).ready(function(){
	$('#div_show_data').empty();
	$('#div_show_data').load('../report/report_po_so_api.php');
});*/

$('#btn_search').click(function(){

    if($("#ddl_condition").val() == "not"){
        alert('กรุณาเลือก  เงื่อนไขในการค้นหา');
        return false;
    }else{
		search();
		
		//$('#divshowreport').empty();
		//$('#divshowreport').show();
	   // $('#divshowreport').html('<img src="../images/progress.gif" border="0" width="32" height="32" alt="Please Wait">');
		
		//$('#divshowreport').load('report_po_so_api.php?condition='+$('#ddl_condition').val() );
		//$('#divshowreport').load('report_po_so_api.php');
		
		
		/*if($("#cb_type").val() == "1"){
			$('#divshowreport').load('report_reserve_api.php?cmd=report&type=1&cb_month='+$('#cb_month').val()+'&cb_year='+$('#cb_year').val());
		}*/
	}
	
});

function search(){
	$('#div_show_data').empty();
    $.post('report_po_so_api.php',{
       // keyword: 'test' //$('#ddl_condition').val()
    },
    function(data){
        $('#div_show_data').html(data);
		
    },'html');
	
	
}

</script>

</body>
</html>