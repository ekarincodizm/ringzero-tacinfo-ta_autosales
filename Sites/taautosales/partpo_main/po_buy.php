<?php
include_once("../include/config.php");
include_once("../include/function.php");

if(!CheckAuth()){
    header("Refresh: 0; url=../index.php");
    exit();
}

$page_title = "สั่งซื้ออะไหล่/อุปกรณ์";
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

<div class="roundedcornr_box" style="width:1000px">
   <div class="roundedcornr_top"><div></div></div>
      <div class="roundedcornr_content">

<?php
include_once("../include/header_popup.php");

// include_once("po_buy_api_old.php");
// include_once("po_buy_api_all.php");
// include_once("po_buy_api1.php");
// include_once("po_buy_api2.php");
// include_once("po_buy_api3.php");
// include_once("po_buy_api4.php");


?>

<div id="maintabs">
    <ul>
        <li><a href="../partpo/po_buy_api_all.php">สั่งซื้ออะไหล่/อุปกรณ์</a></li>
        <li><a href="po_buy_api.php?cmd=div_other">สั่งซื้ออุปกรณ์อื่น</a></li>
		<li><a href="po_buy_api.php?cmd=div_usedcar">สั่งซื้อรถยนต์มือสอง</a></li>
    </ul>
</div>

      </div>
   <div class="roundedcornr_bottom"><div></div></div>
</div>

<script>
$(function(){
    $( "#maintabs" ).tabs({
        select: function(e, ui) {
            $('#ui-tabs-1').empty();
            $('#ui-tabs-2').empty();
        },
        ajaxOptions: {
            error: function( xhr, status, index, anchor ) {
                $( anchor.hash ).html("ไม่สามารถโหลดเนื้อหาได้");
            }
        }
    });
});
</script>

?>

</body>
</html>