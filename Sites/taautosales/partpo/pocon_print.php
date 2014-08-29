<?php
/*include_once("../include/config.php");
include_once("../include/function.php");
if(!CheckAuth()){
    header("Refresh: 0; url=../index.php");
    exit();
}*/
$page_title = "พิมพ์สัญญา ซื้อรถยนต์มือสอง";
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
			<b>ค้นหาทะเบียนรถ:</b> <input type="text" name="txt_name" id="txt_name" style="width:300px" onkeyup="javascript:CheckCon()">
			<input type="button" name="search" id="search" value="ค้นหา" />
		</div>
		<div id="div_list" style="margin-top:10px"></div>
			
		</div>
		
		 <div class="roundedcornr_bottom"><div></div></div>
	</div>
	
<script>
$(document).ready(function(){
	 $('#div_list').load('list_pocon_print.php');
});
function CheckCon(){
    if( $('#txt_name').val() == '' ){
        $('#divshow').empty();
        $('#divshow').hide('fast');
    }
}
$("#txt_name").autocomplete({
    source: "po_autocomplete.php?cmd=pocon_print",
    minLength:1
});
$("#search").click(function(){
	if($('#txt_name').val() == ""){
		alert("กรุณาระบุทะเบียนรถ ");
	}else{
		//var txt_con = $('#txt_name').val();
		//alert(txt_con );
		$('#div_list').load('list_pocon_print.php?license_plate='+$('#txt_name').val());
	}
});
</script>
</body>
</html>