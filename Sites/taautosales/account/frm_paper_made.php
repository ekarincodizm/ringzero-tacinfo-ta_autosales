<?php
include_once("../include/config.php");
include_once("../include/function.php");

if(!CheckAuth()){
    header("Refresh: 0; url=../index.php");
    exit();
}

$page_title = "กระดาษทำการ";
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

<div style="margin:5px">
<b>ปี</b>
<select name="yy" id="yy">
<?php
$nowyear = date("Y");
$year_a = $nowyear + 5; 
$year_b =  $nowyear - 5;

$s_b = $year_b+543;

while($year_b <= $year_a){
    if($nowyear != $year_b){
        echo "<option value=\"$year_b\">$s_b</option>";
    }else{
        echo "<option value=\"$year_b\" selected>$s_b</option>";
    }
    $year_b += 1;
    $s_b +=1;
} 
?>
</select>

<b>เลือกรายการแสดง</b>
<select name="ty" id="ty">
<option value="">เลือก</option>
<option value="1">แสดงทีละเดือน</option>
<!--<option value="2">แสดงไตรมาส</option>-->
<option value="3">แสดงทั้งปี</option>
</select>

<span id="mmshow">
<b>เดือน</b>
<select name="mm" id="mm">
<?php
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
</span>

<span id="trimas">
<b>ไตรมาส</b>
<select name="ctrimas" id="ctrimas">
<option value="1">มกราคม-มีนาคม</option>
<option value="2">เมษายน-มิถุนายน</option>
<option value="3">กรกฏาคม-กันยายน</option>
<option value="4">ตุลาคม-ธันวาคม</option>   
</select>
</span>

<input type="button" name="btn1" id="btn1" value="ค้นหา">
</div>

<div id="panel" style="margin:5px"></div>

      </div>
   <div class="roundedcornr_bottom"><div></div></div>
</div>

<script type="text/javascript">
$(function(){
    
    $('#mmshow').hide();
    $('#trimas').hide();
    
    $('#btn1').click(function(){
        $('#panel').empty();
        $('#panel').html('<img src="../images/progress.gif" border="0" width="32" height="32" alt="กำลังโหลด...">');
        $("#panel").load("frm_paper_made_show.php?yy="+ $("#yy").val() +"&ty="+ $("#ty").val() +"&mm="+ $("#mm").val() +"&trimas="+ $("#ctrimas").val());
    });

    $('#ty').change(function(){
        if($('#ty').val() == 1){
            $('#mmshow').show();
            $('#trimas').hide();
        }else if($('#ty').val() == 2){
            $('#mmshow').hide();
            $('#trimas').show();
        }else{
            $('#mmshow').hide();
            $('#trimas').hide();
        }
    });
    
});
</script>

</body>
</html>