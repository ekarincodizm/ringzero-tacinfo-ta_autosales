<?php
include_once("../include/config.php");
include_once("../include/function.php");

if(!CheckAuth()){
    header("Refresh: 0; url=../index.php");
    exit();
}

$page_title = "ลูกค้าที่ซื้อสินค้า";
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
<b>ค้นชื่อในฐานข้อมุล :</b> <input type="text" name="txt_name" id="txt_name" style="width:350px" onkeyup="javascript:CheckNaN()">
</div>

<div id="divshow"></div>

      </div>
   <div class="roundedcornr_bottom"><div></div></div>
</div>

<script>
function CheckNaN(){
    if( $('#txt_name').val() == '' ){
        $('#divshow').empty();
        $('#divshow').hide('fast');
    }
}

$("#txt_name").autocomplete({
    source: "regular_customer_api.php?cmd=autocomplete",
    minLength:1,
    select: function(event, ui) {
        if(ui.item.value == 'ไม่พบข้อมูลเก่า - เพิ่มข้อมูลใหม่'){
            $('#divshow').show('fast');
            $('#divshow').load('regular_customer_api.php?cmd=new');
        }else{
            var str_plan = ui.item.value;
            var arr_plan = str_plan.split("#");
            console.log(arr_plan[0]);
            $('#divshow').show('fast');
            $('#divshow').html('<img src="../images/progress.gif" border="0" width="32" height="32" alt="Please Wait">');
            $('#divshow').load('regular_customer_api.php?cmd=show&id='+arr_plan[0]);
        }
    }
});
</script>

</body>
</html>