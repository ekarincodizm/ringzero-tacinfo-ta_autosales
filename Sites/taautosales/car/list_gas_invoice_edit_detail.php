<?php
include_once("../include/config.php");
include_once("../include/function.php");

$page_title = "แก้ไข รายละเอียดติดตั้งแก๊ส";

$qry_list = "select \"gasInvoiceNo\", \"gasInvoiceDate\", \"wh_id\"
			from \"installGas\"
			order by \"gasInvoiceDate\" DESC, \"gasInvoiceNo\" ASC ";
?>
<table cellpadding="3" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
<tr bgcolor="#D0D0D0" style="font-weight:bold; text-align:center">
    <th>ลำดับที่</th>
	<th>วันที่ใบแจ้งหนี้/ใบส่งของ</th>
    <th>เลขที่ใบแจ้งหนี้/ใบส่งของ</th>
    <th>ชื่อร้านค้า</th>
    <th>แก้ไข</th>
</tr>

<?php
$j = 0;
$qry = pg_query($qry_list);

while($res = pg_fetch_array($qry))
{
    $j++;
    $gasInvoiceNo = $res['gasInvoiceNo'];
    $gasInvoiceDate = $res['gasInvoiceDate'];
	$wh_id = $res['wh_id'];
	
	// กำหนดชื่อบริษัท
	if($wh_id == "18")
	{
		$storeName = "บริษัท เอ็น.จี.วี. พลัส จำกัด";
	}
	elseif($wh_id == "19")
	{
		$storeName = "บริษัท สแกนอินเตอร์ จำกัด";
	}
	else
	{
		$storeName = "";
	}
?>
	<tr bgcolor="#E1F0FF" style="font-weight:bold">
		<td align="center"><?php echo $j; ?></td>
		<td align="center"><?php echo $gasInvoiceDate; ?></td>
		<td align="center"><?php echo $gasInvoiceNo; ?></td>
		<td align="left"><?php echo $storeName; ?></td>
		<td align="center"><img src="../images/detail.gif" onclick="javascript:ShowDetail('<?php echo $gasInvoiceNo;?>')" style="cursor:pointer;"/></td>
	</tr>
<?php
}

if($j == 0){
    echo "<tr><td colspan=6 align=center>- ไม่พบข้อมูล -</td></tr>";
}
?>
</table>
<script>
function ShowDetail(id){
    $('body').append('<div id="divdialogshow"></div>');
    $('#divdialogshow').load('popup_edit_detail_install_gas.php?gasInvoiceNo='+id);
    $('#divdialogshow').dialog({
        title: 'แสดงรายละเอียด',
        resizable: false,
        modal: true,  
        width:850,
        height: 600,
        close: function(ev, ui){
            $('#divdialogshow').remove();
        }
    });
}
</script>