<?php
/*include_once("../include/config.php");
include_once("../include/function.php");
if(!CheckAuth()){
    header("Refresh: 0; url=../index.php");
    exit();
}*/
$page_title = "รายงาน PO";
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

<div style="margin-top:5px">
<b>เลือกการค้นหา : </b>
<select name="cb_type" id="cb_type">
    <option value="">แสดงทั้งหมด</option>
    <option value="1">วันที่ออก PO</option>
    <option value="2">สถานะ PO</option>
    <option value="3">สถานะของส่ง</option>
    <option value="4">สถานะการชำระ</option>
</select>

<span id="span_where" style="display:none"></span>

<input type="button" name="btnShow" id="btnShow" value="ค้นหา">
</div>

<div id="divshowreport" style="margin-top:5px"></div>

      </div>
   <div class="roundedcornr_bottom"><div></div></div>
</div>

<script type="text/javascript">
$("#cb_type").change(function(){
    
    $('#divshowreport').empty();
    $('#divshowreport').hide('fast');
    
    if($("#cb_type").val() != ""){
        $('#span_where').load('report_po_api.php?cmd=changetype&type='+$('#cb_type').val());
        $('#span_where').show('fast');
    }else{
        $('#span_where').empty();
        $('#span_where').hide('fast');
    }
});

$('#btnShow').click(function(){
    $('#divshowreport').empty();
    $('#divshowreport').show();
    $('#divshowreport').html('<img src="../images/progress.gif" border="0" width="32" height="32" alt="Please Wait">');
    
    if($("#cb_type").val() == "1"){
        $('#divshowreport').load('report_po_api.php?cmd=report&type=1&date='+$('#txt_po_date').val());
    }else if($("#cb_type").val() == "2"){
        $('#divshowreport').load('report_po_api.php?cmd=report&type=2&status_po='+$('#cb_status_po').val());
    }else if($("#cb_type").val() == "3"){
        $('#divshowreport').load('report_po_api.php?cmd=report&type=3&receive_all='+$('#cb_receive_all').val());
    }else if($("#cb_type").val() == "4"){
        $('#divshowreport').load('report_po_api.php?cmd=report&type=4&pay='+$('#cb_pay').val());
    }else{
        $('#divshowreport').load('report_po_api.php?cmd=report&type=all');
    }
});
</script>

</body>
</html>