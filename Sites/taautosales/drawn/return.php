<?php
include_once("../include/config.php");
include_once("../include/function.php");

if(!CheckAuth()){
    header("Refresh: 0; url=../index.php");
    exit();
}

$page_title = "รับคืนสินค้า";
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

<div id="maintabs">
    <ul>
        <li><a href="return_api.php?cmd=div_1">รับคืนเข้า stock</a></li>
        <li><a href="return_api.php?cmd=div_2">รับคืนเป็นของเสีย</a></li>
        <li><a href="return_api.php?cmd=div_3">รับคืนเป็น finish goods</a></li>
        <li><a href="return_api.php?cmd=div_4">รับสินค้าจาก คลัง(สาขา)อื่น</a></li>
    </ul>
</div>

      </div>
   <div class="roundedcornr_bottom"><div></div></div>
</div>
    
<script>
$(function(){
    $( "#maintabs" ).tabs({
        ajaxOptions: {
            error: function( xhr, status, index, anchor ) {
                $( anchor.hash ).html("ไม่สามารถโหลดเนื้อหาได้");
            }
        }
    });    
});
</script>
          
</body>
</html>