<?php
include_once("../include/config.php");
include_once("../include/function.php");

if(!CheckAuth()){
    header("Refresh: 0; url=../index.php");
    exit();
}

$page_title = "รายงานภาษีซื้อ";
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

<b>เลือกเดือน</b>
<select name="mm" id="mm">
<?php
$cur_month = date('m');
$thaimonth=array("มกราคม","กุมภาพันธ์","มีนาคม","เมษายน","พฤษภาคม ","มิถุนายน","กรกฎาคม","สิงหาคม","กันยายน","ตุลาคม","พฤศจิกายน ","ธันวาคม");
for($i=0; $i<=11; $i++){
    $m = $i+1;
    if($m > 0 && $m < 10){
        $m = "0".$m;
    }

    if($m == $cur_month)
        echo "<option value=\"$m\" selected>$thaimonth[$i]</option>";
    else
        echo "<option value=\"$m\">$thaimonth[$i]</option>";
}
?>
</select>

<b>ปี</b>
<select name="yy" id="yy">
<?php
$cur_year = date('Y');
for($a=($cur_year-3); $a<=($cur_year+3); $a++){
    if($a == $cur_year)
        echo "<option value=\"$a\" selected>$a</option>";
    else
        echo "<option value=\"$a\">$a</option>";
}
?>
</select>

<input type="button" id="btn00" value="เริ่มค้น"/></p>

</div>

<div id="panel"></div>

      </div>
   <div class="roundedcornr_bottom"><div></div></div>
</div>

<script type="text/javascript">
$(document).ready(function(){
    $('#btn00').click(function(){
        $("#btn00").attr('disabled', true);
        $("#panel").text('กำลังค้นหาข้อมูล ....');
        $("#panel").load("tax_buy_panel.php?yy="+ $("#yy").val() +"&mm="+ $("#mm").val() );
        $("#btn00").attr('disabled', false);
    });
});
</script>

</body>
</html>