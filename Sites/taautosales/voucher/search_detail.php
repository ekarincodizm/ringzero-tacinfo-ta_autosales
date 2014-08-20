<?php
include_once("../include/config.php");
include_once("../include/function.php");

if(!CheckAuth()){
    header("Refresh: 0; url=../index.php");
    exit();
}

$page_title = "Voucher - ค้นรายการชำระ";
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

<style type="text/css">
.btline{
    font-weight: bold;
    border-style: dashed; border-width: 1px; border-color:#000000
}
</style>
    
</head>
<body>

<div class="roundedcornr_box" style="width:900px">
   <div class="roundedcornr_top"><div></div></div>
      <div class="roundedcornr_content">

<?php
include_once("../include/header_popup.php");
?>

<div>
<b>รายการที่ต้องการค้น</b>
<input type="text" name="txt_search" id="txt_search" size="50">
<input type="button" name="btn_search" id="btn_search" value="แสดง">
</div>

<div id="div_search" style="margin-top:5px"></div>
          
        </div>
   <div class="roundedcornr_bottom"><div></div></div>
</div>

<script>
$('#btn_search').click(function(){
    $('#div_search').empty();
    
    $.post('search_detail_api.php',{
        txt_search: $('#txt_search').val()
    },
    function(data){
        $('#div_search').html(data);
    },'html');
});

function showdetail(id){
    $('body').append('<div id="dialogedit"></div>');
    $('#dialogedit').html('<img src="../images/progress.gif" border="0" width="32" height="32" alt="กำลังโหลด...">');
    $('#dialogedit').load('voucher_acc_api.php?cmd=div_detail&id='+id);
    $('#dialogedit').dialog({
        title: 'แสดงบัญชี Abh ID : '+id,
        resizable: false,
        modal: false,  
        width: 600,
        height: 200,
        close: function(ev, ui){
            $('#dialogedit').remove();
        }
    });
}
</script>
    
</body>
</html>