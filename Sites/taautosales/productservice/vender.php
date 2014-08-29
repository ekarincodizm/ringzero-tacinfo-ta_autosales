<?php
include_once("../include/config.php");
include_once("../include/function.php");

if(!CheckAuth()){
    header("Refresh: 0; url=../index.php");
    exit();
}

$page_title = "Vender";

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

<div style="text-align:right; margin:5px 0px 5px 0px">
<input type="button" name="btnAddVender" id="btnAddVender" value="เพิ่ม Vender" onclick="javascript:AddVender()">
</div>
          
<div>
<table cellpadding="3" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
<tr bgcolor="#D0D0D0" style="font-weight:bold; text-align:center">
    <td>#</td>
	<td>อักษรย่อ</td>	
    <td>ชื่อบริษัท/ร้าน</td>
	<td>สาขาที่</td>
    <td>ลักษณะการขายสินค้า</td>
    <td>&nbsp;</td>
</tr>
<?php
$j=0;
$qry = pg_query("SELECT * FROM \"VVenders\" ORDER BY alphas ASC ");
while($res = pg_fetch_array($qry)){
    $j++;
    $vender_id = $res['vender_id'];
    $cus_id = $res['cus_id'];
	$alphas = $res['alphas'];
    $cus_name = trim($res["cus_name"]);
	$surname = trim($res["surname"]);
    $type_ven = $res['type_ven'];
	$branch_id = $res['branch_id'];

	
    if($type_ven == "P"){
        $str_type = "สินค้าสำเร็จรูป";
    }elseif($type_ven == "M"){
        $str_type = "วัตถุดิบเริ่มต้น";
    }elseif($type_ven == "B"){
        $str_type = "ทั้ง 2 ประเภท";
    }
?>

<tr bgcolor="#FFFFFF">
    <td align="center"><?php echo $j."."; ?></td>
	<td><?php echo $alphas; ?></td>
    <td><?php echo $cus_name." ".$surname; ?></td>
	<td><?php echo $branch_id; ?></td>
    <td><?php echo $str_type; ?></td>
	<td align="center"><a href="javascript:EditVender('<?php echo $cus_id; ?>')"><u>แก้ไข</u></a></td>
</tr>
    
<?php
}
?>
</table>
</div>

      </div>
   <div class="roundedcornr_bottom"><div></div></div>
</div>

<script>
    
function AddVender(){
    $('body').append('<div id="div_dialog" style="margin:0px; padding:5px"></div>');
    $("#div_dialog").load('vender_api.php?cmd=addvender');
    $("#div_dialog").dialog({
        autoOpen: true,
        width: 800,
        height: 500,
        title: ' เพิ่ม Vender',
        modal: true,
        resizable: false,
        close: function(ev, ui){
            $("#div_dialog").remove();
        }
    });
}
    
function EditVender(id){
    $('body').append('<div id="div_dialog" style="margin:0px; padding:5px"></div>');
    $("#div_dialog").load('vender_api.php?cmd=editvender&id='+id);
    $("#div_dialog").dialog({
        autoOpen: true,
        width: 800,
        height: 500,
        title: ' แก้ไข Vender : '+id,
        modal: true,
        resizable: false,
        close: function(ev, ui){
            $("#div_dialog").remove();
        }
    });
}
</script>
    
</body>
</html>
