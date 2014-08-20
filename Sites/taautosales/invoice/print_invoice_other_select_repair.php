<?php
/* include_once("../include/config.php");
include_once("../include/function.php"); */
?>
<label><b>กรุณาระบุทะเบียนรถ (กด Enter เพื่อดูทั้งหมด) : </b><label>
<input type="text" name="txt_search" id="txt_search" size="60" onkeyup="javascript:CheckCarRepair()">

<br><br>

<div id="dev_edit"> </div>
<div id ="div_show_data"></div>
<br><br><br>
<label><b>คำอธิบาย</b></label>
<hr/>
<div id = "div_img">
	<img src="../images/viewdetails.gif" border="0" width="15" height="15" title="ทำรายการ" /> <label>&nbsp;คือ  ทำรายการ</label>&nbsp;&nbsp;	
<div>

<script>

function CheckCarRepair(){
		$('#div_show_data').empty();
		$('#div_show_data').load('../invoice/list_car_repair.php?keyword='+$('#txt_search').val());

}
//================ ค้นหารถฝากจอดซ่อม =====================//
$(document).ready(function(){
	$('#div_show_data').empty();
	//$('#div_show_data').load('../invoice/list_car_repair.php?keyword='+$('#txt_search').val());

    $("#txt_search").autocomplete({
        source: "../car/car_autocomplete.php?cmd=repair",
        minLength:1,
    });
	
});


function show_rpdialog(res_id){
	    window.open('../invoice/print_invoice_other.php?resid='+res_id,'sdf7fd8s7fs789','toolbar=no,menubar=no,resizable=yes,scrollbars=yes,status=no,location=no,width=950,height=600');
	}
	  
</script>