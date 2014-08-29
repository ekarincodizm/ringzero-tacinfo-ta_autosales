<?php
include_once("../include/config.php");
include_once("../include/function.php");
?>

<table cellpadding="5" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
<tr bgcolor="#D0D0D0" style="font-weight:bold; text-align:center">
    <th>ลำดับ</th>
    <th>เลขที่ใบแจ้งหนี้</th>
	<th>มูลค่า</th>
	<th>vat</th>
	<th>ยอดรวม</th>
    <th>ผู้ทำรายการยกเลิก</th>
	<th>วันเวลาที่ทำรายการยกเลิก</th>
</tr>
<?php

$j = 0;
$qry = pg_query("SELECT * FROM \"Invoices_account_cancel\" WHERE \"appvStatus\" = '9' ORDER BY \"doerNote\" ");
while($res = pg_fetch_array($qry))
{
    $j++;
    $cancelID = $res['cancelID']; // รหัสการขอยกเลิก
    $inv_no = $res['inv_no']; // เลขที่ใบแจ้งหนี้
    $doerID = $res['doerID']; // รหัสพนักงานที่ทำรายการ
    $doerStamp = $res['doerStamp']; // วันเวลาที่ทำรายการ
	
	// หาชื่อพนักงาน
	$qry_nameUser = pg_query("select \"fullname\" from \"fuser\" where \"id_user\" = '$doerID' ");
	$fullnameUser = pg_fetch_result($qry_nameUser,0);
	
	// หาจำนวนเงิน
	$qry_money = pg_query("select sum(\"amount\") as \"sum_amount\", sum(\"vat\") as \"sum_vat\"
						from \"InvoiceDetails_account\" where \"inv_no\" = '$inv_no' ");
	$sum_amount = pg_fetch_result($qry_money,0); // มูลค่า ก่อน vat
	$sum_vat = pg_fetch_result($qry_money,1); // ยอดรวม vat
	$sum_all = $sum_amount + $sum_vat; // ยอดรวม
    
    if($j%2==0){
        echo "<tr class=\"odd\">";
    }else{
        echo "<tr class=\"even\">";
    }
?>
	<td align="center"><?php echo $j; ?></td>
    <td align="center"><a href="javascript:show_detail('<?php echo $cancelID; ?>')" title="ทำรายการอนุมัติ"><u><?php echo $inv_no; ?></u></a></td>
	<td align="right"><?php echo number_format($sum_amount,2); ?></td>
    <td align="right"><?php echo number_format($sum_vat,2); ?></td>
	<td align="right"><?php echo number_format($sum_all,2);; ?></td>
    <td align="left"><?php echo $fullnameUser; ?></td>
    <td align="center"><?php echo $doerStamp; ?></td>
</tr>
<?php
}

if($j == 0){
    echo "<tr><td colspan=7 align=center>- ไม่พบข้อมูล -</td></tr>";
}
?>

</table>

<script>
	function show_detail(cancelID)
	{
		$('body').append('<div id="div_print"></div>');
		$('#div_print').load('popup_appv_cancel.php?cancelID='+cancelID);
		$('#div_print').dialog({
			title: 'อนุมัติ ยกเลิกค่าใช้จ่ายอื่นๆ (บัญชี) ',
			resizable: false,
			modal: true,  
			width: 780,
			height: 620,
			close: function(ev, ui){
				$('#div_print').remove();
			}
		});
	}
</script>