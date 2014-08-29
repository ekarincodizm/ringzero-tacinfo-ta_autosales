<?php
include_once("../include/config.php");
include_once("../include/function.php");

if(!CheckAuth()){
    header("Refresh: 0; url=../index.php");
    exit();
}

$page_title = "Voucher - รับเข้า";
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

          
<div id="tabs">
    <ul>
        <li><a href="#tabs-1">อนุมัติแล้วแต่ยังไม่จบทั้งหมด</a></li>
        <li><a href="#tabs-2">ค้นหาตามวันที่</a></li>
    </ul>
    <div id="tabs-1" style="padding:10px 5px 10px 5px;">
<div id="divShow"></div>
    </div>
    <div id="tabs-2" style="padding:10px 5px 10px 5px;">
    <b>แสดงวันที่</b>&nbsp;<input type="text" id="datepicker" name="datepicker" value="<?php echo $nowdate; ?>" size="12">&nbsp;<input type="submit" name="btnshow" id="btnshow" value="แสดง">
    <div id="divDateShow"></div>
    </div>
</div>
          
        </div>
   <div class="roundedcornr_bottom"><div></div></div>
</div>
          
<script type="text/javascript">
$(document).ready(function(){
    $("#tabs").tabs();
    
    $('#divShow').html('<img src="../images/progress.gif" border="0" width="32" height="32" alt="กำลังโหลด...">');
    $('#divShow').load('voucher_rub_api.php?type=1');

    $("#datepicker").datepicker({
        showOn: 'button',
        buttonImage: '../images/calendar.gif',
        buttonImageOnly: true,
        changeMonth: true,
        changeYear: true,
        dateFormat: 'yy-mm-dd'
    });

    $('#btnshow').click(function(){
        $('#divDateShow').empty();
        $('#divDateShow').html('<img src="../images/progress.gif" border="0" width="32" height="32" alt="กำลังโหลด...">');
        $('#divDateShow').load('voucher_rub_api.php?date='+ $('#datepicker').val());
    });
});

function editfill(id){
    $('body').append('<div id="dialogedit"></div>');
    $('#dialogedit').load('voucher_rub_in.php?cmd=showdivdialog&type=1&id='+id);
    $('#dialogedit').dialog({
        title: 'รายการ '+id,
        resizable: false,
        modal: true,  
        width: 600,
        height: 400,
        close: function(ev, ui){
            $('#dialogedit').remove();
        }
    });
}
</script>
          
</body>
</html>