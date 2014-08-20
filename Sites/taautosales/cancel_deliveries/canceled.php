<?php
/*
include_once("../include/config.php");
include_once("../include/function.php");
if(!CheckAuth()){
    header("Refresh: 0; url=../index.php");
    exit();
}*/
$page_title = "ขอยกเลิกการส่งมอบรถ";
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
			<b>ค้นหาเลขที่จอง:</b> <input type="text" name="txt_name" id="txt_name" style="width:300px" onkeyup="javascript:CheckReserv()" >
			<input type="button" name="search" id="search" value="ค้นหา" />
		</div>
		<div id="div_list" style="margin-top:10px"></div>
			
		</div>
		
		 <div class="roundedcornr_bottom"><div></div></div>
	</div>

<script>
$(document).ready(function(){ 
	//แสดงรายการหนังสือสัญญาจะซื้อจะขายรถยนต์
	$('#div_show_result').empty();
	$('#div_show_result').load('list_sale_contract_for_reprint.php');
});
	

function CheckReserv(){
    if( $('#txt_name').val() == '' ){
        $('#divshow').empty();
        $('#divshow').hide('fast');
    }
}

$("#txt_name").autocomplete({
	source: "../po/po_autocomplete.php?cmd=cancel_deliveries",
	minLength:1
});

$("#search").click(function(){
	if($('#txt_name').val() == ""){
		alert("กรุณาระบุเลขที่จอง ");
	}else{
		//var txt_con = $('#txt_name').val();
		//alert(txt_con );
		$('#div_list').load('list_sale_contract_for_cancel.php?res_id='+$('#txt_name').val());
	}
});
	
	

	// แสดง Modal Form เหตุผลของการ reprint
	function show_dialog(res_id, doc_no, car_id){
		$('body').append('<div id="div_dialog"></div>');
		$('#div_dialog').load('cancel_reason.php?doc_no='+doc_no+'&res_id='+res_id+'&car_id='+car_id);
		$('#div_dialog').dialog({
			title: 'ขอยกเลิกการส่งมอบรถ เลขที่จอง : '+res_id,
			resizable: false,
			modal: true,  
			width: 450,
			height: 250,
			close: function(ev, ui){
				$('#div_dialog').remove();
			}
		});
	}
	
	// todo ส่วนที่ copy งานที่คล้ายกันมา แต่ยังไม่ได้ใช้ เพื่อจะมีประโยชน์
	/*// แสดง Modal Form เหตุผลของการ reprint
	function show2_dialog(doc_no){
		$('body').append('<div id="div_dialog"></div>');
		$('#div_dialog').load('reprint_reason.php?doc_no='+doc_no+'&c1=2');
		$('#div_dialog').dialog({
			title: 'พิมพ์สำเนาเอกสาร: '+doc_no,
			resizable: false,
			modal: true,  
			width: 450,
			height: 250,
			close: function(ev, ui){
				$('#div_dialog').remove();
			}
		});
	}

		// แสดง Modal Form เหตุผลของการ reprint
	function show3_dialog(doc_no){
		$('body').append('<div id="div_dialog"></div>');
		$('#div_dialog').load('reprint_reason.php?doc_no='+doc_no+'&c1=3');
		$('#div_dialog').dialog({
			title: 'พิมพ์สำเนาเอกสาร: '+doc_no,
			resizable: false,
			modal: true,  
			width: 450,
			height: 250,
			close: function(ev, ui){
				$('#div_dialog').remove();
			}
		});
	}*/
	
</script>

</body>
</html>