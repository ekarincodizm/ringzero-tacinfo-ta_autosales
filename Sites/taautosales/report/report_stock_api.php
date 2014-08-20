<?php
include_once("../include/config.php");
include_once("../include/function.php");

$cmd = $_REQUEST['cmd'];

if($cmd == "divshowdetail"){
    $id = $_GET['id'];
    
    if(substr($id, 0,1) == "P" )
        $product_name = GetProductName($id);
    else
        $product_name = GetRawMaterialName($id);
?>

<div style="float:left; margin-top:5px; margin-bottom:5px; font-weight:bold">Product Name : <?php echo $product_name; ?></div>
<div style="float:right"><a href="report_stock_pdf.php?mode=detail&id=<?php echo $id; ?>" target="_blank"><span style="font-weight:bold; font-size:13px; color:#006AD5"><img src="../images/print.png" border="0" width="16" height="16"> Print PDF</span></a></div>
<div style="clear:both"></div>

<table cellpadding="3" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
<tr bgcolor="#4682B4" style="font-weight:bold; text-align:center; color:#FFFFFF">
    <td>วันที่</td>
    <td>รหัสอ้างอิง</td>
    <td>ประเภท</td>
    <td>จำนวน</td>
    <td>คงเหลือ</td>
</tr>
<?php
    $sum = 0;
    $j = 0;
    $qry = pg_query("SELECT * FROM \"StockMovement\" WHERE product_id='$id' ORDER BY auto_id ASC");
    while( $res = pg_fetch_array($qry) ){
        $j++;
        $amount = $res['amount'];
        $type_inout = $res['type_inout'];
        $date_inout = $res['date_inout'];
        $ref_1 = $res['ref_1'];
        
        if($type_inout == "I"){
            $bgcolor = "#F0FFF0";
            $str_inout = "เข้า";
            $sum += $amount;
        }else{
            $bgcolor = "#FFF0F5";
            $str_inout = "ออก";
            $sum += ($amount);
        }
?>
<tr bgcolor="<?php echo $bgcolor; ?>">
    <td align="center"><?php echo $date_inout; ?></td>
    <td><?php echo $ref_1; ?></td>
    <td><?php echo $str_inout; ?></td>
    <td align="right"><?php echo number_format($amount,0); ?></td>
    <td align="right"><?php echo number_format($sum,0); ?></td>
</tr>
<?php
    }
    
    if($j == 0){
        echo "<tr><td colspan=\"5\" align=\"center\">- ไม่พบข้อมูล -</td></tr>";
    }
?>
</table>
<?php
}

elseif($cmd == "divshow"){
?>

<div style="margin:5px 0 2px 0; text-align:right">
<a href="report_stock_pdf.php?mode=full&type=1" target="_blank"><span style="font-weight:bold; font-size:13px; color:#006AD5"><img src="../images/print.png" border="0" width="16" height="16"> พิมพ์รายงานสรุป</span></a> |
<a href="report_stock_pdf.php?mode=full&type=2" target="_blank"><span style="font-weight:bold; font-size:13px; color:#006AD5"><img src="../images/print.png" border="0" width="16" height="16"> พิมพ์รายงานรายละเอียด</span></a>
</div>

<table cellpadding="3" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
<tr bgcolor="#4682B4" style="font-weight:bold; text-align:center; color:#FFFFFF">
    <td>ID</td>
    <td>Name</td>
    <td>ยอดคงเหลือ</td>
</tr>
<?php
    $j=0;
    $qry = pg_query("SELECT * FROM \"RawMaterialProduct\" ORDER BY product_id ASC");
    while( $res = pg_fetch_array($qry) ){
        $j++;
        $material_id = $res['product_id'];
        $name = $res['name'];
        
        if($j%2 == 0){
            echo "<tr class=\"old\">";
        }else{
            echo "<tr class=\"even\">";
        }
?>
    <td align="center"><a href="javascript:ShowDetail('<?php echo $material_id; ?>')"><u><?php echo $material_id; ?></u></a></td>
    <td><?php echo $name; ?></td>
    <td align="right"><?php echo number_format(GetAmountRawMaterial($material_id),0); ?></td>
</tr>
<?php
    }
?>

</table>

<script>
function ShowDetail(id){
    $('body').append('<div id="divdetail"></div>');
    $('#divdetail').load('report_stock_api.php?cmd=divshowdetail&id='+id);
    $('#divdetail').dialog({
        title: 'แสดงรายละเอียด : '+id,
        resizable: false,
        modal: true,  
        width: 550,
        height: 500,
        close: function(ev, ui){
            $('#divdetail').remove();
        }
    });
}
</script>
<?php
}
?>