<?php
include_once("../include/config.php");
include_once("../include/function.php");
$res_id = pg_escape_string($_GET['res_id']);
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
				<td><input type="button" id="appv" value=" ยืนยันยกเลิก " onclick="javascript:Cancel('<?php echo $res_id;?>')"></td>
			</tr>
		</table>
	</div>
	
<script>
function Cancel(id){
	var remark = $('#remark').val();
	
	if(remark == ""){
		alert('กรุณาระบุเหตุผล!')
		return false;
	}
	
    $.post('reserv_car_edit_api.php',{
        cmd: 'cancel',
        id: id,
		txtremark: remark
    },
    function(data){
        if(data.success){
            alert(data.message);
            location.reload();
        }else{
            alert(data.message);
        }
    },'json');
}
</script>
<body>
</html>