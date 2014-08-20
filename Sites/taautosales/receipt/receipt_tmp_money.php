<?php
include_once("../include/config.php");
include_once("../include/function.php");

if(!CheckAuth()){
    header("Refresh: 0; url=../index.php");
    exit();
}

$page_title = "รับเงินใบเสร็จชั่วคราว";
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
<b>ค้นหา ชื่อลูกค้า/ทะเบียนรถ :</b> <input type="text" name="txt_name" id="txt_name" size="60" onkeyup="javascript:CheckNaN()">    
</div>

<div id="div_show" style="margin-top:10px"></div>

      </div>
   <div class="roundedcornr_bottom"><div></div></div>
</div>

<script>
$(document).ready(function(){
    $("#txt_name").autocomplete({
        source: "receipt_tmp_money_api.php?cmd=autocomplete",
        minLength:1,
        select: function(event, ui) {
            if(ui.item.value == 'ไม่พบข้อมูลเก่า - เพิ่มข้อมูลใหม่'){
                $('#div_show').empty();
                $('#div_show').load('receipt_tmp_money_api.php?cmd=divshow&name=');
            }else{
                var str_value = ui.item.value;
                var arr_value = str_value.split("#");
                $('#div_show').empty();
                $('#div_show').load('receipt_tmp_money_api.php?cmd=divshow&name='+ encodeURIComponent(arr_value[0]) +'&license_plate='+ encodeURIComponent(arr_value[1]) +'&idno='+ encodeURIComponent(arr_value[2])+'&cus='+ encodeURIComponent(arr_value[3]));
            }
        }
    });
});

function CheckNaN(){
    if( $('#txt_name').val() == '' ){
        $('#div_show').empty();
    }
}
</script>

</body>
</html>