<?php
include_once("../include/config.php");
include_once("../include/function.php");

$auto_id = pg_escape_string($_GET['auto_id']);

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
	<div>
		<table cellpadding="1" cellspacing="10" align="center">
			<tr align="center">
				<td><b>หมายเหตุ :</b></td>
			</tr>
			<tr align="center">
				<td><textarea id="remark" name="remark" cols="40" rows="5"></textarea></td>
			</tr>
			<tr align="center">
				<td><input type="button" name="appv" id="appv" value="อนุมัติ"> <input type="button" name="notappv" id="notappv" value="ไม่อนุมัติ"></td>
			</tr>
		</table>
	</div>
	
<script>
$('#appv').click(function(){
	if($('#remark').val() == ""){
		alert('กรุณาระบุหมายเหตุด้วย');
		return false;
	}
	$.post('process_appv_carmove.php',{
		cmd : 'appv',
		remark : $('#remark').val(),
		auto_id : '<?php echo $auto_id;?>'
	},function(data){
		if(data == 1){
			alert('บันทึกข้อมูลเรียบร้อยแล้ว');
			location.reload();
		}else{
			alert(data);
			location.reload();
		}
	});
});
$('#notappv').click(function(){
	if($('#remark').val() == ""){
		alert('กรุณาระบุหมายเหตุด้วย');
		return false;
	}
	$.post('process_appv_carmove.php',{
		cmd : 'notappv',
		remark : $('#remark').val(),
		auto_id : '<?php echo $auto_id;?>'
	},function(data){
		if(data == 1){
			alert('บันทึกข้อมูลเรียบร้อยแล้ว');
			location.reload();
		}else{
			alert(data);
			location.reload();
		}
	});
});
</script>
<body>
</html>