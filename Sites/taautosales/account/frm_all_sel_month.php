<?php
set_time_limit(0);

include_once("../include/config.php");
include_once("../include/function.php");

if(!CheckAuth()){
    exit();
}
?>
<?php
$page_title = "สมุดรายวันทั่วไป";
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

<form action="frm_all_month.php" method="post">
เลือกเดือน
<input type="hidden" name="f_year" value="<?php echo $_GET["i_year"]; ?>" />
<select name="f_mon">
	<option value="1">มกราคม</option>
	<option value="2">กุมพาพันธ์</option>
	<option value="3">มีนาึคม</option>
	<option value="4">เมษายน</option>
	<option value="5">พฤษภาคม</option>
	<option value="6">มิถุนายน</option>
	<option value="7">กรกฏาคม</option>
	<option value="8">สิงหาคม</option>
	<option value="9">กันยายน</option>
	<option value="10">ตุลาคม</option>
	<option value="11">พฤศจิกายน</option>
	<option value="12">ธันวาคม</option>
</select>
<input type="submit" value="NEXT">
</form>
	
      </div>
   <div class="roundedcornr_bottom"><div></div></div>
</div>

</body>
</html>