<?php
include_once ("../include/config.php");
include_once ("../include/function.php");
$page_title = "เพิ่มรายการอะไหล่/อุปกรณ์";
//Title Bar Name
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
	
<div style="text-align:center">บันทึกเรียบร้อยแล้ว<br /><br /><input type="button" name="btnPrint" id="btnPrint" value="พิมพ์เอกสาร" onclick=" _close_dialog(); "></div>

</body>
</html>

<script>
	function _close_dialog(){
		$('#divdialog_new_print').dialog("close");
	}
</script>