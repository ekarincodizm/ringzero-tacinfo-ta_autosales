<?php
include_once("../include/config.php");
include_once("../include/function.php");

if(!CheckAuth()){
    header("Refresh: 0; url=../index.php");
    exit();
}

$page_title = "พิมพ์ใบสำคัญจ่าย";
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

<form name="frm_ppay" action="list_print_pay.php" method="post">
 หมวดที่จะพิมพ์
 <select name="s_type">
	<option value="GJ">GJ</option>
	<option value="AJ">AJ</option>
</select>

เลือกปี
<select name="s_year">
	<option value="<?php echo date("Y"); ?>"><?php echo date("Y"); ?></option>
	<option value="<?php echo date("Y")-1; ?>"><?php echo date("Y")-1; ?></option>
	<option value="<?php echo date("Y")-2; ?>"><?php echo date("Y")-2; ?></option>
</select>

เลือกรายการ
<select name="s_mode">
	<option value="ALL">ทั้งหมด</option>
	<option value="MONTH">เลือกเดือน</option>
	<option value="ID">เลือกพิมพ์เฉพาะรายการ</option>
</select>

<input type="submit" value="NEXT">

<br/>

<div align="left"><a href="#" onclick="javascript:window.close();">close [x]</a></div>

</form>
 
       </div>
   <div class="roundedcornr_bottom"><div></div></div>
</div>

</body>
</html>