<?php
include_once("../include/config.php");
include_once("../include/function.php");

if(!CheckAuth()){
    header("Refresh: 0; url=../index.php");
    exit();
}

$page_title = "พิมพ์ใบเบิกสินค้า";
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
<b>เลขที่ใบเบิก :</b> <input type="text" name="txt_name" id="txt_name" size="40">    
</div>

      </div>
   <div class="roundedcornr_bottom"><div></div></div>
</div>

<script>
$(document).ready(function(){
    $("#txt_name").autocomplete({
        source: "withdrawal_reprint_api.php?cmd=autocomplete",
        minLength:1,
        select: function(event, ui){
            var str_value = ui.item.value;
            var arr_value = str_value.split("#");
            document.location='../report/withdrawal_pdf.php?id='+ arr_value[0];
        }
    });
});
</script>

</body>
</html>