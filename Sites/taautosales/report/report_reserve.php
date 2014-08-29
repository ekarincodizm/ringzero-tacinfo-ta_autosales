<?php
/*include_once("../include/config.php");
include_once("../include/function.php");

if(!CheckAuth()){
    header("Refresh: 0; url=../index.php");
    exit();
}*/

$page_title = "รายงานการจองรถ";
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

<div style="margin-top:5px; margin-bottom:5px">
<b>เลือกรูปแบบ : </b>
<select name="cb_type" id="cb_type">
    <option value="">เลือก</option>
    <option value="1">ยอดจองเดือน/ปี</option>
    <option value="2">ค้นจากชื่อลูกค้า</option>
    <option value="3">ยี่ห้อรุ่นรถ</option>
    <option value="4">เฉพาะรถที่จองอยู่</option>
    <option value="5">ลูกค้ารับรถไปแล้ว</option>
	<option value="8">ลูกค้ารับรถแล้วแต่ค้างชำระ</option>
    <option value="7">ผู้รับจอง เดือน/ปี</option>
	<option value="9">ใบจองรอเปลี่ยนคัน</option>
	<option value="10">ใบจองที่ยกเลิก</option>
	<option value="11">รับรถแล้วเดือน/ปี</option>
    <option value="6">ทั้งหมด</option>
</select>
<span id="span_where" style="display:none"></span>
<input type="button" name="btnShow" id="btnShow" value="ค้นหา">
</div>

<div id="divshowreport"></div>

      </div>
   <div class="roundedcornr_bottom"><div></div></div>
</div>

<script type="text/javascript">
$("#cb_type").change(function(){
    $('#divshowreport').empty();
    $('#divshowreport').hide('fast');
    
    if($("#cb_type").val() != ""){
        $('#span_where').load('report_reserve_api.php?cmd=changetype&type='+$('#cb_type').val());
        $('#span_where').show('fast');
    }else{
        $('#span_where').empty();
        $('#span_where').hide('fast');
    }
});

$('#btnShow').click(function(){
    
    if($("#cb_type").val() == ""){
        alert('กรุณาเลือกรูปแบบ ก่อนค่ะ');
        return false;
    }
    
    $('#divshowreport').empty();
    $('#divshowreport').show();
    $('#divshowreport').html('<img src="../images/progress.gif" border="0" width="32" height="32" alt="Please Wait">');
    
    if($("#cb_type").val() == "1"){
        $('#divshowreport').load('report_reserve_api.php?cmd=report&type=1&cb_month='+$('#cb_month').val()+'&cb_year='+$('#cb_year').val());
    }else if($("#cb_type").val() == "2"){
        var str_name = $('#txt_name').val();
        var arr_name = str_name.split("#");
        $('#divshowreport').load('report_reserve_api.php?cmd=report&type=2&txt_name='+arr_name[0]);
    }else if($("#cb_type").val() == "3"){
        $('#divshowreport').load('report_reserve_api.php?cmd=report&type=3&cb_product='+$('#cb_product').val());
    }else if($("#cb_type").val() == "4"){
        $('#divshowreport').load('report_reserve_api.php?cmd=report&type=4');
    }else if($("#cb_type").val() == "5"){
        $('#divshowreport').load('report_reserve_api.php?cmd=report&type=5');
    }else if($("#cb_type").val() == "6"){
        $('#divshowreport').load('report_reserve_api.php?cmd=report&type=6');
    }else if($("#cb_type").val() == "7"){
        $('#divshowreport').load('report_reserve_api.php?cmd=report&type=7&cb_user_sale='+$('#cb_user_sale').val()+'&cb_month='+$('#cb_month').val()+'&cb_year='+$('#cb_year').val());
    }else if($("#cb_type").val() == "8"){
        $('#divshowreport').load('report_reserve_api.php?cmd=report&type=8');
    }else if($("#cb_type").val() == "9"){
        $('#divshowreport').load('report_reserve_api.php?cmd=report&type=9');
    }else if($("#cb_type").val() == "10"){
        $('#divshowreport').load('report_reserve_api.php?cmd=report&type=10');
    }else if($("#cb_type").val() == "11"){
         $('#divshowreport').load('report_reserve_api.php?cmd=report&type=11&cb_month='+$('#cb_month').val()+'&cb_year='+$('#cb_year').val());
	}else{
        $('#divshowreport').empty();
        alert('error type');
        return false;
    }
});
</script>

</body>
</html>