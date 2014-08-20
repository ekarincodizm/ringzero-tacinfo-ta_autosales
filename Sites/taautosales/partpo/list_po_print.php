<?php
include_once("../include/config.php");
include_once("../include/function.php");

$page_title = "อนุมัติส่งมอบรถ ";
$po_id = $_GET['po_id'];

	if($po_id != ""){
		$qry_list = "select * from \"PurchaseOrders\" where po_id='$po_id' and cancel = false order by approve_date ASC";
	}else{
		$qry_list = "select * from \"PurchaseOrders\" where cancel = false order by approve_date ASC";
	}
?>
<table cellpadding="3" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
<tr bgcolor="#D0D0D0" style="font-weight:bold; text-align:center">
    <td>ลำดับที่</td>
	<td>เลขที่ใบสั่งซื้อ</td>
    <td>วันที่สั่งซื้อ</td>
	<td>ผู้ขาย</td>
    <td>ผู้ทำรายการ</td>
    <td></td>
</tr>

<?php
$j = 0;
$qry = pg_query($qry_list);

while($res = pg_fetch_array($qry)){
    $j++;
    $po_id = $res['po_id'];
    $po_date = $res['po_date'];
    $vender_id = $res['vender_id'];
	$po_type_id = $res['po_type_id'];
		$vender_name = getCusNameFromVender($vender_id,$po_type_id);
    $user_id = $res['user_id'];
		$doer_name = GetUserName($user_id);
    
	if($po_type_id == "POMA"){
		$type = "M";
	}else{
		$type = "C";
	}
  
?>
<tr bgcolor="#E1F0FF" style="font-weight:bold">
	<td><?php echo $j; ?></td>
	<td><?php echo $po_id; ?></td>
    <td><?php echo $po_date; ?></td>
    <td><?php echo $vender_name; ?></td>
    <td><?php echo $doer_name; ?></td>
    <td align="center"><img src="../images/print.png" onclick="javascript:Save_print_log('<?php echo $po_id;?>','<?php echo $type; ?>')" style="cursor:pointer;"/></td>
</tr>

<?php
}

if($j == 0){
    echo "<tr><td colspan=8 align=center>- ไม่พบข้อมูล -</td></tr>";
}
?>
</table>
<script>
function Save_print_log(id,type){
	$('body').append('<div id="divdialogshow"></div>');
    $('#divdialogshow').load('po_remark_print.php?po_id='+id+'&type='+type);
    $('#divdialogshow').dialog({
        title: 'แสดงรายละเอียด',
        resizable: false,
        modal: true,  
        width:400,
        height: 300,
        close: function(ev, ui){
            $('#divdialogshow').remove();
        }
    });
}
</script>
