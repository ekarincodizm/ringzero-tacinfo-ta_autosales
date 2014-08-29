<?php
include_once("../include/config.php");
include_once("../include/function.php");

if(!CheckAuth()){
    header("Refresh: 0; url=../index.php");
    exit();
}

$page_title = "สมุดบัญชีทั้งหมด";
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

<div style="margin-top:10px">
<b>สมุดรายวันทั่วไป</b>
<form action="frm_list_accbook.php" method="post" style="font-family:Tahoma; font-size:small;">
เลือกรายการ
<select name="select_book">
	<option value="ALL">รายการทั้งหมด</option>
	<option value="GJ">รายการบันทึกด้วยมือ</option>
	<option value="AJ">รายการปรับปรุง</option>
	<option value="AP">รายการออโต้โพส [auto post]</option>
	<option value="AP-RE">รายการจัดใหม่ / รถยึด</option   >
	<option value="AP-BR">รายการรายวันรับเงิน</option   >
	<option value="AP-PSL">รายการส่วนลดจ่าย</option>
	<option value="AP-BSAL">รายการจากรายวันขาย</option>
	<option value="AP-VATS">รายการภาษีขาย</option>
	<option value="AP-VATB">รายการภาษีซื้อ</option>
</select>
เดือน
<select name="month_select">
<?php
$a = 0;
$nowmonth = date("m");
$month = array('มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน', 'กรกฏาคม', 'สิงหาคม' ,'กันยายน' ,'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม');
for($i=0; $i<12; $i++){
    $a+=1;
    if($a > 0 AND $a <10) $a = "0".$a;
    if($nowmonth != $a){
        echo "<option value=\"$a\">$month[$i]</option>";
    }else{
        echo "<option value=\"$a\" selected>$month[$i]</option>";
    }
    
}
?>    
</select>
เลือกปี
<select name="year_select">
      <option value="<?php echo date("Y"); ?>"><?php echo date("Y"); ?></option>
      <option value="<?php echo date("Y")-1; ?>"><?php echo date("Y")-1; ?></option>
      <option value="<?php echo date("Y")-2; ?>"><?php echo date("Y")-2; ?></option>
</select>
<input type="submit" value="NEXT">
</form>
</div>

<div style="margin-top:10px">
<b>สมุดบัญชีแยกประเภท</b>
<form name="frm_acid" method="post" action="frm_list_acidbook.php">
เลือกเลขที่บัญชี</td>
<select name="acid_id">
<?php
$qry_acid=pg_query("select * from account.\"AcTable\" order by \"AcID\" ");
while($res_acid=pg_fetch_array($qry_acid)){
	$ac_id=$res_acid["AcID"];
	$ac_name=$res_acid["AcName"];
	echo "<option value=\"$ac_id\">[ ".$ac_id." ] - ".$ac_name."</option>";
}
?>
</select>
เดือน
<select name="se2_month">
<?php
$a = 0;
$nowmonth = date("m");
$month = array('มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน', 'กรกฏาคม', 'สิงหาคม' ,'กันยายน' ,'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม');
for($i=0; $i<12; $i++){
    $a+=1;
    if($a > 0 AND $a <10) $a = "0".$a;
    if($nowmonth != $a){
        echo "<option value=\"$a\">$month[$i]</option>";
    }else{
        echo "<option value=\"$a\" selected>$month[$i]</option>";
    }
    
}
?>    
</select>
เลือกปี
<select name="se2_year">
	<option value="<?php echo date("Y"); ?>"><?php echo date("Y"); ?></option>
	<option value="<?php echo date("Y")-1; ?>"><?php echo date("Y")-1; ?></option>
	<option value="<?php echo date("Y")-2; ?>"><?php echo date("Y")-2; ?></option>
</select>
<input name="submit" type="submit" value="NEXT">
</form>
</div>

<div style="margin-top:10px">
<b>สมุดเงินสดรับจ่าย</b>
<br>
<iframe width="100%" height="280" src="frm_table.php" frameborder="0"></iframe>
</div>

      </div>
   <div class="roundedcornr_bottom"><div></div></div>
</div>

</body>
</html>