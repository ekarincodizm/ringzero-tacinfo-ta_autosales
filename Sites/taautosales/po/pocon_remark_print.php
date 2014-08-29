<?php
/*include_once("../include/config.php");
include_once("../include/function.php"); */
$car_idno = pg_escape_string($_GET['car_idno']);
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
				<td><input type="button" name="appv" id="appv" value="บันทึก"> </td>
			</tr>
		</table>
	</div>
	
<script>
$('#appv').click(function(){
	if($('#remark').val() == ""){
		alert('กรุณาระบุหมายเหตุด้วย');
		return false;
	}
	$.post('process_printcon_log.php',{
		remark : $('#remark').val(),
		car_idno : '<?php echo $car_idno; ?>'
	},function(data){
		if(data == 1){
			alert('บันทึกข้อมูลเรียบร้อยแล้ว');
			ShowPrintCon('<?php echo $car_idno; ?>')
		}else{
			alert(data);
			location.reload();
		}
	});
});
function ShowPrintCon(id){
	var pdf_form = 'report_receive.php';
	$('body').append('<div id="divdialogprint"></div>');
	$('#divdialogprint').html("<div style=\"text-align:center\"><br /><br /><input type=\"button\" name=\"btnPrint\" id=\"btnPrint\" value=\"พิมพ์เอกสาร\" onclick=\"javascript:window.open('../po/"+pdf_form+"?car_idno="+ id +"','po_id4343423','toolbar=no,menubar=no,resizable=yes,scrollbars=yes,status=no,location=no,width=800,height=600'); javascript:location.reload();\"></div>");
    $('#divdialogprint').dialog({
        title: 'พิมพ์รายงาน : '+id,
        resizable: false,
        modal: true,  
        width: 300,
        height: 200,
        close: function(ev, ui){
            $('#divdialogprint').remove();
			location.reload();
		}
    });
}
</script>
<body>
</html>