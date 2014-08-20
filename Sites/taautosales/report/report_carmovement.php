<?php
include_once("../include/config.php");
include_once("../include/function.php");

if(!CheckAuth()){
    header("Refresh: 0; url=../index.php");
    exit();
}

$page_title = "รายงานรถเข้าออก";
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

<div style="margin:5px 0 5px 0">
เลือกวันที่ <input type="text" name="txt_date" id="txt_date" value="<?php echo $nowdate; ?>" style="width:80px; text-align:center">
<input type="button" name="btnShow" id="btnShow" value="ค้นหา">
</div>

<div id="divshowreport"></div>

      </div>
   <div class="roundedcornr_bottom"><div></div></div>
</div>

<script type="text/javascript">
$(document).ready(function(){
    $("#txt_date").datepicker({
        showOn: 'button',
        buttonImage: '../images/calendar.gif',
        buttonImageOnly: true,
        changeMonth: true,
        changeYear: true,
        dateFormat: 'yy-mm-dd'
    });
    
    $('#btnShow').click(function(){
        $('#divshowreport').empty();
        $('#divshowreport').html('<img src="../images/progress.gif" border="0" width="32" height="32" alt="Please Wait">');
        //$('#divshowreport').load('report_carmovement_api.php?cmd=show&date='+$("#txt_date").val());
		$('#divshowreport').load('report_carmovement_api.php?cmd=show&date='+$("#txt_date").val());
    });
});
</script>

</body>
</html>